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
            header('Location: ' . APP_URL . '/index.php?gate=dashboard');
            exit;
        }
        $auth = $this;
        require VIEWS_PATH . '/auth/login.php';
    }

    /**
     * Handle login
     */
    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        // Verify CSRF token
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            logSecurityEvent('CSRF_VALIDATION_FAILED', [
                'action' => 'login',
                'username' => $_POST['username'] ?? 'unknown'
            ]);
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'CSRF token invalid']);
            return;
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Username dan password wajib diisi']);
            return;
        }

        $user = $this->userModel->findByUsername($username);

        if (!$user || !$this->userModel->verifyPassword($password, $user['password_hash'])) {
            // Log failed attempt
            logSecurityEvent('LOGIN_FAILED', [
                'username' => $username,
                'ip' => getClientIP()
            ]);
            
            // Rate limit check
            if (!checkRateLimit('login_failed', 5, 300)) { // 5 attempts per 5 minutes
                logSecurityEvent('LOGIN_RATE_LIMIT_EXCEEDED', [
                    'username' => $username,
                    'ip' => getClientIP()
                ]);
                http_response_code(429);
                echo json_encode(['success' => false, 'message' => 'Terlalu banyak percobaan. Silakan tunggu 5 menit.']);
                return;
            }
            
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Username atau password salah']);
            return;
        }

        // Check rate limit for successful logins too
        if (!checkRateLimit('login_success', 10, 60)) { // 10 logins per minute
            logSecurityEvent('LOGIN_RATE_LIMIT_EXCEEDED', [
                'username' => $username,
                'ip' => getClientIP()
            ]);
            http_response_code(429);
            echo json_encode(['success' => false, 'message' => 'Terlalu banyak percobaan.']);
            return;
        }

        // Transition Mapping: Translate legacy DB roles to new Norav2 roles
        $effectiveRole = $user['role'];
        if (in_array($effectiveRole, ['notaris', 'role_owner', 'role_trusted', 'trusted'])) {
            $effectiveRole = ROLE_OWNER;
        } elseif (in_array($effectiveRole, ['admin', 'role_staff'])) {
            $effectiveRole = ROLE_STAFF;
        }

        // Create secure session (with name)
        createSecureSession($user['id'], $user['username'], $effectiveRole, $user['name'] ?? $user['username']);

        // Log audit
        $this->auditLogModel->create(
            $user['id'],
            $effectiveRole,
            AUDIT_LOGIN,
            null,
            null,
            json_encode(['ip' => getClientIP()])
        );

        echo json_encode([
            'success' => true,
            'message' => 'Login berhasil',
            'redirect' => APP_URL . '/index.php?gate=dashboard'
        ]);
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
        header('Location: ' . APP_URL . '/index.php?gate=login');
        exit;
    }

    /**
     * Check if user is authenticated
     */
    public function isAuthenticated(): bool {
        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if session exists and user is logged in
        if (!isset($_SESSION[SESSION_NAME])) {
            return false;
        }
        
        if (!isset($_SESSION[SESSION_NAME]['logged_in']) || $_SESSION[SESSION_NAME]['logged_in'] !== true) {
            return false;
        }

        // Check session lifetime
        if (!isset($_SESSION[SESSION_NAME]['login_time'])) {
            return false;
        }
        
        if (time() - $_SESSION[SESSION_NAME]['login_time'] > SESSION_LIFETIME) {
            // Session expired
            $this->logout();
            return false;
        }

        return true;
    }

    /**
     * Get current user
     */
    public function getCurrentUser(): ?array {
        if (!$this->isAuthenticated()) {
            return null;
        }

        // Don't start session if already started (prevents double session_start error)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $this->userModel->findById($_SESSION[SESSION_NAME]['user_id']);
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
     */
    public function requireAuth(): void {
        if (!$this->isAuthenticated()) {
            if ($this->isAjaxRequest()) {
                http_response_code(401);
                echo json_encode([
                    'success' => false, 
                    'message' => 'Session expired or unauthorized access. Please login again.',
                    'code' => 'SESSION_EXPIRED'
                ]);
                exit;
            }
            header('Location: ' . APP_URL . '/index.php?gate=login&expired=1');
            exit;
        }
    }

    /**
     * Require specific role
     * For dashboard pages - redirects to login on session expired
     */
    public function requireRole(string $role): void {
        $this->requireAuth();

        if (!$this->hasRole($role)) {
            if ($this->isAjaxRequest()) {
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
     */
    public function generateCSRFToken(): string {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }

        return $_SESSION[CSRF_TOKEN_NAME];
    }

    /**
     * Verify CSRF token
     */
    public function verifyCSRFToken(string $token): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
    }

    /**
     * Check if request is AJAX
     */
    private function isAjaxRequest(): bool {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
