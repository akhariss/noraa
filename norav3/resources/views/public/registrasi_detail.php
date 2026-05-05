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
    
    <link rel="stylesheet" href="<?= APP_URL ?>/public/assets/css/company-profile.css?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= APP_URL ?>/public/assets/css/tracking.css?v=<?= time() ?>">
    <style>
        .detail-page {
            max-width: 1100px;
            margin: 0 auto;
            padding: 140px 20px 80px;
            background: var(--cream);
            color: var(--text);
            min-height: 100vh;
            overflow: hidden;
        }
        .hero-detail {
            border-bottom: 1px solid var(--border);
            margin-bottom: 40px;
            padding-bottom: 20px;
        }
        .hero-detail h1 {
            font-size: 48px;
            color: var(--primary-dark);
            margin: 10px 0;
            letter-spacing: -1px;
        }
        .section-glass {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        }
        .section-glass h2 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 28px;
            color: var(--gold);
            margin-bottom: 25px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 15px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 25px;
        }
        .info-item .label {
            display: block;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--text-muted);
            margin-bottom: 5px;
        }
        .info-item .value {
            font-size: 18px;
            font-weight: 600;
            color: var(--text);
        }
        
        .timeline-item.current .timeline-marker {
            background: var(--gold);
            box-shadow: 0 0 0 5px rgba(156, 124, 56, 0.2);
            color: #fff;
        }
        .timeline-label { color: var(--text); }
        .timeline-marker { background: var(--white); border: 1px solid var(--border); color: var(--text); }
        
        .log-entry {
            background: var(--white);
            border: 1px solid var(--border);
            border-left: 3px solid var(--gold);
            padding: 20px;
            border-radius: 0 15px 15px 0;
            margin-bottom: 15px;
        }
        
        @media (max-width: 900px) {
            .detail-grid { grid-template-columns: 1fr !important; }
            .hero-detail h1 { font-size: 32px; }
        }
    </style>
</head>
<body>
    <?php require VIEWS_PATH . '/company_profile/partials/navbar.php'; ?>

    <div class="detail-page">
        <div class="hero-detail">
            <a href="<?= APP_URL ?>/index.php?gate=lacak" class="back-link" style="color: var(--primary); display: inline-flex; align-items: center; gap: 8px; text-decoration: none; margin-bottom: 10px; font-weight: 600;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Kembali ke Lacak
            </a>
            <h1><?= htmlspecialchars($registrasi['nomor_registrasi']) ?></h1>
            <div style="display: flex; align-items: center; gap: 15px;">
                <?php $pStyle = \App\Domain\Entities\Registrasi::getStatusStyle((int)($registrasi['behavior_role'] ?? 0)); ?>
                <span class="badge" style="background: <?= $pStyle['bg'] ?>; color: <?= $pStyle['color'] ?>; padding: 8px 18px; border-radius: 50px; font-weight: 600;">
                    <?= getStatusLabels()[$registrasi['status']] ?? $registrasi['status'] ?>
                </span>
                <span style="color: var(--text-muted); font-size: 14px; font-weight: 500;">Pembaruan Terakhir: <?= date('d M Y, H:i', strtotime($registrasi['updated_at'])) ?></span>
                
                <?php if ($registrasi['kendala_flag']): ?>
                    <span class="badge-status error" style="background: #fff5f5; color: #e53e3e; border: 1px solid #feb2b2; padding: 4px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        BERMASALAH
                    </span>
                <?php else: ?>
                    <span class="badge-status success" style="background: #f0fff4; color: #38a169; border: 1px solid #9ae6b4; padding: 4px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        LANCAR
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <div class="section-glass">
            <div class="info-grid">
                <div class="info-item"><span class="label">Nama Klien</span><span class="value"><?= htmlspecialchars($registrasi['klien_nama']) ?></span></div>
                <div class="info-item"><span class="label">Jenis Layanan</span><span class="value"><?= htmlspecialchars($registrasi['nama_layanan']) ?></span></div>
                <div class="info-item"><span class="label">Tanggal Daftar</span><span class="value"><?= date('d M Y', strtotime($registrasi['created_at'])) ?></span></div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 30px;" class="detail-grid">
            <div class="section-glass">
                <h2>Progres Dokumen</h2>
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
                <h2>Riwayat Catatan</h2>
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
                    <p style="color: var(--text-muted); text-align: center;">Belum ada riwayat proses.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php require VIEWS_PATH . '/company_profile/partials/footer.php'; ?>

    <!-- Extra custom scripts if any can go here -->
</body>
</html>
