<?php
spl_autoload_register(function ($class) {
    if (strpos($class, 'App\\') === 0) {
        $file = __DIR__ . '/app/' . str_replace(['App\\', '\\'], ['', '/'], $class) . '.php';
        if (file_exists($file)) require $file;
    }
});
require_once __DIR__ . '/app/Config/Config.php';

try {
    $repo = new \App\Repositories\RankingConfigRepository();
    $res = $repo->set('active_track', 'CHINA');
    var_dump($res);
    
    $val = $repo->get('active_track');
    echo "VALOR EN DB: " . $val . "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
