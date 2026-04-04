<?php
declare(strict_types=1);

namespace Modules\Dashboard;

use Modules\Auth\Controller as AuthController;

/**
 * SK-14: DashboardController
 * Slim controller — logic delegated to Services.
 */

use Exception;
use App\Domain\Entities\Registrasi;
use App\Domain\Entities\Klien;
use App\Domain\Entities\Layanan;
use App\Domain\Entities\RegistrasiHistory;
use App\Domain\Entities\AuditLog;
use App\Domain\Entities\Kendala;
use App\Domain\Entities\WorkflowStep;
use App\Services\WorkflowService;
use App\Services\UserService;
use App\Services\BackupService;
use App\Services\CMSEditorService;

class Controller
{
    private Registrasi $registrasiModel;
    private Klien $klienModel;
    private Layanan $layananModel;
    private WorkflowService $workflowService;
    private UserService $userService;
    private BackupService $backupService;
    private AuditLog $auditLogModel;
    private Kendala $kendalaModel;
    private RegistrasiHistory $registrasiHistoryModel;
    private WorkflowStep $workflowStepModel;

    public function __construct()
    {
        $this->registrasiModel = new Registrasi();
        $this->klienModel = new Klien();
        $this->layananModel = new Layanan();
        $this->workflowService = new WorkflowService();
        $this->userService = new UserService();
        $this->backupService = new BackupService();
        $this->auditLogModel = new AuditLog();
        $this->kendalaModel = new Kendala();
        $this->registrasiHistoryModel = new RegistrasiHistory();
        $this->workflowStepModel = new WorkflowStep();
    }

    /**
     * Show dashboard War Room v4.7
     */
    public function index(): void
    {
        // Check for AJAX chart requests
        if (isset($_GET['ajax']) && $_GET['ajax'] === 'chart') {
            $this->chart();
            return;
        }

        $warRoomStats = $this->registrasiModel->getWarRoomStats();
        $overdueTasks = $this->registrasiModel->getOverdueTasks(25);
        $pendingApproval = $this->registrasiModel->getPendingApproval(15);
        $importantTasks = $this->registrasiModel->getCarouselTasks(15, true);
        
        $currentUser = getCurrentUser();
        $auth = new AuthController();

        require VIEWS_PATH . '/dashboard/index.php';
    }

    /**
     * AJAX Endpoint for Chart Data
     */
    private function chart(): void
    {
        header('Content-Type: application/json');
        $period = $_GET['period'] ?? 'monthly';
        $date = $_GET['date'] ?? null;
        $data = $this->registrasiModel->getAnalytics($period, $date);
        echo json_encode($data);
        exit;
    }

    /**
     * Show registrasi detail for finalisasi (with full sections)
     */
    public function showRegistrasiDetailFinalisasi(int $id): void
    {
        $registrasi = $this->registrasiModel->findById($id);

        if (!$registrasi) {
            http_response_code(404);
            echo 'Registrasi tidak ditemukan';
            exit;
        }

        // Finalisasi detail hanya boleh diakses untuk status finalisasi:
        // Diserahkan (5), Ditutup (6), Batal (7)
        if (!in_array((int)($registrasi['behavior_role'] ?? 0), [5, 6, 7], true)) {
            header('Location: ' . APP_URL . '/index.php?gate=finalisasi');
            exit;
        }

        // Elite Milestone Extraction v5.72: Historical Data Recovery
        $history = $this->registrasiHistoryModel->getByRegistrasi($id);
        $tanggalTuntas = null;
        $tanggalDiserahkan = null;

        if (!empty($history)) {
            foreach ($history as $h) {
                // If this is moving TO Selesai or Batal
                if (in_array((int)$h['status_new_behavior_role'], [3, 7])) {
                    if (!$tanggalTuntas) $tanggalTuntas = $h['created_at']; 
                }
                // If this is moving TO Diserahkan
                if ((int)$h['status_new_behavior_role'] === 5) {
                    if (!$tanggalDiserahkan) $tanggalDiserahkan = $h['created_at'];
                }
            }
        }

        // Fallback to table columns if history is empty (migration safety)
        if (!$tanggalTuntas) $tanggalTuntas = $registrasi['selesai_batal_at'] ?? null;
        if (!$tanggalDiserahkan) $tanggalDiserahkan = $registrasi['diserahkan_at'] ?? null;

        $currentUser = getCurrentUser();
        $auth = new AuthController();

        require VIEWS_PATH . '/dashboard/registrasi_detail_finalisasi.php';
    }

