<?php
/**
 * Registrasi Detail View - WITH WHATSAPP POPUP
 */

use App\Domain\Entities\WorkflowStep;

$currentUser = getCurrentUser();
$pageTitle = 'Detail Registrasi - ' . ($registrasi['nomor_registrasi'] ?? '-');
$activePage = 'registrasi';
$pageScript = 'registrasi-detail.js?v=' . time();

require VIEWS_PATH . '/templates/header.php';

// Define UI dynamic variables early to fix undefined warnings
$role = (int)($registrasi['behavior_role'] ?? 0);
$bg = '#e3f2fd'; $color = '#1976d2'; $border_badge = '#90caf9';
if ($role === 2) { $bg = '#fff3e0'; $color = '#f57c00'; $border_badge = '#ffcc80'; } // Perbaikan
if ($role === 3) { $bg = '#e8f5e9'; $color = '#2e7d32'; $border_badge = '#a5d6a7'; } // Selesai
if ($role === 5) { $bg = '#ffebee'; $color = '#c62828'; $border_badge = '#ef9a9a'; } // Batal

$targetDateRaw = $registrasi['target_completion_at'] ?? '';
$isOverdue = (!empty($targetDateRaw) && strtotime($targetDateRaw) > 0 && new DateTime() > new DateTime($targetDateRaw));

// Elite Fix v4.37: Only read-only if BEHAVIOR role is 4 (Success) or above (Standard 0-7 Schema)
$currentBehaviorRole = (int)($registrasi['behavior_role'] ?? 0);
$isReadOnlyStatus = ($currentBehaviorRole >= 4);

$showTerminalInfo = in_array($currentBehaviorRole, [4, 5, 6, 7], true);
$showPenyelesaian = in_array($currentBehaviorRole, [4, 7], true);
$showPenyerahan = ($currentBehaviorRole === 5);
$showPenutupan = ($currentBehaviorRole === 6);

// Elite Update: Removed JSON inline templating. 
// Transitioned to AJAX fetch just like Create page for 100% Sync.
?>
<?php
    // Michelin Force Populator v5.33: SMART DB Fetch by Behavior 5 ID
    // 1. Find the step ID where behavior is 5
    $stepData = \App\Adapters\Database::selectOne("SELECT id FROM workflow_steps WHERE behavior_role = 5 LIMIT 1");
    $targetStepId = (int)($stepData['id'] ?? 0);
    
    // 2. Fetch the corresponding template
    $handoverTemplateBody = 'Berkas telah diserahkan kepada [penerima].';
    if ($targetStepId > 0) {
        $tplData = \App\Adapters\Database::selectOne("SELECT template_body FROM note_templates WHERE workflow_step_id = ? LIMIT 1", [$targetStepId]);
        if ($tplData) $handoverTemplateBody = $tplData['template_body'];
    }
?>
<script>
    // JS Global Registry for Auto-Fill
    window.DISERAHKAN_TPL_CMS = <?= json_encode($handoverTemplateBody) ?>;
    const REG_DATA = {
        id: <?= (int)$registrasi['id'] ?>,
        nama: <?= json_encode($registrasi['klien_nama']) ?>,
        nomor: <?= json_encode($registrasi['nomor_registrasi']) ?>,
        hp: <?= json_encode($registrasi['klien_hp']) ?>,
        sender: <?= json_encode($currentUser['username'] ?? 'Admin') ?>,
        kantor: <?= json_encode(APP_NAME) ?>,
        tanggal: <?= json_encode(date('l, d F Y')) ?>
    };
</script>

