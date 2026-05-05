<?php
/**
 * Navbar Partial - Nora V4 (Exact Mirror V3)
 */
$brandName = $brandName ?? cmsContent($homepageData ?? [], 'footer', 'brand', 'Notaris Sri Anah SH.M.Kn');
$navPhone = $hero['wa_number'] ?? $footerPhone ?? cmsContent($homepageData ?? [], 'footer', 'phone', '6285747898811');
?>
    <header>
        <div class="container">
            <div class="header-inner">
                <a href="<?= APP_URL ?>/" class="logo">
                    <div class="logo-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="24" height="24">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                        </svg>
                    </div>
                    <span class="logo-text"><?= htmlspecialchars($brandName) ?></span>
                </a>

                <!-- Desktop Navigation Menu -->
                <nav class="desktop-menu desktop-only">
                    <a href="<?= APP_URL ?>/index.php">Beranda</a>
                    <a href="<?= APP_URL ?>/index.php#masalah">FAQ</a>
                    <a href="<?= APP_URL ?>/index.php?gate=lacak">Lacak</a>
                    <a href="<?= APP_URL ?>/index.php#tentang">Tentang</a>
                </nav>

                <div class="header-actions">
                    <a href="https://wa.me/<?= sanitizePhoneForWa($navPhone) ?>" class="btn-hubungi desktop-only" target="_blank">
                        Hubungi Kami
                    </a>
                    <button class="hamburger" id="hamburger-btn" aria-label="Menu">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="24" height="24">
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile Sidebar & Overlay -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>
    <nav id="nav-menu">
        <!-- Sidebar Header (Mobile Only) -->
        <div class="sidebar-header mobile-only">
            <span class="sidebar-brand"><?= htmlspecialchars($brandName) ?></span>
            <button class="close-sidebar" id="close-sidebar-btn" aria-label="Tutup Menu">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="20" height="20">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <a href="<?= APP_URL ?>/index.php">
            <svg class="mobile-only" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="24" height="24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
            Beranda
        </a>
        <a href="<?= APP_URL ?>/index.php#masalah">
            <svg class="mobile-only" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="24" height="24"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
            FAQ
        </a>
        <a href="<?= APP_URL ?>/index.php?gate=lacak">
            <svg class="mobile-only" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="24" height="24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
            Lacak
        </a>
        <a href="<?= APP_URL ?>/index.php#tentang">
            <svg class="mobile-only" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="24" height="24"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
            Tentang
        </a>
        <a href="https://wa.me/<?= sanitizePhoneForWa($navPhone) ?>" target="_blank" class="btn-hubungi mobile-only">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="24" height="24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
            Hubungi Kami
        </a>

        <!-- Sidebar Footer (Mobile Only) -->
        <div class="sidebar-footer mobile-only">
            <div class="footer-contact-item">
                <div class="contact-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                </div>
                <div class="contact-text">
                    <span class="contact-label">Email</span>
                    <a href="mailto:<?= e(cmsContent($homepageData, 'footer', 'email', 'info@example.com')) ?>" class="contact-value"><?= e(cmsContent($homepageData, 'footer', 'email', 'info@example.com')) ?></a>
                </div>
            </div>
        </div>
    </nav>
