<?php
$pdo = new PDO('mysql:host=localhost;dbname=norasblmupdate','root','');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$out = "=== REGISTRASI_HISTORY COLUMNS ===\n";
$cols = $pdo->query('SHOW COLUMNS FROM registrasi_history')->fetchAll(PDO::FETCH_ASSOC);
foreach($cols as $c) {
    $out .= $c['Field'] . ' | ' . $c['Type'] . ' | Null:' . $c['Null'] . ' | Key:' . $c['Key'] . "\n";
}

$out .= "\n=== AUDIT_LOG COLUMNS ===\n";
$cols2 = $pdo->query('SHOW COLUMNS FROM audit_log')->fetchAll(PDO::FETCH_ASSOC);
foreach($cols2 as $c) {
    $out .= $c['Field'] . ' | ' . $c['Type'] . ' | Null:' . $c['Null'] . ' | Key:' . $c['Key'] . "\n";
}

$out .= "\n=== WORKFLOW_STEPS ===\n";
$steps = $pdo->query('SELECT id, step_key, behavior_role FROM workflow_steps ORDER BY sort_order')->fetchAll(PDO::FETCH_ASSOC);
foreach($steps as $s) {
    $out .= $s['id'] . ' | ' . $s['step_key'] . ' | behavior:' . $s['behavior_role'] . "\n";
}

file_put_contents(__DIR__ . '/schema_report.txt', $out);
echo "Report saved to schema_report.txt\n";
