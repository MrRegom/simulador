<?php
// Simulación de petición del agente al servidor local
$url = 'http://localhost/servirec/public/index.php?url=api/simulador/estado';

// Caso 1: Enviar como JSON (ID del simulador)
$data = ['id' => 3];
$options = [
    'http' => [
        'header'  => "Content-type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($data),
    ]
];
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
echo "PRUEBA JSON ID=3: " . $result . "\n";

// Caso 2: Enviar como POST normal
$post_data = ['id' => 1];
$options_post = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($post_data),
    ]
];
$context_post = stream_context_create($options_post);
$result_post = file_get_contents($url, false, $context_post);
echo "PRUEBA POST ID=1: " . $result_post . "\n";
