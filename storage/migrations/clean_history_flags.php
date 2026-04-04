<?php
$pdo = new PDO('mysql:host=localhost;dbname=norasblmupdate','root','');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    // 1. Reset all flag_kendala_active in registrasi_history to 0
    $pdo->exec("UPDATE registrasi_history SET flag_kendala_active = 0");
    echo "Reset all history flags to 0.\n";

    // 2. We don't automatically backfill history because history is sequential, 
    // and we only want new records to be clean. The bug caused random 1s.
    // If the kendala is active, we just ensure the view works. 
    // The view relies on `$hasActiveKendala` for the header and checkbox.
    // The history loop displays the flag if it was 1 AT THE TIME. Since it's corrupted,
    // setting them to 0 will clear the incorrect "🚩 ON" banners from the history view.
    
    echo "History data cleaned up.\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
