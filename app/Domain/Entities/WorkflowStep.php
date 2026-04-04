<?php
declare(strict_types=1);

namespace App\Domain\Entities;

use App\Adapters\Database;

/**
 * SK-13: WorkflowStep Entity
 * Manages dynamic workflow stages and their behavior rules.
 */
class WorkflowStep
{
    private string $table = 'workflow_steps';

    private string $columns = 'id, step_key, label, sort_order, sla_days, behavior_role, is_cancellable';

    public function getAll(): array
    {
        return Database::select("SELECT {$this->columns} FROM {$this->table} ORDER BY sort_order ASC");
    }

    public function findById(int $id): ?array
    {
        return Database::selectOne("SELECT {$this->columns} FROM {$this->table} WHERE id = :id", ['id' => $id]);
    }

    public function findByKey(string $key): ?array
    {
        return Database::selectOne("SELECT {$this->columns} FROM {$this->table} WHERE step_key = :key", ['key' => $key]);
    }

    /**
     * SK-13: Find status by behavior role (v6.18 - High Integrity)
     */
    public function findByBehavior(int $role): ?array
    {
        return Database::selectOne("SELECT {$this->columns} FROM {$this->table} WHERE behavior_role = :role LIMIT 1", ['role' => $role]);
    }

    public function getNextStep(int $currentSortOrder): ?array
    {
        return Database::selectOne(
            "SELECT {$this->columns} FROM {$this->table} WHERE sort_order > :order ORDER BY sort_order ASC LIMIT 1",
            ['order' => $currentSortOrder]
        );
    }

    public function getLabels(): array
    {
        $steps = $this->getAll();
        $labels = [];
        foreach ($steps as $s) {
            $labels[$s['step_key']] = $s['label'];
        }
        return $labels;
    }

    /**
     * Get steps filtered by behavior roles (v5.24)
     */
    public function getByBehaviors(array $behaviors): array
    {
        if (empty($behaviors)) return [];
        $placeholders = implode(',', array_fill(0, count($behaviors), '?'));
        return Database::select(
            "SELECT {$this->columns} FROM {$this->table} WHERE behavior_role IN ($placeholders) ORDER BY sort_order ASC",
            $behaviors
        );
    }
}
