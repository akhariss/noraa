<?php
declare(strict_types=1);

namespace App\Core;

class InputSanitizer
{
    public static function sanitizeGlobal(): void
    {
        $_GET = self::sanitize($_GET);
        $_POST = self::sanitize($_POST);
        $_COOKIE = self::sanitize($_COOKIE);
        $_REQUEST = self::sanitize($_REQUEST);
    }

    private static function sanitize(array $input): array
    {
        return array_map(function ($value) {
            if (is_array($value)) {
                return self::sanitize($value);
            }
            return htmlspecialchars(trim((string)$value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }, $input);
    }

    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function validatePhone(string $phone): bool
    {
        return preg_match('/^[\+]?[0-9\s\-\(\)]{10,15}$/', $phone);
    }
}

