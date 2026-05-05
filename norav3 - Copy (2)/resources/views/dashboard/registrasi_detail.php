<?php
/**
 * Registrasi Detail View - WITH WHATSAPP POPUP
 */

use App\Domain\Entities\WorkflowStep;

$currentUser = getCurrentUser();
$pageTitle = 'Detail Registrasi - ' . ($registrasi['nomor_registrasi'] ?? '-');

// Context-aware Navigation
$from = $_GET['from'] ?? '';
$activePage = !empty($from) ? $from : 'registrasi';
$backUrl = APP_URL . '/index.php?gate=' . (!empty($from) ? $from : 'registrasi');
$backLabel = 'Kembali ke Daftar ' . (!empty($from) ? ucwords(str_replace(['_', 'list'], [' ', ''], $from)) : 'Registrasi');

$pageScript = 'registrasi-detail.js';

require VIEWS_PATH . '/templates/header.php';

// Define UI dynamic variables early to fix undefined warnings
$role = (int)($registrasi['behavior_role'] ?? 0);
$statusStyle = \App\Domain\Entities\Registrasi::getStatusStyle($role);
$bg = $statusStyle['bg']; 
$color = $statusStyle['color']; 
$border_badge = $statusStyle['border'];

$targetDateRaw = $registrasi['target_completion_at'] ?? '';
$isOverdue = (!empty($targetDateRaw) && strtotime($targetDateRaw) > 0 && new DateTime() > new DateTime($targetDateRaw));

// Elite Fix v4.37: Workflow Visibility Rules
$currentBehaviorRole = (int)($registrasi['behavior_role'] ?? 0);
$isReadOnlyStatus = in_array($currentBehaviorRole, [4, 5, 6, 7, 8], true);

