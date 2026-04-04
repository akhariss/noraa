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
            <h1>Kelola Layanan</h1>
            <p>Tambah, edit, atau hapus jenis layanan notaris</p>
        </div>
    </div>

    <div class="form-message-container" id="formMessageContainer"></div>

    <div class="content-actions">
        <button type="button" onclick="showAddLayananModal()" class="btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Tambah Layanan
        </button>
    </div>

    <div class="table-container" style="overflow-x: auto; width: 100%; -webkit-overflow-scrolling: touch;">
        <table class="data-table" id="layananTable" style="width: 100%; min-width: 500px; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="width: 80px;">No</th>
                    <th>Nama Layanan</th>
                    <th style="width: 150px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($layanan)): ?>
                    <?php $no = 1; foreach ($layanan as $item): ?>
                        <tr id="layanan-row-<?= $item['id'] ?>">
                            <td><?= $no++ ?></td>
                            <td>
                                <span class="layanan-name" id="layanan-name-<?= $item['id'] ?>"><?= htmlspecialchars($item['nama_layanan']) ?></span>

                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 6px; justify-content: center;">
                                    <button type="button" onclick="editLayanan(<?= $item['id'] ?>, '<?= htmlspecialchars(addslashes($item['nama_layanan'])) ?>')" class="btn-icon btn-edit" title="Edit">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                    </button>
                                    <button type="button" onclick="confirmDeleteLayanan(<?= $item['id'] ?>, '<?= htmlspecialchars($item['nama_layanan']) ?>')" class="btn-icon btn-delete" <?= $item['id'] == 1 ? 'disabled' : '' ?> title="Hapus">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 30px; color: #94a3b8;">Belum ada data layanan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah/Edit Layanan -->
<div id="layananModal" class="modal">
    <div class="modal-content modal-sm">
        <div class="modal-header">
            <h3 id="modalTitle">Tambah Layanan</h3>
            <button type="button" onclick="closeLayananModal()" class="btn-close">&times;</button>
        </div>
        <form id="layananForm">
            <input type="hidden" name="id" id="layananId">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

            <div class="modal-body">
                <div class="form-group">
                    <label for="inputNamaLayanan">Nama Layanan</label>
                    <input type="text" name="nama_layanan" id="inputNamaLayanan" required placeholder="Contoh: Akta Jual Beli" class="form-control">
                </div>
            </div>

            <div class="modal-actions">
                <button type="button" onclick="closeLayananModal()" class="btn-secondary">Batal</button>
                <button type="submit" id="btnSubmitLayanan" class="btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div id="deleteModal" class="modal">
    <div class="modal-content modal-sm">
        <div class="modal-header">
            <h3>Konfirmasi Hapus</h3>
            <button type="button" onclick="closeDeleteModal()" class="btn-close">&times;</button>
        </div>
        <div class="modal-body">
            <div class="alert-warning">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 2h16.94a2 2 0 0 0 1.71-2L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
                <p id="deleteMessage"></p>
            </div>
            <div class="alert-info">
                <strong>Perhatian:</strong> Semua registrasi yang menggunakan layanan ini akan dialihkan ke layanan utama (ID: 1).
            </div>
        </div>
        <div class="modal-actions">
            <button type="button" onclick="closeDeleteModal()" class="btn-secondary">Batal</button>
            <button type="button" id="btnConfirmDelete" class="btn-delete">Ya, Hapus</button>
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

.content-actions {
    margin-bottom: 24px;
}

.btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    background: #1B3A4B;
    color: #9C7C38;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-primary:hover {
    background: #2D5A6B;
}

.table-container {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table thead {
    background: #f8fafc;
    border-bottom: 2px solid #e2e8f0;
}

.data-table th {
    padding: 14px 16px;
    text-align: left;
    font-size: 13px;
    font-weight: 600;
    color: #475569;
}

.data-table td {
    padding: 14px 16px;
    border-bottom: 1px solid #e2e8f0;
    font-size: 14px;
    color: #334155;
}

.data-table tbody tr:hover {
    background: #f8fafc;
}

.btn-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-icon.btn-edit {
    background: #e0f2fe;
    color: #0284c7;
}

.btn-icon.btn-edit:hover {
    background: #bae6fd;
}

.btn-icon.btn-delete {
    background: #fee2e2;
    color: #dc2626;
}

.btn-icon.btn-delete:hover:not(:disabled) {
    background: #fecaca;
}

.btn-icon:disabled {
    opacity: 0.5;
    cursor: not-allowed;
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
}

.btn-close:hover {
    background: #f1f5f9;
}

.modal-body {
    padding: 24px;
}

.form-group {
    margin-bottom: 0;
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
}

.form-control:focus {
    outline: none;
    border-color: #1B3A4B;
    box-shadow: 0 0 0 3px rgba(27, 58, 75, 0.1);
}

.modal-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    padding: 20px 24px;
    border-top: 1px solid #e2e8f0;
}

