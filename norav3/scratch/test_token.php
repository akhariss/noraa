<?php
require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/app/Core/Utils/security.php';

$id = 35;
$code = 'testcode';
$token = generateTrackingToken($id, $code);
$verified = verifyTrackingToken($token);

echo "Token: $token\n";
echo "Verified: " . ($verified ? 'YES' : 'NO') . "\n";
if ($verified) {
    echo "Data: " . json_encode($verified) . "\n";
}
?>
