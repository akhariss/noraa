<?php
/**
 * Company Profile - Homepage (Mirroring V3 Exact Partial Sequence)
 */
require_once __DIR__ . '/cms_helpers.php';

if (!isset($homepageData) || !is_array($homepageData)) {
    $homepageData = [];
}

$partialsBase = __DIR__ . '/partials/';

require $partialsBase . 'hero.php';
require $partialsBase . 'masalah.php';
require $partialsBase . 'services.php';
require $partialsBase . 'testimoni.php';
require $partialsBase . 'tentang.php';
?>
