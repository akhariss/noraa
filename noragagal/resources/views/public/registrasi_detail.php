<?php
/**
 * Public Registrasi Detail View - SECURE (Premium Dark Style)
 */
require_once VIEWS_PATH . '/company_profile/cms_helpers.php';
require_once APP_PATH . '/Core/Utils/helpers.php';

$brandName = cmsContent($homepageData ?? [], 'footer', 'brand', 'Notaris Sri Anah SH.M.Kn');
$footerPhone = cmsContent($homepageData ?? [], 'footer', 'phone', '6285747898811');

$pageTitle = 'Detail Registrasi - ' . ($registrasi['nomor_registrasi'] ?? '-');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="<?= APP_URL ?>/public/assets/css/variables.css?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= APP_URL ?>/public/assets/css/company-profile.css?v=<?= time() ?>">
    <!-- Style managed modularly via company-profile.css -->
    <!-- Style managed in tracking.css -->
</head>
<body>
    <?php require VIEWS_PATH . '/company_profile/partials/navbar.php'; ?>

    <div class="detail-page">
        <div class="detail-wrapper">
            <div class="hero-detail">
                <a href="<?= APP_URL ?>/index.php?gate=lacak" class="back-link">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Kembali ke Lacak
                </a>
                <h1><?= htmlspecialchars($registrasi['nomor_registrasi']) ?></h1>
                <div style="display: flex; align-items: center; gap: 15px; margin-top: 15px;">
                    <?php $pStyle = \App\Domain\Entities\Registrasi::getStatusStyle((int)($registrasi['behavior_role'] ?? 0)); ?>
                    <span class="badge" style="background: <?= $pStyle['bg'] ?>; color: <?= $pStyle['color'] ?>; padding: 8px 18px; border-radius: 50px; font-weight: 600; font-size: 14px;">
                        <?= getStatusLabels()[$registrasi['status']] ?? $registrasi['status'] ?>
                    </span>
                    <span style="color: var(--text-muted); font-size: 14px; font-weight: 500;">Update: <?= date('d M Y, H:i', strtotime($registrasi['updated_at'])) ?></span>
                </div>
            </div>

            <div class="section-glass">
                <div class="info-grid">
                    <div class="info-item"><span class="label">Nama Klien</span><span class="value"><?= htmlspecialchars($registrasi['klien_nama']) ?></span></div>
                    <div class="info-item"><span class="label">Jenis Layanan</span><span class="value"><?= htmlspecialchars($registrasi['nama_layanan']) ?></span></div>
                    <div class="info-item"><span class="label">Tanggal Daftar</span><span class="value"><?= date('d M Y', strtotime($registrasi['created_at'])) ?></span></div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 30px; text-align: left;" class="detail-grid">
                <div class="section-glass">
                    <h2 style="font-family: var(--font-heading); font-size: 24px; color: var(--primary); margin-bottom: 20px; border-bottom: 1px solid var(--border); padding-bottom: 10px;">Progres Dokumen</h2>
                    <div class="timeline">
                        <?php foreach ($progress as $status => $item): ?>
                        <div class="timeline-item <?= $item['completed'] ? 'completed' : '' ?> <?= $item['current'] ? 'current' : '' ?>">
                            <div class="timeline-marker">
                                <?php if ($item['completed']): ?>
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <?php else: ?>
                                    <span><?= $item['order'] ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="timeline-content">
                                <span class="timeline-label"><?= $item['label'] ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="section-glass">
                    <h2 style="font-family: var(--font-heading); font-size: 24px; color: var(--primary); margin-bottom: 20px; border-bottom: 1px solid var(--border); padding-bottom: 10px;">Riwayat Catatan</h2>
                    <?php foreach (array_reverse($history) as $h): ?>
                    <div class="log-entry">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 13px;">
                            <span style="color: var(--gold-dark); font-weight: 700;"><?= date('d M, H:i', strtotime($h['created_at'])) ?></span>
                            <span style="color: var(--text-muted);"><?= $h['status_new_label'] ?? 'Update Progres' ?></span>
                        </div>
                        <?php if ($h['flag_kendala_active']): ?>
                            <div style="color: var(--danger); font-size: 13px; margin-bottom: 10px; font-weight: 600;">🚩 Kendala: <?= htmlspecialchars($h['flag_kendala_tahap']) ?></div>
                        <?php endif; ?>
                        <?php if ($h['catatan']): ?>
                            <div style="color: var(--text-light); font-style: italic; font-size: 14px;"><?= nl2br(htmlspecialchars($h['catatan'])) ?></div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($history)): ?>
                        <p style="color: var(--text-muted); text-align: center; padding: 40px 0;">Belum ada riwayat proses.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php require VIEWS_PATH . '/company_profile/partials/footer.php'; ?>
</body>
</html>
