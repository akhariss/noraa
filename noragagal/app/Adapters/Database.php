<?php
declare(strict_types=1);

namespace App\Adapters;

use PDO;
use PDOException;
use PDOStatement;

/**
 * SK-06 & SK-12: Singleton PDO Adapter
 * Zero concatenation, prepared statements only.
 */
class Database
{
    private static ?PDO $instance = null;

    private function __construct()
    {
    }
    private function __clone()
    {
    }

    /**
     * Get singleton PDO connection.
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            try {
                $host = defined('DB_HOST') ? DB_HOST : 'localhost';
                $dbName = defined('DB_NAME') ? DB_NAME : 'norasblmupdate3';
                $user = defined('DB_USER') ? DB_USER : 'root';
                $pass = defined('DB_PASS') ? DB_PASS : '';
                $charset = defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4';

                $dsn = "mysql:host={$host};dbname={$dbName};charset={$charset}";

                self::$instance = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_PERSISTENT => false,
                ]);
            } catch (PDOException $e) {
                Logger::error('Database connection failed', [
                    'message' => $e->getMessage()
                ]);
                throw $e;
            }
        }

        return self::$instance;
    }

    /**
     * SELECT multiple rows.
     * @param string $sql
     * @param array $params
     * @return array
     */
    public static function select(string $sql, array $params = []): array
    {
        try {
            $stmt = self::getInstance()->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            Logger::error('DB select failed', ['sql' => $sql, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * SELECT single row.
     * @param string $sql
     * @param array $params
     * @return array|null
     */
    public static function selectOne(string $sql, array $params = []): ?array
    {
        try {
            $stmt = self::getInstance()->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            return $result ?: null;
        } catch (PDOException $e) {
            Logger::error('DB selectOne failed', ['sql' => $sql, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * INSERT and return last insert ID.
     * @param string $sql
     * @param array $params
     * @return int
     */
    public static function insert(string $sql, array $params = []): int
    {
        try {
            $stmt = self::getInstance()->prepare($sql);
            $stmt->execute($params);
            return (int) self::$instance->lastInsertId();
        } catch (PDOException $e) {
            Logger::error('DB insert failed', ['sql' => $sql, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Execute UPDATE/DELETE and return affected rows.
     * @param string $sql
     * @param array $params
     * @return int
     */
    public static function execute(string $sql, array $params = []): int
    {
        try {
            $stmt = self::getInstance()->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            Logger::error('DB execute failed', ['sql' => $sql, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Prepare a statement for complex operations (e.g., bindValue with types).
     * @param string $sql
     * @return PDOStatement
     */
    public static function prepare(string $sql): PDOStatement
    {
        return self::getInstance()->prepare($sql);
    }

    /**
     * Begin transaction.
     */
    public static function beginTransaction(): bool
    {
        return self::getInstance()->beginTransaction();
    }

    /**
     * Commit transaction.
     */
    public static function commit(): bool
    {
        return self::getInstance()->commit();
    }

    /**
     * Rollback transaction.
     */
    public static function rollback(): bool
    {
        return self::getInstance()->rollBack();
    }

    /**
     * Get last insert ID.
     */
    public static function lastInsertId(): int
    {
        return (int) self::getInstance()->lastInsertId();
    }

    /**
     * Raw query (for simple SELECTs without params).
     */
    public static function query(string $sql): PDOStatement
    {
        return self::getInstance()->query($sql);
    }

    /**
     * Reset connection (for testing).
     */
    public static function reset(): void
    {
        self::$instance = null;
    }
}
