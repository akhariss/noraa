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
require_once UTILS_PATH . '/helpers.php';
require_once UTILS_PATH . '/security_helpers.php';
require_once UTILS_PATH . '/security.php';

use Modules\Media\Controller as ImageMediaController;

// Get image ID from request
$imageId = $_GET['id'] ?? '';

if (empty($imageId)) {
    showErrorPage(400, 'Permintaan Tidak Valid', 'Parameter ID gambar tidak ditemukan atau kosong.');
}

// Validate image ID format (must be base64 encoded)
if (!preg_match('/^[A-Za-z0-9+\/=]+$/', $imageId)) {
    showErrorPage(400, 'Format Tidak Valid', 'Token keamanan gambar memiliki format yang tidak didukung atau rusak.');
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
    showErrorPage(403, 'Akses Dilarang', 'Anda tidak memiliki otoritas untuk mengakses file ini atau token telah kadaluarsa.');
}

// Create controller and serve image
try {
    $imageController = new ImageMediaController();
    $imageController->serve($decodedFileName);
} catch (\Exception $e) {
    showErrorPage(500, 'Kesalahan Server', 'Terjadi kesalahan internal saat mencoba memproses file gambar Anda.');
}
