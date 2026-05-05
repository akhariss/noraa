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
     * Uses constants from config/app.php if defined, otherwise uses defaults.
     */
    private static function getThreshold(string $type): array
    {
        switch ($type) {
            case 'homepage':
                return ['limit' => defined('RATE_LIMIT_HOMEPAGE') ? RATE_LIMIT_HOMEPAGE : 100, 'window' => 60];
            case 'tracking_verify':
                return ['limit' => defined('RATE_LIMIT_TRACKING') ? RATE_LIMIT_TRACKING : 5, 'window' => 30];
            case 'login':
                return ['limit' => 5, 'window' => 300];
            case 'login_success':
                return ['limit' => 10, 'window' => 60];
            case 'tracking_search':
                return ['limit' => 100, 'window' => 60];
            default:
                return ['limit' => 100, 'window' => 60];
        }
    }

    /**
     * Check rate limit for a given type.
     */
    public static function check(string $ip, string $type = 'global'): bool
    {
        $config = self::getThreshold($type);
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

    public static function checkGlobal(string $type = 'global'): bool
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        return self::check($ip, $type);
    }

    /**
     * Check if blocked without incrementing.
     */
    public static function isBlocked(string $type = 'global'): bool
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $config = self::getThreshold($type);
        $limit  = $config['limit'];
        $window = $config['window'];

        $dir = self::getCacheDir();
        $file = $dir . '/' . md5($ip . ':' . $type) . '.rl';
        $now = time();

        if (file_exists($file)) {
            $data = @json_decode(file_get_contents($file), true);
            if ($data && ($now - ($data['time'] ?? 0)) < $window) {
                return ($data['count'] ?? 0) >= $limit;
            }
        }
        return false;
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
