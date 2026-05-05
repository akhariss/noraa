<?php
/**
 * Masalah (FAQ) Partial - Nora V4
 */
$masalahTag     = cmsContent($homepageData, 'masalah', 'section_name', 'Kendala Umum');
$masalahTitle   = cmsContent($homepageData, 'masalah', 'title', 'Mengapa Memilih Kami?');
$masalahItems   = cmsItems($homepageData, 'masalah', 'card');
$masalahClosing = cmsContent($homepageData, 'masalah', 'closing', 'Kami hadir memberikan solusi hukum yang pasti dan transparan.');
?>
<section class="section masalah" id="masalah">
    <div class="container">
        <div class="section-title-wrap reveal">
            <span class="section-tag"><?= htmlspecialchars($masalahTag) ?></span>
            <h2 class="section-title"><?= htmlspecialchars($masalahTitle) ?></h2>
        </div>

        <div class="masalah-grid">
            <?php if (!empty($masalahItems)): ?>
                <?php foreach ($masalahItems as $index => $item): ?>
                    <div class="masalah-card reveal" style="transition-delay: <?= ($index * 0.1) ?>s">
                        <div class="masalah-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="24" height="24">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                        </div>
                        <div class="masalah-content">
                            <h4><?= htmlspecialchars($item['title'] ?? '') ?></h4>
                            <p><?= htmlspecialchars($item['description'] ?? '') ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="masalah-card reveal">
                    <div class="masalah-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    </div>
                    <div class="masalah-content">
                        <h4>Proses Berbelit?</h4>
                        <p>Kami menyederhanakan setiap tahapan legalitas Anda dengan sistem tracking transparan.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($masalahClosing)): ?>
            <div class="masalah-closing reveal">
                <p><?= htmlspecialchars($masalahClosing) ?></p>
            </div>
        <?php endif; ?>
    </div>
</section>
