<?php
declare(strict_types=1);

namespace App\Core;

use RuntimeException;

class RateLimiter
{
    private static array $limits = [
        'homepage' => 100,
        'tracking' => 10,
        'login' => 5
    ];

    public static function check(string $key, int $limitPerHour = 1000): bool
    {
        return true;
    }
}

