<?php
/**
 * Create Registrasi View
 */

$currentUser = getCurrentUser();
$pageTitle = 'Tambah Registrasi Baru';
$activePage = 'registrasi';

require VIEWS_PATH . '/templates/header.php';
?>

<div class="form-container">
    <form id="createRegistrasiForm" class="form-horizontal">
        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

        <div class="form-section">
            <h3>Data Klien & Perkara</h3>

            <!-- Contact Grid -->
            <div style="display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 20px;">
                <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 250px;">
                    <label for="klien_nama">Nama Klien *</label>
                    <input type="text" id="klien_nama" name="klien_nama" required placeholder="Nama lengkap">
                </div>

                <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 250px;">
                    <label for="klien_hp">Nomor HP *</label>
                    <input type="text" id="klien_hp" name="klien_hp" required placeholder="08xxxxxxxxxx">
                </div>
            </div>

            <!-- SLA Date -->
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="target_date" style="display: flex; align-items: center; justify-content: space-between;">
                    <span>Estimasi *</span>
                    <button type="button"
                        onclick="const el = document.getElementById('estimasiInfo'); el.style.display = el.style.display === 'none' ? 'block' : 'none';"
                        style="background: none; border: none; font-size: 12px; color: var(--gold); cursor: pointer; display: flex; align-items: center; padding: 0;">
                        Info Perhitungan
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 4px;">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                </label>

                <!-- Information Dropdown -->
                <div id="estimasiInfo"
                    style="display: none; background: #FFF9F0; border: 1px solid var(--gold); border-radius: 6px; padding: 12px; margin-bottom: 12px; font-size: 12px; line-height: 1.5; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                    <strong style="color: var(--primary); display: block; margin-bottom: 4px;">SLA Penyelesaian
                        Berkas:</strong>
                    Input bawaan otomatis genap <strong>2 bulan</strong> sejak hari pendaftaran. Tambahan batas
                    toleransi penyelesaian (+5 hari) akan terbentuk sendiri saat dipantau.
                    <br><em style="color: var(--text-muted); display: block; margin-top: 4px;">(Contoh: Admin input
                        batas tgl 20 Mei. Maka di halaman Lacak Resi Klien akan tertulis 20 - 25 Mei).</em>
                </div>

                <input type="date" id="target_date" name="target_date"
                    value="<?= date('Y-m-d', strtotime('+2 months')) ?>" required style="max-width: 300px;"
                    class="form-control">
            </div>

            <!-- Case Description -->
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="keterangan">Keterangan *</label>
                <textarea id="keterangan" name="keterangan" rows="5" required
                    placeholder="Tuliskan keterangan lengkap perkara di sini..." class="form-control"></textarea>
            </div>
        </div>

        <div class="form-section">
            <h3>Data Registrasi</h3>

            <div class="form-group">
                <label for="layanan_id">Jenis Layanan *</label>
                <select id="layanan_id" name="layanan_id" required>
                    <option value="">Pilih Layanan</option>
                    <?php foreach ($layanan as $l): ?>
                        <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['nama_layanan']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="status">Status Awal *</label>
                <select id="status" name="status" required onchange="autoFillCatatan()">
                    <?php 
                    // Elite Engine: Fetch steps with behavior 0 (Process) or 1 (Start)
                    // This allows newly added steps in DB to appear here automatically (v5.24)
                    $wsModel = new \App\Domain\Entities\WorkflowStep();
                    $initialSteps = $wsModel->getByBehaviors([0, 1]);
                    
                    foreach ($initialSteps as $step): 
                        $isSelected = ($step['step_key'] === 'draft') ? 'selected' : '';
                    ?>
                        <option value="<?= htmlspecialchars($step['step_key']) ?>" <?= $isSelected ?>>
                            <?= htmlspecialchars($step['label']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="catatan">Catatan / Pesan WA</label>
                <textarea id="catatan" name="catatan" rows="5"
                    placeholder="Pilih status untuk auto-fill pesan..."></textarea>
            </div>
        </div>

        <div class="form-actions">
            <a href="<?= APP_URL ?>/index.php?gate=registrasi" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">Simpan Registrasi</button>
        </div>
    </form>
</div>

<div id="formMessage" class="form-message" style="display: none;"></div>

<script>
    // Status templates - will be loaded from database
    let statusTemplates = {};
    let currentUsername = '<?= htmlspecialchars($currentUser['username'] ?? '') ?>';

    // Load note templates from database on page load
    function loadNoteTemplates() {
        console.log('Loading note templates...');
        fetch('<?= APP_URL ?>/index.php?gate=cms_get_note_templates')
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Templates data:', data);
                if (data.success) {
                    statusTemplates = data.templates || {};
                    console.log('Status templates loaded:', statusTemplates);
                    // Auto-fill draft on page load after templates loaded
                    setTimeout(() => autoFillCatatan(), 200);
                } else {
                    console.error('Failed to load templates:', data.message);
                }
            })
            .catch(err => console.error('Error loading templates:', err));
    }

    // Auto-fill catatan when status changes
    function autoFillCatatan() {
        const statusSelect = document.getElementById('status');
        const catatanTextarea = document.getElementById('catatan');
        const selectedStatus = statusSelect.value;

        console.log('Auto-fill catatan called. Status:', selectedStatus);
        console.log('Available templates:', Object.keys(statusTemplates));

        if (selectedStatus && statusTemplates[selectedStatus]) {
            let note = statusTemplates[selectedStatus];
            console.log('Template found:', note);

            // Grab current values for replacement
            const nama = document.getElementById('klien_nama') ? document.getElementById('klien_nama').value : '';
            const hp = document.getElementById('klien_hp') ? document.getElementById('klien_hp').value : '';
            const statusLabel = statusSelect.options[statusSelect.selectedIndex].text;
            const now = new Date();
            const tanggal = now.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            // Replace all variables
            note = note.replace(/\{nama_klien\}/g, nama || '[Nama Klien]')
                .replace(/\{nomor_registrasi\}/g, '[Pending]')
                .replace(/\{status\}/g, statusLabel)
                .replace(/\{user_name\}/g, currentUsername || '[User]')
                .replace(/\{nama_pengirim\}/g, currentUsername || '[User]')
                .replace(/\{nama_kantor\}/g, 'Kantor Notaris')
                .replace(/\{phone\}/g, hp || '[Phone]')
                .replace(/\{tanggal\}/g, tanggal);

            catatanTextarea.value = note;
            console.log('Catatan filled:', note);
        } else {
            console.log('No template found for status:', selectedStatus);
        }
    }

    // Load templates on page load
    document.addEventListener('DOMContentLoaded', function () {
        console.log('Page loaded, loading templates...');
        loadNoteTemplates();
    });

    // Auto-fill when client name changes
    document.getElementById('klien_nama')?.addEventListener('input', function () {
        console.log('Client name changed, auto-filling...');
        autoFillCatatan();
    });

    // Auto-fill when status changes
    document.getElementById('status')?.addEventListener('change', function () {
        console.log('Status changed, auto-filling...');
        autoFillCatatan();
    });

    // Form submit handler
    document.getElementById('createRegistrasiForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        const messageDiv = document.getElementById('formMessage');

        messageDiv.style.display = 'block';
        messageDiv.className = 'form-message';
        messageDiv.textContent = 'Menyimpan registrasi...';

        fetch('<?= APP_URL ?>/index.php?gate=registrasi_store', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                console.log('Create response:', data); // Debug log
                messageDiv.className = 'form-message ' + (data.success ? 'success' : 'error');
                messageDiv.textContent = data.message;

                if (data.success) {
                    // Get data from response
                    const klienNama = data.klien_nama || document.getElementById('klien_nama').value;
                    const klienHp = data.klien_hp || document.getElementById('klien_hp').value;
                    const nomorRegistrasi = data.nomor_registrasi || '-';
                    const status = document.getElementById('status').value;

                    console.log('Showing WA popup with:', { klienNama, klienHp, nomorRegistrasi, status }); // Debug

                    setTimeout(() => {
                        try {
                            showWaPopup(klienNama, klienHp, nomorRegistrasi, status);
                        } catch (e) {
                            console.error('Error showing WA popup:', e);
                            alert('Popup error: ' + e.message);
                            // Fallback: just redirect
                            setTimeout(() => {
                                window.location.href = '<?= APP_URL ?>/index.php?gate=registrasi';
                            }, 200);
                        }
                    }, 100);
                } else {
                    setTimeout(() => {
                        messageDiv.style.display = 'none';
                    }, 5000);
                }
            })
            .catch(error => {
                messageDiv.className = 'form-message error';
                messageDiv.textContent = 'Terjadi kesalahan. Silakan coba lagi.';
                setTimeout(() => {
                    messageDiv.style.display = 'none';
                }, 5000);
            });
    });

    // WhatsApp Popup Functions
    function showWaPopup(nama, hp, nomorRegistrasi, status) {
        const statusLabels = {
            <?php foreach ($initialSteps as $step): ?>
            '<?= $step['step_key'] ?>': '<?= addslashes($step['label']) ?>',
            <?php endforeach; ?>
        };

        console.log('Popup data received:', { nama, hp, nomorRegistrasi, status });

        // Set popup data
        const elNama = document.getElementById('waKlienNama');
        const elNomor = document.getElementById('waNomorRegistrasi');
        const elStatus = document.getElementById('waStatus');

        if (elNama) elNama.textContent = nama;
        if (elNomor) elNomor.textContent = nomorRegistrasi;
        if (elStatus) elStatus.textContent = statusLabels[status] || status;

        // Store phone number for later use (hidden)
        window.waPhoneNumber = hp;

        // Show popup
        const popup = document.getElementById('waPopup');
        if (popup) {
            popup.style.display = 'flex';
            console.log('✅ Popup displayed successfully');
            console.log('Popup data set - Nama:', nama, 'Nomor:', nomorRegistrasi, 'Status:', status);
        } else {
            console.error('❌ waPopup element not found in DOM');
            alert('Popup element not found! Redirecting...');
            setTimeout(() => {
                window.location.href = '<?= APP_URL ?>/index.php?gate=registrasi';
            }, 200);
        }
    }

    function closeWaPopup() {
        document.getElementById('waPopup').style.display = 'none';
        // Redirect to daftar registrasi after close
        setTimeout(() => {
            window.location.href = '<?= APP_URL ?>/index.php?gate=registrasi';
        }, 300);
    }

    function confirmSendWa() {
        const nama = document.getElementById('waKlienNama').textContent;
        const nomorRegistrasi = document.getElementById('waNomorRegistrasi').textContent;
        const status = document.getElementById('waStatus').textContent;
        const hp = window.waPhoneNumber || '';

        console.log('=== CONFIRM SEND WA ===');
        console.log('Nama:', nama);
        console.log('Nomor Registrasi:', nomorRegistrasi);
        console.log('Status:', status);
        console.log('HP (from window):', hp);

        if (!hp) {
            alert('Nomor HP tidak ditemukan!');
            return;
        }

        // Clean phone number - remove all non-numeric characters
        let cleanPhone = hp.replace(/[^0-9]/g, '');
        console.log('Clean phone (removed non-numeric):', cleanPhone);

        // Convert leading 0 to 62
        if (cleanPhone.startsWith('0')) {
            cleanPhone = '62' + cleanPhone.substring(1);
            console.log('Converted 0 to 62:', cleanPhone);
        }

        // Validate phone number
        if (cleanPhone.length < 10) {
            alert('Nomor HP tidak valid: ' + hp);
            return;
        }

        const username = '<?= htmlspecialchars($currentUser['username'] ?? '') ?>';

        fetch(APP_URL + '/index.php?gate=cms_get_msg_tpl&key=wa_create')
            .then(r => r.json())
            .then(d => {
                let msg;
                if (d.success && d.template) {
                    msg = d.template.template_body
                        .replace(/\{nama_klien\}/g, nama)
                        .replace(/\{telp_klien\}/g, hp)
                        .replace(/\{nomor_registrasi\}/g, nomorRegistrasi)
                        .replace(/\{status\}/g, status)
                        .replace(/\{nama_pengirim\}/g, username)
                        .replace(/\{phone\}/g, '<?= htmlspecialchars($appPhone ?? '') ?>')
                        .replace(/\{alamat\}/g, '<?= htmlspecialchars($appAddress ?? '') ?>');
                } else {
                    msg = `Halo Bapak/Ibu ${nama},\n\nKami dari <?= htmlspecialchars($appName) ?> menginformasikan bahwa registrasi Anda telah terdaftar.\n\nDetail Registrasi:\n• Nomor Registrasi: ${nomorRegistrasi}\n• Status: ${status}\n\nTerima kasih atas kepercayaan Anda.\n\nHormat kami,\n<?= htmlspecialchars($appName) ?>`;
                }

                const waUrl = `https://wa.me/${cleanPhone}?text=${encodeURIComponent(msg)}`;
                console.log('Final phone number:', cleanPhone);
                console.log('WA URL:', waUrl);

                const waWindow = window.open(waUrl, '_blank');
                if (!waWindow || waWindow.closed || typeof waWindow.closed === 'undefined') {
                    alert('Popup blocker mencegah WhatsApp terbuka. Silakan izinkan popup untuk website ini.');
                    return;
                }
                console.log('✅ WhatsApp opened in new tab');
                console.log('✅ WhatsApp opened in new tab');

                // Close our popup after WA opens
                setTimeout(() => {
                    const waPopup = document.getElementById('waPopup');
                    if (waPopup) waPopup.style.display = 'none';
                    console.log('✅ Popup closed, WA opened in new tab');

                    // Redirect to registrasi list
                    window.location.href = '<?= APP_URL ?>/index.php?gate=registrasi';
                }, 500);
            })
            .catch(() => {
                const msg = `Halo Bapak/Ibu ${nama},\n\nKami dari <?= htmlspecialchars($appName) ?> menginformasikan bahwa registrasi Anda telah terdaftar.\n\nDetail Registrasi:\n• Nomor Registrasi: ${nomorRegistrasi}\n• Status: ${status}`;
                const waUrl = `https://wa.me/${cleanPhone}?text=${encodeURIComponent(msg)}`;
                const waWindow = window.open(waUrl, '_blank');
                if (waWindow) {
                    setTimeout(() => {
                        const waPopup = document.getElementById('waPopup');
                        if (waPopup) waPopup.style.display = 'none';
                        // Redirect to registrasi list
                        window.location.href = '<?= APP_URL ?>/index.php?gate=registrasi';
                    }, 500);
                }
            });
    }
