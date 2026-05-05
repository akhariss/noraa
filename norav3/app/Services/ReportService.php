<?php
declare(strict_types=1);

namespace App\Services;

use App\Adapters\Database;
use App\Adapters\Logger;
use App\Domain\Entities\Registrasi;
use App\Domain\Entities\RegistrasiHistory;
use App\Domain\Entities\WorkflowStep;
use App\Domain\Entities\Transaksi;
use App\Domain\Entities\TransaksiHistory;
use Exception;

/**
 * ReportService (V3 Architecture)
 * Handles complex analytical queries for Laporan & Audit.
 */
class ReportService
{
    private Registrasi $registrasiModel;
    private RegistrasiHistory $registrasiHistoryModel;
    private WorkflowStep $workflowStepModel;
    private Transaksi $transaksiModel;

    // Mapping for user-friendly short labels (Gaptek-friendly)
    private array $shortLabels = [
        'DRAFT'             => '📁 Draft',
        'PENGECEKAN_SERTIFIKAT' => '🔎 Cek',
        'PEMBAYARAN_PAJAK'   => '💰 Pajak',
        'VALIDASI_PAJAK'     => '✅ Valid',
        'PENDAFTARAN'       => '📝 Daftar',
        'PEMERIKSAAN_BPN'    => '🏛️ BPN',
        'SELESAI'           => '🎉 OK',
        'DISERAHKAN'        => '🤝 Serah',
        'BATAL'             => '❌ Batal',
        'DITUTUP'           => '🔒 Tutup',
        'REVIEW'            => '👀 Review',
        'PERBAIKAN'         => '🔧 Perbaikan'
    ];

    // SLA Exclusion: Roles 3,4,5,6,7,8 are excluded from overdue tracking
    public const SLA_EXCLUDED_BEHAVIOR_ROLES = [3, 4, 5, 6, 7, 8];

    // CMS Row IDs for Branding
    private const CMS_ID_NAME = 13;
    private const CMS_ID_ADDRESS = 20;
    private const CMS_ID_PHONE = 21;

    public function __construct()
    {
        $this->registrasiModel = new Registrasi();
        $this->registrasiHistoryModel = new RegistrasiHistory();
        $this->workflowStepModel = new WorkflowStep();
        $this->transaksiModel = new Transaksi();
    }

