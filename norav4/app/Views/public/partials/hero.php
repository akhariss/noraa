<?php
/**
 * Hero Section Partial - Nora V4 (Exact Mirror V3)
 */
$footerPhone = cmsContent($homepageData, 'footer', 'phone', '6285747898811');
$footerWorkDays     = cmsContent($homepageData, 'footer', 'work_days', 'Senin - Jumat');
$footerWorkHours    = cmsContent($homepageData, 'footer', 'work_hours', '08:00 - 16:00');
$footerWorkDaysSat  = cmsContent($homepageData, 'footer', 'work_days_sat', 'Sabtu');
$footerWorkHoursSat = cmsContent($homepageData, 'footer', 'work_hours_sat', '08:00 - 12:00');

$operationalHoursDisplay = htmlspecialchars($footerWorkDays) . ': ' . htmlspecialchars($footerWorkHours);
if (!empty($footerWorkDaysSat) && !empty($footerWorkHoursSat)) {
    $operationalHoursDisplay .= '<br>' . htmlspecialchars($footerWorkDaysSat) . ': ' . htmlspecialchars($footerWorkHoursSat);
}

$hero = [
    'badge'    => cmsContent($homepageData, 'hero', 'badge', 'Notaris & PPAT Sri Anah SH.M.Kn'),
    'title'    => cmsContent($homepageData, 'hero', 'title', 'Pendamping Hukum Resmi untuk Properti, Usaha, dan Keluarga'),
    'subtitle' => cmsContent($homepageData, 'hero', 'subtitle', 'Aman, transparan, dan sesuai peraturan perundang-undangan.'),
    'description' => cmsContent($homepageData, 'hero', 'description', 'Melayani pembuatan akta, legalisasi, dan konsultasi hukum dengan professionalism dan penuh kehati-hatian.'),
    'wa_number' => $footerPhone,
    'hours'     => $operationalHoursDisplay,
];

$waSvg = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>';
?>
<section class="hero">
    <div class="container">
        <div class="hero-inner">
            <div class="hero-content reveal reveal-left active">
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
            
            <div class="hero-card reveal reveal-right active">
                <h3><?= htmlspecialchars(cmsContent($homepageData, 'hero', 'quick_title', 'Layanan Cepat')) ?></h3>
                <div class="quick-links">
                    <a href="<?= APP_URL ?>/lacak" class="quick-link">
                        <div class="quick-link-icon">
                             <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </div>
                        <div>
                            <div class="quick-link-text"><?= htmlspecialchars(cmsContent($homepageData, 'hero', 'tracking_label', 'Lacak Registrasi')) ?></div>
                            <div class="quick-link-sub"><?= htmlspecialchars(cmsContent($homepageData, 'hero', 'tracking_sub', 'Cek status dokumen Anda')) ?></div>
                        </div>
                    </a>
                    <a href="https://wa.me/<?= sanitizePhoneForWa($hero['wa_number']) ?>" class="quick-link" target="_blank">
                        <div class="quick-link-icon"><?= $waSvg ?></div>
                        <div>
                            <div class="quick-link-text"><?= htmlspecialchars(cmsContent($homepageData, 'hero', 'contact_label', 'Hubungi Kami')) ?></div>
                            <div class="quick-link-sub"><?= htmlspecialchars(cmsContent($homepageData, 'hero', 'contact_sub', 'Respons cepat hari ini')) ?></div>
                        </div>
                    </a>
                </div>
                
                <div class="hero-card-hours">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <span><?= $hero['hours'] ?></span>
                </div>
            </div>
        </div>
    </div>
</section>
