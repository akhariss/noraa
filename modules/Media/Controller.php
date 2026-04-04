<?php
declare(strict_types=1);

namespace Modules\Media;

/**
 * SK-14: ImageMediaController
 * Slim controller for Image Media
 */

use Exception;
use App\Domain\Entities\CMSPage;
use App\Services\CMSEditorService;

class Controller {
    private CMSEditorService $cmsService;
    private string $imagesDir;
    private int $maxFileSize = 5242880; // 5MB
    private array $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
    private array $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];

    public function __construct() {
        $this->cmsService = new CMSEditorService();
        $this->imagesDir = BASE_PATH . '/public/assets/images/';
        
        if (!is_dir($this->imagesDir)) {
            mkdir($this->imagesDir, 0755, true);
        }
    }

    /**
     * API: Upload image and return secure URL
     * LAW 19.1: Only authenticated notaris can upload
     * LAW 25.1: Max 5MB per file
     */
    public function upload(): void {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        // LAW 19.1: Fail Closed - Check auth first
        $user = getCurrentUser();
        if (!$user || !in_array($user['role'], [ROLE_OWNER, ROLE_TRUSTED])) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Session expired. Silakan login kembali.']);
            return;
        }

        // Validate request
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'File upload failed']);
            return;
        }

        $file = $_FILES['image'];
        $contentId = (int)($_POST['content_id'] ?? 0);

        if ($contentId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid content ID']);
            return;
        }

        // LAW 25.1: Resource Budget - Check file size
        if ($file['size'] > $this->maxFileSize) {
            echo json_encode(['success' => false, 'message' => 'File terlalu besar (max 5MB)']);
            return;
        }

        // Security: Check MIME type (prevent malicious uploads)
        $mimeType = mime_content_type($file['tmp_name']);
        if (!in_array($mimeType, $this->allowedMimes, true)) {
            echo json_encode(['success' => false, 'message' => 'Format file tidak mendukung']);
            return;
        }

        // Security: Sanitize filename and generate unique name
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $this->allowedExt, true)) {
            echo json_encode(['success' => false, 'message' => 'Ekstensi file tidak valid']);
            return;
        }

        // Generate unique filename with timestamp + random hash
        $fileName = 'img_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $filePath = $this->imagesDir . $fileName;

        // Get old image info BEFORE updating (for cleanup)
        $oldImagePath = null;
        $oldContent = $this->cmsService->getContentById($contentId);
        if ($oldContent && !empty($oldContent['content_value'])) {
            $oldUrl = $oldContent['content_value'];
            // Extract filename from old URL (format: /image.php?id=BASE64_FILENAME)
            if (preg_match('/image\.php\?id=([A-Za-z0-9+\/=]+)/', $oldUrl, $matches)) {
                $oldFileName = base64_decode($matches[1]);
                if ($oldFileName && file_exists($this->imagesDir . $oldFileName)) {
                    $oldImagePath = $this->imagesDir . $oldFileName;
                }
            }
        }

        // Move file to secure directory
        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan file']);
            return;
        }

        // Set restrictive permissions (LAW 19.1: Fail Closed)
        chmod($filePath, 0640);

        // Return secure URL (will be served through image.php script)
        // LAW 23.1: Encrypt filename to prevent directory disclosure
        $imageUrl = 'image.php?id=' . urlencode(encryptImageId($fileName));

        // Update database with image path (LAW 0.2)
        $result = $this->cmsService->updateContent($contentId, $imageUrl);

        if ($result['success']) {
            // Delete old image file after successful replacement (LAW 0.2: Resource cleanup)
            if ($oldImagePath && file_exists($oldImagePath) && $oldImagePath !== $filePath) {
                @unlink($oldImagePath);
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Foto berhasil diunggah',
                'url' => $imageUrl,
                'fileName' => $fileName
            ]);
        } else {
            // If DB update fails, delete the newly uploaded file
            @unlink($filePath);
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan ke database']);
        }
    }

    /**
     * Serve image with security checks
     * LAW 19.1: Fail Closed - Validate request before serving
     * Prevents directory traversal and unauthorized access
     */
    public function serve(string $fileName): void {
        // Security: Prevent directory traversal
        if (strpos($fileName, '..') !== false || strpos($fileName, '/') !== false) {
            http_response_code(403);
            die('Forbidden');
        }

        $filePath = $this->imagesDir . $fileName;

        // Security: Check file exists and is in correct directory
        if (!file_exists($filePath) || realpath($filePath) !== realpath($filePath)) {
            http_response_code(404);
            die('File not found');
        }

        // Security: Verify it's actually an image file
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if (!in_array($ext, $this->allowedExt, true)) {
            http_response_code(403);
            die('Invalid file type');
        }

        // Serve image with appropriate headers
        $mimeType = mime_content_type($filePath);
        
        // Security: Set headers to prevent execution
        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: public, max-age=86400'); // 24 hour cache
        header('X-Content-Type-Options: nosniff'); // Prevent MIME sniffing
        header('X-Frame-Options: DENY'); // Prevent clickjacking
        
        // LAW 23.1: Prevent directory disclosure
        header('Content-Disposition: inline; filename="image.' . $ext . '"');

        readfile($filePath);
        exit;
    }
}
