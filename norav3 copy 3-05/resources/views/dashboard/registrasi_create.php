<?php
/**
 * Create Registrasi View — Final Layout v8.7 (Premium Compact Design)
 */

$currentUser = getCurrentUser();
$pageTitle = 'Tambah Registrasi Baru';
$activePage = 'registrasi_create';

$wsModel = new \App\Domain\Entities\WorkflowStep();
$defaultStep = $wsModel->findByBehavior(0);
$layanan = (new \App\Domain\Entities\Layanan())->getAll();

$totalSlaDays = (int)array_sum(array_column($wsModel->getAll(), 'sla_days'));
$estimasiDate = new DateTime();
$estimasiDate->modify("+{$totalSlaDays} days");
$estimasiFormatted = $estimasiDate->format('Y-m-d');
$estimasiLabel = $estimasiDate->format('d M Y');

require VIEWS_PATH . '/templates/header.php';
?>

<style>
/* === LAYOUT & CONTAINER === */
.form-container {
    width: 100%;
    padding: 20px;
}

/* === CARD SECTION - MINIMAL === */
.reg-section {
    background: var(--white);
    border-radius: 12px;
    padding: 20px 24px;
    margin-bottom: 24px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.reg-section-header {
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 12px;
}

.reg-section-header h3 {
    margin: 0;
    font-size: 14px;
    font-weight: 800;
    color: var(--primary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    flex: 1;
}

.reg-section-header .reg-badge {
    position: relative;
    flex-shrink: 0;
}

.reg-section-body {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px 24px;
    align-items: start;
    text-align: left !important;
}

/* === FORM GROUP === */
.form-group {
    display: flex;
    flex-direction: column;
    align-items: flex-start !important;
    gap: 6px;
    margin-bottom: 0;
    width: 100%;
    text-align: left !important;
}

.form-group label {
    font-size: 10px;
    font-weight: 800;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.form-group label .required {
    color: var(--danger);
}

.form-group input[type="text"],
.form-group input[type="number"],
.form-group input[type="date"],
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid var(--border);
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    background: var(--white);
    color: var(--text);
    transition: all 0.2s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--gold);
    box-shadow: 0 0 0 3px rgba(156, 124, 56, 0.1);
}

.form-group.full-width {
    grid-column: span 2;
}

.form-group textarea {
    resize: vertical;
    min-height: 80px;
}

.form-help {
    display: block;
    width: 100%;
    font-size: 11px;
    color: var(--text-muted);
    margin-top: 4px;
}

/* === ACTIONS === */
.reg-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    margin-top: 24px;
    padding: 16px 0;
    border-top: 1px solid var(--border);
}

/* === THEME ALIGNED WA POPUP === */
#waChatPreview {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 14px;
    text-align: left;
    margin-top: 12px;
    max-height: 140px;
    overflow-y: auto;
    border: 1px solid var(--border);
}

.wa-bubble {
    background: var(--white);
    padding: 12px;
    border-radius: 8px;
    border-left: 4px solid var(--gold);
    font-size: 13px;
    color: var(--text);
    line-height: 1.6;
    box-shadow: 0 2px 5px rgba(0,0,0,0.03);
    word-wrap: break-word;
    white-space: pre-wrap;
}

