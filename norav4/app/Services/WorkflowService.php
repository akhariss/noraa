<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\RegistrasiModel;
use App\Models\KendalaModel;
use App\Models\AuditLogModel;
use App\Models\RegistrasiHistoryModel;
use App\Models\WorkflowStepModel;
use App\Core\Database;

/**
 * WorkflowService - The elite engine of Nora V4
 * Orchestrates status transitions, audit trails, and business logic enforcement.
 */
class WorkflowService
{
    private RegistrasiModel $registrasiModel;
    private KendalaModel $kendalaModel;
    private AuditLogModel $auditLogModel;
    private RegistrasiHistoryModel $historyModel;
    private WorkflowStepModel $stepModel;

    public function __construct()
    {
        $this->registrasiModel = new RegistrasiModel();
        $this->kendalaModel = new KendalaModel();
        $this->auditLogModel = new AuditLogModel();
        $this->historyModel = new RegistrasiHistoryModel();
        $this->stepModel = new WorkflowStepModel();
    }

    /**
     * Update status with transaction and validation
     */
    public function updateStatus(
        int $registrasiId,
        string $newStatusKey,
        int $userId,
        string $role,
        ?string $catatan = null,
        ?bool $flagKendala = null,
        ?string $keterangan = null
    ): array {
        try {
            Database::beginTransaction();

            $registrasi = $this->registrasiModel->findById($registrasiId);
            if (!$registrasi) throw new \Exception('Registrasi tidak ditemukan');

            // Security Check: Lock Mechanism
            if (!empty($registrasi['locked']) && (int)$registrasi['locked'] === 1) {
                Database::rollBack();
                return ['success' => false, 'message' => 'Registrasi sedang dikunci.'];
            }

            // Load Steps
            $currentStep = $this->stepModel->findById((int)$registrasi['current_step_id']);
            $nextStep = $this->stepModel->findByKey($newStatusKey);
            if (!$nextStep) throw new \Exception('Status tujuan tidak valid');

            // Workflow Rule: No backward movement unless REPAIR (3)
            if ($currentStep && $nextStep['sort_order'] < $currentStep['sort_order']) {
                if ((int)$currentStep['behavior_role'] !== 3 && (int)$nextStep['behavior_role'] !== 3) {
                    throw new \Exception('Status tidak dapat mundur kecuali ke tahap Perbaikan.');
                }
            }

            // Milestone Injection
            $now = date('Y-m-d H:i:s');
            $milestones = [];
            $nextRole = (int)$nextStep['behavior_role'];
            
            if (in_array($nextRole, [4, 7])) $milestones['selesai_batal_at'] = $now;
            if ($nextRole === 5) $milestones['final_at'] = $now;

            // Update Database
            $updateData = array_merge($milestones, [
                'current_step_id' => (int)$nextStep['id'],
                'step_started_at' => $now,
                'keterangan'      => $keterangan ?? $registrasi['keterangan'],
                'catatan_internal'=> $catatan ?? $registrasi['catatan_internal']
            ]);

            $this->registrasiModel->update($registrasiId, $updateData);

            // Handle Kendala (Flag)
            $isTerminal = ($nextRole >= 4 && $nextRole <= 7);
            if ($isTerminal) {
                $this->kendalaModel->deactivateAll($registrasiId);
            } elseif ($flagKendala !== null) {
                $active = $this->kendalaModel->getActiveByRegistrasi($registrasiId);
                if ($flagKendala && empty($active)) {
                    $this->kendalaModel->create($registrasiId, (int)$nextStep['id']);
                } elseif (!$flagKendala && !empty($active)) {
                    $this->kendalaModel->deactivateAll($registrasiId);
                }
            }

            // Audit Trail
            $this->historyModel->create([
                'registrasi_id'            => $registrasiId,
                'status_old_id'            => $currentStep['id'] ?? null,
                'status_new_id'            => $nextStep['id'],
                'target_completion_at_old' => $registrasi['target_completion_at'],
                'target_completion_at_new' => $registrasi['target_completion_at'],
                'catatan'                  => $catatan ?? $registrasi['catatan_internal'],
                'keterangan'               => $keterangan ?? $registrasi['keterangan'],
                'flag_kendala_active'      => ($isTerminal ? 0 : ($flagKendala ? 1 : 0)),
                'user_id'                  => $userId,
                'ip_address'               => $_SERVER['REMOTE_ADDR'] ?? null
            ]);

            Database::commit();
            return ['success' => true, 'message' => 'Status berhasil diperbarui'];

        } catch (\Throwable $e) {
            Database::rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get Progress Timeline
     */
    public function getProgress(int $registrasiId): array
    {
        $registrasi = $this->registrasiModel->findById($registrasiId);
        if (!$registrasi) return [];

        $steps = $this->stepModel->getAll();
        $currentOrder = $registrasi['workflow_order'] ?? 0;

        $progress = [];
        foreach ($steps as $s) {
            $progress[$s['step_key']] = [
                'label'     => $s['label'],
                'completed' => $s['sort_order'] <= $currentOrder,
                'current'   => $s['id'] == $registrasi['current_step_id'],
                'is_overdue'=> $this->isOverdue($registrasi, $s)
            ];
        }
        return $progress;
    }

    private function isOverdue(array $registrasi, array $step): bool
    {
        if ($registrasi['current_step_id'] != $step['id']) return false;
        $started = strtotime($registrasi['step_started_at']);
        return (time() - $started) > ($step['sla_days'] * 86400);
    }
}
