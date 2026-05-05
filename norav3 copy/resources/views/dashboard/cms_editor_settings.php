<?php
$activePage = 'app_settings';
$pageTitle = 'Manajemen Identitas & Kontak'; // Updated as requested
require VIEWS_PATH . '/templates/header.php';

// Get brand name from cms_section_content id 13
$brandId = 13;
$brandValue = '';
try {
    $result = \App\Adapters\Database::selectOne("SELECT content_value FROM cms_section_content WHERE id = :id", ['id' => $brandId]);
    $brandValue = $result['content_value'] ?? '';
} catch (Exception $e) { }
?>

<div class="content-wrapper">
    <div class="luxe-main-card">
        <div class="clean-header-section">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <p>Kelola identitas kantor, kontak, dan jam operasional.</p>
                </div>
                <a href="<?= APP_URL ?>/index.php?gate=cms_editor" class="btn-clean-back">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                    Kembali
                </a>
            </div>
        </div>

        <form id="settingsForm" class="settings-form-linear">
            
            <div class="settings-group-header-luxe">
                IDENTITAS & BRANDING
            </div>
            
            <div class="note-list-integrated">
                <div class="note-row-luxe">
                    <div class="setting-label-area">
                        <strong class="setting-name">Nama Brand / Notaris</strong>
                        <span class="setting-key">BRAND_ID: <?= $brandId ?></span>
                    </div>
                    <div class="setting-input-area">
                        <input type="text" name="settings[<?= $brandId ?>]" value="<?= htmlspecialchars($brandValue) ?>" class="form-control-luxe" placeholder="Sri Anah SH.M.Kn">
                    </div>
                    <div class="setting-help-area">Utama Website</div>
                </div>

                <div class="note-row-luxe">
                    <div class="setting-label-area">
                        <strong class="setting-name">Badge Hero</strong>
                        <span class="setting-key">HERO_BADGE</span>
                    </div>
                    <div class="setting-input-area">
                        <input type="text" name="settings[<?= $pageData['badge']['id'] ?>]" value="<?= htmlspecialchars($pageData['badge']['value']) ?>" class="form-control-luxe">
                    </div>
                    <div class="setting-help-area">Headline Kecil</div>
                </div>
            </div>

            <div class="settings-group-header-luxe mt-20">
                KONTAK & LOKASI
            </div>

            <div class="note-list-integrated">
                <div class="note-row-luxe">
                    <div class="setting-label-area">
                        <strong class="setting-name">WhatsApp / Phone</strong>
                        <span class="setting-key">PHONE_CONTACT</span>
                    </div>
                    <div class="setting-input-area">
                        <input type="text" name="settings[<?= $pageData['contact']['phone']['id'] ?>]" value="<?= htmlspecialchars($pageData['contact']['phone']['value']) ?>" class="form-control-luxe" placeholder="628xxxx">
                    </div>
                    <div class="setting-help-area">Primary WhatsApp</div>
                </div>

                <div class="note-row-luxe">
                    <div class="setting-label-area">
                        <strong class="setting-name">Email Kantor</strong>
                        <span class="setting-key">EMAIL_ADDRESS</span>
                    </div>
                    <div class="setting-input-area">
                        <input type="email" name="settings[<?= $pageData['contact']['email']['id'] ?>]" value="<?= htmlspecialchars($pageData['contact']['email']['value']) ?>" class="form-control-luxe">
                    </div>
                    <div class="setting-help-area">Official Email</div>
                </div>

                <div class="note-row-luxe">
                    <div class="setting-label-area">
                        <strong class="setting-name">Alamat Lengkap</strong>
                        <span class="setting-key">OFFICE_ADDRESS</span>
                    </div>
                    <div class="setting-input-area">
                        <textarea name="settings[<?= $pageData['contact']['address']['id'] ?>]" rows="1" class="form-control-luxe textarea"><?= htmlspecialchars($pageData['contact']['address']['value']) ?></textarea>
                    </div>
                    <div class="setting-help-area">Lokasi Kantor</div>
                </div>
            </div>

            <div class="settings-group-header-luxe mt-20">
                JAM OPERASIONAL
            </div>

            <div class="note-list-integrated">
                <div class="note-row-luxe">
                    <div class="setting-label-area">
                        <strong class="setting-name">Senin - Jumat</strong>
                        <span class="setting-key">WORKDAYS_WEEK</span>
                    </div>
                    <div class="setting-input-area">
                        <div class="dual-input">
                            <input type="text" name="settings[<?= $pageData['contact']['work_days']['id'] ?>]" value="<?= htmlspecialchars($pageData['contact']['work_days']['value']) ?>" class="form-control-luxe" placeholder="Hari">
                            <input type="text" name="settings[<?= $pageData['contact']['work_hours']['id'] ?>]" value="<?= htmlspecialchars($pageData['contact']['work_hours']['value']) ?>" class="form-control-luxe" placeholder="Jam">
                        </div>
                    </div>
                    <div class="setting-help-area">Main Schedule</div>
                </div>

                <div class="note-row-luxe">
                    <div class="setting-label-area">
                        <strong class="setting-name">Hari Sabtu</strong>
                        <span class="setting-key">WORKDAYS_SAT</span>
                    </div>
                    <div class="setting-input-area">
                        <div class="dual-input">
                            <input type="text" name="settings[<?= $pageData['contact']['work_days_sat']['id'] ?>]" value="<?= htmlspecialchars($pageData['contact']['work_days_sat']['value']) ?>" class="form-control-luxe" placeholder="Sabtu">
                            <input type="text" name="settings[<?= $pageData['contact']['work_hours_sat']['id'] ?>]" value="<?= htmlspecialchars($pageData['contact']['work_hours_sat']['value']) ?>" class="form-control-luxe" placeholder="Jam">
                        </div>
                    </div>
                    <div class="setting-help-area">Weekend Schedule</div>
                </div>
            </div>

            <div class="settings-footer-luxe">
                <button type="submit" id="btnSubmitSettings" class="btn-luxe-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline></svg>
                    SIMPAN PERUBAHAN
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.content-wrapper { padding: 30px 20px; max-width: 1250px; margin: 0 auto; }

