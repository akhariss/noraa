<?php
declare(strict_types=1);

namespace Modules\Finalisasi;

use Modules\Auth\Controller as AuthController;

/**
 * SK-14: FinalisasiController
 * Slim controller for finalisasi
 */

use Exception;
use App\Domain\Entities\Registrasi;
use App\Domain\Entities\User;
use App\Services\FinalisasiService;

class Controller {
    private Registrasi $registrasiModel;
    private User $userService;
    private FinalisasiService $finalisasiService;

    public function __construct() {
        $this->registrasiModel = new Registrasi();
        $this->userService = new User();
        $this->finalisasiService = new FinalisasiService();
    }

    /**
     * Show finalisasi list (selesai & batal only)
     */
    public function index(): void {
        try {
            // Only Administrator (Owner) can access
            if (!verifyUserRole(ROLE_OWNER)) {
                showErrorPage(403, 'Akses Ditolak', 'Hanya Administrator yang dapat mengakses halaman finalisasi.');
            }

            // Pagination & Filter
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $perPage = 20;
            $filter = $_GET['filter'] ?? 'all'; // all, selesai, batal, ditutup
            $search = $_GET['search'] ?? '';
            $order = $_GET['order'] ?? 'baru'; // baru = terbaru, lama = terlama
            if (!in_array($order, ['baru', 'lama'], true)) {
                $order = 'baru';
            }

            // Get finalisasi list with pagination
            $result = $this->finalisasiService->getFinalisasiList($page, $perPage, $filter, $search, $order);

            $currentUser = getCurrentUser();
            $auth = new AuthController(); // Keep it for the view if needed, or pass it explicitly

            require VIEWS_PATH . '/dashboard/finalisasi.php';
        } catch (Exception $e) {
            error_log("Finalisasi index error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            showErrorPage(500, 'Error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Review page - Same data as finalisasi but with review.php view
     */
    public function review(): void {
        try {
            if (!verifyUserRole(ROLE_OWNER)) {
                showErrorPage(403, 'Akses Ditolak', 'Hanya Administrator yang dapat mengakses halaman review.');
            }

            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $perPage = 20;
            $filter = $_GET['filter'] ?? 'all';
            $search = $_GET['search'] ?? '';
            $order = $_GET['order'] ?? 'baru';
            if (!in_array($order, ['baru', 'lama'], true)) {
                $order = 'baru';
            }

            $result = $this->finalisasiService->getFinalisasiList($page, $perPage, $filter, $search, $order);

            $currentUser = getCurrentUser();
            $auth = new AuthController();

            require VIEWS_PATH . '/dashboard/review.php';
        } catch (Exception $e) {
            error_log("Review error: " . $e->getMessage());
            showErrorPage(500, 'Error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Tutup registrasi (change status to ditutup)
     */
    public function tutupRegistrasi(): void {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
            // SK-04: Secure Session Retrieval
            $user = getCurrentUser();
            if (!$user) {
                echo json_encode(['success' => false, 'message' => 'Sesi berakhir, silakan login kembali.']);
                return;
            }

            // Only Administrator can tutup
            if ($user['role'] !== ROLE_OWNER) {
                echo json_encode(['success' => false, 'message' => 'Hanya Administrator yang dapat menutup registrasi']);
                return;
            }

            // SK-02: Input Extraction & Sanitization
            $registrasiId = (int)($_POST['registrasi_id'] ?? 0);
            $notes = $_POST['notes'] ?? null;
            
            // Detect user ID (fallback between 'id' and 'user_id' based on session structure)
            $userIdValue = (int)($user['id'] ?? $user['user_id'] ?? 0);

            if ($registrasiId <= 0 || $userIdValue <= 0) {
                echo json_encode(['success' => false, 'message' => "Parameter tidak lengkap (ID: $registrasiId)"]);
                return;
            }

            // SK-14: Orchestrate via Service
            $result = $this->finalisasiService->tutupRegistrasi($registrasiId, $userIdValue, $notes);

            echo json_encode($result);

        } catch (Exception $e) {
            error_log("Tutup registrasi critical error: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Internal Server Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Reopen a finalized case (back to process)
     */
    public function reopen(): void {
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

            // Only Administrator can reopen
            if ($user['role'] !== ROLE_OWNER) {
                echo json_encode(['success' => false, 'message' => 'Hanya Administrator yang dapat membuka kembali registrasi']);
                return;
            }

            $registrasiId = (int)($_POST['registrasi_id'] ?? 0);
            $targetStatus = $_POST['target_status'] ?? 'selesai'; // selesai or back to process
            $notes = $_POST['notes'] ?? null;

            // Detect user ID (fallback between 'id' and 'user_id' based on session structure)
            $userIdValue = (int)($user['id'] ?? $user['user_id'] ?? 0);

            if ($registrasiId <= 0 || $userIdValue <= 0) {
                echo json_encode(['success' => false, 'message' => "Parameter tidak lengkap (ID: $registrasiId, User: $userIdValue)"]);
                return;
            }

            $result = $this->finalisasiService->reopenCase($registrasiId, $userIdValue, $targetStatus, $notes);

            echo json_encode($result);

        } catch (Exception $e) {
            error_log("Reopen error: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
}
