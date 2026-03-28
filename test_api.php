<?php
$url = 'http://localhost/servirec/public/api/simulador/iniciar';
$data = [
    'equipo_id' => 1,
    'tiempo' => 15,
    'monto' => 1500,
    'metodo' => 'efectivo'
];

$options = [
    'http' => [
        'header'  => "Content-type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($data),
        'ignore_errors' => true
    ]
];

$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

echo "Response: " . $result . "\n";
