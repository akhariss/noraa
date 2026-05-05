<?php
declare(strict_types=1);

namespace App\Security;

/**
 * SK-02: Input Sanitizer
 * Centralized sanitization layer.
 */
class InputSanitizer
{
    /**
     * Sanitize all PHP global input arrays.
     * Call once in the front controller.
     */
    public static function sanitizeGlobal(): void
    {
        $_GET    = self::sanitizeArray($_GET);
        $_POST   = self::sanitizeArray($_POST);
        $_COOKIE = self::sanitizeArray($_COOKIE);
    }

    /**
     * Sanitize a string value.
     */
    public static function string(mixed $value): string
    {
        if (!is_string($value)) {
            return '';
        }
        // Trim, strip null bytes, escape HTML entities
        $value = trim($value);
        $value = str_replace("\0", '', $value);
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitize to integer.
     */
    public static function int(mixed $value): int
    {
        return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Sanitize email.
     */
    public static function email(mixed $value): string
    {
        $filtered = filter_var(trim((string)$value), FILTER_SANITIZE_EMAIL);
        return $filtered !== false ? $filtered : '';
    }

    /**
     * Sanitize URL.
     */
    public static function url(mixed $value): string
    {
        $filtered = filter_var(trim((string)$value), FILTER_SANITIZE_URL);
        return $filtered !== false ? $filtered : '';
    }

    /**
     * Strip tags for plain text.
     */
    public static function plain(mixed $value): string
    {
        if (!is_string($value)) {
            return '';
        }
        return strip_tags(trim($value));
    }

    /**
     * Recursively sanitize an array.
     */
    public static function sanitizeArray(array $data): array
    {
        return array_map(function ($v) {
            if (is_array($v)) {
                return self::sanitizeArray($v);
            }
            if (is_string($v)) {
                return self::string($v);
            }
            return $v;
        }, $data);
    }
}
