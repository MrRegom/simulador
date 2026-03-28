<?php
// Navbar Quantum
$path = $_SERVER['REQUEST_URI'];
?>
<nav class="navbar-quantum">
    <!-- Brand -->
    <a href="dashboard" class="brand-quantum">
        <i class="fas fa-microchip"></i> A4R <span style="font-weight: 300; margin-left: 5px; color: #fff;">OS</span>
    </a>
    
    <!-- Navigation Links -->
    <div class="nav-links-q">
        <a href="dashboard" class="nav-item-q <?php echo strpos($path, 'dashboard') !== false ? 'active' : ''; ?>">
            <i class="fas fa-th-large me-2"></i> Dashboard
        </a>
        <a href="equipos" class="nav-item-q <?php echo strpos($path, 'equipos') !== false ? 'active' : ''; ?>">
            <i class="fas fa-gamepad me-2"></i> Simuladores
        </a>
        <a href="sesiones" class="nav-item-q <?php echo strpos($path, 'sesiones') !== false ? 'active' : ''; ?>">
            <i class="fas fa-history me-2"></i> Historial
        </a>
        <a href="pagos" class="nav-item-q <?php echo strpos($path, 'pagos') !== false ? 'active' : ''; ?>">
            <i class="fas fa-wallet me-2"></i> Caja
        </a>
        <a href="reportes" class="nav-item-q <?php echo strpos($path, 'reportes') !== false ? 'active' : ''; ?>">
            <i class="fas fa-chart-pie me-2"></i> Analytics
        </a>
    </div>

    <!-- Right Side -->
    <div style="margin-left: auto; display: flex; align-items: center; gap: 20px;">
        <div style="text-align: right;">
            <span style="font-size: 0.7rem; color: #58687a; text-transform: uppercase; letter-spacing: 1px;">System Status</span>
            <div style="color: #00e396; font-size: 0.8rem; font-weight: 700;">● ONLINE</div>
        </div>
        <div style="background: rgba(255,255,255,0.05); padding: 5px 15px; border-radius: 4px; border: 1px solid #253040;">
            <span id="q-clock" style="color: #fff; font-weight: 700; font-family: monospace;">00:00:00</span>
        </div>
        
        <div style="width: 35px; height: 35px; background: linear-gradient(135deg, #00e396 0%, #008ffb 100%); border-radius: 50%; display: flex; justify-content: center; align-items: center; color: #fff; font-weight: 700; font-size: 0.8rem;">
            A4
        </div>
    </div>
</nav>

<script>
setInterval(() => {
    const now = new Date();
    document.getElementById('q-clock').innerText = now.toLocaleTimeString('es-CL', { hour12: false });
}, 1000);
</script>
