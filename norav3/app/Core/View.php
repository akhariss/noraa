<?php
declare(strict_types=1);

namespace App\Core;

/**
 * SK-07: XSS Output Defense
 * Recursive escaping and secure JSON.
 */
class View
{
    /**
     * Escape a single string for HTML context.
     */
    public static function e(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Render a view template with escaped data.
     * @param string $template  Path relative to VIEWS_PATH (without .php)
     * @param array  $data      Variables to pass to the template
     */
    public static function render(string $template, array $data = []): void
    {
        // Recursively escape all string data
        $safeData = self::escapeData($data);

        // Extract variables into scope
        extract($safeData, EXTR_SKIP);

        // Also make raw data available for cases that need it (e.g., HTML content from CMS)
        $__rawData = $data;

        $viewPath = defined('VIEWS_PATH') ? VIEWS_PATH : BASE_PATH . '/views';
        $file = $viewPath . '/' . $template . '.php';

        if (file_exists($file)) {
            require $file;
        } else {
            http_response_code(500);
            echo 'View not found: ' . self::e($template);
        }
    }

    /**
     * Encode data as secure JSON.
     * SK-07: Force HEX encoding for safe JS context.
     */
    public static function json(mixed $data): string
    {
        return json_encode($data,
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE
        );
    }

    /**
     * Recursively escape all strings in an array.
     */
    private static function escapeData(array $data): array
    {
        return array_map(function ($v) {
            if (is_string($v)) {
                return htmlspecialchars($v, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
            if (is_array($v)) {
                return self::escapeData($v);
            }
            return $v;
        }, $data);
    }
}
