<?php
$pdo = new PDO('mysql:host=localhost;dbname=norasblmupdate','root','');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    // Step 1: Fix column type to match workflow_steps.id (INT 11 SIGNED)
    $pdo->exec("ALTER TABLE registrasi_history MODIFY COLUMN status_old_id INT(11) NULL");
    echo "1. status_old_id type fixed to INT(11)\n";
    
    $pdo->exec("ALTER TABLE registrasi_history MODIFY COLUMN status_new_id INT(11) NULL");
    echo "2. status_new_id type fixed to INT(11)\n";
    
    // Step 2: Add Foreign Key constraints
    $pdo->exec("ALTER TABLE registrasi_history ADD CONSTRAINT fk_hist_status_old FOREIGN KEY (status_old_id) REFERENCES workflow_steps(id) ON DELETE CASCADE ON UPDATE CASCADE");
    echo "3. FK fk_hist_status_old linked!\n";
    
    $pdo->exec("ALTER TABLE registrasi_history ADD CONSTRAINT fk_hist_status_new FOREIGN KEY (status_new_id) REFERENCES workflow_steps(id) ON DELETE CASCADE ON UPDATE CASCADE");
    echo "4. FK fk_hist_status_new linked!\n";
    
    echo "\n=== ALL DONE! Database is now FULLY SYNCED! ===\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