<div class="registrasi-detail">
    <!-- Back Button -->
    <div style="margin-bottom: 5px;">
        <a href="<?= APP_URL ?>/index.php?gate=registrasi" class="btn-back" style="
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
            Kembali ke Daftar Registrasi
        </a>
    </div>

    <!-- Premium Registration Info Component -->
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
        .nora-detail-title {
            color: var(--primary);
            font-size: 16px;
            font-weight: 800;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .nora-detail-body {
            padding: 16px 20px;
        }
        .nora-detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            column-gap: 20px;
            row-gap: 16px;
        }
        .nora-data-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .nora-data-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .nora-data-value {
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            word-break: break-word;
        }
        .nora-data-box {
            background: #fffcf5;
            border: 1px solid #f2e9d8;
            border-radius: 8px;
            padding: 12px 16px;
            margin-top: 20px;
            border-left: 3px solid var(--gold);
        }
        .nora-data-box p {
            margin: 0;
            color: var(--text);
            font-size: 12px;
            line-height: 1.5;
            font-weight: 500;
        }
        .btn-nora-edit {
            background: var(--gold);
            color: var(--primary);
            border: none;
            padding: 8px 18px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 10px rgba(197,163,101,0.25);
            transition: all 0.2s ease;
        }
        .btn-nora-edit:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(197,163,101,0.35);
        }
    </style>

    <div class="nora-detail-card">
        <!-- Card Header -->
        <div class="nora-detail-header">
            <h2 class="nora-detail-title">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color: var(--gold);">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <?= htmlspecialchars($registrasi['nomor_registrasi'] ?? '-') ?>
            </h2>
            
            <div style="display: flex; align-items: center; gap: 10px;">
                <span class="badge" style="background: <?= $bg ?>; color: <?= $color ?>; padding: 4px 12px; border-radius: 4px; font-size: 11px; font-weight: 700; border: 1px solid <?= $border_badge ?>;">
                    <?= htmlspecialchars($registrasi['status_label'] ?? $registrasi['status']) ?>
                </span>
                
                <button type="button" onclick="openEditModal()" class="btn-nora-edit" style="padding: 6px 14px; font-size: 11px;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                    Ubah Data
                </button>
            </div>
        </div>

        <!-- Card Grid Body -->
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
                        Nomor Kontak
                    </div>
                    <div class="nora-data-value"><?= htmlspecialchars($registrasi['klien_hp']) ?></div>
                </div>

                <div class="nora-data-group">
                    <div class="nora-data-label">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        Jenis Layanan
                    </div>
                    <div class="nora-data-value"><?= htmlspecialchars($registrasi['nama_layanan']) ?></div>
                </div>

                <div class="nora-data-group">
                    <div class="nora-data-label">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        Tanggal Dibuat
                    </div>
                    <div class="nora-data-value"><?= date('d F Y', strtotime($registrasi['created_at'])) ?></div>
                </div>

                <?php if ($showPenyelesaian) : ?>
                <div class="nora-data-group">
                    <div class="nora-data-label">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        📊 Tgl Penyelesaian
                    </div>
                    <div class="nora-data-value" style="color: #2e7d32; font-weight: 800;">
                        <?= !empty($registrasi['selesai_batal_at']) ? date('d F Y H:i', strtotime($registrasi['selesai_batal_at'])) : '-' ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($showPenyerahan) : ?>
                <div class="nora-data-group">
                    <div class="nora-data-label">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg>
                        📦 Tgl Penyerahan
                    </div>
                    <div class="nora-data-value" style="color: #1976d2; font-weight: 800;">
                        <?= !empty($registrasi['diserahkan_at']) ? date('d F Y H:i', strtotime($registrasi['diserahkan_at'])) : '-' ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($showPenutupan) : ?>
                <div class="nora-data-group">
                    <div class="nora-data-label">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        🗄️ Arsip Ditutup
                    </div>
                    <div class="nora-data-value" style="color: #455a64; font-weight: 800;">
                        <?= !empty($registrasi['ditutup_at']) ? date('d F Y H:i', strtotime($registrasi['ditutup_at'])) : '-' ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="nora-data-group">
                    <div class="nora-data-label">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2L2 7l10 5 10-5-10-5z"></path><path d="M2 17l10 5 10-5"></path><path d="M2 12l10 5 10-5"></path></svg>
                        Flag Kendala
                    </div>
                    <div class="nora-data-value"><?php 
                        if ($hasActiveKendala) {
                            echo '<span style="color: #f57c00; font-weight: 700;">🚩 Kendala Aktif</span>';
                        } else {
                            echo '<span style="color: #2e7d32; font-weight: 700;">✅ Hijau (Lancar)</span>';
                        } ?></div>
                </div>

                <div class="nora-data-group">
                    <div class="nora-data-label">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        Target Estimasi
                    </div>
                    <div class="nora-data-value" style="color: <?= $isOverdue ? '#c62828' : 'var(--primary)' ?>;">
                        <?php 
                            $tgt = $registrasi['target_completion_at'] ?? '';
                            if (!empty($tgt) && strtotime($tgt) > 0) {
                                $d1 = date('d M Y', strtotime($tgt));
                                $d2 = date('d M Y', strtotime($tgt . ' + 5 days'));
                                echo "{$d1} - {$d2}";
                                if ($isOverdue) echo ' <span style="font-size: 9px; text-transform: uppercase; padding: 2px 4px; background: #ffebee; color: #c62828; border-radius: 4px; margin-left: 4px; border: 1px solid #ef9a9a;">Terlambat</span>';
                            } else {
                                echo '<span class="text-muted">Belum Ada</span>';
                            }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Descriptive Box -->
            <div class="nora-data-box">
                <div class="nora-data-label" style="margin-bottom: 4px;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                    Keterangan Perkara
                </div>
                <p>
                    <?= !empty($registrasi['keterangan']) ? nl2br(\App\Core\View::e($registrasi['keterangan'])) : '<span style="color: var(--text-muted); font-style: italic;">Tidak ada keterangan opsional yang dilampirkan untuk perkara ini.</span>' ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Update/Status Card -->
    <div class="detail-card" style="background: var(--white); border-radius: 12px; padding: 24px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 24px;">
        <?php if (!$isReadOnlyStatus): ?>
            <h3 style="margin: 0 0 20px 0; color: var(--primary); font-size: 18px;">Update Status</h3>
            <form id="updateStatusForm" class="action-form">
                <input type="hidden" name="registrasi_id" value="<?= $registrasi['id'] ?>">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text); font-size: 14px;">Status Saat Ini</label>
                    <div style="padding: 10px 14px; background: var(--cream); border-radius: 6px; font-weight: 600; color: var(--primary); font-size: 14px;">
                        <?= htmlspecialchars($registrasi['status_label'] ?? $registrasi['status']) ?>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text); font-size: 14px;">Status Baru</label>
                    <select id="status" name="status" onchange="autoFillCatatan()" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 6px; font-size: 14px; font-family: inherit; background: var(--white); cursor: pointer;">
                        <option value="">-- Pilih Status Berikutnya --</option>
                        <?php
                        $currentOrder = (int)($registrasi['workflow_order'] ?? 0);
                        $currentBehavior = (int)($registrasi['behavior_role'] ?? 0);
                        $isRepairMode = ($currentBehavior === 3);
                        $canCancel = (new \App\Domain\Entities\Registrasi())->canBeCancelled((int)$registrasi['id']);
                        
                        foreach ($availableSteps as $step) {
                            if ($step['step_key'] === $registrasi['status']) continue;
                            
                            $s_behavior = (int)$step['behavior_role'];
                            
                            // RULE: Jika sedang di Perbaikan (behavior 3), boleh kembali ke tahap sebelumnya,
                            // termasuk status normal earlier (behavior 0/1/2), tetapi tidak boleh ke finalisasi 5 atau ditutup 6.
                            $isForward = ($step['sort_order'] > $currentOrder);
                            $isRepair = ($s_behavior === 3);
                            $isBackwardFromRepair = $isRepairMode && $step['sort_order'] < $currentOrder;
                            $isBatal = ($s_behavior === 7);
                            
                            if ($isBatal && !$canCancel) continue;
                            if (in_array($s_behavior, [5, 6], true)) continue;
                            
                            $showInDropdown = in_array($s_behavior, [0, 1, 2, 3, 4, 7], true);
                            
                            if ($showInDropdown && ($isForward || $isRepair || $isBackwardFromRepair)) {
                                echo "<option value='{$step['step_key']}'>" . htmlspecialchars($step['label']) . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text); font-size: 14px;">Catatan Update Status</label>
                    <textarea id="catatan" name="catatan" rows="3" placeholder="Pilih status untuk auto-fill catatan..." style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 6px; font-size: 14px; font-family: inherit; resize: vertical;"><?= htmlspecialchars($registrasi['catatan_internal'] ?? '') ?></textarea>
                </div>

                <!-- Flag Kendala (v4.67 - Clean Standard) -->
                <?php if (!$isReadOnlyStatus): ?>
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="
                        display: flex;
                        align-items: center;
                        gap: 12px;
                        padding: 16px;
                        background: #fff8e1;
                        border: 1px solid #ffe082;
                        border-radius: 8px;
                        cursor: pointer;
                    ">
                        <input type="checkbox" name="flag_kendala" id="flagKendala" value="1" 
                            <?= $hasActiveKendala ? 'checked' : '' ?>
                            style="width: 20px; height: 20px; cursor: pointer; accent-color: #ff1100;">
                        <div>
                            <span style="font-weight: 600; color: var(--text); font-size: 14px;">
                                <?= $hasActiveKendala ? '✓ Kendala Aktif' : 'Tandai ada kendala' ?>
                            </span>
                            <p style="margin: 4px 0 0 0; font-size: 12px; color: var(--text-muted);">
                                <?= $hasActiveKendala ? 'Hapus centang untuk mematikan kendala' : 'Centang untuk menandai registrasi ini sedang terkendala' ?>
                            </p>
                        </div>
                    </label>
                </div>
                <?php endif; ?>
                
                <div style="display: flex; gap: 12px; margin-top: 20px; flex-wrap: wrap;">
                    <button type="button" onclick="window.location.href='<?= APP_URL ?>/index.php?gate=registrasi'" class="btn-secondary" style="background: var(--cream); color: var(--text); padding: 10px 20px; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer;">Batal</button>
                    <button type="submit" class="btn-primary" style="background: var(--primary); color: var(--gold); padding: 10px 20px; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer;">Simpan Status</button>
                </div>
            </form>
        <?php else: ?>
            <!-- TERMINAL STATUS (MICHELIN ZERO MARGIN v5.51) -->
            <?php 
            if ($isTerminal): 
                $currentRole = (int)($registrasi['behavior_role'] ?? 0);
            ?>
            <div style="background: linear-gradient(135deg, #2e7d32, #1b5e20); color: white; border-radius: 12px; padding: 14px 20px; display: flex; align-items: center; justify-content: space-between; margin: 0; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    <span style="font-size: 14px; font-weight: 800;"><?= ($currentRole === 5) ? 'Berkas Telah Diserahkan' : 'Registrasi Telah Selesai' ?></span>
                </div>
                <div style="background: rgba(255,255,255,0.2); padding: 5px 12px; border-radius: 6px; font-size: 11px; font-weight: 800; border: 1px solid rgba(255,255,255,0.2); white-space: nowrap;">
                    Menunggu Berkas Ditutup
                </div>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Hidden success/error message -->
    <div id="actionMessage" class="form-message" style="position: fixed; bottom: 20px; right: 20px; padding: 12px 20px; border-radius: 8px; font-weight: 600; z-index: 9999; display: none; box-shadow: 0 4px 20px rgba(0,0,0,0.15);"></div>

    <!-- DELIVERY SECTION -->
    <?php 
    $rawRole = (int)($registrasi['behavior_role'] ?? 0);
    if ($rawRole === 4): 
    ?>
    <div id="handover_card_compact" style="background: #fffdf5; border: 1px solid #ffcc80; border-radius: 12px; padding: 15px; margin: 0 0 15px 0; box-shadow: 0 4px 15px rgba(255,152,0,0.05);">
        <h3 style="margin: 0 0 12px 0; color: #E65100; font-size: 14px; font-weight: 800; display: flex; align-items: center; gap: 8px;">📦 Penyerahan Berkas</h3>
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 12px; margin-bottom: 12px;">
            <input type="text" id="penerima_name" oninput="updatePenerimaNote(this.value)" placeholder="Nama Penerima..." style="width:100%; padding:10px; border:1px solid #f2e9d8; border-radius:8px; font-size:12px;">
            <textarea id="handover_note" rows="1" style="width:100%; padding:10px; border:1px solid #f2e9d8; border-radius:8px; font-size:12px; resize:none;"><?= htmlspecialchars($finalNote) ?></textarea>
        </div>
        <button type="button" onclick="serahkanRegistrasi()" style="width: 100%; padding: 12px; background: #FF9800; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 800; cursor: pointer; box-shadow: 0 4px 10px rgba(255,152,0,0.2);">✓ Konfirmasi & Serahkan Berkas</button>
    </div>
    <?php endif; ?>

    <!-- WHATSAPP BOX (ZERO MARGIN v5.51) -->
    <div class="detail-card" style="background: #f0f7f4; border: 1px solid #c8e6c9; border-radius: 12px; padding: 15px; margin: 0;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px;">
            <div style="background: #25d366; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="white"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.72.94 3.659 1.437 5.63 1.438h.004c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
            </div>
            <div>
                <h3 style="margin: 0; color: #1b5e20; font-size: 14px; font-weight: 800;">Kirim Notifikasi (WhatsApp)</h3>
                <p style="margin: 2px 0 0 0; color: #2e7d32; font-size: 11px; font-weight: 600; opacity: 0.8;">Kirim pemberitahuan status terbaru ke klien.</p>
            </div>
        </div>
        <button type="button" onclick="sendWhatsApp()" style="width: 100%; padding: 12px; background: #25d366; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; box-shadow: 0 4px 12px rgba(37,211,102,0.2);">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.025 3.141l-.66 2.41 2.464-.647c.834.456 1.834.823 2.939.823 3.181 0 5.767-2.586 5.768-5.766 0-3.18-2.587-5.766-5.768-5.766zM15.42 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347"/></svg>
            Kirim via WhatsApp Sekarang
        </button>
    </div>

    <!-- Audit Log / History -->
    <style>
        .nora-history-table th, .nora-history-table td {
            padding: 12px 14px;
            font-size: 13px;
        }
        .nora-history-table th {
            text-transform: uppercase;
            font-size: 10px;
            white-space: nowrap;
        }
        .col-nobreak {
            white-space: nowrap;
        }
        .col-notes {
            min-width: 250px;
        }
    </style>
    <div class="detail-card" style="
        background: var(--white);
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        margin-top: 20px;
    ">
        <div style="
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            flex-wrap: wrap;
            gap: 12px;
        ">
            <h3 style="margin: 0; color: var(--primary); font-size: 16px;">Riwayat Perubahan</h3>
            <a href="<?= APP_URL ?>/index.php?gate=registrasi_history&id=<?= $registrasi['id'] ?>" class="btn-sm" style="
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 6px 12px;
                background: var(--primary);
                color: var(--gold);
                border-radius: 6px;
                font-size: 11px;
                font-weight: 600;
                text-decoration: none;
            ">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
                Lihat Semua Riwayat
            </a>
        </div>
        <div style="overflow-x: auto; border: 1px solid var(--border); border-radius: 8px;">
            <table class="data-table nora-history-table" style="
                width: 100%;
                border-collapse: collapse;
            ">
                <thead>
                    <tr style="background: var(--cream);">
                        <th style="font-weight: 700; color: var(--text-muted); border-bottom: 1px solid var(--border); text-align: left;">Waktu</th>
                        <th style="font-weight: 700; color: var(--text-muted); border-bottom: 1px solid var(--border); text-align: left;">Admin</th>
                        <th style="font-weight: 700; color: var(--text-muted); border-bottom: 1px solid var(--border); text-align: left;">Flag</th>
                        <th style="font-weight: 700; color: var(--text-muted); border-bottom: 1px solid var(--border); text-align: left;">Perubahan Status</th>
                        <th class="col-notes" style="font-weight: 700; color: var(--text-muted); border-bottom: 1px solid var(--border); text-align: left;">Catatan / Info</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Show only last 7 entries
                    $recentHistory = array_slice($history, 0, 7);
                    
                    if (empty($recentHistory)): 
                    ?>
                    <tr>
                        <td colspan="5" style="padding: 30px; text-align: center; color: var(--text-muted); font-size: 13px;">Belum ada riwayat perubahan</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($recentHistory as $h): ?>
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td class="col-nobreak" style="color: var(--text); font-size: 12px;"><?= date('d M Y H:i', strtotime($h['created_at'])) ?></td>
                        <td class="col-nobreak" style="color: var(--text); font-size: 12px;"><?= htmlspecialchars($h['user_name'] ?? 'System') ?></td>
                        <td class="col-nobreak" style="font-size: 12px;">
                            <?php if ($h['flag_kendala_active']): ?>
                                <span style="color: #ffc107; font-weight: 600;">🚩 ON</span>
                                <?php if (!empty($h['flag_kendala_tahap'])): ?>
                                <br><small style="color: var(--text-muted);"><?= htmlspecialchars($h['flag_kendala_tahap']) ?></small>
                                <?php endif; ?>
                            <?php else: ?>
                                <span style="color: var(--text-muted);">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="col-nobreak" style="font-size: 12px; color: var(--text-light);">
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
                        <td style="font-size: 12px; color: var(--text); line-height: 1.5;">
                            <?php
                            $catatan = $h['catatan'] ?? '';
                            // Elite Fix v5.55: strip_tags to prevent JS leaks/HTML breakage from history data
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

<!-- Hidden success/error message -->
<div id="actionMessage" class="form-message" style="
    position: fixed;
    bottom: 20px;
    right: 20px;
    padding: 16px 24px;
    border-radius: 8px;
    font-weight: 600;
    z-index: 9999;
    display: none;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
"></div>

<!-- Edit Modal -->
<div id="editModal" style="
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 9998;
    align-items: center;
    justify-content: center;
">
    <div style="
        background: var(--white);
        border-radius: 12px;
        padding: 32px;
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
    ">
        <div style="
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border);
        ">
            <h3 style="margin: 0; color: var(--primary); font-size: 18px;">✏️ Edit Data Klien</h3>
            <button type="button" onclick="closeEditModal()" style="
                background: none;
                border: none;
                font-size: 24px;
                cursor: pointer;
                color: var(--text-muted);
            ">&times;</button>
        </div>

        <form id="editKlienForm">
            <input type="hidden" name="registrasi_id" value="<?= $registrasi['id'] ?>">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

            <div style="margin-bottom: 20px;">
                <label style="
                    display: block;
                    font-weight: 600;
                    margin-bottom: 8px;
                    color: var(--text);
                    font-size: 14px;
                ">Nama Klien</label>
                <input type="text" id="edit_nama" name="nama" value="<?= htmlspecialchars($registrasi['klien_nama']) ?>" required style="
                    width: 100%;
                    padding: 12px 16px;
                    border: 1px solid var(--border);
                    border-radius: 8px;
                    font-size: 14px;
                    font-family: inherit;
                ">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="
                    display: block;
                    font-weight: 600;
                    margin-bottom: 8px;
                    color: var(--text);
                    font-size: 14px;
                ">Nomor HP</label>
                <input type="text" id="edit_hp" name="hp" value="<?= htmlspecialchars($registrasi['klien_hp']) ?>" required placeholder="08xxxxxxxxxx" style="
                    width: 100%;
                    padding: 12px 16px;
                    border: 1px solid var(--border);
                    border-radius: 8px;
                    font-size: 14px;
                    font-family: inherit;
                ">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="
                    display: block;
                    font-weight: 600;
                    margin-bottom: 8px;
                    color: var(--text);
                    font-size: 14px;
                ">Nomor Registrasi</label>
                <input type="text" id="edit_nomor_registrasi" name="nomor_registrasi" value="<?= htmlspecialchars($registrasi['nomor_registrasi']) ?>" required style="
                    width: 100%;
                    padding: 12px 16px;
                    border: 1px solid var(--border);
                    border-radius: 8px;
                    font-size: 14px;
                    font-family: inherit;
                    background: #fdfaf5;
                ">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="
                    display: block;
                    font-weight: 600;
                    margin-bottom: 8px;
                    color: var(--text);
                    font-size: 14px;
                ">Target Selesai (Global SLA)</label>
                <input type="date" id="edit_target_date" name="target_date" value="<?= !empty($registrasi['target_completion_at']) ? date('Y-m-d', strtotime($registrasi['target_completion_at'])) : '' ?>" required style="
                    width: 100%;
                    padding: 12px 16px;
                    border: 1px solid var(--border);
                    border-radius: 8px;
                    font-size: 14px;
                    font-family: inherit;
                ">
                <small style="color: var(--text-muted); font-size: 11px;">Default: 2 bulan dari pendaftaran.</small>
            </div>

            <div style="margin-bottom: 24px;">
                <label style="
                    display: block;
                    font-weight: 600;
                    margin-bottom: 8px;
                    color: var(--text);
                    font-size: 14px;
                ">Keterangan / Tentang Apa</label>
                <textarea id="edit_keterangan" name="keterangan" rows="3" placeholder="Contoh: Balik Nama Tanah Waris..." style="
                    width: 100%;
                    padding: 12px 16px;
                    border: 1px solid var(--border);
                    border-radius: 8px;
                    font-size: 14px;
                    font-family: inherit;
                    resize: vertical;
                "><?= htmlspecialchars($registrasi['keterangan'] ?? '') ?></textarea>
            </div>

            <div style="
                display: flex;
                gap: 12px;
                justify-content: flex-end;
            ">
                <button type="button" onclick="closeEditModal()" style="
                    background: var(--cream);
                    color: var(--text);
                    padding: 12px 24px;
                    border: none;
                    border-radius: 8px;
                    font-size: 14px;
                    font-weight: 600;
                    cursor: pointer;
                ">Batal</button>
                <button type="submit" style="
                    background: var(--primary);
                    color: var(--gold);
                    padding: 12px 24px;
                    border: none;
                    border-radius: 8px;
                    font-size: 14px;
                    font-weight: 600;
                    cursor: pointer;
                ">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Batal Confirmation Modal -->
