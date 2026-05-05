<?php
define('BASE_PATH', dirname(__DIR__));
require 'app/Core/Autoloader.php';
App\Core\Autoloader::register();
App\Core\Autoloader::addNamespace('App\\', 'app/');
require 'app/Config/App.php';
require 'app/Config/Database.php';

use App\Core\Database;

$nomor = 'NP-20260410-4877';
$res = Database::selectOne("SELECT * FROM registrasi WHERE nomor_registrasi = ?", [$nomor]);

echo "RECORD FOR $nomor:\n";
print_r($res);

if ($res) {
    $token = $res['tracking_token'];
    echo "\nTRACKING TOKEN: " . ($token ?? 'NULL') . "\n";
    
    $encrypted = encryptImageId($nomor);
    echo "ENCRYPTED NOMOR: $encrypted\n";
    
    $decrypted = decryptImageId($encrypted);
    echo "DECRYPTED TEST: $decrypted\n";
}