$showPenyelesaian = in_array($currentBehaviorRole, [4, 7], true);
$showPenyerahan = in_array($currentBehaviorRole, [5, 6, 8], true);
$showPenutupan = ($currentBehaviorRole === 6);
$isTerminal = in_array($currentBehaviorRole, [5, 6, 7], true);
$isReviewOnly = ($currentBehaviorRole === 8);

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
    // JS Global Config
    window.APP_URL = '<?= APP_URL ?>';
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
        <a href="<?= $backUrl ?>" class="btn-back" style="
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
            <?= $backLabel ?>
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
                <h2 class="nora-detail-title" style="font-weight: 800; font-size: 16px; font-family: 'DM Sans', sans-serif;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color: var(--gold);">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span style="font-weight: 800;"><?= htmlspecialchars($registrasi['nomor_registrasi'] ?? '-') ?></span>
                </h2>
            
            <div style="display: flex; align-items: center; gap: 10px;">
                <span class="badge" style="background: <?= $bg ?>; color: <?= $color ?>; padding: 4px 12px; border-radius: 4px; font-size: 11px; font-weight: 700; border: 1px solid <?= $border_badge ?>;">
                    <?= htmlspecialchars($registrasi['status_label'] ?? $registrasi['status']) ?>
                </span>
                
                <?php if (!$isReadOnlyStatus): ?>
                <button type="button" onclick="openEditModal()" class="btn-nora-edit" style="padding: 6px 14px; font-size: 11px;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                    Ubah Data
                </button>
                <?php endif; ?>
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

                <!-- Absolute Unified Milestone Grid v6.15 -->
                <?php 
                    $showTglSelesai = false;
                    $tglSelesai = '';
                    if (!empty($registrasi['selesai_batal_at'])) {
                        $showTglSelesai = true;
                        $tglSelesai = $registrasi['selesai_batal_at'];
                    } else if ($role >= 4) {
                        $showTglSelesai = true;
                        $tglSelesai = $registrasi['updated_at'];
                    }
                ?>
                <?php if ($showTglSelesai) : ?>
                <div class="nora-data-group">
                    <div class="nora-data-label" style="color: #2e7d32;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        📊 Tgl Selesai
                    </div>
                    <div class="nora-data-value" style="color: #1b5e20; font-weight: 800;">
                        <?= date('d M Y H:i', strtotime($tglSelesai)) ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($registrasi['final_at'])) : ?>
                <div class="nora-data-group">
                    <div class="nora-data-label" style="color: <?= $role === 6 ? '#455a64' : '#1976d2' ?>;">
                        <?php if ($role === 6): ?>
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            🗄️ Tgl Penutupan
                        <?php else: ?>
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg>
                            📦 Tgl Diserahkan
                        <?php endif; ?>
                    </div>
                    <div class="nora-data-value" style="color: <?= $role === 6 ? '#37474f' : '#1565c0' ?>; font-weight: 800;">
                        <?= date('d F Y H:i', strtotime($registrasi['final_at'])) ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($role <= 3): ?>
                <div class="nora-data-group">
                    <div class="nora-data-label">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2L2 7l10 5 10-5-10-5z"></path><path d="M2 17l10 5 10-5"></path><path d="M2 12l10 5 10-5"></path></svg>
                        Flag Kendala
                    </div>
                    <div class="nora-data-value">
                        <?php
                            if ($hasActiveKendala) {
                                echo '<span style="font-weight: 700;">Bermasalah</span>';
                            } else {
                                echo '<span style="font-weight: 700;">Lancar</span>';
                            } ?>
                    </div>
                </div>
                <?php endif; ?>

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

    <!-- Card Switching Logic: Payment Section vs Audit Section v6.38 -->
    <?php if (in_array($role, [4, 5, 6, 7, 8])): ?>
        <!-- Unified Audit Summary Component v7.0 -->
        <?php require VIEWS_PATH . '/dashboard/parts/ringkasan_pelaksanaan.php'; ?>

    <?php else: ?>
        <!-- ORIGINAL PAYMENT SECTION (For Operational Phases 0, 1, 2, 3) -->
        <div class="nora-detail-card">
            <div class="nora-detail-header">
                <h3 class="nora-detail-title" style="font-size: 14px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color: var(--gold);">
                        <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                        <line x1="8" y1="12" x2="16" y2="12"></line>
                        <path d="M12 8v4"></path>
                    </svg>
                    Pembayaran
                </h3>
                
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span id="pembayaranBadgeHeader"></span>
                    <button type="button" id="btnTambahPembayaran" onclick="toggleFormBayar()" style="
                        background: var(--primary); color: white; border: none; cursor: pointer;
                        padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 700;
                        display: none; align-items: center; gap: 6px; transition: all 0.2s;
                    ">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                        </svg>
                        <span>Tambah</span>
                    </button>
                </div>
            </div>

            <div class="nora-detail-body">
                <div id="pembayaranSummary"></div>
                <div id="pembayaranForm" style="display: none;"></div>
                <div id="pembayaranRiwayat" style="display: none;"></div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Pembayaran Modal removed - transitioned to inline form -->

    <!-- Negative Confirm Modal -->
    <div id="negativeConfirmModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 10001; align-items: center; justify-content: center;">
        <div style="background: var(--white); border-radius: 12px; padding: 24px; max-width: 420px; width: 95%; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                <div style="background: #fff3e0; padding: 10px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <svg width="24" height="24" fill="var(--primary)" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                </div>
                <h3 style="margin: 0; color: var(--primary); font-size: 18px; font-weight: 800;">Konfirmasi Nilai Negatif</h3>
            </div>
            <p id="negativeConfirmMsg" style="margin-bottom: 24px; color: var(--text); line-height: 1.6; font-size: 13px;"></p>
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" onclick="closeNegativeConfirm()" style="background: #f5f5f5; color: var(--text); padding: 10px 24px; border: none; border-radius: 6px; font-weight: 700; cursor: pointer; font-size: 13px;">Batal</button>
                <button type="button" onclick="confirmNegativePayment()" style="background: var(--primary); color: white; padding: 10px 24px; border: none; border-radius: 6px; font-weight: 800; cursor: pointer; font-size: 13px; box-shadow: 0 4px 12px rgba(10,31,68,0.2);">Ya, Lanjutkan</button>
            </div>
        </div>
    </div>

    <!-- Success Modal (generic) -->
    <div id="successModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 10002; align-items: center; justify-content: center;">
        <div style="background: var(--white); border-radius: 12px; padding: 32px; max-width: 400px; width: 90%; text-align: center;">
            <svg width="48" height="48" fill="#2e7d32" viewBox="0 0 24 24" style="margin-bottom: 16px;"><polyline points="20 6 9 17 4 12"></polyline></svg>
            <h3 id="successTitle" style="margin: 0 0 8px 0; color: var(--primary);">Berhasil!</h3>
            <p id="successMessage" style="margin: 0 0 24px 0; color: var(--text);">Pembayaran berhasil disimpan.</p>
            <button onclick="closeSuccessModal()" style="background: var(--primary); color: white; padding: 12px 32px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">OK</button>
        </div>
    </div>

    <!-- Update/Status Card -->
    <div class="detail-card" style="background: var(--white); border-radius: 12px; padding: 16px 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 16px;">
        <?php if (!$isReadOnlyStatus): ?>
            <h3 style="margin: 0 0 14px 0; color: var(--primary); font-size: 14px; font-weight: 800; border-bottom: 1px solid var(--border); padding-bottom: 8px;">Update Status</h3>
            <form id="updateStatusForm" class="action-form">
                <input type="hidden" name="registrasi_id" value="<?= $registrasi['id'] ?>">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

                <div class="form-group" style="margin-bottom: 12px;">
                    <span style="display: block; font-weight: 700; margin-bottom: 4px; color: var(--text-muted); font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Status Saat Ini</span>
                    <div style="padding: 8px 12px; background: var(--cream); border-radius: 6px; font-weight: 700; color: var(--primary); font-size: 13px; border: 1px solid rgba(197,163,101,0.2);">
                        <?= htmlspecialchars($registrasi['status_label'] ?? $registrasi['status']) ?>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 12px;">
                    <label for="status_select" style="display: block; font-weight: 700; margin-bottom: 4px; color: var(--text-muted); font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Status Baru</label>
                    <select id="status_select" name="status" onchange="autoFillCatatan()" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border); border-radius: 6px; font-size: 13px; font-weight: 600; font-family: inherit; background: var(--white); cursor: pointer;">
                        <option value="">-- Pilih Status Berikutnya --</option>
                        <?php
                        $currentOrder = (int)($registrasi['workflow_order'] ?? 0);
                        $currentBehavior = (int)($registrasi['behavior_role'] ?? 0);
                        $isRepairMode = ($currentBehavior === 3);
                        $canCancel = (new \App\Domain\Entities\Registrasi())->canBeCancelled((int)$registrasi['id']);
                        
                        $totalTagihan = (float)($registrasi['total_tagihan'] ?? 0);
                        $totalBayar = (float)($registrasi['jumlah_bayar'] ?? 0);
                        $isLunas = ($totalTagihan > 0 && ($totalTagihan - $totalBayar) <= 0);

                        foreach ($availableSteps as $step) {
                            if ($step['step_key'] === $registrasi['status']) continue;

                            $s_behavior = (int)$step['behavior_role'];
                            $s_order = (int)$step['sort_order'];
                            $showInDropdown = false;
                            $isDisabled = false;
                            $labelSuffix = '';

                            // --- HIERARKI STATUS BARU (Elite Sync v5.89) ---
                            
                            // 1. Rule Global: Batal (Behavior 7)
                            // Muncul jika is_cancellable=1 ATAU Lunas
                            if ($s_behavior === 7) {
                                if ($canCancel || $isLunas) {
                                    $showInDropdown = true;
                                }
                            }

                            // 2. Rule Per Tahap
                            if ($currentBehavior <= 2) {
                                // Tampilkan SEMUA langkah setelahnya (hanya jika target adalah behavior proses 0-2)
                                if ($s_order > $currentOrder && $s_behavior <= 2) {
                                    $showInDropdown = true;
                                }
                                
                                // SELALU tampilkan behavior 8 (Audit) tapi kunci jika belum Lunas
                                if ($s_behavior === 8) {
                                    $showInDropdown = true;
                                    if (!$isLunas) {
                                        $isDisabled = true;
                                        $labelSuffix = ' (🔐 Perlu Lunas)';
                                    }
                                }

                                // Jika is_cancellable=1, munculkan opsi Perbaikan (Behavior 3)
                                if ($canCancel && $s_behavior === 3) {
                                    $showInDropdown = true;
                                }
                            }

                            // 3. Mode Perbaikan (Behavior 3)
                            if ($currentBehavior === 3) {
                                // Bisa kembali ke langkah-langkah proses (0, 1, 2)
                                if (in_array($s_behavior, [0, 1, 2], true)) {
                                    $showInDropdown = true;
                                }
                            }

                            // Status Terminal Utama (4, 8) tetap melalui alur Finalisasi

                            if ($showInDropdown) {
                                $disabledAttr = $isDisabled ? 'disabled style="color: #ccc;"' : '';
                                echo "<option value='{$step['step_key']}' {$disabledAttr}>" . htmlspecialchars($step['label']) . $labelSuffix . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 12px;">
                    <label for="status_catatan" style="display: block; font-weight: 700; margin-bottom: 4px; color: var(--text-muted); font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Catatan Update Status</label>
                    <textarea id="status_catatan" name="catatan" rows="3" placeholder="Pilih status untuk auto-fill catatan..." style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 6px; font-size: 13px; font-family: inherit; resize: vertical;"><?= htmlspecialchars($registrasi['catatan_internal'] ?? '') ?></textarea>
                </div>

                <!-- Flag Kendala (v4.67 - Clean Standard) -->
                <?php if (!$isReadOnlyStatus): ?>
                <div class="form-group" style="margin-bottom: 12px;">
                    <label for="flagKendala" style="
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
                    <button type="submit" class="btn-primary" style="background: var(--primary); color: white; padding: 10px 20px; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer;">Simpan Status</button>
                </div>
            </form>
        <?php else: ?>
            <!-- TERMINAL STATUS PANELS (Uniform Primary v5.84) -->
            <?php 
            $currentRole = (int)($registrasi['behavior_role'] ?? 0);
            
            $statusLabel = 'Registrasi Telah Selesai';
            $badgeText = 'Selesai';
            $icon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>';

            if ($currentRole === 8) {
                $statusLabel = 'Berkas Sedang Direview';
                $badgeText = 'Menunggu Review';
                $icon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>';
            } else if ($currentRole === 7) {
                $statusLabel = 'Registrasi Telah Dibatalkan';
                $badgeText = 'Menunggu Ditutup';
                $icon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>';
            } else if ($currentRole === 5 || $currentRole === 7) {
                $statusLabel = $currentRole === 7 ? 'Registrasi Telah Dibatalkan' : 'Berkas Telah Diserahkan';
                $badgeText = 'Menunggu Ditutup';
                $icon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>';
            } else if ($currentRole === 4) {
                $statusLabel = 'Penyelesaian Berkas Berhasil';
                $badgeText = 'Menunggu Diserahkan';
            } else if ($currentRole === 6) {
                $statusLabel = 'Registrasi Telah Ditutup';
                $badgeText = 'Telah Ditutup';
                $icon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>';
            }
            ?>
            <div style="background: linear-gradient(135deg, var(--primary), #2D5A6B); color: white; border-radius: 12px; padding: 14px 20px; display: flex; align-items: center; justify-content: space-between; margin: 0; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <?= $icon ?>
                    <span style="font-size: 14px; font-weight: 800;"><?= $statusLabel ?></span>
                </div>
                <div style="background: rgba(255,255,255,0.2); color: white; padding: 5px 12px; border-radius: 6px; font-size: 11px; font-weight: 800; border: 1px solid rgba(255,255,255,0.3); white-space: nowrap; text-transform: uppercase; letter-spacing: 0.5px;">
                    <?= $badgeText ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Hidden success/error message -->
    <div id="actionMessage" class="form-message" style="position: fixed; bottom: 20px; right: 20px; padding: 12px 20px; border-radius: 8px; font-weight: 600; z-index: 9999; display: none; box-shadow: 0 4px 20px rgba(0,0,0,0.15);"></div>

    <!-- DELIVERY SECTION (Gold Theme v5.88) -->
    <?php 
    $rawRole = (int)($registrasi['behavior_role'] ?? 0);
    if ($rawRole === 4): 
    ?>
    <div id="handover_card_compact" style="background: #fffcf5; border: 1px solid #f2e9d8; border-left: 4px solid var(--gold); border-radius: 12px; padding: 14px 18px; margin: 0 0 16px 0; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
        <h3 style="margin: 0 0 10px 0; color: var(--primary); font-size: 13px; font-weight: 800; display: flex; align-items: center; gap: 8px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color: var(--gold);"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg>
            Konfirmasi Penyerahan Berkas
        </h3>
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 10px; margin-bottom: 10px;">
            <div>
                <label for="penerima_name" style="display: block; font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 3px;">Penerima</label>
                <input type="text" id="penerima_name" oninput="updatePenerimaNote(this.value)" placeholder="Nama penerima..." style="width:100%; padding:8px 10px; border:1px solid var(--border); border-radius:6px; font-size:12px; font-weight: 500;">
            </div>
            <div>
                <label for="handover_note" style="display: block; font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 3px;">Catatan Penyerahan</label>
                <textarea id="handover_note" rows="1" style="width:100%; padding:8px 10px; border:1px solid var(--border); border-radius:6px; font-size:12px; font-weight: 500; resize:none;"><?= htmlspecialchars($finalNote) ?></textarea>
            </div>
        </div>
        <button type="button" onclick="serahkanRegistrasi()" style="width: 100%; padding: 10px; background: var(--gold); color: white; border: none; border-radius: 8px; font-size: 12px; font-weight: 800; cursor: pointer; box-shadow: 0 4px 10px rgba(197,163,101,0.2); transition: all 0.2s;">
            ✓ Konfirmasi & Selesaikan Registrasi
        </button>
    </div>
    <?php endif; ?>

    <!-- WHATSAPP BOX -->
    <div style="background: #e8f5e9; border: 1px solid #a5d6a7; border-radius: 8px; padding: 12px 16px; margin: 0;">
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px;">
            <div style="display: flex; align-items: center; gap: 10px; flex: 1;">
                <div style="background: #25d366; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="white"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.72.94 3.659 1.437 5.63 1.438h.004c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                </div>
                <div>
                    <h3 style="margin: 0; color: #1b5e20; font-size: 13px; font-weight: 700;">Kirim Notifikasi WhatsApp</h3>
                    <p style="margin: 2px 0 0 0; color: #2e7d32; font-size: 11px; font-weight: 600;">Kirim status terbaru ke klien.</p>
                </div>
            </div>
            <button type="button" onclick="sendWhatsApp()" style="padding: 8px 16px; background: #25d366; color: white; border: none; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer; white-space: nowrap;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" style="vertical-align: middle; margin-right: 4px;"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.025 3.141l-.66 2.41 2.464-.647c.834.456 1.834.823 2.939.823 3.181 0 5.767-2.586 5.768-5.766 0-3.18-2.587-5.766-5.768-5.766z"/></svg>
                Kirim WA
            </button>
        </div>
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
                color: white;
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
        border-radius: 10px;
        padding: 20px;
        max-width: 480px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
    ">
        <div style="
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border);
        ">
            <h3 style="margin: 0; color: var(--primary); font-size: 15px;">✏️ Edit Data Klien</h3>
            <button type="button" onclick="closeEditModal()" style="
                background: none;
                border: none;
                font-size: 20px;
                cursor: pointer;
                color: var(--text-muted);
            ">&times;</button>
        </div>

        <form id="editKlienForm">
            <input type="hidden" name="registrasi_id" value="<?= $registrasi['id'] ?>">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

            <div style="margin-bottom: 14px;">
                <label for="edit_nama" style="display: block; font-weight: 700; margin-bottom: 5px; color: var(--text); font-size: 12px;">Nama Klien</label>
                <input type="text" id="edit_nama" name="nama" value="<?= htmlspecialchars($registrasi['klien_nama']) ?>" required style="
                    width: 100%; padding: 8px 12px; border: 1px solid var(--border); border-radius: 6px; font-size: 13px; font-family: inherit;
                ">
            </div>

            <div style="margin-bottom: 14px;">
                <label for="edit_hp" style="display: block; font-weight: 700; margin-bottom: 5px; color: var(--text); font-size: 12px;">Nomor HP</label>
                <input type="text" id="edit_hp" name="hp" value="<?= htmlspecialchars($registrasi['klien_hp']) ?>" required placeholder="08xxxxxxxxxx" style="
                    width: 100%; padding: 8px 12px; border: 1px solid var(--border); border-radius: 6px; font-size: 13px; font-family: inherit;
                ">
            </div>

            <div style="margin-bottom: 14px;">
                <label for="edit_total_tagihan" style="display: block; font-weight: 700; margin-bottom: 5px; color: var(--text); font-size: 12px;">Total Tagihan (Rp)</label>
                <input type="number" id="edit_total_tagihan" name="total_tagihan" min="0" step="1" value="<?= (int)($registrasi['total_tagihan'] ?? 0) ?>" style="
                    width: 100%; padding: 8px 12px; border: 1px solid var(--border); border-radius: 6px; font-size: 13px; font-family: inherit;
                ">
                <small style="color: var(--text-muted); font-size: 10px;">Ubah total tagihan perkara.</small>
            </div>

            <div style="margin-bottom: 14px;">
                <label for="edit_target_date" style="display: block; font-weight: 700; margin-bottom: 5px; color: var(--text); font-size: 12px;">Target Selesai (Global SLA)</label>
                <input type="date" id="edit_target_date" name="target_date" value="<?= !empty($registrasi['target_completion_at']) ? date('Y-m-d', strtotime($registrasi['target_completion_at'])) : '' ?>" required style="
                    width: 100%; padding: 8px 12px; border: 1px solid var(--border); border-radius: 6px; font-size: 13px; font-family: inherit;
                ">
                <small style="color: var(--text-muted); font-size: 10px;">Default: 2 bulan dari pendaftaran.</small>
            </div>

            <div style="margin-bottom: 16px;">
                <label for="edit_keterangan" style="display: block; font-weight: 700; margin-bottom: 5px; color: var(--text); font-size: 12px;">Keterangan / Tentang Apa</label>
                <textarea id="edit_keterangan" name="keterangan" rows="2" placeholder="Contoh: Balik Nama Tanah Waris..." style="
                    width: 100%; padding: 8px 12px; border: 1px solid var(--border); border-radius: 6px; font-size: 13px; font-family: inherit; resize: vertical;
                "><?= htmlspecialchars($registrasi['keterangan'] ?? '') ?></textarea>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeEditModal()" style="
                    background: var(--cream); color: var(--text); padding: 8px 18px;
                    border: none; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer;
                ">Batal</button>
                <button type="submit" style="
                    background: var(--primary); color: white; padding: 8px 18px;
                    border: none; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer;
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
            margin: -12px 0 24px 0;
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

// Send WhatsApp update using DB template
function sendWhatsApp() {
    const klien = '<?= htmlspecialchars($registrasi['klien_nama']) ?>';
    const hp = '<?= htmlspecialchars($registrasi['klien_hp']) ?>';
    const nomorRegistrasi = '<?= htmlspecialchars($registrasi['nomor_registrasi']) ?>';
    const statusSaatIni = '<?= getStatusLabels()[$registrasi['status']] ?? $registrasi['status'] ?>';
    const username = '<?= htmlspecialchars($currentUser['username'] ?? '') ?>';

    const previewEl = document.getElementById('waChatPreviewDetail');
    previewEl.value = 'Memuat template...';
    document.getElementById('waNomorHaltDetail').textContent = nomorRegistrasi;
    window.waTargetPhone = hp;
    document.getElementById('waPopupDetail').style.display = 'flex';

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
                    .replace(/\{nama_kantor\}/g, '<?= addslashes(OFFICE_NAME) ?>')
                    .replace(/\{phone\}/g, '<?= addslashes(OFFICE_PHONE) ?>')
                    .replace(/\{alamat\}/g, '<?= addslashes(OFFICE_ADDRESS) ?>');
            } else {
                msg = `Halo Bapak/Ibu ${klien},\n\nStatus registrasi ${nomorRegistrasi}: ${statusSaatIni}\n\nHormat kami,\n${username}`;
            }
            previewEl.value = msg;
        })
        .catch(() => {
            const msg = `Halo Bapak/Ibu ${klien},\n\nStatus registrasi ${nomorRegistrasi}: ${statusSaatIni}`;
            previewEl.value = msg;
        });
}

