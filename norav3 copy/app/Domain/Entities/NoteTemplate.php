<?php
declare(strict_types=1);

namespace App\Domain\Entities;

use App\Adapters\Database;
use App\Adapters\Logger;

class NoteTemplate
{
    public function getAll(): array
    {
        // Elite Sync v6.42: Use RIGHT JOIN to ensure ALL workflow steps appear
        // even if they don't have an entry in note_templates yet.
        return Database::select(
            "SELECT ws.id as workflow_step_id, nt.id as template_id, nt.template_body, nt.updated_at, 
                    ws.label as status_label, ws.step_key as status_key, ws.behavior_role
             FROM note_templates nt
             RIGHT JOIN workflow_steps ws ON nt.workflow_step_id = ws.id
             ORDER BY ws.sort_order ASC"
        );
    }

    public function getByWorkflowStepId(int $workflowStepId): ?array
    {
        // Elite Sync v4.51: Using the new integer ID for peak performance
        return Database::selectOne(
            "SELECT id, workflow_step_id, template_body, updated_at
             FROM note_templates WHERE workflow_step_id = :id LIMIT 1",
            ['id' => $workflowStepId]
        );
    }

    public function getAllAsMap(): array
    {
        $all = $this->getAll();
        $map = [];
        foreach ($all as $row) {
            // Mapping by the underlying step_key for logic backwards compatibility
            $map[$row['status_key']] = $row['template_body'];
        }
        return $map;
    }

    public function getByStatusKey(string $statusKey): ?array
    {
        return Database::selectOne(
            "SELECT nt.id, nt.workflow_step_id, nt.template_body
             FROM note_templates nt
             JOIN workflow_steps ws ON nt.workflow_step_id = ws.id
             WHERE ws.step_key = :key LIMIT 1",
            ['key' => $statusKey]
        );
    }

    public function create(array $data): bool
    {
        try {
            // Find step id from status_key (Case-insensitive)
            $step = Database::selectOne(
                "SELECT id FROM workflow_steps WHERE LOWER(step_key) = LOWER(:key) LIMIT 1",
                ['key' => $data['status_key']]
            );

            if (!$step) return false;

            // Adjusted to match Observed Schema: id, workflow_step_id, template_body, updated_at, updated_by
            Database::execute(
                "INSERT INTO note_templates (workflow_step_id, template_body, updated_at, updated_by)
                 VALUES (:ws_id, :body, NOW(), :user_id)",
                [
                    'ws_id' => (int)$step['id'],
                    'body' => trim($data['template_body']),
                    'user_id' => $data['created_by']
                ]
            );
            return true;
        } catch (\PDOException $e) {
            Logger::error('NoteTemplate create failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function updateBody(int $id, string $body, ?int $userId = null): bool
    {
        try {
            Database::execute(
                "UPDATE note_templates SET template_body = :body, updated_by = :user_id, updated_at = NOW() WHERE id = :id",
                ['body' => trim($body), 'user_id' => $userId, 'id' => $id]
            );
            return true;
        } catch (\PDOException $e) {
            Logger::error('NoteTemplate update failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
