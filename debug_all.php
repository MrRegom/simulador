<?php
spl_autoload_register(function ($class) {
    if (strpos($class, 'App\\') === 0) {
        $file = __DIR__ . '/app/' . str_replace(['App\\', '\\'], ['', '/'], $class) . '.php';
        if (file_exists($file)) require $file;
    }
});
require_once __DIR__ . '/app/Config/Config.php';

try {
    $db = \App\Core\Database::getInstance()->getConnection();
    
    echo "--- RANKING_CONFIG ---\n";
    $stmt = $db->query("SELECT * FROM ranking_config WHERE key_name = 'active_track'");
    print_r($stmt->fetch(PDO::FETCH_ASSOC));
    
    echo "\n--- PISTAS ACTIVAS ---\n";
    $stmt = $db->query("SELECT nombre FROM pistas WHERE is_active = 1");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    
    echo "\n--- PISTAS EN RANKING ---\n";
    $stmt = $db->query("SELECT DISTINCT pista FROM ranking");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    
    echo "\n--- TODA LA CONFIG ---\n";
    $stmt = $db->query("SELECT * FROM ranking_config");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
