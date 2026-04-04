<?php
$activePage = 'cms';
require VIEWS_PATH . '/templates/header.php';
?>

<div class="cms-page-wrapper">
    <!-- Page Header -->
    <div class="cms-page-header">
        <div class="container">
            <div class="page-header-content">
                <a href="<?= APP_URL ?>/index.php?gate=cms_editor" class="btn-back">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                </a>
                <div class="header-text">
                    <span class="page-badge">Template Management</span>
                    <h1>Template Catatan Status</h1>
                    <p>Edit template catatan internal yang diotomatisasi saat merubah status registrasi</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="form-message-container" id="formMessageContainer"></div>

        <div class="templates-container">
            <?php if (!empty($templates)): ?>
                <?php foreach ($templates as $template): ?>
                    <?php
                    $statusName = $statusLabels[$template['status_key']] ?? $template['status_label'];
                    ?>
                    <div class="template-card" id="note-card-<?= $template['id'] ?>">
                        <div class="template-top">
                            <div class="template-info">
                                <span class="status-badge"><?= htmlspecialchars($statusName) ?></span>
                                <span class="template-key"><?= htmlspecialchars($template['status_key']) ?></span>
                            </div>
                            <button type="button" onclick='editNoteTemplate(<?= $template['id'] ?>, <?= htmlspecialchars(json_encode($statusName, JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES) ?>, <?= htmlspecialchars(json_encode($template['template_body'], JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES) ?>)' class="btn-edit">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                                Edit Template
                            </button>
                        </div>
                        <div class="template-preview-section">
                            <div class="preview-label">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                </svg>
                                Preview Catatan
                            </div>
                            <div class="preview-content"><?= nl2br(htmlspecialchars($template['template_body'])) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                    <p>Belum ada template catatan.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Edit Template -->
<div id="noteModal" class="modal">
    <div class="modal-content modal-lg">
        <div class="modal-header">
            <h3 id="noteModalTitle">Edit Template Catatan</h3>
            <button type="button" onclick="closeNoteModal()" class="btn-close">&times;</button>
        </div>
        <form id="noteForm">
            <input type="hidden" name="template_id" id="noteTemplateId">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

            <div class="modal-body">
                <div class="form-group">
                    <label>Status</label>
                    <input type="text" id="noteTemplateName" disabled class="form-control-disabled">
                </div>

                <div class="form-group">
                    <label>Format Catatan</label>
                    <textarea name="template_body" id="noteTemplateBody" rows="8" required class="form-control" placeholder="Tulis template catatan di sini..."></textarea>
                    <div class="form-help">
                        <strong>Variabel yang tersedia:</strong>
                        <div class="variables-grid">
                            <span class="variable-tag">{nama_klien}</span>
                            <span class="variable-tag">{nomor_registrasi}</span>
                            <span class="variable-tag">{status}</span>
                            <span class="variable-tag">{user_name}</span>
                            <span class="variable-tag">{nama_kantor}</span>
                            <span class="variable-tag">{phone}</span>
                            <span class="variable-tag">{tanggal}</span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Preview</label>
                    <div class="preview-box">
                        <div id="notePreview"></div>
                    </div>
                </div>
            </div>

            <div class="modal-actions">
                <button type="button" onclick="closeNoteModal()" class="btn-secondary">Batal</button>
                <button type="submit" class="btn-primary">Simpan Template</button>
            </div>
        </form>
    </div>
</div>

<style>
.cms-page-wrapper {
    min-height: 100vh;
    background: var(--cream);
    padding-bottom: 60px;
}

.cms-page-header {
    background: linear-gradient(145deg, var(--primary-dark) 0%, var(--primary) 50%, var(--primary-light) 100%);
    padding: 50px 0 30px;
    position: relative;
    overflow: hidden;
    margin-bottom: 20px;
}

.cms-page-header::before {
    content: '';
    position: absolute;
    top: -30%;
    right: -15%;
    width: 700px;
    height: 700px;
    background: radial-gradient(circle, rgba(156, 124, 56, 0.12) 0%, transparent 60%);
    animation: float 8s ease-in-out infinite;
}

