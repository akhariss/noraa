<?php
/**
 * Security Helper Functions
 * Encryption, hashing, and security utilities
 */

/**
 * Encrypt ID with hash (secure, non-reversible without key)
 */
function encryptId($id, $salt = 'notaris_ppat_2026') {
    $key = defined('SECURITY_KEY_ID') ? SECURITY_KEY_ID : 'fallback_key_must_change';
    return hash_hmac('sha256', $id . $salt, $key);
}

/**
 * Encrypt image filename for URL (Reversible)
 */
function encryptImageId($filename) {
    if (empty($filename)) return '';
    $key = defined('SECURITY_KEY_IMG') ? SECURITY_KEY_IMG : 'fallback_img_key_must_change';
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted = openssl_encrypt($filename, 'aes-256-cbc', hash('sha256', $key, true), 0, $iv);
    return base64_encode($iv . $encrypted);
}

/**
 * Decrypt image filename from URL
 */
function decryptImageId($token) {
    if (empty($token)) return false;
    $key = defined('SECURITY_KEY_IMG') ? SECURITY_KEY_IMG : 'fallback_img_key_must_change';
    $data = base64_decode($token);
    $ivLen = openssl_cipher_iv_length('aes-256-cbc');
    if (strlen($data) <= $ivLen) return false;

    $iv = substr($data, 0, $ivLen);
    $encrypted = substr($data, $ivLen);
    return openssl_decrypt($encrypted, 'aes-256-cbc', hash('sha256', $key, true), 0, $iv);
}

/**
 * Verify encrypted ID
 */
function verifyEncryptedId($id, $encryptedId, $salt = 'notaris_ppat_2026') {
    $expected = encryptId($id, $salt);
    return hash_equals($expected, $encryptedId);
}

/**
 * Generate secure tracking token (ID + verification code)
 */
function generateTrackingToken($registrasiId, $verificationCode) {
    $data = [
        'id' => $registrasiId,
        'code' => $verificationCode,
        'time' => time()
    ];
    
    // Encode data
    $encoded = base64_encode(json_encode($data));
    
    // Add HMAC signature
    $key = defined('SECURITY_KEY_TRACKING') ? SECURITY_KEY_TRACKING : 'fallback_tracking_key_must_change';
    $signature = hash_hmac('sha256', $encoded, $key);
    
    return $encoded . '.' . $signature;
}

/**
 * Verify and decode tracking token
 */
function verifyTrackingToken($token) {
        // Split token and signature
        $parts = explode('.', $token);
        if (count($parts) !== 2) {
            logSecurityEvent('TOKEN_FORMAT_ERROR', ['count' => count($parts)]);
            return false;
        }
        
        $encoded = $parts[0];
        $signature = $parts[1];
        
        // Verify signature
        $key = defined('SECURITY_KEY_TRACKING') ? SECURITY_KEY_TRACKING : 'fallback_tracking_key_must_change';
        $expectedSignature = hash_hmac('sha256', $encoded, $key);
        
        if (!hash_equals($expectedSignature, $signature)) {
            logSecurityEvent('TOKEN_SIG_MISMATCH', [
                'expected' => substr($expectedSignature, 0, 8),
                'actual' => substr($signature, 0, 8)
            ]);
            return false;
        }
        
        // Decode data
        $data = json_decode(base64_decode($encoded), true);
        
        if (!$data || !isset($data['id']) || !isset($data['code'])) {
            logSecurityEvent('TOKEN_DATA_ERROR', ['data' => $data]);
            return false;
        }
        
        return $data;
}

/**
 * Generate short hash for URL (8 characters)
 */
function generateShortHash($id, $salt = 'short_hash_salt') {
    $key = defined('SECURITY_KEY_SHORT') ? SECURITY_KEY_SHORT : 'fallback_short_key_must_change';
    return substr(hash_hmac('md5', $id . $salt, $key), 0, 8);
}

/**
 * Verify short hash
 */
function verifyShortHash($id, $hash, $salt = 'short_hash_salt') {
    $expected = generateShortHash($id, $salt);
    return hash_equals($expected, $hash);
}

/**
 * Sanitize input for security
 */
function secureSanitize($input) {
    if (is_array($input)) {
        return array_map('secureSanitize', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF token
 */
function generateSecureCSRFToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifySecureCSRFToken($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Rate limiting with file storage
 */
function secureRateLimit($identifier, $limit = 5, $window = 60) {
    $file = sys_get_temp_dir() . '/rate_limit_' . md5($identifier);
    $now = time();
    
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        if ($data && ($now - $data['time']) < $window) {
            if ($data['count'] >= $limit) {
                return false;
            }
            $data['count']++;
            file_put_contents($file, json_encode($data));
            return true;
        }
    }
    
    file_put_contents($file, json_encode(['count' => 1, 'time' => $now]));
    return true;
}

// NOTE: logSecurityEvent() is now in security_helpers.php
// This function is intentionally left empty to avoid duplicates
