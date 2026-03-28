<?php
require_once __DIR__ . '/app/Core/Database.php';
$db = \App\Core\Database::getInstance()->getConnection();
$stmt = $db->query("DESCRIBE ranking_config");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
