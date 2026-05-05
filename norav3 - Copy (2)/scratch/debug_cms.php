<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/app/Core/Autoloader.php';
App\Core\Autoloader::register();
App\Core\Autoloader::addNamespace('App\\', BASE_PATH . '/app/');
require_once BASE_PATH . '/config/app.php';

$conn = \App\Adapters\Database::getInstance();
$res13 = $conn->query("SELECT * FROM cms_section_content WHERE id = 13")->fetch();
echo "ID 13: "; print_r($res13); echo "\n";

$section8 = $conn->query("SELECT * FROM cms_section_content WHERE section_id = 8")->fetchAll();
echo "Section 8: "; print_r($section8);
