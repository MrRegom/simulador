<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<style>
/* EQUIPOS LAYOUT ADAPTATION */
.equipos-container { 
    background: #000; 
    min-height: calc(100vh - 65px); 
    padding: 30px;
}

/* Card HUD Table - Premium Version */
.hud-card {
    background: #0a0a0a;
    border: 1px solid rgba(255,255,255,0.05);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
}

.hud-table { margin-bottom: 0; }

.hud-table thead th {
    background: #111;
    border-bottom: 1px solid #222;
    color: #444;
    font-size: 0.65rem;
    padding: 18px 25px;
    letter-spacing: 2px;
    text-transform: uppercase;
}

.hud-table td {
    padding: 20px 25px;
    border-bottom: 1px solid rgba(255,255,255,0.02);
}

/* Status Indicators */
.status-pill-v2 {
    display: inline-flex;
    align-items: center;
    padding: 5px 12px;
    font-size: 0.65rem;
    font-weight: 700;
    border-radius: 4px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.status-active-v2 { background: rgba(0,255,157,0.1); color: #00ff9d; border: 1px solid rgba(0,255,157,0.2); }
.status-ready-v2 { background: #1a1a1a; color: #666; border: 1px solid #333; }

.ip-badge {
    font-family: 'Orbitron', sans-serif;
    color: #ff0000;
    font-weight: 700;
    font-size: 0.85rem;
}

.btn-action-hardware {
    width: 38px;
    height: 38px;
    background: #111;
    border: 1px solid #333;
    border-radius: 8px;
    color: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: 0.2s;
}

.btn-action-hardware:hover { background: #ff0000; border-color: #ff0000; transform: translateY(-2px); }
.btn-add-main {
    background: #ff0000;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 0.8rem;
    transition: 0.3s;
}
.btn-add-main:hover { background: #fff; color: #000; }

/* ── RESPONSIVE MOBILE TABLE (CARD LAYOUT) ── */
@media (max-width: 767.98px) {
    .equipos-container { padding: 15px; }
    
    .hud-table thead { display: none; }
    .hud-table, .hud-table tbody, .hud-table tr, .hud-table td {
        display: block;
        width: 100%;
        text-align: left !important;
    }
    
    .hud-table tr {
        margin-bottom: 20px;
        background: #000;
        border: 1px solid rgba(255,0,0,0.3) !important;
        border-radius: 12px;
        padding: 10px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.8);
    }
    
    .hud-table td {
        padding: 12px 10px !important;
        border: none !important;
        border-bottom: 1px solid rgba(255,255,255,0.05) !important;
        position: relative;
    }
    
    .hud-table td:last-child {
        border-bottom: none !important;
        text-align: center !important;
        padding-top: 20px !important;
    }
    
    .hud-table td::before {
        content: attr(data-label);
        display: block;
        font-family: 'Orbitron', sans-serif;
        font-size: 0.65rem;
        color: #ff0000;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-bottom: 8px;
        opacity: 0.8;
    }
    
    .hide-on-mobile { display: block !important; } /* Forzamos mostrar todo en mobile */
    .id-badge-small { display: inline-block; padding: 2px 8px; background: #222; border-radius: 4px; font-family: monospace; color: #888; }
    
    /* Botones centrales en cell card */
    .hud-table td:last-child .d-flex {
        justify-content: center !important;
    }
}
</style>

<div class="equipos-container">
    <div class="container-fluid">
        <div class="d-flex justify-content-end align-items-center mb-4">
            <button class="btn btn-add-main" data-bs-toggle="modal" data-bs-target="#modalEquipo" onclick="abrirModalNuevo()">
                <i class="fas fa-plus me-2"></i> NUEVO EQUIPO
            </button>
        </div>
        
        
        <div class="hud-card">
            <div class="table-responsive">
                <table class="table hud-table align-middle">
                    <thead>
                        <tr>
                            <th width="80" class="text-center hide-on-mobile">ID Sistema</th>
                            <th>Identificación Unidad</th>
                            <th>Dirección IP</th>
                            <th width="200" class="hide-on-mobile">Estado Actual</th>
                            <th width="150" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($equipos)): ?>
                        <tr>
                            <td colspan="5" class="py-5 text-center">
                                <span class="font-monospace text-muted opacity-50">ERROR: NO_SE_DETECTÓ_HARDWARE</span>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($equipos as $equipo): ?>
                            <tr>
                                <td data-label="ID Sistema" class="text-center hide-on-mobile">
                                    <div class="id-badge-small"><?php echo str_pad($equipo['id'], 2, '0', STR_PAD_LEFT); ?></div>
                                </td>
                                <td data-label="Identificación Unidad">
                                    <div class="d-flex align-items-center">
                                        <div class="sim-icon-box me-3" style="color:#ff0000; font-size:1.2rem; background:#111; width:45px; height:45px; display:flex; align-items:center; justify-content:center; border:1px solid #222;">
                                            <i class="fas fa-microchip"></i>
                                        </div>
                                        <div>
                                            <div class="sim-name-txt fw-bold" style="font-size:1.1rem;"><?php echo strtoupper($equipo['nombre']); ?></div>
                                            <small class="text-muted font-monospace" style="font-size:0.65rem; letter-spacing:1px;">SIMULADOR DE CARRERAS</small>
                                        </div>
                                    </div>
                                </td>
                                 <td data-label="Conectividad">
                                    <div class="d-flex flex-column">
                                        <?php 
                                        $ip = $equipo['ip_address'] ?? '0.0.0.0';
                                        $lastHeartbeat = !empty($equipo['ultimo_heartbeat']) ? strtotime($equipo['ultimo_heartbeat']) : 0;
                                        $isOnline = ($lastHeartbeat > (time() - 300)); // 5 minutos de margen
                                        $isOffline = !$isOnline || $ip == '0.0.0.0' || empty($ip);
                                        ?>
                                        <span class="ip-neon fw-bold <?php echo $isOffline ? 'text-muted opacity-50' : 'text-danger'; ?>" style="font-size:1rem; font-family: 'JetBrains Mono', monospace;">
                                            > <?php echo $isOffline ? 'SIN CONEXIÓN' : $ip; ?>
                                        </span>
                                        <small class="text-muted font-monospace mt-1" style="font-size:0.6rem;">PUERTO DE SESIÓN: 8080</small>
                                    </div>
                                </td>
                                 <td data-label="Estado Operativo" class="hide-on-mobile">
                                    <?php if ($equipo['estado'] == 'en_uso'): ?>
                                        <div class="status-pill-v2 status-active-v2">
                                            <i class="fas fa-radiation fa-spin me-2"></i> EN USO
                                        </div>
                                    <?php else: ?>
                                        <div class="status-pill-v2 status-ready-v2">
                                            <i class="fas fa-check-circle me-2"></i> DISPONIBLE
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td data-label="Acciones" class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <?php 
                                        $isVisible = isset($equipo['is_visible']) ? (bool)$equipo['is_visible'] : true; 
                                        ?>
                                        <button class="btn-action-hardware" onclick="toggleVisibility(<?php echo $equipo['id']; ?>)" title="<?php echo $isVisible ? 'Ocultar del Monitor' : 'Mostrar en Monitor'; ?>">
                                            <i class="fas <?php echo $isVisible ? 'fa-eye-slash' : 'fa-eye text-success'; ?>"></i>
                                        </button>
                                        <button class="btn-action-hardware" onclick="abrirModalEditar(<?php echo htmlspecialchars(json_encode($equipo)); ?>)" title="Configuración Técnica">
                                            <i class="fas fa-cog"></i>
                                        </button>
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

<!-- MODAL EDICIÓN TÉCNICA -->
<div class="modal fade" id="modalEquipo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border-0" style="border-radius: 12px;">
            <div class="modal-header border-bottom border-secondary">
                <h6 class="modal-title font-monospace fw-bold text-white">> CONFIGURACIÓN TÉCNICA DE UNIDAD</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formEquipo">
                    <input type="hidden" name="id" id="edit_id">
                    
                    <div class="mb-4 p-3 rounded" style="background: rgba(255,0,0,0.05); border: 1px solid rgba(255,0,0,0.1);">
                        <div class="small text-muted mb-1">ID Único de Sistema (Para Configurar .exe)</div>
                        <div class="h5 mb-0 fw-bold text-white font-monospace" id="display_id">--</div>
                    </div>

                    <div class="mb-3">
                        <label class="small text-muted mb-1">Nombre de la Unidad (Etiqueta Visual)</label>
                        <input type="text" name="nombre" id="edit_nombre" class="form-control bg-black border-secondary text-white" placeholder="Ej: SIMULADOR 07" required>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-danger w-100 py-2 fw-bold">GUARDAR EN BASE DE DATOS</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function getModal() {
    return bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEquipo'));
}

function abrirModalNuevo() {
    document.getElementById('formEquipo').reset();
    document.getElementById('edit_id').value = '';
    document.getElementById('display_id').innerText = 'NUEVO_REGISTRO';
    getModal().show();
}

function abrirModalEditar(equipo) {
    document.getElementById('edit_id').value = equipo.id;
    document.getElementById('display_id').innerText = equipo.id.toString().padStart(2, '0');
    document.getElementById('edit_nombre').value = equipo.nombre;
    getModal().show();
}

document.getElementById('formEquipo').onsubmit = async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());
    
    try {
        const res = await fetch(`${BASE_URL}/api/equipo/save`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        });
        const result = await res.json();
        if (result.success) {
            location.reload();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'ERROR DE REGISTRO',
                text: result.error || 'No se pudo guardar la configuración'
            });
        }
    } catch (e) {
        Swal.fire({
            icon: 'error',
            title: 'ERROR TÉCNICO',
            text: 'Error de conexión con el núcleo del sistema'
        });
    }
};

async function toggleVisibility(id) {
    const { isConfirmed } = await Swal.fire({
        title: 'CAMBIAR VISIBILIDAD',
        text: '¿Desea ocultar o mostrar este equipo en el monitor?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'SÍ, CAMBIAR',
        cancelButtonText: 'CANCELAR'
    });

    if (!isConfirmed) return;
    
    try {
        const res = await fetch(`${BASE_URL}/api/equipo/toggle-visibility`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id: id})
        });
        const data = await res.json();
        if (data.success) location.reload();
        else {
            Swal.fire({
                icon: 'error',
                title: 'ERROR',
                text: data.error || 'Error al actualizar visibilidad'
            });
        }
    } catch (e) {
        Swal.fire({
            icon: 'error',
            title: 'ERROR DE CONEXIÓN',
            text: 'No se pudo contactar con el servidor'
        });
    }
}
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
