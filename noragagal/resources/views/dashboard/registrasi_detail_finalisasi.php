<?php
/**
 * Detail Registrasi View (For Finalisasi Page) - Command Center Luxe v5.3
 * EXACT MIRROR OF registrasi_detail.php
 */

use App\Domain\Entities\WorkflowStep;

// Fetch Templates (Finalisasi Logic)
$dbRaw = \App\Adapters\Database::select(
    "SELECT workflow_step_id, template_body FROM note_templates WHERE workflow_step_id IN (11, 14, 16)"
);
$realTpls = []; 
foreach($dbRaw as $r) {
    $realTpls[(int)$r['workflow_step_id']] = addslashes(trim($r['template_body']));
}
$tplReview    = !empty($realTpls[16]) ? $realTpls[16] : 'Registrasi telah diperiksa dan disetujui untuk diselesaikan.';
$tplPerbaikan = !empty($realTpls[11]) ? $realTpls[11] : 'Terdapat hal yang perlu ditinjau ulang.';
$tplDitutup   = !empty($realTpls[14]) ? $realTpls[14] : 'Perkara telah resmi dibatalkan dan ditutup.';

$currentUser = getCurrentUser();
$pageTitle = 'Detail Finalisasi - ' . ($registrasi['nomor_registrasi'] ?? '-');
$activePage = 'finalisasi';
$pageScript = 'registrasi-detail.js'; // Use main JS for compatibility

require VIEWS_PATH . '/templates/header.php';

// Define UI dynamic variables (Sync with registrasi_detail.php)
$role = (int)($registrasi['behavior_role'] ?? 0);
$statusStyle = \App\Domain\Entities\Registrasi::getStatusStyle($role);
$bg = $statusStyle['bg']; 
$color = $statusStyle['color']; 
$border_badge = $statusStyle['border'];

$targetDateRaw = $registrasi['target_completion_at'] ?? '';
$isOverdue = (!empty($targetDateRaw) && strtotime($targetDateRaw) > 0 && new DateTime() > new DateTime($targetDateRaw));

// Read-only for finalization view
$isReadOnlyStatus = true; 
?>

<script>
    window.APP_URL = '<?= APP_URL ?>';
    const REG_DATA = {
        id: <?= (int)$registrasi['id'] ?>,
        nama: <?= json_encode($registrasi['klien_nama']) ?>,
        nomor: <?= json_encode($registrasi['nomor_registrasi']) ?>,
        role: <?= $role ?>,
        hp: <?= json_encode($registrasi['klien_hp'] ?? '') ?>,
        sender: <?= json_encode($currentUser['username'] ?? 'Admin') ?>,
        kantor: <?= json_encode(APP_NAME) ?>,
        tanggal: <?= json_encode(date('l, d F Y')) ?>
    };
    const tplReview = <?= json_encode($tplReview) ?>;
    const tplPerbaikan = <?= json_encode($tplPerbaikan) ?>;
    const tplDitutup = <?= json_encode($tplDitutup) ?>;
</script>