.page-header-content {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    gap: 24px;
}

.btn-back {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 56px;
    height: 56px;
    background: rgba(255,255,255,0.1);
    border-radius: 12px;
    transition: all 0.3s;
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255,255,255,0.2);
}

.btn-back svg {
    width: 28px;
    height: 28px;
    color: var(--gold);
}

.btn-back:hover {
    background: rgba(255,255,255,0.2);
    transform: translateX(-4px);
    border-color: var(--gold);
}

.header-text {
    flex: 1;
}

.page-badge {
    display: inline-block;
    background: rgba(156, 124, 56, 0.15);
    color: var(--gold-light);
    padding: 10px 14px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-bottom: 0;
    border: 1px solid rgba(156, 124, 56, 0.3);
}

.header-text h1 {
    font-size: 0;
    height: 0;
    margin: 0;
    visibility: hidden;
}

.header-text p {
    color: rgba(255,255,255,0.85);
    margin: 0;
    font-size: 15px;
    padding-bottom: 10px;
}

.templates-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(550px, 1fr));
    gap: 24px;
    margin-bottom: 40px;
}

.template-card {
    background: var(--white);
    border-radius: 18px;
    padding: 28px;
    box-shadow: 0 15px 45px rgba(0, 0, 0, 0.06);
    border: 2px solid rgba(156, 124, 56, 0.2);
    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
}

.template-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 25px 60px rgba(27, 58, 75, 0.15);
    border-color: rgba(156, 124, 56, 0.4);
}

.template-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 2px solid var(--cream);
}

.template-info {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.status-badge {
    background: linear-gradient(135deg, var(--primary), var(--primary-light));
    color: var(--gold);
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 700;
    box-shadow: 0 4px 12px rgba(27, 58, 75, 0.2);
}

.template-key {
    background: var(--cream);
    color: var(--primary);
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 11px;
    font-family: monospace;
    font-weight: 600;
    border: 1px solid rgba(156, 124, 56, 0.2);
}

.btn-edit {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: linear-gradient(135deg, var(--gold), var(--gold-light));
    color: var(--primary-dark);
    border: none;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 4px 12px rgba(156, 124, 56, 0.3);
}

.btn-edit:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(156, 124, 56, 0.4);
}

.template-preview-section {
    background: var(--cream);
    border: 2px solid rgba(156, 124, 56, 0.2);
    border-radius: 12px;
    padding: 20px;
}

.preview-label {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 12px;
    color: var(--primary);
    font-weight: 700;
    margin-bottom: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.preview-label svg {
    width: 16px;
    height: 16px;
    color: var(--gold);
}

.preview-content {
    font-size: 14px;
    line-height: 1.8;
    color: var(--text);
    white-space: pre-wrap;
    background: var(--white);
    padding: 18px;
    border-radius: 10px;
    border-left: 4px solid var(--gold);
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 80px 20px;
    color: var(--text-muted);
}

.empty-state svg {
    width: 64px;
    height: 64px;
    margin-bottom: 16px;
    opacity: 0.5;
}

.empty-state p {
    font-size: 16px;
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.6);
    z-index: 1000;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(5px);
}

.modal-lg {
    max-width: 800px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-content {
    background: var(--white);
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 24px 32px;
    border-bottom: 2px solid var(--cream);
}

.modal-header h3 {
    margin: 0;
    color: var(--primary);
    font-size: 22px;
    font-family: 'Cormorant Garamond', serif;
    font-weight: 700;
}

.btn-close {
    background: none;
    border: none;
    font-size: 28px;
    color: var(--text-muted);
    cursor: pointer;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    transition: all 0.3s;
}

.btn-close:hover {
    background: var(--cream);
    color: var(--primary);
}

.modal-body {
    padding: 32px;
}

.form-group {
    margin-bottom: 24px;
}

.form-group label {
    display: block;
    font-weight: 700;
    margin-bottom: 10px;
    color: var(--primary);
    font-size: 14px;
}

.form-control {
    width: 100%;
    padding: 14px 18px;
    border: 2px solid var(--border);
    border-radius: 10px;
    font-size: 14px;
    font-family: inherit;
    resize: vertical;
    transition: all 0.3s;
}

.form-control:focus {
    outline: none;
    border-color: var(--gold);
    box-shadow: 0 0 0 4px rgba(156, 124, 56, 0.1);
}

.form-control-disabled {
    width: 100%;
    padding: 14px 18px;
    border: 2px solid var(--cream);
    border-radius: 10px;
    font-size: 14px;
    background: var(--cream);
    color: var(--text-muted);
}

.form-help {
    margin-top: 10px;
    font-size: 13px;
    color: var(--text-muted);
}

.variables-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 10px;
}