.luxe-main-card {
    background: #fff;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.03);
    border: 1px solid #F0F0F0;
}

.clean-header-section {
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #F5F5F5;
}

.clean-header-section h2 {
    font-family: 'Cormorant Garamond', serif;
    font-size: 34px;
    color: #1B3A4B;
    margin: 0;
    font-weight: 900;
    letter-spacing: -0.5px;
}

.clean-header-section p {
    color: #1B3A4B;
    font-size: 18px; 
    margin: 2px 0 0;
    font-family: 'Cormorant Garamond', serif;
    font-weight: 800;
}

.btn-clean-back {
    display: flex; align-items: center; gap: 8px; padding: 8px 15px;
    background: #F8F9FA; border: 1px solid #EEE; border-radius: 8px;
    color: #495057; font-size: 12px; font-weight: 700; text-decoration: none; transition: 0.2s;
}
.btn-clean-back:hover { background: #EEE; transform: translateX(-3px); }

.settings-group-header-luxe {
    font-size: 11px; font-weight: 800; color: #9C7C38; letter-spacing: 1.5px;
    margin-bottom: 8px; padding-left: 5px; opacity: 0.8;
}
.mt-20 { margin-top: 20px; }

/* REPLICATING NOTE-LIST STYLE EXACTLY */
.note-list-integrated { display: flex; flex-direction: column; gap: 6px; }

.note-row-luxe {
    background: #fff;
    border: 1px solid #F1E9D7;
    border-radius: 8px;
    display: grid;
    grid-template-columns: 240px 1fr 140px;
    align-items: center;
    padding: 6px 20px;
    transition: 0.2s;
}

.setting-label-area .setting-name { display: block; font-size: 14px; color: #1B3A4B; font-weight: 700; }
.setting-label-area .setting-key { display: block; font-size: 10px; color: #AAA; text-transform: uppercase; font-weight: 700; }

.form-control-luxe {
    width: 100%; border: 1px solid transparent; background: #F9F9F9;
    padding: 6px 12px; border-radius: 6px; font-size: 14px; color: #1B3A4B; transition: 0.2s;
}
.form-control-luxe:focus { background: #FFF; border-color: #9C7C38; outline: none; box-shadow: 0 0 0 3px rgba(156, 124, 56, 0.1); }
.form-control-luxe.textarea { resize: none; min-height: 34px; padding-top: 8px; }

.dual-input { display: flex; gap: 10px; }

.setting-help-area { font-size: 11px; color: #999; font-style: italic; text-align: right; }

.settings-footer-luxe { margin-top: 25px; display: flex; justify-content: center; }

.btn-luxe-primary {
    display: flex; align-items: center; gap: 10px; padding: 12px 35px;
    background: #1B3A4B; color: #F1E9D7; border: none; border-radius: 10px;
    font-size: 14px; font-weight: 800; cursor: pointer; transition: 0.2s;
}
.btn-luxe-primary:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(27, 58, 75, 0.2); }

@media (max-width: 768px) {
    .note-row-luxe { grid-template-columns: 1fr; gap: 5px; padding: 12px; }
    .setting-help-area { text-align: left; margin-top: 5px; }
}
</style>

<script>
document.getElementById('settingsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    showAtomicModal('confirm', 'Konfirmasi', 'Simpan perubahan pengaturan aplikasi?', () => {
        executeSaveSettings();
    });
});

async function executeSaveSettings() {
    const btn = document.getElementById('btnSubmitSettings');
    const originalContent = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = 'MENYIMPAN...';
    const formData = new FormData(document.getElementById('settingsForm'));
    formData.append('csrf_token', '<?= generateCSRFToken() ?>');
    try {
        const res = await fetch('<?= APP_URL ?>/index.php?gate=cms_save_settings', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
            showAtomicModal('success', 'Berhasil', 'Pengaturan telah diperbarui! ✨', () => { location.reload(); });
        } else {
            showAtomicModal('error', 'Gagal', data.message || 'Terjadi kesalahan sistem.');
            btn.disabled = false; btn.innerHTML = originalContent;
        }
    } catch (e) {
        showAtomicModal('error', 'Kesalahan', 'Terjadi kesalahan koneksi jaringan.');
        btn.disabled = false; btn.innerHTML = originalContent;
    }
}
</script>

<?php require VIEWS_PATH . '/templates/footer.php'; ?>