/* === RESPONSIVE === */
@media (max-width: 768px) {
    .reg-section-body {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="form-container">
    <form id="createRegistrasiForm">
        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
        <input type="hidden" id="status" name="status" value="<?= htmlspecialchars($defaultStep['step_key'] ?? 'draft') ?>">

        <!-- DATA KLIEN -->
        <div class="reg-section">
            <div class="reg-section-header">
                <h3>Data Klien</h3>
            </div>
            <div class="reg-section-body">
                <div class="form-group">
                    <label for="klien_nama">Nama Klien <span class="required">*</span></label>
                    <input type="text" id="klien_nama" name="klien_nama" required placeholder="Nama lengkap">
                </div>
                <div class="form-group">
                    <label for="klien_hp">No. Telepon <span class="required">*</span></label>
                    <input type="text" id="klien_hp" name="klien_hp" required placeholder="08xxxxxxxxxx">
                </div>
                <div class="form-group">
                    <label for="total_tagihan">Total Tagihan (Rp)</label>
                    <input type="text" id="total_tagihan" name="total_tagihan" placeholder="Opsional" oninput="formatCurrencyInput(this)">
                </div>
                <div class="form-group">
                    <label for="target_date">Estimasi Selesai <span class="required">*</span></label>
                    <input type="date" id="target_date" name="target_date" required value="<?= $estimasiFormatted ?>">
                    <div class="form-help">Total SLA <?= $totalSlaDays ?> hari → <?= $estimasiLabel ?></div>
                </div>
                <div class="form-group full-width">
                    <label for="catatan_klien">Catatan</label>
                    <textarea id="catatan_klien" name="catatan_klien" placeholder="Catatan tentang klien..."></textarea>
                </div>
            </div>
        </div>

        <!-- DATA REGISTRASI -->
        <div class="reg-section">
            <div class="reg-section-header">
                <h3>Data Registrasi</h3>
                <span class="reg-badge" style="background: rgba(156, 124, 56, 0.1); color: var(--gold); border-color: rgba(156, 124, 56, 0.2);">📋 <?= htmlspecialchars($defaultStep['label'] ?? 'Draft') ?></span>
            </div>
            <div class="reg-section-body">
                <div class="form-group">
                    <label for="layanan_id">Jenis Layanan <span class="required">*</span></label>
                    <select id="layanan_id" name="layanan_id" required>
                        <option value="">Pilih Layanan</option>
                        <?php foreach ($layanan as $l): ?>
                            <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['nama_layanan']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="pembayaran">Pembayaran Awal (Rp)</label>
                    <input type="text" id="pembayaran" name="pembayaran" placeholder="Jika ada" oninput="formatCurrencyInput(this)">
                    <div class="form-help" id="pembayaranInfo">Akan masuk ke transaksi</div>
                </div>
                <div class="form-group full-width">
                    <label for="catatan">Catatan Status</label>
                    <textarea id="catatan" name="catatan" placeholder="Detail status..."></textarea>
                </div>
            </div>
        </div>

        <div class="reg-actions">
            <a href="<?= APP_URL ?>/index.php?gate=registrasi" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">Simpan Registrasi</button>
        </div>
    </form>
</div>

<script>
    let statusTemplates = {};
    let currentUsername = '<?= htmlspecialchars($currentUser['username'] ?? '') ?>';
    window.currentWaMessage = ''; // Global for WA popup

    function loadNoteTemplates() {
        fetch('<?= APP_URL ?>/index.php?gate=cms_get_note_templates')
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    statusTemplates = data.templates || {};
                    if (statusTemplates['<?= $defaultStep['step_key'] ?? 'draft' ?>']) autoFillCatatanStatus();
                }
            })
            .catch(() => {});
    }

    function autoFillCatatanStatus() {
        const ta = document.getElementById('catatan');
        const s = document.getElementById('status').value;
        if (statusTemplates[s]) {
            let note = statusTemplates[s];
            const nama = document.getElementById('klien_nama')?.value || '';
            const hp = document.getElementById('klien_hp')?.value || '';
            const tgl = new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            note = note.replace(/\{nama_klien\}/g, nama || '[Nama Klien]')
                .replace(/\{nomor_registrasi\}/g, '[Pending]')
                .replace(/\{status\}/g, '<?= addslashes($defaultStep['label'] ?? 'Draft') ?>')
                .replace(/\{user_name\}/g, currentUsername || '[User]')
                .replace(/\{nama_pengirim\}/g, currentUsername || '[User]')
                .replace(/\{nama_kantor\}/g, '<?= addslashes(OFFICE_NAME) ?>')
                .replace(/\{phone\}/g, hp || '[Phone]')
                .replace(/\{tanggal\}/g, tgl);
            ta.value = note;
        }
    }

    function formatCurrencyInput(input) {
        let value = input.value;
        let isNegative = value.startsWith('-');
        let cleanValue = value.replace(/[^-0-9]/g, '');
        if (cleanValue.includes('-')) cleanValue = '-' + cleanValue.replace(/-/g, '');
        if (!cleanValue || cleanValue === '-') { input.value = cleanValue; return; }
        let numPart = cleanValue.split('-').pop();
        let formatted = numPart.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        input.value = (isNegative ? '-' : '') + formatted;
        updateCalc();
    }

    function updateCalc() {
        const tagihanInput = document.getElementById('total_tagihan');
        const pembayaranInput = document.getElementById('pembayaran');
        const info = document.getElementById('pembayaranInfo');
        if (!tagihanInput || !pembayaranInput || !info) return;

        const tagihan = parseInt(tagihanInput.value.replace(/\./g, '')) || 0;
        const bayar = parseInt(pembayaranInput.value.replace(/\./g, '')) || 0;
        
        if (tagihan > 0 && bayar > tagihan) {
            info.textContent = '⚠️ Melebihi total tagihan!'; info.style.color = '#c62828'; info.style.fontWeight = '700';
        } else if (bayar > 0) {
            info.textContent = `Dibayar: Rp ${bayar.toLocaleString('id-ID')} — Sisa: Rp ${(tagihan - bayar).toLocaleString('id-ID')}`;
            info.style.color = '#2e7d32'; info.style.fontWeight = '700';
        } else {
            info.textContent = 'Pembayaran awal akan masuk ke transaksi';
            info.style.color = 'var(--text-muted)'; info.style.fontWeight = '400';
        }
    }

    document.addEventListener('DOMContentLoaded', loadNoteTemplates);

    document.getElementById('createRegistrasiForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const tagihan = parseInt(document.getElementById('total_tagihan').value.replace(/\./g, '')) || 0;
        const bayar = parseInt(document.getElementById('pembayaran').value.replace(/\./g, '')) || 0;
        
        if (tagihan <= 0 && bayar > 0) {
            showAtomicModal('warning', 'Pemberitahuan', 'Jika Total Tagihan kosong, Pembayaran Awal juga harus kosong.');
            return;
        }
        if (bayar > tagihan) {
            showAtomicModal('warning', 'Pemberitahuan', 'Pembayaran Awal tidak boleh melebihi Total Tagihan!');
            return;
        }

        const fd = new FormData(this);
        fd.set('total_tagihan', tagihan);
        fd.set('pembayaran', bayar);
        
        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.textContent;
        btn.disabled = true; btn.textContent = 'Menyimpan...';

        fetch('<?= APP_URL ?>/index.php?gate=registrasi_store', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showAtomicModal('success', 'Berhasil', data.message, () => {
                        showWaPopup(data.klien_nama, data.klien_hp, data.nomor_registrasi);
                    }, 500); 
                } else {
                    showAtomicModal('error', 'Gagal', data.message || 'Gagal menyimpan.');
                    btn.disabled = false; btn.textContent = originalText;
                }
            })
            .catch(() => { 
                showAtomicModal('error', 'Kesalahan', 'Terjadi kesalahan koneksi.'); 
                btn.disabled = false; btn.textContent = originalText;
            });
    });

    async function showWaPopup(nama, hp, nomor) {
        document.getElementById('waPopup').style.display = 'flex';
        document.getElementById('waKlienNama').textContent = (nama || '-').toUpperCase();
        document.getElementById('waNomorRegistrasi').textContent = nomor || '-';
        window.waPhoneNumber = hp || '';
        
        const previewEl = document.getElementById('waChatPreview');
        previewEl.value = 'Memuat template...';
        
        let message = `Halo ${nama || 'Bapak/Ibu'}, registrasi ${nomor || ''} telah terdaftar. Terima kasih.`;
        
        try {
            const r = await fetch('<?= APP_URL ?>/index.php?gate=cms_get_msg_tpl&key=wa_create');
            const d = await r.json();
            if (d.success && d.template) {
                const tgl = new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                message = d.template.template_body
                    .replace(/\{nama_klien\}/g, nama || '-')
                    .replace(/\{nomor_registrasi\}/g, nomor || '-')
                    .replace(/\{status\}/g, '<?= addslashes($defaultStep['label'] ?? 'Draft') ?>')
                    .replace(/\{user_name\}/g, currentUsername || 'Staff')
                    .replace(/\{nama_pengirim\}/g, currentUsername || 'Staff')
                    .replace(/\{nama_kantor\}/g, '<?= addslashes(OFFICE_NAME) ?>')
                    .replace(/\{phone\}/g, hp || '-')
                    .replace(/\{tanggal\}/g, tgl);
            }
        } catch (e) {}

        previewEl.value = message;
    }

    function closeWaPopup() { 
        document.getElementById('waPopup').style.display = 'none'; 
        setTimeout(() => { window.location.href = '<?= APP_URL ?>/index.php?gate=registrasi'; }, 300); 
    }
    
    function confirmSendWa() {
        const hp = window.waPhoneNumber || '';
        const message = document.getElementById('waChatPreview').value;
        if (!hp) { showAtomicModal('warning', 'Pemberitahuan', 'Nomor HP tujuan tidak ditemukan!'); return; }
        
        let cp = hp.replace(/[^0-9]/g, '');
        if (cp.startsWith('0')) cp = '62' + cp.substring(1);
        if (cp.length < 10) { showAtomicModal('warning', 'Pemberitahuan', 'Nomor HP tidak valid'); return; }
        
        window.open(`https://wa.me/${cp}?text=${encodeURIComponent(message)}`, '_blank');
        setTimeout(() => { document.getElementById('waPopup').style.display = 'none'; window.location.href = '<?= APP_URL ?>/index.php?gate=registrasi'; }, 500);
    }
