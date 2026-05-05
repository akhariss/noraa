<?php
/**
 * Testimoni and Alur Kerja Partial (Final Production)
 */
// Get phone for dynamic WA links (LAW 0.2 - Single Source of Truth)
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
?>
<section class="testimoni" id="testimoni">
    <div class="container">
        <div class="section-title-wrap">
            <div class="section-tag"><?= htmlspecialchars($testimoni['tag']) ?></div>
            <h2 class="section-title">
                <?= htmlspecialchars($testimoni['title']) ?>
            </h2>
        </div>
        <div class="testimoni-grid">
            <?php foreach ($testimoni['items'] as $i => $item): 
                $rating = cmsExtra($item, 'rating', 5);
                $role = cmsExtra($item, 'role', 'Klien');
                $avatar = cmsExtra($item, 'avatar', substr($item['title'] ?? 'K', 0, 1));
            ?>
            <div class="testimoni-card">
                <div class="testimoni-stars"><?= str_repeat('★', $rating) ?></div>
                <p class="testimoni-text">
                   "<?= htmlspecialchars($item['description'] ?? '') ?>"
                </p>
                <div class="testimoni-author">
                    <div class="testimoni-avatar"><?= htmlspecialchars($avatar) ?></div>
                    <div class="testimoni-info">
                        <div class="testimoni-name">
                            <?= htmlspecialchars($item['title'] ?? '') ?>
                        </div>
                        <div class="testimoni-layanan"><?= htmlspecialchars($role) ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="testimoni-cta" style="text-align: center; margin-top: 40px;">
            <p style="margin-bottom: 20px;">Ingin berkonsultasikan juga? Klik tombol WhatsApp di bawah ya!</p>
            <a href="https://wa.me/<?= sanitizePhoneForWa($footerPhone) ?>" class="btn-cta" target="_blank">
                <?= $waSvg ?> Chat WhatsApp
            </a>
        </div>
    </div>
</section>

<section class="section alur" id="alur">
    <div class="container">
        <div class="section-title-wrap">
            <div class="section-tag"><?= htmlspecialchars($alur['tag']) ?></div>
            <h2 class="section-title">
                <?= htmlspecialchars($alur['title']) ?>
            </h2>
        </div>
        <div class="alur-grid">
            <?php foreach ($alur['items'] as $i => $item): 
                $step = cmsExtra($item, 'step_number', $item['sort_order']);
            ?>
            <div class="alur-item">
                <div class="alur-number"><?= $step ?></div>
                <div class="alur-content text-center">
                    <h4>
                        <?= htmlspecialchars($item['title'] ?? '') ?>
                    </h4>
                    <p>
                        <?= htmlspecialchars($item['description'] ?? '') ?>
                    </p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
