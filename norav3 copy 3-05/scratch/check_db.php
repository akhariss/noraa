<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/app/Core/Autoloader.php';
\App\Core\Autoloader::register();
\App\Core\Autoloader::addNamespace('App\\', BASE_PATH . '/app/');

require_once BASE_PATH . '/config/app.php';

use App\Adapters\Database;

try {
    $db = Database::getInstance();
    $stmt = $db->query("DESCRIBE registrasi");
    echo "COLUMNS IN registrasi:\n";
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['Field'] . "\n";
    }
    $stmt = $db->query("DESCRIBE registrasi_history");
    echo "\nCOLUMNS IN registrasi_history:\n";
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['Field'] . "\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
