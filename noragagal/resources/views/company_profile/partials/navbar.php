<?php
/**
 * Navbar Partial (Extracted for reusability)
 */
// Ensure variables exist
$brandName = $brandName ?? cmsContent($homepageData ?? [], 'footer', 'brand', 'Notaris Sri Anah SH.M.Kn');
$navPhone = $hero['wa_number'] ?? $footerPhone ?? '';
?>
    <header>
        <div class="container">
            <div class="header-inner">
                <a href="<?= APP_URL ?>/" class="logo">
                    <div class="logo-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                        </svg>
                    </div>
                    <span class="logo-text"><?= htmlspecialchars($brandName) ?></span>
                </a>
                <nav id="nav-menu">
                    <a href="<?= url('home') ?>">Beranda</a>
                    <a href="<?= url('home') ?>#masalah">FAQ</a>
                    <a href="<?= url('lacak') ?>">Lacak</a>
                    <a href="<?= url('home') ?>#tentang">Tentang</a>
                    <a href="https://wa.me/<?= sanitizePhoneForWa($navPhone) ?>" target="_blank" class="btn-hubungi mobile-only">Hubungi Kami</a>
                </nav>
                <div class="header-actions">
                    <a href="https://wa.me/<?= sanitizePhoneForWa($navPhone) ?>" class="btn-hubungi desktop-only" target="_blank" aria-label="Hubungi Kami melalui WhatsApp di Menu Navigasi">
                        Hubungi Kami
                    </a>
                    <button class="hamburger" id="hamburger-btn" aria-label="Menu">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </header>
