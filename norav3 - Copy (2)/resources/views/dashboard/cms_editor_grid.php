<?php
/**
 * CMS Editor - Main Grid Menu
 * Following company-profile.css design system
 */

// Variables are passed from controller:
// - $currentUser
// - $auth
// - $cmsStats
// - $pageTitle
// - $activePage

if (!defined('VIEWS_PATH')) {
    http_response_code(500);
    require VIEWS_PATH . '/errors/500.php';
    exit;
}

require VIEWS_PATH . '/templates/header.php';
?>

<div class="cms-grid-wrapper">
    <!-- Hero Section -->
    <div class="cms-hero">
        <div class="container">
            <div class="cms-hero-content">
                <span class="cms-badge">CMS Management</span>
                <p class="cms-hero-subtitle">Kelola semua konten website dan aplikasi dari satu pusat kontrol</p>
            </div>
        </div>
    </div>

    <!-- Grid Cards -->
    <div class="container">
        <div class="cms-grid">
            <!-- Beranda Card -->
            <a href="<?= APP_URL ?>/index.php?gate=cms_editor&page=beranda" class="cms-card">
                <div class="card-icon card-icon-beranda">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    </svg>
                </div>
                <h3>Edit Beranda</h3>
                <p>Kelola konten halaman beranda website dengan editor visual</p>
                <div class="card-tags">
                    <span>Hero</span>
                    <span>Layanan</span>
                    <span>Testimoni</span>
                    <span>Footer</span>
                </div>
            </a>

            <!-- Workflow Card -->
            <a href="<?= APP_URL ?>/index.php?gate=cms_editor&page=workflow" class="cms-card">
                <div class="card-icon card-icon-workflow">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="16 3 21 3 21 8"></polyline>
                        <line x1="4" y1="20" x2="21" y2="3"></line>
                        <polyline points="21 16 21 21 16 21"></polyline>
                        <line x1="15" y1="15" x2="21" y2="21"></line>
                        <line x1="4" y1="4" x2="9" y2="9"></line>
                    </svg>
                </div>
                <h3>Kelola Workflow</h3>
                <p>Atur tahapan alur kerja registrasi, behavior, SLA, dan urutan drag & drop</p>
                <div class="card-tags">
                    <span>Drag & Drop</span>
                    <span>Behavior</span>
                    <span>SLA</span>
                </div>
            </a>

            <!-- Layanan Card -->
            <a href="<?= APP_URL ?>/index.php?gate=cms_editor&page=layanan" class="cms-card">
                <div class="card-icon card-icon-layanan">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                    </svg>
                </div>
                <h3>Kelola Layanan</h3>
                <p>Tambah, edit, atau hapus jenis layanan notaris</p>
                <div class="card-tags">
                    <span>CRUD</span>
                    <span>Layanan</span>
                </div>
            </a>

            <!-- Pesan WA Card -->
            <a href="<?= APP_URL ?>/index.php?gate=cms_editor&page=pesan" class="cms-card">
                <div class="card-icon card-icon-pesan">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                </div>
                <h3>Template Pesan WA</h3>
                <p>Edit template pesan WhatsApp otomatis untuk klien</p>
                <div class="card-tags">
                    <span>Auto</span>
                    <span>WhatsApp</span>
                    <span>Templates</span>
                </div>
            </a>

            <!-- Catatan Card -->
            <a href="<?= APP_URL ?>/index.php?gate=cms_editor&page=catatan" class="cms-card">
                <div class="card-icon card-icon-catatan">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    </svg>
                </div>
                <h3>Template Catatan</h3>
                <p>Template catatan internal auto-generate per status</p>
                <div class="card-tags">
                    <span>Auto</span>
                    <span>Internal</span>
                    <span>Status</span>
                </div>
            </a>

            <!-- Settings Card -->
            <a href="<?= APP_URL ?>/index.php?gate=cms_editor&page=settings" class="cms-card">
                <div class="card-icon card-icon-settings">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                    </svg>
                </div>
                <h3>Pengaturan Aplikasi</h3>
                <p>Identitas kantor, kontak, dan jam operasional</p>
                <div class="card-tags">
                    <span>Brand</span>
                    <span>Kontak</span>
                    <span>Jam</span>
                </div>
            </a>
        </div>
    </div>