</script>

<!-- WhatsApp Popup -->
<div id="waPopup" style="
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 99999;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(2px);
">
    <div style="
        background: var(--white);
        border-radius: 12px;
        padding: 32px;
        max-width: 450px;
        width: 90%;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        position: relative;
        z-index: 100000;
    ">
        <div style="
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        ">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="#25D366" style="flex-shrink: 0;">
                <path
                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
            </svg>
            <h3 style="margin: 0; color: var(--primary); font-size: 18px;">Kirim Notifikasi WhatsApp?</h3>
        </div>

        <div style="
            background: #f0fdf4;
            border-left: 3px solid #25D366;
            border-radius: 6px;
            padding: 16px;
            margin-bottom: 24px;
        ">
            <p style="margin: 0 0 12px 0; color: #1a7f37; font-weight: 600; font-size: 14px;">
                ✓ Registrasi Berhasil Dibuat
            </p>
            <div style="font-size: 13px; color: #1a7f37; line-height: 1.8;">
                <div><strong>Nama:</strong> <span id="waKlienNama"></span></div>
                <div><strong>Nomor Registrasi:</strong> <span id="waNomorRegistrasi"></span></div>
                <div><strong>Status:</strong> <span id="waStatus"></span></div>
            </div>
        </div>

        <div style="
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        ">
            <button type="button" onclick="closeWaPopup()" style="
                background: var(--cream);
                color: var(--text);
                padding: 10px 20px;
                border: none;
                border-radius: 6px;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                z-index: 100001;
                position: relative;
            ">
                Lewati
            </button>
            <button type="button" onclick="confirmSendWa()" style="
                background: #25D366;
                color: white;
                padding: 10px 20px;
                border: none;
                border-radius: 6px;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                z-index: 100001;
                position: relative;
            ">
                Kirim WhatsApp
            </button>
        </div>
    </div>
</div>

<?php require VIEWS_PATH . '/templates/footer.php'; ?>