<?php
/**
 * Company Profile - Homepage (SUPER DYNAMIC V2)
 */

// 1. Ensure CMS helpers are available
require_once __DIR__ . '/cms_helpers.php';

// 2. Safely capture $homepageData from the controller
if (!isset($homepageData) || !is_array($homepageData)) {
    $homepageData = [];
}

$pageTitle = cmsContent($homepageData, 'footer', 'brand', APP_NAME);

// 3. Include partials for each section (Production Files)
$partialsBase = __DIR__ . '/partials/';

require $partialsBase . 'header_hero.php';
require $partialsBase . 'masalah_layanan.php';
require $partialsBase . 'testimoni_alur.php';
require $partialsBase . 'tentang_cta.php';
require $partialsBase . 'footer.php';
