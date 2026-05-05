<?php
declare(strict_types=1);

/**
 * Global Helpers for Nora V4
 */

use App\Core\Auth;

if (!function_exists('getCurrentUser')) {
    function getCurrentUser(): ?array
    {
        return Auth::user();
    }
}

if (!function_exists('requireRole')) {
    function requireRole(string $role): void
    {
        if (!Auth::checkRole($role)) {
            http_response_code(403);
            die('Access Denied');
        }
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path): void
    {
        header('Location: ' . APP_URL . $path);
        exit;
    }
}

if (!function_exists('jsonResponse')) {
    function jsonResponse(array $data, int $code = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data);
        exit;
    }
}

/**
 * Ported from V3: Encrypt image filename for URL
 */
function encryptImageId($filename) {
    if (empty($filename)) return '';
    $key = SECURITY_KEY_IMG;
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted = openssl_encrypt($filename, 'aes-256-cbc', hash('sha256', $key, true), 0, $iv);
    return base64_encode($iv . $encrypted);
}

/**
 * Ported from V3: Decrypt image filename from URL
 */
function decryptImageId($token) {
    if (empty($token)) return false;
    $key = SECURITY_KEY_IMG;
    $data = base64_decode($token);
    $ivLen = openssl_cipher_iv_length('aes-256-cbc');
    if (strlen($data) <= $ivLen) return false;

    $iv = substr($data, 0, $ivLen);
    $encrypted = substr($data, $ivLen);
    return openssl_decrypt($encrypted, 'aes-256-cbc', hash('sha256', $key, true), 0, $iv);
}

/**
 * Returns UI style array for a given workflow role
 */
function getStatusStyle(int $role): array {
    return match($role) {
        0 => ['bg' => '#e3f2fd', 'color' => '#1976d2', 'border' => '#90caf9'],
        1 => ['bg' => '#fff3e0', 'color' => '#f57c00', 'border' => '#ffcc80'],
        2 => ['bg' => '#f3e5f5', 'color' => '#7b1fa2', 'border' => '#ce93d8'],
        3 => ['bg' => '#e8f5e9', 'color' => '#388e3c', 'border' => '#a5d6a7'],
        4 => ['bg' => '#fff8e1', 'color' => '#fbc02d', 'border' => '#ffe082'],
        5 => ['bg' => '#e8f5e9', 'color' => '#2e7d32', 'border' => '#a5d6a7'],
        6 => ['bg' => '#eceff1', 'color' => '#546e7a', 'border' => '#b0bec5'],
        7 => ['bg' => '#ffebee', 'color' => '#c62828', 'border' => '#ef9a9a'],
        8 => ['bg' => '#fff3e0', 'color' => '#f57c00', 'border' => '#ffcc80'],
        default => ['bg' => '#f5f5f5', 'color' => '#757575', 'border' => '#e0e0e0']
    };
}
