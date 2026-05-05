<?php
/**
 * Image Serving Script (Secure)
 * Location: /image.php (public but serves protected files)
 */

define('BASE_PATH', dirname(__DIR__));

// Initialize Autoloader
require_once BASE_PATH . '/app/Core/Autoloader.php';
App\Core\Autoloader::register();
App\Core\Autoloader::addNamespace('App\\', BASE_PATH . '/app/');
App\Core\Autoloader::addNamespace('Modules\\', BASE_PATH . '/modules/');

// Load Config
require_once BASE_PATH . '/config/app.php';

// Load Essential Utils
require_once BASE_PATH . '/app/Core/Utils/helpers.php';
require_once BASE_PATH . '/app/Core/Utils/security_helpers.php';
require_once BASE_PATH . '/app/Core/Utils/security.php';

use Modules\Media\Controller as ImageMediaController;

// Get image ID from request
$imageId = $_GET['id'] ?? '';

if (empty($imageId)) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Parameter ID gambar tidak ditemukan atau kosong.']);
    exit;
}

// Validate image ID format (must be base64 encoded)
if (!preg_match('/^[A-Za-z0-9+\/=]+$/', $imageId)) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Format tidak valid.']);
    exit;
}

// Decode and validate filename using secure decryption
try {
    $decodedFileName = decryptImageId($imageId);

    if ($decodedFileName === false || empty($decodedFileName)) {
        throw new \Exception('Invalid or expired image token');
    }

    // Validate filename format
    if (!preg_match('/^img_\d{10}_[a-f0-9]{16}\.(jpg|jpeg|png|webp)$/i', $decodedFileName)) {
        throw new \Exception('Invalid filename signature');
    }
} catch (\Exception $e) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Token tidak valid atau kadaluarsa.']);
    exit;
}

// Create controller and serve image
try {
    $imageController = new ImageMediaController();
    $imageController->serve($decodedFileName);
} catch (\Exception $e) {
    error_log('Image serve error: ' . $e->getMessage());
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat memproses gambar.']);
    exit;
}
