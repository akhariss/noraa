<?php
declare(strict_types=1);

namespace Modules\Main;

/**
 * SK-14: PublicController
 * Slim controller for public-facing pages
 */

use Exception;
use App\Adapters\Database;
use App\Domain\Entities\Registrasi;
use App\Domain\Entities\Klien;
use App\Domain\Entities\AuditLog;
use App\Domain\Entities\RegistrasiHistory;

class Controller
{
    private Registrasi $registrasiModel;
    private Klien $klienModel;
    private AuditLog $auditLogModel;

    public function __construct()
    {
        $this->registrasiModel = new Registrasi();
        $this->klienModel = new Klien();
        $this->auditLogModel = new AuditLog();
    }

    /**
     * Show homepage (company profile)
     * Pillar 3.1: Graceful error handling - no crash if DB empty
     */
    public function home(): void
    {
        // NOTE: CMS feature partially implemented - load data directly from database
        $homepageData = $this->loadHomepageData();

        // Pass data to view
        require VIEWS_PATH . '/company_profile/home.php';
    }

    /**
     * Load homepage data directly from CMS tables
     * Temporary solution until CMSService is implemented
     */
    private function loadHomepageData(): array
    {
        try {
            $conn = Database::getInstance();
            
            if (!$conn) {
                return [];
            }

            // Query: Get all sections for home page
            $stmt = $conn->prepare("
                SELECT ps.id, ps.section_key
                FROM cms_page_sections ps
                JOIN cms_pages p ON ps.page_id = p.id
                WHERE p.page_key = 'home' AND ps.is_active = 1
                ORDER BY ps.section_order
            ");
            $stmt->execute();
            $sections = $stmt->fetchAll();

            $homepageData = [];

            foreach ($sections as $section) {
                $sectionKey = $section['section_key'];
                $sectionId = $section['id'];
                
                // Initialize section data
                $homepageData[$sectionKey] = [
                    'section' => [],
                    'content' => [],
                    'items' => []
                ];

                // Get section content
                $contentStmt = $conn->prepare("
                    SELECT content_key, content_value
                    FROM cms_section_content
                    WHERE section_id = ? 
                    ORDER BY sort_order
                ");
                $contentStmt->execute([$sectionId]);
                $contents = $contentStmt->fetchAll();

                foreach ($contents as $content) {
                    $homepageData[$sectionKey]['content'][] = [
                        'content_key' => $content['content_key'],
                        'content_value' => $content['content_value']
                    ];
                }

                // Get section items (buttons, layanan, testimoni, etc.)
                $itemsStmt = $conn->prepare("
                    SELECT id, item_type, title, description, extra_data, sort_order
                    FROM cms_section_items
                    WHERE section_id = ? AND is_active = 1
                    ORDER BY sort_order
                ");
                $itemsStmt->execute([$sectionId]);
                $items = $itemsStmt->fetchAll();

                foreach ($items as $item) {
                    $homepageData[$sectionKey]['items'][] = [
                        'id' => $item['id'],
                        'item_type' => $item['item_type'],
                        'title' => $item['title'],
                        'description' => $item['description'],
                        'extra_data' => $item['extra_data'],
                        'sort_order' => $item['sort_order']
                    ];
                }
            }

            return $homepageData;
        } catch (Exception $e) {
            error_log("Failed to load homepage data: " . $e->getMessage());
            return [];
        }
    }

    public function tracking(): void
    {
        // Handle POST request for tracking search
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->searchRegistrasiByNomor();
            return;
        }
        
        // Load CMS data for header/footer
        $homepageData = $this->loadHomepageData();
        
        require VIEWS_PATH . '/public/tracking.php';
    }

    /**
     * Search registrasi by nomor registrasi (SECURE: No phone number exposed)
     */
    public function searchRegistrasiByNomor(): void
    {
        header('Content-Type: application/json');

        try {
            // Rate limiting check
            // Search is now unrestricted as per USER request
            // Only verification step is rate-limited

            $nomorRegistrasi = isset($_POST['nomor_registrasi']) ? trim($_POST['nomor_registrasi']) : '';

            if (empty($nomorRegistrasi)) {
                echo json_encode(['success' => false, 'message' => 'Nomor registrasi wajib diisi']);
                return;
            }

            // Sanitize input
            $nomorRegistrasi = htmlspecialchars($nomorRegistrasi, ENT_QUOTES, 'UTF-8');

            $registrasi = $this->registrasiModel->findByNomorRegistrasi($nomorRegistrasi);

            if (!$registrasi) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Nomor registrasi tidak ditemukan'
                ]);
                return;
            }

            // Return minimal data - require verification for full details
            echo json_encode([
                'success' => true,
                'message' => 'Nomor registrasi ditemukan. Silakan verifikasi dengan 4 digit terakhir nomor HP.',
                'data' => [
                    'registrasi_id' => $registrasi['id'],
                    'nomor_registrasi' => $registrasi['nomor_registrasi'],
                    'requires_verification' => true
                ]
            ]);

        }
        catch (Exception $e) {
            error_log("Search tracking error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.']);
        }
    }

