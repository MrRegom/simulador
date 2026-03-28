<?php
$tokenSafe = htmlspecialchars($token ?? '');
$baseUrlSafe = htmlspecialchars($base_url ?? '');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A4R | Vincular Simulador</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Orbitron:wght@600;700&display=swap">
    <style>
        :root { --red:#ff0000; --bg:#050505; --panel:#0e0e0e; --border:#1b1b1b; }
        * { box-sizing:border-box; }
        body { margin:0; font-family:'Inter',sans-serif; background:var(--bg); color:#eee; }
        .wrap { min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; }
        .card { width:100%; max-width:560px; background:var(--panel); border:1px solid var(--border); border-radius:16px; padding:24px; box-shadow:0 20px 50px rgba(0,0,0,0.6); }
        h1 { font-family:'Orbitron',sans-serif; font-size:1.1rem; letter-spacing:2px; text-transform:uppercase; margin:0 0 10px; color:#fff; }
        .token { font-family:'Orbitron',sans-serif; font-size:0.8rem; color:#777; margin-bottom:18px; }
        .list { display:grid; gap:10px; }
        .btn { border:1px solid #222; background:#111; color:#fff; padding:14px 16px; border-radius:10px; text-align:left; font-weight:600; cursor:pointer; transition:0.2s; }
        .btn:hover { border-color:var(--red); box-shadow:0 0 0 2px rgba(255,0,0,0.15); }
        .btn small { display:block; color:#888; font-weight:400; }
        .status { margin-top:16px; font-size:0.85rem; color:#888; }
        .ok { color:#00ff9d; }
        .err { color:#ff6b6b; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <h1>Vincular Simulador</h1>
        <div class="token">TOKEN: <?php echo $tokenSafe; ?></div>
        <div class="list" id="equipos">
            <?php foreach ($equipos as $e): ?>
                <button class="btn" onclick="assignEquipo(<?php echo (int)$e['id']; ?>)">
                    <?php echo htmlspecialchars(strtoupper($e['nombre'])); ?>
                    <small>ID Sistema: <?php echo str_pad($e['id'], 2, '0', STR_PAD_LEFT); ?></small>
                </button>
            <?php endforeach; ?>
        </div>
        <div class="status" id="status">Seleccione el simulador correspondiente.</div>
    </div>
</div>

<script>
const BASE_URL = <?php echo json_encode($baseUrlSafe); ?>;
const TOKEN = <?php echo json_encode($token ?? ''); ?>;

async function assignEquipo(id) {
    const status = document.getElementById('status');
    status.textContent = 'Asignando...';
    try {
        const res = await fetch(`${BASE_URL}/api/pair/assign`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ token: TOKEN, equipo_id: id })
        });
        const data = await res.json();
        if (data.success) {
            status.innerHTML = '<span class="ok">Asignado con éxito. Ya puedes cerrar esta pantalla.</span>';
        } else {
            status.innerHTML = `<span class="err">Error: ${data.error || 'No se pudo asignar.'}</span>`;
        }
    } catch (e) {
        status.innerHTML = '<span class="err">Error de conexión con el servidor.</span>';
    }
}
</script>
</body>
</html>
