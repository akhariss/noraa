<?php
/**
 * Migration: Michelin Behavior Sync v2.5 (0-7 Logic)
 */
$pdo = new PDO('mysql:host=localhost;dbname=norasblmupdate','root','');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$mapping = [
    'draft'                 => 0, // DRAFT
    'pembayaran_admin'      => 1, // STATUS AWAL (Cancellable)
    'validasi_sertifikat'   => 2, // NORMAL PROCESS
    'pencecekan_sertifikat' => 2, // NORMAL PROCESS
    'pembayaran_pajak'      => 2, // NORMAL PROCESS
    'validasi_pajak'        => 2, // NORMAL PROCESS
    'penomoran_akta'        => 2, // NORMAL PROCESS
    'pendaftaran'           => 2, // NORMAL PROCESS
    'pembayaran_pnbp'       => 2, // NORMAL PROCESS
    'pemeriksaan_bpn'       => 2, // NORMAL PROCESS
    'perbaikan'             => 3, // REPAIR (Cancellable)
    'selesai'               => 4, // SELESAI
    'diserahkan'            => 5, // DISERAHKAN
    'ditutup'               => 6, // DITUTUP
    'batal'                 => 7, // BATAL
];

try {
    $pdo->beginTransaction();

    // 1. Update all steps behavior_role according to new 0-7 schema
    foreach ($mapping as $key => $role) {
        $stmt = $pdo->prepare("UPDATE workflow_steps SET behavior_role = ? WHERE step_key = ?");
        $stmt->execute([$role, $key]);
        echo "Updated '{$key}' to Role {$role}\n";
    }

    // 2. Set is_cancellable flag based on new logic (1 and 3 are cancellable)
    $pdo->exec("UPDATE workflow_steps SET is_cancellable = 1 WHERE behavior_role IN (1, 3)");
    $pdo->exec("UPDATE workflow_steps SET is_cancellable = 0 WHERE behavior_role NOT IN (1, 3)");
    echo "Updated is_cancellable flags (Rules: 1 & 3 can cancel).\n";

    $pdo->commit();
    echo "\nSUCCESS: Michelin Behavior Standar 0-7 Applied!\n";

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo "ERROR: " . $e->getMessage() . "\n";
}
