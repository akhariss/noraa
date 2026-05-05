<?php
/**
 * Image Serving Script (Ported to Nora V4)
 */

define('BASE_PATH', dirname(__DIR__));

// Initialize Autoloader
require_once BASE_PATH . '/app/Core/Autoloader.php';
App\Core\Autoloader::register();
App\Core\Autoloader::addNamespace('App\\', BASE_PATH . '/app/');

// Load Config & Helpers
require_once BASE_PATH . '/app/Config/App.php';
require_once BASE_PATH . '/app/Core/helpers.php';

// Get image ID from request
$imageId = $_GET['id'] ?? '';

if (empty($imageId)) {
    http_response_code(400);
    exit('Missing image ID');
}

// Decode filename
$fileName = decryptImageId($imageId);

if (!$fileName) {
    http_response_code(403);
    exit('Invalid image token');
}

// Sanitize filename
$fileName = basename($fileName);
$imagesDir = PUBLIC_PATH . '/assets/images/';
$filePath = $imagesDir . $fileName;

if (!file_exists($filePath)) {
    http_response_code(404);
    exit('File not found: ' . $fileName);
}

// Serve image
$mimeType = mime_content_type($filePath);
header('Content-Type: ' . $mimeType);
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: public, max-age=86400');
header('X-Content-Type-Options: nosniff');
header('Content-Disposition: inline; filename="' . $fileName . '"');

readfile($filePath);
exit;
