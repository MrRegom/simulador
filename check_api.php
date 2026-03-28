<?php
spl_autoload_register(function ($class) {
    if (strpos($class, 'App\\') === 0) {
        $file = __DIR__ . '/app/' . str_replace(['App\\', '\\'], ['', '/'], $class) . '.php';
        if (file_exists($file)) require $file;
    }
});
require_once __DIR__ . '/app/Config/Config.php';

$ctrl = new \App\Controllers\RankingController();
ob_start();
$ctrl->api_data();
$out = ob_get_clean();
$data = json_decode($out, true);
echo "ACTIVE_TRACK: " . (new \App\Repositories\RankingConfigRepository())->get('active_track', 'NONE') . "\n";
echo "TOP COUNT: " . count($data['top'] ?? []) . "\n";
print_r($data['top'] ?? []);
