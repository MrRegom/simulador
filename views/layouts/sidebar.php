<?php
$path = $_SERVER['REQUEST_URI'];
?>
<!-- Sidebar AdminLTE 4 -->
<style>
    .app-sidebar .nav-link { padding-top: 5px !important; padding-bottom: 5px !important; }
    .app-sidebar .nav-link p { font-size: 0.88rem !important; }
    .app-sidebar .nav-icon { font-size: 0.95rem !important; width: 1.5rem !important; }
    .app-sidebar .nav-header { 
        font-size: 0.72rem !important; 
        padding: 20px 15px 5px !important; 
        letter-spacing: 1.5px; 
        color: rgba(255,255,255,0.4) !important;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        margin-bottom: 10px;
        font-weight: 800;
    }
</style>
<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <!-- Brand Area Con Altura Sincronizada -->
    <div class="sidebar-brand d-flex align-items-center" style="background: #000; border-bottom: 2px solid #222; height: 70px; padding: 0 15px;">
        <!-- Sándwich Pegado a la Izquierda -->
        <a class="nav-link text-white me-3" data-lte-toggle="sidebar" href="#" role="button" style="font-size: 1.2rem;">
            <i class="fas fa-bars"></i>
        </a>
        <!-- Logo a continuación -->
        <a href="<?php echo \App\Config\Config::getAppUrl(); ?>/dashboard" class="brand-link p-0 flex-grow-1 text-center">
            <img src="<?php echo \App\Config\Config::getAppUrl(); ?>/public/assets/img/logoa4r.png" alt="A4R Logo" style="height: 52px; filter: drop-shadow(0 0 15px rgba(255,0,0,0.6));">
        </a>
    </div>

    <!-- Sidebar Menu -->
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">
                <li class="nav-header">MAIN</li>
                <li class="nav-item">
                    <a href="<?php echo \App\Config\Config::getAppUrl(); ?>/dashboard" class="nav-link <?php echo strpos($path, 'dashboard') !== false ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo \App\Config\Config::getAppUrl(); ?>/simuladores" class="nav-link <?php echo strpos($path, 'simuladores') !== false ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-desktop"></i>
                        <p>Simuladores</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo \App\Config\Config::getAppUrl(); ?>/equipos" class="nav-link <?php echo strpos($path, 'equipos') !== false ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-gamepad"></i>
                        <p>Equipos</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo \App\Config\Config::getAppUrl(); ?>/ranking" class="nav-link <?php echo strpos($path, 'ranking') !== false && strpos($path, 'display') === false && strpos($path, 'config') === false && strpos($path, 'admin') === false ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-trophy"></i>
                        <p>Ranking Vivo</p>
                    </a>
                </li>

                <li class="nav-header">GESTIÓN</li>
                <li class="nav-item">
                    <a href="<?php echo \App\Config\Config::getAppUrl(); ?>/pagos" class="nav-link <?php echo strpos($path, 'pagos') !== false ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-wallet"></i>
                        <p>Caja</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo \App\Config\Config::getAppUrl(); ?>/reportes" class="nav-link <?php echo strpos($path, 'reportes') !== false ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>Reportes</p>
                    </a>
                </li>
 
                <li class="nav-header">CLIENTES</li>
                <li class="nav-item">
                    <a href="<?php echo \App\Config\Config::getAppUrl(); ?>/clientes_reg" class="nav-link <?php echo strpos($path, 'clientes_reg') !== false ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-user-friends"></i>
                        <p>Base Clientes</p>
                    </a>
                </li>

                <li class="nav-header">AJUSTES VISOR</li>
                <li class="nav-item">
                    <a href="<?php echo \App\Config\Config::getAppUrl(); ?>/ranking/admin" class="nav-link <?php echo strpos($path, 'ranking/admin') !== false ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-list-ol"></i>
                        <p>Ranking</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo \App\Config\Config::getAppUrl(); ?>/pistas" class="nav-link <?php echo strpos($path, 'pistas') !== false ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-road"></i>
                        <p>Pistas</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo \App\Config\Config::getAppUrl(); ?>/ranking/config" class="nav-link <?php echo strpos($path, 'ranking/config') !== false ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-ad"></i>
                        <p>Publicidad</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo \App\Config\Config::getAppUrl(); ?>/ranking/display" target="_blank" class="nav-link">
                        <i class="nav-icon fas fa-tv"></i>
                        <p>Lanzar Visor</p>
                    </a>
                </li>

                <?php if(\App\Services\AuthService::isAdmin()): ?>
                <li class="nav-header">ADMINISTRACIÓN</li>
                <li class="nav-item">
                    <a href="<?php echo \App\Config\Config::getAppUrl(); ?>/usuarios" class="nav-link <?php echo strpos($path, '/usuarios') !== false ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-users-cog"></i>
                        <p>Usuarios</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo \App\Config\Config::getAppUrl(); ?>/roles" class="nav-link <?php echo strpos($path, '/roles') !== false ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-shield-alt"></i>
                        <p>Permisos y Roles</p>
                    </a>
                </li>
                <?php endif; ?>
                
                <li class="nav-item mt-4">
                    <a href="<?php echo \App\Config\Config::getAppUrl(); ?>/logout" class="nav-link text-danger">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>CERRAR SESIÓN</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>

<!-- Inicio de Contenido Principal (Cerrado en footer.php) -->
<main class="app-main">
    <div class="app-content pt-3">
        <div class="container-fluid">
