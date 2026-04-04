<?php
$pdo = new PDO('mysql:host=localhost;dbname=norasblmupdate','root','');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$out = "=== REGISTRASI_HISTORY (latest 10) ===\n";
$rows = $pdo->query("SELECT id, registrasi_id, status_old_id, status_new_id, flag_kendala_active, flag_kendala_tahap, catatan, created_at FROM registrasi_history ORDER BY id DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
foreach($rows as $r) {
    $out .= "id:{$r['id']} | reg:{$r['registrasi_id']} | flag_active:[{$r['flag_kendala_active']}] | flag_tahap:[{$r['flag_kendala_tahap']}] | {$r['created_at']}\n";
}

$out .= "\n=== KENDALA TABLE STRUCTURE ===\n";
try {
    $cols = $pdo->query("SHOW COLUMNS FROM kendala")->fetchAll(PDO::FETCH_ASSOC);
    foreach($cols as $c) $out .= $c['Field'] . " | " . $c['Type'] . "\n";
    
    $out .= "\n=== KENDALA DATA (latest) ===\n";
    $kendala = $pdo->query("SELECT * FROM kendala ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    foreach($kendala as $k) $out .= json_encode($k) . "\n";
} catch(Exception $e) {
    $out .= "Error: " . $e->getMessage() . "\n";
}

file_put_contents(__DIR__ . '/flag_debug.txt', $out);
echo "Done!\n";
