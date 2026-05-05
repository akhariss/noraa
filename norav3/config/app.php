<?php
declare(strict_types=1);
date_default_timezone_set('Asia/Jakarta');

/**
 * NORA v2.0 — Application Configuration
 *
 * Sumber kebenaran tunggal untuk: environment, paths, database,
 * security, roles, dan status workflow.
 *
 * CATATAN PENTING:
 *   Status workflow (label, order, SLA) di-hardcode di sini sebagai
 *   FALLBACK. Untuk data real-time, gunakan WorkflowStep entity
 *   yang membaca dari tabel `workflow_steps`.
 */

// ═══════════════════════════════════════════════════════════════
// 1. ENVIRONMENT
// ═══════════════════════════════════════════════════════════════
require_once __DIR__ . '/../app/Core/Env.php';
\App\Core\Env::load(__DIR__ . '/../.env');

define('APP_ENV',     \App\Core\Env::get('APP_ENV', 'production'));
define('APP_NAME',    \App\Core\Env::get('APP_NAME', 'Notaris Sri Anah SH.M.Kn'));
define('APP_VERSION', \App\Core\Env::get('APP_VERSION', '1.1.2'));

// ═══════════════════════════════════════════════════════════════
// 2. DIRECTORY PATHS
// ═══════════════════════════════════════════════════════════════
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}
define('CONFIG_PATH',   BASE_PATH . '/config');
define('APP_PATH',      BASE_PATH . '/app');
define('MODULES_PATH',  BASE_PATH . '/modules');
define('VIEWS_PATH',    BASE_PATH . '/resources/views');
define('PUBLIC_PATH',   BASE_PATH . '/public');
define('STORAGE_PATH',  BASE_PATH . '/storage');
define('LOGS_PATH',     STORAGE_PATH . '/logs');
define('CACHE_PATH',    STORAGE_PATH . '/cache');

// ═══════════════════════════════════════════════════════════════
// 3. DATABASE
// ═══════════════════════════════════════════════════════════════
define('DB_HOST',     \App\Core\Env::get('DB_HOST', '127.0.0.1'));
define('DB_NAME',     \App\Core\Env::get('DB_NAME', 'nora3.0'));
define('DB_USER',     \App\Core\Env::get('DB_USER', 'root'));
define('DB_PASS',     \App\Core\Env::get('DB_PASS', ''));
define('DB_CHARSET',  \App\Core\Env::get('DB_CHARSET', 'utf8mb4'));

// ═══════════════════════════════════════════════════════════════
// 4. APP URL
// ═══════════════════════════════════════════════════════════════
$envUrl = \App\Core\Env::get('APP_URL');
if ($envUrl) {
    define('APP_URL', rtrim($envUrl, '/'));
} else {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // Auto-detect subfolder if not in .env
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $appDir = '';
    if ($script) {
        $appDir = strpos($script, '/public/index.php') !== false 
            ? dirname(dirname($script)) 
            : dirname($script);
        $appDir = str_replace('\\', '/', $appDir);
        if ($appDir === '/') $appDir = '';
    }
    
    define('APP_URL', rtrim($protocol . $host . $appDir, '/'));
}
define('ASSET_URL', APP_URL . '/assets');

// ═══════════════════════════════════════════════════════════════
// 5. ROLES
// ═══════════════════════════════════════════════════════════════
define('ROLE_OWNER',  'administrator');
define('ROLE_STAFF',  'staff');
define('ROLE_PUBLIK', 'publik');

define('ROLE_LABELS', [
    ROLE_OWNER  => 'Administrator',
    ROLE_STAFF  => 'Staff Operational',
    ROLE_PUBLIK => 'Publik',
]);

// ═══════════════════════════════════════════════════════════════
// 6. WORKFLOW STATUS (FROM DATABASE)
// ═══════════════════════════════════════════════════════════════
// Sumber kebenaran: tabel `workflow_steps` (step_key, label, sort_order, sla_days).
// Gunakan helper function: getStatusLabels(), getStatusOrder(), getStatusEstimasi()
// Lihat: app/Core/Utils/helpers.php (bottom)

// ═══════════════════════════════════════════════════════════════
// 7. SESSION & CACHE
// ═══════════════════════════════════════════════════════════════
define('SESSION_LIFETIME',    7200);   // 2 hours
define('SESSION_NAME',        'nora_session');
define('CACHE_TTL_HOMEPAGE',  3600);   // 1 hour
define('CACHE_TTL_TRACKING',  300);    // 5 minutes

// ═══════════════════════════════════════════════════════════════
// 8. SECURITY
// ═══════════════════════════════════════════════════════════════
define('CSRF_TOKEN_NAME', 'csrf_token');
define('HASH_ALGO',       'bcrypt');
define('HASH_COST',       12);

// Encryption keys — LOADED FROM ENVIRONMENT
define('SECURITY_KEY_ID',       \App\Core\Env::get('SECURITY_KEY_ID'));
define('SECURITY_KEY_IMG',      \App\Core\Env::get('SECURITY_KEY_IMG'));
define('SECURITY_KEY_TRACKING', \App\Core\Env::get('SECURITY_KEY_TRACKING'));
define('SECURITY_KEY_SHORT',    \App\Core\Env::get('SECURITY_KEY_SHORT'));

// Rate limiting
define('RATE_LIMIT_HOMEPAGE', 100);
define('RATE_LIMIT_TRACKING', 5);

// File upload
define('MAX_UPLOAD_SIZE',    5 * 1024 * 1024);  // 5 MB
define('ALLOWED_EXTENSIONS', ['pdf', 'jpg', 'jpeg', 'png']);

// ═══════════════════════════════════════════════════════════════
// 9. CMS & AUDIT
// ═══════════════════════════════════════════════════════════════
define('CMS_PAGES', ['home', 'layanan', 'tentang', 'kontak', 'testimoni']);

define('AUDIT_CREATE',   'create');
define('AUDIT_UPDATE',   'update');
define('AUDIT_DELETE',   'delete');
define('AUDIT_LOGIN',    'login');
define('AUDIT_LOGOUT',   'logout');
define('AUDIT_RESTORE',  'restore');

// ═══════════════════════════════════════════════════════════════
// 10. GLOBAL VARIABLES
// ═══════════════════════════════════════════════════════════════
require_once __DIR__ . '/variables.php';
