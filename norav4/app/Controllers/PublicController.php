<?php
declare(strict_types=1);

namespace App\Controllers;

class PublicController
{
    public function home(): void
    {
        $homepageData = $this->loadHomepageData();
        $title = APP_NAME . ' - Notaris & PPAT Professional';
        $content = VIEWS_PATH . '/public/home.php';
        require VIEWS_PATH . '/public/layout.php';
    }

    private function loadHomepageData(): array
    {
        try {
            $db = \App\Core\Database::get();
            
            // Query: Get all sections for home page
            $stmt = $db->prepare("
                SELECT ps.id, ps.section_key, ps.section_name
                FROM cms_page_sections ps
                JOIN cms_pages p ON ps.page_id = p.id
                WHERE p.page_key = 'home' AND ps.is_active = 1
                ORDER BY ps.section_order
            ");
            $stmt->execute();
            $sections = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $homepageData = [];

            foreach ($sections as $section) {
                $sectionKey = $section['section_key'];
                $sectionId = $section['id'];
                
                $homepageData[$sectionKey] = [
                    'section' => $section,
                    'content' => [],
                    'items' => []
                ];

                // Get section content
                $contentStmt = $db->prepare("
                    SELECT content_key, content_value
                    FROM cms_section_content
                    WHERE section_id = ? 
                    ORDER BY sort_order
                ");
                $contentStmt->execute([$sectionId]);
                $contents = $contentStmt->fetchAll(\PDO::FETCH_ASSOC);

                foreach ($contents as $content) {
                    $homepageData[$sectionKey]['content'][] = [
                        'content_key' => $content['content_key'],
                        'content_value' => $content['content_value']
                    ];
                }

                // Get section items
                $itemsStmt = $db->prepare("
                    SELECT id, item_type, title, description, extra_data, sort_order
                    FROM cms_section_items
                    WHERE section_id = ? AND is_active = 1
                    ORDER BY sort_order
                ");
                $itemsStmt->execute([$sectionId]);
                $items = $itemsStmt->fetchAll(\PDO::FETCH_ASSOC);

                foreach ($items as $item) {
                    $homepageData[$sectionKey]['items'][] = $item;
                }
            }

            return $homepageData;
        } catch (\Exception $e) {
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
        
        $homepageData = $this->loadHomepageData();
        $title = 'Lacak Berkas - ' . APP_NAME;
        $content = VIEWS_PATH . '/public/tracking.php';
        require VIEWS_PATH . '/public/layout.php';
    }

    private function searchRegistrasiByNomor(): void
    {
        header('Content-Type: application/json');
        try {
            $nomorRegistrasi = $_POST['nomor_registrasi'] ?? '';
            if (empty($nomorRegistrasi)) {
                echo json_encode(['success' => false, 'message' => 'Nomor registrasi wajib diisi']);
                return;
            }

            $model = new \App\Models\RegistrasiModel();
            $registrasi = $model->findByNomorRegistrasi($nomorRegistrasi);

            if (!$registrasi) {
                echo json_encode(['success' => false, 'message' => 'Nomor registrasi tidak ditemukan']);
                return;
            }

            echo json_encode([
                'success' => true,
                'data' => [
                    'registrasi_id' => $registrasi['id'],
                    'nomor_registrasi' => $registrasi['nomor_registrasi']
                ]
            ]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Sistem sibuk. Coba lagi nanti.']);
        }
        exit;
    }

    public function verifyTracking(): void
    {
        header('Content-Type: application/json');
        try {
            $registrasiId = (int)($_POST['registrasi_id'] ?? 0);
            $phoneCode = $_POST['phone_code'] ?? '';

            if (strlen($phoneCode) !== 4) {
                echo json_encode(['success' => false, 'message' => 'Masukkan 4 digit nomor HP']);
                return;
            }

            $model = new \App\Models\RegistrasiModel();
            $registrasi = $model->findById($registrasiId);

            if (!$registrasi) {
                echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan']);
                return;
            }

            // Verify phone
            $klienModel = new \App\Models\KlienModel();
            $klien = $klienModel->findById((int)$registrasi['klien_id']);
            
            if (!$klien || empty($klien['hp'])) {
                echo json_encode(['success' => false, 'message' => 'Data kontak klien tidak lengkap']);
                return;
            }
            
            $cleanPhone = preg_replace('/[^0-9]/', '', $klien['hp']);
            if (substr($cleanPhone, -4) !== $phoneCode) {
                echo json_encode(['success' => false, 'message' => 'Kode verifikasi salah']);
                return;
            }

            // Success - Get details
            $estimasi = !empty($registrasi['target_completion_at']) ? date('d M Y', strtotime($registrasi['target_completion_at'])) : '-';
            $latestLog = $model->getLatestLog((int)$registrasi['id']);
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'token' => !empty($registrasi['tracking_token']) ? $registrasi['tracking_token'] : encryptImageId($registrasi['nomor_registrasi']),
                    'nomor_registrasi' => $registrasi['nomor_registrasi'],
                    'klien_nama' => $klien['nama'] ?? 'Klien',
                    'layanan' => $registrasi['nama_layanan'] ?? 'Layanan',
                    'estimasi_selesai' => $estimasi,
                    'status_label' => $registrasi['status_label'] ?? ($registrasi['status'] ?? 'Proses'),
                    'status_style' => \App\Models\RegistrasiModel::getStatusStyle((int)($registrasi['behavior_role'] ?? 0)),
                    'updated_at' => date('d M Y H:i', strtotime($registrasi['updated_at'] ?? 'now')),
                    'is_lunas' => (bool)($registrasi['is_lunas'] ?? false),
                    'kendala_flag' => (bool)($registrasi['kendala_flag'] ?? false),
                    'keterangan' => $registrasi['keterangan'] ?? '',
                    'latest_log' => $latestLog ? [
                        'date' => date('d M Y H:i', strtotime($latestLog['created_at'])),
                        'note' => $latestLog['catatan']
                    ] : null
                ]
            ]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Kesalahan verifikasi: ' . $e->getMessage()]);
        }
        exit;
    }

    public function showDetail(): void
    {
        try {
            $token = $_GET['token'] ?? '';
            
            // --- DEBUG LOGGING ---
            $logFile = BASE_PATH . '/scratch/token_debug.txt';
            $debugLog = "[" . date('Y-m-d H:i:s') . "] RAW TOKEN IN: '$token'\n";
            // ---------------------

            if (empty($token)) {
                redirect('/lacak');
            }

            // Normalize token (replace spaces with + if any, common URL issue)
            $token = str_replace(' ', '+', $token);
            $debugLog .= "NORMALIZED TOKEN: '$token'\n";

            $model = new \App\Models\RegistrasiModel();
            
            // 1. Try direct token lookup (tracking_token column)
            $registrasi = $model->findByToken($token);
            $debugLog .= "findByToken Result: " . ($registrasi ? "FOUND" : "NULL") . "\n";
            
            if (!$registrasi) {
                // 2. Try decryption if it's an encrypted Nomor Registrasi
                $nomor = decryptImageId($token);
                $debugLog .= "DECRYPTED NOMOR: '$nomor'\n";
                if ($nomor) {
                    $nomor = trim((string)$nomor);
                    $registrasi = $model->findByNomorRegistrasiFull($nomor);
                    $debugLog .= "findByNomor (Decrypted) Result: " . ($registrasi ? "FOUND" : "NULL") . "\n";
                }
            }
            
            // 3. Last fallback: Try the token as a raw Nomor Registrasi
            if (!$registrasi) {
                $registrasi = $model->findByNomorRegistrasiFull($token);
                $debugLog .= "findByNomor (Raw) Result: " . ($registrasi ? "FOUND" : "NULL") . "\n";
            }

            file_put_contents($logFile, $debugLog, FILE_APPEND);

            if (!$registrasi) {
                require VIEWS_PATH . '/public/tracking_error.php';
                return;
            }

            // Get Progress Timeline
            $progress = $model->getTrackingProgress((int)$registrasi['id']);
            
            // Get History Logs
            $history = $model->getTrackingHistory((int)$registrasi['id']);

            $homepageData = $this->loadHomepageData();
            $title = 'Detail Registrasi ' . $registrasi['nomor_registrasi'] . ' - ' . APP_NAME;
            $content = VIEWS_PATH . '/public/detail.php';
            require VIEWS_PATH . '/public/layout.php';
        } catch (\Throwable $e) {
            $logFile = BASE_PATH . '/scratch/token_debug.txt';
            file_put_contents($logFile, "EXCEPTION/ERROR: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n", FILE_APPEND);
            
            require VIEWS_PATH . '/public/tracking_error.php';
        }
    }
}

