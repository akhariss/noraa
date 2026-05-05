<?php
declare(strict_types=1);

namespace App\Core;

class Autoloader
{
    private static array $namespaces = [];

    public static function register(): void
    {
        spl_autoload_register([self::class, 'loadClass']);
    }

    public static function addNamespace(string $namespace, string $path): void
    {
        self::$namespaces[$namespace] = rtrim($path, '/') . '/';
    }

    private static function loadClass(string $class): void
    {
        $class = ltrim($class, '\\');
        
        foreach (self::$namespaces as $ns => $path) {
            if (str_starts_with($class, $ns)) {
                $relative = substr($class, strlen($ns));
                $file = $path . str_replace('\\', '/', $relative) . '.php';
                
                if (file_exists($file)) {
                    require_once $file;
                    return;
                }
            }
        }
    }
}