</div>

<style>
.cms-grid-wrapper {
    min-height: 100vh;
    background: var(--cream);
    padding-bottom: 10px;
}

/* Hero Section - Following homepage hero */
.cms-hero {
    background: linear-gradient(145deg, var(--primary-dark) 0%, var(--primary) 50%, var(--primary-light) 100%);
    padding: 20px 0 20px;
    position: relative;
    overflow: hidden;
    margin-bottom: 5px;
}



@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.cms-hero-content {
    position: relative;
    z-index: 1;
    text-align: center;
    max-width: 800px;
    margin: 0 auto;
}

.cms-badge {
    display: inline-block;
    background: rgba(156, 124, 56, 0.15);
    color: var(--gold-light);
    padding: 10px 16px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-bottom: 0;
    border: 1px solid rgba(156, 124, 56, 0.3);
}

.cms-hero h1 {
    font-size: 0;
    line-height: 0;
    margin: 0;
    color: var(--white);
    font-family: 'Cormorant Garamond', serif;
    font-weight: 700;
    visibility: hidden;
    height: 0;
}

.cms-hero-subtitle {
    font-size: 15px;
    color: rgba(255, 255, 255, 0.85);
    margin: 0;
    font-weight: 500;
    padding-bottom: 10px;
}

/* Grid Cards - Following layanan cards */
.cms-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 10px;
    margin-bottom: 60px;
    margin-top: 15px;
}

.cms-card {
    background: linear-gradient(135deg, var(--cream), var(--white) 100%);
    padding: 22px 20px;
    border-radius: 14px;
    text-decoration: none;
    border: 2px solid rgba(156, 124, 56, 0.2);
    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    position: relative;
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.cms-card:hover {
    transform: translateY(-12px);
    box-shadow: 0 25px 60px rgba(27, 58, 75, 0.15);
    border-color: rgba(156, 124, 56, 0.4);
}

.cms-card:hover::before {
    transform: scale(1.4);
    background: radial-gradient(circle, rgba(156, 124, 56, 0.15) 0%, transparent 70%);
}

.cms-card:hover::after {
    opacity: 1;
}

.cms-card * {
    position: relative;
    z-index: 1;
}

.card-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 14px;
    box-shadow: 0 6px 16px rgba(0,0,0,0.12);
}

.card-icon svg {
    width: 24px;
    height: 24px;
    color: white;
}

.card-icon-beranda { background: linear-gradient(135deg, var(--primary), var(--primary-light)); }
.card-icon-layanan { background: linear-gradient(135deg, var(--gold), var(--gold-light)); }
.card-icon-pesan { background: linear-gradient(135deg, var(--primary-light), var(--primary)); }
.card-icon-catatan { background: linear-gradient(135deg, var(--gold-light), var(--gold)); }
.card-icon-settings { background: linear-gradient(135deg, var(--primary), var(--gold)); }
.card-icon-workflow { background: linear-gradient(135deg, #2d8b7a, var(--primary-light)); }

.cms-card h3 {
    font-size: 17px;
    color: var(--primary);
    margin-bottom: 10px;
    font-weight: 700;
    font-family: 'Cormorant Garamond', serif;
}

.cms-card p {
    font-size: 13px;
    color: var(--text-light);
    margin-bottom: 12px;
    line-height: 1.5;
    flex-grow: 1;
}

.card-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.card-tags span {
    background: var(--white);
    color: var(--primary);
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    border: 1px solid rgba(156, 124, 56, 0.2);
}
</style>

<?php require VIEWS_PATH . '/templates/footer.php'; ?>