    /**
     * Get CMS Branding & Identity with fallbacks
     */
    public function getCMSBranding(): array
    {
        $cmsBranding = [];
        try {
            $conn = Database::getInstance();
            
            // Fetch multiple values in one query if possible, or sequential
            $ids = [self::CMS_ID_NAME, self::CMS_ID_ADDRESS, self::CMS_ID_PHONE];
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $conn->prepare("SELECT id, content_value FROM cms_section_content WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            $rows = $stmt->fetchAll();
            
            $mapping = [];
            foreach ($rows as $row) {
                $mapping[(int)$row['id']] = $row['content_value'];
            }

            $cmsBranding['name']    = $mapping[self::CMS_ID_NAME] ?? OFFICE_NAME;
            $cmsBranding['address'] = $mapping[self::CMS_ID_ADDRESS] ?? OFFICE_ADDRESS;
            $cmsBranding['phone']   = $mapping[self::CMS_ID_PHONE] ?? OFFICE_PHONE;
            
        } catch (Exception $e) {
            Logger::error('ReportService::getCMSBranding failed', ['error' => $e->getMessage()]);
            $cmsBranding['name']    = OFFICE_NAME;
            $cmsBranding['address'] = OFFICE_ADDRESS;
            $cmsBranding['phone']   = OFFICE_PHONE;
        }
        return $cmsBranding;
    }

    /**
     * Get summary statistics for a period
     */
    public function getSummary(string $startDate, string $endDate): array
    {
        try {
            $conn = Database::getInstance();
            
            // 1. Registrasi Baru
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM registrasi WHERE created_at BETWEEN ? AND ?");
            $stmt->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            $baru = (int)$stmt->fetch()['total'];

            // 2. Registrasi Ditutup (Behavior 5, 6)
            $stmt = $conn->prepare("
                SELECT COUNT(*) as total 
                FROM registrasi r
                JOIN workflow_steps ws ON ws.id = r.current_step_id
                WHERE ws.behavior_role IN (5, 6)
                  AND r.created_at BETWEEN ? AND ?
            ");
            $stmt->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            $ditutup = (int)$stmt->fetch()['total'];

            // 3. Masih Aktif (Per akhir periode)
            $stmt = $conn->prepare("
                SELECT COUNT(*) as total 
                FROM registrasi r
                JOIN workflow_steps ws ON ws.id = r.current_step_id
                WHERE ws.behavior_role IN (0, 1, 2, 3, 4, 7, 8)
                  AND r.created_at BETWEEN ? AND ?
            ");
            $stmt->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            $aktif = (int)$stmt->fetch()['total'];

            // 4. Keuangan (Tagihan dari Registrasi Baru)
            $stmt = $conn->prepare("
                SELECT SUM(t.total_tagihan) as total_tagihan
                FROM transaksi t
                JOIN registrasi r ON r.id = t.registrasi_id
                WHERE r.created_at BETWEEN ? AND ?
            ");
            $stmt->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            $tagihan = (float)($stmt->fetch()['total_tagihan'] ?? 0);

            // 5. Keuangan (Total Terbayar di periode ini)
            $stmt = $conn->prepare("
                SELECT SUM(nominal_bayar) as total_bayar
                FROM transaksi_history
                WHERE created_at BETWEEN ? AND ?
            ");
            $stmt->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            $terbayar = (float)($stmt->fetch()['total_bayar'] ?? 0);

            return [
                'registrasi_baru' => $baru,
                'registrasi_ditutup' => $ditutup,
                'masih_aktif' => $aktif,
                'total_tagihan' => $tagihan,
                'total_terbayar' => $terbayar
            ];
        } catch (Exception $e) {
            Logger::error('ReportService::getSummary failed', ['error' => $e->getMessage()]);
            return [
                'registrasi_baru' => 0,
                'registrasi_ditutup' => 0,
                'masih_aktif' => 0,
                'total_tagihan' => 0,
                'total_terbayar' => 0
            ];
        }
    }

    /**
     * Get distribution per service
     */
    public function getServiceDistribution(string $startDate, string $endDate): array
    {
        try {
            $conn = Database::getInstance();
            $stmt = $conn->prepare("
                SELECT l.nama_layanan, COUNT(*) as jumlah
                FROM registrasi r
                JOIN layanan l ON l.id = r.layanan_id
                WHERE r.created_at BETWEEN ? AND ?
                GROUP BY l.id, l.nama_layanan
                ORDER BY jumlah DESC
            ");
            $stmt->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            $data = $stmt->fetchAll();

            $total = array_sum(array_column($data, 'jumlah'));
            foreach ($data as &$item) {
                $item['persentase'] = $total > 0 ? round(($item['jumlah'] / $total) * 100, 1) : 0;
            }

            return $data;
        } catch (Exception $e) {
            Logger::error('ReportService::getServiceDistribution failed', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Get complete service distribution (includes zero counts)
     */
    public function getCompleteServiceDistribution(string $startDate, string $endDate): array
    {
        try {
            $conn = Database::getInstance();
            $masterLayanan = $conn->query("SELECT nama_layanan FROM layanan ORDER BY nama_layanan ASC")->fetchAll();
            $distribution = $this->getServiceDistribution($startDate, $endDate);
            
            $allDistribution = [];
            foreach ($masterLayanan as $ml) {
                $found = false;
                foreach ($distribution as $d) {
                    if ($d['nama_layanan'] === $ml['nama_layanan']) {
                        $allDistribution[] = $d;
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $allDistribution[] = ['nama_layanan' => $ml['nama_layanan'], 'jumlah' => 0, 'persentase' => 0];
                }
            }
            return $allDistribution;
        } catch (Exception $e) {
            Logger::error('ReportService::getCompleteServiceDistribution failed', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Get counts per workflow stage
     */
    public function getStageCounts(array $steps, array $berjalan, array $selesai): array
    {
        $allStageCounts = [];
        $merged = array_merge($berjalan, $selesai);
        foreach ($steps as $s) {
            $count = 0;
            foreach ($merged as $row) {
                if (isset($row['durations'][$s['id']])) {
                    $count++;
                }
            }
            $allStageCounts[] = ['label' => $s['label'], 'count' => $count];
        }
        return $allStageCounts;
    }

    /**
     * Count overdue registrations based on SLA
     */
    public function countOverdue(array $steps, array $registrations): int
    {
        $totalOverdue = 0;
        foreach ($registrations as $row) {
            foreach ($steps as $s) {
                if (in_array((int)$s['behavior_role'], self::SLA_EXCLUDED_BEHAVIOR_ROLES)) continue;
                
                if (isset($row['durations'][$s['id']]) && $row['durations'][$s['id']] > (int) $s['sla_days']) {
                    $totalOverdue++;
                    break;
                }
            }
        }
        return $totalOverdue;
    }

    /**
     * Get User Activity Ranking
     */
    public function getUserRankings(string $startDate, string $endDate): array
    {
        try {
            $conn = Database::getInstance();
            if (!$conn) return ['creators' => [], 'updaters' => [], 'collectors' => []];
            
            $range = [$startDate . ' 00:00:00', $endDate . ' 23:59:59'];

            // 1. Pembuat Registrasi
            $stmt = $conn->prepare("
                SELECT u.name, u.role, COUNT(*) as total
                FROM registrasi r
                JOIN users u ON u.id = r.created_by
                WHERE r.created_at BETWEEN ? AND ?
                GROUP BY u.id, u.name, u.role
                ORDER BY total DESC
            ");
            $stmt->execute($range);
            $creators = $stmt->fetchAll() ?: [];

            // 2. Update Status
            $stmt = $conn->prepare("
                SELECT u.name, u.role, COUNT(*) as total
                FROM registrasi_history rh
                JOIN users u ON u.id = rh.user_id
                WHERE rh.status_old_id IS NOT NULL 
                  AND rh.status_new_id IS NOT NULL
                  AND rh.created_at BETWEEN ? AND ?
                GROUP BY u.id, u.name, u.role
                ORDER BY total DESC
            ");
            $stmt->execute($range);
            $updaters = $stmt->fetchAll() ?: [];

            // 3. Transaksi
            $stmt = $conn->prepare("
                SELECT u.name, u.role, COUNT(*) as total_transaksi, SUM(nominal_bayar) as total_nominal
                FROM transaksi_history th
                JOIN users u ON u.id = th.created_by
                WHERE th.created_at BETWEEN ? AND ?
                GROUP BY u.id, u.name, u.role
                ORDER BY total_transaksi DESC
            ");
            $stmt->execute($range);
            $collectors = $stmt->fetchAll() ?: [];

            return [
                'creators' => $creators,
                'updaters' => $updaters,
                'collectors' => $collectors
            ];
        } catch (Exception $e) {
            Logger::error('ReportService::getUserRankings failed', ['error' => $e->getMessage()]);
            return ['creators' => [], 'updaters' => [], 'collectors' => []];
        }
    }

    /**
     * The Matrix Timeline (Complex) - Split into Berjalan & Selesai
     * Fixed N+1 Query Problem by pre-fetching history in batch.
     */
    public function getMatrixTimeline(string $startDate, string $endDate): array
    {
        try {
            $conn = Database::getInstance();
            $range = [$startDate . ' 00:00:00', $endDate . ' 23:59:59'];

            // Get all workflow steps ordered by sort_order
            $stepStmt = $conn->query("SELECT * FROM workflow_steps ORDER BY sort_order ASC");
            $allSteps = $stepStmt->fetchAll();
            
            $stepsAktif = [];
            $stepsSelesai = [];
            foreach ($allSteps as $s) {
                $stepData = [
                    'id' => $s['id'],
                    'label' => $s['label'],
                    'short' => $this->shortLabels[$s['step_key']] ?? $s['label'],
                    'sla_days' => $s['sla_days'],
                    'behavior_role' => $s['behavior_role']
                ];
                
                if (in_array((int)$s['behavior_role'], [0, 1, 2, 3, 4, 7, 8])) {
                    $stepsAktif[] = $stepData;
                }
                // Selesai gets all steps so we can see the full journey including step 5 and 6
                $stepsSelesai[] = $stepData;
            }
            
            // Get all registrations CREATED strictly within this period
            $stmt = $conn->prepare("
                SELECT DISTINCT r.id, r.nomor_registrasi, k.nama as klien_nama, l.nama_layanan, 
                       r.current_step_id, r.target_completion_at, ws.behavior_role, r.created_at, r.selesai_batal_at
                FROM registrasi r
                JOIN klien k ON k.id = r.klien_id
                JOIN layanan l ON l.id = r.layanan_id
                JOIN workflow_steps ws ON ws.id = r.current_step_id
                WHERE r.created_at BETWEEN ? AND ?
                ORDER BY r.created_at DESC
            ");
            $stmt->execute([$range[0], $range[1]]);
            $registrasi = $stmt->fetchAll();

            if (empty($registrasi)) {
                 return ['steps_aktif' => $stepsAktif, 'steps_selesai' => $stepsSelesai, 'berjalan' => [], 'selesai' => []];
            }

            // --- OPTIMIZATION: BATCH FETCH HISTORY (Fix N+1) ---
            $regIds = array_column($registrasi, 'id');
            $placeholders = implode(',', array_fill(0, count($regIds), '?'));
            $hStmt = $conn->prepare("
                SELECT rh.registrasi_id, rh.status_new_id, rh.created_at, u.name as user_name
                FROM registrasi_history rh
                LEFT JOIN users u ON u.id = rh.user_id
                WHERE rh.registrasi_id IN ($placeholders)
                ORDER BY rh.registrasi_id, rh.created_at ASC
            ");
            $hStmt->execute($regIds);
            $allHistory = $hStmt->fetchAll();
            
            $historyByReg = [];
            foreach ($allHistory as $h) {
                $historyByReg[$h['registrasi_id']][] = $h;
            }
            // --------------------------------------------------

            $berjalan = [];
            $selesai = [];

            foreach ($registrasi as $reg) {
                $regId = (int)$reg['id'];
                $behavior = (int)$reg['behavior_role'];
                
                $history = $historyByReg[$regId] ?? [];

                $stepDurations = [];
                $lastUser = '-';
                for ($i = 0; $i < count($history); $i++) {
                    $stepId = (int)$history[$i]['status_new_id'];
                    $start = strtotime($history[$i]['created_at']);
                    $lastUser = $history[$i]['user_name'] ?? $lastUser;
                    
                    if ($i + 1 < count($history)) {
                        $end = strtotime($history[$i + 1]['created_at']);
                    } else {
                        $end = in_array($behavior, [5, 6]) ? $start : time();
                    }
                    
                    $days = round(($end - $start) / 86400, 1);
                    if (!isset($stepDurations[$stepId])) $stepDurations[$stepId] = 0;
                    $stepDurations[$stepId] += $days;
                }

                $totalDays = array_sum($stepDurations);

                $rowData = [
                    'id' => $regId,
                    'nomor' => $reg['nomor_registrasi'],
                    'klien' => $reg['klien_nama'],
                    'layanan' => $reg['nama_layanan'],
                    'durations' => $stepDurations,
                    'total_days' => $totalDays,
                    'last_user' => $lastUser,
                    'current_step' => (int)$reg['current_step_id'],
                    'target' => $reg['target_completion_at'],
                    'created_at' => $reg['created_at'],
                    'selesai_at' => $reg['selesai_batal_at']
                ];

                if (in_array($behavior, [5, 6])) {
                    $selesai[] = $rowData;
                } else {
                    $berjalan[] = $rowData;
                }
            }

            return [
                'steps_aktif' => $stepsAktif,
                'steps_selesai' => $stepsSelesai,
                'berjalan' => $berjalan,
                'selesai' => $selesai
            ];
        } catch (Exception $e) {
            Logger::error('ReportService::getMatrixTimeline failed', ['error' => $e->getMessage()]);
            return [
                'steps_aktif' => [], 
                'steps_selesai' => [], 
                'berjalan' => [], 
                'selesai' => []
            ];
        }
    }
}

