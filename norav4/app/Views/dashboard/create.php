<?php
/**
 * Create Registrasi View - Nora V4
 * Premium Compact Design - Re-engineered for reliability
 */

$totalSlaDays = $totalSlaDays ?? 0;
$estimasiDate = new DateTime();
$estimasiDate->modify("+{$totalSlaDays} days");
$estimasiFormatted = $estimasiDate->format('Y-m-d');
$estimasiLabel = $estimasiDate->format('d M Y');
?>

<!-- Hero Section -->
<div class="hero-authority">
    <span class="hero-badge-auth">Layanan Klien & Administrasi Akta</span>
    <p class="hero-p-auth">Input data pendaftaran baru untuk diproses dalam sistem tracking real-time</p>
</div>

<style>
    /* === LAYOUT === */
    .form-box-v4 { padding: 0; }
    
    /* === SECTIONS === */
    .reg-section { 
        background: var(--white); 
        border-radius: 16px; 
        padding: 30px; 
        margin-bottom: 24px; 
        border: 1px solid var(--border); 
        box-shadow: var(--shadow-sm); 
    }
    
    .section-header { 
        margin-bottom: 25px; 
        padding-bottom: 15px; 
        border-bottom: 1px solid #f5f5f5; 
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .section-header h3 { 
        font-size: 13px; 
        font-weight: 800; 
        color: var(--primary); 
        text-transform: uppercase; 
        letter-spacing: 1.5px; 
        margin: 0;
    }

    .status-badge {
        padding: 6px 14px;
        background: rgba(156, 124, 56, 0.1);
        color: var(--gold);
        border-radius: 50px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        border: 1px solid rgba(156, 124, 56, 0.2);
    }

    /* === FORM GRID === */
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
    .form-group { margin-bottom: 0; }
    .form-group label { 
        display: block; 
        font-size: 11px; 
        font-weight: 800; 
        color: var(--text-muted); 
        text-transform: uppercase; 
        margin-bottom: 10px; 
        letter-spacing: 0.5px;
    }
    
    .form-group label span { color: var(--danger); }

    .form-control { 
        width: 100%; 
        padding: 12px 16px; 
        border: 1.5px solid var(--border); 
        border-radius: 10px; 
        font-size: 14px; 
        font-weight: 600; 
        color: var(--primary); 
        transition: all 0.2s ease;
        background: #fafafa;
    }
    
    .form-control:focus { 
        outline: none; 
        border-color: var(--gold); 
        background: #fff;
        box-shadow: 0 0 0 4px rgba(156, 124, 56, 0.08); 
    }
    
    .full-width { grid-column: span 2; }
    
    .form-help { 
        font-size: 11px; 
        color: var(--text-muted); 
        margin-top: 8px; 
        display: block; 
        font-weight: 500;
    }
    
    /* === ACTIONS === */
    .form-actions { 
        display: flex; 
        justify-content: flex-end; 
        gap: 15px; 
        margin-top: 10px;
        padding-bottom: 40px;
    }

    /* === WA POPUP === */
    #waPopup {
        display: none;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(15, 31, 40, 0.9);
        backdrop-filter: blur(10px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }

    .wa-card {
        background: #FDFBFA;
        width: 90%;
        max-width: 600px;
        border-radius: 32px;
        padding: 40px;
        border: 2px solid var(--gold);
        box-shadow: 0 30px 60px rgba(0,0,0,0.4);
        text-align: center;
    }
</style>

<div class="form-box-v4">
    <form id="createForm" method="POST" action="<?= APP_URL ?>/registrasi/store">
        <input type="hidden" name="current_step_id" value="<?= $defaultStep['id'] ?? 1 ?>">

        <!-- SECTION 1: DATA KLIEN -->
        <div class="reg-section">
            <div class="section-header">
                <h3>Informasi Klien</h3>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Nama Lengkap <span>*</span></label>
                    <input type="text" name="klien_nama" id="klien_nama" class="form-control" required placeholder="Contoh: Budi Santoso">
                </div>
                <div class="form-group">
                    <label>Nomor WhatsApp <span>*</span></label>
                    <input type="text" name="klien_hp" id="klien_hp" class="form-control" required placeholder="08xxxxxxxxxx">
                </div>
                <div class="form-group">
                    <label>Total Tagihan (Rp)</label>
                    <input type="text" name="total_tagihan" id="total_tagihan" class="form-control" placeholder="0" oninput="formatCurrency(this)">
                </div>
                <div class="form-group">
                    <label>Estimasi Selesai <span>*</span></label>
                    <input type="date" name="target_date" id="target_date" class="form-control" value="<?= $estimasiFormatted ?>" required>
                    <span class="form-help">Total SLA <?= $totalSlaDays ?> hari → <strong><?= $estimasiLabel ?></strong></span>
                </div>
                <div class="form-group full-width">
                    <label>Catatan Administratif</label>
                    <textarea name="keterangan" class="form-control" style="min-height: 80px;" placeholder="Catatan internal tentang klien..."></textarea>
                </div>
            </div>
        </div>

        <!-- SECTION 2: DETAIL LAYANAN -->
        <div class="reg-section">
            <div class="section-header">
                <h3>Detail Registrasi</h3>
                <span class="status-badge">📋 <?= htmlspecialchars($defaultStep['label'] ?? 'Draft') ?></span>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Jenis Layanan <span>*</span></label>
                    <select name="layanan_id" class="form-control" required>
                        <option value="">Pilih Layanan...</option>
                        <?php foreach ($layanans ?? [] as $l): ?>
                        <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['nama_layanan']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Pembayaran Awal (Rp)</label>
                    <input type="text" name="pembayaran" id="pembayaran" class="form-control" placeholder="0" oninput="formatCurrency(this)">
                    <span class="form-help" id="pembayaranHelp">Akan tercatat sebagai riwayat transaksi</span>
                </div>
                <div class="form-group full-width">
                    <label>Catatan Status (Catatan Perihal)</label>
                    <textarea name="catatan" id="catatan" class="form-control" style="min-height: 100px;" placeholder="Tuliskan detail perihal berkas..."></textarea>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <a href="<?= APP_URL ?>/registrasi" class="btn-secondary" style="padding: 12px 30px;">Batal</a>
            <button type="submit" class="btn-primary" style="padding: 12px 50px;">Simpan Registrasi</button>
        </div>
    </form>
</div>

<!-- WA POPUP (MODERN LUXE) -->
<div id="waPopup">
    <div class="wa-card">
        <div style="margin-bottom: 25px;">
            <div style="width: 60px; height: 60px; background: #E8F5E9; color: #2E7D32; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
            </div>
            <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 28px; color: var(--primary); margin: 0;">Notifikasi WhatsApp</h2>
            <p style="color: var(--text-muted); font-size: 14px; margin-top: 5px;">Registrasi berhasil disimpan. Kirim pesan ke klien?</p>
        </div>

        <div style="background: #fff; border: 1.5px solid var(--border); border-radius: 20px; padding: 25px; text-align: left; margin-bottom: 30px;">
            <p style="margin: 0 0 15px; font-size: 12px; font-weight: 800; color: var(--gold); text-transform: uppercase; letter-spacing: 1px;">Preview Pesan:</p>
            <textarea id="waMessage" style="width: 100%; border: none; font-size: 14px; line-height: 1.6; color: #333; resize: none; background: transparent; padding: 0; min-height: 150px; outline: none;"></textarea>
        </div>

        <div style="display: flex; gap: 15px; justify-content: center;">
            <button type="button" onclick="closeWa()" style="padding: 14px 40px; border-radius: 50px; border: 2px solid var(--border); background: #fff; font-weight: 700; cursor: pointer;">Lewati</button>
            <button type="button" onclick="sendWa()" style="padding: 14px 50px; border-radius: 50px; border: none; background: var(--primary); color: #fff; font-weight: 700; cursor: pointer; box-shadow: 0 10px 20px rgba(0,0,0,0.1);">Kirim Sekarang</button>
        </div>
    </div>
</div>

<script>
    window.waTarget = '';
    let statusTemplates = {};

    function formatCurrency(input) {
        // Strict numeric only - remove any non-digit
        let val = input.value.replace(/[^0-9]/g, '');
        if (val === '') { input.value = ''; return; }
        
        // Format with dots
        input.value = parseInt(val).toLocaleString('id-ID');
        updatePaymentHelp();
    }

    function updatePaymentHelp() {
        const tagihan = parseInt(document.getElementById('total_tagihan').value.replace(/\./g, '')) || 0;
        const bayar = parseInt(document.getElementById('pembayaran').value.replace(/\./g, '')) || 0;
        const help = document.getElementById('pembayaranHelp');

        if (tagihan > 0 && bayar > 0) {
            const sisa = tagihan - bayar;
            if (sisa < 0) {
                help.innerHTML = '<span style="color:var(--danger); font-weight:800;">⚠️ Melebihi total tagihan!</span>';
            } else {
                help.innerHTML = `Terbayar: <strong>Rp ${bayar.toLocaleString('id-ID')}</strong> — Sisa: <strong>Rp ${sisa.toLocaleString('id-ID')}</strong>`;
                help.style.color = '#2E7D32';
            }
        } else {
            help.textContent = 'Akan tercatat sebagai riwayat transaksi';
            help.style.color = 'var(--text-muted)';
        }
    }

    // Auto-fill Note Templates (V3 Parity)
    async function loadNoteTemplates() {
        try {
            const response = await fetch('<?= APP_URL ?>/registrasi/templates');
            const data = await response.json();
            if (data.success) {
                statusTemplates = data.templates;
                applyTemplate();
            }
        } catch (e) { console.error("Failed to load templates"); }
    }

    function applyTemplate() {
        const stepId = document.querySelector('input[name="current_step_id"]').value;
        const catatanArea = document.getElementById('catatan');
        if (statusTemplates[stepId] && catatanArea.value === '') {
            catatanArea.value = statusTemplates[stepId];
        }
    }

    function closeWa() {
        window.location.href = '<?= APP_URL ?>/registrasi';
    }

    function sendWa() {
        const msg = document.getElementById('waMessage').value;
        const hp = window.waTarget.replace(/[^0-9]/g, '');
        let formattedHp = hp;
        if (hp.startsWith('0')) formattedHp = '62' + hp.substring(1);
        
        window.open(`https://wa.me/${formattedHp}?text=${encodeURIComponent(msg)}`, '_blank');
        setTimeout(() => { window.location.href = '<?= APP_URL ?>/registrasi'; }, 500);
    }

    document.getElementById('createForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const tagihan = parseInt(document.getElementById('total_tagihan').value.replace(/\./g, '')) || 0;
        const bayar = parseInt(document.getElementById('pembayaran').value.replace(/\./g, '')) || 0;

        if (tagihan <= 0 && bayar > 0) {
            alert('Jika total tagihan kosong, pembayaran awal harus kosong juga.');
            return;
        }

        if (bayar > tagihan) {
            alert('Pembayaran awal tidak boleh melebihi total tagihan!');
            return;
        }

        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.textContent;
        btn.disabled = true;
        btn.textContent = 'Memproses...';

        const fd = new FormData(this);
        // Overwrite formatted currency with plain numbers
        fd.set('total_tagihan', tagihan);
        fd.set('pembayaran', bayar);

        fetch(this.action, {
            method: 'POST',
            body: fd
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                // Show WA Popup
                window.waTarget = data.klien_hp;
                document.getElementById('waMessage').value = data.wa_message || `Halo ${data.klien_nama},\n\nRegistrasi Anda dengan nomor *${data.nomor_registrasi}* telah berhasil didaftarkan.\n\nLacak: <?= APP_URL ?>/lacak?token=${data.tracking_token}`;
                document.getElementById('waPopup').style.display = 'flex';
            } else {
                alert('Gagal: ' + data.message);
                btn.disabled = false;
                btn.textContent = originalText;
            }
        })
        .catch(err => {
            alert('Terjadi kesalahan koneksi.');
            btn.disabled = false;
            btn.textContent = originalText;
        });
    });

    document.addEventListener('DOMContentLoaded', loadNoteTemplates);
</script>
</script>
