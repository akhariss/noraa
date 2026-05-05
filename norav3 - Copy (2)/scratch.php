<?php
require 'public/index.php';
$db = \App\Core\Database::getInstance();
print_r($db->query('DESCRIBE workflow_steps')->fetchAll(PDO::FETCH_ASSOC));
