<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class KendalaModel
{
    public function create(int $registrasiId, int $workflowStepId, ?string $catatan = null): int
    {
        return Database::insert(
            "INSERT INTO kendala (registrasi_id, workflow_step_id, flag_active) 
             VALUES (:registrasi_id, :workflow_step_id, 1)",
            ['registrasi_id' => $registrasiId, 'workflow_step_id' => $workflowStepId]
        );
    }

    public function getActiveByRegistrasi(int $registrasiId): array
    {
        return Database::select(
            "SELECT k.*, ws.label as tahap 
             FROM kendala k 
             LEFT JOIN workflow_steps ws ON k.workflow_step_id = ws.id 
             WHERE k.registrasi_id = :id AND k.flag_active = 1",
            ['id' => $registrasiId]
        );
    }

    public function deactivateAll(int $registrasiId): bool
    {
        return Database::execute(
            "UPDATE kendala SET flag_active = 0 WHERE registrasi_id = :id AND flag_active = 1",
            ['id' => $registrasiId]
        );
    }
}
