<?php
/**
 * Security Helper Functions
 * ONLY functions NOT in helpers.php
 */

/**
 * Regenerate CSRF token (for sensitive operations)
 * NOTE: generateCSRFToken() already in helpers.php
 */
function regenerateCSRFToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}

/**
 * Log security event (audit trail)
 */
function logSecurityEvent($eventType, $data = []) {
    $logFile = LOGS_PATH . '/security.log';
    $timestamp = date('Y-m-d H:i:s');
    $ip = getClientIP();
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    
    $sessionId = session_id();
    $userId = isset($_SESSION[SESSION_NAME]) ? $_SESSION[SESSION_NAME]['user_id'] : 'guest';
    $userRole = isset($_SESSION[SESSION_NAME]) ? $_SESSION[SESSION_NAME]['role'] : 'guest';
    
    $logEntry = sprintf(
        "[%s] [IP: %s] [User: %s/%s] [Session: %s] [%s] %s %s\n",
        $timestamp,
        $ip,
        $userId,
        $userRole,
        $sessionId,
        $eventType,
        json_encode($data),
        $userAgent
    );
    
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}

/**
 * Security headers (anti-clickjacking, XSS protection)
 * Pillar 6.1: Hardened Headers & CSP
 */
function sendSecurityHeaders() {
    // Prevent clickjacking
    header('X-Frame-Options: DENY');

    // XSS protection
    header('X-XSS-Protection: 1; mode=block');

    // Prevent MIME type sniffing
    header('X-Content-Type-Options: nosniff');

    // Referrer policy
    header('Referrer-Policy: strict-origin-when-cross-origin');

    // Pillar 6.1: HSTS (HTTPS only)
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }

    // Pillar 6.1: Content Security Policy
    $csp = "default-src 'self'; " .
           "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; " .
           "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; " .
           "font-src 'self' data: https://fonts.gstatic.com; " .
           "img-src 'self' data: https:; " .
           "connect-src 'self' https:; " .
           "frame-ancestors 'none';";

    header("Content-Security-Policy: " . $csp);
    
    // Additional security headers
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
}

/**
 * Verify user role with session validation (anti-session hijacking)
 * For AJAX/API requests - returns JSON error
 */
function verifyUserRole($requiredRole) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check if session exists
    if (!isset($_SESSION[SESSION_NAME])) {
        return false;
    }

    // Check if user is logged in
    if (!isset($_SESSION[SESSION_NAME]['logged_in']) ||
        $_SESSION[SESSION_NAME]['logged_in'] !== true) {
        return false;
    }

    // Verify role
    if (!isset($_SESSION[SESSION_NAME]['role']) ||
        $_SESSION[SESSION_NAME]['role'] !== $requiredRole) {
        return false;
    }

    // Verify session hasn't been tampered with
    if (!isset($_SESSION[SESSION_NAME]['session_hash'])) {
        return false;
    }

    // Verify session hash
    $expectedHash = hash('sha256',
        $_SESSION[SESSION_NAME]['user_id'] .
        $_SESSION[SESSION_NAME]['role'] .
        session_id()
    );

    if ($_SESSION[SESSION_NAME]['session_hash'] !== $expectedHash) {
        // Session tampering detected!
        logSecurityEvent('SESSION_TAMPERING_DETECTED', [
            'user_id' => $_SESSION[SESSION_NAME]['user_id'],
            'expected_hash' => $expectedHash,
            'actual_hash' => $_SESSION[SESSION_NAME]['session_hash']
        ]);
        return false;
    }

    return true;
}

/**
 * Create secure session with hash verification
 * Delegates to Auth::loginUser() for unified auth system
 */
function createSecureSession($userId, $username, $role, $name = null) {
    \App\Security\Auth::loginUser((int)$userId, $username, $role);
}