.btn-secondary {
    padding: 10px 20px;
    background: #f1f5f9;
    color: #475569;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
}

.btn-secondary:hover {
    background: #e2e8f0;
}

.btn-delete {
    padding: 10px 20px;
    background: #dc2626;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
}

.btn-delete:hover {
    background: #b91c1c;
}

.alert-warning, .alert-info {
    padding: 16px;
    border-radius: 8px;
    margin-bottom: 16px;
    display: flex;
    gap: 12px;
    align-items: flex-start;
}

.alert-warning {
    background: #fef3c7;
    border: 1px solid #f59e0b;
    color: #92400e;
}

.alert-warning p {
    margin: 0;
    font-size: 14px;
    line-height: 1.5;
}

.alert-info {
    background: #e0f2fe;
    border: 1px solid #0284c7;
    color: #0c4a6e;
    font-size: 13px;
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
</style>

<script>
let deleteLayananId = null;

function showAddLayananModal() {
    document.getElementById('modalTitle').textContent = 'Tambah Layanan';
    document.getElementById('layananId').value = '';
    document.getElementById('inputNamaLayanan').value = '';
    document.getElementById('layananModal').style.display = 'flex';
}

function editLayanan(id, name) {
    document.getElementById('modalTitle').textContent = 'Edit Layanan';
    document.getElementById('layananId').value = id;
    document.getElementById('inputNamaLayanan').value = name;
    document.getElementById('layananModal').style.display = 'flex';
}

function closeLayananModal() {
    document.getElementById('layananModal').style.display = 'none';
    document.getElementById('layananForm').reset();
}

function confirmDeleteLayanan(id, name) {
    if (id === 1) {
        alert('Layanan utama (ID: 1) tidak boleh dihapus.');
        return;
    }

    deleteLayananId = id;
    document.getElementById('deleteMessage').innerHTML = 'Apakah Anda yakin ingin menghapus layanan <strong>"' + name + '"</strong>?';
    document.getElementById('deleteModal').style.display = 'flex';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
    deleteLayananId = null;
}

function executeDelete() {
    if (!deleteLayananId) return;

    const btn = document.getElementById('btnConfirmDelete');
    const originalText = btn.textContent;

    btn.disabled = true;
    btn.textContent = 'Menghapus...';

    const formData = new FormData();
    formData.append('id', deleteLayananId);
    formData.append('csrf_token', '<?= generateCSRFToken() ?>');

    fetch('<?= APP_URL ?>/index.php?gate=cms_delete_layanan', {
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

        if (data.success) {
            closeDeleteModal();
            location.reload();
        } else {
            alert(data.message || 'Gagal menghapus layanan.');
            closeDeleteModal();
        }
    })
    .catch((err) => {
        console.error(err);
        alert('Terjadi kesalahan jaringan.');
        closeDeleteModal();
    })
    .finally(() => {
        btn.disabled = false;
        btn.textContent = originalText;
    });
}

document.getElementById('btnConfirmDelete').addEventListener('click', executeDelete);

document.getElementById('layananModal').addEventListener('click', function(e) {
    if (e.target === this) closeLayananModal();
});

document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});

document.getElementById('layananForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const id = document.getElementById('layananId').value;
    const gate = id ? 'cms_update_layanan' : 'cms_add_layanan';
    const btn = document.getElementById('btnSubmitLayanan');
    const originalText = btn.textContent;
    const messageDiv = document.getElementById('formMessageContainer');

    try {
        btn.disabled = true;
        btn.textContent = 'Menyimpan...';

        const formData = new FormData(e.target);
        const res = await fetch('<?= APP_URL ?>/index.php?gate=' + gate, {
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
                closeLayananModal();
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
