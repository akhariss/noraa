<?php
declare(strict_types=1);

namespace App\Domain\Entities;

use App\Adapters\Database;
use App\Adapters\Logger;

/**
 * SK-06: Kendala Model
 * DB columns: id, registrasi_id, tahap, flag_active, created_at, updated_at
 */
class Kendala
{
    public function create(int $registrasiId, int $workflowStepId): int
    {
        try {
            return Database::insert(
                "INSERT INTO kendala (registrasi_id, workflow_step_id, flag_active) 
                 VALUES (:registrasi_id, :workflow_step_id, 1)",
                ['registrasi_id' => $registrasiId, 'workflow_step_id' => $workflowStepId]
            );
        } catch (\PDOException $e) {
            Logger::error('Kendala create failed', ['error' => $e->getMessage()]);
            return 0;
        }
    }

    public function getByRegistrasi(int $registrasiId): array
    {
        return Database::select(
            "SELECT k.id, k.registrasi_id, k.workflow_step_id, ws.label as tahap, k.flag_active, k.created_at, k.updated_at
             FROM kendala k
             LEFT JOIN workflow_steps ws ON k.workflow_step_id = ws.id
             WHERE k.registrasi_id = :registrasi_id ORDER BY k.created_at DESC",
            ['registrasi_id' => $registrasiId]
        );
    }

    public function getActiveByRegistrasi(int $registrasiId): array
    {
        return Database::select(
            "SELECT k.id, k.registrasi_id, k.workflow_step_id, ws.label as tahap, k.flag_active, k.created_at
             FROM kendala k
             LEFT JOIN workflow_steps ws ON k.workflow_step_id = ws.id
             WHERE k.registrasi_id = :registrasi_id AND k.flag_active = 1",
            ['registrasi_id' => $registrasiId]
        );
    }

    public function toggleFlag(int $id): bool
    {
        try {
            Database::execute(
                "UPDATE kendala SET flag_active = NOT flag_active WHERE id = :id",
                ['id' => $id]
            );
            return true;
        } catch (\PDOException $e) {
            Logger::error('Kendala toggle failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function deactivateAll(int $registrasiId): bool
    {
        try {
            Database::execute(
                "UPDATE kendala SET flag_active = 0 WHERE registrasi_id = :registrasi_id AND flag_active = 1",
                ['registrasi_id' => $registrasiId]
            );
            return true;
        } catch (\PDOException $e) {
            Logger::error('Kendala deactivateAll failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
