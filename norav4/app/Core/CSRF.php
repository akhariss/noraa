<?php
declare(strict_types=1);

namespace App\Core;

class CSRF
{
    private static string $tokenName = CSRF_TOKEN_NAME;

    public static function generateToken(): string
    {
        if (empty($_SESSION[self::$tokenName])) {
            $_SESSION[self::$tokenName] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::$tokenName];
    }

    public static function verifyToken(string $token): bool
    {
        if (!isset($_SESSION[self::$tokenName])) {
            return false;
        }
        $result = hash_equals($_SESSION[self::$tokenName], $token);
        if ($result) {
            unset($_SESSION[self::$tokenName]);
        }
        return $result;
    }

    public static function getTokenName(): string
    {
        return self::$tokenName;
    }
}

