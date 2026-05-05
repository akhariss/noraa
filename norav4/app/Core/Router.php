<?php
declare(strict_types=1);

namespace App\Core;

use App\Controllers\{AuthController, DashboardController};
use Closure;

class Router
{
    private static array $routes = [];
    private static string $currentGate = '';

    public static function add(
        string $gate, 
        string $method, 
        array|string $handler, 
        array $guards = []
    ): void {
        self::$routes[$gate][strtoupper($method)] = [
            'handler' => $handler,
            'guards' => $guards
        ];
    }

    public static function dispatch(): void
    {
        self::$currentGate = trim($_GET['gate'] ?? 'home', '/');
        $method = $_SERVER['REQUEST_METHOD'];
        
        // Debug: Log the gate and available routes
        // error_log("Dispatching Gate: " . self::$currentGate);
        // error_log("Available Gates: " . implode(', ', array_keys(self::$routes)));
        
        if (!array_key_exists(self::$currentGate, self::$routes)) {
            http_response_code(404);
            require APP_PATH . '/Views/errors/404.php';
            exit;
        }

        $gateRoutes = self::$routes[self::$currentGate];

        if (!array_key_exists($method, $gateRoutes)) {
            // Fallback for HEAD request
            if ($method === 'HEAD' && array_key_exists('GET', $gateRoutes)) {
                $route = $gateRoutes['GET'];
            } else {
                http_response_code(405);
                require APP_PATH . '/Views/errors/405.php';
                exit;
            }
        } else {
            $route = $gateRoutes[$method];
        }

        // Run guards
        foreach ($route['guards'] ?? [] as $key => $value) {
            if (!self::runGuard($key, $value)) {
                http_response_code(403);
                // requirement for professional error pages
                require APP_PATH . '/Views/errors/403.php';
                exit;
            }
        }

        // Dispatch handler
        self::handle($route['handler']);
    }

    private static function runGuard(string $key, mixed $value): bool
    {
        return match($key) {
            'auth' => $value ? Auth::check() : true,
            'role' => Auth::checkRole((string)$value),
            default => true
        };
    }

    private static function handle(array|string $handler): void
    {
        if (is_string($handler)) {
            // Legacy route
            require APP_PATH . '/Views/' . $handler . '.php';
        } else {
            [$controller, $action] = $handler;
            $controllerInstance = new $controller();
            $controllerInstance->$action();
        }
    }
}