function confirmSendWaDetail() {
    const hp = window.waTargetPhone;
    const msg = document.getElementById('waChatPreviewDetail').value;
    let cleanPhone = hp.replace(/[^0-9]/g, '');
    if (cleanPhone.startsWith('0')) cleanPhone = '62' + cleanPhone.substring(1);
    
    const waUrl = `https://wa.me/${cleanPhone}?text=${encodeURIComponent(msg)}`;
    window.open(waUrl, '_blank');
    document.getElementById('waPopupDetail').style.display = 'none';
}

function closeWaPopupDetail() {
    document.getElementById('waPopupDetail').style.display = 'none';
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
    
    const btnSubmit = this.querySelector('button[type="submit"]');
    const originalBtnContent = btnSubmit.innerHTML;
    btnSubmit.disabled = true;
    btnSubmit.innerHTML = 'Menyimpan...';

    fetch(APP_URL + '/index.php?gate=update_klien', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        return response.text().then(text => {
            try {
                return JSON.parse(text);
            } catch(e) {
                console.error('Server returned non-JSON:', text.substring(0, 200));
                return { success: false, message: 'Server error. Data mungkin sudah tersimpan, silakan refresh halaman.' };
            }
        });
    })
    .then(data => {
        if (data.success) {
            showAtomicModal('success', 'Berhasil', data.message || 'Data berhasil disimpan.', () => {
                window.location.reload();
            }, 500);
        } else {
            showAtomicModal('error', 'Gagal', data.message || 'Gagal menyimpan.');
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = originalBtnContent;
            messageDiv.style.display = 'none';
        }
    })
    .catch(err => {
        console.error('Fetch error:', err);
        showAtomicModal('error', 'Kesalahan', 'Gagal tersambung ke server. Coba refresh halaman.');
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = originalBtnContent;
        messageDiv.style.display = 'none';
    });
});

