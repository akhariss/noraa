<?php
declare(strict_types=1);

namespace App\Security;

/**
 * SK-03: CSRF Protection
 * One-time token with hash_equals verification.
 */
class CSRF
{
    private const TOKEN_KEY = 'csrf_token';

    /**
     * Generate or retrieve the current CSRF token from session.
     */
    public static function token(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION[self::TOKEN_KEY])) {
            $_SESSION[self::TOKEN_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::TOKEN_KEY];
    }

    /**
     * Render a hidden input field for forms.
     */
    public static function field(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . self::token() . '">';
    }

    /**
     * Validate a submitted token using timing-safe comparison.
     */
    public static function validate(?string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($token) || !isset($_SESSION[self::TOKEN_KEY])) {
            return false;
        }

        return hash_equals($_SESSION[self::TOKEN_KEY], $token);
    }

    /**
     * Regenerate token (for sensitive ops or after successful validation).
     */
    public static function regenerate(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION[self::TOKEN_KEY] = bin2hex(random_bytes(32));
        return $_SESSION[self::TOKEN_KEY];
    }
}
