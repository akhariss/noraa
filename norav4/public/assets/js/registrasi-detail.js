/**
 * Registrasi Detail JavaScript - MICHELIN EDITION v5.29 (AJAX SYNC)
 * Notaris & PPAT Tracking System
 */

// Global Templates Cache
let statusTemplates = {};
window.DISERAHKAN_TPL = 'Berkas telah diserahkan kepada [penerima].';

// Load templates asynchronously (100% Synced with Create Page)
function loadNoteTemplates() {
    console.log('Michelin Sync: Loading note templates...');
    fetch(APP_URL + '/index.php?gate=cms_get_note_templates')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                statusTemplates = data.templates || {};
                console.log('Michelin Sync: Templates loaded successfully.');
                
                // Michelin Elite Mode: Use explicit handover template from API
                const noteEl = document.getElementById('handover_note');
                if (noteEl) {
                    let tplData = data.handover_tpl || statusTemplates['diserahkan'] || '';
                    
                    if (tplData) {
                        console.log('Michelin Sync: Found handover template via Behavior 5. Populating...');
                        let note = tplData;
                        // Replace common placeholders using Global REG_DATA
                        note = note.replace(/\{nama_klien\}/g, REG_DATA.nama || '')
                                   .replace(/\{nomor_registrasi\}/g, REG_DATA.nomor || '')
                                   .replace(/\{penerima\}|\[penerima\]/g, '...');
                        noteEl.value = note;
                        window.DISERAHKAN_TPL = tplData; // Global fallback
                    } else {
                        console.warn('Michelin Sync: No Behavior 5 handover template found in CMS.');
                    }
                }
            }
        })
        .catch(err => console.error('Michelin Sync Error:', err));
}

document.addEventListener('DOMContentLoaded', function () {
    // 1. Fetch all templates for general status updates (Dropdowns)
    loadNoteTemplates();
    
    // 2. ZERO-ACTION AUTO-FILL: Handover Card (Behavior 5)
    // Michelin Rule: No dropdown selection needed. If the card exists, fill it!
    const handoverNoteEl = document.getElementById('handover_note');
    if (handoverNoteEl && window.DISERAHKAN_TPL_CMS) {
         console.log('Michelin Zero-Action: Populating handover note via Server Injection.');
         let note = window.DISERAHKAN_TPL_CMS;
         
         // Direct Variable Replacement
         note = note.replace(/\{nama_klien\}/g, REG_DATA.nama || '')
                    .replace(/\{nomor_registrasi\}/g, REG_DATA.nomor || '')
                    .replace(/\{penerima\}|\[penerima\]/g, '...');
         
         // FORCE VALUE: This ensures it is filled immediately on load
         handoverNoteEl.value = note;
         window.DISERAHKAN_TPL = window.DISERAHKAN_TPL_CMS; // Sync for real-time typing
    }
});


// 1. Update Status Form Handler (0-7 Logic)
const updateStatusForm = document.getElementById('updateStatusForm');
if (updateStatusForm) {
    updateStatusForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const messageDiv = document.getElementById('actionMessage');

        // Michelin Anti-Spam Control
        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Menyimpan...';
        }

        fetch(APP_URL + '/index.php?gate=update_status', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (messageDiv) {
                messageDiv.style.display = 'block';
                // Handle Michelin "Duplicate" Response gracefully
                if (data.message && data.message.includes('Data sudah tersimpan')) {
                    messageDiv.className = 'form-message success';
                } else {
                    messageDiv.className = 'form-message ' + (data.success ? 'success' : 'error');
                }
                
                messageDiv.textContent = data.message;

                if (data.success) {
                    setTimeout(() => { window.location.reload(); }, 200);
                } else if (submitBtn) {
                    // Re-enable only on actual failure
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Simpan Status';
                }
            }
        })
        .catch(error => { 
            console.error('Error:', error); 
            alert('Terjadi kesalahan sistem.'); 
            if(submitBtn) { submitBtn.disabled = false; submitBtn.textContent = 'Simpan Status'; }
        });
    });
}

// 2. Edit Klien Modal Logic (Handled in registrasi_detail.php)