    /**
     * Show all registrasi with pagination
     * SERVER-SIDE FILTER + PAGINATION (guidesop.md compliant)
     */
    public function registrasi(): void
    {
        // Get filter & pagination params
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 20;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $filterStatus = isset($_GET['status']) ? trim($_GET['status']) : '';
        $filterLayanan = isset($_GET['layanan']) ? trim($_GET['layanan']) : '';
        $filterFlag = isset($_GET['flag']) ? trim($_GET['flag']) : '';
        $filterOrder = isset($_GET['order']) ? trim($_GET['order']) : 'terbaru';

        // Validasi order mode agar sinkron dengan model
        if (!in_array($filterOrder, ['terbaru', 'terlama', 'nama_asc', 'nama_desc', 'terlambat'])) {
            $filterOrder = 'terbaru';
        }

        // Debug logging
        error_log("=== REGISTRASI PAGINATION (SERVER-SIDE) ===");
        error_log("Page: {$page}, PerPage: {$perPage}");
        error_log("Search: '{$search}', Status: '{$filterStatus}', Layanan: '{$filterLayanan}', Flag: '{$filterFlag}', Order: '{$filterOrder}'");

        // Get total count FIRST (with filters)
        $total = $this->registrasiModel->getCountWithFilters($search, $filterStatus, $filterLayanan, $filterOrder);
        $totalPages = ceil($total / $perPage);

        error_log("Total count: {$total}, Total pages: {$totalPages}");

        // Ensure page is within valid range
        if ($page > $totalPages && $totalPages > 0) {
            $page = $totalPages;
        }

        // Calculate offset
        $offset = ($page - 1) * $perPage;

        error_log("Offset: {$offset}");

        // Get paginated data WITH FILTERS (LIMIT + OFFSET)
        $registrasiPage = $this->registrasiModel->getWithFilters(
            $search,
            $filterStatus,
            $filterLayanan,
            $perPage, // LIMIT 20
            $offset, // OFFSET based on page
            $filterOrder // ORDER mode
        );

        error_log("Retrieved " . count($registrasiPage) . " items");

        // Get flag status for each registrasi
        $registrasiWithFlags = [];
        foreach ($registrasiPage as $p) {
            // Check if any active kendala exists for this registrasi
            try {
                $activeKendala = $this->kendalaModel->getActiveByRegistrasi($p['id']);
                $hasFlag = is_array($activeKendala) && count($activeKendala) > 0;

                // Apply flag filter if set
                if ($filterFlag !== '') {
                    if ($filterFlag === '1' && !$hasFlag)
                        continue; // Skip if flag=1 but no flag
                    if ($filterFlag === '0' && $hasFlag)
                        continue; // Skip if flag=0 but has flag
                }
            }
            catch (Exception $e) {
                error_log("Error checking kendala for registrasi " . $p['id'] . ": " . $e->getMessage());
                $hasFlag = false;

                // Apply flag filter if set
                if ($filterFlag === '1')
                    continue;
            }

            $registrasiWithFlags[] = [
                ...$p,
                'has_flag' => $hasFlag
            ];
        }

        // Elite: Load all steps for filter dropdown instead of hardcoded list
        $allSteps = $this->workflowStepModel->getAll();

        error_log("Final registrasiWithFlags count: " . count($registrasiWithFlags));

        // Get layanan for filter dropdown
        $layanan = $this->layananModel->getAll();

        $currentUser = getCurrentUser();
        $auth = new AuthController();

        require VIEWS_PATH . '/dashboard/registrasi.php';
    }

    /**
     * Show create registrasi form
     */
    public function createRegistrasi(): void
    {
        $klien = $this->klienModel->getAll();
        $layanan = $this->layananModel->getAll();
        $currentUser = getCurrentUser();
        $auth = new AuthController();

        // Load app settings for WA template default text fallback
        /* Autoloaded */
        $cmsServiceL = new CMSEditorService();
        $appSettings = $cmsServiceL->getAppSettings();
        $appPhone = $appSettings['contact']['phone']['value'] ?? '';
        $appAddress = $appSettings['contact']['address']['value'] ?? '';
        $appName = $appSettings['profile']['name']['value'] ?? APP_NAME;

        require VIEWS_PATH . '/dashboard/registrasi_create.php';
    }

