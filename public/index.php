<?php
declare(strict_types=1);

/**
 * SK-01: Front Controller
 *
 * Lifecycle: Autoload → Config → Session → Headers → Sanitize → Routes → Dispatch
 */

// ── Base Path ─────────────────────────────────────────────────
define('BASE_PATH', dirname(__DIR__));

// ── Autoloader (SK-18: Lazy Loading) ──────────────────────────
require_once BASE_PATH . '/app/Core/Autoloader.php';

App\Core\Autoloader::register();
App\Core\Autoloader::addNamespace('App\\', BASE_PATH . '/app/');
App\Core\Autoloader::addNamespace('Modules\\', BASE_PATH . '/modules/');

// ── Configuration ─────────────────────────────────────────────
require_once BASE_PATH . '/config/app.php';

// ── Utils ─────────────────────────────────────────────────────
require_once BASE_PATH . '/app/Core/Utils/helpers.php';
require_once BASE_PATH . '/app/Core/Utils/security_helpers.php';
require_once BASE_PATH . '/app/Core/Utils/security.php';

// ── Secure Session (SK-04) ────────────────────────────────────
App\Security\Auth::startSecureSession();

// ── Security Headers (SK-17) ──────────────────────────────────
sendSecurityHeaders();
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// ── Input Sanitization (SK-02) ────────────────────────────────
App\Security\InputSanitizer::sanitizeGlobal();

// ── Route Registry (SK-01) ────────────────────────────────────
require_once BASE_PATH . '/config/routes.php';

// ── Dispatch (SK-01) ──────────────────────────────────────────
try {
    App\Core\Router::dispatch();
} catch (\Throwable $e) {
    App\Adapters\Logger::error('DISPATCH_EXCEPTION', [
        'message' => $e->getMessage(),
        'file'    => $e->getFile(),
        'line'    => $e->getLine(),
    ]);
    http_response_code(500);
    echo '<h1>500 - Internal Server Error</h1>';
}