<div class="registrasi-detail">
    <!-- Back Button -->
    <div style="margin-bottom: 5px;">
        <a href="<?= APP_URL ?>/index.php?gate=finalisasi" class="btn-back" style="
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--text-light);
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
        " onmouseover="this.style.background='var(--cream)';this.style.color='var(--primary)'" onmouseout="this.style.background='';this.style.color='var(--text-light)'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Kembali ke Daftar Finalisasi
        </a>
    </div>

    <!-- Premium Info Card (Mirror) -->
    <style>
        .nora-detail-card {
            background: var(--white);
            border-radius: 8px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.03);
            margin-bottom: 20px;
            border: 1px solid var(--border);
            overflow: hidden;
        }
        .nora-detail-header {
            background: #fdfcfb;
            padding: 12px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }
        .nora-detail-title { color: var(--primary); font-size: 16px; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 8px; }
        .nora-detail-body { padding: 16px 20px; }
        .nora-detail-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); column-gap: 20px; row-gap: 16px; }
        .nora-data-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; color: var(--text-muted); display: flex; align-items: center; gap: 4px; }
        .nora-data-value { font-size: 13px; font-weight: 600; color: var(--text); }
        .nora-data-box { background: #fffcf5; border: 1px solid #f2e9d8; border-radius: 8px; padding: 12px 16px; margin-top: 20px; border-left: 3px solid var(--gold); }
    </style>

    <div class="nora-detail-card">
        <div class="nora-detail-header">
            <h2 class="nora-detail-title">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color: var(--gold);">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <span style="font-weight: 800;"><?= htmlspecialchars($registrasi['nomor_registrasi'] ?? '-') ?></span>
            </h2>
            <span class="badge" style="background: <?= $bg ?>; color: <?= $color ?>; padding: 4px 12px; border-radius: 4px; font-size: 11px; font-weight: 700; border: 1px solid <?= $border_badge ?>;">
                <?= htmlspecialchars($registrasi['status_label'] ?? $registrasi['status']) ?>
            </span>
        </div>

        <div class="nora-detail-body">
            <div class="nora-detail-grid">
                <div class="nora-data-group">
                    <div class="nora-data-label">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        Nama Klien
                    </div>
                    <div class="nora-data-value" style="font-size: 14px; color: var(--primary); font-weight: 800;"><?= htmlspecialchars($registrasi['klien_nama']) ?></div>
                </div>
                <div class="nora-data-group">
                    <div class="nora-data-label">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                        Kontak
                    </div>
                    <div class="nora-data-value"><?= htmlspecialchars($registrasi['klien_hp']) ?></div>
                </div>
                <div class="nora-data-group">
                    <div class="nora-data-label">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                        Layanan
                    </div>
                    <div class="nora-data-value"><?= htmlspecialchars($registrasi['nama_layanan']) ?></div>
                </div>
                <div class="nora-data-group">
                    <div class="nora-data-label">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        Dibuat
                    </div>
                    <div class="nora-data-value"><?= date('d F Y', strtotime($registrasi['created_at'])) ?></div>
                </div>
            </div>
            <div class="nora-data-box">
                <div class="nora-data-label">Keterangan</div>
                <p><?= !empty($registrasi['keterangan']) ? nl2br(\App\Core\View::e($registrasi['keterangan'])) : 'Tidak ada keterangan.' ?></p>
            </div>
        </div>
    </div>

    <!-- History Summary Component (Now Before Action Hub) -->
    <?php require VIEWS_PATH . '/dashboard/parts/ringkasan_pelaksanaan.php'; ?>

    <!-- Finalisasi Hub (Integrated Layout Sync - Now After Ringkasan) -->
    <?php if (in_array($role, [7, 8])): ?>
    <div id="finalCard" class="nora-detail-card" style="border: 2px solid var(--primary); margin-top: 5px;">
        <div class="nora-detail-header" style="background: var(--cream);">
            <h3 id="hubTitle" style="margin: 0; font-size: 14px; font-weight: 800; color: var(--primary);">⚖️ COMMAND CENTER FINALISASI</h3>
        </div>
        <div class="nora-detail-body">
            <div style="display: flex; gap: 10px; margin-bottom: 20px;">
                <button type="button" id="tab-tutup" onclick="switchMode('tutup')" style="flex: 1; padding: 12px; border-radius: 8px; border: 2px solid var(--primary); background: #fff; color: var(--primary); font-size: 11px; font-weight: 800; cursor: pointer;">
                    📁 <?= $role === 8 ? 'SELESAIKAN BERKAS' : 'TUTUP ARSIP BATAL' ?>
                </button>
                <button type="button" id="tab-review" onclick="switchMode('review')" style="flex: 1; padding: 12px; border-radius: 8px; border: 2px solid transparent; background: #f8f9fa; color: #888; font-size: 11px; font-weight: 800; cursor: pointer;">
                    🔄 <?= $role === 8 ? 'KEMBALIKAN PERBAIKAN' : 'BUKA KEMBALI KASUS' ?>
                </button>
            </div>

            <div style="margin-bottom: 15px;">
                <label for="hubNotes" style="display: block; font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Catatan Aktivitas</label>
                <textarea id="hubNotes" rows="3" style="width: 100%; border: 1px solid var(--border); border-radius: 8px; padding: 12px; font-size: 13px; border-left: 4px solid var(--primary); outline: none;"></textarea>
            </div>

            <button id="finalSubmitBtn" type="button" onclick="handleFinalSubmit()" style="width: 100%; padding: 14px; background: var(--primary); color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px;">
                <span id="btnIcon">📁</span> <span id="btnText">Simpan Penyelesaian</span>
            </button>
        </div>
    </div>
    <?php else: ?>
        <!-- Terminal State Panel (Sync with main) -->
        <div style="background: linear-gradient(135deg, var(--primary), #2D5A6B); color: white; border-radius: 8px; padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; margin-top: 5px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span style="font-size: 14px; font-weight: 800;">BERKAS TELAH DIFINALISASI</span>
            </div>
            <div style="background: rgba(255,255,255,0.2); padding: 5px 12px; border-radius: 4px; font-size: 10px; font-weight: 800; border: 1px solid rgba(255,255,255,0.3); text-transform: uppercase;"><?= htmlspecialchars($registrasi['status_label']) ?></div>
        </div>
    <?php endif; ?>

    <!-- Riwayat Perubahan (EXACT MIRROR OF registrasi_detail.php - NOW AT BOTTOM) -->
    <div class="nora-detail-card" style="margin-top: 20px;">
        <div class="nora-detail-body">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 12px;">
                <h3 style="margin: 0; color: var(--primary); font-size: 16px;">Riwayat Perubahan</h3>
                <a href="<?= APP_URL ?>/index.php?gate=registrasi_history&id=<?= $registrasi['id'] ?>" class="btn-sm" style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: var(--primary); color: var(--gold); border-radius: 6px; font-size: 11px; font-weight: 600; text-decoration: none;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    Lihat Semua Riwayat
                </a>
            </div>
            <div style="overflow-x: auto; border: 1px solid var(--border); border-radius: 8px;">
                <table class="data-table nora-history-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: var(--cream);">
                            <th style="padding: 12px 16px; font-weight: 700; color: var(--text-muted); border-bottom: 1px solid var(--border); text-align: left;">Waktu</th>
                            <th style="padding: 12px 16px; font-weight: 700; color: var(--text-muted); border-bottom: 1px solid var(--border); text-align: left;">Admin</th>
                            <th style="padding: 12px 16px; font-weight: 700; color: var(--text-muted); border-bottom: 1px solid var(--border); text-align: left;">Flag</th>
                            <th style="padding: 12px 16px; font-weight: 700; color: var(--text-muted); border-bottom: 1px solid var(--border); text-align: left;">Perubahan Status</th>
                            <th style="padding: 12px 16px; font-weight: 700; color: var(--text-muted); border-bottom: 1px solid var(--border); text-align: left;">Catatan / Info</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $recentHistory = array_slice($history, 0, 7);
                        if (empty($recentHistory)): ?>
                        <tr>
                            <td colspan="5" style="padding: 20px; text-align: center; color: var(--text-muted); font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">
                                <div style="display: flex; align-items: center; justify-content: center; gap: 8px; opacity: 0.6;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                    Belum ada riwayat perubahan
                                </div>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($recentHistory as $h): ?>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <td style="padding: 12px 16px; color: var(--text); font-size: 12px; white-space: nowrap;"><?= date('d M Y H:i', strtotime($h['created_at'])) ?></td>
                            <td style="padding: 12px 16px; color: var(--text); font-size: 12px; white-space: nowrap;"><?= htmlspecialchars($h['user_name'] ?? 'System') ?></td>
                            <td style="padding: 12px 16px; font-size: 12px; white-space: nowrap;">
                                <?php if ($h['flag_kendala_active']): ?>
                                    <span style="color: #ffc107; font-weight: 600;">🚩 ON</span>
                                <?php else: ?>
                                    <span style="color: var(--text-muted);">-</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 12px 16px; font-size: 12px; color: var(--text-light); white-space: nowrap;">
                                <?php
                                $oldLabel = $h['status_old_label'] ?? '';
                                $newLabel = $h['status_new_label'] ?? '';
                                if ($oldLabel && $oldLabel !== $newLabel) {
                                    echo "<span style='color: var(--text-muted); text-decoration: line-through; font-size: 10px;'>" . htmlspecialchars($oldLabel) . "</span><br>";
                                    echo "<span style='color: var(--primary); font-weight: 600;'>" . htmlspecialchars($newLabel) . "</span>";
                                } else {
                                    echo "<span style='color: var(--text); font-weight: 600;'>" . htmlspecialchars($newLabel) . "</span>";
                                }
                                ?>
                            </td>
                            <td style="padding: 12px 16px; font-size: 12px; color: var(--text); line-height: 1.5;">
                                <?php
                                $catatan = $h['catatan'] ?? '';
                                $cleanCatatan = strip_tags($catatan);
                                echo $cleanCatatan ? nl2br(htmlspecialchars($cleanCatatan)) : '<span style="color: var(--text-muted); font-style: italic;">-</span>';
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="actionMessage" style="position: fixed; bottom: 20px; right: 20px; padding: 12px 20px; border-radius: 8px; font-weight: 800; z-index: 9999; display: none; box-shadow: 0 4px 20px rgba(0,0,0,0.15); font-size: 13px;"></div>

<script>
let currentMode = 'tutup';
function switchMode(m) {
    currentMode = m;
    const tTutup = document.getElementById('tab-tutup');
    const tReview = document.getElementById('tab-review');
    const btn = document.getElementById('finalSubmitBtn');
    const notes = document.getElementById('hubNotes');
    
    if (m === 'tutup') {
        tTutup.style.borderColor = 'var(--primary)'; tTutup.style.background = '#fff'; tTutup.style.color = 'var(--primary)';
        tReview.style.borderColor = 'transparent'; tReview.style.background = '#f8f9fa'; tReview.style.color = '#888';
        btn.style.background = 'var(--primary)'; btn.innerHTML = '<span>📁</span> ' + (REG_DATA.role === 8 ? 'Selesaikan Berkas' : 'Tutup Arsip Batal');
        notes.value = (REG_DATA.role === 8 ? tplReview : tplDitutup);
    } else {
        tReview.style.borderColor = 'var(--gold)'; tReview.style.background = '#fff'; tReview.style.color = 'var(--gold)';
        tTutup.style.borderColor = 'transparent'; tTutup.style.background = '#f8f9fa'; tTutup.style.color = '#888';
        btn.style.background = 'var(--gold)'; btn.innerHTML = '<span>🔄</span> ' + (REG_DATA.role === 8 ? 'Kembalikan Perbaikan' : 'Buka Kembali Kasus');
        notes.value = tplPerbaikan;
    }
}

function showToast(msg, isSuccess = true) {
    const t = document.getElementById('actionMessage');
    t.innerText = msg; t.style.background = isSuccess ? '#e8f5e9' : '#ffebee'; t.style.color = isSuccess ? '#2e7d32' : '#c62828';
    t.style.display = 'block'; setTimeout(() => { t.style.display = 'none'; }, 2000);
}

function handleFinalSubmit() {
    const btn = document.getElementById('finalSubmitBtn');
    const notes = document.getElementById('hubNotes').value;
    btn.disabled = true; btn.innerText = '⏳ Memproses...';

    const gate = (currentMode === 'tutup') ? 'finalize_case' : 'reopen_case';
    const fd = new FormData();
    fd.append('registrasi_id', REG_DATA.id); fd.append('notes', notes); 
    fd.append('csrf_token', '<?= $_SESSION['_csrf_token'] ?? '' ?>');

    if (gate === 'reopen_case') fd.append('target_status', 'back_to_process');

    fetch('<?= APP_URL ?>/index.php?gate=' + gate, { method: 'POST', body: fd })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast("✓ Berhasil diperbarui");
            setTimeout(() => { window.location.href = '<?= APP_URL ?>/index.php?gate=finalisasi'; }, 800);
        } else {
            showToast("✕ Gagal: " + data.message, false);
            btn.disabled = false; btn.innerText = 'Simpan';
        }
    });
}
document.addEventListener('DOMContentLoaded', () => { if(document.getElementById('tab-tutup')) switchMode('tutup'); });
</script>

<?php require VIEWS_PATH . '/templates/footer.php'; ?>