// Michelin Script Consolidation: All logic moved to registrasi-detail.js to avoid double-declaration
</script>

<?php
// Load pembayaran JS before footer
?>
<script>
// ── Pembayaran (Transaksi) Module ──────────────────────────
const REGISTRASI_ID = <?= (int)($registrasi['id'] ?? 0) ?>;
let _pembayaranData = {}; // Cache data untuk toggle
let totalTagihanGlobal = 0;

function toggleFormBayar() {
    const form = document.getElementById('pembayaranForm');
    if (form.style.display === 'none') {
        renderFormBayar(totalTagihanGlobal - (_pembayaranData.jumlah_bayar || 0));
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
}

function openPembayaranModal() {
    toggleFormBayar();
}

function closePembayaranModal() {
    // Modal is removed, no-op
}

function togglePembayaranRiwayat() {
    const list = document.getElementById('pembayaranRiwayat');
    const arrow = document.getElementById('pembayaranArrow');
    if (list.style.display === 'none') {
        list.style.display = 'block';
        arrow.style.transform = 'rotate(180deg)';
    } else {
        list.style.display = 'none';
        arrow.style.transform = 'rotate(0deg)';
    }
}

function closeNegativeConfirm() {
    document.getElementById('negativeConfirmModal').style.display = 'none';
}

function closeSuccessModal() {
    document.getElementById('successModal').style.display = 'none';
    loadPembayaran();
    closePembayaranModal();
}

function loadPembayaran() {
    if (!REGISTRASI_ID) return;

    fetch(APP_URL + '/index.php?gate=registrasi_detail&id=' + REGISTRASI_ID + '&fetch_transaksi=1')
        .then(r => r.json())
        .then(data => {
            _pembayaranData = data;
            renderPembayaranSummary(data);
            // SELALU render detail saat data sudah ada, tidak peduli state collapse
            renderPembayaranDetail(data);
        })
        .catch(() => {});
}

function renderPembayaranSummary(data) {
    _pembayaranData = data;
    const totalTagihan = data.total_tagihan || 0;
    totalTagihanGlobal = totalTagihan;
    const jumlahBayar = data.jumlah_bayar || 0;
    const sisa = totalTagihan - jumlahBayar;
    const isOverpayment = sisa < 0;
    const lunas = totalTagihan > 0 && sisa <= 0;
    const belumSet = totalTagihan <= 0;

    const badgeHeader = document.getElementById('pembayaranBadgeHeader');
    if (badgeHeader) {
        if (belumSet) badgeHeader.innerHTML = '';
        else if (lunas) badgeHeader.innerHTML = `<span style="background: #e8f5e9; color: #2e7d32; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 800; border: 1px solid #a5d6a7;">LUNAS</span>`;
        else badgeHeader.innerHTML = `<span style="background: #fff3e0; color: #f57c00; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 800; border: 1px solid #ffcc80;">BELUM LUNAS</span>`;
    }

    const btnTambah = document.getElementById('btnTambahPembayaran');
    if (btnTambah) btnTambah.style.display = lunas ? 'none' : 'flex';

    if (belumSet) {
        document.getElementById('pembayaranSummary').innerHTML = '<div style="padding: 12px 16px; background: #fdfdfd; border: 1px solid #eee; border-radius: 8px; color: var(--text-muted); font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; gap: 8px;">' +
            '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="opacity: 0.5;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>' +
            'Belum ada tagihan ditetapkan</div>';
        return;
    }

    const sisaLabel = isOverpayment ? 'Kelebihan' : 'Sisa Tagihan';
    const sisaDisplay = isOverpayment ? Math.abs(sisa) : sisa;
    const sisaColor = lunas ? '#2e7d32' : (isOverpayment ? '#1976d2' : '#f57c00');
    
    document.getElementById('pembayaranSummary').innerHTML = `
        <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
            <div style="display: flex; align-items: center; gap: 28px;">
                <div style="display: flex; flex-direction: column; gap: 2px;">
                    <span style="font-size: 10px; color: var(--text-muted); text-transform: uppercase; font-weight: 800; letter-spacing: 0.5px;">Total Tagihan</span>
                    <span style="color: var(--primary); font-weight: 900; font-size: 14px;">Rp ${formatNumber(totalTagihan)}</span>
                </div>
                <div style="width: 1px; height: 24px; background: #eee;"></div>
                <div style="display: flex; flex-direction: column; gap: 2px;">
                    <span style="font-size: 10px; color: var(--text-muted); text-transform: uppercase; font-weight: 800; letter-spacing: 0.5px;">Terbayar</span>
                    <span style="color: #1976d2; font-weight: 900; font-size: 14px;">Rp ${formatNumber(jumlahBayar)}</span>
                </div>
                <div style="width: 1px; height: 24px; background: #eee;"></div>
                <div style="display: flex; flex-direction: column; gap: 2px;">
                    <span style="font-size: 10px; color: var(--text-muted); text-transform: uppercase; font-weight: 800; letter-spacing: 0.5px;">${sisaLabel}</span>
                    <span style="color: ${sisaColor}; font-weight: 900; font-size: 14px;">Rp ${formatNumber(sisaDisplay)}</span>
                </div>
            </div>
            
            <button type="button" onclick="togglePembayaranRiwayat()" style="
                background: none; color: var(--text-muted); border: none; cursor: pointer;
                padding: 0; font-size: 12px; font-weight: 800;
                display: flex; align-items: center; gap: 6px; text-transform: uppercase; letter-spacing: 0.5px;
            ">
                <svg id="pembayaranArrow" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="transition: transform 0.2s;">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
                Riwayat
            </button>
        </div>
    `;
}
function renderPembayaranDetail(data) {
    const totalTagihan = data.total_tagihan || 0;
    const jumlahBayar = data.jumlah_bayar || 0;
    const sisa = totalTagihan - jumlahBayar;
    const lunas = totalTagihan > 0 && sisa <= 0;
    const belumSet = totalTagihan <= 0;

    if (belumSet) {
        document.getElementById('pembayaranRiwayat').innerHTML = '';
        document.getElementById('pembayaranForm').innerHTML = `
            <div style="background: #fff9f0; border: 1px solid var(--gold); border-radius: 6px; padding: 12px;">
                <form onsubmit="submitSetTagihan(event)">
                    <div style="display: flex; gap: 8px; align-items: flex-end;">
                        <div style="flex: 1;">
                            <label for="totalTagihanInput" style="display: block; font-size: 10px; font-weight: 800; margin-bottom: 4px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Total Tagihan (Rp)</label>
                            <input type="text" id="totalTagihanInput" name="total_tagihan" required 
                                placeholder="Contoh: 150.000"
                                style="width: 100%; padding: 8px 12px; border: 1px solid var(--border); border-radius: 6px; font-size: 13px; font-weight: 600;"
                                oninput="formatCurrencyInput(this)">
                        </div>
                        <button type="submit" style="padding: 9px 16px; background: var(--primary); color: white; border: none; border-radius: 6px; font-size: 11px; font-weight: 800; cursor: pointer; white-space: nowrap; text-transform: uppercase; letter-spacing: 0.5px;">Set Tagihan</button>
                    </div>
                </form>
            </div>
        `;
        return;
    }

    // Riwayat
    let riwayatHtml = '';
    if (data.riwayat && data.riwayat.length > 0) {
        riwayatHtml = '<div style="margin-top: 15px; border: 1px solid var(--border); border-radius: 12px; overflow: hidden; font-family: \'DM Sans\', sans-serif;">';
        riwayatHtml += '<table style="width: 100%; border-collapse: collapse; font-size: 13px;">';
        riwayatHtml += '<thead><tr style="background: #fdfcfb; color: var(--primary);">';
        riwayatHtml += '<th style="padding: 15px; border-bottom: 1px solid var(--border); text-align: left; font-weight: 800; font-size: 11px; text-transform: uppercase;">TANGGAL</th>';
        riwayatHtml += '<th style="padding: 15px; border-bottom: 1px solid var(--border); text-align: left; font-weight: 800; font-size: 11px; text-transform: uppercase;">DITAMBAH OLEH</th>';
        riwayatHtml += '<th style="padding: 15px; border-bottom: 1px solid var(--border); text-align: left; font-weight: 800; font-size: 11px; text-transform: uppercase;">KETERANGAN</th>';
        riwayatHtml += '<th style="padding: 15px; border-bottom: 1px solid var(--border); text-align: right; font-weight: 800; font-size: 11px; text-transform: uppercase;">NOMINAL</th>';
        riwayatHtml += '</tr></thead><tbody style="background: #fff;">';
        
        let totalMasuk = 0;
        data.riwayat.forEach(r => {
            const nominal = parseFloat(r.nominal_bayar);
            totalMasuk += nominal;
            const nominalClass = nominal < 0 ? 'color: #c62828;' : 'color: #2e7d32;';
            const nominalPrefix = nominal < 0 ? '-' : '+';
            riwayatHtml += `<tr style="border-bottom: 1px solid #f0f0f0;">
                <td style="padding: 15px; color: #444; font-weight: 500; font-size: 12px;">${formatTanggal(r.tanggal_bayar)}</td>
                <td style="padding: 15px; color: var(--primary); font-weight: 700; font-size: 12px;">${r.name || r.username || '-'}</td>
                <td style="padding: 15px; color: #666; font-size: 12px;">${r.catatan || '-'}</td>
                <td style="padding: 15px; text-align: right; font-weight: 700; ${nominalClass}; font-size: 14px;">
                    ${nominalPrefix}Rp ${formatNumber(Math.abs(nominal))}
                </td>
            </tr>`;
        });
        riwayatHtml += '</tbody>';
        riwayatHtml += `<tfoot style="background: var(--cream);">
            <tr>
                <td colspan="3" style="padding: 15px; text-align: right; font-weight: 800; color: var(--primary); font-size: 11px; text-transform: uppercase;">Total Transaksi Terverifikasi</td>
                <td style="padding: 15px; text-align: right; font-weight: 900; color: var(--primary); font-size: 15px;">
                    Rp ${formatNumber(totalMasuk)}
                </td>
            </tr>
        </tfoot>`;
        riwayatHtml += '</table></div>';
    } else {
        riwayatHtml = '<p style="color: var(--text-muted); font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">Belum ada riwayat pembayaran.</p>';
    }
    document.getElementById('pembayaranRiwayat').innerHTML = riwayatHtml;

    // Form bayar: do NOT auto-render, stays hidden until user clicks 'Tambah'
    if (lunas && !belumSet) {
        document.getElementById('pembayaranForm').innerHTML = '';
        document.getElementById('pembayaranForm').style.display = 'none';
    }
}

function renderFormBayar(maxNominal = null) {
    const today = new Date().toISOString().split('T')[0];
    const sisaSekarang = totalTagihanGlobal - (_pembayaranData.jumlah_bayar || 0);

    document.getElementById('pembayaranForm').innerHTML = `
        <div style="background: #fdfdfd; border: 1px solid #eee; border-radius: 8px; padding: 14px; margin-top: 16px;">
            <form onsubmit="submitBayar(event)">
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-bottom: 10px;">
                    <div>
                        <label for="nominalInput" style="display: block; font-size: 10px; font-weight: 800; margin-bottom: 4px; color: var(--text-muted); text-transform: uppercase;">Nominal (Rp)</label>
                        <input type="text" id="nominalInput" name="nominal_bayar" required 
                            placeholder="Contoh: 50.000"
                            style="width: 100%; padding: 7px 10px; border: 1px solid var(--border); border-radius: 5px; font-size: 13px; font-weight: 600;"
                            oninput="formatCurrencyInput(this); updateInlineCalc(this.value)">
                        <div id="inlineCalc" style="margin-top: 4px; font-size: 11px; font-weight: 700;"></div>
                    </div>
                    <div>
                        <label for="tanggalInput" style="display: block; font-size: 10px; font-weight: 800; margin-bottom: 4px; color: var(--text-muted); text-transform: uppercase;">Tanggal</label>
                        <input type="date" id="tanggalInput" name="tanggal_bayar" value="${today}" required
                            style="width: 100%; padding: 7px 10px; border: 1px solid var(--border); border-radius: 5px; font-size: 12px;">
                    </div>
                    <div>
                        <label for="catatanInput" style="display: block; font-size: 10px; font-weight: 800; margin-bottom: 4px; color: var(--text-muted); text-transform: uppercase;">Catatan</label>
                        <input type="text" id="catatanInput" name="catatan" placeholder="Opsional"
                            style="width: 100%; padding: 7px 10px; border: 1px solid var(--border); border-radius: 5px; font-size: 12px;">
                    </div>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 8px;">
                    <button type="button" onclick="toggleFormBayar()" style="padding: 6px 14px; background: #f5f5f5; color: var(--text); border: 1px solid var(--border); border-radius: 5px; font-size: 11px; font-weight: 700; cursor: pointer;">Batal</button>
                    <button type="submit" style="padding: 6px 16px; background: var(--primary); color: white; border: none; border-radius: 5px; font-size: 11px; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    `;
}

function updateInlineCalc(bayar) {
    // Strip dots and handle negative
    let rawVal = bayar.replace(/\./g, '');
    const isNegative = rawVal.startsWith('-');
    if (isNegative) rawVal = rawVal.substring(1);
    
    const valNominal = (parseFloat(rawVal) || 0) * (isNegative ? -1 : 1);
    const currentSisa = totalTagihanGlobal - (_pembayaranData.jumlah_bayar || 0);
    const sisaAkhir = currentSisa - valNominal;
    const calcEl = document.getElementById('inlineCalc');
    
    if (valNominal === 0) {
        calcEl.innerHTML = '';
        return;
    }

    if (sisaAkhir < 0) {
        calcEl.innerHTML = `<span style="color: #1976d2;">⚠️ Kelebihan: Rp ${formatNumber(Math.abs(sisaAkhir))}</span>`;
    } else {
        calcEl.innerHTML = `<span style="color: #2e7d32;">Sisa Setelah Bayar: Rp ${formatNumber(sisaAkhir)}</span>`;
    }
}

function formatCurrencyInput(input) {
    let value = input.value;
    
    // Allow leading negative sign
    const isNegative = value.startsWith('-');
    
    // Remove all non-numeric characters except leading '-'
    let cleanValue = value.replace(/[^-0-9]/g, '');
    if (cleanValue.includes('-')) {
        cleanValue = '-' + cleanValue.replace(/-/g, '');
    }
    
    if (!cleanValue || cleanValue === '-') {
        input.value = cleanValue;
        return;
    }

    // Format with dots
    let parts = cleanValue.split('-');
    let numPart = parts[parts.length - 1];
    let formatted = numPart.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    
    input.value = (isNegative ? '-' : '') + formatted;
}

// submitBayar validation wrapper removed - real submitBayar is below

function submitSetTagihan(e) {
    e.preventDefault();
    const rawVal = document.getElementById('totalTagihanInput').value.replace(/\./g, '');
    const total = parseFloat(rawVal) || 0;
    
    const formData = new FormData();
    formData.append('action', 'set_tagihan');
    formData.append('registrasi_id', REGISTRASI_ID);
    formData.append('total_tagihan', total);
    formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);

    fetch(APP_URL + '/index.php?gate=transaksi_store', { method: 'POST', body: formData })
        .then(r => {
            if (!r.ok) {
                return r.text().then(text => {
                    try {
                        return { success: false, message: JSON.parse(text).message || text };
                    } catch (e) {
                        return { success: false, message: text || 'Server error (' + r.status + ')' };
                    }
                });
            }
            return r.json();
        })
        .then(data => {
            if (data.success) {
                showAtomicModal('success', 'Berhasil', data.message, () => {
                    window.location.reload();
                }, 500);
            } else {
                showAtomicModal('error', 'Gagal', data.message || 'Gagal menetapkan tagihan');
            }
        })
        .catch(err => {
            console.error('Set tagihan error:', err);
            showAtomicModal('error', 'Kesalahan', 'Terjadi kesalahan: ' + err.message);
        });
}

