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

require_once __DIR__ . '/app/Config/Config.php';

try {
    $db = \App\Core\Database::getInstance()->getConnection();
    echo "SCHEMA:\n";
    $stmt = $db->query("DESCRIBE ranking_config");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    
    echo "\nDATA:\n";
    $stmt = $db->query("SELECT * FROM ranking_config");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
