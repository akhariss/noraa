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

    /**
     * Show tracking page (search by nomor registrasi)
     */
    public function tracking(): void
    {
        // Handle POST request for tracking search
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->searchRegistrasiByNomor();
            return;
        }
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
            if (!$this->checkRateLimit('tracking_search')) {
                http_response_code(429);
                echo json_encode(['success' => false, 'message' => 'Terlalu banyak permintaan. Silakan tunggu beberapa saat.']);
                return;
            }

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
            // Rate limiting check
            if (!$this->checkRateLimit('tracking_verify')) {
                http_response_code(429);
                echo json_encode(['success' => false, 'message' => 'Terlalu banyak percobaan. Silakan tunggu beberapa saat.']);
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
                // Log failed attempt
                logSecurityEvent('FAILED_VERIFICATION', [
                    'registrasi_id' => $registrasiId,
                    'attempted_code' => $phoneCode,
                    'correct_code' => $last4Phone,
                    'phone' => $klien['hp']
                ]);

                echo json_encode(['success' => false, 'message' => 'Kode verifikasi salah. 4 digit terakhir nomor HP tidak sesuai.']);
                return;
            }

            // Generate secure tracking token
            $trackingToken = generateTrackingToken($registrasiId, $registrasi['verification_code']);

            // Update database with token
            $this->registrasiModel->update($registrasiId, ['tracking_token' => $trackingToken]);

            // Verification successful - return token and data
            echo json_encode([
                'success' => true,
                'message' => 'Verifikasi berhasil',
                'data' => [
                    'token' => $trackingToken,
                    'id' => $registrasi['id'],
                    'nomor_registrasi' => $registrasi['nomor_registrasi'],
                    'klien_nama' => $registrasi['klien_nama'],
                    'layanan' => $registrasi['nama_layanan'],
                    'status' => $registrasi['status'],
                    'status_label' => STATUS_LABELS[$registrasi['status']] ?? $registrasi['status'],
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

        if (empty($token)) {
            header('Location: ' . APP_URL . '/index.php?gate=lacak');
            exit;
        }

        // Verify token
        $tokenData = verifyTrackingToken($token);

        if (!$tokenData) {
            // Invalid or expired token
            logSecurityEvent('INVALID_TOKEN_ACCESS', ['token' => substr($token, 0, 20) . '...']);
            http_response_code(403);
            echo '<h1>Akses Ditolak</h1>';
            echo '<p>Token tidak valid atau sudah kadaluarsa (max 24 jam).</p>';
            echo '<p><a href="' . APP_URL . '/index.php?gate=lacak">← Kembali ke Lacak Registrasi</a></p>';
            exit;
        }

        // Get registrasi by ID from token
        $registrasi = $this->registrasiModel->findById($tokenData['id']);

        if (!$registrasi) {
            http_response_code(404);
            echo '<h1>Registrasi Tidak Ditemukan</h1>';
            echo '<p><a href="' . APP_URL . '/index.php?gate=lacak">← Kembali ke Lacak Registrasi</a></p>';
            exit;
        }

        // Verify token matches database (check if tracking_token exists and matches)
        if (isset($registrasi['tracking_token']) && !empty($registrasi['tracking_token'])) {
            if (!hash_equals($registrasi['tracking_token'], $token)) {
                logSecurityEvent('TOKEN_MISMATCH', [
                    'registrasi_id' => $registrasi['id'],
                    'token_provided' => substr($token, 0, 20) . '...',
                    'token_in_db' => substr($registrasi['tracking_token'], 0, 20) . '...'
                ]);
                http_response_code(403);
                echo '<h1>Akses Ditolak</h1>';
                echo '<p>Token tidak sesuai dengan registrasi.</p>';
                echo '<p>Mungkin Anda perlu verifikasi ulang.</p>';
                echo '<p><a href="' . APP_URL . '/index.php?gate=lacak">← Kembali ke Lacak Registrasi</a></p>';
                exit;
            }
        }
        else {
            // No token in database - verify using verification_code instead (fallback for old data)
            if (!isset($registrasi['verification_code']) || empty($registrasi['verification_code'])) {
                logSecurityEvent('NO_VERIFICATION_DATA', ['registrasi_id' => $registrasi['id']]);
                http_response_code(403);
                echo '<h1>Akses Ditolak</h1>';
                echo '<p>Data verifikasi tidak tersedia.</p>';
                echo '<p><a href="' . APP_URL . '/index.php?gate=lacak">← Kembali ke Lacak Registrasi</a></p>';
                exit;
            }

            // Verify token contains correct verification code
            $tokenParts = explode('.', $token);
            if (count($tokenParts) !== 2) {
                logSecurityEvent('INVALID_TOKEN_FORMAT', ['registrasi_id' => $registrasi['id']]);
                http_response_code(403);
                echo '<h1>Akses Ditolak</h1>';
                echo '<p>Format token tidak valid.</p>';
                echo '<p><a href="' . APP_URL . '/index.php?gate=lacak">← Kembali ke Lacak Registrasi</a></p>';
                exit;
            }

            $tokenDataDecoded = json_decode(base64_decode($tokenParts[0]), true);
            if (!$tokenDataDecoded || !isset($tokenDataDecoded['code'])) {
                logSecurityEvent('INVALID_TOKEN_DATA', ['registrasi_id' => $registrasi['id']]);
                http_response_code(403);
                echo '<h1>Akses Ditolak</h1>';
                echo '<p>Data token tidak valid.</p>';
                echo '<p><a href="' . APP_URL . '/index.php?gate=lacak">← Kembali ke Lacak Registrasi</a></p>';
                exit;
            }

            if ($tokenDataDecoded['code'] !== $registrasi['verification_code']) {
                logSecurityEvent('VERIFICATION_CODE_MISMATCH', [
                    'registrasi_id' => $registrasi['id'],
                    'expected_code' => $registrasi['verification_code'],
                    'token_code' => $tokenDataDecoded['code']
                ]);
                http_response_code(403);
                echo '<h1>Akses Ditolak</h1>';
                echo '<p>Kode verifikasi tidak sesuai.</p>';
                echo '<p>Silakan verifikasi ulang untuk mendapatkan token baru.</p>';
                echo '<p><a href="' . APP_URL . '/index.php?gate=lacak">← Kembali ke Lacak Registrasi</a></p>';
                exit;
            }

            // Token is valid, save it to database for future use
            $this->registrasiModel->update($registrasi['id'], ['tracking_token' => $token]);
        }

        // Get progress
        $progress = $this->getProgressForPublic($registrasi['status']);

        // Get business history (for process log) - PENTING!
        $registrasiHistoryModel = new RegistrasiHistory();
        $history = $registrasiHistoryModel->getByRegistrasi($registrasi['id']);

        // Show detail page with process log
        require VIEWS_PATH . '/public/registrasi_detail.php';
    }

    /**
     * Get progress for public display
     */
    private function getProgressForPublic(string $currentStatus): array
    {
        $currentOrder = STATUS_ORDER[$currentStatus] ?? 0;
        $progress = [];

        foreach (STATUS_ORDER as $status => $order) {
            $progress[$status] = [
                'label' => STATUS_LABELS[$status],
                'order' => $order,
                'completed' => $order <= $currentOrder,
                'current' => $status === $currentStatus,
                'estimasi' => STATUS_ESTIMASI[$status] ?? '-'
            ];
        }

        return $progress;
    }

    /**
     * Rate limiting helper
     */
    private function checkRateLimit($endpoint): bool
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $limit = 5; // 5 requests
        $window = 60; // per 60 seconds

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $key = 'rate_limit_' . $endpoint;
        $now = time();

        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = ['count' => 1, 'time' => $now];
            return true;
        }

        if ($now - $_SESSION[$key]['time'] > $window) {
            $_SESSION[$key] = ['count' => 1, 'time' => $now];
            return true;
        }

        if ($_SESSION[$key]['count'] >= $limit) {
            return false;
        }

        $_SESSION[$key]['count']++;
        return true;
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
