<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class RegistrasiModel
{
    /**
     * Find detail registrasi by ID with joins
     */
    public function findById(int $id): ?array
    {
        return Database::selectOne(
            "SELECT p.*, 
                    k.nama AS klien_nama, k.hp AS klien_hp, k.email AS klien_email,
                    l.nama_layanan, l.deskripsi AS layanan_deskripsi,
                    w.step_key AS status, w.label AS status_label, w.behavior_role, w.sort_order AS workflow_order,
                    tx.total_tagihan, tx.jumlah_bayar
             FROM registrasi p
             LEFT JOIN klien k ON p.klien_id = k.id
             LEFT JOIN layanan l ON p.layanan_id = l.id
             LEFT JOIN workflow_steps w ON p.current_step_id = w.id
             LEFT JOIN transaksi tx ON tx.registrasi_id = p.id
             WHERE p.id = :id LIMIT 1",
            ['id' => $id]
        );
    }

    /**
     * Create new registration
     */
    public function create(array $data): int
    {
        return Database::insert(
            "INSERT INTO registrasi (
                klien_id, layanan_id, nomor_registrasi, current_step_id, 
                step_started_at, target_completion_at, keterangan, catatan_internal
            ) VALUES (
                :klien_id, :layanan_id, :nomor_registrasi, :current_step_id, 
                :step_started_at, :target_completion_at, :keterangan, :catatan_internal
            )",
            [
                'klien_id'             => $data['klien_id'],
                'layanan_id'           => $data['layanan_id'],
                'nomor_registrasi'     => $data['nomor_registrasi'],
                'current_step_id'      => $data['current_step_id'],
                'step_started_at'      => $data['step_started_at'] ?? date('Y-m-d H:i:s'),
                'target_completion_at' => $data['target_completion_at'] ?? null,
                'keterangan'           => $data['keterangan'] ?? null,
                'catatan_internal'     => $data['catatan_internal'] ?? null,
            ]
        );
    }

    /**
     * Update registration data
     */
    public function update(int $id, array $data): bool
    {
        $allowed = [
            'layanan_id', 'nomor_registrasi', 'current_step_id', 'step_started_at', 
            'catatan_internal', 'tracking_token', 'verification_code', 'keterangan', 
            'target_completion_at', 'selesai_batal_at', 'final_at', 'locked', 'batal_flag'
        ];
        
        $fields = [];
        $params = ['id' => $id];

        foreach ($allowed as $field) {
            if (isset($data[$field])) {
                $fields[] = "{$field} = :{$field}";
                $params[$field] = $data[$field];
            }
        }

        if (empty($fields)) return false;

        return Database::execute("UPDATE registrasi SET " . implode(', ', $fields) . " WHERE id = :id", $params);
    }

    /**
     * Get statistics for Dashboard
     */
    public function getStats(): array
    {
        return Database::selectOne(
            "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN w.behavior_role IN (0, 1, 2, 3) THEN 1 ELSE 0 END) as aktif,
                SUM(CASE WHEN w.behavior_role = 4 THEN 1 ELSE 0 END) as penyerahan,
                SUM(CASE WHEN w.behavior_role IN (5, 6) THEN 1 ELSE 0 END) as arsip,
                SUM(CASE WHEN w.behavior_role = 7 THEN 1 ELSE 0 END) as batal
             FROM registrasi p
             LEFT JOIN workflow_steps w ON p.current_step_id = w.id"
        );
    }

    private function buildPaginatedConditions(array $filters): array
    {
        $conditions = ["1=1"];
        $params = [];

        if (!empty($filters['search'])) {
            $conditions[] = "(p.nomor_registrasi LIKE :s1 OR k.nama LIKE :s2 OR k.hp LIKE :s3)";
            $params['s1'] = "%" . $filters['search'] . "%";
            $params['s2'] = "%" . $filters['search'] . "%";
            $params['s3'] = "%" . $filters['search'] . "%";
        }

        if (!empty($filters['status'])) {
            $conditions[] = "w.step_key = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['tab'])) {
            switch ($filters['tab']) {
                case 'semua': break;
                case 'aktif': $conditions[] = "w.behavior_role IN (0, 1, 2, 3)"; break;
                case 'review': $conditions[] = "w.behavior_role = 8"; break;
                case 'penyerahan': $conditions[] = "w.behavior_role = 4"; break;
                case 'arsip': $conditions[] = "w.behavior_role IN (5, 6)"; break;
                case 'batal': $conditions[] = "w.behavior_role = 7"; break;
            }
        }

        if (!empty($filters['layanan'])) {
            $conditions[] = "p.layanan_id = :layanan";
            $params['layanan'] = $filters['layanan'];
        }

        if (!empty($filters['flag']) && $filters['flag'] === '1') {
            $conditions[] = "EXISTS (SELECT 1 FROM kendala WHERE registrasi_id = p.id AND flag_active = 1)";
        }

        if (!empty($filters['bayar'])) {
            if ($filters['bayar'] === 'lunas') {
                $conditions[] = "tx.total_tagihan > 0 AND tx.jumlah_bayar >= tx.total_tagihan";
            } elseif ($filters['bayar'] === 'belum') {
                $conditions[] = "tx.total_tagihan > 0 AND (tx.jumlah_bayar IS NULL OR tx.jumlah_bayar < tx.total_tagihan)";
            }
        }

        if (!empty($filters['periode'])) {
            $conditions[] = "p.created_at LIKE :periode";
            $params['periode'] = $filters['periode'] . "%";
        }

        $orderBy = match($filters['order'] ?? 'terbaru') {
            'terlama' => 'p.updated_at ASC',
            'baru_dibuat' => 'p.created_at DESC',
            'lama_dibuat' => 'p.created_at ASC',
            'nama_asc' => 'k.nama ASC',
            'nama_desc' => 'k.nama DESC',
            'terlambat' => 'p.target_completion_at ASC',
            default => 'p.updated_at DESC'
        };

        return ['conditions' => $conditions, 'params' => $params, 'orderBy' => $orderBy];
    }

    /**
     * Get paginated registrations with filters
     */
    public function getPaginated(array $filters, int $limit = 20, int $offset = 0): array
    {
        $built = $this->buildPaginatedConditions($filters);
        $conditions = $built['conditions'];
        $params = $built['params'];
        $orderBy = $built['orderBy'];
        
        // PDO with ATTR_EMULATE_PREPARES=false requires LIMIT/OFFSET as integers.
        // Since Database::select uses execute($params), we must bind them manually or change the query.
        // A common trick is to use placeholders and ensure they are passed correctly, but here we can just append them if we trust the values (they are cast to int).
        
        $limit = (int)$limit;
        $offset = (int)$offset;

        return Database::select(
            "SELECT p.*, k.nama AS klien_nama, k.hp AS klien_hp, l.nama_layanan, 
                    w.label AS status_label, w.behavior_role,
                    tx.total_tagihan, tx.jumlah_bayar,
                    IFNULL((SELECT 1 FROM kendala WHERE registrasi_id = p.id AND flag_active = 1 LIMIT 1), 0) AS has_flag
             FROM registrasi p
             LEFT JOIN klien k ON p.klien_id = k.id
             LEFT JOIN layanan l ON p.layanan_id = l.id
             LEFT JOIN workflow_steps w ON p.current_step_id = w.id
             LEFT JOIN transaksi tx ON tx.registrasi_id = p.id
             WHERE " . implode(' AND ', $conditions) . "
             ORDER BY $orderBy
             LIMIT $limit OFFSET $offset",
            $params
        );
    }

    /**
     * Get accurate total count of registrations for pagination
     */
    public function countPaginated(array $filters): int
    {
        $built = $this->buildPaginatedConditions($filters);
        
        $result = Database::selectOne(
            "SELECT COUNT(*) as total
             FROM registrasi p
             LEFT JOIN klien k ON p.klien_id = k.id
             LEFT JOIN layanan l ON p.layanan_id = l.id
             LEFT JOIN workflow_steps w ON p.current_step_id = w.id
             LEFT JOIN transaksi tx ON tx.registrasi_id = p.id
             WHERE " . implode(' AND ', $built['conditions']),
            $built['params']
        );
        
        return (int)($result['total'] ?? 0);
    }

    /**
     * Get War Room Stats (v4 Optimized)
     */
    public function getWarRoomStats(): array
    {
        return Database::selectOne(
            "SELECT COUNT(*) as total,
                COUNT(CASE WHEN w.behavior_role IN (0, 1, 2, 3, 4, 8) THEN 1 END) as aktif,
                COUNT(CASE WHEN w.behavior_role IN (5, 6) THEN 1 END) as selesai,
                COUNT(CASE WHEN p.final_at IS NOT NULL THEN 1 END) as terkunci,
                (SELECT COUNT(*) FROM kendala WHERE flag_active = 1) as kendala_aktif
             FROM registrasi p 
             LEFT JOIN workflow_steps w ON p.current_step_id = w.id"
        );
    }

    /**
     * Find basic registration data by number for tracking
     */
    public function findByNomorRegistrasi(string $nomor): ?array
    {
        return Database::selectOne(
            "SELECT id, nomor_registrasi FROM registrasi WHERE nomor_registrasi = :nomor LIMIT 1",
            ['nomor' => $nomor]
        );
    }

    /**
     * Helper for UI colors based on behavior role
     */
    public static function getStatusStyle(int $role): array
    {
        return match($role) {
            0, 1, 8 => ['bg' => '#e3f2fd', 'color' => '#1565c0'], // Proses
            2, 3    => ['bg' => '#fff3e0', 'color' => '#e65100'], // Urusan
            4       => ['bg' => '#f3e5f5', 'color' => '#7b1fa2'], // Penyerahan
            5, 6    => ['bg' => '#e8f5e9', 'color' => '#2e7d32'], // Selesai
            7       => ['bg' => '#ffebee', 'color' => '#c62828'], // Batal
            default => ['bg' => '#f5f5f5', 'color' => '#616161']
        };
    }

    public function findByToken(string $token): ?array
    {
        return Database::selectOne(
            "SELECT p.*, k.nama AS klien_nama, k.hp AS klien_hp, l.nama_layanan, 
                    w.label AS status_label, w.behavior_role
             FROM registrasi p
             LEFT JOIN klien k ON p.klien_id = k.id
             LEFT JOIN layanan l ON p.layanan_id = l.id
             LEFT JOIN workflow_steps w ON p.current_step_id = w.id
             WHERE p.tracking_token = :token LIMIT 1",
            ['token' => $token]
        );
    }

    public function findByNomorRegistrasiFull(string $nomor): ?array
    {
        return Database::selectOne(
            "SELECT p.*, k.nama AS klien_nama, k.hp AS klien_hp, l.nama_layanan, 
                    w.label AS status_label, w.behavior_role
             FROM registrasi p
             LEFT JOIN klien k ON p.klien_id = k.id
             LEFT JOIN layanan l ON p.layanan_id = l.id
             LEFT JOIN workflow_steps w ON p.current_step_id = w.id
             WHERE p.nomor_registrasi = :nomor LIMIT 1",
            ['nomor' => $nomor]
        );
    }

    public function getTrackingProgress(int $id): array
    {
        // Get all steps for this registration's service
        $registrasi = Database::selectOne("SELECT layanan_id, current_step_id FROM registrasi WHERE id = ?", [$id]);
        if (!$registrasi) return [];

        $steps = Database::select(
            "SELECT id, step_key, label, sort_order 
             FROM workflow_steps 
             ORDER BY sort_order ASC"
        );

        $currentStepFound = false;
        $progress = [];
        foreach ($steps as $step) {
            $isCurrent = (int)$step['id'] === (int)$registrasi['current_step_id'];
            if ($isCurrent) $currentStepFound = true;

            $progress[$step['step_key']] = [
                'label' => $step['label'],
                'completed' => !$currentStepFound || $isCurrent,
                'current' => $isCurrent,
                'order' => $step['sort_order']
            ];
        }

        return $progress;
    }

    public function getTrackingHistory(int $id): array
    {
        return Database::select(
            "SELECT rh.*, w.label as status_new_label 
             FROM registrasi_history rh
             LEFT JOIN workflow_steps w ON rh.status_new_id = w.id
             WHERE rh.registrasi_id = :id 
             ORDER BY rh.created_at DESC",
            ['id' => $id]
        );
    }

    public function getLatestLog(int $id): ?array
    {
        return Database::selectOne(
            "SELECT rh.created_at, rh.catatan, w.label as status_label 
             FROM registrasi_history rh
             LEFT JOIN workflow_steps w ON rh.status_new_id = w.id
             WHERE rh.registrasi_id = ? 
             ORDER BY rh.created_at DESC LIMIT 1",
            [$id]
        );
    }
}
