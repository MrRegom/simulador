<?php
spl_autoload_register(function ($class) {
    if (strpos($class, 'App\\') === 0) {
        $file = __DIR__ . '/app/' . str_replace(['App\\', '\\'], ['', '/'], $class) . '.php';
        if (file_exists($file)) require $file;
    }
});
require_once __DIR__ . '/app/Config/Config.php';

$db = \App\Core\Database::getInstance()->getConnection();
$stmt = $db->query("SELECT pista, count(*) as c FROM ranking GROUP BY pista");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

$stmt2 = $db->query("SELECT nombre FROM pistas WHERE activa = 1");
echo "\nPISTAS EN TABLA PISTAS:\n";
print_r($stmt2->fetchAll(PDO::FETCH_ASSOC));
