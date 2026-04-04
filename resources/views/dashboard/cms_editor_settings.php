<?php
$activePage = 'cms';
require VIEWS_PATH . '/templates/header.php';

// Get brand name from cms_section_content id 13
$brandId = 13;
$brandValue = '';
try {
    $result = \App\Adapters\Database::selectOne("SELECT content_value FROM cms_section_content WHERE id = :id", ['id' => $brandId]);
    $brandValue = $result['content_value'] ?? '';
} catch (Exception $e) {
    // Fallback to pageData if available
}
?>

<div class="content-wrapper">
    <div class="page-header">
        <a href="<?= APP_URL ?>/index.php?gate=cms_editor" class="btn-back">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
        </a>
        <div class="header-content">
            <h1>Pengaturan Aplikasi</h1>
            <p>Kelola identitas kantor, kontak, dan jam operasional</p>
        </div>
    </div>

    <div class="form-message-container" id="formMessageContainer"></div>

    <form id="settingsForm" class="settings-form">
        <div class="settings-grid">
            <!-- Branding Section -->
            <div class="settings-group">
                <div class="group-header">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    </svg>
                    Identitas Kantor
                </div>
                <div class="form-group">
                    <label for="brand">Nama Brand / Notaris</label>
                    <input type="text" name="settings[<?= $brandId ?>]" id="brand" value="<?= htmlspecialchars($brandValue) ?>" data-setting-key="brand" class="form-control">
                    <div class="form-help">Nama brand yang ditampilkan di website (cms_section_content id: 13)</div>
                </div>
                <div class="form-group">
                    <label for="badge">Badge Hero</label>
                    <input type="text" name="settings[<?= $pageData['badge']['id'] ?>]" id="badge" value="<?= htmlspecialchars($pageData['badge']['value']) ?>" data-setting-key="badge" class="form-control">
                    <div class="form-help">Teks kecil di atas judul hero section</div>
                </div>
            </div>

            <!-- Contact Section -->
            <div class="settings-group">
                <div class="group-header">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                    </svg>
                    Kontak & Lokasi
                </div>
                <div class="form-group">
                    <label for="phone">WhatsApp / Phone</label>
                    <input type="text" name="settings[<?= $pageData['contact']['phone']['id'] ?>]" id="phone" value="<?= htmlspecialchars($pageData['contact']['phone']['value']) ?>" data-setting-key="phone" class="form-control" placeholder="6285747898811">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="settings[<?= $pageData['contact']['email']['id'] ?>]" id="email" value="<?= htmlspecialchars($pageData['contact']['email']['value']) ?>" data-setting-key="email" class="form-control" placeholder="info@notaris.com">
                </div>
                <div class="form-group">
                    <label for="address">Alamat Lengkap</label>
                    <textarea name="settings[<?= $pageData['contact']['address']['id'] ?>]" id="address" rows="3" data-setting-key="address" class="form-control"><?= htmlspecialchars($pageData['contact']['address']['value']) ?></textarea>
                </div>
            </div>

            <!-- Operating Hours -->
            <div class="settings-group">
                <div class="group-header">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    Jam Operasional
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="work_days">Hari Kerja (Mingguan)</label>
                        <input type="text" name="settings[<?= $pageData['contact']['work_days']['id'] ?>]" id="work_days" value="<?= htmlspecialchars($pageData['contact']['work_days']['value']) ?>" data-setting-key="work_days" class="form-control" placeholder="Senin - Jumat">
                    </div>
                    <div class="form-group">
                        <label for="work_hours">Jam Kerja</label>
                        <input type="text" name="settings[<?= $pageData['contact']['work_hours']['id'] ?>]" id="work_hours" value="<?= htmlspecialchars($pageData['contact']['work_hours']['value']) ?>" data-setting-key="work_hours" class="form-control" placeholder="09:00 - 16:00">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="work_days_sat">Hari Kerja (Sabtu)</label>
                        <input type="text" name="settings[<?= $pageData['contact']['work_days_sat']['id'] ?>]" id="work_days_sat" value="<?= htmlspecialchars($pageData['contact']['work_days_sat']['value']) ?>" data-setting-key="work_days_sat" class="form-control" placeholder="Sabtu">
                    </div>
                    <div class="form-group">
                        <label for="work_hours_sat">Jam Kerja (Sabtu)</label>
                        <input type="text" name="settings[<?= $pageData['contact']['work_hours_sat']['id'] ?>]" id="work_hours_sat" value="<?= htmlspecialchars($pageData['contact']['work_hours_sat']['value']) ?>" data-setting-key="work_hours_sat" class="form-control" placeholder="08:00 - 12:00">
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary btn-submit">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                </svg>
                Simpan Semua Perubahan
            </button>
        </div>
    </form>
</div>

<!-- Modal Konfirmasi -->
<div id="settingsModal" class="modal">
    <div class="modal-content modal-sm">
        <div class="modal-header">
            <h3>Konfirmasi Simpan</h3>
            <button type="button" onclick="closeSettingsModal()" class="btn-close">&times;</button>
        </div>
        <div class="modal-body">
            <p>Apakah Anda yakin ingin menyimpan perubahan pengaturan ini?</p>
            <div class="alert-info">
                <strong>Catatan:</strong> Beberapa perubahan mungkin memerlukan refresh halaman untuk ditampilkan.
            </div>
        </div>
        <div class="modal-actions">
            <button type="button" onclick="closeSettingsModal()" class="btn-secondary">Batal</button>
            <button type="button" id="btnConfirmSaveSettings" class="btn-primary">Ya, Simpan</button>
        </div>
    </div>
</div>

<style>
.content-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    padding: 24px;
}

