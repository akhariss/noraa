<?php
declare(strict_types=1);

namespace App\Domain\Entities;

use App\Adapters\Database;
use App\Adapters\Logger;

class MessageTemplate
{
    public function getAll(): array
    {
        return Database::select(
            "SELECT id, template_key, template_name, template_body, description, updated_at
             FROM message_templates ORDER BY id ASC"
        );
    }

    public function getByKey(string $key): ?array
    {
        return Database::selectOne(
            "SELECT id, template_key, template_name, template_body, description, updated_at
             FROM message_templates WHERE template_key = :key LIMIT 1",
            ['key' => $key]
        );
    }

    public function updateBody(int $id, string $body, ?int $userId = null): bool
    {
        try {
            Database::execute(
                "UPDATE message_templates SET template_body = :body, updated_by = :user_id, updated_at = NOW() WHERE id = :id",
                ['body' => trim($body), 'user_id' => $userId, 'id' => $id]
            );
            return true;
        } catch (\PDOException $e) {
            Logger::error('MessageTemplate update failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