function submitBayar(e) {
    e.preventDefault();
    const rawVal = document.getElementById('nominalInput').value.replace(/\./g, '');
    const nominal = parseFloat(rawVal) || 0;
    const currentSisa = totalTagihanGlobal - (_pembayaranData.jumlah_bayar || 0);
    
    if (currentSisa > 0 && nominal > currentSisa) {
        showAtomicModal('warning', 'Nominal Melebihi', 'Nominal melebihi sisa tagihan (Rp ' + formatNumber(currentSisa) + '). Tidak diizinkan.');
        return;
    }

    const formData = new FormData();
    formData.append('action', 'tambah_bayar');
    formData.append('registrasi_id', REGISTRASI_ID);
    formData.append('nominal_bayar', nominal);
    formData.append('tanggal_bayar', document.getElementById('tanggalInput').value);
    formData.append('catatan', document.getElementById('catatanInput').value);
    formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);

    // Confirm negative
    if (nominal < 0) {
        const afterMin = currentSisa - nominal; // nominal is negative, so currentSisa - (-val) = currentSisa + val
        const msg = `Apakah Anda yakin ingin input nilai negatif sebesar <strong>Rp ${formatNumber(Math.abs(nominal))}</strong>? <br><br>Input ini akan mengurangi jumlah terbayar saat ini. Sisa tagihan akan berubah dari Rp ${formatNumber(currentSisa)} menjadi <strong>Rp ${formatNumber(afterMin)}</strong>. Lanjutkan?`;
        
        document.getElementById('negativeConfirmMsg').innerHTML = msg;
        window._pendingBayarData = formData;
        document.getElementById('negativeConfirmModal').style.display = 'flex';
        return;
    }

    executeSubmitBayar(formData);
}

