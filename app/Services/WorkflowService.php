<?php
declare(strict_types=1);

namespace App\Services;

use App\Domain\Entities\Registrasi;
use App\Domain\Entities\Kendala;
use App\Domain\Entities\AuditLog;
use App\Domain\Entities\RegistrasiHistory;
use App\Domain\Entities\User;
use App\Domain\Entities\WorkflowStep;
use App\Adapters\Database;
use App\Adapters\Logger;

/**
 * SK-14: WorkflowService (Elite Engine)
 * Orchestrates dynamic status transitions and workflow logic.
 */
class WorkflowService
{
    private Registrasi $registrasiModel;
    private Kendala $kendalaModel;
    private AuditLog $auditLogModel;
    private RegistrasiHistory $registrasiHistoryModel;
    private User $userModel;
    private WorkflowStep $workflowStepModel;

    public function __construct()
    {
        $this->registrasiModel = new Registrasi();
        $this->kendalaModel = new Kendala();
        $this->auditLogModel = new AuditLog();
        $this->registrasiHistoryModel = new RegistrasiHistory();
        $this->userModel = new User();
        $this->workflowStepModel = new WorkflowStep();
    }

    /**
     * Update status with role-based behavior + Transactions (SK-06/SK-14)
     */
    public function updateStatus(
        int $registrasiId,
        string $newStatusKey,
        int $userId,
        string $role,
        ?string $catatan = null,
        ?bool $flagKendala = null,
        ?string $keterangan = null,
        ?int $newStepId = null
    ): array {
        $db = Database::getInstance();
        try {
            $db->beginTransaction();

            $registrasi = $this->registrasiModel->findById($registrasiId);
            if (!$registrasi) throw new \Exception('Registrasi tidak ditemukan');

            $oldStatus = $registrasi['status'];
            $oldCatatan = $registrasi['catatan_internal'];
            $oldKeterangan = $registrasi['keterangan'];
            $oldTargetDate = $registrasi['target_completion_at'];

            // If no new status provided (only updating flag/notes), fallback to current status
            if (empty($newStatusKey)) {
                $newStatusKey = $oldStatus;
            }

            // Find current and next steps for logic enforcement
            $currentStep = $this->workflowStepModel->findById((int)$registrasi['current_step_id']);
            $nextStep = $this->workflowStepModel->findByKey($newStatusKey);
            if (!$nextStep) throw new \Exception('Status tujuan tidak terdaftar');

            // Logic: Prevent backward movement unless:
            // 1. Target is a REPAIR step (Behavior 3) (Entering repair mode)
            // 2. OR Current is a REPAIR step (Behavior 3) (Repairing and re-doing process)
            if ($currentStep && $nextStep['sort_order'] < $currentStep['sort_order']) {
                $isCurrentRepair = ((int)$currentStep['behavior_role'] === 3);
                $isTargetRepair = ((int)$nextStep['behavior_role'] === 3);
                
                if (!$isCurrentRepair && !$isTargetRepair) {
                    throw new \Exception('Status tidak dapat mundur kecuali ke tahap Perbaikan (REPAIR)');
                }
            }

            // Logic: Check if cancellable (behavior 7 = Batal)
            if ((int)$nextStep['behavior_role'] === 7 && !$this->registrasiModel->canBeCancelled($registrasiId)) {
                throw new \Exception('Registrasi sudah dalam tahap yang tidak bisa dibatalkan');
            }

            // Logic: Ensure actual change happened OR we are just confirming the current state (v4.62)
            $hasStatusChange = ($newStatusKey !== $oldStatus);
            $activeKendala = $this->kendalaModel->getActiveByRegistrasi($registrasiId);
            $currentFlagState = !empty($activeKendala);
            $hasFlagChange = ($flagKendala !== null && $flagKendala !== $currentFlagState);
            $hasNotesChange = ($catatan !== null && $catatan !== $oldCatatan);

            // Anti-Spam / Ghost Submit Prevention (v4.71)
            // Block if and only if NO data at all has changed to prevent double-click duplicates.
            // We use strict comparison to ensure notes or flags aren't accidentally ignored.
            $hasKeteranganChange = ($keterangan !== null && $keterangan !== $oldKeterangan);
            
            if (!$hasStatusChange && !$hasFlagChange && !$hasNotesChange && !$hasKeteranganChange) {
                $db->rollBack();
                return ['success' => true, 'message' => 'Data sudah tersimpan (Tidak ada perubahan baru).'];
            }

            // Michelin: Valid workflow action starts here

            // Michelin v5.68: Auto-Milestone Injection
            $milestones = [];
            $now = date('Y-m-d H:i:s');
            $nextRole = (int)$nextStep['behavior_role'];
            if ($nextRole === 3 || $nextRole === 7) $milestones['selesai_batal_at'] = $now;
            if ($nextRole === 5) $milestones['diserahkan_at'] = $now;

            // Update main record (Maintaining Global TargetDate)
            $success = $this->registrasiModel->updateStatus(
                $registrasiId, 
                $newStatusKey, 
                (int)$nextStep['id'], 
                $keterangan ?? $oldKeterangan, 
                $catatan ?? $oldCatatan, 
                $oldTargetDate,
                $milestones
            );
            if (!$success) throw new \Exception('Gagal memperbarui database utama');

            // Michelin v5.56: Force flag_kendala bit off if Terminal Status (Selesai, Serah, Tutup, Batal = 4-7)
            $isTerminalState = ((int)$nextStep['behavior_role'] >= 4);
            if ($isTerminalState) {
                $this->kendalaModel->deactivateAll($registrasiId);
                // Force update flag field in main record if we had a separate flag column (not used currently but for consistency)
            } elseif ($hasFlagChange) {
                if ($flagKendala && empty($activeKendala)) {
                    // Michelin v5.57: Pass integer ID instead of string label
                    $this->kendalaModel->create($registrasiId, (int)$nextStep['id']);
                } elseif (!$flagKendala && !empty($activeKendala)) {
                    $this->kendalaModel->deactivateAll($registrasiId);
                }
            }

            // Save History (Audit Trail)
            $user = $this->userModel->findById($userId);
            
            // Michelin v5.56: If it's a terminal state, override history flag to 0 (OFF)
            $logFlag = ($isTerminalState) ? 0 : ($flagKendala ? 1 : 0);
            $logTahap = ($isTerminalState) ? null : ($flagKendala ? $nextStep['label'] : null);

            $this->registrasiHistoryModel->create([
                'registrasi_id'            => $registrasiId,
                'status_old_id'            => $currentStep ? (int)$currentStep['id'] : null,
                'status_new_id'            => (int)$nextStep['id'],
                'target_completion_at_old' => $oldTargetDate,
                'target_completion_at_new' => $oldTargetDate,
                'catatan'                  => $catatan ?? $oldCatatan,
                'keterangan'               => $keterangan ?? $oldKeterangan,
                'flag_kendala_active'      => $logFlag,
                'flag_kendala_tahap'       => $logTahap,
                'user_id'                  => $userId,
                'ip_address'               => $_SERVER['REMOTE_ADDR'] ?? null,
            ]);

            $db->commit();
            return ['success' => true, 'message' => 'Status berhasil diperbarui'];

        } catch (\Exception $e) {
            $db->rollBack();
            Logger::error('Workflow transition failed', ['error' => $e->getMessage(), 'reg_id' => $registrasiId]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get dynamic progress from DB (SK-15 Scalability)
     */
    public function getProgress(int $registrasiId): array
    {
        $registrasi = $this->registrasiModel->findById($registrasiId);
        if (!$registrasi) return [];

        $steps = $this->workflowStepModel->getAll();
        $currentSort = (int)$registrasi['behavior_role'] ?? 0; // Or index based
        // Better: find current step's sort order
        $currentOrder = 0;
        foreach ($steps as $s) {
            if ($s['id'] == $registrasi['current_step_id']) {
                $currentOrder = $s['sort_order'];
                break;
            }
        }

        $progress = [];
        foreach ($steps as $s) {
            $progress[$s['step_key']] = [
                'label'     => $s['label'],
                'order'     => $s['sort_order'],
                'completed' => $s['sort_order'] <= $currentOrder,
                'current'   => $s['id'] == $registrasi['current_step_id'],
                'estimasi'  => $s['sla_days'] . ' hari',
                'is_overdue'=> $this->isOverdue($registrasi, $s)
            ];
        }
        return $progress;
    }

    private function isOverdue(array $registrasi, array $step): bool
    {
        if ($registrasi['current_step_id'] != $step['id']) return false;
        $started = strtotime($registrasi['step_started_at']);
        $daysPassed = (time() - $started) / 86400;
        return $daysPassed > (int)$step['sla_days'];
    }

    public function getHistory(int $registrasiId): array
    {
        return $this->registrasiHistoryModel->getByRegistrasi($registrasiId);
    }
}
