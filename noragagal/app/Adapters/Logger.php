<?php
declare(strict_types=1);

namespace App\Adapters;

/**
 * SK-10: Secure Logger
 * Segregated log files with PII redaction.
 */
class Logger
{
    private const SENSITIVE_KEYS = [
        'password', 'password_hash', 'token', 'csrf_token',
        'secret', 'api_key', 'session_hash', 'credit_card',
    ];

    private static ?string $logDir = null;

    /**
     * Initialize log directory.
     */
    private static function getLogDir(): string
    {
        if (self::$logDir === null) {
            self::$logDir = defined('STORAGE_PATH')
                ? STORAGE_PATH . '/logs'
                : (defined('BASE_PATH') ? BASE_PATH . '/storage/logs' : sys_get_temp_dir());
        }

        if (!is_dir(self::$logDir)) {
            @mkdir(self::$logDir, 0775, true);
        }

        return self::$logDir;
    }

    /**
     * General info log → app.log
     */
    public static function info(string $message, array $context = []): void
    {
        self::write('app.log', 'INFO', $message, $context);
    }

    /**
     * Error log → error.log
     */
    public static function error(string $message, array $context = []): void
    {
        self::write('error.log', 'ERROR', $message, $context);
    }

    /**
     * Security event → security.log
     */
    public static function security(string $message, array $context = []): void
    {
        self::write('security.log', 'SECURITY', $message, $context);
    }

    /**
     * Honeypot trap → honeypot.log
     */
    public static function honeypot(string $message, array $context = []): void
    {
        self::write('honeypot.log', 'HONEYPOT', $message, $context);
    }

    /**
     * Write to a specific log file.
     */
    private static function write(string $file, string $level, string $message, array $context): void
    {
        $logDir = self::getLogDir();
        $path = $logDir . '/' . $file;

        $timestamp = date('Y-m-d H:i:s');
        $ip = self::getClientIP();

        // Redact sensitive data
        $safeContext = self::redact($context);

        $contextStr = !empty($safeContext) ? ' ' . json_encode($safeContext, JSON_UNESCAPED_UNICODE) : '';

        $entry = sprintf(
            "[%s] [%s] [%s] %s%s\n",
            $timestamp,
            $level,
            $ip,
            $message,
            $contextStr
        );

        @file_put_contents($path, $entry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Redact sensitive keys from context.
     */
    private static function redact(array $data): array
    {
        foreach ($data as $key => &$value) {
            if (is_string($key) && in_array(strtolower($key), self::SENSITIVE_KEYS, true)) {
                $value = '[REDACTED]';
            } elseif (is_array($value)) {
                $value = self::redact($value);
            }
        }
        return $data;
    }

    /**
     * Get client IP.
     */
    private static function getClientIP(): string
    {
        $keys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
        foreach ($keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                if (strpos($ip, ',') !== false) {
                    $ip = explode(',', $ip)[0];
                }
                return trim($ip);
            }
        }
        return 'unknown';
    }
}
