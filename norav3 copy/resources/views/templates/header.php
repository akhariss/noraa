<?php
// Load global app settings for layout if not injected
if (!isset($globalAppSettings)) {
    try {
        $cmsServiceL = new \App\Services\CMSEditorService();
        $globalAppSettings = $cmsServiceL->getAppSettings();
    } catch (\Exception $e) {
        $globalAppSettings = ['profile' => ['name' => ['value' => APP_NAME]]];
    }
}
$appNameDisplay = $globalAppSettings['profile']['name']['value'] ?? APP_NAME;

// ═══ Sidebar Count Badges ═══
// Single lightweight query for all sidebar badge counts
try {
    $__db = \App\Adapters\Database::getInstance();
    $__countRow = $__db->query(
        "SELECT 
            SUM(CASE WHEN w.behavior_role IN (0,1,2,3) THEN 1 ELSE 0 END) AS cnt_aktif,
            SUM(CASE WHEN w.behavior_role IN (8,7) THEN 1 ELSE 0 END) AS cnt_review,
            SUM(CASE WHEN w.behavior_role = 4 THEN 1 ELSE 0 END) AS cnt_penyerahan
         FROM registrasi p
         LEFT JOIN workflow_steps w ON p.current_step_id = w.id"
    )->fetch(\PDO::FETCH_ASSOC);
    $sidebarCounts = [
        'aktif'      => (int)($__countRow['cnt_aktif'] ?? 0),
        'review'     => (int)($__countRow['cnt_review'] ?? 0),
        'penyerahan' => (int)($__countRow['cnt_penyerahan'] ?? 0),
    ];
} catch (\Exception $e) {
    $sidebarCounts = ['aktif' => 0, 'review' => 0, 'penyerahan' => 0];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Dashboard' ?> - <?= htmlspecialchars($appNameDisplay) ?></title>
    <meta name="description" content="Dashboard Notaris & PPAT">
    
    <!-- Prevent caching -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Modular Button System (guidesop.md compliant) -->
    <link rel="stylesheet" href="<?= htmlspecialchars(APP_URL) ?>/public/assets/css/buttons.css?v=<?= time() ?>">
    
    <!-- Main styles -->
    <link rel="stylesheet" href="<?= htmlspecialchars(APP_URL) ?>/public/assets/css/dashboard.css?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars(APP_URL) ?>/public/assets/css/responsive.css?v=<?= time() ?>">
    
    <style>
        /* ═══ Premium Sidebar Nav ═══ */
        .sidebar-nav { padding: 20px 0; }
        
        .nav-item {
            display: flex; align-items: center; gap: 14px;
            padding: 12px 24px; margin: 2px 0;
            color: rgba(255, 255, 255, 0.7) !important;
            font-size: 14px; font-weight: 500;
            text-decoration: none; transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 3px solid transparent;
        }
        
        .nav-item:hover {
            color: #fff !important;
            background: rgba(255, 255, 255, 0.05);
        }
        
        .nav-item.active {
            color: #fff !important;
            background: rgba(255, 255, 255, 0.1);
            border-left-color: var(--gold);
            font-weight: 600;
        }

        .nav-item svg {
            width: 18px; height: 18px; opacity: 0.8;
            transition: opacity 0.2s;
        }
        .nav-item:hover svg, .nav-item.active svg { opacity: 1; }

        /* ═══ Proportional Sidebar Badge ═══ */
        .nav-badge {
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 22px; height: 20px; padding: 0 6px;
            background: rgba(255, 255, 255, 0.12); color: #fff;
            font-size: 11px; font-weight: 800; border-radius: 6px;
            margin-left: auto; letter-spacing: -0.2px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .nav-item.active .nav-badge {
            background: var(--gold); color: #fff;
            border-color: rgba(255,255,255,0.1);
        }

        /* Definitive Hide */
        .sidebar-close, .mobile-close, .sidebar-header button { display: none !important; }
    </style>
    
    <script>
        // Global variables
        const APP_URL = '<?= htmlspecialchars(APP_URL) ?>';
        
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
            document.querySelector('.sidebar-overlay').classList.toggle('active');
        }
    </script>
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Mobile Header -->
        <div class="mobile-header">
            <button class="menu-toggle" onclick="toggleSidebar()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>
            <span class="mobile-logo"><?= htmlspecialchars($appNameDisplay) ?></span>
        </div>
        
        <!-- Sidebar Overlay -->
        <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
        
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                </div>
                <span class="logo-text"><?= htmlspecialchars($appNameDisplay) ?></span>
            </div>

            <nav class="sidebar-nav">
                <!-- Data Registrasi -->
                <a href="<?= APP_URL ?>/index.php?gate=registrasi" class="nav-item <?= ($activePage ?? '') === 'registrasi' ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                    Data Registrasi
                </a>
                <!-- Tambah Registrasi -->
                <a href="<?= APP_URL ?>/index.php?gate=registrasi_create" class="nav-item <?= ($activePage ?? '') === 'registrasi_create' ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="12" y1="11" x2="12" y2="17"></line>
                        <line x1="9" y1="14" x2="15" y2="14"></line>
                    </svg>
                    Tambah Registrasi
                </a>
                <!-- Update Status -->
                <a href="<?= APP_URL ?>/index.php?gate=update_status_list" class="nav-item <?= ($activePage ?? '') === 'update_status_list' ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="1 4 1 10 7 10"></polyline>
                        <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path>
                    </svg>
                    Update Status
                    <?php if ($sidebarCounts['aktif'] > 0): ?>
                        <span class="nav-badge"><?= $sidebarCounts['aktif'] ?></span>
                    <?php endif; ?>
                </a>

                <?php if (($currentUser['role'] ?? '') === ROLE_OWNER): ?>
                <!-- Review (Owner only) -->
                <a href="<?= APP_URL ?>/index.php?gate=review" class="nav-item <?= in_array($activePage ?? '', ['review', 'finalisasi']) ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 11l3 3L22 4"></path>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                    </svg>
                    Review
                    <?php if ($sidebarCounts['review'] > 0): ?>
                        <span class="nav-badge"><?= $sidebarCounts['review'] ?></span>
                    <?php endif; ?>
                </a>
                <!-- Penyerahan (Owner only) -->
                <a href="<?= APP_URL ?>/index.php?gate=penyerahan" class="nav-item <?= ($activePage ?? '') === 'penyerahan' ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14"></path>
                        <path d="M12 5l7 7-7 7"></path>
                    </svg>
                    Penyerahan
                    <?php if ($sidebarCounts['penyerahan'] > 0): ?>
                        <span class="nav-badge"><?= $sidebarCounts['penyerahan'] ?></span>
                    <?php endif; ?>
                </a>
                <?php endif; ?>

                <!-- Arsip -->
                <a href="<?= APP_URL ?>/index.php?gate=arsip" class="nav-item <?= ($activePage ?? '') === 'arsip' ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="21 8 21 21 3 21 3 8"></polyline>
                        <rect x="1" y="3" width="22" height="5"></rect>
                        <line x1="10" y1="12" x2="14" y2="12"></line>
                    </svg>
                    Arsip
                </a>
                <!-- Laporan -->
                <a href="<?= APP_URL ?>/index.php?gate=laporan" class="nav-item <?= in_array($activePage ?? '', ['laporan', 'laporan_registrasi', 'laporan_keuangan', 'laporan_aktivitas']) ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="20" x2="18" y2="10"></line>
                        <line x1="12" y1="20" x2="12" y2="4"></line>
                        <line x1="6" y1="20" x2="6" y2="14"></line>
                    </svg>
                    Laporan
                </a>

                <?php if (($currentUser['role'] ?? '') === ROLE_OWNER): ?>
                <!-- App Settings (Owner only) -->
                <a href="<?= APP_URL ?>/index.php?gate=app_settings" class="nav-item <?= in_array($activePage ?? '', ['app_settings', 'users', 'cms', 'backups', 'audit']) ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                    </svg>
                    App Settings
                </a>
                <?php endif; ?>
            </nav>

            <div class="sidebar-footer">
                <div style="padding: 12px 20px; border-top: 1px solid rgba(255,255,255,0.1); margin-bottom: 4px;">
                    <span style="display: block; font-size: 14px; color: #fff; font-weight: 600;"><?= htmlspecialchars($currentUser['name'] ?? $currentUser['username'] ?? '') ?></span>
                    <span style="display: block; font-size: 11px; color: var(--gold); font-weight: 500; margin-top: 2px;"><?= ROLE_LABELS[$currentUser['role'] ?? ''] ?? 'User' ?></span>
                </div>
                <a href="<?= APP_URL ?>/index.php?gate=logout" class="logout-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    Logout
                </a>
            </div>
        </aside>

        <!-- Main Content (Elite Dynamic Area) -->
        <main class="main-content">
            <!-- Topbar (Symmetrical Header) -->
            <header class="topbar">
                <div class="topbar-left">
                    <h1 class="page-title"><?= $pageTitle ?? 'Dashboard' ?></h1>
                </div>
                <div class="topbar-right">
                    <div class="user-info">
                        <span class="user-name"><?= htmlspecialchars($currentUser['name'] ?? $currentUser['username'] ?? '') ?></span>
                        <span class="user-role"><?= ROLE_LABELS[$currentUser['role'] ?? ''] ?? 'User' ?></span>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="content">
