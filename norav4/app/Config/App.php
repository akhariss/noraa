<?php
declare(strict_types=1);

/**
 * norav4 App Configuration - Manual .env loader for shared hosting
 */

// Load .env if exists
$envFile = BASE_PATH . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#') || !strpos($line, '=')) continue;
        [$key, $value] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value, '"\' ');
    }
}

// Environment
define('APP_ENV', $_ENV['APP_ENV'] ?? 'production');
define('APP_NAME', $_ENV['APP_NAME'] ?? 'Notaris Sri Anah SH.M.Kn');
define('APP_VERSION', $_ENV['APP_VERSION'] ?? '4.0.0');

// Paths
define('APP_PATH', BASE_PATH . '/app');
define('CONFIG_PATH', BASE_PATH . '/config');
define('VIEWS_PATH', BASE_PATH . '/app/Views');
define('PUBLIC_PATH', BASE_PATH . '/public');
define('STORAGE_PATH', BASE_PATH . '/storage');

// Robust URL detection for XAMPP/nested folders
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$script = $_SERVER['SCRIPT_NAME'];
$baseUrl = str_replace('/public/index.php', '', $script);
$baseUrl = ($baseUrl === '/') ? '' : $baseUrl;

define('APP_URL', $protocol . $host . $baseUrl);
define('ASSET_URL', dirname($script) . '/assets');

// Roles
define('ROLE_OWNER', 'administrator');
define('ROLE_STAFF', 'staff');
define('ROLE_PUBLIK', 'publik');

// Security Keys (Ported from V3)
define('SECURITY_KEY_ID', 'd37e3c2f8ce54887f74ee25cb9b4be3b613231e339aea81e32bb367e4a9b7571');
define('SECURITY_KEY_IMG', 'ce7f66322a49bc6e86217af3d2abb83793f8460804a8ebab43cc1311b5f181c0');
define('SECURITY_KEY_TRACKING', '67d1ffd626810995a4d1401b521addf11517c4ab3dc3555a0f997a8d751ebb3d');
define('SECURITY_KEY_SHORT', '220ab221ed744d70b4c399afaa634df429e60955fd0115a5be91c95534bf7c9c');

// Session
define('SESSION_LIFETIME', 7200);
define('SESSION_NAME', 'norav4_session');

// Security
define('HASH_ALGO', 'bcrypt');
define('CSRF_TOKEN_NAME', 'csrf_token');

// CMS
define('CMS_PAGES', ['home', 'layanan', 'tentang', 'kontak']);

