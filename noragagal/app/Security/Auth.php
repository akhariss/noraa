<?php
declare(strict_types=1);

namespace App\Security;

use App\Adapters\Logger;

/**
 * SK-04: Session & Auth
 * Secure session management with fingerprinting and anti-hijacking.
 */
class Auth
{
    // SESSION_NAME is defined in config/app.php
    private const SESSION_TIMEOUT = 28800; // 8 hours for better UX
    private const REGEN_INTERVAL = 14400;  // 4 hours against session fixation

    /**
     * Generate CSRF Token for form security.
     */
    public static function generateCSRFToken(): string {
        // Ensure session is started via Auth class if possible, or fallback
        if (session_status() === PHP_SESSION_NONE) {
            self::startSecureSession();
        }
        
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Start a secure session with hardened settings.
     */
    public static function startSecureSession(): void
    {
        if (session_status() !== PHP_SESSION_NONE) {
            return;
        }

        $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
                    (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

        ini_set('session.gc_maxlifetime', (string)self::SESSION_TIMEOUT);
        ini_set('session.cookie_lifetime', '0'); // Browser session length, let server enforce idle limit
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_secure', $isSecure ? '1' : '0'); 
        ini_set('session.use_strict_mode', '1');
        ini_set('session.cookie_samesite', 'Lax'); // Lax is more stable for subdirectory redirects

        session_start();

        // Check session timeout
        if (self::isLoggedIn()) {
            // 1. Path Identification for security context
            $urlPath = $_GET['url'] ?? 'home';
            $isOfficeRequest = (strpos($urlPath, 'office') === 0);
            $publicPaths = ['home', 'lacak', 'detail', 'verify_tracking', 'health', 'login', ''];
            $isPublicPath = in_array(trim($urlPath, '/'), $publicPaths, true);

            // 2. Strict Security for Office/Auth paths
            if ($isOfficeRequest || !$isPublicPath) {
                $lastActivity = $_SESSION[SESSION_NAME]['last_activity'] ?? time();

                if (time() - $lastActivity > self::SESSION_TIMEOUT) {
                    Logger::security('SESSION_TIMEOUT_LOGOUT', [
                        'user_id'  => $_SESSION[SESSION_NAME]['user_id'] ?? 'unknown',
                        'username' => $_SESSION[SESSION_NAME]['username'] ?? 'unknown',
                    ]);

                    self::destroySession();
                    if (self::isAjax()) {
                        http_response_code(401);
                        echo json_encode(['success' => false, 'message' => 'Sesi Anda telah berakhir. Silakan muat ulang halaman.']);
                    } else {
                        header('Location: ' . url('login', ['timeout' => 1]));
                    }
                    exit;
                }

                $_SESSION[SESSION_NAME]['last_activity'] = time();

                // 3. Advanced Fingerprint Validation (Anti-Hijacking)
                $currentFingerprint = self::generateFingerprint();
                if (isset($_SESSION[SESSION_NAME]['fingerprint'])) {
                    if (!hash_equals($_SESSION[SESSION_NAME]['fingerprint'], $currentFingerprint)) {
                        Logger::security('SESSION_FINGERPRINT_MISMATCH', [
                            'user_id' => $_SESSION[SESSION_NAME]['user_id'] ?? 'unknown',
                        ]);
                        self::destroySession();
                        if (self::isAjax()) {
                            http_response_code(401);
                            echo json_encode(['success' => false, 'message' => 'Sesi tidak valid. Silakan muat ulang halaman.']);
                        } else {
                            header('Location: ' . url('login', ['expired' => 1]));
                        }
                        exit;
                    }
                }
            }
        }

        // Force No-Cache for authenticated or office requests
        if (self::isLoggedIn() || (isset($_GET['url']) && strpos($_GET['url'], 'office') === 0)) {
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Pragma: no-cache');
        }


        // Periodic regeneration (anti-fixation)
        if (!isset($_SESSION['_created'])) {
            $_SESSION['_created'] = time();
        } elseif (time() - $_SESSION['_created'] > self::REGEN_INTERVAL) {
            // Only regenerate if it's NOT an ajax request to prevent crossing race conditions
            if (!self::isAjax()) {
                session_regenerate_id(true);
                $_SESSION['_created'] = time();

            // Update session hash after regeneration
            if (isset($_SESSION[SESSION_NAME])) {
                $_SESSION[SESSION_NAME]['session_hash'] = self::computeSessionHash(
                    $_SESSION[SESSION_NAME]['user_id'],
                    $_SESSION[SESSION_NAME]['role']
                );
            }
            }
        }
    }

    /**
     * Login user and create secure session.
     */
    public static function loginUser(int $userId, string $username, string $role, ?string $name = null): void
    {
        session_regenerate_id(true); // Mandatory: prevent fixation

        $_SESSION[SESSION_NAME] = [
            'user_id'       => $userId,
            'username'      => $username,
            'name'          => $name ?? $username,
            'role'          => $role,
            'logged_in'     => true,
            'login_time'    => time(),
            'last_activity' => time(),
            'ip_address'    => self::getClientIP(),
            'user_agent'    => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'fingerprint'   => self::generateFingerprint(),
            'session_hash'  => self::computeSessionHash($userId, $role),
        ];
    }

    /**
     * Check if user is logged in.
     */
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION[SESSION_NAME]['logged_in'])
            && $_SESSION[SESSION_NAME]['logged_in'] === true;
    }

    /**
     * Get current session data.
     */
    public static function getSession(): ?array
    {
        return $_SESSION[SESSION_NAME] ?? null;
    }

    /**
     * Get session value by key.
     */
    public static function get(string $key): mixed
    {
        return $_SESSION[SESSION_NAME][$key] ?? null;
    }

    /**
     * Require authentication. Redirects or returns 401.
     */
    public static function requireAuth(): void
    {
        if (!self::isLoggedIn()) {
            if (self::isAjax()) {
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'message' => 'Session expired. Please login again.',
                    'code' => 'SESSION_EXPIRED',
                ]);
                exit;
            }
            self::destroySession();
            header('Location: ' . url('login', ['expired' => 1]));
            exit;
        }
    }

    /**
     * Verify user role with session hash validation.
     */
    public static function verifyRole(string $requiredRole): bool
    {
        if (!self::isLoggedIn()) {
            return false;
        }

        $session = $_SESSION[SESSION_NAME];

        if (($session['role'] ?? '') !== $requiredRole) {
            return false;
        }

        // Verify session hash hasn't been tampered
        if (!isset($session['session_hash'])) {
            return false;
        }

        $expected = self::computeSessionHash($session['user_id'], (string)$session['role']);
        if (!hash_equals($expected, $session['session_hash'])) {
            Logger::security('SESSION_TAMPERING_DETECTED', [
                'user_id' => $session['user_id'],
            ]);
            return false;
        }

        return true;
    }

    /**
     * Destroy session completely.
     */
    public static function destroySession(): void
    {
        $_SESSION = [];

        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    /**
     * Refresh session activity timestamp.
     */
    public static function refreshActivity(): void
    {
        if (self::isLoggedIn()) {
            $_SESSION[SESSION_NAME]['last_activity'] = time();
        }
    }

    /**
     * Generate browser fingerprint for anti-hijacking.
     */
    private static function generateFingerprint(): string
    {
        $ua   = $_SERVER['HTTP_USER_AGENT'] ?? 'none';
        $lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'none';
        $ip   = self::getClientIP();
        
        // Stabilize for localhost development (handle ::1 and 127.0.0.1)
        $isLocal = ($ip === '::1' || $ip === '127.0.0.1' || strpos($ip, '192.168.') === 0);
        $ipSegment = $isLocal ? 'localhost' : 
                     ((strpos($ip, '.') !== false) ? substr($ip, 0, strrpos($ip, '.')) : $ip);
        
        // Use SECURITY_KEY_ID from config/app.php as a secret salt
        $salt = defined('SECURITY_KEY_ID') ? SECURITY_KEY_ID : 'nora_fallback_salt';
        
        return hash('sha256', $ua . $lang . $ipSegment . $salt);
    }

    /**
     * Compute session hash for tamper detection.
     */
    private static function computeSessionHash(int|string $userId, string $role): string
    {
        $salt = defined('SECURITY_KEY_ID') ? SECURITY_KEY_ID : 'nora_session_salt';
        return hash('sha256', (string)$userId . $role . session_id() . $salt);
    }

    /**
     * Check if request is AJAX.
     */
    private static function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Get client IP address.
     */
    private static function getClientIP(): string
    {
        $keys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
        foreach ($keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                if (strpos($ip, ',') !== false) {
                    $ip = explode(',', $ip)[0];
                }
                return trim($ip);
            }
        }
        return 'unknown';
    }

    /**
     * Get the session key constant (for backward compatibility).
     */
    public static function getSessionName(): string
    {
        return SESSION_NAME;
    }
}
