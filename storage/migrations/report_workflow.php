<?php
$pdo = new PDO('mysql:host=localhost;dbname=norasblmupdate','root','');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$out = "=== WORKFLOW_STEPS DATA ===\n";
$rows = $pdo->query("SELECT id, step_key, label, behavior_role FROM workflow_steps ORDER BY sort_order ASC")->fetchAll(PDO::FETCH_ASSOC);
foreach($rows as $r) {
    $out .= "ID:{$r['id']} | KEY:{$r['step_key']} | LABEL:{$r['label']} | BEHAVIOR:{$r['behavior_role']}\n";
}

file_put_contents(__DIR__ . '/workflow_report.txt', $out);
echo "Workflow report generated!\n";
