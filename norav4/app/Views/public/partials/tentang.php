<?php
/**
 * Tentang and CTA Partial - Nora V4 (Exact Mirror V3)
 */
$footerPhone = cmsContent($homepageData, 'footer', 'phone', '6285747898811');

$tentang = [
    'title'      => cmsContent($homepageData, 'tentang', 'title', 'Tentang Kami'),
    'quote'      => cmsContent($homepageData, 'tentang', 'quote', '"Setiap klien adalah keluarga."'),
    'name'       => cmsContent($homepageData, 'tentang', 'name', 'Sri Anah SH.M.Kn'),
    'role'       => cmsContent($homepageData, 'tentang', 'role', 'Notaris & PPAT'),
    'experience' => cmsContent($homepageData, 'tentang', 'experience', '15+'),
    'photo'      => cmsContent($homepageData, 'tentang', 'photo', ''), 
    'benefits'   => cmsItems($homepageData, 'tentang', 'benefit'),
];

$checkSvg = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>';
$waSvg = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>';
?>
<section class="section tentang" id="tentang">
    <div class="container">
        <div class="tentang-grid">
            <div class="tentang-img reveal reveal-left">
                <?php if (!empty($tentang['photo'])): ?>
                    <div class="tentang-photo">
                        <img src="<?= e($tentang['photo']) ?>" alt="<?= e($tentang['name']) ?>">
                    </div>
                    <div class="tentang-photo-info">
                        <div class="tentang-img-name"><?= e($tentang['name']) ?></div>
                        <div class="tentang-img-title"><?= e($tentang['role']) ?></div>
                    </div>
                <?php else: ?>
                    <div class="tentang-img-inner">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <div class="tentang-img-name"><?= e($tentang['name']) ?></div>
                        <div class="tentang-img-title"><?= e($tentang['role']) ?></div>
                    </div>
                <?php endif; ?>
                
                <div class="tentang-badge">
                    <strong><?= e($tentang['experience']) ?></strong>
                    <span>Tahun Pengalaman</span>
                </div>
            </div>
            <div class="tentang-content reveal reveal-right">
                <div class="section-tag"><?= e($tentang['title']) ?></div>
                <h2>Profesionalisme & Dedikasi</h2>
                <div class="tentang-narasi">
                    <?= e($tentang['quote']) ?>
                </div>
                <ul class="tentang-list">
                    <?php foreach ($tentang['benefits'] as $b): ?>
                    <li><?= $checkSvg ?> <?= e($b['title'] ?? '') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="section tracking reveal" id="track">
    <div class="container">
        <div class="tracking-wrapper">
            <div class="tracking-icon">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="40" height="40">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </div>
            <h2>Lacak Status Registrasi Anda</h2>
            <p>Cek progress registrasi dengan mudah dan cepat</p>
            <a href="<?= APP_URL ?>/lacak" class="btn-track" aria-label="Lacak Registrasi Anda">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                <span>Lacak Registrasi Sekarang</span>
            </a>
        </div>
    </div>
</section>

<section class="cta reveal" id="kontak">
    <div class="container">
        <div class="cta-box">
            <h2>Siap Melayani Anda</h2>
            <p>Konsultasikan kebutuhan hukum Anda sekarang juga melalui WhatsApp</p>
            <a href="https://wa.me/<?= sanitizePhoneForWa($footerPhone) ?>" class="cta-btn" target="_blank" aria-label="Konsultasi Kebutuhan Hukum via WhatsApp">
                <?= $waSvg ?> Hubungi via WhatsApp
            </a>
        </div>
    </div>
</section>