.page-header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 32px;
    padding-bottom: 20px;
    border-bottom: 2px solid var(--border);
}

.btn-back {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: #f1f5f9;
    border-radius: 8px;
    transition: all 0.2s;
    flex-shrink: 0;
}

.btn-back svg {
    width: 20px;
    height: 20px;
    color: #475569;
}

.btn-back:hover {
    background: #e2e8f0;
    transform: translateX(-4px);
}

.header-content {
    flex: 1;
}

.header-content h1 {
    font-size: 24px;
    color: #1B3A4B;
    margin: 0 0 6px 0;
    font-family: 'Cormorant Garamond', serif;
    font-weight: 700;
}

.header-content p {
    color: #64748b;
    margin: 0;
    font-size: 14px;
    line-height: 1.5;
}

.settings-form {
    background: white;
    border-radius: 12px;
    padding: 32px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.settings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 32px;
    margin-bottom: 32px;
}

.settings-group {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 24px;
}

.group-header {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 16px;
    font-weight: 700;
    color: #1B3A4B;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 2px solid #e2e8f0;
}

.group-header svg {
    color: #1B3A4B;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: #334155;
    font-size: 14px;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 14px;
    font-family: inherit;
    transition: all 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: #1B3A4B;
    box-shadow: 0 0 0 3px rgba(27, 58, 75, 0.1);
}

.form-help {
    margin-top: 6px;
    font-size: 12px;
    color: #64748b;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.form-actions {
    display: flex;
    justify-content: center;
    padding-top: 20px;
    border-top: 1px solid #e2e8f0;
}

.btn-submit {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 32px;
    background: #1B3A4B;
    color: #9C7C38;
    border: none;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-submit:hover {
    background: #2D5A6B;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(27, 58, 75, 0.2);
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
}

.modal-sm {
    max-width: 500px;
    width: 90%;
}

.modal-content {
    background: white;
    border-radius: 12px;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid #e2e8f0;
}

.modal-header h3 {
    margin: 0;
    color: #1B3A4B;
    font-size: 18px;
}

.btn-close {
    background: none;
    border: none;
    font-size: 24px;
    color: #64748b;
    cursor: pointer;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    transition: all 0.2s;
}

.btn-close:hover {
    background: #f1f5f9;
}

.modal-body {
    padding: 24px;
}

.modal-body p {
    margin: 0 0 16px 0;
    color: #334155;
    font-size: 14px;
    line-height: 1.6;
}

.alert-info {
    background: #e0f2fe;
    border: 1px solid #0284c7;
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 13px;
    color: #0c4a6e;
}

.modal-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    padding: 20px 24px;
    border-top: 1px solid #e2e8f0;
}

.btn-secondary, .btn-primary {
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    transition: all 0.2s;
}

.btn-secondary {
    background: #f1f5f9;
    color: #475569;
}

.btn-secondary:hover {
    background: #e2e8f0;
}

.btn-primary {
    background: #1B3A4B;
    color: #9C7C38;
}

.btn-primary:hover {
    background: #2D5A6B;
}

.form-message-container {
    margin-bottom: 20px;
}

.form-message {
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 14px;
    margin-bottom: 12px;
    animation: slideIn 0.3s ease-out;
}

.form-message.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.form-message.error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@media (max-width: 768px) {
    .settings-grid {
        grid-template-columns: 1fr;
    }

    .form-row {
        grid-template-columns: 1fr;
    }

    .page-header {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>

<script>
document.getElementById('settingsForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    document.getElementById('settingsModal').style.display = 'flex';
});

function closeSettingsModal() {
    document.getElementById('settingsModal').style.display = 'none';
}

function executeSaveSettings() {
    const btn = document.getElementById('btnConfirmSaveSettings');
    const originalText = btn.textContent;

    btn.disabled = true;
    btn.textContent = 'Menyimpan...';

    const form = document.getElementById('settingsForm');
    const formData = new FormData(form);
    formData.append('csrf_token', '<?= generateCSRFToken() ?>');

    fetch('<?= APP_URL ?>/index.php?gate=cms_save_settings', {
        method: 'POST',
        body: formData
    })
    .then(async (res) => {
        if (res.status === 401 || res.status === 403) {
            window.location.href = APP_URL + '/index.php?gate=login&expired=1';
            return;
        }

        const data = await res.json();

        if (data && data.message && data.message.includes('Session expired')) {
            window.location.href = APP_URL + '/index.php?gate=login&expired=1';
            return;
        }

        const messageDiv = document.getElementById('formMessageContainer');
        const messageClass = data.success ? 'success' : 'error';
        messageDiv.innerHTML = `<div class="form-message ${messageClass}">${data.message}</div>`;

        if (data.success) {
            closeSettingsModal();
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            closeSettingsModal();
            btn.disabled = false;
            btn.textContent = originalText;
        }
    })
    .catch((err) => {
        console.error(err);
        const messageDiv = document.getElementById('formMessageContainer');
        messageDiv.innerHTML = '<div class="form-message error">Terjadi kesalahan jaringan.</div>';
        closeSettingsModal();
        btn.disabled = false;
        btn.textContent = originalText;
    })
    .finally(() => {
        if (!document.getElementById('formMessageContainer').querySelector('.success')) {
            btn.disabled = false;
            btn.textContent = originalText;
        }
    });
}

document.getElementById('btnConfirmSaveSettings').addEventListener('click', executeSaveSettings);

// Close modal on outside click
document.getElementById('settingsModal').addEventListener('click', function(e) {
    if (e.target === this) closeSettingsModal();
});
</script>

<?php require VIEWS_PATH . '/templates/footer.php'; ?>
