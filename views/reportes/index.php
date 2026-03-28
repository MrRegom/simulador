<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<style>
/* 1. CORE FUTURISTA - AUDIT HUD A4R */
@import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700;800&family=Orbitron:wght@400;700;900&display=swap');

body { 
    font-family: 'JetBrains Mono', monospace !important; 
    background-color: #000; 
    color: #fff;
}

/* 2. CONTENEDOR DE REPORTES */
.report-container { 
    background: #000; 
    min-height: calc(100vh - 60px); 
    padding: 30px;
}

.cyber-section-title {
    font-family: 'Orbitron', sans-serif;
    color: #fff;
    margin-bottom: 0 !important;
    text-transform: uppercase;
    letter-spacing: 2px;
    border-left: 4px solid #ff0000;
    padding-left: 15px;
}

/* Filtros Compactos HUD */
.filter-hud-bar {
    background: #0a0a0a;
    border: 1px solid #1a1a1a;
    padding: 10px 20px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    position: relative;
}

.filter-hud-bar::before {
    content: '';
    position: absolute;
    top: 0; left: 0; width: 4px; height: 100%;
    background: #ff0000;
}

/* Tab Switcher Compacto */
.compact-tabs {
    background: #111;
    padding: 3px;
    display: inline-flex;
    gap: 2px;
}

.tab-item {
    padding: 6px 15px;
    font-family: 'Orbitron', sans-serif;
    font-size: 0.65rem;
    font-weight: 900;
    color: #444;
    text-transform: uppercase;
    cursor: pointer;
    border: none;
    background: transparent;
    transition: all 0.2s;
}

.tab-item.active {
    background: #ff0000;
    color: #fff;
}

.input-cyber-sm {
    background: #000 !important;
    border: 1px solid #333 !important;
    color: #fff !important;
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.75rem !important;
    height: 32px;
    padding: 0 10px;
}

/* Resumen Financiero Top */
.finance-stats {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
}

.stat-box {
    flex: 1;
    background: #050505;
    border: 1px solid #111;
    padding: 20px;
    position: relative;
    overflow: hidden;
}

.stat-box.highlight {
    border-color: rgba(255,0,0,0.2);
}

.stat-label {
    font-family: 'Orbitron', sans-serif;
    font-size: 0.6rem;
    color: #555;
    text-transform: uppercase;
    letter-spacing: 1px;
    display: block;
    margin-bottom: 5px;
}

.stat-value {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.8rem;
    font-weight: 900;
    color: #fff;
}

.stat-value.red { 
    color: #ff0000; 
    text-shadow: 0 0 15px rgba(255,0,0,0.3);
}

/* Tabla de Auditoría */
.audit-card {
    background: #050505;
    border: 1px solid #111;
}

.audit-table {
    margin: 0;
    color: #888;
}

.audit-table thead th {
    background: #0a0a0a;
    border-bottom: 2px solid #222;
    color: #444;
    font-family: 'Orbitron';
    font-size: 0.65rem;
    padding: 15px 20px;
    letter-spacing: 1px;
}

.audit-table td {
    padding: 15px 20px;
    border-bottom: 1px solid #111;
}

.audit-row:hover {
    background: rgba(255,0,0,0.01);
}

