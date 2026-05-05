<?php
require_once __DIR__ . '/app/Config/Database.php';
try {
    $db = \App\Config\Database::getConnection();
    $stmt = $db->query("SHOW TABLES");
    print_r($stmt->fetchAll(PDO::FETCH_COLUMN));
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
