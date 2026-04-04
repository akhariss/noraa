<?php
declare(strict_types=1);

namespace App\Security;

use App\Adapters\Logger;

/**
 * SK-08: Rate Limiter
 * File-based per-IP throttling.
 */
class RateLimiter
{
    private static ?string $cacheDir = null;

    /**
     * Thresholds per type.
     */
    private static array $thresholds = [
        'global'           => ['limit' => 100, 'window' => 60],
        'homepage'         => ['limit' => 100, 'window' => 60],
        'login'            => ['limit' => 5,   'window' => 300],
        'login_success'    => ['limit' => 10,  'window' => 60],
        'tracking_search'  => ['limit' => 5,   'window' => 60],
        'tracking_verify'  => ['limit' => 5,   'window' => 60],
    ];

    /**
     * Check rate limit for a given type.
     */
    public static function check(string $ip, string $type = 'global'): bool
    {
        $config = self::$thresholds[$type] ?? self::$thresholds['global'];
        $limit  = $config['limit'];
        $window = $config['window'];

        $dir = self::getCacheDir();
        $file = $dir . '/' . md5($ip . ':' . $type) . '.rl';
        $now = time();

        if (file_exists($file)) {
            $data = @json_decode(file_get_contents($file), true);

            if ($data && ($now - ($data['time'] ?? 0)) < $window) {
                if (($data['count'] ?? 0) >= $limit) {
                    Logger::security('RATE_LIMIT_EXCEEDED', [
                        'ip'   => $ip,
                        'type' => $type,
                    ]);
                    return false;
                }
                $data['count']++;
                @file_put_contents($file, json_encode($data), LOCK_EX);
                return true;
            }
        }

        // Reset counter
        @file_put_contents($file, json_encode(['count' => 1, 'time' => $now]), LOCK_EX);
        return true;
    }

    /**
     * Quick check using REMOTE_ADDR.
     */
    public static function checkGlobal(string $type = 'global'): bool
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        return self::check($ip, $type);
    }

    /**
     * Get cache directory for rate limit files.
     */
    private static function getCacheDir(): string
    {
        if (self::$cacheDir === null) {
            self::$cacheDir = defined('STORAGE_PATH')
                ? STORAGE_PATH . '/cache/ratelimit'
                : (defined('BASE_PATH') ? BASE_PATH . '/storage/cache/ratelimit' : sys_get_temp_dir());
        }

        if (!is_dir(self::$cacheDir)) {
            @mkdir(self::$cacheDir, 0775, true);
        }

        return self::$cacheDir;
    }
}