function confirmNegativePayment() {
    if (window._pendingBayarData) {
        executeSubmitBayar(window._pendingBayarData);
        window._pendingBayarData = null;
    }
    closeNegativeConfirm();
}

function executeSubmitBayar(formData) {
    const btnSubmit = document.querySelector('#pembayaranForm button[type="submit"]');
    const originalText = btnSubmit ? btnSubmit.textContent : '';
    if (btnSubmit) { btnSubmit.disabled = true; btnSubmit.textContent = 'Menyimpan...'; }

    fetch(APP_URL + '/index.php?gate=transaksi_store', { method: 'POST', body: formData })
        .then(r => {
            if (!r.ok) {
                return r.text().then(text => {
                    try { return { success: false, message: JSON.parse(text).message || text }; }
                    catch (e) { return { success: false, message: text || 'Server error' }; }
                });
            }
            return r.json();
        })
        .then(data => {
            if (data.success) {
                showAtomicModal('success', 'Berhasil', data.message || 'Pembayaran berhasil disimpan.', () => {
                    window.location.reload();
                }, 500);
                document.getElementById('pembayaranForm').style.display = 'none';
            } else {
                showAtomicModal('error', 'Gagal', data.message || 'Gagal menyimpan pembayaran.');
            }
        })
        .catch(err => {
            console.error('Transaksi error:', err);
            showAtomicModal('error', 'Kesalahan', 'Terjadi kesalahan koneksi.');
        })
        .finally(() => {
            const btnSubmit = document.querySelector('#pembayaranForm button[type="submit"]');
            if (btnSubmit) { btnSubmit.disabled = false; btnSubmit.textContent = 'Simpan Pembayaran'; }
        });
}

