<?php
declare(strict_types=1);

namespace App\Services;

use RuntimeException;

class CacheService
{
    public static function get(string $key, int $ttl = 3600): mixed
    {
        $file = STORAGE_PATH . '/cache/' . md5($key) . '.cache';
        if (file_exists($file) && time() - filemtime($file) < $ttl) {
            return unserialize(file_get_contents($file));
        }
        return null;
    }

    public static function set(string $key, mixed $data, int $ttl = 3600): void
    {
        $dir = STORAGE_PATH . '/cache';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $file = $dir . '/' . md5($key) . '.cache';
        file_put_contents($file, serialize($data), LOCK_EX);
        touch($file, time() + $ttl);
    }

    public static function delete(string $key): void
    {
        $file = STORAGE_PATH . '/cache/' . md5($key) . '.cache';
        if (file_exists($file)) {
            unlink($file);
        }
    }

    public static function clear(): void
    {
        $dir = STORAGE_PATH . '/cache';
        if (is_dir($dir)) {
            array_map('unlink', glob($dir . '/*'));
        }
    }
}

