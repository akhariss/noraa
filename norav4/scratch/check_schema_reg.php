<?php
define('BASE_PATH', dirname(__DIR__));
require 'app/Core/Autoloader.php';
App\Core\Autoloader::register();
App\Core\Autoloader::addNamespace('App\\', 'app/');
require 'app/Config/App.php';
require 'app/Config/Database.php';

$db = App\Core\Database::get();
$stmt = $db->query('DESCRIBE registrasi');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
