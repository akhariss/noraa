<?php
declare(strict_types=1);

namespace App\Domain\Entities;

use App\Adapters\Database;
use App\Adapters\Logger;

class NoteTemplate
{
    public function getAll(): array
    {
        // Elite Sync v4.54: Joining with workflow_steps to get labels for CMS
        return Database::select(
            "SELECT nt.id, nt.workflow_step_id, nt.template_body, nt.updated_at, ws.label as status_label, ws.step_key as status_key
             FROM note_templates nt
             JOIN workflow_steps ws ON nt.workflow_step_id = ws.id
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
