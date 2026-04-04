<?php
/**
 * Helper Functions
 * Security, validation, and utility functions
 */

/**
 * Sanitize input string
 */
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Sanitize output for HTML
 */
function e($string): string {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email
 */
function isValidEmail($email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone number (Indonesian format)
 */
function isValidPhone($phone): bool {
    // Remove spaces and dashes
    $phone = preg_replace('/[\s\-]/', '', $phone);
    // Check if it matches Indonesian phone format
    return preg_match('/^(\+62|62|0)8[1-9][0-9]{7,11}$/', $phone);
}

/**
 * Generate random string
 */
function generateRandomString($length = 32): string {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Generate secure token
 */
function generateToken(): string {
    return bin2hex(random_bytes(32));
}

/**
 * Hash password
 */
function hashPassword($password): string {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Verify password
 */
function verifyPassword($password, $hash): bool {
    return password_verify($password, $hash);
}

/**
 * Check CSRF token
 */
function verifyCSRFToken($token): bool {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        return false;
    }
    
    return hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

/**
 * Generate CSRF token
 */
function generateCSRFToken(): string {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Rate limiting
 */
function checkRateLimit($endpoint, $limit = 5, $window = 1): bool {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $key = 'rate_limit:' . $ip . ':' . $endpoint;
    
    // Simple file-based rate limiting (for production, use Redis/Memcached)
    $file = sys_get_temp_dir() . '/' . md5($key);
    $now = time();
    
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        if ($data && ($now - $data['timestamp']) < $window) {
            if ($data['count'] >= $limit) {
                return false; // Rate limit exceeded
            }
            $data['count']++;
            file_put_contents($file, json_encode($data));
            return true;
        }
    }
    
    // Reset counter
    file_put_contents($file, json_encode(['count' => 1, 'timestamp' => $now]));
    return true;
}

/**
 * Check if request is AJAX
 */
function isAjaxRequest(): bool {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Get client IP address
 */
function getClientIP(): string {
    $ipKeys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
    
    foreach ($ipKeys as $key) {
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
 * Redirect with message
 */
function redirect($url, $message = null, $type = 'info') {
    if ($message) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
    }
    
    header('Location: ' . $url);
    exit;
}

/**
 * Get flash message
 */
function getFlashMessage(): ?array {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';
        
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        
        return ['message' => $message, 'type' => $type];
    }
    
    return null;
}

/**
 * Format date to Indonesian
 */
function formatDateID($date): string {
    $months = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $timestamp = strtotime($date);
    $day = date('j', $timestamp);
    $month = $months[(int)date('n', $timestamp)];
    $year = date('Y', $timestamp);
    
    return "$day $month $year";
}

/**
 * Format datetime to Indonesian
 */
function formatDateTimeID($date): string {
    return formatDateID($date) . ' ' . date('H:i', strtotime($date));
}

/**
 * Format file size
 */
function formatFileSize($bytes): string {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    
    return round($bytes, 2) . ' ' . $units[$pow];
}

/**
 * Log error to file
 */
function logError($message, $context = []): void {
    $logFile = LOGS_PATH . '/error.log';
    $timestamp = date('Y-m-d H:i:s');
    $ip = getClientIP();
    
    $logEntry = sprintf(
        "[%s] [%s] %s %s\n",
        $timestamp,
        $ip,
        $message,
        !empty($context) ? ' Context: ' . json_encode($context) : ''
    );
    
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}

/**
 * Log audit trail
 */
function logAudit($userId, $role, $action, $registrasiId = null, $oldValue = null, $newValue = null): void {
    $logFile = LOGS_PATH . '/audit.log';
    $timestamp = date('Y-m-d H:i:s');
    $ip = getClientIP();
    
    $logEntry = sprintf(
        "[%s] [%s] User: %s (%s) | Action: %s | registrasi: %s | Old: %s | New: %s\n",
        $timestamp,
        $ip,
        $userId,
        $role,
        $action,
        $registrasiId ?? '-',
        $oldValue ? json_encode($oldValue) : '-',
        $newValue ? json_encode($newValue) : '-'
    );
    
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}

/**
 * Check if user is logged in
 */
function isLoggedIn(): bool {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return isset($_SESSION[SESSION_NAME]) && $_SESSION[SESSION_NAME]['logged_in'] === true;
}

/**
 * Get current user
 */
function getCurrentUser(): ?array {
    if (!isLoggedIn()) {
        return null;
    }
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return $_SESSION[SESSION_NAME] ?? null;
}

/**
 * Require authentication
 */
function requireAuth(): void {
    // DEBUG
    error_log("requireAuth called");
    error_log("isLoggedIn: " . (isLoggedIn() ? 'YES' : 'NO'));
    
    if (!isLoggedIn()) {
        error_log("requireAuth: NOT logged in, redirecting");
        if (isAjaxRequest()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        redirectToLogin();
    }
    error_log("requireAuth: OK");
}

/**
 * Redirect to login page with expired flag
 * Central function for all session expired redirects
 */
function redirectToLogin(): void {
    // Destroy current session
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
    
    // Redirect to login with expired flag
    header('Location: ' . APP_URL . '/index.php?gate=login&expired=1');
    exit;
}

/**
 * Require specific role
 * For dashboard pages - redirects to login on session expired
 */
function requireRole($role): void {
    // Ensure session is started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    requireAuth();

    $user = getCurrentUser();
    if (!$user || !isset($user['role'])) {
        if (isAjaxRequest()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Forbidden']);
            exit;
        }
        redirectToLogin();
        exit;
    }

    // Hierarchical role check:
    // Only Administrator (ROLE_OWNER) and Staff (ROLE_STAFF) are supported now.
    $allowedRoles = is_array($role) ? $role : [$role];
    

    if (!in_array($user['role'], $allowedRoles)) {
        if (isAjaxRequest()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Forbidden']);
            exit;
        }
        redirectToLogin();
        exit;
    }
}

/**
 * JSON response helper
 */
function jsonResponse($data, $statusCode = 200): void {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Validate required fields
 */
function validateRequired($data, $fields): array {
    $errors = [];
    
    foreach ($fields as $field) {
        if (empty($data[$field])) {
            $errors[] = ucfirst($field) . ' is required';
        }
    }
    
    return $errors;
}

/**
 * Slugify string
 */
function slugify($text): string {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);

    return empty($text) ? 'n-a' : $text;
}

/**
 * Show error page
 */
function showErrorPage(int $statusCode, string $title, string $message, array $data = []): void {
    http_response_code($statusCode);
    
    extract(array_merge([
        'statusCode' => $statusCode,
        'title' => $title,
        'message' => $message,
        'showBackButton' => true,
        'isLoggedIn' => isLoggedIn()
    ], $data));
    
    $errorPage = VIEWS_PATH . '/errors/' . $statusCode . '.php';
    if (file_exists($errorPage)) {
        require $errorPage;
    } else {
        // Fallback generic error page
        require VIEWS_PATH . '/errors/error.php';
    }
    exit;
}
