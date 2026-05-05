<?php
/**
 * Testimoni and Alur Kerja Partial - Nora V4 (Exact Mirror V3)
 */
$footerPhone = cmsContent($homepageData, 'footer', 'phone', '6285747898811');

$testimoniSec = cmsSection($homepageData, 'testimoni');
$alurSec = cmsSection($homepageData, 'alur');

$testimoni = [
    'tag'   => $testimoniSec['section_name'] ?? 'Testimoni',
    'title' => cmsContent($homepageData, 'testimoni', 'title', 'Apa Kata Klien Kami?'),
    'items' => cmsItems($homepageData, 'testimoni', 'testimonial'),
];

$alur = [
    'tag'   => $alurSec['section_name'] ?? 'Alur Kerja',
    'title' => cmsContent($homepageData, 'alur', 'title', 'Cara Kerja Kami'),
    'items' => cmsItems($homepageData, 'alur', 'step'),
];

$waSvg = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>';
?>
<section class="testimoni" id="testimoni">
    <div class="container">
        <div class="section-title-wrap reveal">
            <div class="section-tag"><?= e($testimoni['tag']) ?></div>
            <h2 class="section-title">
                <?= e($testimoni['title']) ?>
            </h2>
        </div>
        <div class="testimoni-grid">
            <?php foreach ($testimoni['items'] as $i => $item): 
                $rating = cmsExtra($item, 'rating', 5);
                $role = cmsExtra($item, 'role', 'Klien');
                $avatar = cmsExtra($item, 'avatar', substr($item['title'] ?? 'K', 0, 1));
            ?>
            <div class="testimoni-card reveal" style="transition-delay: <?= $i * 0.1 ?>s">
                <div class="testimoni-stars"><?= str_repeat('★', (int)$rating) ?></div>
                <p class="testimoni-text">
                   "<?= e($item['description'] ?? '') ?>"
                </p>
                <div class="testimoni-author">
                    <div class="testimoni-avatar"><?= e($avatar) ?></div>
                    <div class="testimoni-info">
                        <div class="testimoni-name">
                            <?= e($item['title'] ?? '') ?>
                        </div>
                        <div class="testimoni-layanan"><?= e($role) ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="testimoni-cta reveal" style="text-align: center; margin-top: 40px;">
            <p style="margin-bottom: 20px;">Ingin berkonsultasikan juga? Klik tombol WhatsApp di bawah ya!</p>
            <a href="https://wa.me/<?= sanitizePhoneForWa($footerPhone) ?>" class="btn-cta" target="_blank">
                <?= $waSvg ?> Chat WhatsApp
            </a>
        </div>
    </div>
</section>

<section class="section alur" id="alur">
    <div class="container">
        <div class="section-title-wrap reveal">
            <div class="section-tag"><?= e($alur['tag']) ?></div>
            <h2 class="section-title">
                <?= e($alur['title']) ?>
            </h2>
        </div>
        <div class="alur-grid">
            <?php foreach ($alur['items'] as $i => $item): 
                $step = cmsExtra($item, 'step_number', $item['sort_order']);
            ?>
            <div class="alur-item reveal" style="transition-delay: <?= $i * 0.1 ?>s">
                <div class="alur-number"><?= $step ?></div>
                <div class="alur-content text-center">
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
