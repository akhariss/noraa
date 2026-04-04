<?php
define('BASE_PATH', __DIR__);
require_once 'app/Core/Autoloader.php';
\App\Core\Autoloader::register();
\App\Core\Autoloader::addNamespace('App\\', 'app/');

require_once 'config/app.php';

try {
    $db = \App\Adapters\Database::getInstance();
    echo "Connected successfully to " . DB_NAME . "\n";
    $result = $db->query("SELECT DATABASE()")->fetchColumn();
    echo "Current database: " . $result . "\n";
} catch (\Exception $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
