<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo \App\Config\Config::APP_NAME; ?> | Dashboard</title>
    
    <!-- Fuentes modernas -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Orbitron:wght@600;700;900&display=swap">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AdminLTE 4 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/css/adminlte.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>const BASE_URL = '<?php echo \App\Config\Config::getAppUrl(); ?>';</script>
    
    <!-- A4R MODERN CSS -->
    <link rel="stylesheet" href="<?php echo \App\Config\Config::getAppUrl(); ?>/public/assets/css/modern.css">

    <style>
    /* =====================================================
       A4R SERVIREC — GLOBAL DESIGN SYSTEM 2026
       ===================================================== */
    :root {
        --brand-red: #ff0000;
        --app-bg:    #000000;
        --panel-bg:  #0a0a0a;
        --sidebar-w: 260px;
        --border:    rgba(255,255,255,0.05);
    }

    /* ── BASE ── */
    html, body {
        font-family: 'Inter', system-ui, sans-serif !important;
        background: #000 !important;
        color: #e0e0e0;
        overflow-x: hidden !important;
        max-width: 100vw;
    }

    /* ── HEADER ── */
    .app-header {
        background: rgba(0,0,0,0.97) !important;
        border-bottom: 1px solid #1a1a1a !important;
        height: 60px !important;
    }

    /* ── APP WRAPPER: fondo negro siempre ── */
    .app-wrapper { background: #000 !important; }

    /* ── SIDEBAR ── */
    .app-sidebar {
        background: #000 !important;
        border-right: 1px solid #111 !important;
        box-shadow: 10px 0 30px rgba(0,0,0,0.5);
    }
    .sidebar-wrapper { overflow-y: auto !important; padding-bottom: 50px; }

    /* ── MAIN CONTENT ── */
    .app-wrapper .app-main {
        padding-top: 0 !important;
    }

    /* ── NAV SIDEBAR LINKS ── */
    .nav-sidebar .nav-item .nav-link {
        display: flex !important;
        align-items: center;
        padding: 10px 16px !important;
        border-radius: 10px;
        margin: 3px 10px;
        color: #aaa !important;
        border: 1px solid transparent;
        transition: all 0.2s ease;
        white-space: normal !important;
        overflow: visible !important;
    }
    .nav-sidebar .nav-item .nav-link p {
        margin: 0 0 0 10px !important;
        font-size: 0.86rem;
        font-weight: 500;
        flex: 1;
        white-space: normal !important;
        overflow: visible !important;
        text-overflow: unset !important;
    }
    .nav-sidebar .nav-item .nav-link i { width: 22px; text-align: center; color: #555; transition: 0.2s; }
    .nav-sidebar .nav-item .nav-link:hover,
    .nav-sidebar .nav-item .nav-link.active {
        color: #fff !important;
        background: rgba(255,0,0,0.1) !important;
        border-color: rgba(255,0,0,0.2);
        box-shadow: inset 3px 0 0 #ff0000;
    }
    .nav-sidebar .nav-item .nav-link:hover i,
    .nav-sidebar .nav-item .nav-link.active i { color: #ff0000 !important; }
    .nav-sidebar .nav-item .nav-link:hover p,
    .nav-sidebar .nav-item .nav-link.active p { color: #fff !important; font-weight: 700 !important; }

    .nav-header {
        font-size: 0.6rem; color: #333; text-transform: uppercase;
        letter-spacing: 2px; margin: 20px 0 5px 18px; font-weight: 800;
    }

    /* ── SCROLLBAR ── */
    ::-webkit-scrollbar { width: 4px; }
    ::-webkit-scrollbar-track { background: #000; }
    ::-webkit-scrollbar-thumb { background: #222; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #ff0000; }

    /* ── PAGE TITLE ── */
    .page-title-hdr {
        font-family: 'Orbitron', sans-serif;
        font-size: 0.75rem;
        font-weight: 700;
        color: #444;
        text-transform: uppercase;
        letter-spacing: 2px;
        border-left: 1px solid #222;
        padding-left: 14px;
        margin-left: 14px;
    }

    /* ── SWEETALERT DARK ── */
    .swal2-popup { background:#0a0a0a !important; border:1px solid rgba(255,0,0,0.2) !important; border-radius:14px !important; color:#fff !important; font-family:'Inter',sans-serif !important; }
    .swal2-title { font-family:'Orbitron',sans-serif !important; font-size:1rem !important; color:#fff !important; text-transform:uppercase; }
    .swal2-confirm { background:#ff0000 !important; border-radius:8px !important; font-weight:700 !important; }
    .swal2-cancel { background:#222 !important; border-radius:8px !important; }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg">

<div class="app-wrapper">
    <!-- ── HEADER NAVBAR ── -->
    <nav class="app-header navbar navbar-expand bg-body">
        <div class="container-fluid">
            <ul class="navbar-nav align-items-center">
                <!-- Menú sándwich nativo de AdminLTE 4 -->
                <li class="nav-item">
                    <a class="nav-link text-white" data-lte-toggle="sidebar" href="#" role="button" style="padding: 8px 14px; margin-right: 10px; opacity: 0.8; transition: 0.3s;"
                       onmouseenter="this.style.opacity='1'" onmouseleave="this.style.opacity='0.8'">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
                <li class="nav-item d-none d-md-flex align-items-center">
                    <span class="page-title-hdr"><?php echo strtoupper($page_title ?? 'SISTEMA_DE_GESTIÓN'); ?></span>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto align-items-center gap-2">
                <!-- Usuario -->
                <li class="nav-item d-none d-sm-block">
                    <div style="font-size:0.7rem;color:#00ff9d;background:rgba(0,255,157,0.05);padding:4px 14px;border:1px solid rgba(0,255,157,0.15);border-radius:20px;">
                        <i class="fas fa-user-circle me-1"></i>
                        <span class="text-uppercase fw-bold"><?php echo $_SESSION['user_nombre'] ?? 'ADMIN'; ?></span>
                    </div>
                </li>
                <!-- Fullscreen -->
                <li class="nav-item d-none d-md-block">
                    <a class="nav-link text-white" href="#" data-lte-toggle="fullscreen">
                        <i class="fa-solid fa-expand"></i>
                    </a>
                </li>
                <!-- Reloj -->
                <li class="nav-item">
                    <div class="nav-link font-monospace" style="font-size:0.75rem;color:#555;">
                        <i class="far fa-clock me-1"></i><span id="server-time"><?php echo date('H:i:s'); ?></span>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
    <!-- ── FIN HEADER ── -->

    <!-- ── RELOJ ────────────────────────────────────────────── -->
    <script>
    setInterval(() => {
        const el = document.getElementById('server-time');
        if (el) el.innerText = new Date().toLocaleTimeString('es-CL', {hour12: false});
    }, 1000);

    // ── FULLSCREEN PERSISTENCE ──────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        const fsBtn = document.querySelector('[data-lte-toggle="fullscreen"]');
        const requestFS = () => {
            const d = document.documentElement;
            if (d.requestFullscreen) d.requestFullscreen();
            else if (d.webkitRequestFullscreen) d.webkitRequestFullscreen();
            document.removeEventListener('click', requestFS);
        };
        if (localStorage.getItem('app_fullscreen') === 'true') {
            document.addEventListener('click', requestFS);
        }
        document.addEventListener('fullscreenchange', () => {
            localStorage.setItem('app_fullscreen', !!document.fullscreenElement);
        });
    });
    </script>

    <!-- NAVEGACIÓN MÓVIL V5 (NUCLEAR FIX) -->
    <script src="<?php echo \App\Config\Config::getAppUrl(); ?>/public/assets/js/mobile-nav.js"></script>
