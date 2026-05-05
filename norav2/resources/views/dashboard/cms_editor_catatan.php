<?php
/**
 * Note Template Management - WA LUXE INTEGRATED
 * Standardized with WA Popup Style and Atomic Modals.
 */
$activePage = 'cms';
$pageTitle = 'Manajemen Note Templates'; 

$wsModel = new \App\Domain\Entities\WorkflowStep();
$allSteps = $wsModel->getAll();

$mappedTemplates = [];
foreach ($templates as $t) {
    $mappedTemplates[$t['status_key']] = $t;
}

require VIEWS_PATH . '/templates/header.php';
?>

<style>
.note-mgmt-container {
    padding: 20px 0;
}

.luxe-main-card {
    background: #fff;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.02);
    border: 1px solid #F0F0F0;
}

.clean-header-section {
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #F5F5F5;
}

.clean-header-section h2 {
    font-family: 'Cormorant Garamond', serif;
    font-size: 34px; /* Larger and Bolder */
    color: #1B3A4B;
    margin: 0;
    font-weight: 900;
    letter-spacing: -0.5px;
}

.clean-header-section p {
    color: #1B3A4B; /* Primary Navy */
    font-size: 18px; 
    margin: 2px 0 0; /* Ultra tight margin */
    font-family: 'Cormorant Garamond', serif;
    font-weight: 800;
    font-style: normal; /* No Italic */
}

