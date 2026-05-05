<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class AuditLogModel
{
    public function create(int $userId, string $role, string $action, ?string $newValue = null): int
    {
        return Database::insert(
            "INSERT INTO audit_log (user_id, role, action, new_value) VALUES (:user_id, :role, :action, :new_value)",
            [
                'user_id'   => $userId,
                'role'      => $role,
                'action'    => $action,
                'new_value' => $newValue
            ]
        );
    }

    public function getRecent(int $limit = 20): array
    {
        return Database::select(
            "SELECT al.*, u.username 
             FROM audit_log al 
             LEFT JOIN users u ON al.user_id = u.id 
             ORDER BY al.timestamp DESC LIMIT :limit",
            ['limit' => $limit]
        );
    }
}
