<?php
declare(strict_types=1);

namespace App\Domain\Entities;

use App\Adapters\Database;
use App\Adapters\Logger;

/**
 * SK-06: Registrasi Model
 * Note: findById uses p.* for backward compat with views.
 * All other methods use explicit columns.
 */
class Registrasi
{
    /**
     * Get PDO connection (for services that need raw queries).
     */
    public function getConnection(): \PDO
    {
        return Database::getInstance();
    }

    public function findById(int $id): ?array
    {
        return Database::selectOne(
            "SELECT p.id, p.klien_id, p.layanan_id, p.nomor_registrasi,
                    p.current_step_id, p.step_started_at, p.target_completion_at,
                    p.selesai_batal_at, p.diserahkan_at, p.ditutup_at,
                    p.keterangan, p.catatan_internal, p.tracking_token, p.verification_code,
                    p.created_at, p.updated_at,
                    k.nama AS klien_nama, k.hp AS klien_hp, k.email AS klien_email,
                    l.nama_layanan, l.deskripsi AS layanan_deskripsi,
                    w.step_key AS status, w.label AS status_label, w.behavior_role, w.sort_order AS workflow_order
             FROM registrasi p
             LEFT JOIN klien k ON p.klien_id = k.id
             LEFT JOIN layanan l ON p.layanan_id = l.id
             LEFT JOIN workflow_steps w ON p.current_step_id = w.id
             WHERE p.id = :id LIMIT 1",
            ['id' => $id]
        );
    }

    public function findByNomorRegistrasi(string $nomor): ?array
    {
        return Database::selectOne(
            "SELECT p.id, p.klien_id, p.layanan_id, p.nomor_registrasi,
                    p.current_step_id, p.step_started_at,
                    p.keterangan, p.catatan_internal, p.tracking_token, p.created_at, p.updated_at,
                    k.nama AS klien_nama, k.hp AS klien_hp, k.email AS klien_email,
                    l.nama_layanan, w.step_key AS status, w.label AS status_label, w.sort_order AS workflow_order
             FROM registrasi p
             LEFT JOIN klien k ON p.klien_id = k.id
             LEFT JOIN layanan l ON p.layanan_id = l.id
             LEFT JOIN workflow_steps w ON p.current_step_id = w.id
             WHERE p.nomor_registrasi = :nomor LIMIT 1",
            ['nomor' => $nomor]
        );
    }

    public function findByKlienPhone(string $hp): array
    {
        return Database::select(
            "SELECT p.id, p.nomor_registrasi, p.keterangan, p.catatan_internal,
                    p.current_step_id, p.step_started_at,
                    p.tracking_token, p.created_at, p.updated_at,
                    k.nama AS klien_nama, l.nama_layanan, w.step_key AS status, w.label AS status_label
             FROM registrasi p
             LEFT JOIN klien k ON p.klien_id = k.id
             LEFT JOIN layanan l ON p.layanan_id = l.id
             LEFT JOIN workflow_steps w ON p.current_step_id = w.id
             WHERE k.hp = :hp
             ORDER BY p.created_at DESC",
            ['hp' => $hp]
        );
    }

