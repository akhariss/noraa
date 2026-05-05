<?php
/**
 * Nora Workflow Management - THE ELITE SPLIT v18.0 (The Unified Strategy)
 * Side-by-Side: Operational (Left) vs. Patent (Right).
 * action bar: [Batal] [Simpan Urutan] [+ Tambah]
 * Patent Locking & Production Flexibility.
 */

$activePage = 'app_settings';
$pageTitle = 'Manajemen Alur Kerja';
require VIEWS_PATH . '/templates/header.php';

$wfSteps = $workflowSteps ?? [];
$patentRoles = [0, 8, 3, 4, 5, 7, 6];

$leftSteps = [];
$rightSteps = [];

foreach ($wfSteps as $s) {
    if (in_array((int)$s['behavior_role'], $patentRoles)) $rightSteps[] = $s;
    else $leftSteps[] = $s;
}

// Right column metadata mapping
$patentDefs = [
    0 => ['role' => 'AWAL', 'icon' => '🆕', 'hint' => 'Wajib di urutan pertama (Draft).'],
    8 => ['role' => 'VERIFIKASI', 'icon' => '🔍', 'hint' => 'Proses validasi akhir berkas.'],
    3 => ['role' => 'REVISI', 'icon' => '🛠️', 'hint' => 'Pengembalian berkas bermasalah.'],
    4 => ['role' => 'SIAP', 'icon' => '✅', 'hint' => 'Berkas sudah selesai dikerjakan.'],
    5 => ['role' => 'SERAH', 'icon' => '🎁', 'hint' => 'Penyerahan akhir ke klien.'],
    7 => ['role' => 'BATAL', 'icon' => '❌', 'hint' => 'Tahapan pembatalan registrasi.'],
    6 => ['role' => 'TUTUP', 'icon' => '🔒', 'hint' => 'Arsip dan penutupan data.']
];
?>

<div class="cms-workflow-v18">
    <div class="elite-grid">
        
        <!-- LEFT: OPERATIONAL WORKFLOW -->
        <div class="grid-section section-left">
            <div class="section-action-header">
                <div class="sah-left">
                    <span class="pulse-icon">⚡</span>
                    <h3>Alur Pengerjaan Berkala</h3>
                </div>
                <div class="sah-right">
                    <button type="button" onclick="noraConfirm('Batalkan semua perubahan urutan yang belum disimpan?', () => location.reload(), '🔄')" class="btn-action-ghost-danger">Batal</button>
                    <button type="button" onclick="saveOperationalSequence()" class="btn-action-gold">Simpan Urutan</button>
                    <button type="button" onclick="openStepModal(null, false)" class="btn-action-navy">+ Tambah</button>
                </div>
            </div>

            <div class="scroll-container" id="leftSequence">
                <?php if (empty($leftSteps)): ?>
                    <div class="empty-state">Belum ada tahapan operasional. Klik tambah untuk memulai.</div>
                <?php else: ?>
                    <?php foreach ($leftSteps as $step): ?>
                        <div class="luxe-card-small op-item" data-id="<?= $step['id'] ?>">
                            <div class="lcs-header">
                                <div class="lcs-tag">#<?= (int)$step['sort_order'] ?></div>
                                <h4 class="lcs-title"><?= htmlspecialchars($step['label']) ?></h4>
                                <span class="lcs-role-badge"><?= (int)$step['behavior_role'] == 1 ? 'PROS A' : 'PROS B' ?></span>
                            </div>
                            <div class="lcs-body">
                                <div class="lcs-info">
                                    <span>Target: <b><?= (int)$step['sla_days'] ?> Hari</b></span>
                                    <span>ID: <code style="color: var(--gold);"><?= htmlspecialchars($step['step_key']) ?></code></span>
                                </div>
                                <div class="lcs-actions">
                                    <div class="move-group">
                                        <button onclick="shiftItem(<?= $step['id'] ?>, 'up')" class="btn-mini-move">↑</button>
                                        <button onclick="shiftItem(<?= $step['id'] ?>, 'down')" class="btn-mini-move">↓</button>
                                    </div>
                                    <button onclick='openStepModal(<?= json_encode($step, JSON_HEX_APOS | JSON_HEX_QUOT) ?>, false)' class="btn-mini-edit">Edit</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- RIGHT: PATENT FIXED CONFIG -->
        <div class="grid-section section-right">
            <div class="section-action-header">
                <div class="sah-left">
                    <span class="pulse-icon blue">⚖️</span>
                    <h3>Konfigurasi Paten (Wajib)</h3>
                </div>
            </div>

            <div class="patent-list-v18">
                <?php foreach ($rightSteps as $step): 
                    $def = $patentDefs[(int)$step['behavior_role']] ?? ['role' => 'SISTEM', 'icon' => '📦', 'hint' => 'Tahapan sistem.'];
                ?>
                    <div class="patent-card-v18">
                        <div class="pc-top">
                            <div class="pc-meta">
                                <span class="pc-icon"><?= $def['icon'] ?></span>
                                <div class="pc-titles">
                                    <span class="pc-role"><?= $def['role'] ?></span>
                                    <h5 class="pc-name"><?= htmlspecialchars($step['label']) ?></h5>
                                </div>
                            </div>
                            <button onclick='openStepModal(<?= json_encode($step, JSON_HEX_APOS | JSON_HEX_QUOT) ?>, true)' class="btn-pc-edit">Edit</button>
                        </div>
                        <div class="pc-bottom">
                            <span class="pc-sla"><?= (int)$step['sla_days'] ?> Hari SLA</span>
                            <p class="pc-hint"><?= $def['hint'] ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</div>

    <!-- CUSTOM CONFIRM MODAL -->
    <div id="confirmOverlay" class="nora-confirm-overlay">
        <div class="nora-confirm-card">
            <span id="ncIcon" class="nc-icon">❓</span>
            <h3 id="ncTitle" class="nc-title">Konfirmasi</h3>
            <p id="ncText" class="nc-text">Apakah Anda yakin ingin melanjutkan?</p>
            <div class="nc-footer">
                <button type="button" onclick="closeConfirm(false)" class="btn-nora-secondary-sm">Batal</button>
                <button type="button" id="btnConfirmOk" class="btn-nora-submit-sm">Ya, Lanjutkan</button>
            </div>
        </div>
    </div>

