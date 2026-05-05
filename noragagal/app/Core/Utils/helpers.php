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
 * Sanitize phone number for WhatsApp API link
 * Removes +, spaces, dashes - returns pure digits
 * e.g., "+62877-4877-8885" → "6287748778885"
 */
function sanitizePhoneForWa($phone): string {
    return preg_replace('/[+\s\-]/', '', $phone);
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
        \App\Security\Auth::startSecureSession();
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
        \App\Security\Auth::startSecureSession();
    }
    
    return \App\Security\Auth::generateCSRFToken();
}

/**
 * Rate limiting (deprecated - delegates to RateLimiter class)
 * @deprecated Use \App\Security\RateLimiter::checkGlobal() directly
 */
function checkRateLimit($endpoint, $limit = 5, $window = 1): bool {
    // Map legacy endpoint names to RateLimiter types
    $typeMap = [
        'login_failed'    => 'login',
        'login_success'   => 'login_success',
        'tracking_search' => 'tracking_search',
        'tracking_verify' => 'tracking_verify',
    ];
    $type = $typeMap[$endpoint] ?? 'global';
    return \App\Security\RateLimiter::checkGlobal($type);
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
 * SK-01: Pretty URL Generator
 * Generates SEO-friendly URLs.
 * Public: APP_URL/home, APP_URL/lacak
 * Office: APP_URL/office/dashboard, APP_URL/office/users
 */
function url(string $path = '', array $params = []): string {
    $path = trim($path, '/');
    
    // Determine if this is an admin/auth route from config/routes.php
    // In a real app, we might check a registry, but here we can check 
    // common admin keywords or just use a prefix if provided.
    
    $adminGates = [
        'login', 'dashboard', 'registrasi', 'registrasi_create', 'registrasi_detail', 
        'finalisasi', 'users', 'cms_editor', 'backups', 'audit', 'logout',
        'update_status', 'update_klien', 'toggle_kendala', 'toggle_lock'
    ];

    $isOffice = in_array($path, $adminGates) || strpos($path, 'cms_') === 0;
    
    $prefix = $isOffice ? '/office/' : '/';
    if ($path === 'home' && !$isOffice) {
        $path = ''; // Root URL for home
    }

    $url = rtrim(APP_URL, '/') . $prefix . $path;

    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }

    return $url;
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
 * Delegates to Auth class for unified auth system
 */
function isLoggedIn(): bool {
    return \App\Security\Auth::isLoggedIn();
}

/**
 * Get current user
 * Delegates to Auth class for unified auth system
 */
function getCurrentUser(): ?array {
    return \App\Security\Auth::getSession();
}

/**
 * Require authentication
 * Delegates to Auth class for unified auth system
 */
function requireAuth(): void {
    \App\Security\Auth::requireAuth();
}

/**
 * Redirect to login page with expired flag
 * Delegates to Auth class for session destruction
 */
function redirectToLogin(): void {
    // Destroy session via Auth class
    \App\Security\Auth::destroySession();

    // Redirect to login with expired flag
    header('Location: ' . url('login', ['expired' => 1]));
    exit;
}

/**
 * Require specific role
 * For dashboard pages - redirects to login on session expired
 * Supports single role or array of roles
 */
function requireRole($role): void {
    // Delegate auth check to Auth class
    \App\Security\Auth::requireAuth();

    $user = \App\Security\Auth::getSession();
    if (!$user || !isset($user['role'])) {
        if (isAjaxRequest()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Forbidden']);
            exit;
        }
        redirectToLogin();
        exit;
    }

    // Hierarchical role check: support single or array of allowed roles
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

/**
 * ═══════════════════════════════════════════════════════════════
 * WORKFLOW STEP HELPERS — Single source of truth from database
 * Replaces hardcoded STATUS_WORKFLOW, STATUS_LABELS, STATUS_ORDER, STATUS_ESTIMASI
 * ═══════════════════════════════════════════════════════════════
 */

/**
 * Get all workflow step labels: ['step_key' => 'label', ...]
 * Cached to avoid repeated queries.
 */
function getStatusLabels(): array {
    static $cache = null;
    if ($cache === null) {
        $steps = \App\Adapters\Database::select(
            "SELECT step_key, label FROM workflow_steps ORDER BY sort_order ASC"
        );
        $cache = [];
        foreach ($steps as $s) {
            $cache[$s['step_key']] = $s['label'];
        }
    }
    return $cache;
}

/**
 * Get workflow step sort order: ['step_key' => order, ...]
 */
function getStatusOrder(): array {
    static $cache = null;
    if ($cache === null) {
        $steps = \App\Adapters\Database::select(
            "SELECT step_key, sort_order FROM workflow_steps ORDER BY sort_order ASC"
        );
        $cache = [];
        foreach ($steps as $s) {
            $cache[$s['step_key']] = (int)$s['sort_order'];
        }
    }
    return $cache;
}

/**
 * Get workflow step SLA days: ['step_key' => sla_days, ...]
 */
function getStatusEstimasi(): array {
    static $cache = null;
    if ($cache === null) {
        $steps = \App\Adapters\Database::select(
            "SELECT step_key, sla_days FROM workflow_steps ORDER BY sort_order ASC"
        );
        $cache = [];
        foreach ($steps as $s) {
            $cache[$s['step_key']] = (int)$s['sla_days'];
        }
    }
    return $cache;
}
