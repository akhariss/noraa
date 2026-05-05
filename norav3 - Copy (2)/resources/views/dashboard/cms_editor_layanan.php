<?php
/**
 * Nora Layanan Management - NATURAL LUXE EDITION v18.0
 * Service listing in a beautiful grid of cards.
 */
$activePage = 'app_settings';
require VIEWS_PATH . '/templates/header.php';
?>

<style>
:root {
    --gold: #9C7C38;
    --gold-light: #B8964F;
    --navy: #1B3A4B;
    --bg-cream: #F7F4EF;
    --cream: #F7F4EF;
    --text: #1B3A4B;
    --white: #FFFFFF;
}

.cms-layanan-luxe {
    background: #fdfdfd;
    min-height: 100vh;
    font-family: 'DM Sans', sans-serif;
    color: var(--navy);
    padding: 30px;
}

/* Header Premium */
.luxe-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 35px;
    background: #fff;
    padding: 25px 35px;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.03);
    border: 1px solid #F1E9D7;
}

.lh-left { display: flex; align-items: center; gap: 20px; }
.btn-back-diamond {
    width: 45px; height: 45px; background: var(--bg-cream); border: 1px solid var(--gold); border-radius: 12px;
    display: flex; align-items: center; justify-content: center; color: var(--gold); transition: 0.2s;
}
.btn-back-diamond:hover { background: var(--gold); color: #fff; transform: translateX(-5px); }

.lh-info h1 { margin: 0; font-family: 'Cormorant Garamond', serif; font-size: 28px; font-weight: 700; color: var(--navy); }
.lh-info p { margin: 2px 0 0; font-size: 14px; color: #888; }

.btn-add-luxe {
    background: var(--navy); color: #fff; border: none; padding: 12px 28px; border-radius: 50px;
    font-size: 13px; font-weight: 950; cursor: pointer; transition: 0.2s; display: flex; align-items: center; gap: 10px;
}
.btn-add-luxe:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(27,58,75,0.2); }

/* Table UI V4 (From Registrasi) */
.table-vault-v4 { background: #fff; border-radius: 15px; border: 1px solid var(--bg-cream); overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.02); margin-top: 10px; }
.table-head-v4 { padding: 15px 25px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f3f5; }

.table-responsive { overflow-x: auto; width: 100%; }
.lx-auth-table { width: 100%; border-collapse: collapse; }
.lx-auth-table th { 
    padding: 15px 20px; text-align: left; font-size: 11px; font-weight: 950; 
    text-transform: uppercase; letter-spacing: 0.5px;
    font-family: 'DM Sans', sans-serif;
    background: var(--cream) !important; color: var(--text) !important; 
    line-height: 1.6;
    border-bottom: 2px solid var(--gold);
}
.lx-auth-table td { padding: 14px 20px; font-size: 13.5px; color: var(--navy); border-bottom: 1px solid #f1f1f1; vertical-align: middle; }
.lx-auth-table tr:nth-child(even) { background: #fcfbf8; }
.lx-auth-table tr:hover { background: #fdfaf3 !important; }

/* Action Buttons (Table) */
.btn-auth-detail { 
    display: inline-flex; align-items: center; gap: 8px; 
    background: var(--cream) !important; color: var(--navy) !important; 
    padding: 8px 18px; border-radius: 50px; 
    font-size: 11.5px; font-weight: 800; 
    text-decoration: none; border: 1.5px solid var(--navy);
    cursor: pointer; transition: 0.2s;
}
.btn-auth-detail:hover { background: var(--navy) !important; color: var(--cream) !important; }
.btn-auth-detail i { width: 14px; height: 14px; border: 1.5px solid var(--navy); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 8px; font-style: normal; }
.btn-auth-detail:hover i { border-color: var(--cream); }

.btn-auth-delete {
    display: inline-flex; align-items: center; gap: 8px; 
    background: #fff !important; color: #EF4444 !important; 
    padding: 8px 18px; border-radius: 50px; 
    font-size: 11.5px; font-weight: 800; 
    text-decoration: none; border: 1.5px solid #FEE2E2;
    cursor: pointer; transition: 0.2s;
}
.btn-auth-delete:hover:not(:disabled) { background: #EF4444 !important; color: #fff !important; border-color: #EF4444; }
.btn-auth-delete:disabled { opacity: 0.3; cursor: not-allowed; }

.badge-auth-lx { padding: 4px 12px; border-radius: 50px; font-size: 9px; font-weight: 900; text-transform: uppercase; border: 1px solid; display: inline-block; background: #fff; color: var(--gold); border-color: #EEE; }
.badge-core { background: rgba(156, 124, 56, 0.1); color: var(--gold); border-color: rgba(156, 124, 56, 0.3); }

/* Unified Full-Width Button */
.btn-export-v4 { 
    height: 40px; padding: 0 20px; background: var(--navy); color: var(--cream); 
    border-radius: 8px; border: 1.5px solid var(--navy); font-weight: 800; 
    cursor: pointer; font-size: 11px; display: flex; align-items: center; gap: 10px; transition: 0.2s;
}
.btn-export-v4:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(27,58,75,0.2); }

/* Modal Luxe */
.nora-modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15,31,40,0.8); display: none; align-items: center; justify-content: center; z-index: 20000; pointer-events: auto; }
.nora-modal-content { background: #fff; width: 440px; border-radius: 20px; overflow: hidden; box-shadow: 0 25px 60px rgba(0,0,0,0.3); position: relative; z-index: 20001; }
.nora-modal-header { padding: 25px 30px; border-bottom: 1px solid #EEE; display:flex; justify-content:space-between; align-items:center; }
.nora-modal-header h3 { margin:0; font-family: 'Cormorant Garamond', serif; font-size:22px; }
.nora-modal-close { background: #fff; border: 1.5px solid #EEE; width:34px; height:34px; border-radius:50%; cursor:pointer; display:flex; align-items:center; justify-content:center; }
.nora-modal-close:hover { border-color: #EF4444; color: #EF4444; }

.nora-modal-body { padding: 30px; }
.f-group-luxe { margin-bottom: 20px; }
.f-group-luxe label { font-size: 9px; font-weight: 950; color: #AAA; text-transform: uppercase; margin-bottom: 8px; display: block; }
.f-input-luxe { width: 100%; padding: 14px; border: 2px solid #F5F5F5; border-radius: 12px; font-size: 15px; font-weight: 800; outline: none; transition: 0.2s; }
.f-input-luxe:focus { border-color: var(--gold); background: #FFF; }

.nora-modal-footer { padding: 20px 30px; background: #FAFAFA; display: flex; gap: 10px; justify-content: flex-end; }
.btn-nora-submit { background: var(--navy); color: #fff; padding: 12px 35px; border-radius: 50px; font-weight: 950; font-size: 13px; border: none; cursor: pointer; transition: 0.2s; }
.btn-nora-submit:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(27,58,75,0.3); }
.btn-nora-secondary { background: #fff; color: #888; border: 1.5px solid #EEE; padding: 12px 30px; border-radius: 50px; font-weight: 800; font-size: 12px; cursor: pointer; }

/* Confirm Modal */
.nora-confirm-overlay { position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(15,31,40,0.85); display:none; align-items:center; justify-content:center; z-index: 11000; }
.nora-confirm-card { background:#fff; width:360px; border-radius:24px; padding:35px; text-align:center; }
.nc-icon { font-size:45px; margin-bottom:15px; display:block; }
.nc-title { font-size:18px; font-weight:900; color:var(--navy); margin-bottom:10px; }
.nc-text { font-size:13px; color:#666; line-height:1.6; margin-bottom:25px; }

#toast { position: fixed; bottom: 30px; right: 30px; background: var(--navy); color: #fff; padding: 18px 35px; border-radius: 14px; font-weight: 950; box-shadow: 0 15px 40px rgba(0,0,0,0.2); border-left: 6px solid var(--gold); z-index: 12000; animation: slideIn 0.3s forwards; display: none; }
@keyframes slideIn { from { transform: translateY(100%); opacity:0; } to { transform: translateY(0); opacity:1; } }
</style>

<div class="cms-layanan-luxe">
    
    <!-- Table Container -->
    <div class="table-vault-v4">
        <div class="table-head-v4">
            <h4 style="font-size:12px; font-weight:900; color:var(--navy); margin:0; text-transform:uppercase;">DAFTAR LAYANAN SISTEM (<?= count($layanan) ?>)</h4>
            <div style="display:flex; gap:12px;">
                <button onclick="showAddLayananModal()" class="btn-export-v4">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    TAMBAH LAYANAN
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="lx-auth-table">
                <thead>
                    <tr>
                        <th style="width:100px;">ID</th>
                        <th>Nama Layanan</th>
                        <th>Tipe / Kategori</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($layanan)): ?>
                        <tr><td colspan="4" style="text-align:center; padding:60px; color:#bbb; font-style:italic;">Data tidak ditemukan.</td></tr>
                    <?php else: ?>
                        <?php foreach ($layanan as $item): 
                            $isCore = ($item['id'] == 1);
                        ?>
                        <tr>
                            <td style="font-weight:800; color:var(--gold);"><?= $item['id'] ?></td>
                            <td><div style="font-weight:800; color:var(--navy); font-size: 14px;"><?= htmlspecialchars($item['nama_layanan']) ?></div></td>
                            <td>
                                <span class="badge-auth-lx <?= $isCore ? 'badge-core' : '' ?>">
                                    <?= $isCore ? 'CORE SYSTEM' : 'DYNAMIC' ?>
                                </span>
                            </td>
                            <td style="text-align:right;">
                                <div style="display:flex; gap:8px; justify-content:flex-end;">
                                    <button onclick="editLayanan(<?= $item['id'] ?>, '<?= htmlspecialchars(addslashes($item['nama_layanan'])) ?>')" class="btn-auth-detail"><i>o</i> Edit</button>
                                    <button onclick="confirmDeleteLayanan(<?= $item['id'] ?>, '<?= htmlspecialchars($item['nama_layanan']) ?>')" class="btn-auth-delete" <?= $isCore ? 'disabled' : '' ?>>&times; Hapus</button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Luxe -->
<div id="layananModal" class="nora-modal-overlay">
    <div class="nora-modal-content">
        <div class="nora-modal-header">
            <h3 id="modalTitle">Tambah Layanan</h3>
            <button onclick="closeLayananModal()" class="nora-modal-close">&times;</button>
        </div>
        <form id="layananForm">
            <input type="hidden" name="id" id="layananId">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            <div class="nora-modal-body">
                <div class="f-group-luxe">
                    <label>Nama Layanan</label>
                    <input type="text" name="nama_layanan" id="inputNamaLayanan" required placeholder="Contoh: Akta Jual Beli" class="f-input-luxe">
                </div>
            </div>
            <div class="nora-modal-footer">
                <button type="button" onclick="closeLayananModal()" class="btn-nora-secondary">Batal</button>
                <button type="submit" id="btnSubmitLayanan" class="btn-nora-submit">Simpan Layanan</button>
            </div>
        </form>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmOverlay" class="nora-confirm-overlay">
    <div class="nora-confirm-card">
        <span id="ncIcon" class="nc-icon">❓</span>
        <h3 id="ncTitle" class="nc-title">Konfirmasi</h3>
        <p id="ncText" class="nc-text"></p>
        <div style="display:flex; gap:10px; justify-content:center;">
            <button onclick="closeConfirm(false)" class="btn-nora-secondary">Batal</button>
            <button id="btnConfirmOk" class="btn-nora-submit">Ya, Lanjutkan</button>
        </div>
    </div>
</div>

<div id="toast">Notif</div>

<script>
let confirmCallback = null;

function noraConfirm(text, callback, icon = '❓', title = 'Konfirmasi') {
    document.getElementById('ncText').innerText = text;
    document.getElementById('ncTitle').innerText = title;
    document.getElementById('ncIcon').innerText = icon;
    document.getElementById('confirmOverlay').style.display = 'flex';
    confirmCallback = callback;
    document.getElementById('btnConfirmOk').onclick = () => { closeConfirm(true); };
}

function closeConfirm(isOk) {
    document.getElementById('confirmOverlay').style.display = 'none';
    if (isOk && confirmCallback) confirmCallback();
    confirmCallback = null;
}

function showAddLayananModal() {
    document.getElementById('modalTitle').innerText = 'Tambah Layanan Baru';
    document.getElementById('layananId').value = '';
    const input = document.getElementById('inputNamaLayanan');
    input.value = '';
    document.getElementById('layananModal').style.display = 'flex';
    setTimeout(() => input.focus(), 100);
}

function editLayanan(id, name) {
    document.getElementById('modalTitle').innerText = 'Edit Layanan';
    document.getElementById('layananId').value = id;
    const input = document.getElementById('inputNamaLayanan');
    input.value = name;
    document.getElementById('layananModal').style.display = 'flex';
    setTimeout(() => input.focus(), 100);
}

function closeLayananModal() {
    document.getElementById('layananModal').style.display = 'none';
}

async function confirmDeleteLayanan(id, name) {
    noraConfirm(
        `Apakah Anda yakin ingin menghapus layanan "${name}"? Semua registrasi yang menggunakan layanan ini akan dialihkan ke layanan utama secara otomatis.`,
        async () => {
            const fd = new FormData();
            fd.append('id', id);
            fd.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);
            
            try {
                const res = await fetch('<?= APP_URL ?>/index.php?gate=cms_delete_layanan', {
                    method: 'POST',
                    body: fd
                });
                const data = await res.json();
                if (data.success) {
                    showToast('Layanan berhasil dihapus.');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    alert(data.message);
                }
            } catch (e) {
                alert('Kesalahan koneksi.');
            }
        },
        '🗑️',
        'Hapus Layanan'
    );
}

document.getElementById('layananForm').onsubmit = async (e) => {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitLayanan');
    btn.disabled = true;
    
    const id = document.getElementById('layananId').value;
    const gate = id ? 'cms_update_layanan' : 'cms_add_layanan';
    
    const fd = new FormData(e.target);
    try {
        const res = await fetch(`<?= APP_URL ?>/index.php?gate=${gate}`, {
            method: 'POST',
            body: fd
        });
        const data = await res.json();
        if (data.success) {
            showToast('Data layanan tersimpan.');
            setTimeout(() => location.reload(), 1000);
        } else {
            alert(data.message);
            btn.disabled = false;
        }
    } catch (e) {
        alert('Kesalahan koneksi.');
        btn.disabled = false;
    }
};

function showToast(m) {
    const t = document.getElementById('toast');
    t.innerText = m;
    t.style.display = 'block';
    setTimeout(() => { t.style.display = 'none'; }, 3000);
}

window.onclick = (e) => {
    if (e.target.id === 'layananModal') closeLayananModal();
};
</script>

<?php require VIEWS_PATH . '/templates/footer.php'; ?>