    /**
     * Verify 4-digit phone code (SECURE: Returns encrypted token for access)
     */
    public function verifyTracking(): void
    {
        header('Content-Type: application/json');

        try {
            // Only check if already blocked. Do NOT increment yet.
            if (\App\Security\RateLimiter::isBlocked('tracking_verify')) {
                http_response_code(429);
                echo json_encode(['success' => false, 'message' => 'Terlalu banyak percobaan. Silakan tunggu 30 detik.']);
                return;
            }

            $registrasiId = isset($_POST['registrasi_id']) ? (int)$_POST['registrasi_id'] : 0;
            $phoneCode = isset($_POST['phone_code']) ? trim($_POST['phone_code']) : '';

            if (empty($phoneCode) || strlen($phoneCode) !== 4) {
                echo json_encode(['success' => false, 'message' => 'Kode verifikasi harus 4 digit angka']);
                return;
            }

            if ($registrasiId <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID registrasi tidak valid']);
                return;
            }

            $registrasi = $this->registrasiModel->findById($registrasiId);

            if (!$registrasi) {
                echo json_encode(['success' => false, 'message' => 'Registrasi tidak ditemukan']);
                return;
            }

            // Get klien data
            $klien = $this->klienModel->findById($registrasi['klien_id']);

            if (!$klien) {
                echo json_encode(['success' => false, 'message' => 'Data klien tidak ditemukan']);
                return;
            }

            // Get last 4 digits of phone (remove non-numeric characters)
            $cleanPhone = preg_replace('/[^0-9]/', '', $klien['hp']);
            $last4Phone = substr($cleanPhone, -4);

            // Verify code
            if ($phoneCode !== $last4Phone) {
                // Log failed attempt and penalize (increment rate limiter)
                \App\Security\RateLimiter::checkGlobal('tracking_verify');
                
                logSecurityEvent('FAILED_VERIFICATION', [
                    'registrasi_id' => $registrasiId,
                    'attempted_code' => $phoneCode,
                    'correct_code' => $last4Phone,
                    'phone' => $klien['hp']
                ]);

                echo json_encode(['success' => false, 'message' => 'Kode verifikasi salah. Silakan coba lagi.']);
                return;
            }

            // Ensure we have a verification_code for this registration (Secure Session Bind)
            $vCode = $registrasi['verification_code'];
            if (empty($vCode)) {
                $vCode = bin2hex(random_bytes(8));
                // Critical: Save it FIRST to ensure subsequent reads (like in detail page) see the correct code
                $this->registrasiModel->update($registrasiId, ['verification_code' => $vCode]);
                logSecurityEvent('VCODE_GENERATED', ['registrasi_id' => $registrasiId]);
            }

            // Generate secure tracking token using the guaranteed vCode
            $trackingToken = generateTrackingToken($registrasiId, $vCode);

            // Update database with token
            $this->registrasiModel->update($registrasiId, ['tracking_token' => $trackingToken]);

            // Check payment status
            $transaksi = \App\Adapters\Database::selectOne("SELECT total_tagihan, jumlah_bayar FROM transaksi WHERE registrasi_id = ?", [$registrasiId]);
            $isLunas = false;
            if ($transaksi && $transaksi['total_tagihan'] > 0 && $transaksi['jumlah_bayar'] >= $transaksi['total_tagihan']) {
                $isLunas = true;
            }

            // Estimate
            $estimasi = $registrasi['target_completion_at'] ? date('d M Y', strtotime($registrasi['target_completion_at'])) : '-';

            // Verification successful - return token and data
            echo json_encode([
                'success' => true,
                'message' => 'Verifikasi berhasil',
                'data' => [
                    'token' => $trackingToken,
                    'id' => $registrasi['id'],
                    'nomor_registrasi' => $registrasi['nomor_registrasi'],
                    'klien_nama' => $registrasi['klien_nama'],
                    'klien_hp' => $registrasi['klien_hp'] ?? '-',
                    'keterangan' => $registrasi['keterangan'] ?? '-',
                    'catatan_status' => $registrasi['catatan_internal'] ?? '-',
                    'estimasi_selesai' => $estimasi,
                    'is_lunas' => $isLunas,
                    'layanan' => $registrasi['nama_layanan'],
                    'status' => $registrasi['status'],
                    'status_label' => getStatusLabels()[$registrasi['status']] ?? $registrasi['status'],
                    'behavior_role' => (int)($registrasi['behavior_role'] ?? 0),
                    'status_style' => \App\Domain\Entities\Registrasi::getStatusStyle((int)($registrasi['behavior_role'] ?? 0)),
                    'batal_flag' => (bool)($registrasi['batal_flag'] ?? false),
                    'created_at' => date('d M Y', strtotime($registrasi['created_at'])),
                    'updated_at' => date('d M Y H:i', strtotime($registrasi['updated_at']))
                ]
            ]);

        }
        catch (Exception $e) {
            error_log("Verify tracking error: " . $e->getMessage());
            logSecurityEvent('VERIFY_ERROR', ['error' => $e->getMessage()]);
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.']);
        }
    }