    /**
     * Store new registrasi
     */
    public function storeRegistrasi(): void
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        $user = getCurrentUser();

        // Get or create klien (without email)
        $klienData = [
            'nama' => $_POST['klien_nama'] ?? '',
            'hp' => $_POST['klien_hp'] ?? '',
        ];

        $klienId = $this->klienModel->getOrCreate($klienData);

        // Generate nomor registrasi
        $nomorRegistrasi = 'NP-' . date('Ymd') . '-' . str_pad((string)rand(1, 9999), 4, '0', STR_PAD_LEFT);

        // Elite: Fetch initial step ID from database instead of hardcoding
        $requestStatus = $_POST['status'] ?? 'draft';
        $initialStep = $this->workflowStepModel->findByKey($requestStatus);
        
        // Fallback to draft if invalid status provided
        if (!$initialStep) {
            $initialStep = $this->workflowStepModel->findByKey('draft');
        }

        $statusKey = $initialStep['step_key'] ?? 'draft';
        $stepId = (int)($initialStep['id'] ?? 1);

        $catatan = $_POST['catatan'] ?? null;
        $keterangan = $_POST['keterangan'] ?? null;
        $targetDate = $_POST['target_date'] ?? null;
        $targetCompletion = !empty($targetDate) ? $targetDate . ' 23:59:59' : date('Y-m-d H:i:s', strtotime('+2 months +5 days'));

        $registrasiData = [
            'klien_id'             => $klienId,
            'layanan_id'           => (int)($_POST['layanan_id'] ?? 1),
            'nomor_registrasi'     => $nomorRegistrasi,
            'status'               => $statusKey,
            'current_step_id'      => $stepId,
            'target_completion_at' => $targetCompletion,
            'keterangan'           => $keterangan,
            'catatan_internal'     => $catatan
        ];

        $registrasiId = $this->registrasiModel->create($registrasiData);

        // Create FIRST history entry
        $this->registrasiHistoryModel->create([
            'registrasi_id'      => $registrasiId,
            'status_old_id'      => null,
            'status_new_id'      => $stepId,
            'catatan'            => $catatan,
            'flag_kendala_active' => 0,
            'flag_kendala_tahap' => null,
            'user_id'            => $user['user_id'],
            'ip_address'         => $_SERVER['REMOTE_ADDR'] ?? null
        ]);

        // Log audit
        $this->auditLogModel->create(
            $user['user_id'],
            $user['role'],
            AUDIT_CREATE,
            $registrasiId,
            null,
            json_encode($registrasiData)
        );

        // Get klien and layanan data for response
        $klien = $this->klienModel->findById($klienId);
        $layanan = $this->layananModel->findById((int)($_POST['layanan_id'] ?? 1));

        // Generate secure tracking token
        $verificationCode = substr(preg_replace('/[^0-9]/', '', $klien['hp']), -4);
        $trackingToken = generateTrackingToken($registrasiId, $verificationCode);

        // Save token to database
        $this->registrasiModel->update($registrasiId, ['tracking_token' => $trackingToken]);

