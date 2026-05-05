<?php
/**
 * Masalah and Layanan Section Partial (Final Production)
 */
$masalahSec = cmsSection($homepageData, 'masalah');
$layananSec = cmsSection($homepageData, 'layanan');

$masalah = [
    'tag'     => $masalahSec['section_name'] ?? 'Masalah',
    'title'   => cmsContent($homepageData, 'masalah', 'title', 'Solusi Hukum Terpercaya'),
    'closing' => cmsContent($homepageData, 'masalah', 'closing', 'Kami hadir untuk melayani.'),
    'items'   => cmsItems($homepageData, 'masalah', 'card'),
];

$layanan = [
    'tag'   => $layananSec['section_name'] ?? 'Layanan',
    'title' => cmsContent($homepageData, 'layanan', 'title', 'Layanan Profesional'),
    'items' => cmsItems($homepageData, 'layanan', 'card'),
];
?>
<section class="section masalah" id="masalah">
    <div class="container">
        <div class="section-title-wrap reveal">
            <div class="section-tag"><?= e($masalah['tag']) ?></div>
            <h2 class="section-title">
                <?= e($masalah['title']) ?>
            </h2>
        </div>
        <div class="masalah-grid">
            <?php foreach ($masalah['items'] as $i => $item): ?>
            <div class="masalah-card reveal" style="transition-delay: <?= $i * 0.1 ?>s">
                <div class="masalah-icon"><?= $warnSvg ?></div>
                <div class="masalah-content">
                    <h4>
                        <?= e($item['title'] ?? '') ?>
                    </h4>
                    <p>
                        <?= e($item['description'] ?? '') ?>
                    </p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section layanan" id="layanan">
    <div class="container">
        <div class="section-title-wrap reveal">
            <div class="section-tag"><?= e($layanan['tag']) ?></div>
            <h2 class="section-title">
                <?= e($layanan['title']) ?>
            </h2>
        </div>
        <div class="layanan-grid">
            <?php
            foreach ($layanan['items'] as $i => $item):
                $title = trim($item['title'] ?? '');
            ?>
            <div class="layanan-card premium reveal" style="transition-delay: <?= $i * 0.1 ?>s">
                <h4>
                    <?= e($title) ?>
                </h4>
                <p>
                    <?= e($item['description'] ?? '') ?>
                </p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
