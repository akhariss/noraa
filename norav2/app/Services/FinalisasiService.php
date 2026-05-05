<?php
declare(strict_types=1);

namespace App\Services;

use App\Domain\Entities\Registrasi;
use App\Domain\Entities\RegistrasiHistory;
use App\Domain\Entities\User;
use App\Adapters\Database;
use App\Adapters\Logger;

/**
 * SK-14: FinalisasiService
 */
use App\Domain\Entities\WorkflowStep;

/**
 * SK-14: FinalisasiService (Elite Refactor)
 */
class FinalisasiService
{
    private Registrasi $registrasiModel;
    private RegistrasiHistory $registrasiHistoryModel;
    private WorkflowStep $workflowStepModel;

    public function __construct()
    {
        $this->registrasiModel = new Registrasi();
        $this->registrasiHistoryModel = new RegistrasiHistory();
        $this->workflowStepModel = new WorkflowStep();
    }

    public function getFinalisasiList(int $page, int $perPage, string $filter = 'all', string $search = '', string $order = 'baru'): array
    {
        $offset = ($page - 1) * $perPage;
        
        // Roles: 8 (Review), 7 (Batal) - removed 6 (Ditutup)
        $conditions = ["w.behavior_role IN (8, 7)"];
        $params = [];

        if ($filter === 'review') {
            $conditions[] = "w.behavior_role = 8";
        } elseif ($filter === 'batal') {
            $conditions[] = "w.behavior_role = 7";
        }

        if ($search !== '') {
            $conditions[] = "(p.nomor_registrasi LIKE :s1 OR k.nama LIKE :s2 OR k.hp LIKE :s3)";
            $params['s1'] = $params['s2'] = $params['s3'] = "%{$search}%";
        }

        $whereClause = "WHERE " . implode(' AND ', $conditions);

        try {
            $countRow = Database::selectOne(
                "SELECT COUNT(*) AS total 
                 FROM registrasi p 
                 LEFT JOIN klien k ON p.klien_id = k.id
                 LEFT JOIN workflow_steps w ON p.current_step_id = w.id
                 $whereClause",
                $params
            );
            $total = (int)($countRow['total'] ?? 0);
            $totalPages = (int)ceil($total / $perPage);

            $statsRow = Database::selectOne(
                "SELECT COUNT(*) AS total,
                        SUM(CASE WHEN w.behavior_role = 8 THEN 1 ELSE 0 END) AS review,
                        SUM(CASE WHEN w.behavior_role = 7 THEN 1 ELSE 0 END) AS batal
                 FROM registrasi p
                 LEFT JOIN workflow_steps w ON p.current_step_id = w.id
                 WHERE w.behavior_role IN (8, 7)"
            );
            
            $sortDirection = ($order === 'lama') ? 'ASC' : 'DESC';
            $registrasiList = Database::select(
                "SELECT p.id, p.klien_id, p.layanan_id, p.nomor_registrasi,
                        p.created_at, p.updated_at, p.step_started_at,
                        p.selesai_batal_at, p.final_at,
                        k.nama AS klien_nama, k.hp AS klien_hp, l.nama_layanan,
                        w.step_key AS status, w.label AS status_label, w.behavior_role
                 FROM registrasi p
                 LEFT JOIN klien k ON p.klien_id = k.id
                 LEFT JOIN layanan l ON p.layanan_id = l.id
                 LEFT JOIN workflow_steps w ON p.current_step_id = w.id
                 $whereClause
                 ORDER BY p.updated_at $sortDirection
                 LIMIT $perPage OFFSET $offset",
                $params
            );

            return [
                'data'       => $registrasiList,
                'stats'      => [
                    'total'   => (int)($statsRow['total'] ?? 0),
                    'review'  => (int)($statsRow['review'] ?? 0),
                    'batal'   => (int)($statsRow['batal'] ?? 0),
                    'ditutup' => 0,
                ],
                'pagination' => [
                    'current_page' => $page,
                    'per_page'     => $perPage,
                    'total'        => $total,
                    'total_pages'  => $totalPages,
                    'has_next'     => $page < $totalPages,
                    'has_prev'     => $page > 1,
                ],
                'filter' => $filter,
            ];
        } catch (\Exception $e) {
            Logger::error('FinalisasiService error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function tutupRegistrasi(int $registrasiId, int $userId, ?string $notes = null): array
    {
        try {
            Database::beginTransaction();

            $registrasi = $this->registrasiModel->findById($registrasiId);
            if (!$registrasi) throw new \Exception('Registrasi tidak ditemukan');

            // Determine target behavior based on current status (Sync with USER_REQUEST v5.2)
            // Review (8) -> Diserahkan/Selesai (5), Batal (7) -> Ditutup (6)
            $currentBehavior = (int)($registrasi['behavior_role'] ?? 0);
            $targetBehavior = ($currentBehavior === 8) ? 5 : 6;

            $targetStep = $this->workflowStepModel->findByBehavior($targetBehavior);
            if (!$targetStep) throw new \Exception("Stage dengan Behavior $targetBehavior tidak ditemukan");

            $updateData = [
                'current_step_id'  => (int)$targetStep['id'],
                'step_started_at'  => date('Y-m-d H:i:s'),
                'final_at'         => date('Y-m-d H:i:s'),
                'catatan_internal' => $notes ?? $registrasi['catatan_internal']
            ];

            $success = $this->registrasiModel->update($registrasiId, $updateData);
            
            if (!$success) throw new \Exception('Gagal update database finalisasi');

            // SK-06: Foreign Key Safety - Use null instead of 0 for strict DB (v6.23)
            $this->registrasiHistoryModel->create([
                'registrasi_id'            => $registrasiId,
                'status_old_id'            => !empty($registrasi['current_step_id']) ? (int)$registrasi['current_step_id'] : null,
                'status_new_id'            => (int)$targetStep['id'],
                'action'                   => 'Finalisasi',
                'target_completion_at_old' => $registrasi['target_completion_at'] ?? null,
                'target_completion_at_new' => $registrasi['target_completion_at'] ?? null,
                'keterangan'               => $registrasi['keterangan'] ?? null,
                'catatan'                  => $notes ?? 'Registrasi ditutup secara digital.',
                'user_id'                  => $userId,
                'ip_address'               => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            ]);

            Database::commit();
            return ['success' => true, 'message' => 'Registrasi berhasil ditutup.'];

        } catch (\Exception $e) {
            Database::rollback();
            Logger::error('tutupRegistrasi failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function reopenCase(int $registrasiId, int $userId, string $targetStatus = 'selesai', ?string $notes = null): array
    {
        try {
            Database::beginTransaction();

            $registrasi = $this->registrasiModel->findById($registrasiId);
            if (!$registrasi) throw new \Exception('Registrasi tidak ditemukan');

            // SK-15: Determine target stage based on action (4 for Selesai, 3 for Perbaikan/Process)
            $behaviorId = ($targetStatus === 'selesai') ? 4 : 3;
            $targetStep = $this->workflowStepModel->findByBehavior($behaviorId);
            
            // Fallback: If behavior search fails, find by key (Sync with SQL v8)
            if (!$targetStep) {
                $targetKey = ($targetStatus === 'selesai') ? 'selesai' : 'perbaikan';
                $steps = Database::select("SELECT id, step_key, label FROM workflow_steps WHERE step_key = :key LIMIT 1", ['key' => $targetKey]);
                $targetStep = !empty($steps) ? $steps[0] : null;
            }

            if (!$targetStep) throw new \Exception("Stage target ($targetStatus) tidak ditemukan di database.");

            // G-08: Extract milestone reset to Registrasi method to avoid duplication
            $this->registrasiModel->resetMilestones($registrasiId);
            
            // SK-06: Update step and notes
            $updateData = [
                'current_step_id'  => (int)$targetStep['id'],
                'step_started_at'  => date('Y-m-d H:i:s'),
                'catatan_internal' => $notes ?? 'Membuka kembali registrasi.'
            ];

            // Behavior 4 tetap mengisi selesai_batal_at (Re-open success path)
            if ($behaviorId === 4) {
                $updateData['selesai_batal_at'] = date('Y-m-d H:i:s');
            }

            $success = $this->registrasiModel->update($registrasiId, $updateData);

            if (!$success) {
                throw new \Exception('Gagal update database saat membuka kembali registrasi.');
            }

            // SK-06: Precise History Mapping - Filling all v8 Columns (v6.22)
            $this->registrasiHistoryModel->create([
                'registrasi_id'            => $registrasiId,
                'status_old_id'            => !empty($registrasi['current_step_id']) ? (int)$registrasi['current_step_id'] : null,
                'status_new_id'            => (int)$targetStep['id'],
                'action'                   => 'Re-open',
                'target_completion_at_old' => $registrasi['target_completion_at'] ?? null,
                'target_completion_at_new' => $registrasi['target_completion_at'] ?? null,
                'keterangan'               => $registrasi['keterangan'] ?? null,
                'catatan'                  => $notes ?? 'Registrasi dibuka kembali (Re-open).',
                'user_id'                  => $userId,
                'ip_address'               => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            ]);

            Database::commit();
            return ['success' => true, 'message' => "Registrasi dibuka kembali ke status: " . $targetStep['label']];

        } catch (\Exception $e) {
            Database::rollback();
            Logger::error('reopenCase failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function getStatistik(): array
    {
        return $this->registrasiModel->getStatistik();
    }
}