<div id="batalConfirmModal" style="
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 10000;
    align-items: center;
    justify-content: center;
">
    <div style="
        background: var(--white);
        border-radius: 12px;
        padding: 32px;
        max-width: 450px;
        width: 90%;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        animation: modalSlideIn 0.3s ease-out;
    ">
        <!-- Header -->
        <div style="
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        ">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#c62828" stroke-width="2" style="flex-shrink: 0;">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
            <h3 style="margin: 0; color: var(--primary); font-size: 18px;">Konfirmasi Pembatalan</h3>
        </div>

        <!-- Warning Box -->
        <div style="
            background: #ffebee;
            border-left: 3px solid #c62828;
            border-radius: 6px;
            padding: 16px;
            margin-bottom: 24px;
        ">
            <p style="margin: 0; color: #c62828; font-size: 14px; line-height: 1.6;">
                <strong>Perhatian:</strong> Registrasi yang dibatalkan tidak dapat dilanjutkan kembali.
            </p>
        </div>

        <!-- Status Note -->
        <p id="batalConfirmLabel" style="
            margin: -12 0 24px 0;
            color: var(--text);
            font-size: 14px;
            line-height: 1.6;
        ">
            <!-- Will be filled by JavaScript -->
        </p>

        <!-- Buttons -->
        <div style="
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        ">
            <button type="button" onclick="cancelBatalAction()" style="
                background: var(--cream);
                color: var(--text);
                padding: 10px 20px;
                border: none;
                border-radius: 6px;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
            " onmouseover="this.style.background='var(--border)'" onmouseout="this.style.background='var(--cream)'">
                Batal
            </button>
            <button type="button" onclick="confirmBatalAction()" style="
                background: #c62828;
                color: white;
                padding: 10px 20px;
                border: none;
                border-radius: 6px;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
            " onmouseover="this.style.background='#b71c1c'" onmouseout="this.style.background='#c62828'">
                Ya, Batalkan
            </button>
        </div>
    </div>