<!-- Modal v18 -->
<div id="stepModal" class="nora-modal-overlay">
    <div class="nora-modal-content">
        <div class="nora-modal-header">
            <h3 id="modalTitle">Setelan Tahapan</h3>
            <button type="button" onclick="closeModal()" class="nora-modal-close">&times;</button>
        </div>
        <form id="stepForm">
            <input type="hidden" name="id" id="f_id">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

            <div class="nora-modal-body">
                <!-- Hidden input for behavior_role to preserve it for patent steps -->
                <input type="hidden" name="behavior_role" id="f_role_hidden" disabled>

                <div class="f-group-luxe">
                    <label>JUDUL TAHAPAN</label>
                    <input type="text" name="label" id="f_label" required class="f-input-luxe">
                </div>

                <div class="f-row">
                    <div class="f-group-luxe">
                        <label>SLA (HARI)</label>
                        <input type="number" name="sla_days" id="f_sla" required min="0" value="0" class="f-input-luxe">
                    </div>
                    <div class="f-group-luxe" id="idGroup">
                        <label>ID SISTEM (UNIQUE)</label>
                        <input type="text" name="step_key" id="f_key" required class="f-input-luxe">
                    </div>
                </div>

                <div class="f-group-luxe" id="roleGroup">
                    <label>PERAN LOGIKA SISTEM</label>
                    <div class="role-selector-v18">
                        <label class="role-tile"><input type="radio" name="behavior_role" id="role1" value="1"><span>PROSES A</span></label>
                        <label class="role-tile"><input type="radio" name="behavior_role" id="role2" value="2"><span>PROSES B</span></label>
                    </div>
                </div>

                <div id="cancelArea">
                    <label class="nora-checkbox-label">
                        <input type="checkbox" name="is_cancellable" id="f_cancel" value="1">
                        <span class="checkbox-custom"></span>
                        <span style="font-size: 12px; font-weight: 850;">Aktifkan Pembatalan di Tahap Ini</span>
                    </label>
                </div>
            </div>

            <div class="nora-modal-footer">
                <button type="button" id="btnDelete" onclick="deleteStep()" class="btn-nora-danger-sm">Hapus</button>
                <div style="flex: 1;"></div>
                <button type="button" onclick="closeModal()" class="btn-nora-secondary-sm">Batal</button>
                <button type="submit" id="btnSubmit" class="btn-nora-submit-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<style>
