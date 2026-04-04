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
        
        // Roles: 5 (Diserahkan), 6 (Ditutup), 7 (Batal)
        $conditions = ["w.behavior_role IN (5, 6, 7)"];
        $params = [];

        if ($filter === 'selesai') {
            $conditions[] = "w.behavior_role = 5";
        } elseif ($filter === 'batal') {
            $conditions[] = "w.behavior_role = 7";
        } elseif ($filter === 'ditutup') {
            $conditions[] = "w.behavior_role = 6";
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
                        SUM(CASE WHEN w.behavior_role = 5 THEN 1 ELSE 0 END) AS diserahkan,
                        SUM(CASE WHEN w.behavior_role = 7 THEN 1 ELSE 0 END) AS batal,
                        SUM(CASE WHEN w.behavior_role = 6 THEN 1 ELSE 0 END) AS ditutup
                 FROM registrasi p
                 LEFT JOIN workflow_steps w ON p.current_step_id = w.id
                 WHERE w.behavior_role IN (5, 6, 7)"
            );
            
            $sortDirection = ($order === 'lama') ? 'ASC' : 'DESC';
            $registrasiList = Database::select(
                "SELECT p.id, p.nomor_registrasi, p.keterangan, p.catatan_internal,
                        p.created_at, p.updated_at, p.step_started_at,
                        p.selesai_batal_at, p.diserahkan_at, p.ditutup_at,
                        k.nama AS klien_nama, k.hp AS klien_hp, l.nama_layanan,
                        w.step_key AS status, w.label AS status_label, w.behavior_role
                 FROM registrasi p
                 LEFT JOIN klien k ON p.klien_id = k.id
                 LEFT JOIN layanan l ON p.layanan_id = l.id
                 LEFT JOIN workflow_steps w ON p.current_step_id = w.id
                 $whereClause
                 ORDER BY COALESCE(p.selesai_batal_at, p.ditutup_at, p.updated_at) $sortDirection
                 LIMIT $perPage OFFSET $offset",
                $params
            );

            return [
                'data'       => $registrasiList,
                'stats'      => [
                    'total'       => (int)($statsRow['total'] ?? 0),
                    'diserahkan'  => (int)($statsRow['diserahkan'] ?? 0),
                    'batal'       => (int)($statsRow['batal'] ?? 0),
                    'ditutup'     => (int)($statsRow['ditutup'] ?? 0),
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

            // SK-15: Use behavior_role 6 for Ditutup stages (Sync with SQL v8 Schema)
            $targetStep = $this->workflowStepModel->findByBehavior(6);
            if (!$targetStep) throw new \Exception('Stage dengan Behavior Ditutup (ID 6) tidak ditemukan');

            // SK-06: Use robust update() method to persist data (v6.21)
            $updateData = [
                'current_step_id'  => (int)$targetStep['id'],
                'step_started_at'  => date('Y-m-d H:i:s'),
                'ditutup_at'       => date('Y-m-d H:i:s'),
                'catatan_internal' => $notes ?? $registrasi['catatan_internal']
            ];

            $success = $this->registrasiModel->update($registrasiId, $updateData);
            
            if (!$success) throw new \Exception('Gagal update database (Cek apakah kolom ditutup_at sudah ada di DB?)');

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

            // SK-15: Use behavior_role 3 for Peninjauan/Perbaikan stages
            $targetStep = $this->workflowStepModel->findByBehavior(3);
            
            // Fallback: If behavior search fails, find by key 'perbaikan' (Sync with SQL v8)
            if (!$targetStep) {
                $steps = Database::select("SELECT id, step_key, label FROM workflow_steps WHERE step_key = 'perbaikan' LIMIT 1");
                $targetStep = !empty($steps) ? $steps[0] : null;
            }

            if (!$targetStep) throw new \Exception('Stage Perbaikan tidak ditemukan di database.');

            // SK-06: Hard Reset closure milestone during re-open (v6.37)
            $success = $this->registrasiModel->update($registrasiId, [
                'current_step_id'  => (int)$targetStep['id'],
                'step_started_at'  => date('Y-m-d H:i:s'),
                'ditutup_at'       => null, // Reset the seal!
                'diserahkan_at'    => null,
                'selesai_batal_at' => null,
                'catatan_internal' => $notes ?? 'Membuka kembali registrasi.'
            ]);

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
