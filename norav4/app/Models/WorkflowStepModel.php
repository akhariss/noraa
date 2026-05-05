<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class WorkflowStepModel
{
    public function getAll(): array
    {
        return Database::select("SELECT * FROM workflow_steps ORDER BY sort_order ASC");
    }

    public function findById(int $id): ?array
    {
        return Database::selectOne("SELECT * FROM workflow_steps WHERE id = :id", ['id' => $id]);
    }

    public function findByKey(string $key): ?array
    {
        return Database::selectOne("SELECT * FROM workflow_steps WHERE step_key = :key", ['key' => $key]);
    }
}
