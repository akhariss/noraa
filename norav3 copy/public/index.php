<?php
declare(strict_types=1);

/**
 * SK-01: Front Controller — Single Entry Point
 *
 * Request Lifecycle:
 *   1. Bootstrap  → BASE_PATH, Autoloader (PSR-4 lazy-load)
 *   2. Config    → constants (DB, roles, status, security keys)
 *   3. Helpers   → procedural functions (auth, format, SEO, security)
 *   4. Session   → hardened (fingerprint, timeout, regeneration)
 *   5. Headers   → CSP, anti-clickjacking, anti-sniff, no-cache
 *   6. Sanitize  → $_GET, $_POST, $_COOKIE via InputSanitizer
 *   7. Routes    → register all gate → [Controller, action] mappings
 *   8. Dispatch  → rate-limit → auth → role → controller → view
 */

// ── 1. Bootstrap ──────────────────────────────────────────────
define('BASE_PATH', dirname(__DIR__));

if (file_exists(BASE_PATH . '/vendor/autoload.php')) {
    require_once BASE_PATH . '/vendor/autoload.php';
}

require_once BASE_PATH . '/app/Core/Autoloader.php';

App\Core\Autoloader::register();
App\Core\Autoloader::addNamespace('App\\', BASE_PATH . '/app/');
App\Core\Autoloader::addNamespace('Modules\\', BASE_PATH . '/modules/');

// ── 2. Configuration ─────────────────────────────────────────
require_once BASE_PATH . '/config/app.php';

// ── 3. Helpers ────────────────────────────────────────────────
require_once BASE_PATH . '/app/Core/Utils/helpers.php';
require_once BASE_PATH . '/app/Core/Utils/security_helpers.php';
require_once BASE_PATH . '/app/Core/Utils/security.php';
require_once BASE_PATH . '/app/Core/Utils/seo_helpers.php';

// ── 4. Secure Session ─────────────────────────────────────────
App\Security\Auth::startSecureSession();

// ── 5. Security Headers ───────────────────────────────────────
header_remove('X-Powered-By');
sendSecurityHeaders();
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// ── 6. Input Sanitization ─────────────────────────────────────
App\Security\InputSanitizer::sanitizeGlobal();

// ── 7. Route Registry ─────────────────────────────────────────
require_once BASE_PATH . '/config/routes.php';

// ── 8. Dispatch ───────────────────────────────────────────────
try {
    App\Core\Router::dispatch();
} catch (\Throwable $e) {
    App\Adapters\Logger::error('DISPATCH_EXCEPTION', [
        'message' => $e->getMessage(),
        'file'    => $e->getFile(),
        'line'    => $e->getLine(),
        'trace'   => $e->getTraceAsString(),
    ]);

    http_response_code(500);

    if (defined('APP_ENV') && APP_ENV === 'development') {
        // Development: show full error details
        $statusCode  = 500;
        $title       = 'Internal Server Error';
        $message     = $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine();
        $showBackBtn = false;
        require VIEWS_PATH . '/errors/error.php';
    } else {
        // Production: generic message, no details exposed
        $statusCode  = 500;
        $title       = 'Terjadi Kesalahan';
        $message     = 'Maaf, terjadi kesalahan pada server. Silakan coba lagi nanti.';
        $showBackBtn = true;
        require VIEWS_PATH . '/errors/error.php';
    }
}