function formatNumber(n) {
    return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function formatTanggal(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
    return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
}


// Load data saat halaman siap
document.addEventListener('DOMContentLoaded', function() {
    loadPembayaran();
    
    // Payment modal event listeners
    const modalNominal = document.getElementById('modalNominal');
    if (modalNominal) {
        modalNominal.addEventListener('input', function() {
            const bayar = parseFloat(this.value) || 0;
            const total = totalTagihanGlobal;
            const sisa = total - bayar;
            const calcEl = document.getElementById('realtimeCalc');
            
            if (total > 0) {
                if (bayar >= total) {
                    calcEl.innerHTML = `<span style="color: #c62828;">⚠️ Dibayar: Rp ${formatNumber(bayar)} — Sisa: Rp ${formatNumber(sisa)}</span>`;
                } else {
                    calcEl.innerHTML = `Dibayar: Rp ${formatNumber(bayar)} — Sisa: Rp ${formatNumber(sisa)}`;
                }
            }
        });
    }
    
    const pembayaranFormModal = document.getElementById('pembayaranFormModal');
    if (pembayaranFormModal) {
        // Obsolete
    }
});

function submitModalPayment(formData) {
    fetch(APP_URL + '/index.php?gate=transaksi_store', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showAtomicModal('success', 'Berhasil', data.message || 'Pembayaran berhasil disimpan.', () => {
                    window.location.reload();
                }, 500);
            } else {
                showAtomicModal('error', 'Gagal', data.message || 'Gagal menyimpan pembayaran');
            }
        })
        .catch(err => {
            showAtomicModal('error', 'Kesalahan', 'Terjadi kesalahan: ' + err.message);
        });
}

