<?php
/**
 * Nora V4 - Main Professional Layout
 * Preserves "Natural Luxe" aesthetic while ensuring 100% security and performance.
 */
$currentUser = \App\Core\Auth::user();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard' ?></title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS Assets -->
    <link rel="stylesheet" href="<?= ASSET_URL ?>/css/variables.css">
    <link rel="stylesheet" href="<?= ASSET_URL ?>/css/buttons.css">
    <link rel="stylesheet" href="<?= ASSET_URL ?>/css/dashboard.css">
    <link rel="stylesheet" href="<?= ASSET_URL ?>/css/responsive.css">
    
    <style>
        /* Modern Layout Fixes */
        .sidebar-nav { padding: 20px 0; }
        .nav-item {
            display: flex; align-items: center; gap: 14px;
            padding: 12px 24px; margin: 2px 0;
            color: rgba(255, 255, 255, 0.7) !important;
            font-size: 14px; font-weight: 500;
            text-decoration: none; transition: all 0.25s ease;
            border-left: 3px solid transparent;
        }
        .nav-item:hover, .nav-item.active {
            color: #fff !important;
            background: rgba(255, 255, 255, 0.08);
        }
        .nav-item.active {
            border-left-color: var(--gold);
            font-weight: 600;
        }
        .nav-badge {
            margin-left: auto;
            background: var(--gold);
            color: #000;
            font-size: 10px;
            font-weight: 800;
            padding: 2px 6px;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                </div>
                <span class="logo-text"><?= APP_NAME ?></span>
            </div>

            <nav class="sidebar-nav">
                <a href="<?= APP_URL ?>/registrasi" class="nav-item <?= ($activeTab ?? '') === 'registrasi' ? 'active' : '' ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                    Data Registrasi
                </a>
                <a href="<?= APP_URL ?>/registrasi/create" class="nav-item <?= ($activeTab ?? '') === 'registrasi/create' ? 'active' : '' ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="12" y1="11" x2="12" y2="17"></line><line x1="9" y1="14" x2="15" y2="14"></line></svg>
                    Tambah Registrasi
                </a>
                <a href="<?= APP_URL ?>/registrasi/update" class="nav-item <?= ($activeTab ?? '') === 'update_status_list' ? 'active' : '' ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
                    Update Status
                </a>

                <?php if (($currentUser['role'] ?? '') === ROLE_OWNER): ?>
                <a href="<?= APP_URL ?>/registrasi/review" class="nav-item <?= ($activeTab ?? '') === 'review' ? 'active' : '' ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"></path><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                    Review
                </a>
                <a href="<?= APP_URL ?>/registrasi/penyerahan" class="nav-item <?= ($activeTab ?? '') === 'penyerahan' ? 'active' : '' ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"></path><path d="M12 5l7 7-7 7"></path></svg>
                    Penyerahan
                </a>
                <?php endif; ?>

                <a href="<?= APP_URL ?>/registrasi/arsip" class="nav-item <?= ($activeTab ?? '') === 'arsip' ? 'active' : '' ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="21 8 21 21 3 21 3 8"></polyline><rect x="1" y="3" width="22" height="5"></rect><line x1="10" y1="12" x2="14" y2="12"></line></svg>
                    Arsip
                </a>
                <a href="<?= APP_URL ?>/laporan" class="nav-item <?= ($activeTab ?? '') === 'laporan' ? 'active' : '' ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                    Laporan
                </a>

                <?php if (($currentUser['role'] ?? '') === ROLE_OWNER): ?>
                <div class="nav-divider" style="height: 1px; background: rgba(255,255,255,0.1); margin: 10px 20px;"></div>
                <a href="<?= APP_URL ?>/settings" class="nav-item <?= ($activeTab ?? '') === 'settings' ? 'active' : '' ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                    App Settings
                </a>
                <?php endif; ?>
            </nav>

            <div class="sidebar-footer">
                <div class="user-pill">
                    <span class="u-name"><?= htmlspecialchars($currentUser['username'] ?? $currentUser['nama'] ?? 'User') ?></span>
                    <span class="u-role"><?= strtoupper($currentUser['role']) ?></span>
                </div>
                <a href="<?= APP_URL ?>/logout" class="logout-link">Logout</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="topbar">
                <h1 class="page-title"><?= $title ?? 'Dashboard' ?></h1>
                <div class="topbar-actions">
                    <!-- Dynamic actions can be injected here -->
                </div>
            </header>

            <div class="content">
                <?php require $contentView; ?>
            </div>
        </main>
    </div>

    <script src="<?= ASSET_URL ?>/js/main.js"></script>
</body>
</html>
