<?php
declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use Throwable;

class Database
{
    private static ?PDO $pdo = null;

    public static function get(): PDO
    {
        if (self::$pdo === null) {
            self::connect();
        }
        return self::$pdo;
    }

    private static function connect(): void
    {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        
        try {
            self::$pdo = new PDO(
                $dsn,
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                ]
            );
        } catch (PDOException $e) {
            throw new RuntimeException('Database connection failed: ' . $e->getMessage());
        }
    }

    public static function beginTransaction(): bool
    {
        return self::get()->beginTransaction();
    }

    public static function commit(): bool
    {
        return self::get()->commit();
    }

    public static function rollBack(): bool
    {
        return self::get()->rollBack();
    }

    public static function select(string $query, array $params = []): array
    {
        $stmt = self::get()->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function selectOne(string $query, array $params = []): ?array
    {
        $stmt = self::get()->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public static function insert(string $query, array $params = []): int
    {
        $stmt = self::get()->prepare($query);
        $stmt->execute($params);
        return (int)self::get()->lastInsertId();
    }

    public static function execute(string $query, array $params = []): bool
    {
        $stmt = self::get()->prepare($query);
        return $stmt->execute($params);
    }
}

