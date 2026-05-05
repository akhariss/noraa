<?php
declare(strict_types=1);

namespace App\Core;

use App\Security\Auth;
use App\Security\RateLimiter;
use App\Adapters\Logger;

/**
 * SK-01: Secure Router
 * Query-parameter based routing (?gate=xxx) for backward compatibility.
 */
class Router
{
    private static array $routes = [];

    /**
     * Register a route.
     * @param string $gate      The gate name (query parameter value)
     * @param string $method    HTTP method ('GET', 'POST', 'ANY')
     * @param array  $handler   [ControllerClass, 'actionMethod']
     * @param array  $options   ['auth' => bool, 'role' => string|null, 'rateType' => string]
     */
    public static function add(string $gate, string $method, array $handler, array $options = []): void
    {
        self::$routes[$gate][] = [
            'method'  => strtoupper($method),
            'handler' => $handler,
            'options' => array_merge([
                'auth'     => false,
                'role'     => null,
                'rateType' => 'global',
            ], $options),
        ];
    }

    /**
     * Dispatch the current request.
     */
    public static function dispatch(): void
    {
        $gate   = $_GET['gate'] ?? 'home';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        // Handle session refresh AJAX endpoint
        if ($gate === 'refresh_session') {
            self::handleSessionRefresh();
            return;
        }

        // Find matching route
        if (!isset(self::$routes[$gate])) {
            self::notFound();
            return;
        }

        $matched = null;
        foreach (self::$routes[$gate] as $route) {
            if ($route['method'] === 'ANY' || $route['method'] === $method) {
                $matched = $route;
                break;
            }
        }

        if (!$matched) {
            // Check if there's a different method registered
            foreach (self::$routes[$gate] as $route) {
                if ($route['method'] !== $method) {
                    http_response_code(405);
                    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                    return;
                }
            }
            self::notFound();
            return;
        }

        $options = $matched['options'];

        // Rate limiting
        if (!RateLimiter::checkGlobal($options['rateType'])) {
            http_response_code(429);
            echo json_encode(['success' => false, 'message' => 'Too many requests']);
            return;
        }

        // Authentication check
        if ($options['auth']) {
            Auth::requireAuth();
        }

        // Role check
        if ($options['role'] !== null) {
            if (!Auth::verifyRole($options['role'])) {
                Logger::security('UNAUTHORIZED_ACCESS_ATTEMPT', [
                    'gate'     => $gate,
                    'user_id'  => Auth::get('user_id') ?? 'guest',
                    'role'     => Auth::get('role') ?? 'guest',
                    'required' => $options['role'],
                ]);

                if (self::isAjax()) {
                    http_response_code(403);
                    echo json_encode(['success' => false, 'message' => 'Session expired. Silakan login kembali.']);
                    exit;
                }

                http_response_code(403);
                if (file_exists(VIEWS_PATH . '/errors/403.php')) {
                    $statusCode = 403;
                    $title = 'Akses Ditolak';
                    $message = 'Anda tidak memiliki izin untuk mengakses halaman ini.';
                    $showBackButton = true;
                    $isLoggedIn = Auth::isLoggedIn();
                    require VIEWS_PATH . '/errors/403.php';
                } else {
                    echo '<h1>403 - Forbidden</h1>';
                }
                exit;
            }
        }

        // Dispatch to controller
        [$class, $action] = $matched['handler'];

        // Pass the ID parameter if available
        $id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;

        $controller = new $class();
        if ($id !== null) {
            $controller->$action($id);
        } else {
            $controller->$action();
        }
    }

    /**
     * Handle session refresh AJAX endpoint.
     */
    private static function handleSessionRefresh(): void
    {
        header('Content-Type: application/json');
        if (Auth::isLoggedIn()) {
            Auth::refreshActivity();
            echo json_encode(['success' => true, 'message' => 'Session refreshed']);
        } else {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Session expired']);
        }
        exit;
    }

    /**
     * 404 response.
     */
    private static function notFound(): void
    {
        http_response_code(404);
        echo '<h1>404 - Page Not Found</h1>';
        echo '<p><a href="' . APP_URL . '/index.php?gate=home">← Kembali ke Homepage</a></p>';
    }

    /**
     * Check if AJAX.
     */
    private static function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
