<?php
declare(strict_types=1);

/**
 * Unified Application Configuration
 * Replaces constants.php + database.php
 */

// ── Environment ───────────────────────────────────────────────
define('APP_ENV', 'development'); // 'production' on deploy
define('APP_NAME', 'Notaris Sri Anah SH.M.Kn');
define('APP_VERSION', '1.1.2');

// ── Status Labels (Elite Central Registry) ────────────────────
define('STATUS_LABELS', [
    'draft'                  => 'Draft / Pengumpulan Persyaratan',
    'pembayaran_admin'       => 'Pembayaran Administrasi',
    'validasi_sertifikat'    => 'Validasi Sertifikat',
    'pencecekan_sertifikat'  => 'Pengecekan Sertifikat',
    'pembayaran_pajak'       => 'Pembayaran Pajak',
    'validasi_pajak'         => 'Validasi Pajak',
    'penomoran_akta'         => 'Penomoran Akta',
    'pendaftaran'            => 'Pendaftaran',
    'pembayaran_pnbp'        => 'Pembayaran PNBP',
    'pemeriksaan_bpn'        => 'Pemeriksaan BPN',
    'perbaikan'              => 'Perbaikan',
    'selesai'                => 'Selesai',
    'diserahkan'             => 'Diserahkan',
    'ditutup'                => 'Ditutup',
    'batal'                  => 'Batal'
]);

// ── Development Mode ──────────────────────────────────────────
define('DEVELOPMENT_MODE', true); // false on production

// ── App URL ───────────────────────────────────────────────────
if (DEVELOPMENT_MODE) {
        // Strict APP_URL matching
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptName = $_SERVER['SCRIPT_NAME'];
    
    // Determine base folder path
    $appDir = dirname($scriptName);
    if (strpos($scriptName, '/public/index.php') !== false) {
        $appDir = dirname(dirname($scriptName));
    }
    
    if ($appDir === '\\' || $appDir === '/') $appDir = '';
    if ($appDir !== '' && substr($appDir, 0, 1) !== '/') $appDir = '/' . $appDir;
    define('APP_URL', rtrim($protocol . $host . $appDir, '/'));
} else {
    define('APP_URL', 'https://notaris.example.com'); // CHANGE FOR PRODUCTION
}

// ── Dynamic Constants ─────────────────────────────────────────
define('ASSET_URL', APP_URL . '/assets');
define('RESOURCES_PATH', BASE_PATH . '/resources');

// ── Directory Paths ───────────────────────────────────────────
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}
define('CONFIG_PATH', BASE_PATH . '/config');
define('APP_PATH', BASE_PATH . '/app');
define('MODULES_PATH', BASE_PATH . '/modules');
define('SERVICES_PATH', APP_PATH . '/Services');
define('MODELS_PATH', APP_PATH . '/Domain/Entities');
define('VIEWS_PATH', BASE_PATH . '/resources/views');
define('PUBLIC_PATH', BASE_PATH . '/public');
define('STORAGE_PATH', BASE_PATH . '/storage');
define('LOGS_PATH', STORAGE_PATH . '/logs');
define('BACKUPS_PATH', STORAGE_PATH . '/backups');
define('UTILS_PATH', APP_PATH . '/Core/Utils');

// ── Database ──────────────────────────────────────────────────
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'norasblmupdate');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// ── User Roles ────────────────────────────────────────────────
define('ROLE_OWNER', 'administrator');      // Administrator
define('ROLE_TRUSTED', 'administrator');    // Secondary Admin Role / Trusted Staff
define('ROLE_STAFF', 'staff');              // Staff
define('ROLE_PUBLIK', 'publik');

// ── Role Labels (UI Display) ──────────────────────────────────
define('ROLE_LABELS', [
    ROLE_OWNER   => 'Administrator',
    ROLE_STAFF   => 'Staff',
    ROLE_PUBLIK  => 'Publik',
]);

// ── Legacy Aliases (Backward Compat) ──────────────────────────
define('ROLE_NOTARIS', ROLE_OWNER);
define('ROLE_ADMIN', ROLE_STAFF);

// ── Status Workflow ───────────────────────────────────────────
// ── Status Keys (Legacy Compatibility Constants) ─────────
define('STATUS_DRAFT', 'draft');
define('STATUS_PEMBAYARAN_ADMIN', 'pembayaran_admin');
define('STATUS_VALIDASI_SERTIFIKAT', 'validasi_sertifikat');
define('STATUS_PENCECEKAN_SERTIFIKAT', 'pencecekan_sertifikat');
define('STATUS_PEMBAYARAN_PAJAK', 'pembayaran_pajak');
define('STATUS_VALIDASI_PAJAK', 'validasi_pajak');
define('STATUS_PENOMORAN_AKTA', 'penomoran_akta');
define('STATUS_PENDAFTARAN', 'pendaftaran');
define('STATUS_PEMBAYARAN_PNBP', 'pembayaran_pnbp');
define('STATUS_PEMERIKSAAN_BPN', 'pemeriksaan_bpn');
define('STATUS_PERBAIKAN', 'perbaikan');
define('STATUS_SELESAI', 'selesai');
define('STATUS_DISERAHKAN', 'diserahkan');
define('STATUS_DITUTUP', 'ditutup');
define('STATUS_BATAL', 'batal');

// ── Session Settings ──────────────────────────────────────────
define('SESSION_LIFETIME', 7200); // 2 hours
define('SESSION_NAME', 'nora_session');

// ── Cache Configuration ───────────────────────────────────────
define('CACHE_PATH', STORAGE_PATH . '/cache');
define('CACHE_TTL_HOMEPAGE', 3600);
define('CACHE_TTL_TRACKING', 300);

// ── Security Settings ─────────────────────────────────────────
define('CSRF_TOKEN_NAME', 'csrf_token');
define('HASH_ALGO', 'bcrypt');
define('HASH_COST', 12);

// ── Rate Limiting ─────────────────────────────────────────────
define('RATE_LIMIT_REQUESTS', 10);
define('RATE_LIMIT_WINDOW', 1);
define('RATE_LIMIT_HOMEPAGE', 100);
define('RATE_LIMIT_TRACKING', 5);

// ── File Settings ─────────────────────────────────────────────
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024);
define('ALLOWED_EXTENSIONS', ['pdf', 'jpg', 'jpeg', 'png']);

// ── CMS Pages ─────────────────────────────────────────────────
define('CMS_PAGES', ['home', 'layanan', 'tentang', 'kontak', 'testimoni']);

// ── Audit Log Actions ─────────────────────────────────────────
define('AUDIT_CREATE', 'create');
define('AUDIT_UPDATE', 'update');
define('AUDIT_DELETE', 'delete');
define('AUDIT_LOGIN', 'login');
define('AUDIT_LOGOUT', 'logout');
define('AUDIT_RESTORE', 'restore');

