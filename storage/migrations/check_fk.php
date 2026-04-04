<?php
$pdo = new PDO('mysql:host=localhost;dbname=norasblmupdate','root','');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$out = "";

// Check FK constraints on registrasi_history
$out .= "=== FK CONSTRAINTS ON registrasi_history ===\n";
$fks = $pdo->query("SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME='registrasi_history' AND TABLE_SCHEMA='norasblmupdate' AND REFERENCED_TABLE_NAME IS NOT NULL")->fetchAll(PDO::FETCH_ASSOC);
foreach($fks as $f) {
    $out .= $f['CONSTRAINT_NAME'] . ' => ' . $f['COLUMN_NAME'] . ' -> ' . $f['REFERENCED_TABLE_NAME'] . '(' . $f['REFERENCED_COLUMN_NAME'] . ")\n";
}

// Check column types
$out .= "\n=== REGISTRASI_HISTORY COLUMNS ===\n";
$cols = $pdo->query('SHOW COLUMNS FROM registrasi_history')->fetchAll(PDO::FETCH_ASSOC);
foreach($cols as $c) {
    $out .= $c['Field'] . ' | ' . $c['Type'] . "\n";
}

file_put_contents(__DIR__ . '/fk_report.txt', $out);
echo "FK Report saved!\n";
