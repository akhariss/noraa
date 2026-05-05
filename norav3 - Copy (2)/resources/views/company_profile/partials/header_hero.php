<?php
/**
 * Header and Hero Section Partial (Final Production)
 * Updated: 11 March 2026 - Phone & hours now from footer (LAW 0.2 Single Source of Truth)
 */
// Get phone and hours from FOOTER section (Primary source)
$footerPhone = cmsContent($homepageData, 'footer', 'phone', '6285747898811');
$footerWorkDays     = cmsContent($homepageData, 'footer', 'work_days', 'Senin - Jumat');
$footerWorkHours    = cmsContent($homepageData, 'footer', 'work_hours', '08:00 - 16:00');
$footerWorkDaysSat  = cmsContent($homepageData, 'footer', 'work_days_sat', 'Sabtu');
$footerWorkHoursSat = cmsContent($homepageData, 'footer', 'work_hours_sat', '08:00 - 12:00');

// Build operational hours display - Same format as footer
$footerWorkDays     = strip_tags($footerWorkDays);
$footerWorkHours    = strip_tags($footerWorkHours);
$footerWorkDaysSat  = strip_tags($footerWorkDaysSat);
$footerWorkHoursSat = strip_tags($footerWorkHoursSat);

// Build hours string - only add Saturday if it has values
$operationalHoursDisplay = htmlspecialchars($footerWorkDays) . ': ' . htmlspecialchars($footerWorkHours);
if (!empty($footerWorkDaysSat) && !empty($footerWorkHoursSat)) {
    $operationalHoursDisplay .= '<br>' . htmlspecialchars($footerWorkDaysSat) . ': ' . htmlspecialchars($footerWorkHoursSat);
}

$heroSec = cmsSection($homepageData, 'hero');
$hero = [
    'badge'    => cmsContent($homepageData, 'hero', 'badge', 'Notaris & PPAT Sri Anah SH.M.Kn'),
    'title'    => cmsContent($homepageData, 'hero', 'title', 'Pendamping Hukum Resmi untuk Properti, Usaha, dan Keluarga'),
    'subtitle' => cmsContent($homepageData, 'hero', 'subtitle', 'Aman, transparan, dan sesuai peraturan perundang-undangan.'),
    'description' => cmsContent($homepageData, 'hero', 'description', 'Melayani pembuatan akta, legalisasi, dan konsultasi hukum dengan professionalism dan penuh kehati-hatian.'),
    'buttons'  => cmsItems($homepageData, 'hero', 'button'),
    'wa_number' => $footerPhone, // Use footer phone (LAW 0.2)
    'hours'     => $operationalHoursDisplay, // Use footer hours (LAW 0.2)
];

// WhatsApp SVG Icon
$waSvg = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>';

$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$pageUrl = "$protocol://$host$uri";
$ogImage = APP_URL . '/public/assets/img/og-image.jpg'; 
$brandName = cmsContent($homepageData ?? [], 'footer', 'brand', 'Notaris Sri Anah SH.M.Kn');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($brandName) ?> - <?= htmlspecialchars($hero['badge']) ?></title>
    <meta name="description" content="<?= htmlspecialchars(!empty($hero['description']) ? $hero['description'] : 'Notaris Cirebon, Notaris Sri Anah SH.M.Kn, PPAT Cirebon. Pendamping Hukum Resmi untuk Properti, Usaha, dan Keluarga.') ?>">
    <meta name="keywords" content="Notaris Cirebon, PPAT Cirebon, Akta Properti, Pendirian Usaha Cirebon, Konsultasi Hukum, Notaris Sri Anah">
    <meta name="author" content="<?= htmlspecialchars($brandName) ?>">
    <link rel="canonical" href="<?= htmlspecialchars($pageUrl) ?>" />
    <meta property="og:title" content="<?= htmlspecialchars($brandName) ?> - <?= htmlspecialchars($hero['badge']) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($hero['desc'] ?? '') ?>">
    <meta property="og:image" content="<?= $ogImage ?>">
    <meta property="og:url" content="<?= htmlspecialchars($pageUrl) ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?= htmlspecialchars($brandName) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= APP_URL ?>/public/assets/css/company-profile.css?v=<?= time() ?>">
</head>
<body>
    <?php require __DIR__ . '/navbar.php'; ?>

    <main>

    <section class="hero">
        <div class="container">
            <div class="hero-inner">
                <div class="hero-content">
                    <div class="hero-badge">
                        <?= htmlspecialchars($hero['badge']) ?>
                    </div>
                    <h1>
                        <?= htmlspecialchars($hero['title']) ?>
                    </h1>
                    <p class="hero-subtitle">
                        <?= htmlspecialchars($hero['subtitle']) ?>
                    </p>
                    <p class="hero-desc">
                        <?= htmlspecialchars($hero['description']) ?>
                    </p>
                    <div class="hero-buttons">
                        <a href="https://wa.me/<?= sanitizePhoneForWa($hero['wa_number']) ?>" class="btn-cta" target="_blank">
                            <?= $waSvg ?> <?= htmlspecialchars(cmsContent($homepageData, 'hero', 'wa_text', 'Konsultasi via WhatsApp')) ?>
                        </a>
                        <a href="#testimoni" class="btn-layanan">
                            Lihat Testimoni
                        </a>
                    </div>
                </div>
                
                <div class="hero-card">
                    <h3><?= htmlspecialchars(cmsContent($homepageData, 'hero', 'quick_title', 'Layanan Cepat')) ?></h3>
                    <div class="quick-links">
                        <a href="<?= APP_URL ?>/index.php?gate=lacak" class="quick-link">
                            <div class="quick-link-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                            </div>
                            <div>
                                <div class="quick-link-text"><?= htmlspecialchars(cmsContent($homepageData, 'hero', 'tracking_label', 'Lacak Registrasi')) ?></div>
                                <div class="quick-link-sub"><?= htmlspecialchars(cmsContent($homepageData, 'hero', 'tracking_sub', 'Cek status dokumen Anda')) ?></div>
                            </div>
                        </a>
                        <a href="https://wa.me/<?= str_replace([' ', '-'], '', $hero['wa_number']) ?>" class="quick-link" target="_blank">
                            <div class="quick-link-icon"><?= $waSvg ?></div>
                            <div>
                                <div class="quick-link-text"><?= htmlspecialchars(cmsContent($homepageData, 'hero', 'contact_label', 'Hubungi Kami')) ?></div>
                                <div class="quick-link-sub"><?= htmlspecialchars(cmsContent($homepageData, 'hero', 'contact_sub', 'Respons cepat hari ini')) ?></div>
                            </div>
                        </a>
                    </div>
                    
                    <div class="hero-card-hours">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <span><?= $hero['hours'] ?></span>
                    </div>
                </div>
            </div>
        </div>
    </section>
