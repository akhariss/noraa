<?php
$activePage = 'cms';
require VIEWS_PATH . '/templates/header.php';
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
            <h1>Template Pesan WhatsApp</h1>
            <p>Edit template pesan yang dikirim otomatis ke klien</p>
        </div>
    </div>

    <div class="form-message-container" id="formMessageContainer"></div>

    <div class="templates-grid">
        <?php if (!empty($templates)): ?>
            <?php foreach ($templates as $template): ?>
                <div class="template-card" id="message-card-<?= $template['id'] ?>">
                    <div class="template-header">
                        <div>
                            <h4><?= htmlspecialchars($template['template_name']) ?></h4>
                            <span class="template-key"><?= htmlspecialchars($template['template_key']) ?></span>
                        </div>
                        <button type="button" onclick='editMessageTemplate(<?= $template['id'] ?>, <?= htmlspecialchars(json_encode($template['template_name'], JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES) ?>, <?= htmlspecialchars(json_encode($template['template_body'], JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES) ?>)' class="btn-edit">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            Edit
                        </button>
                    </div>
                    <?php if (!empty($template['description'])): ?>
                        <p class="template-description"><?= htmlspecialchars($template['description']) ?></p>
                    <?php endif; ?>
                    <div class="template-preview">
                        <div class="preview-label">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                            Preview Pesan
                        </div>
                        <div class="preview-content wa-preview"><?= nl2br(htmlspecialchars($template['template_body'])) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">Belum ada template pesan.</div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Edit Template -->
<div id="messageModal" class="modal">
    <div class="modal-content modal-lg">
        <div class="modal-header">
            <h3 id="messageModalTitle">Edit Template Pesan</h3>
            <button type="button" onclick="closeMessageModal()" class="btn-close">&times;</button>
        </div>
        <form id="messageForm">
            <input type="hidden" name="template_id" id="messageTemplateId">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

            <div class="modal-body">
                <div class="form-group">
                    <label>Nama Template</label>
                    <input type="text" id="messageTemplateName" disabled class="form-control-disabled">
                </div>

                <div class="form-group">
                    <label>Format Pesan</label>
                    <textarea name="template_body" id="messageTemplateBody" rows="10" required class="form-control" placeholder="Tulis template pesan di sini..."></textarea>
                    <div class="form-help">
                        <strong>Variabel yang tersedia:</strong>
                        <div class="variables-grid">
                            <span class="variable-tag">{nama_klien}</span>
                            <span class="variable-tag">{nomor_registrasi}</span>
                            <span class="variable-tag">{status}</span>
                            <span class="variable-tag">{nama_pengirim}</span>
                            <span class="variable-tag">{phone}</span>
                            <span class="variable-tag">{alamat}</span>
                            <span class="variable-tag">{nama_kantor}</span>
                            <span class="variable-tag">{tanggal}</span>
                            <span class="variable-tag">{link_tracking}</span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Preview</label>
                    <div class="preview-box wa-preview-box">
                        <div id="messagePreview"></div>
                    </div>
                </div>
            </div>

            <div class="modal-actions">
                <button type="button" onclick="closeMessageModal()" class="btn-secondary">Batal</button>
                <button type="submit" class="btn-primary">Simpan Template</button>
            </div>
        </form>
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

.btn-back, .btn-close, .btn-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.btn-back {
    width: 40px;
    height: 40px;
    background: #f1f5f9;
    border-radius: 8px;
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

.btn-back:hover {
    background: #e2e8f0;
    transform: translateX(-4px);
}

.page-header h1 {
    font-size: 24px;
    color: #1B3A4B;
    margin: 0 0 4px 0;
    font-family: 'Cormorant Garamond', serif;
    font-weight: 700;
}

.page-header p {
    color: #64748b;
    margin: 0;
    font-size: 13px;
}

.templates-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(500px, 1fr));
    gap: 20px;
}

.template-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border: 1px solid #e2e8f0;
}

.template-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    padding-bottom: 12px;
    border-bottom: 1px solid #e2e8f0;
}

.template-header h4 {
    font-size: 15px;
    color: #1B3A4B;
    margin: 0 0 6px 0;
    font-weight: 600;
}

.template-key {
    background: #f1f5f9;
    color: #475569;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-family: monospace;
    font-weight: 600;
}