</script>

<!-- PREMIUM LUXE WA POPUP (Navy & Gold Theme) -->
<div id="waPopup" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(15,31,40,0.85); z-index: 100000; align-items: center; justify-content: center; backdrop-filter: blur(8px);">
    <div style="background: #F7F4EF; border-radius: 30px; padding: 45px; max-width: 650px; width: 95%; text-align: center; box-shadow: 0 40px 100px rgba(0,0,0,0.5); border: 2.5px solid #B8964F; animation: atomicModalFadeIn 0.35s cubic-bezier(0.34, 1.56, 0.64, 1); position: relative;">
        
        <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 30px; gap: 15px;">
            <div style="width: 55px; height: 55px; background: #fff; border: 2px solid #9C7C38; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(156,124,56,0.2);">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="#9C7C38"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
            </div>
            <h3 style="margin: 0; color: #1B3A4B; font-family: 'Cormorant Garamond', serif; font-weight: 700; font-size: 28px;">Notifikasi WhatsApp</h3>
        </div>

        <div style="text-align: left; margin-bottom: 20px; background: rgba(255,255,255,0.7); padding: 15px 25px; border-radius: 16px; border: 1.5px solid #EEE; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <p id="waKlienNama" style="margin: 0; color: #1B3A4B; font-size: 16px; font-weight: 950; letter-spacing: -0.3px;"></p>
                <div style="display: flex; align-items: center; gap: 10px; margin-top: 3px;">
                    <p style="margin: 0; color: #9C7C38; font-size: 11px; font-weight: 950; text-transform: uppercase;">ID REG: <span id="waNomorRegistrasi"></span></p>
                </div>
            </div>
            <span style="background: #1B3A4B; color: #fff; font-size: 9px; font-weight: 950; padding: 5px 12px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.5px;">New Registration</span>
        </div>
        
        <div style="text-align: left; margin-bottom: 5px;">
            <label style="font-size: 10px; font-weight: 950; color: #BBB; text-transform: uppercase; margin-bottom: 8px; display: block; margin-left: 5px; letter-spacing: 0.5px;">Draf Pesan WhatsApp (Bisa Diedit):</label>
            <textarea id="waChatPreview" style="width: 100%; min-height: 200px; background: #fff; border: 2px solid #F1E9D7; border-radius: 20px; padding: 25px; font-size: 15px; line-height: 1.6; color: #222; font-family: inherit; outline: none; transition: 0.3s; resize: vertical; box-shadow: inset 0 2px 10px rgba(156,124,56,0.05);"></textarea>
        </div>

        <div style="margin-top: 25px; display: flex; gap: 12px; justify-content: center;">
            <button type="button" onclick="closeWaPopup()" style="background: #fff; color: #888; padding: 12px 35px; border: 2.5px solid #EEE; border-radius: 50px; font-weight: 800; cursor: pointer; font-size: 13px; transition: 0.2s;">Lewati</button>
            <button type="button" onclick="confirmSendWa()" style="background: #1B3A4B; color: #fff; padding: 12px 55px; border: none; border-radius: 50px; font-weight: 950; cursor: pointer; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; box-shadow: 0 10px 25px rgba(27,58,75,0.3); transition: 0.2s;">Kirim Pesan</button>
        </div>
    </div>
</div>

<?php require VIEWS_PATH . '/templates/footer.php'; ?>
