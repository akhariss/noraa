<?php
declare(strict_types=1);

namespace App\Domain\Entities;

use App\Adapters\Database;
use App\Adapters\Logger;

/**
 * SK-06: AuditLog Model
 */
class AuditLog
{
    public function create(int $userId, string $role, string $action, ?int $registrasiId = null, ?string $oldData = null, ?string $newData = null): int
    {
        try {
            return Database::insert(
                "INSERT INTO audit_log (user_id, role, action, new_value)
                 VALUES (:user_id, :role, :action, :new_data)",
                [
                    'user_id'        => $userId,
                    'role'           => $role,
                    'action'         => $action,
                    'new_data'       => $newData,
                ]
            );
        } catch (\PDOException $e) {
            Logger::error('AuditLog create failed', ['error' => $e->getMessage()]);
            return 0;
        }
    }

    public function getRecent(int $limit = 20): array
    {
        return Database::select(
            "SELECT al.id, al.user_id, al.role, al.action,
                    al.new_value, al.timestamp,
                    u.username
             FROM audit_log al
             LEFT JOIN users u ON al.user_id = u.id
             ORDER BY al.timestamp DESC
             LIMIT :limit",
            ['limit' => $limit]
        );
    }

    public function getAll(int $limit = 100): array
    {
        return Database::select(
            "SELECT al.id, al.user_id, al.role, al.action,
                    al.new_value, al.timestamp,
                    u.username
             FROM audit_log al
             LEFT JOIN users u ON al.user_id = u.id
             ORDER BY al.timestamp DESC
             LIMIT :limit",
            ['limit' => $limit]
        );
    }

    public function getCountByAction(): array
    {
        return Database::select(
            "SELECT action, COUNT(*) as count FROM audit_log GROUP BY action ORDER BY count DESC"
        );
    }

    public function getByRegistrasi(int $registrasiId): array
    {
        return Database::select(
            "SELECT al.id, al.user_id, al.role, al.action, al.new_value,
                    al.timestamp, u.username
             FROM audit_log al
             LEFT JOIN users u ON al.user_id = u.id
             ORDER BY al.timestamp DESC",
            ['registrasi_id' => $registrasiId]
        );
    }
}
