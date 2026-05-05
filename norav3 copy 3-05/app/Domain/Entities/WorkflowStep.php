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

    /**
     * Create a new workflow step
     */
    public function create(array $data): int
    {
        return Database::insert(
            "INSERT INTO {$this->table} (step_key, label, sort_order, sla_days, behavior_role, is_cancellable)
             VALUES (:step_key, :label, :sort_order, :sla_days, :behavior_role, :is_cancellable)",
            [
                'step_key'       => $data['step_key'],
                'label'          => $data['label'],
                'sort_order'     => (int)($data['sort_order'] ?? 0),
                'sla_days'       => (int)($data['sla_days'] ?? 0),
                'behavior_role'  => (int)($data['behavior_role'] ?? 1),
                'is_cancellable' => (int)($data['is_cancellable'] ?? 0),
            ]
        );
    }

    /**
     * Update a workflow step
     */
    public function update(int $id, array $data): bool
    {
        $allowed = ['step_key', 'label', 'sort_order', 'sla_days', 'behavior_role', 'is_cancellable'];
        $fields = [];
        $params = ['id' => $id];

        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "{$field} = :{$field}";
                $params[$field] = $data[$field];
            }
        }

        if (empty($fields)) return false;

        try {
            Database::execute(
                "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id",
                $params
            );
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Delete a workflow step
     */
    public function delete(int $id): bool
    {
        try {
            Database::execute("DELETE FROM {$this->table} WHERE id = :id", ['id' => $id]);
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Get max sort_order value
     */
    public function getMaxSortOrder(): int
    {
        $row = Database::selectOne("SELECT MAX(sort_order) as mx FROM {$this->table}");
        return (int)($row['mx'] ?? 0);
    }

    /**
     * Bulk reorder steps (for drag-and-drop)
     * @param array $orderedIds [id => sort_order, ...]
     */
    public function reorder(array $orderedIds): bool
    {
        try {
            foreach ($orderedIds as $id => $sortOrder) {
                Database::execute(
                    "UPDATE {$this->table} SET sort_order = :sort WHERE id = :id",
                    ['sort' => (int)$sortOrder, 'id' => (int)$id]
                );
            }
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Get behavior role usage map: behavior_role => [id, label]
     * Used to enforce uniqueness for roles 0,3,4,5,6,7,8
     */
    public function getBehaviorMap(): array
    {
        $rows = Database::select("SELECT id, behavior_role, label FROM {$this->table}");
        $map = [];
        foreach ($rows as $r) {
            $role = (int)$r['behavior_role'];
            if (!isset($map[$role])) {
                $map[$role] = [];
            }
            $map[$role][] = ['id' => (int)$r['id'], 'label' => $r['label']];
        }
        return $map;
    }

    /**
     * Check if a step_key already exists (excluding a given ID)
     */
    public function keyExists(string $key, int $excludeId = 0): bool
    {
        $row = Database::selectOne(
            "SELECT COUNT(*) as cnt FROM {$this->table} WHERE step_key = :key AND id != :id",
            ['key' => $key, 'id' => $excludeId]
        );
        return (int)($row['cnt'] ?? 0) > 0;
    }

    /**
     * Count registrations using a specific step
     */
    public function countRegistrasiUsing(int $stepId): int
    {
        $row = Database::selectOne(
            "SELECT COUNT(*) as cnt FROM registrasi WHERE current_step_id = :id",
            ['id' => $stepId]
        );
        return (int)($row['cnt'] ?? 0);
    }

}