    public function create(array $data): int
    {
        try {
            return Database::insert(
                "INSERT INTO registrasi (klien_id, layanan_id, nomor_registrasi, current_step_id, step_started_at, target_completion_at, keterangan, catatan_internal)
                 VALUES (:klien_id, :layanan_id, :nomor_registrasi, :current_step_id, :step_started_at, :target_completion_at, :keterangan, :catatan_internal)",
                [
                    'klien_id'             => $data['klien_id'],
                    'layanan_id'           => $data['layanan_id'],
                    'nomor_registrasi'     => $data['nomor_registrasi'] ?? null,
                    'current_step_id'      => $data['current_step_id'] ?? null,
                    'step_started_at'      => $data['step_started_at'] ?? date('Y-m-d H:i:s'),
                    'target_completion_at' => $data['target_completion_at'] ?? null,
                    'keterangan'           => $data['keterangan'] ?? null,
                    'catatan_internal'     => $data['catatan_internal'] ?? null,
                ]
            );
        } catch (\PDOException $e) {
            Logger::error('Registrasi create failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function updateStatus(int $id, string $status, int $stepId, ?string $keterangan = null, ?string $catatan = null, ?string $targetDate = null, array $milestones = []): bool
    {
        $fields = ["current_step_id = :step_id", "step_started_at = :now"];
        $params = ['id' => $id, 'step_id' => $stepId, 'now' => date('Y-m-d H:i:s')];

        // Add milestone columns if provided
        foreach ($milestones as $col => $val) {
            $fields[] = "{$col} = :{$col}";
            $params[$col] = $val;
        }

        if ($keterangan !== null) {
            $fields[] = "keterangan = :keterangan";
            $params['keterangan'] = $keterangan;
        }
        if ($catatan !== null) {
            $fields[] = "catatan_internal = :catatan";
            $params['catatan'] = $catatan;
        }
        if ($targetDate !== null) {
            $fields[] = "target_completion_at = :target";
            $params['target'] = $targetDate;
        }

        try {
            Database::execute("UPDATE registrasi SET " . implode(', ', $fields) . " WHERE id = :id", $params);
            return true;
        } catch (\PDOException $e) {
            Logger::error('Registrasi updateStatus failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function canBeCancelled(int $id): bool
    {
        $row = Database::selectOne(
            "SELECT w.is_cancellable 
             FROM registrasi r 
             JOIN workflow_steps w ON r.current_step_id = w.id 
             WHERE r.id = :id", 
            ['id' => $id]
        );
        return (bool)($row['is_cancellable'] ?? false);
    }

    public function getAll(): array
    {
        return Database::select(
            "SELECT p.id, p.nomor_registrasi, p.keterangan, p.catatan_internal,
                    p.current_step_id, p.step_started_at, p.target_completion_at,
                    p.layanan_id, p.created_at, p.updated_at,
                    k.nama AS klien_nama, k.hp AS klien_hp, l.nama_layanan, 
                    w.step_key AS status, w.label AS status_label
             FROM registrasi p
             LEFT JOIN klien k ON p.klien_id = k.id
             LEFT JOIN layanan l ON p.layanan_id = l.id
             LEFT JOIN workflow_steps w ON p.current_step_id = w.id
             ORDER BY p.created_at DESC"
        );
    }

    public function getStatistik(): array
    {
        $row = Database::selectOne(
            "SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN w.behavior_role = 3 THEN 1 ELSE 0 END) AS selesai,
                SUM(CASE WHEN w.behavior_role = 5 THEN 1 ELSE 0 END) AS batal,
                SUM(CASE WHEN w.behavior_role NOT IN (3, 4, 5) THEN 1 ELSE 0 END) AS aktif
             FROM registrasi p
             LEFT JOIN workflow_steps w ON p.current_step_id = w.id"
        );
        return $row ?? ['total' => 0, 'selesai' => 0, 'batal' => 0, 'aktif' => 0];
    }

    public function getCountWithFilters(?string $search = '', ?string $status = '', ?string $layanan = '', string $order = 'terbaru'): int
    {
        $conditions = [];
        $params = [];

        if (!empty($search)) {
            $conditions[] = "(p.nomor_registrasi LIKE :s1 OR k.nama LIKE :s2 OR k.hp LIKE :s3)";
            $params['s1'] = "%$search%";
            $params['s2'] = "%$search%";
            $params['s3'] = "%$search%";
        }
        if (!empty($status)) {
            $conditions[] = "w.step_key = :status";
            $params['status'] = $status;
        }
        if (!empty($layanan)) {
            $conditions[] = "p.layanan_id = :layanan";
            $params['layanan'] = (int)$layanan;
        }
        
        // STRICT FILTER for Terlambat
        if ($order === 'terlambat') {
            $conditions[] = "p.target_completion_at IS NOT NULL AND p.target_completion_at < CURDATE()";
            $conditions[] = "w.behavior_role < 3";
        }

        $where = !empty($conditions) ? "WHERE " . implode(' AND ', $conditions) : "";
        $row = Database::selectOne("SELECT COUNT(*) as total FROM registrasi p 
                                  LEFT JOIN klien k ON p.klien_id = k.id 
                                  LEFT JOIN workflow_steps w ON p.current_step_id = w.id
                                  $where", $params);
        return (int)($row['total'] ?? 0);
    }

    public function getWithFilters(?string $search = '', ?string $status = '', ?string $layanan = '', int $limit = 20, int $offset = 0, string $order = 'terbaru'): array
    {
        $conditions = [];
        $params = [
            'limit' => $limit,
            'offset' => $offset
        ];

        if (!empty($search)) {
            $conditions[] = "(p.nomor_registrasi LIKE :s1 OR k.nama LIKE :s2 OR k.hp LIKE :s3)";
            $params['s1'] = "%$search%";
            $params['s2'] = "%$search%";
            $params['s3'] = "%$search%";
        }
        if (!empty($status)) {
            $conditions[] = "w.step_key = :status";
            $params['status'] = $status;
        }
        if (!empty($layanan)) {
            $conditions[] = "p.layanan_id = :layanan";
            $params['layanan'] = (int)$layanan;
        }

        // STRICT FILTER for Terlambat
        if ($order === 'terlambat') {
            $conditions[] = "p.target_completion_at IS NOT NULL AND p.target_completion_at < CURDATE()";
            $conditions[] = "w.behavior_role < 3";
        }

        $where = !empty($conditions) ? "WHERE " . implode(' AND ', $conditions) : "";

        $orderBy = match ($order) {
            'terlama'   => 'p.created_at ASC',
            'nama_asc'  => 'k.nama ASC',
            'nama_desc' => 'k.nama DESC',
            'terlambat' => 'p.target_completion_at ASC, p.created_at DESC', 
            default     => 'p.created_at DESC',
        };

        return Database::select(
            "SELECT p.id, p.klien_id, p.layanan_id, p.nomor_registrasi,
                    p.current_step_id, p.step_started_at, p.target_completion_at,
                    p.keterangan, p.catatan_internal, p.tracking_token,
                    p.created_at, p.updated_at,
                    k.nama as klien_nama, k.hp as klien_hp, l.nama_layanan, 
                    w.step_key as status, w.label as status_label, w.behavior_role,
                    DATEDIFF(CURDATE(), IFNULL(p.target_completion_at, CURDATE())) as diff_raw
             FROM registrasi p 
             LEFT JOIN klien k ON p.klien_id = k.id 
             LEFT JOIN layanan l ON p.layanan_id = l.id 
             LEFT JOIN workflow_steps w ON p.current_step_id = w.id
             $where 
             ORDER BY $orderBy
             LIMIT :limit OFFSET :offset",
            $params
        );
    }

    public function update(int $id, array $data): bool
    {
        $allowed = [
            'layanan_id', 'nomor_registrasi', 'current_step_id', 'step_started_at', 
            'catatan_internal', 'tracking_token', 'verification_code', 'keterangan', 'target_completion_at',
            'selesai_batal_at', 'diserahkan_at', 'ditutup_at'
        ];
        $fields = [];
        $params = ['id' => $id];

        foreach ($allowed as $field) {
            if (isset($data[$field])) {
                $fields[] = "{$field} = :{$field}";
                $params[$field] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        try {
            Database::execute("UPDATE registrasi SET " . implode(', ', $fields) . " WHERE id = :id", $params);
            return true;
        } catch (\PDOException $e) {
            Logger::error('Registrasi update failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function delete(int $id): bool
    {
        try {
            Database::execute("DELETE FROM registrasi WHERE id = :id", ['id' => $id]);
            return true;
        } catch (\PDOException $e) {
            Logger::error('Registrasi delete failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
    // --- WAR ROOM METHODS v4.6 ---

    public function getWarRoomStats(): array
    {
        return Database::selectOne(
            "SELECT COUNT(*) as total,
                COUNT(CASE WHEN w.behavior_role NOT IN (6, 7) THEN 1 END) as aktif,
                COUNT(CASE WHEN w.behavior_role = 5 THEN 1 END) as pending,
                COUNT(CASE WHEN w.behavior_role = 6 THEN 1 END) as ditutup,
                COUNT(CASE WHEN p.keterangan LIKE '%🚩%' OR p.catatan_internal LIKE '%🚩%' 
                           OR p.keterangan LIKE '%IMPORTANT%' OR p.catatan_internal LIKE '%IMPORTANT%'
                           OR p.keterangan LIKE '%PENTING%' OR p.catatan_internal LIKE '%PENTING%'
                           OR p.keterangan LIKE '%KENDALA%' OR p.catatan_internal LIKE '%KENDALA%' THEN 1 END) as terkendala
             FROM registrasi p LEFT JOIN workflow_steps w ON p.current_step_id = w.id"
        );
    }

    public function getOverdueTasks(int $limit = 25): array
    {
        return Database::select(
            "SELECT p.id, p.nomor_registrasi, p.keterangan, p.catatan_internal,
                    k.nama as klien_nama, l.nama_layanan, 
                    w.step_key as status, w.label as status_label, 
                    p.created_at, p.updated_at, p.target_completion_at,
                    DATEDIFF(CURDATE(), IFNULL(p.target_completion_at, DATE_ADD(p.created_at, INTERVAL 7 DAY))) as diff_raw
             FROM registrasi p
             JOIN klien k ON p.klien_id = k.id
             JOIN layanan l ON p.layanan_id = l.id
             JOIN workflow_steps w ON p.current_step_id = w.id
             WHERE w.behavior_role NOT IN (6, 7) 
               /* Tampilkan jika Terlewat (> 0) ATAU Dekat Deadine (antaran -5 sampai 0) */
               AND DATEDIFF(CURDATE(), IFNULL(p.target_completion_at, DATE_ADD(p.created_at, INTERVAL 7 DAY))) >= -5
             ORDER BY diff_raw DESC LIMIT :limit",
            ['limit' => $limit]
        );
    }

    public function getPendingApproval(int $limit = 15): array
    {
        return Database::select(
            "SELECT p.id, p.nomor_registrasi, k.nama as klien_nama, l.nama_layanan, 
                    w.label as status_label, p.created_at, p.updated_at, p.target_completion_at,
                    p.keterangan, p.catatan_internal
             FROM registrasi p
             JOIN klien k ON p.klien_id = k.id
             JOIN layanan l ON p.layanan_id = l.id
             JOIN workflow_steps w ON p.current_step_id = w.id
             WHERE w.behavior_role = 5
             ORDER BY p.updated_at DESC
             LIMIT :limit",
            ['limit' => $limit]
        );
    }

    public function getAnalytics(string $period = 'monthly', ?string $date = null): array
    {
        $where = "WHERE 1=1"; $params = [];
        if ($date) {
            if ($period === 'monthly') $where .= " AND DATE_FORMAT(p.created_at, '%Y-%m') = :date";
            elseif ($period === 'yearly') $where .= " AND YEAR(p.created_at) = :date";
            else $where .= " AND DATE(p.created_at) = :date";
            $params['date'] = $date;
        }
        return Database::select("SELECT l.nama_layanan as label, COUNT(*) as value FROM registrasi p JOIN layanan l ON p.layanan_id = l.id $where GROUP BY l.id ORDER BY value DESC LIMIT 5", $params);
    }

    public function getCarouselTasks(int $limit = 15, bool $importantOnly = false): array
    {
        $where = $importantOnly ? "WHERE (p.catatan_internal LIKE '%🚩%' OR p.keterangan LIKE '%🚩%' OR p.catatan_internal LIKE '%IMPORTANT%' OR p.keterangan LIKE '%IMPORTANT%' OR p.catatan_internal LIKE '%PENTING%' OR p.keterangan LIKE '%PENTING%' OR p.catatan_internal LIKE '%KENDALA%' OR p.keterangan LIKE '%KENDALA%')" : "WHERE w.behavior_role NOT IN (6, 7)";
        return Database::select("SELECT p.id, p.nomor_registrasi, p.keterangan, p.catatan_internal, k.nama as klien_nama, l.nama_layanan, w.step_key as status, w.label as status_label, p.created_at, p.updated_at, p.target_completion_at FROM registrasi p JOIN klien k ON p.klien_id = k.id JOIN layanan l ON p.layanan_id = l.id JOIN workflow_steps w ON p.current_step_id = w.id $where ORDER BY p.updated_at DESC LIMIT :limit", ['limit' => $limit]);
    }
}
