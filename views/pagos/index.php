<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<style>
/* 1. CORE FUTURISTA - CASH HUD A4R */
@import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700;800&family=Orbitron:wght@400;700;900&display=swap');

body { 
    font-family: 'JetBrains Mono', monospace !important; 
    background-color: #000; 
    color: #fff;
}

/* 2. CONTENEDOR DE CAJA */
.caja-container { 
    background: #000; 
    min-height: calc(100vh - 60px); 
    padding: 30px;
    background-image: radial-gradient(circle at bottom left, rgba(255,0,0,0.03) 0%, transparent 40%);
}

.cyber-section-title {
    font-family: 'Orbitron', sans-serif;
    color: #fff;
    text-transform: uppercase;
    letter-spacing: 2px;
    border-left: 4px solid #ff0000;
    padding-left: 15px;
    margin-bottom: 30px;
}

/* Stats de Caja */
.cash-stats {
    display: flex;
    gap: 20px;
    margin-bottom: 30px;
}

.stats-card {
    flex: 1;
    background: #050505;
    border: 1px solid #111;
    padding: 25px;
    position: relative;
    clip-path: polygon(0 0, 100% 0, 100% 85%, 90% 100%, 0 100%);
}

.stats-card.main {
    border-color: rgba(255,0,0,0.3);
    background: radial-gradient(circle at top right, #0a0000 0%, #050505 100%);
}

.stats-card.main::after {
    content: '';
    position: absolute;
    top: 5px; right: 20px;
    width: 6px; height: 6px;
    background: #ff0000;
    border-radius: 50%;
    box-shadow: 0 0 10px #ff0000;
}

.stats-label {
    display: block;
    font-family: 'Orbitron';
    font-size: 0.65rem;
    color: #444;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 10px;
}

.stats-value {
    font-family: 'Orbitron';
    font-size: 2.2rem;
    font-weight: 900;
    color: #fff;
}

.stats-value.main {
    color: #ff0000;
    text-shadow: 0 0 20px rgba(255,0,0,0.3);
}

.stats-footer {
    font-family: 'JetBrains Mono';
    font-size: 0.6rem;
    color: #222;
    margin-top: 15px;
    display: block;
}

/* Tabla de Movimientos */
.move-card {
    background: #050505;
    border: 1px solid #111;
}

.move-table { margin: 0; }

.move-table thead th {
    background: #0a0a0a;
    border-bottom: 2px solid #222;
    color: #444;
    font-family: 'Orbitron';
    font-size: 0.7rem;
    padding: 20px;
}

.move-table td {
    padding: 18px 20px;
    border-bottom: 1px solid #111;
    color: #888;
}

.move-row:hover {
    background: rgba(255,255,255,0.01);
}

.move-concept {
    font-family: 'Orbitron';
    color: #fff;
    font-weight: 700;
    font-size: 0.9rem;
}

.badge-method {
    font-family: 'Orbitron';
    padding: 4px 10px;
    font-size: 0.6rem;
    font-weight: 900;
    clip-path: polygon(10% 0, 100% 0, 90% 100%, 0 100%);
}

.badge-efectivo { background: rgba(0,255,157,0.1); color: #00ff9d; }
.badge-tarjeta { background: rgba(0,136,255,0.1); color: #0088ff; }

.btn-caja-action {
    background: #ff0000;
    color: #fff;
    font-family: 'Orbitron';
    font-weight: 900;
    border: none;
    padding: 10px 20px;
    font-size: 0.8rem;
    clip-path: polygon(0 0, 100% 0, 100% 70%, 85% 100%, 0 100%);
    transition: all 0.2s;
    text-decoration: none;
}

.btn-caja-action:hover {
    background: #fff;
    color: #000;
    transform: translateY(-2px);
}

/* 📱 ADAPTACIÓN RESPONSIVA CELULARES Y IPAD */
@media (max-width: 991px) {
    .app-wrapper .app-main { margin-left: 0 !important; }
}

@media (max-width: 768px) {
    .caja-container {
        padding: 5px; /* Menos margen lateral para dar espacio a la tabla/tarjetas */
        box-sizing: border-box;
        overflow-x: hidden;
    }
    
    .cash-stats { 
        flex-direction: column; 
        gap: 15px;
    }
    
    .cyber-section-title {
        font-size: 0.9rem !important;
        margin-bottom: 0 !important;
        width: 100%;
        word-break: break-all; /* Permite dividir CONTROL_DE_CAJA... y evitar salidas de margen */
    }
    
    /* Forzar que los botones de acción llenen todo el ancho rompiendo el flex en filas */
    .w-sm-100 > button {
        flex: 1 1 100% !important;
        margin-bottom: 5px;
    }
    
    .btn-caja-action {
        padding: 12px;
        width: 100%;
        text-align: center;
        white-space: normal;
        word-wrap: break-word;
        font-size: 0.8rem;
        clip-path: none !important; /* Desactiva el corte poligonal que cortaba la letra de 'NUEVA_TRANSACCIÓN' */
    }
    
    .export-btn {
        width: 100%;
    }
    
    /* Hacer que la tabla de movimientos y sus celdas se compacten más */
    .move-table td, .move-table th {
        padding: 10px !important;
        font-size: 0.65rem !important;
        word-break: break-all;
    }
    
    .hide-on-mobile { display: none !important; }
}
</style>

<div class="caja-container">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <h4 class="cyber-section-title m-0">CONTROL_DE_CAJA_EN_VIVO</h4>
            
            <div class="d-flex gap-2 flex-wrap w-100 flex-column flex-md-row">
                <button class="export-btn" style="background:transparent; border:1px solid #333; color:#555; padding:8px 15px; font-family:'Orbitron'; font-size:0.7rem; font-weight:900;">
                    <i class="fas fa-print me-2"></i> CORTE_Z
                </button>
                <button class="btn-caja-action flex-grow-1" onclick="alert('Funcionalidad de movimiento en desarrollo')">
                    <i class="fas fa-cash-register me-2"></i> NUEVA_TRANSACCIÓN
                </button>
            </div>
        </div>

        <!-- Resumen Financiero Diario -->
        <div class="cash-stats">
            <div class="stats-card main">
                <span class="stats-label">SALDO_EFECTIVO_HOY</span>
                <div class="stats-value main">$<?php echo number_format($total_hoy, 0, ',', '.'); ?></div>
                <span class="stats-footer">ESTADO: CAJA_OPERATIVA_SIN_ERRORES</span>
            </div>
            <div class="stats-card">
                <span class="stats-label">INGRESOS_SEMANALES</span>
                <div class="stats-value">$<?php echo number_format($resumen['semana'], 0, ',', '.'); ?></div>
                <span class="stats-footer">CICLO: 7_DÍAS_FINANCIEROS</span>
            </div>
            <div class="stats-card">
                <span class="stats-label">PROYECCIÓN_MENSUAL</span>
                <div class="stats-value">$<?php echo number_format($resumen['mes'], 0, ',', '.'); ?></div>
                <span class="stats-footer">DATA: INGRESOS_ESTIMADOS_MES</span>
            </div>
        </div>

        <!-- Historial de Movimientos Recientes -->
        <div class="move-card">
            <div class="table-responsive">
                <table class="table move-table align-middle">
                    <thead>
                        <tr>
                            <th width="150" class="hide-on-mobile">HORA_LOG</th>
                            <th>CONCEPTO_Y_REFERENCIA</th>
                            <th class="text-center hide-on-mobile">MÉTODO_DE_PAGO</th>
                            <th class="text-end">IMPORTE_NETO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($ingresos)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <span class="text-muted font-monospace opacity-50">SIN_MOVIMIENTOS_REGISTRADOS_HOY</span>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach($ingresos as $i): ?>
                            <tr class="move-row">
                                <td class="hide-on-mobile">
                                    <span class="font-monospace text-white fw-bold"><?php echo date('H:i:s', strtotime($i['fecha_pago'])); ?></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div style="width:30px; height:30px; background:#111; border:1px solid #222; display:flex; align-items:center; justify-content:center; color:#ff0000; margin-right:15px; font-size:0.8rem;">
                                            <i class="fas fa-file-invoice-dollar"></i>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="move-concept">ALQUILER_ESTACIÓN [<?php echo $i['equipo']; ?>]</span>
                                            <small class="font-monospace text-muted" style="font-size:0.6rem;">PILOTO: <?php echo htmlspecialchars(strtoupper($i['cliente'] ?? 'INVITADO')); ?></small>
                                            <small class="font-monospace text-muted d-md-none" style="font-size:0.6rem;">MÉTODO: <?php echo strtoupper($i['metodo_pago']); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center hide-on-mobile">
                                    <?php if($i['metodo_pago'] == 'efectivo'): ?>
                                        <span class="badge-method badge-efectivo">EFECTIVO</span>
                                    <?php else: ?>
                                        <span class="badge-method badge-tarjeta">TARJETA/DÉBITO</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex flex-column align-items-end">
                                        <span style="font-family:'Orbitron'; font-size:1.1rem; color:#fff; font-weight:900;">$<?php echo number_format($i['monto'], 0, ',', '.'); ?></span>
                                        <small style="color:#00ff9d; font-size:0.5rem; letter-spacing:1px;">SINCRONIZADO</small>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
