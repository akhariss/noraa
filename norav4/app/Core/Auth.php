<?php
declare(strict_types=1);

namespace App\Core;

use RuntimeException;

class Auth
{
    public static function startSecureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', '1');
            ini_set('session.cookie_secure', APP_ENV === 'production' ? '1' : '0');
            ini_set('session.use_strict_mode', '1');
            ini_set('session.cookie_samesite', 'Strict');
            
            session_name(SESSION_NAME ?? 'norav4_session');
            session_start();
            
            // Regenerate on privilege change
            if (!isset($_SESSION['created'])) {
                $_SESSION['created'] = time();
            } elseif (time() - $_SESSION['created'] > SESSION_LIFETIME) {
                session_regenerate_id(true);
                $_SESSION['created'] = time();
            }
        }
    }

    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public static function checkRole(string $role): bool
    {
        return self::check() && ($_SESSION['role'] === $role);
    }

    public static function login(int $userId, string $username, string $role): void
    {
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;
        $_SESSION['login_time'] = time();
        session_regenerate_id(true);
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();
    }

    public static function user(): ?array
    {
        return self::check() ? [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'role' => $_SESSION['role']
        ] : null;
    }
}