</script>

<style>
/* === PREMIUM COMPACT WA POPUP (Navy & Gold) === */
#waChatPreviewDetail {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 14px;
    text-align: left;
    margin-top: 12px;
    max-height: 140px;
    overflow-y: auto;
    border: 1px solid var(--border);
}
.wa-bubble {
    background: var(--white);
    padding: 12px;
    border-radius: 8px;
    border-left: 4px solid var(--gold);
    font-size: 13px;
    color: var(--text);
    line-height: 1.6;
    box-shadow: 0 2px 5px rgba(0,0,0,0.03);
    word-wrap: break-word;
    white-space: pre-wrap;
}
</style>

<div id="waPopupDetail" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(15,31,40,0.85); z-index: 100000; align-items: center; justify-content: center; backdrop-filter: blur(8px);">
    <div style="background: #F7F4EF; border-radius: 30px; padding: 45px; max-width: 650px; width: 95%; text-align: center; box-shadow: 0 40px 100px rgba(0,0,0,0.5); border: 2.5px solid #B8964F; animation: atomicModalFadeIn 0.35s cubic-bezier(0.34, 1.56, 0.64, 1); position: relative;">
        
        <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 30px; gap: 15px;">
            <div style="width: 55px; height: 55px; background: #fff; border: 2px solid #9C7C38; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(156,124,56,0.2);">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="#9C7C38"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
            </div>
            <h3 style="margin: 0; color: #1B3A4B; font-family: 'Cormorant Garamond', serif; font-weight: 700; font-size: 28px;">Notifikasi WhatsApp</h3>
        </div>

        <div style="text-align: left; margin-bottom: 20px; background: rgba(255,255,255,0.7); padding: 15px 25px; border-radius: 16px; border: 1.5px solid #EEE; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <p style="margin: 0; color: #1B3A4B; font-size: 16px; font-weight: 950; letter-spacing: -0.3px;"><?= strtoupper($registrasi['klien_nama']) ?></p>
                <div style="display: flex; align-items: center; gap: 10px; margin-top: 3px;">
                    <p style="margin: 0; color: #9C7C38; font-size: 11px; font-weight: 950; text-transform: uppercase;">NOMOR ID: <span id="waNomorHaltDetail"><?= htmlspecialchars($registrasi['nomor_registrasi']) ?></span></p>
                </div>
            </div>
            <span style="background: #1B3A4B; color: #fff; font-size: 9px; font-weight: 950; padding: 5px 12px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.5px;">Custom Edit Mode</span>
        </div>
        
        <div style="text-align: left; margin-bottom: 5px;">
            <label style="font-size: 10px; font-weight: 950; color: #BBB; text-transform: uppercase; margin-bottom: 8px; display: block; margin-left: 5px; letter-spacing: 0.5px;">Draf Pesan WhatsApp (Bisa Diedit):</label>
            <textarea id="waChatPreviewDetail" style="width: 100%; min-height: 200px; background: #fff; border: 2px solid #F1E9D7; border-radius: 20px; padding: 25px; font-size: 15px; line-height: 1.6; color: #222; font-family: inherit; outline: none; transition: 0.3s; resize: vertical; box-shadow: inset 0 2px 10px rgba(156,124,56,0.05);"></textarea>
        </div>

        <div style="margin-top: 25px; display: flex; gap: 12px; justify-content: center;">
            <button type="button" onclick="closeWaPopupDetail()" style="background: #fff; color: #888; padding: 12px 35px; border: 2.5px solid #EEE; border-radius: 50px; font-weight: 800; cursor: pointer; font-size: 13px; transition: 0.2s;">Batal</button>
            <button type="button" onclick="confirmSendWaDetail()" style="background: #1B3A4B; color: #fff; padding: 12px 55px; border: none; border-radius: 50px; font-weight: 950; cursor: pointer; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; box-shadow: 0 10px 25px rgba(27,58,75,0.3); transition: 0.2s;">Kirim Notifikasi</button>
        </div>
    </div>
</div>

<?php require VIEWS_PATH . '/templates/footer.php'; ?>