    /**
     * Show registrasi detail for public (SECURE: Requires valid token) + Process Log
     */
    public function showRegistrasi($tokenId = null): void
    {
        // Get token from URL parameter
        $token = isset($_GET['token']) ? trim($_GET['token']) : '';
        
        // Fix: PHP often converts '+' to ' ' in GET params. Restore them for base64.
        $token = str_replace(' ', '+', $token);

        if (empty($token)) {
            header('Location: ' . APP_URL . '/index.php?gate=lacak');
            exit;
        }

        // Step 1: Cryptographic Validation
        $tokenData = verifyTrackingToken($token);
        if (!$tokenData) {
            logSecurityEvent('INVALID_TOKEN_ACCESS', ['token' => substr($token, 0, 15) . '...']);
            http_response_code(403);
            $homepageData = $this->loadHomepageData();
            require VIEWS_PATH . '/public/tracking_error.php';
            exit;
        }

        // Get registrasi by ID from token
        $registrasi = $this->registrasiModel->findById($tokenData['id']);

        if (!$registrasi) {
            http_response_code(404);
            $homepageData = $this->loadHomepageData();
            require VIEWS_PATH . '/public/tracking_error.php';
            exit;
        }

        // Token is cryptographically OK (part 1), but we already checked it at Step 1.
        // No need to repeat verifyTrackingToken($token).

        // Check if token matches the registration's current verification code
        $dbVCode = $registrasi['verification_code'] ?? '';
        if ($tokenData['code'] === $dbVCode) {
            $isTokenValid = true;
            if (($registrasi['tracking_token'] ?? '') !== $token) {
                $this->registrasiModel->update((int)$registrasi['id'], ['tracking_token' => $token]);
            }
        } else {
            logSecurityEvent('VERIFICATION_CODE_MISMATCH', [
                'registrasi_id' => $registrasi['id'],
                'token_code' => $tokenData['code'],
                'db_code' => $dbVCode,
                'token_hint' => substr($token, 0, 10)
            ]);
            http_response_code(403);
            $homepageData = $this->loadHomepageData();
            require VIEWS_PATH . '/public/tracking_error.php';
            exit;
        }

        // Get progress
        $progress = $this->getProgressForPublic($registrasi['status']);

        // Get business history (for process log) - PENTING!
        $registrasiHistoryModel = new RegistrasiHistory();
        $history = $registrasiHistoryModel->getByRegistrasi($registrasi['id']);

        // Load CMS data for header/footer
        $homepageData = $this->loadHomepageData();

        // Show detail page with process log
        require VIEWS_PATH . '/public/registrasi_detail.php';
    }

    /**
     * Get progress for public display
     */
    private function getProgressForPublic(string $currentStatus): array
    {
        $labels = getStatusLabels();
        $orderMap = getStatusOrder();
        $estimasi = getStatusEstimasi();

        $currentOrder = $orderMap[$currentStatus] ?? 0;
        $progress = [];

        foreach ($orderMap as $status => $order) {
            $progress[$status] = [
                'label' => $labels[$status] ?? $status,
                'order' => $order,
                'completed' => $order <= $currentOrder,
                'current' => $status === $currentStatus,
                'estimasi' => $estimasi[$status] ?? '-'
            ];
        }

        return $progress;
    }

    /**
     * Health check endpoint (SK SOP Step 2).
     */
    public function health(): void
    {
        header('Content-Type: application/json');

        try {
            $conn = Database::getInstance();
            $conn->query('SELECT 1');
            $dbStatus = 'up';
        } catch (Exception $e) {
            $dbStatus = 'down';
        }

        echo json_encode([
            'status'        => 'healthy',
            'timestamp'     => time(),
            'timestamp_iso' => date('c'),
            'database'      => $dbStatus,
            'version'       => APP_VERSION,
            'php_version'   => phpversion(),
        ]);
        exit;
    }
}