        echo json_encode([
            'success' => true,
            'message' => 'Registrasi berhasil dibuat',
            'registrasi_id' => $registrasiId,
            'nomor_registrasi' => $nomorRegistrasi,
            'klien_nama' => $klien['nama'],
            'klien_hp' => $klien['hp'],
            'layanan' => $layanan['nama_layanan'] ?? 'Layanan',
            'tracking_url' => APP_URL . '/index.php?gate=detail&token=' . urlencode($trackingToken)
        ]);
    }

    /**
     * Show registrasi detail
     */
    public function showRegistrasi(int $id): void
    {
        $registrasi = $this->registrasiModel->findById($id);

        if (!$registrasi) {
            http_response_code(404);
            echo 'Registrasi tidak ditemukan';
            exit;
        }

        $progress = $this->workflowService->getProgress($id);
        
        // Elite: Load all available steps for the dropdown, sorted correctly
        $availableSteps = $this->workflowStepModel->getAll();
        
        $activeKendala = $this->kendalaModel->getActiveByRegistrasi($id);
        $hasActiveKendala = is_array($activeKendala) && count($activeKendala) > 0;

        try {
            $history = $this->registrasiHistoryModel->getByRegistrasi($id);
        }
        catch (Exception $e) {
            error_log("History error: " . $e->getMessage());
            $history = [];
        }

        $layanan = $this->layananModel->getAll();
        $currentUser = getCurrentUser();
        $auth = new AuthController();

        $cmsService = new CMSEditorService();
        $appSettings = $cmsService->getAppSettings();
        
        // Elite logic: behavior_role 2 (In Process) can still be cancelled
        // behavior_role 3 (Success), 4 (Archive), 5 (Failure) are terminals
        $isTerminal = in_array((int)($registrasi['behavior_role'] ?? 0), [3, 4, 5]);
        $canBatal = !$isTerminal;

        require VIEWS_PATH . '/dashboard/registrasi_detail.php';
    }

    /**
     * Show full history for a registration
     */
    public function showRegistrasiHistory(int $id): void
    {
        $registrasi = $this->registrasiModel->findById($id);

        if (!$registrasi) {
            http_response_code(404);
            echo 'Registrasi tidak ditemukan';
            exit;
        }

        try {
            $history = $this->registrasiHistoryModel->getByRegistrasi($id);
        }
        catch (Exception $e) {
            error_log("History error: " . $e->getMessage());
            $history = [];
        }

        $currentUser = getCurrentUser();
        $auth = new AuthController();

        require VIEWS_PATH . '/dashboard/registrasi_history.php';
    }

    /**
     * Update status
     */
    public function updateStatus(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
            $user = getCurrentUser();
            if (!$user) throw new Exception('User tidak terautentikasi');

            $registrasiId = (int)($_POST['registrasi_id'] ?? 0);
            $newStatusKey = $_POST['status'] ?? ''; 
            $catatan = $_POST['catatan'] ?? null;
            $keterangan = $_POST['keterangan'] ?? null;
            $flagKendala = isset($_POST['flag_kendala']) && $_POST['flag_kendala'] === '1';

            if ($registrasiId <= 0) throw new Exception('ID registrasi tidak valid');

            $registrasi = $this->registrasiModel->findById($registrasiId);
            if (!$registrasi) throw new Exception('Registrasi tidak ditemukan');

            // Find step to ensure validity
            $stepId = null;
            if (!empty($newStatusKey)) {
                $step = $this->workflowStepModel->findByKey($newStatusKey);
                if (!$step) throw new Exception('Status tujuan tidak valid');
                $stepId = (int)$step['id'];
            }

            // Dynamic Variable Processing (v4.52 - Integer ID Sync)
            // If diserahkan, try to fetch template using current step ID
            if ($newStatusKey === 'diserahkan' && isset($_POST['penerima'])) {
                $penerima = trim($_POST['penerima']);
                
                // Force replacement: In case the user sent the raw template tag {penerima}
                if (!empty($catatan)) {
                    $catatan = str_replace(['{penerima}', '[penerima]', '{penerima_name}'], $penerima, $catatan);
                }
                
                $tplModel = new \App\Domain\Entities\NoteTemplate();
                
                // Fetch template by workflow step ID (Optimized)
                if ($stepId && empty($catatan)) {
                    $tpl = $tplModel->getByWorkflowStepId($stepId);
                    if ($tpl && !empty($tpl['template_body'])) {
                        // REPLACE: prioritize CMS template if catatannya masih kosong
                        $catatan = str_replace(['[penerima]', '{penerima}'], $penerima, $tpl['template_body']);
                    } else {
                        // FALLBACK: only if CMS template is empty
                        $catatan = "Berkas telah diserahkan dan diterima oleh: " . $penerima;
                    }
                }
            }

            // Delegating core logic to WorkflowService 'Elite Engine'
            $result = $this->workflowService->updateStatus(
                $registrasiId,
                $newStatusKey, 
                $user['user_id'],
                $user['role'],
                $catatan,
                $flagKendala,
                $keterangan,
                $stepId
            );

            echo json_encode($result);

        }
        catch (Exception $e) {
            error_log("Update status error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Update klien data
     */
    public function updateKlien(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
            $user = getCurrentUser();

            if (!$user) {
                echo json_encode(['success' => false, 'message' => 'User tidak terautentikasi']);
                return;
            }

            $registrasiId = (int)($_POST['registrasi_id'] ?? 0);
            $nama = $_POST['nama'] ?? '';
            $hp = $_POST['hp'] ?? '';
            $nomorRegistrasi = $_POST['nomor_registrasi'] ?? '';
            $targetDate = $_POST['target_date'] ?? '';
            $keterangan = $_POST['keterangan'] ?? '';

            if ($registrasiId <= 0 || empty($nama) || empty($hp)) {
                echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
                return;
            }

            // Get registrasi to find klien_id
            $registrasi = $this->registrasiModel->findById($registrasiId);
            if (!$registrasi) {
                echo json_encode(['success' => false, 'message' => 'Registrasi tidak ditemukan']);
                return;
            }

            // Update klien
            $this->klienModel->update($registrasi['klien_id'], [
                'nama' => $nama,
                'hp' => $hp
            ]);

            // Update registration-specific fields
            $this->registrasiModel->update($registrasiId, [
                'nomor_registrasi'     => $nomorRegistrasi,
                'target_completion_at' => !empty($targetDate) ? $targetDate . ' 23:59:59' : null,
                'keterangan'           => $keterangan
            ]);

            // record history for the change
            $this->registrasiHistoryModel->create([
                'registrasi_id'           => (int)$registrasiId,
                'status_old_id'           => (int)$registrasi['current_step_id'],
                'status_new_id'           => (int)$registrasi['current_step_id'], // No status change
                'target_completion_at_old' => $registrasi['target_completion_at'],
                'target_completion_at_new' => !empty($targetDate) ? $targetDate . ' 23:59:59' : null,
                'catatan'                 => 'Update administratif data klien/SLA.',
                'keterangan'              => null,
                'user_id'                 => $user['user_id'],
                'ip_address'              => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ]);

            // Final response with reliability check (v4.61)
            $result = true;
            echo json_encode([
                'success' => $result,
                'message' => $result ? 'Status registrasi berhasil diperbarui.' : 'Gagal memperbarui status registrasi.'
            ]);

        }
        catch (Exception $e) {
            error_log("Update klien error: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Toggle kendala
     */
    public function toggleKendala(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        $user = getCurrentUser();

        $registrasiId = (int)($_POST['registrasi_id'] ?? 0);
        $tahap = $_POST['tahap'] ?? '';
        $catatan = $_POST['deskripsi'] ?? ''; // Form still sends 'deskripsi'

        $result = $this->workflowService->toggleKendala(
            $registrasiId,
            $tahap,
            $catatan,
            $user['user_id'],
            $user['role']
        );

        echo json_encode($result);
    }

    /**
     * Lock/Unlock registrasi
     */
    public function toggleLock(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        $user = getCurrentUser();

        $registrasiId = (int)($_POST['registrasi_id'] ?? 0);
        $action = $_POST['action'] ?? 'lock';

        if ($action === 'lock') {
            $result = $this->workflowService->lockRegistrasi($registrasiId, $user['user_id'], $user['role']);
        }
        else {
            $result = $this->workflowService->unlockRegistrasi($registrasiId, $user['user_id'], $user['role']);
        }

        echo json_encode($result);
    }

    /**
     * Show users management (Notaris only)
     */
    public function users(): void
    {
        requireRole(ROLE_OWNER);

        // GET request - show view
        $users = $this->userService->getAllUsers();

        // Sort: Administrator (Owner) first, then Staff
        usort($users, function ($a, $b) {
            $order = [ROLE_OWNER => 0, ROLE_STAFF => 1];
            $aOrder = $order[$a['role'] ?? ''] ?? 9;
            $bOrder = $order[$b['role'] ?? ''] ?? 9;
            return $aOrder - $bOrder;
        });

        $currentUser = getCurrentUser();
        $auth = new AuthController();

        require VIEWS_PATH . '/dashboard/users.php';
    }

    /**
     * Update user (Notaris only)
     */
    public function updateUser(): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                return;
            }

            requireRole(ROLE_OWNER);
            $user = getCurrentUser();

            $userId = (int)($_POST['user_id'] ?? 0);
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'admin';

            if ($userId <= 0 || empty($username)) {
                echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
                return;
            }

            $updateData = [
                'username' => $username, 
                'name' => $_POST['name'] ?? $username,
                'role' => $role
            ];
            if (!empty($password)) {
                $updateData['password'] = $password;
            }

            $result = $this->userService->updateUser($userId, $updateData, $user['user_id'], $user['role']);

            if ($result === null) {
                echo json_encode(['success' => false, 'message' => 'Update failed - service returned null']);
            }
            else {
                echo json_encode($result);
            }

        }
        catch (Exception $e) {
            error_log("Update user error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Create user (Notaris only)
     */
    public function createUser(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        requireRole(ROLE_OWNER);
        $user = getCurrentUser();

        $userData = [
            'username' => $_POST['username'] ?? '',
            'name' => $_POST['name'] ?? $_POST['username'] ?? '',
            'password' => $_POST['password'] ?? '',
            'role' => $_POST['role'] ?? ROLE_STAFF
        ];

        $result = $this->userService->createUser($userData, $user['user_id'], $user['role']);
        echo json_encode($result);
    }

    /**
     * Delete user (Notaris only)
     */
    public function deleteUser(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        requireRole(ROLE_OWNER);
        $user = getCurrentUser();

        $userId = (int)($_POST['user_id'] ?? 0);
        $result = $this->userService->deleteUser($userId, $user['user_id'], $user['role']);

        echo json_encode($result);
    }

    /**
     * Show CMS management (Notaris only)
     */
    public function cms(): void
    {
        requireRole(ROLE_OWNER);

        $cmsService = new CMSEditorService();
        $pages = $cmsService->getAllPages();
        $currentUser = getCurrentUser();
        $auth = new AuthController();

        require VIEWS_PATH . '/dashboard/cms.php';
    }

    /**
     * Update CMS content (Notaris only)
     */
    public function updateCMS(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        requireRole(ROLE_OWNER);
        $user = getCurrentUser();

        $page = $_POST['page'] ?? '';
        $content = $_POST['content'] ?? '';

        $cmsService = new CMSEditorService();
        $result = $cmsService->updateContent($page, $content, $user['user_id'], $user['role']);
        echo json_encode($result);
    }

    /**
     * Show backup management (Notaris only)
     */
    public function backups(): void
    {
        requireRole(ROLE_OWNER);

        // Handle download
        if (isset($_GET['file'])) {
            $this->downloadBackup();
            exit;
        }

        $backups = $this->backupService->listBackups();
        $currentUser = getCurrentUser();
        $auth = new AuthController();

        require VIEWS_PATH . '/dashboard/backups.php';
    }

    /**
     * Create backup (Notaris only)
     */
    public function createBackup(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        requireRole(ROLE_OWNER);
        $user = getCurrentUser();

        $type = $_POST['type'] ?? 'database';

        if ($type === 'database') {
            $result = $this->backupService->createBackup($user['user_id'], $user['role']);
        }
        else {
            $result = $this->backupService->createSiteBackup($user['user_id'], $user['role']);
        }

        echo json_encode($result);
    }

    /**
     * Download backup (Notaris only)
     */
    public function downloadBackup(): void
    {
        requireRole(ROLE_OWNER);

        $filename = $_GET['file'] ?? '';
        $this->backupService->downloadBackup($filename);
    }

    /**
     * Delete backup (Notaris only)
     */
    public function deleteBackup(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        requireRole(ROLE_OWNER);
        $user = getCurrentUser();

        $filename = $_POST['filename'] ?? '';
        $result = $this->backupService->deleteBackup($filename, $user['user_id'], $user['role']);

        echo json_encode($result);
    }

    /**
     * Show audit logs (Notaris only) with pagination
     */
    public function auditLogs(): void
    {
        requireRole(ROLE_OWNER);

        // Pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 20;

        $allLogs = $this->auditLogModel->getAll(1000); // Get more for pagination
        $total = count($allLogs);
        $totalPages = ceil($total / $perPage);

        // Slice for pagination
        $offset = ($page - 1) * $perPage;
        $logs = array_slice($allLogs, $offset, $perPage);

        $logsByAction = $this->auditLogModel->getCountByAction();
        $currentUser = getCurrentUser();
        $auth = new AuthController();

        require VIEWS_PATH . '/dashboard/audit_logs.php';
    }

    /**
     * Handle POST requests for users gate (dispatch to create/update/delete).
     */
    public function handleUserPost(): void
    {
        // Verify CSRF for POST requests
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            logSecurityEvent('CSRF_VALIDATION_FAILED', ['action' => 'user_management']);
            http_response_code(403);
            die('Forbidden');
        }

        $action = $_POST['action'] ?? '';

        switch ($action) {
            case 'create':
                $this->createUser();
                break;
            case 'update':
                $this->updateUser();
                break;
            case 'delete':
                $this->deleteUser();
                break;
            default:
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Action tidak valid']);
        }
    }
}
