<?php
declare(strict_types=1);

namespace App\Core;

/**
 * SK-18: PSR-4 Autoloader
 * Lazy loads classes only when needed.
 */
class Autoloader
{
    private static array $prefixes = [];

    /**
     * Register the autoloader with spl_autoload_register.
     */
    public static function register(): void
    {
        spl_autoload_register([self::class, 'loadClass']);
    }

    /**
     * Add a namespace prefix and base directory.
     */
    public static function addNamespace(string $prefix, string $baseDir): void
    {
        $prefix = trim($prefix, '\\') . '\\';
        $baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR) . '/';
        self::$prefixes[$prefix] = $baseDir;
    }

    /**
     * Load the class file for a given fully-qualified class name.
     */
    public static function loadClass(string $class): bool
    {
        foreach (self::$prefixes as $prefix => $baseDir) {
            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                continue;
            }

            $relativeClass = substr($class, $len);
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

            if (file_exists($file)) {
                require_once $file;
                return true;
            }
        }

        return false;
    }
}
