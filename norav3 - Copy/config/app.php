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
define('APP_ENV', 'development');            // 'production' on deploy
define('APP_NAME', 'Notaris Sri Anah SH.M.Kn');
define('APP_VERSION', '1.1.2');

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
define('DB_HOST',     '127.0.0.1');
define('DB_NAME',     'nora3.0');
define('DB_USER',     'root');
define('DB_PASS',     '');
define('DB_CHARSET',  'utf8mb4');

// ═══════════════════════════════════════════════════════════════
// 4. APP URL
// ═══════════════════════════════════════════════════════════════
if (APP_ENV === 'development') {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script   = $_SERVER['SCRIPT_NAME'];
    $appDir   = strpos($script, '/public/index.php') !== false
        ? dirname(dirname($script))
        : dirname($script);

    if ($appDir === '\\' || $appDir === '/') $appDir = '';
    if ($appDir !== '' && $appDir[0] !== '/') $appDir = '/' . $appDir;
    define('APP_URL', rtrim($protocol . $host . $appDir, '/'));
} else {
    define('APP_URL', 'https://notaris.example.com');  // CHANGE FOR PRODUCTION
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

// Encryption keys — AUTO-GENERATED 2026-04-10 | 256-bit random
// JANGAN UBAH DI PRODUCTION — semua token lama akan invalid!
define('SECURITY_KEY_ID',       'd37e3c2f8ce54887f74ee25cb9b4be3b613231e339aea81e32bb367e4a9b7571');
define('SECURITY_KEY_IMG',      'ce7f66322a49bc6e86217af3d2abb83793f8460804a8ebab43cc1311b5f181c0');
define('SECURITY_KEY_TRACKING', '67d1ffd626810995a4d1401b521addf11517c4ab3dc3555a0f997a8d751ebb3d');
define('SECURITY_KEY_SHORT',    '220ab221ed744d70b4c399afaa634df429e60955fd0115a5be91c95534bf7c9c');

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