/* Integrated List Row - ULTRA SLIM */
.note-list-integrated {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.note-row-luxe {
    background: #fff;
    border: 1px solid #F1E9D7;
    border-radius: 8px;
    display: grid;
    grid-template-columns: 200px 1fr 120px;
    align-items: center;
    padding: 8px 20px; /* Thinnest padding */
    transition: 0.2s;
}

.note-row-luxe:hover {
    border-color: #9C7C38;
    box-shadow: 0 8px 25px rgba(156,124,56,0.06);
    transform: translateX(8px);
}

.row-title { font-weight: 800; color: #1B3A4B; font-size: 14px; }
.row-key { display: block; font-size: 9px; color: #BBB; text-transform: uppercase; margin-top: 2px; font-weight: 700; letter-spacing: 0.5px; }
.row-preview { font-size: 13px; color: #666; padding: 0 30px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-style: italic; }

.btn-row-edit {
    background: transparent; color: #1B3A4B; border: 1.5px solid #1B3A4B;
    padding: 8px 18px; border-radius: 50px; font-size: 11px;
    font-weight: 800; cursor: pointer; text-transform: uppercase;
    transition: 0.2s;
}
.btn-row-edit:hover { background: #1B3A4B; color: #E8E2D5; }

/* WA-STYLE MODAL (Stationery Edition) */
.wa-modal-overlay { 
    position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
    background: rgba(15,31,40,0.85); display: none; 
    align-items: center; justify-content: center; z-index: 99999; 
    backdrop-filter: blur(8px);
}
.wa-modal-content { 
    background: #F7F4EF; width: 650px; border-radius: 30px; 
    padding: 45px; text-align: center; border: 2.5px solid #B8964F;
    box-shadow: 0 40px 100px rgba(0,0,0,0.5);
    animation: atomicModalFadeIn 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.wa-modal-header {
    display: flex; align-items: center; justify-content: center; margin-bottom: 25px; gap: 15px;
}
.wa-modal-header h3 { 
    margin: 0; color: #1B3A4B; font-family: 'Cormorant Garamond', serif; 
    font-weight: 700; font-size: 28px; 
}

.wa-client-info {
    text-align: left; margin-bottom: 20px; background: rgba(255,255,255,0.7); 
    padding: 15px 25px; border-radius: 16px; border: 1.5px solid #EEE; 
    display: flex; justify-content: space-between; align-items: center;
}

.wa-editor-label {
    text-align: left; font-size: 10px; font-weight: 950; color: #BBB; 
    text-transform: uppercase; margin-bottom: 8px; display: block; 
    margin-left: 5px; letter-spacing: 0.5px;
}

.wa-textarea {
    width: 100%; min-height: 220px; background: #fff; border: 2px solid #F1E9D7; 
    border-radius: 20px; padding: 25px; font-size: 15px; line-height: 1.6; 
    color: #222; font-family: inherit; outline: none; transition: 0.3s; 
    resize: vertical; box-shadow: inset 0 2px 10px rgba(156,124,56,0.05);
}
.wa-textarea:focus { border-color: #B8964F; box-shadow: inset 0 2px 10px rgba(156,124,56,0.1), 0 0 0 5px rgba(184,150,79,0.05); }

.wa-footer {
    margin-top: 25px; display: flex; gap: 12px; justify-content: center;
}

.btn-wa-cancel {
    background: #fff; color: #888; padding: 12px 35px; border: 2.5px solid #EEE; 
    border-radius: 50px; font-weight: 800; cursor: pointer; font-size: 13px; transition: 0.2s;
}
.btn-wa-save {
    background: #1B3A4B; color: #B8964F; padding: 12px 55px; border: none; 
    border-radius: 50px; font-weight: 950; cursor: pointer; font-size: 14px; 
    text-transform: uppercase; letter-spacing: 1px; box-shadow: 0 10px 25px rgba(27,58,75,0.3); transition: 0.2s;
}

.wa-chips { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 15px; }
.wa-chip { 
    background: #fff; color: #1B3A4B; border: 1.2px solid #EEE;
    padding: 6px 14px; border-radius: 8px; font-size: 10px; font-weight: 800; cursor: pointer;
}
.wa-chip:hover { border-color: #B8964F; color: #9C7C38; }
</style>

<div class="note-mgmt-container">
    <div class="container">
        <div class="luxe-main-card">
            <div class="clean-header-section">
                <p>Kelola draf pesan otomatis untuk setiap alur tahapan kerja.</p>
            </div>

            <div class="note-list-integrated">
                <?php foreach ($allSteps as $step): 
                    $key = $step['step_key'];
                    $tpl = $mappedTemplates[$key] ?? null;
                    $preview = $tpl ? strip_tags($tpl['template_body']) : 'Belum ada draf template...';
                ?>
                    <div class="note-row-luxe">
                        <div>
                            <span class="row-title"><?= htmlspecialchars($step['label']) ?></span>
                            <span class="row-key"><?= $key ?></span>
                        </div>
                        <div class="row-preview">
                            <?= htmlspecialchars($preview) ?>
                        </div>
                        <div style="text-align: right;">
                            <button class="btn-row-edit" onclick="openWaStyleModal('<?= $key ?>', '<?= addslashes($step['label']) ?>', `<?= addslashes($tpl['template_body'] ?? '') ?>`)">
                                Edit Draf
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- LUXE WA-STYLE MODAL -->
<div id="waModal" class="wa-modal-overlay">
    <div class="wa-modal-content">
        <div class="wa-modal-header">
            <div style="width: 45px; height: 45px; background: #fff; border: 2px solid #9C7C38; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(156,124,56,0.15);">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="#9C7C38"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
            </div>
            <h3>Edit Draf Catatan</h3>
        </div>

        <div class="wa-client-info">
            <div>
                <p id="waModalStepName" style="margin: 0; color: #1B3A4B; font-size: 16px; font-weight: 950; letter-spacing: -0.3px;"></p>
                <div style="display: flex; align-items: center; gap: 10px; margin-top: 3px;">
                    <p style="margin: 0; color: #9C7C38; font-size: 11px; font-weight: 950; text-transform: uppercase;">KEY: <span id="waModalStepKey"></span></p>
                </div>
            </div>
            <span style="background: #1B3A4B; color: #B8964F; font-size: 9px; font-weight: 950; padding: 5px 12px; border-radius: 6px; text-transform: uppercase;">Editor Mode</span>
        </div>
        
        <form id="waNoteForm">
            <input type="hidden" name="status_key" id="inputWaKey">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            
            <div style="text-align: left;">
                <label class="wa-editor-label">Isi Template Catatan:</label>
                <textarea name="template_body" id="inputWaBody" class="wa-textarea" placeholder="Tulis draf pesan di sini..."></textarea>
                
                <div class="wa-chips">
                    <span class="wa-chip" onclick="insV('{nama_klien}')">+ Nama Klien</span>
                    <span class="wa-chip" onclick="insV('{nomor_registrasi}')">+ No. Reg</span>
                    <span class="wa-chip" onclick="insV('{status}')">+ Status</span>
                    <span class="wa-chip" onclick="insV('{tanggal}')">+ Tanggal</span>
                </div>
            </div>

            <div class="wa-footer">
                <button type="button" onclick="closeWaModal()" class="btn-wa-cancel">Batal</button>
                <button type="submit" class="btn-wa-save">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openWaStyleModal(key, label, body) {
    document.getElementById('inputWaKey').value = key;
    document.getElementById('waModalStepName').innerText = label;
    document.getElementById('waModalStepKey').innerText = key;
    document.getElementById('inputWaBody').value = body;
    document.getElementById('waModal').style.display = 'flex';
}

function closeWaModal() {
    document.getElementById('waModal').style.display = 'none';
}

function insV(v) {
    const ta = document.getElementById('inputWaBody');
    const s = ta.selectionStart;
    const e = ta.selectionEnd;
    ta.value = ta.value.substring(0, s) + v + ta.value.substring(e);
    ta.focus();
}

document.getElementById('waNoteForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = this.querySelector('.btn-wa-save');
    const originalText = btn.innerText;

    btn.disabled = true;
    btn.innerText = 'MENYIMPAN...';

    try {
        const fd = new FormData(this);
        const res = await fetch('<?= APP_URL ?>/index.php?gate=cms_save_note_tpl', { method: 'POST', body: fd });
        const data = await res.json();
        
        if (data.success) {
            showAtomicModal('success', 'Berhasil', 'Template Catatan berhasil diperbarui! ✨', () => {
                location.reload();
            });
        } else {
            showAtomicModal('error', 'Gagal', data.message || 'Terjadi kesalahan saat menyimpan data.');
            btn.disabled = false;
            btn.innerText = originalText;
        }
    } catch (e) {
        showAtomicModal('error', 'Kesalahan', 'Terjadi kesalahan koneksi jaringan.');
        btn.disabled = false;
        btn.innerText = originalText;
    }
});
</script>

<?php require VIEWS_PATH . '/templates/footer.php'; ?>
",Description:
