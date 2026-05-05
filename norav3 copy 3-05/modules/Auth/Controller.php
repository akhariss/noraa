<?php
declare(strict_types=1);

namespace Modules\Auth;

/**
 * SK-14: AuthController
 * Slim controller for authentication
 */

use App\Domain\Entities\User;
use App\Domain\Entities\AuditLog;

class Controller {
    private User $userModel;
    private AuditLog $auditLogModel;

    public function __construct() {
        $this->userModel = new User();
        $this->auditLogModel = new AuditLog();
    }

    /**
     * Show login page
     */
    public function showLogin(): void {
        // Don't redirect - let the router handle it
        // Just return and let the view be rendered
    }

    /**
     * Show login page (called by Router)
     * Handles redirect if already authenticated.
     */
    public function showLoginPage(): void {
        if ($this->isAuthenticated()) {
            header('Location: index.php?gate=registrasi');
            exit;
        }
        $auth = $this;
        require VIEWS_PATH . '/auth/login.php';
    }

    /**
     * Redirect /office to login page
     */
    public function redirectToLogin(): void {
        header('Location: index.php?gate=login');
        exit;
    }

    /**
     * Handle login
     */
    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?gate=login');
            exit;
        }

        // Verify CSRF token
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            logSecurityEvent('CSRF_VALIDATION_FAILED', [
                'action' => 'login',
                'username' => $_POST['username'] ?? 'unknown'
            ]);
            $_SESSION['login_error'] = 'CSRF token invalid. Silakan coba lagi.';
            header('Location: index.php?gate=login');
            exit;
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $_SESSION['login_error'] = 'Username dan password wajib diisi';
            header('Location: index.php?gate=login');
            exit;
        }

        try {
            $user = $this->userModel->findByUsername($username);

            if (!$user || !$this->userModel->verifyPassword($password, $user['password_hash'])) {
                // Log failed attempt
                logSecurityEvent('LOGIN_FAILED', [
                    'username' => $username,
                    'ip' => getClientIP()
                ]);

                // Rate limit check (5 attempts per 5 minutes)
                if (!\App\Security\RateLimiter::checkGlobal('login')) {
                    logSecurityEvent('LOGIN_RATE_LIMIT_EXCEEDED', [
                        'username' => $username,
                        'ip' => getClientIP()
                    ]);
                    $_SESSION['login_error'] = 'Terlalu banyak percobaan. Silakan tunggu 5 menit.';
                    header('Location: index.php?gate=login');
                    exit;
                }

                $_SESSION['login_error'] = 'Username atau password salah';
                header('Location: index.php?gate=login');
                exit;
            }

            // Check rate limit for successful logins too (10 logins per minute)
            if (!\App\Security\RateLimiter::checkGlobal('login_success')) {
                logSecurityEvent('LOGIN_RATE_LIMIT_EXCEEDED', [
                    'username' => $username,
                    'ip' => getClientIP()
                ]);
                $_SESSION['login_error'] = 'Terlalu banyak percobaan.';
                header('Location: index.php?gate=login');
                exit;
            }

            // Normalize role from DB to application constants
            $effectiveRole = $user['role'];
            if ($effectiveRole === 'administrator') {
                $effectiveRole = ROLE_OWNER;
            } elseif ($effectiveRole === 'staff') {
                $effectiveRole = ROLE_STAFF;
            }

            // Create secure session via Auth class (unified)
            \App\Security\Auth::loginUser(
                (int)$user['id'],
                $user['username'],
                $effectiveRole,
                $user['name'] ?? $user['username']
            );

            // Log audit (non-fatal if fails)
            try {
                $this->auditLogModel->create(
                    $user['id'],
                    $effectiveRole,
                    AUDIT_LOGIN,
                    null,
                    null,
                    json_encode(['ip' => getClientIP()])
                );
            } catch (\Throwable $auditError) {
                error_log('Audit log failed (non-fatal): ' . $auditError->getMessage());
            }

            // Success - redirect to dashboard
            header('Location: index.php?gate=registrasi');
            exit;
        } catch (\Throwable $e) {
            error_log('Login error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            $_SESSION['login_error'] = 'Terjadi kesalahan server. Silakan coba lagi.';
            header('Location: index.php?gate=login');
            exit;
        }
    }

    /**
     * Handle logout
     */
    public function logout(): void {
        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Log audit before destroying session
        if (isset($_SESSION[SESSION_NAME])) {
            $this->auditLogModel->create(
                $_SESSION[SESSION_NAME]['user_id'],
                $_SESSION[SESSION_NAME]['role'],
                AUDIT_LOGOUT,
                null,
                null,
                null
            );
        }
        
        // Unset all session variables
        $_SESSION = [];
        
        // Delete session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        // Destroy session
        session_destroy();
        
        // Redirect to login immediately
        header('Location: index.php?gate=login');
        exit;
    }

    /**
     * Check if user is authenticated
     * G-06: Consolidated to use helper function to avoid duplication
     */
    public function isAuthenticated(): bool {
        return isLoggedIn();
    }

    /**
     * Get current user
     * G-06: Consolidated to use helper function
     */
    public function getCurrentUser(): ?array {
        return getCurrentUser();
    }

    /**
     * Get current user role
     */
    public function getCurrentRole(): ?string {
        if (!$this->isAuthenticated()) {
            return null;
        }

        // Don't start session if already started (prevents double session_start error)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION[SESSION_NAME]['role'];
    }

    /**
     * Check if user has role
     */
    public function hasRole(string $role): bool {
        return $this->getCurrentRole() === $role;
    }

    /**
     * Require authentication
     * G-06: Consolidated to use helper function to avoid duplication
     */
    public function requireAuth(): void {
        requireAuth();
    }

    /**
     * Require specific role
     * For dashboard pages - redirects to login on session expired
     * G-06: Uses consolidated requireAuth helper function
     */
    public function requireRole(string $role): void {
        requireAuth();

        if (!$this->hasRole($role)) {
            if (isAjaxRequest()) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Forbidden']);
                exit;
            }
            // For non-AJAX requests, redirect to login
            redirectToLogin();
            exit;
        }
    }

    /**
     * Generate CSRF token
     * G-07: Consolidated to use helper function to avoid duplication
     */
    public function generateCSRFToken(): string {
        return generateCSRFToken();
    }

    /**
     * Verify CSRF token
     * G-07: Consolidated to use helper function to avoid duplication
     */
    public function verifyCSRFToken(string $token): bool {
        return verifyCSRFToken($token);
    }
}