// 3. Handover (Serahkan - Behavior 5) Logic
function serahkanRegistrasi() {
    console.log('Michelin: Triggering handover submission...');
    const penerimaInput = document.getElementById('penerima_name');
    const noteInput = document.getElementById('handover_note');
    const btn = document.querySelector('button[onclick="serahkanRegistrasi()"]');
    
    if (!penerimaInput || !penerimaInput.value.trim()) {
        alert('Silakan isi nama penerima berkas.');
        penerimaInput?.focus();
        return;
    }

    // Michelin Fix: Explicitly fetch CSRF from the status form
    const csrfVal = document.querySelector('#updateStatusForm input[name="csrf_token"]')?.value || '';

    if (btn) {
        btn.disabled = true;
        btn.textContent = 'Sedang Menyimpan...';
    }

    const formData = new FormData();
    formData.append('registrasi_id', REG_DATA.id);
    formData.append('status', 'diserahkan');
    formData.append('penerima', penerimaInput.value.trim());
    formData.append('catatan', noteInput ? noteInput.value : '');
    formData.append('csrf_token', csrfVal);

    fetch(APP_URL + '/index.php?gate=update_status', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const messageDiv = document.getElementById('actionMessage');
        if (messageDiv) {
            messageDiv.style.display = 'block';
            messageDiv.className = 'form-message ' + (data.success ? 'success' : 'error');
            messageDiv.textContent = data.message;
            if (data.success) { 
                setTimeout(() => { window.location.reload(); }, 300); 
            } else if (btn) {
                btn.disabled = false;
                btn.textContent = '✓ Konfirmasi & Serahkan Sekarang';
            }
        }
    })
    .catch(err => {
        console.error('Michelin Submit Error:', err);
        alert('Gagal mengirim data. Silakan coba lagi.');
        if (btn) {
            btn.disabled = false;
            btn.textContent = '✓ Konfirmasi & Serahkan Sekarang';
        }
    });
}

function updatePenerimaNote(val) {
    const noteEl = document.getElementById('handover_note');
    if (noteEl && window.DISERAHKAN_TPL_CMS) {
        // Michelin Precision Sync v5.36: Start with ORIGINAL CMS Template always
        let note = window.DISERAHKAN_TPL_CMS;
        
        // 1. Static Replacements (Synced from REG_DATA)
        note = note.replace(/\{nama_klien\}/g, REG_DATA.nama || '')
                   .replace(/\{nomor_registrasi\}/g, REG_DATA.nomor || '')
                   .replace(/\{status\}/g, 'Diserahkan')
                   .replace(/\{user_name\}/g, REG_DATA.sender || 'Admin')
                   .replace(/\{nama_kantor\}/g, REG_DATA.kantor || 'Kantor Notaris')
                   .replace(/\{phone\}/g, REG_DATA.hp || '')
                   .replace(/\{tanggal\}/g, REG_DATA.tanggal || '');

        // 2. AGGRESSIVE SYNC: Replace all variations of 'penerima' placeholder
        // This regex covers {penerima}, [penerima], and the initial '....' dot markers
        const nameToInsert = val.trim() || '....';
        note = note.replace(/\{penerima\}|\[penerima\]|\.\.\.\./g, nameToInsert);
        
        // 3. Instant Push
        noteEl.value = note;
        console.log('Michelin Sync: Handover note updated with value: ' + nameToInsert);
    }
}

// 4. Smart Auto-Fill Logic (Synced with Pendaftaran Page)
function autoFillCatatan() {
    const statusSelect = document.getElementById('status_select');
    const catatanTextarea = document.getElementById('status_catatan');
    if (!statusSelect || !catatanTextarea) return;

    const selectedStatus = (statusSelect.value || '').toLowerCase();
    
    // Find template with case-insensitive check
    let templateBody = '';
    for (let key in statusTemplates) {
        if (key.toLowerCase() === selectedStatus) {
            templateBody = statusTemplates[key];
            break;
        }
    }

    if (templateBody) {
        let note = templateBody;
        const statusLabel = statusSelect.options[statusSelect.selectedIndex].text;
        
        const now = new Date();
        const tanggalReal = now.toLocaleDateString('id-ID', {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        });
        
        // Comprehensive Replacement (Handles both {key} and [key] formats)
        const replacements = {
            'nama_klien': REG_DATA.nama || '',
            'klien': REG_DATA.nama || '',
            'nomor_registrasi': REG_DATA.nomor || '',
            'nomor': REG_DATA.nomor || '',
            'status': statusLabel,
            'user_name': REG_DATA.sender || 'Admin',
            'nama_pengirim': REG_DATA.sender || 'Admin',
            'nama_kantor': REG_DATA.kantor || 'Kantor Notaris',
            'phone': REG_DATA.hp || '',
            'hp': REG_DATA.hp || '',
            'tanggal': tanggalReal || REG_DATA.tanggal,
            'catatan': '...'
        };

        for (let key in replacements) {
            const regex = new RegExp(`\\\\{|\\\\}|\\\\[|\\\\]|${key}`, 'g'); // Simplified for broad match
            // More precise regex for each key
            const curlyRegex = new RegExp(`\\\\{${key}\\\\}`, 'gi');
            const bracketRegex = new RegExp(`\\\\[${key}\\\\]`, 'gi');
            note = note.replace(curlyRegex, replacements[key])
                       .replace(bracketRegex, replacements[key]);
        }

        catatanTextarea.value = note;
        console.log(`Michelin Sync: Auto-filled note for [${selectedStatus}]`);
    } else {
        console.warn(`Michelin Sync: No template found for status key [${selectedStatus}]`);
    }
}

