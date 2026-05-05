<?php
/**
 * Layanan Section Partial - Nora V4 (Exact Mirror V3)
 */
$layananSec = cmsSection($homepageData, 'layanan');

$layanan = [
    'tag'   => $layananSec['section_name'] ?? 'Layanan',
    'title' => cmsContent($homepageData, 'layanan', 'title', 'Layanan Profesional'),
    'items' => cmsItems($homepageData, 'layanan', 'card'),
];
?>
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