</div>

<!-- CSS -->
<style>
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

.modal {
    display: flex !important;
}

/* Modal Animation */
@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-30px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

@media (max-width: 768px) {
    .info-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
    // Elite Fix v5.55: Restored missing script tag to prevent code leakage
    function closeBatalModal() {
        document.getElementById('batalConfirmModal').style.display = 'none';
        window.pendingBatalStatus = null;
    }

// Cancel batal action
function cancelBatalAction() {
    const statusSelect = document.getElementById('status');
    statusSelect.value = '';
    document.getElementById('batalConfirmModal').style.display = 'none';
    window.pendingBatalStatus = null;
}

// Close batal modal on outside click
document.getElementById('batalConfirmModal').addEventListener('click', function(e) {
    if (e.target === this) {
        cancelBatalAction();
    }
});

// Send WhatsApp update
// Send WhatsApp update using DB template
function sendWhatsAppUpdate() {
    const klien = '<?= htmlspecialchars($registrasi['klien_nama']) ?>';
    const hp = '<?= htmlspecialchars($registrasi['klien_hp']) ?>';
    const nomorRegistrasi = '<?= htmlspecialchars($registrasi['nomor_registrasi']) ?>';
    const statusSaatIni = '<?= STATUS_LABELS[$registrasi['status']] ?>';
    const username = '<?= htmlspecialchars($currentUser['username'] ?? '') ?>';
    
    // Clean phone number
    let cleanPhone = hp.replace(/[^0-9]/g, '');
    if (cleanPhone.startsWith('0')) {
        cleanPhone = '62' + cleanPhone.substring(1);
    }
    
    fetch(APP_URL + '/index.php?gate=cms_get_msg_tpl&key=wa_update')
        .then(r => r.json())
        .then(d => {
            let msg;
            if (d.success && d.template) {
                msg = d.template.template_body
                    .replace(/\{nama_klien\}/g, klien)
                    .replace(/\{telp_klien\}/g, hp)
                    .replace(/\{nomor_registrasi\}/g, nomorRegistrasi)
                    .replace(/\{status\}/g, statusSaatIni)
                    .replace(/\{nama_pengirim\}/g, username)
                    .replace(/\{phone\}/g, '<?= htmlspecialchars($appPhone ?? '') ?>')
                    .replace(/\{alamat\}/g, '<?= htmlspecialchars($appAddress ?? '') ?>');
            } else {
                msg = `Halo Bapak/Ibu ${klien},\n\nStatus registrasi ${nomorRegistrasi}: ${statusSaatIni}\n\nHormat kami,\n${username}`;
            }

            const waUrl = `https://wa.me/${cleanPhone}?text=${encodeURIComponent(msg)}`;
            window.open(waUrl, '_blank');
        })
        .catch(() => {
            const msg = `Halo Bapak/Ibu ${klien},\n\nStatus registrasi ${nomorRegistrasi}: ${statusSaatIni}`;
            const waUrl = `https://wa.me/${cleanPhone}?text=${encodeURIComponent(msg)}`;
            window.open(waUrl, '_blank');
        });
}

// Edit modal functions
function openEditModal() {
    document.getElementById('editModal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

// Modal terkunci - tidak bisa close dengan klik luar
// document.getElementById('editModal').addEventListener('click', function(e) {
//     if (e.target === this) {
//         closeEditModal();
//     }
// });

// Handle edit form submit
document.getElementById('editKlienForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const messageDiv = document.getElementById('actionMessage');
    
    messageDiv.style.display = 'block';
    messageDiv.className = 'form-message';
    messageDiv.textContent = 'Menyimpan perubahan...';
    
    fetch('<?= APP_URL ?>/index.php?gate=update_klien', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        messageDiv.className = 'form-message ' + (data.success ? 'success' : 'error');
        messageDiv.textContent = data.message;
        
        if (data.success) {
            setTimeout(() => {
                closeEditModal();
                window.location.reload();
            }, 1500);
        } else {
            setTimeout(() => {
                messageDiv.style.display = 'none';
            }, 5000);
        }
    })
    .catch(error => {
        messageDiv.className = 'form-message error';
        messageDiv.textContent = 'Terjadi kesalahan. Silakan coba lagi.';
        setTimeout(() => {
            messageDiv.style.display = 'none';
        }, 5000);
    });
});

// Michelin Script Consolidation: All logic moved to registrasi-detail.js to avoid double-declaration
</script>

<?php require VIEWS_PATH . '/templates/footer.php'; ?>