.text-white-bright { color: #fff !important; font-weight: 700; }

.export-btn {
    background: transparent;
    border: 1px solid #444;
    color: #888;
    font-family: 'Orbitron';
    font-size: 0.65rem;
    padding: 8px 15px;
    font-weight: 900;
    text-transform: uppercase;
    transition: all 0.2s;
}

.export-btn:hover {
    border-color: #ff0000;
    color: #ff0000;
    box-shadow: 0 0 10px rgba(255,0,0,0.2);
}

/* 📱 ADAPTACIÓN RESPONSIVA CELULARES Y IPAD */
@media (max-width: 991px) {
    .app-wrapper .app-main { margin-left: 0 !important; }
}

@media (max-width: 768px) {
    .report-container {
        padding: 5px;
        box-sizing: border-box;
        overflow-x: hidden;
    }
    
    .finance-stats { 
        flex-direction: column; 
        gap: 15px;
    }
    
    .cyber-section-title {
        font-size: 0.9rem !important;
        margin-bottom: 15px !important;
        width: 100%;
        word-break: break-all;
    }
    
    .export-btn {
        width: 100%;
        text-align: center;
        padding: 12px;
        font-size: 0.8rem;
    }
    
    .filter-hud-bar { padding: 10px; }
    
    .compact-tabs { 
        flex-wrap: wrap; 
        justify-content: center; 
        width: 100%; 
    }
    
    .tab-item { 
        flex: 1 1 40%; 
        text-align: center; 
        padding: 8px; 
    }
    
    .audit-table td, .audit-table th {
        padding: 10px !important;
        font-size: 0.65rem !important;
        word-break: break-all;
    }
    
    .hide-on-mobile { display: none !important; }
}
</style>

<div class="report-container">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <h4 class="cyber-section-title">AUDITORÍA_Y_FINANZAS_V1</h4>
        
        <div class="d-flex w-sm-100 flex-grow-1 justify-content-end">
            <button class="export-btn w-sm-100" onclick="alert('Exportando registros corporativos...')">
                <i class="fas fa-file-csv me-2"></i> EXPORTAR_DATOS_MAESTROS
            </button>
        </div>
    </div>
    
    <!-- Filtros Inteligentes Compactos -->
    <div class="filter-hud-bar">
        <form action="reportes" method="GET" class="d-flex flex-column flex-lg-row align-items-lg-center gap-3 w-100">
            <!-- Selector de Periodo -->
            <div class="d-flex align-items-center gap-2">
                <span class="stat-label" style="font-size:0.5rem">PERIODO_ACTUAL</span>
                <div class="compact-tabs">
                    <button type="submit" name="tipo" value="dia" class="tab-item <?php echo $tipo_filtro == 'dia' ? 'active' : ''; ?>">DÍA</button>
                    <button type="submit" name="tipo" value="semana" class="tab-item <?php echo $tipo_filtro == 'semana' ? 'active' : ''; ?>">SEMANA</button>
                    <button type="submit" name="tipo" value="mes" class="tab-item <?php echo $tipo_filtro == 'mes' ? 'active' : ''; ?>">MES</button>
                    <button type="submit" name="tipo" value="anio" class="tab-item <?php echo $tipo_filtro == 'anio' ? 'active' : ''; ?>">AÑO</button>
                </div>
            </div>

            <!-- Inputs de Fecha Dinámicos -->
            <div class="d-flex align-items-center flex-wrap gap-2 flex-grow-1">
                <?php if($tipo_filtro == 'dia'): ?>
                    <input type="date" name="fecha" class="form-control input-cyber-sm" value="<?php echo $filtros['fecha']; ?>" onchange="this.form.submit()">
                <?php elseif($tipo_filtro == 'semana'): ?>
                    <input type="week" name="semana" class="form-control input-cyber-sm" value="<?php echo $filtros['semana']; ?>" onchange="this.form.submit()">
                <?php elseif($tipo_filtro == 'mes'): ?>
                    <select name="mes" class="form-select input-cyber-sm" onchange="this.form.submit()">
                        <?php 
                        $meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
                        foreach($meses as $i => $m): $val = str_pad($i+1, 2, '0', STR_PAD_LEFT); ?>
                            <option value="<?php echo $val; ?>" <?php echo $filtros['mes'] == $val ? 'selected' : ''; ?>><?php echo $m; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="anio" class="form-select input-cyber-sm" onchange="this.form.submit()">
                        <?php for($a = date('Y'); $a >= 2024; $a--): ?>
                            <option value="<?php echo $a; ?>" <?php echo $filtros['anio'] == $a ? 'selected' : ''; ?>><?php echo $a; ?></option>
                        <?php endfor; ?>
                    </select>
                <?php else: ?>
                    <select name="anio" class="form-select input-cyber-sm" onchange="this.form.submit()">
                        <?php for($a = date('Y'); $a >= 2024; $a--): ?>
                            <option value="<?php echo $a; ?>" <?php echo $filtros['anio'] == $a ? 'selected' : ''; ?>><?php echo $a; ?></option>
                        <?php endfor; ?>
                    </select>
                <?php endif; ?>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="badge" style="background: rgba(0,255,157,0.1); color: #00ff9d; font-family:'JetBrains Mono'; font-size: 0.6rem;">CANAL_DE_DATOS_ACTIVO</span>
            </div>
        </form>
    </div>

    <!-- Resumen Premium de Ingresos -->
    <div class="finance-stats">
        <div class="stat-box highlight">
            <span class="stat-label">RECAUDACIÓN_TOTAL_PERIODO</span>
            <div class="stat-value red">$<?php echo number_format($total_periodo, 0, ',', '.'); ?></div>
            <div style="position:absolute; bottom:-10px; right:-10px; opacity:0.05; font-size:5rem;">
                <i class="fas fa-wallet"></i>
            </div>
        </div>
        <div class="stat-box">
            <span class="stat-label">TRANSACCIONES_PROCESADAS</span>
            <div class="stat-value"><?php echo count($trazabilidad); ?></div>
            <span class="stat-label" style="color:#222; margin-top:5px;">INTEGRIDAD_DE_LOG_VERIFICADA</span>
        </div>
        <div class="stat-box">
            <span class="stat-label">TICKET_PROMEDIO</span>
            <div class="stat-value">$<?php echo count($trazabilidad) > 0 ? number_format($total_periodo / count($trazabilidad), 0, ',', '.') : '0'; ?></div>
        </div>
    </div>

    <!-- Feed de Datos Detallado -->
    <div class="audit-card">
        <div class="table-responsive">
            <table class="table audit-table align-middle">
                <thead>
                    <tr>
                        <th width="180">FECHA_Y_HORA</th>
                        <th class="hide-on-mobile">DETALLE_DEL_PILOTO</th>
                        <th>CÓDIGO_UNIDAD</th>
                        <th class="text-center hide-on-mobile">TIEMPO_MISIÓN</th>
                        <th class="hide-on-mobile">PASARELA_DE_PAGO</th>
                        <th class="text-end">MONTO_NETO</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($trazabilidad)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <span class="text-muted font-monospace opacity-50">ERROR: NO_SE_DETECTARON_FLUJOS_DE_DATOS</span>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($trazabilidad as $t): ?>
                        <tr class="audit-row">
                            <td>
                                <div class="d-flex flex-column font-monospace">
                                    <span class="text-white-bright"><?php echo date('d/m/Y', strtotime($t['fecha_pago'])); ?></span>
                                    <small class="text-muted" style="font-size:0.6rem;"><?php echo date('H:i:s', strtotime($t['fecha_pago'])); ?></small>
                                </div>
                            </td>
                            <td class="hide-on-mobile">
                                <div class="d-flex align-items-center">
                                    <div style="width:35px; height:35px; background:#111; border:1px solid #333; display:flex; align-items:center; justify-content:center; color:#ff0000; margin-right:12px;">
                                        <i class="fas fa-user-circle"></i>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-white-bright"><?php echo strtoupper($t['cliente'] ?? 'PILOTO_INVITADO'); ?></span>
                                        <small class="text-muted font-monospace" style="font-size:0.6rem;">ID: <?php echo $t['rut'] ?? 'ANÓNIMO'; ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="background:#111; border:1px solid #222; padding:4px 10px; font-family:'Orbitron'; font-size:0.7rem; color:#fff; display:inline-block;">
                                    <?php echo $t['equipo']; ?>
                                </div>
                            </td>
                            <td class="text-center hide-on-mobile">
                                <span class="font-monospace text-white"><?php echo $t['tiempo_asignado_min']; ?>_MINS</span>
                            </td>
                            <td class="hide-on-mobile">
                                <?php if($t['metodo_pago'] == 'efectivo'): ?>
                                    <span style="color:#00ff9d; font-family:'Orbitron'; font-size:0.6rem; font-weight:900;">[ EFECTIVO ]</span>
                                <?php else: ?>
                                    <span style="color:#0088ff; font-family:'Orbitron'; font-size:0.6rem; font-weight:900;">[ TARJETA/TRANSFERENCIA ]</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="d-flex flex-column align-items-end">
                                    <span style="color:#fff; font-family:'Orbitron'; font-size:1.1rem; font-weight:900;">$<?php echo number_format($t['monto'], 0, ',', '.'); ?></span>
                                    <small style="color:#00ff9d; font-size:0.5rem; letter-spacing:1px;">TRANSACCIÓN_EXITOSA</small>
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

<?php include __DIR__ . '/../layouts/footer.php'; ?>