.template-description {
    font-size: 13px;
    color: #64748b;
    margin: 0 0 12px 0;
}

.btn-edit {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    background: #1B3A4B;
    color: #9C7C38;
    border: none;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
}

.btn-edit:hover {
    background: #2D5A6B;
}

.template-preview {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 16px;
}

.preview-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: #64748b;
    font-weight: 600;
    margin-bottom: 12px;
}

.preview-label svg {
    width: 14px;
    height: 14px;
}

.preview-content {
    font-size: 13px;
    line-height: 1.6;
    color: #334155;
    white-space: pre-wrap;
}

.wa-preview {
    background: #dcf8c6;
    border: 1px solid #25D366;
    padding: 16px;
    border-radius: 8px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #94a3b8;
    font-size: 14px;
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

.modal-lg {
    max-width: 800px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-content {
    background: white;
    border-radius: 12px;
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
    border-radius: 6px;
}

.btn-close:hover {
    background: #f1f5f9;
}

.modal-body {
    padding: 24px;
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
    resize: vertical;
}

.form-control:focus {
    outline: none;
    border-color: #1B3A4B;
    box-shadow: 0 0 0 3px rgba(27, 58, 75, 0.1);
}

.form-control-disabled {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    background: #f8fafc;
    color: #64748b;
}

.form-help {
    margin-top: 8px;
    font-size: 12px;
    color: #64748b;
}

.variables-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 8px;
}

.variable-tag {
    background: #f1f5f9;
    color: #475569;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-family: monospace;
    font-weight: 600;
    border: 1px solid #e2e8f0;
}

.preview-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 16px;
}

.wa-preview-box {
    background: #dcf8c6;
    border-color: #25D366;
}

#messagePreview {
    font-size: 13px;
    line-height: 1.6;
    color: #334155;
    white-space: pre-wrap;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
    .templates-grid {
        grid-template-columns: 1fr;
    }

    .page-header {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>

<script>
function editMessageTemplate(id, name, body) {
    document.getElementById('messageModalTitle').textContent = 'Edit Template: ' + name;
    document.getElementById('messageTemplateId').value = id;
    document.getElementById('messageTemplateName').value = name;
    document.getElementById('messageTemplateBody').value = body;
    updateMessagePreview(body);
    document.getElementById('messageModal').style.display = 'flex';
}

function closeMessageModal() {
    document.getElementById('messageModal').style.display = 'none';
    document.getElementById('messageForm').reset();
}

function updateMessagePreview(text) {
    document.getElementById('messagePreview').textContent = text;
}

document.getElementById('messageTemplateBody')?.addEventListener('input', (e) => {
    updateMessagePreview(e.target.value);
});

document.getElementById('messageModal').addEventListener('click', function(e) {
    if (e.target === this) closeMessageModal();
});

document.getElementById('messageForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();

    const btn = e.target.querySelector('button[type="submit"]');
    const originalText = btn.textContent;
    const messageDiv = document.getElementById('formMessageContainer');

    try {
        btn.disabled = true;
        btn.textContent = 'Menyimpan...';

        const formData = new FormData(e.target);
        const res = await fetch('<?= APP_URL ?>/index.php?gate=cms_save_message_tpl', {
            method: 'POST',
            body: formData
        });

        if (res.status === 401 || res.status === 403) {
            window.location.href = APP_URL + '/index.php?gate=login&expired=1';
            return;
        }

        const data = await res.json();

        if (data && data.message && data.message.includes('Session expired')) {
            window.location.href = APP_URL + '/index.php?gate=login&expired=1';
            return;
        }

        const messageClass = data.success ? 'success' : 'error';
        messageDiv.innerHTML = `<div class="form-message ${messageClass}">${data.message}</div>`;

        if (data.success) {
            setTimeout(() => {
                closeMessageModal();
                location.reload();
            }, 1000);
        } else {
            btn.disabled = false;
            btn.textContent = originalText;
        }
    } catch (err) {
        console.error(err);
        messageDiv.innerHTML = '<div class="form-message error">Terjadi kesalahan jaringan.</div>';
        btn.disabled = false;
        btn.textContent = originalText;
    }
});
</script>

<?php require VIEWS_PATH . '/templates/footer.php'; ?>
