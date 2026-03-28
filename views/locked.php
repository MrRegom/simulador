<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TIEMPO FINALIZADO | ServiRec</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@700;900&display=swap" rel="stylesheet">
    <style>
        body {
            background: #0a0b10;
            color: white;
            font-family: 'Outfit', sans-serif;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            overflow: hidden;
            text-align: center;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255, 75, 43, 0.1) 0%, rgba(10, 11, 16, 1) 100%);
            z-index: 1;
        }

        .content {
            position: relative;
            z-index: 2;
            padding: 2rem;
        }

        h1 {
            font-size: 8rem;
            margin: 0;
            background: linear-gradient(to bottom, #ff4b2b, #ff416c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-transform: uppercase;
            letter-spacing: -5px;
            animation: pulse 2s infinite;
        }

        p {
            font-size: 2rem;
            color: #a0a0a0;
            margin-top: -20px;
            letter-spacing: 2px;
        }

        .icon {
            font-size: 5rem;
            color: #ff4b2b;
            margin-bottom: 2rem;
            filter: drop-shadow(0 0 20px rgba(255, 75, 43, 0.5));
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.8; }
            100% { transform: scale(1); opacity: 1; }
        }

        .footer {
            position: absolute;
            bottom: 40px;
            width: 100%;
            color: #444;
            font-size: 0.9rem;
            letter-spacing: 3px;
        }
    </style>
</head>
<body>
    <div class="overlay"></div>
    <div class="content">
        <div class="icon">⚠️</div>
        <h1>TIEMPO</h1>
        <p>POR FAVOR, ACÉRCATE A CAJA</p>
    </div>
    <div class="footer">
        SERVIREC SIMULATORS • SYSTEM ID: CAB-0<?php echo $_GET['id'] ?? 'X'; ?>
    </div>

    <script>
        // Auto-check poll to see if it becomes free again
        setInterval(() => {
            fetch('api/check?id=<?php echo $_GET['id'] ?? 0; ?>')
                .then(r => r.json())
                .then(data => {
                    if (data.estado_equipo === 'libre') {
                        window.location.reload();
                    }
                });
        }, 5000);
    </script>
</body>
</html>
