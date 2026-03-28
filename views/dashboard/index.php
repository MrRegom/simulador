<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<style>
/* ============================================
   DASHBOARD A4R SIMRACING — HUD PRO v4
   Mobile-First. Full Width. Zero Grey Zones.
   ============================================ */

.dash-wrap {
    background: #000;
    min-height: 100vh;
    padding: 16px;
    width: 100%;
    box-sizing: border-box;
}

/* ---- BANNER IP ---- */
.ip-banner {
    background: linear-gradient(135deg, #0d0d0d 0%, #111 100%);
    border: 1px solid rgba(255,0,0,0.15);
    border-left: 3px solid #ff0000;
    border-radius: 12px;
    padding: 14px 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 22px;
    flex-wrap: wrap;
    gap: 10px;
}
.ip-left { display: flex; align-items: center; gap: 12px; }
.ip-label { font-size: 0.65rem; color: #555; text-transform: uppercase; font-weight: 700; letter-spacing: 1px; }
.ip-value { font-family: 'Orbitron', sans-serif; color: #ff2222; font-weight: 700; font-size: 1rem; }
.live-badge {
    display: flex; align-items: center; gap: 6px;
    background: rgba(0,255,100,0.06); border: 1px solid rgba(0,255,100,0.2);
    border-radius: 20px; padding: 4px 12px;
    font-size: 0.65rem; color: #00ff64; font-weight: 700;
}
.live-dot { width: 6px; height: 6px; border-radius: 50%; background: #00ff64; animation: blink 1s infinite; }
@keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.2} }

/* ---- SECCIÓN TITLE ---- */
.sec-title {
    font-size: 0.7rem;
    font-weight: 800;
    color: #fff;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.sec-title::after {
    content: '';
    flex: 1;
    height: 1px;
    background: rgba(255,255,255,0.06);
}

/* ---- MÉTRICAS GRID ---- */
.metrics-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin-bottom: 24px;
}

.metric-card {
    background: #0a0a0a;
    border: 1px solid rgba(255,255,255,0.05);
    border-radius: 14px;
    padding: 18px;
    position: relative;
    overflow: hidden;
    transition: border-color 0.3s, transform 0.3s;
}
.metric-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0;
    width: 3px; height: 100%;
    background: #1a1a1a;
}
.metric-card.accent::before { background: #ff0000; }
.metric-card.success::before { background: #00ff64; }
.metric-card.info::before   { background: #0088ff; }
.metric-card.gold::before   { background: #ffcc00; }

.metric-card:hover { border-color: rgba(255,0,0,0.2); transform: translateY(-2px); }

.m-label {
    font-size: 0.6rem;
    color: #555;
    text-transform: uppercase;
    font-weight: 700;
    letter-spacing: 1.5px;
    margin-bottom: 8px;
    display: block;
}
.m-value {
    font-size: 2.4rem;
    font-weight: 900;
    color: #fff;
    line-height: 1;
}
.m-value.small-val { font-size: 1.6rem; }
.m-icon {
    position: absolute;
    right: 16px;
    bottom: 14px;
    font-size: 1.8rem;
    color: rgba(255,255,255,0.04);
}

/* ---- ACCESOS DIRECTOS ---- */
.shortcuts-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin-bottom: 24px;
}

.sc-item {
    background: #0d0d0d;
    border: 1px solid rgba(255,255,255,0.05);
    border-radius: 14px;
    padding: 20px 16px;
    text-align: center;
    text-decoration: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    transition: all 0.25s ease;
}
.sc-item:hover {
    background: #ff0000;
    border-color: #ff0000;
    transform: scale(1.02);
    text-decoration: none;
}
.sc-item i { font-size: 1.6rem; color: #ff2222; transition: 0.25s; }
.sc-item:hover i { color: #fff; }
.sc-item span { color: #ccc; font-weight: 600; font-size: 0.8rem; line-height: 1.3; transition: 0.25s; }
.sc-item:hover span { color: #fff; }

/* ============== RESPONSIVE ============== */
/* Tablet → 3 columnas */
@media (min-width: 768px) {
    .dash-wrap { padding: 25px; }
    .metrics-grid { grid-template-columns: repeat(4, 1fr); gap: 16px; }
    .shortcuts-grid { grid-template-columns: repeat(4, 1fr); gap: 16px; }
    .m-value { font-size: 2.8rem; }
    .ip-value { font-size: 1.2rem; }
}

/* Desktop */
@media (min-width: 1200px) {
    .dash-wrap { padding: 30px; }
}

/* Mobile pequeño → forzar 1 columna para accesos */
@media (max-width: 380px) {
    .shortcuts-grid { grid-template-columns: 1fr; }
    .metrics-grid { gap: 10px; }
    .m-value { font-size: 2rem; }
}
</style>

<div class="dash-wrap">

    <!-- ── BANNER IP ── -->
    <div class="ip-banner">
        <div class="ip-left">
            <i class="fas fa-network-wired" style="color:#444;font-size:1.4rem;"></i>
            <div>
                <div class="ip-label">Punto de Entrega (LAN)</div>
                <div class="ip-value"><?php echo $server_ip; ?></div>
            </div>
        </div>
        <div class="live-badge">
            <span class="live-dot"></span> SISTEMA ONLINE
        </div>
    </div>

    <!-- ── MÉTRICAS ── -->
    <p class="sec-title"><i class="fas fa-chart-bar text-danger me-1"></i>Resumen del Sistema</p>

    <div class="metrics-grid">
        <div class="metric-card info">
            <span class="m-label">Simuladores</span>
            <div class="m-value"><?php echo $stats['equipos_activos']; ?></div>
            <i class="fas fa-desktop m-icon"></i>
        </div>
        <div class="metric-card accent">
            <span class="m-label">En Curso</span>
            <div class="m-value"><?php echo $stats['en_uso']; ?></div>
            <i class="fas fa-play m-icon"></i>
        </div>
        <div class="metric-card success">
            <span class="m-label">Disponibles</span>
            <div class="m-value"><?php echo $stats['equipos_activos'] - $stats['en_uso']; ?></div>
            <i class="fas fa-check-circle m-icon"></i>
        </div>
        <div class="metric-card gold">
            <span class="m-label">Caja del Día</span>
            <div class="m-value small-val">$<?php echo number_format($stats['ingresos_dia'], 0, ',', '.'); ?></div>
            <i class="fas fa-wallet m-icon"></i>
        </div>
    </div>

    <!-- ── ACCESOS DIRECTOS ── -->
    <p class="sec-title"><i class="fas fa-bolt text-warning me-1"></i>Accesos Directos</p>

    <div class="shortcuts-grid">
        <a href="<?php echo \App\Config\Config::getAppUrl(); ?>/simuladores" class="sc-item">
            <i class="fas fa-tv"></i>
            <span>Monitor de Estaciones</span>
        </a>
        <a href="<?php echo \App\Config\Config::getAppUrl(); ?>/pagos" class="sc-item">
            <i class="fas fa-receipt"></i>
            <span>Control de Caja</span>
        </a>
        <a href="<?php echo \App\Config\Config::getAppUrl(); ?>/ranking" class="sc-item">
            <i class="fas fa-trophy"></i>
            <span>Ranking Vivo</span>
        </a>
        <a href="<?php echo \App\Config\Config::getAppUrl(); ?>/reportes" class="sc-item">
            <i class="fas fa-chart-line"></i>
            <span>Reportes Financieros</span>
        </a>
    </div>

</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