.variable-tag {
    background: var(--cream);
    color: var(--primary);
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 11px;
    font-family: monospace;
    font-weight: 700;
    border: 1px solid rgba(156, 124, 56, 0.2);
}

.preview-box {
    background: var(--cream);
    border: 2px solid rgba(156, 124, 56, 0.2);
    border-radius: 12px;
    padding: 20px;
}

#notePreview {
    font-size: 14px;
    line-height: 1.8;
    color: var(--text);
    white-space: pre-wrap;
    background: var(--white);
    padding: 16px;
    border-radius: 10px;
    border-left: 4px solid var(--gold);
}

.modal-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    padding: 24px 32px;
    border-top: 2px solid var(--cream);
}

.btn-secondary, .btn-primary {
    padding: 12px 28px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    border: none;
    transition: all 0.3s;
}

.btn-secondary {
    background: var(--cream);
    color: var(--primary);
}

.btn-secondary:hover {
    background: var(--border);
}

.btn-primary {
    background: linear-gradient(135deg, var(--gold), var(--gold-light));
    color: var(--primary-dark);
    box-shadow: 0 4px 12px rgba(156, 124, 56, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(156, 124, 56, 0.4);
}

.form-message-container {
    margin-bottom: 20px;
}

.form-message {
    padding: 14px 18px;
    border-radius: 10px;
    font-size: 14px;
    margin-bottom: 12px;
    animation: slideIn 0.3s ease-out;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.form-message.success {
    background: linear-gradient(135deg, #d4edda, #c3e6cb);
    color: #155724;
    border: 2px solid #c3e6cb;
}

.form-message.error {
    background: linear-gradient(135deg, #f8d7da, #f5c6cb);
    color: #721c24;
    border: 2px solid #f5c6cb;
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
    .templates-container {
        grid-template-columns: 1fr;
    }

    .page-header-content {
        flex-direction: column;
        align-items: flex-start;
    }

    .header-text h1 {
        font-size: 28px;
    }
}
</style>

<script>
function editNoteTemplate(id, name, body) {
    document.getElementById('noteModalTitle').textContent = 'Edit Template: ' + name;
    document.getElementById('noteTemplateId').value = id;
    document.getElementById('noteTemplateName').value = name;
    document.getElementById('noteTemplateBody').value = body;
    updateNotePreview(body);
    document.getElementById('noteModal').style.display = 'flex';
}

function closeNoteModal() {
    document.getElementById('noteModal').style.display = 'none';
    document.getElementById('noteForm').reset();
}

function updateNotePreview(text) {
    document.getElementById('notePreview').textContent = text;
}

document.getElementById('noteTemplateBody')?.addEventListener('input', (e) => {
    updateNotePreview(e.target.value);
});

document.getElementById('noteModal').addEventListener('click', function(e) {
    if (e.target === this) closeNoteModal();
});

document.getElementById('noteForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();

    const btn = e.target.querySelector('button[type="submit"]');
    const originalText = btn.textContent;
    const messageDiv = document.getElementById('formMessageContainer');

    try {
        btn.disabled = true;
        btn.textContent = 'Menyimpan...';

        const formData = new FormData(e.target);
        const res = await fetch('<?= APP_URL ?>/index.php?gate=cms_save_note_tpl', {
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
                closeNoteModal();
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