:root {
    --gold: var(--gold);
    --navy: var(--primary);
    --bg-cream: var(--cream);
    --border-luxe: var(--gold-light);
}

.dashboard-workflow-v18 {
    background: #fdfdfd;
    min-height: 100vh;
    font-family: 'DM Sans', sans-serif;
    color: var(--navy);
    padding: 30px;
}
.elite-grid { display: grid; grid-template-columns: 1fr 400px; gap: 20px; align-items: flex-start; }

.grid-section { background: #fff; border-radius: 12px; border: 1px solid var(--border); box-shadow: 0 5px 20px rgba(0,0,0,0.03); overflow: hidden; }
.section-action-header { padding: 15px 25px; border-bottom: 2px solid #F8F8F8; display: flex; justify-content: space-between; align-items: center; background: #fafafa; }
.sah-left { display: flex; align-items: center; gap: 12px; }
.pulse-icon { width: 32px; height: 32px; background: #fff; border: 1px solid var(--gold); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 18px; }
.pulse-icon.blue { border-color: var(--navy); }
.sah-left h3 { margin: 0; font-size: 14px; font-weight: 900; color: var(--navy); text-transform: uppercase; letter-spacing: 1px; }

.sah-right { display: flex; gap: 10px; }
.btn-action-ghost-danger { background: #fff; border: 1.5px solid #FEE2E2; padding: 10px 22px; border-radius: 50px; font-size: 11px; font-weight: 850; color: #EF4444; cursor: pointer; transition: 0.2s; }
.btn-action-ghost-danger:hover { background: #FEF2F2; border-color: #EF4444; }

.btn-action-gold { background: var(--gold); color: var(--navy); border: none; padding: 10px 25px; border-radius: 50px; font-size: 11px; font-weight: 950; cursor: pointer; transition: 0.2s; box-shadow: 0 4px 15px rgba(156,124,56,0.2); }
.btn-action-gold:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(156,124,56,0.3); }

.btn-action-navy { background: var(--navy); color: #fff; border: none; padding: 10px 25px; border-radius: 50px; font-size: 11px; font-weight: 950; cursor: pointer; transition: 0.2s; }
.btn-action-navy:hover { transform: translateY(-2px); opacity: 0.9; }

/* Edit Buttons */
.btn-mini-edit { background: #fff; color: var(--gold-light); border: 1.5px solid var(--gold-light); padding: 7px 18px; border-radius: 8px; font-size: 11px; font-weight: 950; cursor: pointer; transition: 0.2s; }
.btn-mini-edit:hover { background: var(--navy); color: #fff; border-color: var(--navy); transform: scale(1.05); }

.btn-pc-edit { background: #fff; color: var(--navy); border: 1.5px solid #EEE; padding: 7px 18px; border-radius: 50px; font-size: 10px; font-weight: 950; cursor: pointer; transition: 0.2s; }
.btn-pc-edit:hover { border-color: var(--gold); color: var(--gold); }

/* Confirmation Modal Luxe */
.nora-confirm-overlay { position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(15,31,40,0.8); display:none; align-items:center; justify-content:center; z-index: 20000; }
.nora-confirm-card { background:#fff; width:360px; border-radius:20px; padding:30px; text-align:center; box-shadow: 0 20px 50px rgba(0,0,0,0.3); }
.nc-icon { font-size:40px; margin-bottom:15px; display:block; }
.nc-title { font-size:18px; font-weight:850; color:var(--navy); margin-bottom:10px; }
.nc-text { font-size:13px; color:#666; line-height:1.6; margin-bottom:25px; }
.nc-footer { display:flex; gap:10px; justify-content:center; }

/* Left Section: Luxe Cards */
.scroll-container { padding: 25px; display: grid; grid-template-columns: 1fr; gap: 15px; }
.luxe-card-small { background: var(--bg-cream); border: 1.5px solid #F1E9D7; border-radius: 12px; padding: 18px; transition: 0.2s; cursor: grab; }
.luxe-card-small:active { cursor: grabbing; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
.luxe-card-small:hover { border-color: var(--gold); background: #FFF; }
.lcs-header { display: flex; align-items: center; gap: 10px; margin-bottom: 15px; }
.lcs-tag { font-size: 10px; font-weight: 950; background: var(--navy); color: #fff; padding: 4px 8px; border-radius: 6px; }
.lcs-title { margin: 0; font-size: 15px; font-weight: 850; color: var(--navy); flex: 1; }
.lcs-role-badge { font-size: 9px; font-weight: 900; color: var(--gold); border: 1px solid var(--gold); padding: 2px 6px; border-radius: 4px; }
.lcs-body { display: flex; justify-content: space-between; align-items: center; }
.lcs-info { display: flex; flex-direction: column; gap: 2px; font-size: 11px; color: #888; }
.lcs-actions { display: flex; align-items: center; gap: 10px; }
.move-group { display: flex; gap: 4px; }
.btn-mini-move { width: 30px; height: 30px; background: #fff; border: 1px solid #EEE; border-radius: 6px; font-weight: 900; cursor: pointer; }

/* Sortable Shadow Style */
.sortable-ghost { opacity: 0.4; border: 2px dashed var(--gold); }
.sortable-chosen { background: #fff !important; border-color: var(--gold); }

/* Right Section: Patent */
.patent-list-v18 { padding: 25px; display: flex; flex-direction: column; gap: 12px; }
.patent-card-v18 { background: var(--bg-cream); border: 1.5px solid #F1E9D7; border-radius: 12px; padding: 15px; }
.pc-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
.pc-meta { display: flex; align-items: center; gap: 12px; }
.pc-icon { font-size: 20px; }
.pc-role { font-size: 9px; font-weight: 950; color: #BA9945; display: block; text-transform: uppercase; }
.pc-name { margin: 2px 0 0; font-size: 14px; font-weight: 850; color: var(--navy); }
.btn-pc-edit { background: none; border: 1.5px solid #DDD; padding: 6px 16px; border-radius: 50px; font-size: 10px; font-weight: 850; color: #666; cursor: pointer; }
.pc-bottom { padding-top: 8px; border-top: 1px dashed #EEDFBB; display: flex; justify-content: space-between; align-items: center; }
.pc-sla { font-size: 11px; font-weight: 900; color: #555; }
.pc-hint { font-size: 11px; color: #999; margin: 0; }

/* Modal Luxe */
.nora-modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15,31,40,0.7); display: none; align-items: center; justify-content: center; z-index: 10000; }
.nora-modal-content { background: #fff; width: 440px; border-radius: 16px; box-shadow: 0 25px 60px rgba(0,0,0,0.3); }
.nora-modal-header { padding: 15px 25px; border-bottom: 1px solid #EEE; display: flex; justify-content: space-between; align-items: center; }
.nora-modal-close { background: #fff; border: 1.5px solid #EEE; font-size: 20px; line-height: 1; color: #BBB; cursor: pointer; transition: 0.2s; display: flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 50%; }
.nora-modal-close:hover { background: #FFF1F1; color: #EF4444; border-color: #EF4444; transform: rotate(90deg); }
.nora-modal-body { padding: 25px; }
.f-group-luxe { margin-bottom: 20px; }
.f-group-luxe label { font-size: 9px; font-weight: 950; color: #AAA; text-transform: uppercase; margin-bottom: 8px; display: block; }
.f-input-luxe { width: 100%; padding: 14px; border: 2px solid #F5F5F5; border-radius: 10px; font-size: 15px; font-weight: 750; outline: none; }
.f-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }

.role-selector-v18 { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.role-tile { cursor: pointer; }
.role-tile input { display: none; }
.role-tile span { display: block; padding: 12px; border: 2.5px solid #F5F5F5; border-radius: 10px; text-align: center; font-size: 12px; font-weight: 900; transition: 0.1s; }
.role-tile input:checked + span { background: var(--navy); color: var(--gold); border-color: var(--navy); }

.nora-modal-footer { padding: 20px 25px; background: #FAFAFA; display: flex; gap: 10px; }
.btn-nora-submit-sm { background: var(--navy); color: #fff; padding: 12px 30px; border-radius: 50px; font-weight: 950; font-size: 12px; border: none; cursor: pointer; transition: 0.2s; }
.btn-nora-secondary-sm { background: #fff; color: #888; border: 1.5px solid #EEE; padding: 12px 30px; border-radius: 50px; font-weight: 900; font-size: 12px; cursor: pointer; }
.btn-nora-danger-sm { background: #fff; color: #EF4444; border: 1.5px solid #FEE2E2; padding: 12px 25px; border-radius: 50px; font-weight: 900; font-size: 12px; cursor: pointer; }

/* Custom Checks */
.nora-checkbox-label { display: flex; align-items: center; gap: 12px; cursor: pointer; }
.checkbox-custom { width: 22px; height: 22px; border: 3px solid var(--gold); border-radius: 6px; position: relative; }
.nora-checkbox-label input { display: none; }
.nora-checkbox-label input:checked + .checkbox-custom { background: var(--gold); }
.nora-checkbox-label input:checked + .checkbox-custom::after { content: '✓'; position: absolute; color: #fff; font-size: 14px; top: -1px; left: 3px; font-weight: 950; }

#toast { position: fixed; top: 25px; right: 25px; background: var(--navy); color: #fff; padding: 15px 35px; border-radius: 12px; font-weight: 950; box-shadow: 0 15px 40px rgba(0,0,0,0.2); border-left: 6px solid var(--gold); z-index: 11000; animation: slideIn 0.3s forwards; display: none; }
@keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
</style>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
let curId = null;

// Initialize Sortable for Drag and Drop
document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('leftSequence');
    if (el) {
        Sortable.create(el, {
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            onEnd: function() {
                // Potential visual update for tags if needed, 
                // but save happens on 'Simpan Urutan' click
            }
        });
    }
});

let confirmCallback = null;

function noraConfirm(text, callback, icon = '❓', title = 'Konfirmasi') {
    document.getElementById('ncText').innerText = text;
    document.getElementById('ncTitle').innerText = title;
    document.getElementById('ncIcon').innerText = icon;
    document.getElementById('confirmOverlay').style.display = 'flex';
    confirmCallback = callback;
    
    document.getElementById('btnConfirmOk').onclick = () => {
        closeConfirm(true);
    };
}

function closeConfirm(isOk) {
    document.getElementById('confirmOverlay').style.display = 'none';
    if (isOk && confirmCallback) confirmCallback();
    confirmCallback = null;
}

const apiExec = async (gt, fd) => {
    try {
        const url = `<?= APP_URL ?>/index.php?gate=${gt}`;
        const res = await fetch(url, { method: 'POST', body: fd });
        if (!res.ok) throw new Error(`${res.status}`);
        return await res.json();
    } catch (e) {
        alert('Gagal Terhubung: ' + e.message + '\nSilakan coba lagi (cek login Anda).');
        return null;
    }
};

function openStepModal(step, isPatent) {
    const f = document.getElementById('stepForm');
    f.reset();
    
    // Reset hidden role input
    const hiddenRole = document.getElementById('f_role_hidden');
    hiddenRole.value = '';
    hiddenRole.disabled = true;

    if (step) {
        curId = step.id;
        document.getElementById('modalTitle').textContent = 'Ubah Tahapan: ' + step.label;
        document.getElementById('f_id').value = step.id;
        document.getElementById('f_label').value = step.label;
        document.getElementById('f_sla').value = step.sla_days;
        document.getElementById('f_key').value = step.step_key;
        document.getElementById('f_key').readOnly = true;
        document.getElementById('f_cancel').checked = (parseInt(step.is_cancellable) === 1);
        
        if (isPatent) {
            // Patent: Use hidden input for role, disable radios
            hiddenRole.value = step.behavior_role;
            hiddenRole.disabled = false;
            document.querySelectorAll('input[name="behavior_role"]').forEach(r => { r.disabled = true; r.checked = false; });
        } else {
            // Operational: Use radios
            document.querySelectorAll('input[name="behavior_role"]').forEach(r => { 
                r.disabled = false;
                r.checked = (r.value == step.behavior_role); 
            });
        }
        
        document.getElementById('btnDelete').style.display = isPatent ? 'none' : 'block';
        document.getElementById('roleGroup').style.display = isPatent ? 'none' : 'block';
        document.getElementById('cancelArea').style.display = 'block';
        document.getElementById('idGroup').style.display = isPatent ? 'none' : 'block';
    } else {
        curId = null;
        document.getElementById('modalTitle').textContent = 'Tambah Tahapan Kerja Baru';
        document.getElementById('f_id').value = '';
        document.getElementById('f_key').readOnly = false;
        document.getElementById('btnDelete').style.display = 'none';
        document.getElementById('roleGroup').style.display = 'block';
        document.getElementById('cancelArea').style.display = 'block';
        document.getElementById('idGroup').style.display = 'block';
        
        // Default for new operational: Proses A
        document.querySelectorAll('input[name="behavior_role"]').forEach(r => r.disabled = false);
        document.getElementById('role1').checked = true;
    }
    document.getElementById('stepModal').style.display = 'flex';
}

function closeModal() { document.getElementById('stepModal').style.display = 'none'; }

function shiftItem(id, dir) {
    const items = Array.from(document.querySelectorAll('.op-item'));
    const idx = items.findIndex(i => i.getAttribute('data-id') == id);
    const item = items[idx];
    const container = item.parentElement;
    if (dir === 'up' && idx > 0) container.insertBefore(item, items[idx-1]);
    else if (dir === 'down' && idx < items.length - 1) container.insertBefore(items[idx+1], item);
}

async function saveOperationalSequence() {
    const list = Array.from(document.querySelectorAll('.op-item')).map(i => i.getAttribute('data-id'));
    if (list.length === 0) return;
    
    // Use URLSearchParams for more robust POST parsing in some PHP setups
    const params = new URLSearchParams();
    params.append('order', JSON.stringify(list));
    params.append('csrf_token', '<?= generateCSRFToken() ?>');
    
    // We update apiExec to handle params or use a direct fetch here
    try {
        const res = await fetch('<?= APP_URL ?>/index.php?gate=cms_reorder_workflow', {
            method: 'POST',
            body: params
        });
        const data = await res.json();
        
        if (data && data.success) { 
            showToast('Urutan Berhasil Diperbarui!'); 
            setTimeout(() => location.reload(), 1200); 
        } else {
            const errorMsg = data ? data.message : 'Kesalahan Server';
            alert('Gagal Menyimpan Urutan:\n- ' + errorMsg);
        }
    } catch (e) {
        alert('Gagal Terhubung: ' + e.message);
    }
}

document.getElementById('stepForm').onsubmit = async (e) => {
    e.preventDefault();
    const btn = document.getElementById('btnSubmit'); btn.disabled = true;
    const fd = new FormData(e.target);
    if (!document.getElementById('f_cancel').checked) fd.set('is_cancellable', '0');
    const data = await apiExec('cms_save_workflow', fd);
    if (data && data.success) { showToast('Setelan Alur Tersimpan.'); setTimeout(() => location.reload(), 1000); }
    else if(data) { alert(data.message); btn.disabled = false; }
};

async function deleteStep() {
    if (!curId) return;
    noraConfirm('Apakah Anda yakin ingin menghapus tahapan operasional ini? Data yang sudah terhubung mungkin akan terpengaruh.', async () => {
        const fd = new FormData();
        fd.append('id', curId);
        fd.append('csrf_token', '<?= generateCSRFToken() ?>');
        const data = await apiExec('cms_delete_workflow', fd);
        if (data && data.success) {
            showToast('Tahapan berhasil dihapus.');
            setTimeout(() => location.reload(), 1000);
        } else {
            alert('Gagal: ' + (data ? data.message : 'Error Unknown'));
        }
    }, '🗑️', 'Hapus Tahapan');
}

function showToast(m) { const t = document.getElementById('toast'); t.textContent = m; t.style.display = 'block'; setTimeout(() => t.style.display = 'none', 3000); }
window.onclick = e => { if (e.target.id === 'stepModal') closeModal(); };
</script>

<div id="toast">Notif</div>

<?php require VIEWS_PATH . '/templates/footer.php'; ?>