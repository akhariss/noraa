<?php
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

/**
 * norav4 Front Controller - Single Entry Point
 * Secure, Modular, Production Ready MVC PHP
 */

// 1. Autoloader
require BASE_PATH . '/app/Core/Autoloader.php';
App\Core\Autoloader::register();
App\Core\Autoloader::addNamespace('App\\', BASE_PATH . '/app/');

// 2. Helpers
require BASE_PATH . '/app/Core/helpers.php';

// 2. Config
require BASE_PATH . '/app/Config/App.php';
require BASE_PATH . '/app/Config/Database.php';

// 3. Security Headers & Session
header_remove('X-Powered-By');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

App\Core\Auth::startSecureSession();

// 4. Input Sanitization
App\Core\InputSanitizer::sanitizeGlobal();

// 5. Routes & Dispatch
require BASE_PATH . '/app/Config/Routes.php';
App\Core\Router::dispatch();

