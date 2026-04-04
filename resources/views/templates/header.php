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
                <button class="sidebar-close" onclick="toggleSidebar()">&times;</button>
            </div>

            <nav class="sidebar-nav">
                <a href="<?= APP_URL ?>/index.php?gate=dashboard" class="nav-item <?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                    Dashboard
                </a>
                <a href="<?= APP_URL ?>/index.php?gate=registrasi" class="nav-item <?= ($activePage ?? '') === 'registrasi' ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                    Registrasi
                </a>
                
                <?php if (($currentUser['role'] ?? '') === ROLE_OWNER): ?>
                <a href="<?= APP_URL ?>/index.php?gate=finalisasi" class="nav-item <?= ($activePage ?? '') === 'finalisasi' ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 11l3 3L22 4"></path>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                    </svg>
                    Tutup Registrasi
                </a>
                <a href="<?= APP_URL ?>/index.php?gate=users" class="nav-item <?= ($activePage ?? '') === 'users' ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    User Management
                </a>
                <a href="<?= APP_URL ?>/index.php?gate=cms_editor" class="nav-item <?= ($activePage ?? '') === 'cms' ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 19l7-7 3 3-7 7-3-3z"></path>
                        <path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"></path>
                        <path d="M2 2l7.586 7.586"></path>
                        <circle cx="11" cy="11" r="2"></circle>
                    </svg>
                    CMS Editor
                </a>
                <a href="<?= APP_URL ?>/index.php?gate=backups" class="nav-item <?= ($activePage ?? '') === 'backups' ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                    </svg>
                    Backup
                </a>
                <a href="<?= APP_URL ?>/index.php?gate=audit" class="nav-item <?= ($activePage ?? '') === 'audit' ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                    Audit Log
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
