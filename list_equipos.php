<?php
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require $file;
});

use App\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT id, nombre, ip_address, estado, ultimo_heartbeat FROM equipos ORDER BY id ASC");
    echo "ID | NOMBRE | IP | ESTADO | HEARTBEAT\n";
    echo str_repeat("-", 80) . "\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['id'] . " | " . 
             $row['nombre'] . " | " . 
             ($row['ip_address'] ?? 'N/A') . " | " . 
             $row['estado'] . " | " . 
             ($row['ultimo_heartbeat'] ?? 'N/A') . "\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

