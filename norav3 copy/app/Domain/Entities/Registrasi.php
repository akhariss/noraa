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
     * Cache for column existence check
     */
    private static ?array $columnCache = null;

    /**
     * Get PDO connection (for services that need raw queries).
     */
    public function getConnection(): \PDO
    {
        return Database::getInstance();
    }

    /**
     * Check if a column exists in registrasi table
     * Used for backward compatibility during migration
     */
    private function columnExists(string $columnName): bool
    {
        // Cache column existence to avoid repeated queries
        if (self::$columnCache === null) {
            self::$columnCache = [];
            try {
                $columns = Database::select(
                    "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
                     WHERE TABLE_NAME = 'registrasi' AND TABLE_SCHEMA = DATABASE()"
                );
                foreach ($columns as $col) {
                    self::$columnCache[$col['COLUMN_NAME']] = true;
                }
            } catch (\Exception $e) {
                Logger::error('Column check failed', ['error' => $e->getMessage()]);
                return false;
            }
        }
        return isset(self::$columnCache[$columnName]);
    }

    public function findById(int $id): ?array
    {
        // Build SELECT clause dynamically to handle missing columns during migration
        $selectCols = "SELECT p.id, p.klien_id, p.layanan_id, p.nomor_registrasi,
                    p.current_step_id, p.step_started_at, p.target_completion_at,
                    p.selesai_batal_at, p.final_at,
                    p.keterangan, p.catatan_internal, p.tracking_token, p.verification_code,";
        
        // Add locked and batal_flag only if they exist (post-migration)
        if ($this->columnExists('locked')) {
            $selectCols .= " p.locked,";
        } else {
            $selectCols .= " 0 AS locked,"; // Fallback to default value
        }
        
        if ($this->columnExists('batal_flag')) {
            $selectCols .= " p.batal_flag,";
        } else {
            $selectCols .= " 0 AS batal_flag,"; // Fallback to default value
        }
        
        $selectCols .= " p.created_at, p.updated_at,
                    k.nama AS klien_nama, k.hp AS klien_hp, k.email AS klien_email,
                    l.nama_layanan, l.deskripsi AS layanan_deskripsi,
                    w.step_key AS status, w.label AS status_label, w.behavior_role, w.sort_order AS workflow_order";

        try {
            return Database::selectOne(
                "$selectCols
                 FROM registrasi p
                 LEFT JOIN klien k ON p.klien_id = k.id
                 LEFT JOIN layanan l ON p.layanan_id = l.id
                 LEFT JOIN workflow_steps w ON p.current_step_id = w.id
                 WHERE p.id = :id LIMIT 1",
                ['id' => $id]
            );
        } catch (\Exception $e) {
            Logger::error('findById failed', ['registrasi_id' => $id, 'error' => $e->getMessage()]);
            return null;
        }
    }

    public function findByNomorRegistrasi(string $nomor): ?array
    {
        return Database::selectOne(
            "SELECT p.id, p.klien_id, p.layanan_id, p.nomor_registrasi,
                    p.current_step_id, p.step_started_at,
                    p.keterangan, p.catatan_internal, p.tracking_token, p.verification_code, p.created_at, p.updated_at,
                    k.nama AS klien_nama, k.hp AS klien_hp, k.email AS klien_email,
                    l.nama_layanan, w.step_key AS status, w.label AS status_label, w.sort_order AS workflow_order
             FROM registrasi p
             LEFT JOIN klien k ON p.klien_id = k.id
             LEFT JOIN layanan l ON p.layanan_id = l.id
             LEFT JOIN workflow_steps w ON p.current_step_id = w.id
             WHERE LOWER(p.nomor_registrasi) = LOWER(:nomor) LIMIT 1",
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
                SUM(CASE WHEN w.behavior_role IN (4, 5, 6) THEN 1 ELSE 0 END) AS selesai,
                SUM(CASE WHEN w.behavior_role = 7 THEN 1 ELSE 0 END) AS batal,
                SUM(CASE WHEN w.behavior_role < 4 THEN 1 ELSE 0 END) AS aktif
             FROM registrasi p
             LEFT JOIN workflow_steps w ON p.current_step_id = w.id"
        );
        return $row ?? ['total' => 0, 'selesai' => 0, 'batal' => 0, 'aktif' => 0];
    }

    public function getCountWithFilters(?string $search = '', ?string $status = '', ?string $layanan = '', string $order = 'terbaru', string $tab = 'aktif', string $bayar = '', string $periode = ''): int
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
        
        // Tab mode filter (behavior-based segregation v6)
        if ($tab === 'aktif') {
            $conditions[] = "w.behavior_role IN (0, 1, 2, 3)";
        } elseif ($tab === 'review') {
            $conditions[] = "w.behavior_role IN (8, 7)";
        } elseif ($tab === 'penyerahan') {
            $conditions[] = "w.behavior_role = 4";
        } elseif ($tab === 'arsip') {
            $conditions[] = "w.behavior_role IN (5, 6)";
        }
        // tab === 'semua' => no filter
        
        // Payment status filter
        if ($bayar === 'lunas') {
            $conditions[] = "EXISTS (SELECT 1 FROM transaksi t WHERE t.registrasi_id = p.id AND t.jumlah_bayar >= t.total_tagihan AND t.total_tagihan > 0)";
        } elseif ($bayar === 'belum') {
            $conditions[] = "(NOT EXISTS (SELECT 1 FROM transaksi t WHERE t.registrasi_id = p.id) OR EXISTS (SELECT 1 FROM transaksi t WHERE t.registrasi_id = p.id AND t.jumlah_bayar < t.total_tagihan))";
        }
        
        // Calendar period filter
        if (!empty($periode)) {
            $pLen = strlen($periode);
            if ($pLen === 4) {
                // Year only: 2026
                $conditions[] = "YEAR(p.created_at) = :periode";
                $params['periode'] = $periode;
            } elseif ($pLen === 7) {
                // Year-Month: 2026-04
                $conditions[] = "DATE_FORMAT(p.created_at, '%Y-%m') = :periode";
                $params['periode'] = $periode;
            } elseif ($pLen === 10) {
                // Full date: 2026-04-21
                $conditions[] = "DATE(p.created_at) = :periode";
                $params['periode'] = $periode;
            }
        }
        
        // STRICT FILTER for Terlambat
        if ($order === 'terlambat') {
            $conditions[] = "p.target_completion_at IS NOT NULL AND p.target_completion_at < CURDATE()";
            $conditions[] = "w.behavior_role < 4";
        }

        $where = !empty($conditions) ? "WHERE " . implode(' AND ', $conditions) : "";
        $row = Database::selectOne("SELECT COUNT(*) as total FROM registrasi p 
                                  LEFT JOIN klien k ON p.klien_id = k.id 
                                  LEFT JOIN workflow_steps w ON p.current_step_id = w.id
                                  $where", $params);
        return (int)($row['total'] ?? 0);
    }

    public function getWithFilters(?string $search = '', ?string $status = '', ?string $layanan = '', int $limit = 20, int $offset = 0, string $order = 'terbaru', string $tab = 'aktif', string $bayar = '', string $periode = ''): array
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
        
        // Tab mode filter (behavior-based segregation v6)
        if ($tab === 'aktif') {
            $conditions[] = "w.behavior_role IN (0, 1, 2, 3)";
        } elseif ($tab === 'review') {
            $conditions[] = "w.behavior_role IN (8, 7)";
        } elseif ($tab === 'penyerahan') {
            $conditions[] = "w.behavior_role = 4";
        } elseif ($tab === 'arsip') {
            $conditions[] = "w.behavior_role IN (5, 6)";
        }
        
        // Payment status filter
        if ($bayar === 'lunas') {
            $conditions[] = "EXISTS (SELECT 1 FROM transaksi t WHERE t.registrasi_id = p.id AND t.jumlah_bayar >= t.total_tagihan AND t.total_tagihan > 0)";
        } elseif ($bayar === 'belum') {
            $conditions[] = "(NOT EXISTS (SELECT 1 FROM transaksi t WHERE t.registrasi_id = p.id) OR EXISTS (SELECT 1 FROM transaksi t WHERE t.registrasi_id = p.id AND t.jumlah_bayar < t.total_tagihan))";
        }
        
        // Calendar period filter
        if (!empty($periode)) {
            $pLen = strlen($periode);
            if ($pLen === 4) {
                $conditions[] = "YEAR(p.created_at) = :periode";
                $params['periode'] = $periode;
            } elseif ($pLen === 7) {
                $conditions[] = "DATE_FORMAT(p.created_at, '%Y-%m') = :periode";
                $params['periode'] = $periode;
            } elseif ($pLen === 10) {
                $conditions[] = "DATE(p.created_at) = :periode";
                $params['periode'] = $periode;
            }
        }

        // STRICT FILTER for Terlambat
        if ($order === 'terlambat') {
            $conditions[] = "p.target_completion_at IS NOT NULL AND p.target_completion_at < CURDATE()";
            $conditions[] = "w.behavior_role < 4";
        }

        $where = !empty($conditions) ? "WHERE " . implode(' AND ', $conditions) : "";

        $orderBy = match ($order) {
            'terlama'   => 'p.updated_at ASC',
            'baru_dibuat' => 'p.created_at DESC',
            'lama_dibuat' => 'p.created_at ASC',
            'nama_asc'  => 'k.nama ASC',
            'nama_desc' => 'k.nama DESC',
            'terlambat' => 'p.target_completion_at ASC, p.created_at DESC', 
            default     => 'p.updated_at DESC',
        };

        return Database::select(
            "SELECT p.id, p.klien_id, p.layanan_id, p.nomor_registrasi,
                    p.current_step_id, p.step_started_at, p.target_completion_at,
                    p.keterangan, p.catatan_internal, p.tracking_token,
                    p.created_at, p.updated_at,
                    k.nama as klien_nama, k.hp as klien_hp, l.nama_layanan, 
                    w.step_key as status, w.label as status_label, w.behavior_role,
                    DATEDIFF(CURDATE(), IFNULL(p.target_completion_at, CURDATE())) as diff_raw,
                    IFNULL(tx.total_tagihan, 0) as total_tagihan,
                    IFNULL(tx.jumlah_bayar, 0) as jumlah_bayar
             FROM registrasi p 
             LEFT JOIN klien k ON p.klien_id = k.id 
             LEFT JOIN layanan l ON p.layanan_id = l.id 
             LEFT JOIN workflow_steps w ON p.current_step_id = w.id
             LEFT JOIN transaksi tx ON tx.registrasi_id = p.id
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
            'selesai_batal_at', 'final_at', 'locked', 'batal_flag'
        ];
        $fields = [];
        $params = ['id' => $id];

        foreach ($allowed as $field) {
            if (isset($data[$field])) {
                // SAFETY: Skip new columns if they don't exist yet in database (during migration)
                if (in_array($field, ['locked', 'batal_flag']) && !$this->columnExists($field)) {
                    Logger::warning("Column {$field} does not exist yet. Skipping. Run migration to activate.");
                    continue;
                }
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

    /**
     * G-20: Check if registrasi is locked
     */
    public function isLocked(int $id): bool
    {
        $row = Database::selectOne(
            "SELECT locked FROM registrasi WHERE id = :id",
            ['id' => $id]
        );
        return (bool)($row['locked'] ?? false);
    }

    /**
     * G-20: Lock a registrasi to prevent concurrent edits
     */
    public function lock(int $id): bool
    {
        try {
            Database::execute(
                "UPDATE registrasi SET locked = 1 WHERE id = :id",
                ['id' => $id]
            );
            return true;
        } catch (\PDOException $e) {
            Logger::error('Registrasi lock failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * G-20: Unlock a registrasi
     */
    public function unlock(int $id): bool
    {
        try {
            Database::execute(
                "UPDATE registrasi SET locked = 0 WHERE id = :id",
                ['id' => $id]
            );
            return true;
        } catch (\PDOException $e) {
            Logger::error('Registrasi unlock failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * G-21: Set batal_flag for cancellation tracking
     */
    public function setBatalFlag(int $id, bool $isBatal): bool
    {
        try {
            Database::execute(
                "UPDATE registrasi SET batal_flag = ? WHERE id = :id",
                [':id' => $id],
                [$isBatal ? 1 : 0]
            );
            return true;
        } catch (\PDOException $e) {
            Logger::error('Registrasi setBatalFlag failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * G-21: Get batal_flag status
     */
    public function getBatalFlag(int $id): bool
    {
        $row = Database::selectOne(
            "SELECT batal_flag FROM registrasi WHERE id = :id",
            ['id' => $id]
        );
        return (bool)($row['batal_flag'] ?? false);
    }

    /**
     * G-08: Reset all milestone timestamps (for re-open/repair operations)
     * Consolidates duplicate logic from WorkflowService and FinalisasiService
     */
    public function resetMilestones(int $id): bool
    {
        try {
            Database::execute(
                "UPDATE registrasi SET 
                 final_at = NULL,
                 selesai_batal_at = NULL
                 WHERE id = :id",
                ['id' => $id]
            );
            return true;
        } catch (\PDOException $e) {
            Logger::error('Reset milestones failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
    // --- WAR ROOM METHODS v4.6 ---

    public function getWarRoomStats(): array
    {
        return Database::selectOne(
            "SELECT COUNT(*) as total,
                COUNT(CASE WHEN w.behavior_role IN (0, 1, 2, 3, 4, 5) THEN 1 END) as aktif,
                COUNT(CASE WHEN w.behavior_role = 8 THEN 1 END) as pending,
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
             WHERE w.behavior_role NOT IN (6, 7, 8) 
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
             WHERE w.behavior_role = 8
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
        $where = $importantOnly ? "WHERE (p.catatan_internal LIKE '%🚩%' OR p.keterangan LIKE '%🚩%' OR p.catatan_internal LIKE '%IMPORTANT%' OR p.keterangan LIKE '%IMPORTANT%' OR p.catatan_internal LIKE '%PENTING%' OR p.keterangan LIKE '%PENTING%' OR p.catatan_internal LIKE '%KENDALA%' OR p.keterangan LIKE '%KENDALA%') AND w.behavior_role NOT IN (6, 7, 8)" : "WHERE w.behavior_role NOT IN (6, 7, 8)";
        return Database::select("SELECT p.id, p.nomor_registrasi, p.keterangan, p.catatan_internal, k.nama as klien_nama, l.nama_layanan, w.step_key as status, w.label as status_label, p.created_at, p.updated_at, p.target_completion_at FROM registrasi p JOIN klien k ON p.klien_id = k.id JOIN layanan l ON p.layanan_id = l.id JOIN workflow_steps w ON p.current_step_id = w.id $where ORDER BY p.updated_at DESC LIMIT :limit", ['limit' => $limit]);
    }

    /**
     * getStatusStyle - Centralized styling for all status badges
     * Ensures consistency across Dashboard, List, Tracing, and Details.
     */
    public static function getStatusStyle(int $role): array
    {
        // Default (Draft/Active) - Blue
        $bg = '#e3f2fd'; $color = '#1976d2'; $border = '#90caf9';

        switch ($role) {
            case 2: // Proses (Keluar Kantor)
            case 3: // Perbaikan
                $bg = '#fff3e0'; $color = '#f57c00'; $border = '#ffcc80';
                break;
            case 4: // Selesai (Berkas Beres)
                $bg = '#e8f5e9'; $color = '#2e7d32'; $border = '#a5d6a7';
                break;
            case 8: // Review Akhir
                $bg = '#e3f2fd'; $color = '#1976d2'; $border = '#90caf9';
                break;
            case 5: // Diserahkan
                $bg = '#f3e5f5'; $color = '#7b1fa2'; $border = '#ce93d8';
                break;
            case 6: // Ditutup / Arsip
                $bg = '#f5f5f5'; $color = '#616161'; $border = '#e0e0e0';
                break;
            case 7: // Batal
                $bg = '#ffebee'; $color = '#c62828'; $border = '#ef9a9a';
                break;
        }

        return ['bg' => $bg, 'color' => $color, 'border' => $border];
    }
}
