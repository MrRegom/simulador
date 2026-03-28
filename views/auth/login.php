<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN | A4R SERVIREC</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --neon-red: #ff0000;
            --neon-glow: rgba(255, 0, 0, 0.5);
            --bg-dark: #050505;
            --panel-bg: rgba(10, 10, 10, 0.95);
        }

        body {
            background-color: var(--bg-dark);
            color: #fff;
            font-family: 'JetBrains Mono', monospace;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
            position: relative;
        }

        /* Fondo de Imagen Corporativa con Transparencia */
        body::after {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(rgba(5, 5, 5, 0.8), rgba(5, 5, 5, 0.9)), 
                        url('<?php echo \App\Config\Config::getAppUrl(); ?>/public/assets/img/fondo.jpeg');
            background-size: cover;
            background-position: center;
            z-index: -2;
            opacity: 0.6;
        }

        /* Fondo Animado de Grid Cyber (Capa superior al fondo) */
        body::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background-image: 
                linear-gradient(rgba(255, 0, 0, 0.1) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 0, 0, 0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            z-index: -1;
            transform: perspective(1000px) rotateX(60deg);
            transform-origin: top;
            animation: gridMove 20s linear infinite;
            opacity: 0.3;
        }

        @keyframes gridMove {
            0% { background-position: 0 0; }
            100% { background-position: 0 500px; }
        }

        .login-box {
            width: 400px;
            padding: 40px;
            background: var(--panel-bg);
            border: 1px solid #222;
            position: relative;
            box-shadow: 0 0 50px rgba(0,0,0,0.8);
            clip-path: polygon(0 0, 100% 0, 100% 90%, 90% 100%, 0 100%);
        }

        .login-box::before {
            content: '';
            position: absolute;
            top: 0; right: 0;
            width: 80px; height: 3px;
            background: var(--neon-red);
            box-shadow: 0 0 15px var(--neon-red);
        }

        .brand-logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .brand-logo h1 {
            font-family: 'Orbitron', sans-serif;
            font-weight: 900;
            font-size: 3rem;
            letter-spacing: -2px;
            margin: 0;
            font-style: italic;
        }

        .brand-logo h1 .red { color: var(--neon-red); text-shadow: 0 0 10px var(--neon-glow); }
        .brand-logo h1 .white { color: #fff; }

        .system-tag {
            display: block;
            text-align: center;
            font-size: 0.7rem;
            color: #555;
            letter-spacing: 5px;
            margin-top: -10px;
            text-transform: uppercase;
        }

        .form-label {
            font-family: 'Orbitron', sans-serif;
            font-size: 0.65rem;
            letter-spacing: 2px;
            color: #888;
            text-transform: uppercase;
        }

        .input-cyber {
            background: #000 !important;
            border: 1px solid #333 !important;
            color: #fff !important;
            border-radius: 0;
            padding: 12px;
            font-family: 'JetBrains Mono', monospace;
        }

        .input-cyber:focus {
            border-color: var(--neon-red) !important;
            box-shadow: 0 0 10px var(--neon-glow) !important;
        }

        .btn-login {
            background: var(--neon-red);
            color: #fff;
            border: none;
            width: 100%;
            padding: 15px;
            font-family: 'Orbitron', sans-serif;
            font-weight: 900;
            font-size: 1.1rem;
            margin-top: 20px;
            clip-path: polygon(0 0, 95% 0, 100% 20%, 100% 100%, 5% 100%, 0 80%);
            transition: 0.3s;
            letter-spacing: 2px;
        }

        .btn-login:hover {
            background: #fff;
            color: #000;
            transform: scale(1.02);
        }

        .error-msg {
            background: rgba(255, 0, 0, 0.1);
            color: var(--neon-red);
            padding: 10px;
            font-size: 0.8rem;
            text-align: center;
            margin-bottom: 20px;
            border-left: 3px solid var(--neon-red);
        }

        .footer-info {
            position: absolute;
            bottom: 20px;
            font-size: 0.6rem;
            color: #333;
            text-align: center;
            width: 100%;
            letter-spacing: 1px;
            z-index: 10;
        }

        /* 📱 RESPONSIVE PARA MÓVILES */
        @media (max-width: 480px) {
            .login-box {
                width: 90%;
                padding: 25px;
                /* El clip-path podría no lucir bien tan apretado, lo suavizamos */
                clip-path: polygon(0 0, 100% 0, 100% 95%, 95% 100%, 0 100%);
            }
            .brand-logo img {
                width: 220px !important; /* Achicar el logo maestro en celu */
                margin-bottom: 10px;
            }
            .system-tag {
                font-size: 0.6rem;
                letter-spacing: 2px;
            }
            .btn-login {
                font-size: 0.95rem;
                padding: 12px;
                clip-path: none; /* Mejor sin cortes agresivos en formato vertical de celular */
                border-radius: 5px;
            }
        }
    </style>
</head>
<body>

    <div class="login-box">
        <div class="brand-logo mb-4">
            <img src="<?php echo \App\Config\Config::getAppUrl(); ?>/public/assets/img/logoa4r.png" alt="A4R Logo" style="width: 280px; filter: drop-shadow(0 0 20px rgba(255,0,0,0.5));">
            <span class="system-tag d-block mt-3">ServiRec System v2.0</span>
        </div>

        <?php if (isset($error)): ?>
            <div class="error-msg">
                <i class="fas fa-exclamation-triangle me-2"></i> ERROR: <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo \App\Config\Config::getAppUrl(); ?>/login" method="POST">
            <div class="mb-4">
                <label class="form-label">USUARIO_IDENTIFICADOR</label>
                <div class="input-group">
                    <span class="input-group-text bg-black border-secondary text-secondary"><i class="fas fa-user-shield"></i></span>
                    <input type="text" name="usuario" class="form-control input-cyber" placeholder="ID_ACCESO" required autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">CONTRASEÑA_CRYPT</label>
                <div class="input-group">
                    <span class="input-group-text bg-black border-secondary text-secondary"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="form-control input-cyber" placeholder="********" required>
                </div>
            </div>

            <button type="submit" class="btn-login">
                INICIAR_SESIÓN <i class="fas fa-sign-in-alt ms-2"></i>
            </button>
        </form>

        <div class="mt-4 text-center">
            <small style="color:#222; font-size: 0.5rem;">CÓDIGO_SEGURIDAD_ACTIVO: AES-256-GCM</small>
        </div>
    </div>

    <div class="footer-info">
        AUTORIZED PERSONNEL ONLY | &copy; 2026 A4R SIM RACING CHILE - ALL SYSTEMS ONLINE
    </div>

</body>
</html>
