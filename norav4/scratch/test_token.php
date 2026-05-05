<?php
define('BASE_PATH', dirname(__DIR__));
require 'app/Core/Autoloader.php';
App\Core\Autoloader::register();
App\Core\Autoloader::addNamespace('App\\', 'app/');
require 'app/Config/App.php';
require 'app/Config/Database.php';

$model = new App\Models\RegistrasiModel();
$token = 'eyJpZCI6MSwiY29kZSI6Ijg4ODUiLCJ0aW1lIjoxNzc1ODA0MjMwfQ==.8b3547e9c1c8dbd05078766459bc44680f65e999b796c32e8ecafcac41cb1e4d';

try {
    $res = $model->findByToken($token);
    echo "FindByToken Result: \n";
    print_r($res);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
