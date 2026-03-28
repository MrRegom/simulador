<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<style>
/* PROFESSIONAL MONITOR LAYOUT */
.monitor-container { 
    background: #000; 
    min-height: calc(100vh - 60px); 
    padding: 20px;
}

.monitor-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.sim-card {
    background: #0a0a0a;
    border: 1px solid rgba(255,255,255,0.05);
    border-radius: 12px;
    padding: 25px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    min-height: 380px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
}

.sim-card:hover { 
    transform: translateY(-5px);
    border-color: rgba(255,0,0,0.3);
    box-shadow: 0 10px 30px rgba(255,0,0,0.05);
}

.station-badge {
    background: #111;
    border: 1px solid #333;
    padding: 6px 15px;
    border-radius: 6px;
    font-family: 'Orbitron', sans-serif;
    font-size: 0.75rem;
    color: #ff0000;
    width: fit-content;
    margin-bottom: 5px;
}

.sim-name { font-family: 'Inter', sans-serif; font-weight: 700; font-size: 1.15rem; color: #fff; line-height: 1.2; }
.sim-ip { font-family: 'Inter', sans-serif; font-size: 0.75rem; color: #555; margin-top: 4px; }
.sim-ip.active { color: #ff0000; opacity: 0.8; }

.timer-area {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    margin: 20px 0;
}

.timer-display {
    font-family: 'Orbitron', sans-serif;
    font-size: 4.8rem;
    font-weight: 700;
    color: #1a1a1a;
    letter-spacing: -2px;
    transition: 0.5s;
}

.status-active .timer-display { color: #fff; text-shadow: 0 0 20px rgba(255,0,0,0.2); }

.status-label { font-size: 0.6rem; letter-spacing: 3px; color: #222; font-weight: 700; }
.status-active .status-label { color: #444; }

/* Actions */
.btn-action-main {
    background: #111;
    color: #fff;
    border: 1px solid #333;
    padding: 15px;
    border-radius: 8px;
    font-family: 'Inter', sans-serif;
    font-weight: 600;
    font-size: 0.9rem;
    width: 100%;
    transition: 0.2s;
    cursor: pointer;
}

.btn-start { background: #ff0000; border: none; }
.btn-start:hover { background: #fff; color: #000; transform: scale(1.02); }

.btn-group-sm { display: flex; gap: 10px; width: 100%; }
.btn-add { background: #1a1a1a; border-color: #ff0000; color: #ff0000; flex: 1; }
.btn-add:hover { background: #ff0000; color: #fff; }
.btn-stop { width: 55px; background: #000; color: #333; }
.btn-stop:hover { background: #ff0000; color: #fff; border-color: #ff0000; }

/* Responsive */
@media (max-width: 1200px) { 
    .monitor-grid { grid-template-columns: repeat(2, 1fr); } 
}

@media (max-width: 768px) { 
    .monitor-grid { 
        grid-template-columns: 1fr; 
        gap: 15px;
    } 
    .monitor-container {
        padding: 15px 10px;
    }
}

@media (max-width: 480px) {
    .sim-card {
        padding: 20px 15px;
        min-height: 280px;
    }
    .timer-display {
        font-size: 2.8rem;
        letter-spacing: -2px;
    }
    .station-badge {
        padding: 4px 10px;
    }
    .sim-name {
        font-size: 1rem;
    }
    .btn-action-main {
        padding: 12px;
        font-size: 0.85rem;
    }
    .monitor-header-actions {
        justify-content: center !important;
        margin-bottom: 20px !important;
    }
}
</style>

<div class="monitor-container">
    <div class="container-fluid p-0">
        <div class="d-flex justify-content-end align-items-center mb-4 monitor-header-actions">
            <div class="status-indicator d-flex align-items-center" style="font-size: 0.7rem; color: #555;">
                <i class="fas fa-sync fa-spin me-2"></i> MONITOREO ACTIVO
            </div>
        </div>
        <div class="monitor-grid">
        <?php foreach($equipos as $index => $equipo): 
            $isActive = ($equipo['estado'] == 'en_uso');
            $ip = $equipo['ip_address'] ?? '0.0.0.0';
            
            // Lógica de Conexión Real (Heartbeat)
            $lastHeartbeat = !empty($equipo['ultimo_heartbeat']) ? strtotime($equipo['ultimo_heartbeat']) : 0;
            $isOnline = ($lastHeartbeat > (time() - 300)); // 5 minutos de margen
            $isOffline = !$isOnline || $ip == '0.0.0.0' || empty($ip);
            
            $seconds = $isActive ? ($equipo['tiempo_restante'] ?? 0) : 0;
            $cardClass = $isActive ? 'status-active' : '';
        ?>
        
        <div class="sim-card <?php echo $cardClass; ?>" id="card-<?php echo $equipo['id']; ?>" data-seconds="<?php echo $seconds; ?>">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="station-badge">ESTACIÓN_<?php echo str_pad($index + 1, 2, '0', STR_PAD_LEFT); ?></div>
                    <div class="sim-name"><?php echo strtoupper($equipo['nombre']); ?></div>
                    <div class="sim-ip <?php echo !$isOffline ? 'active' : ''; ?>">
                        <i class="fas fa-network-wired me-1"></i> <?php echo $isOffline ? 'SIN CONEXIÓN' : $ip; ?>
                    </div>
                </div>
                <div class="status-indicator">
                    <span class="dot" style="height: 8px; width: 8px; background: <?php echo $isActive ? '#00ff9d' : '#333'; ?>; border-radius: 50%; display: inline-block; box-shadow: <?php echo $isActive ? '0 0 10px #00ff9d' : 'none'; ?>;"></span>
                </div>
            </div>

            <div class="timer-area">
                <div class="timer-display" id="timer-<?php echo $equipo['id']; ?>">
                    <?php echo $isActive ? '--:--:--' : '00:00:00'; ?>
                </div>
                <div class="status-label">SESIÓN DE PILOTO</div>
            </div>

            <div class="sim-footer">
                <?php if(!$isActive): ?>
                    <button type="button" class="btn-action-main btn-start" onclick="abrirAsignar(<?php echo $equipo['id']; ?>, <?php echo $isOffline ? 'true' : 'false'; ?>)">
                         ASIGNAR TIEMPO
                    </button>
                <?php else: ?>
                    <div class="btn-group-sm">
                        <button type="button" class="btn-action-main btn-add" onclick="abrirExtender(<?php echo $equipo['id']; ?>, '<?php echo addslashes($equipo['nombre']); ?>')">
                            EXTENDER
                        </button>
                        <button type="button" class="btn-action-main btn-stop" onclick="finalizarSesion(<?php echo $equipo['id']; ?>)">
                            <i class="fas fa-power-off"></i>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    </div>
</div>

<!-- MODAL ASIGNAR -->
<div class="modal fade" id="modalAsignar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 bg-dark" style="border-radius: 15px;">
            <div class="modal-header border-bottom border-secondary">
                <h6 class="modal-title font-monospace fw-bold text-white">> ASIGNAR TIEMPO A SIMULADOR</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="eq_id">
                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6">
                        <label class="small text-muted mb-1 d-block">WhatsApp / Teléfono</label>
                        <input type="tel" class="form-control bg-black border-secondary text-white py-2" id="c_tel" onkeyup="checkCli('tel')" placeholder="9...">
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="small text-muted mb-1 d-block">RUT_DATA</label>
                        <input type="text" class="form-control bg-black border-secondary text-white py-2" id="c_rut" onkeyup="checkCli('rut')" placeholder="12.345.678-9">
                    </div>
                </div>
                <div id="c_info" class="p-3 mb-4 rounded" style="display:none; background: rgba(0,255,157,0.05); border: 1px solid rgba(0,255,157,0.2);">
                    <span class="small text-success">PILOTO DETECTADO:</span>
                    <div class="fw-bold text-white h5 mt-1" id="c_nom_display">---</div>
                </div>

                <div id="new_cli_area" class="mb-4">
                    <label class="small text-muted mb-1 d-block">NOMBRE_COMPLETO</label>
                    <input type="text" class="form-control bg-black border-secondary text-white py-2" id="c_nom" placeholder="NOMBRE DEL PILOTO">
                </div>

                <div class="mb-4">
                    <label class="small text-muted mb-2 d-block">MODO_DE_OPERACIÃ“N</label>
                    <div class="d-flex flex-column gap-2">
                        <label class="form-check d-flex align-items-center gap-2 text-white-50">
                            <input class="form-check-input" type="radio" name="modo_op" id="modo_controlado" value="controlado" checked>
                            <span>CONTROLADO (requiere agente online)</span>
                        </label>
                        <label class="form-check d-flex align-items-center gap-2 text-white-50">
                            <input class="form-check-input" type="radio" name="modo_op" id="modo_manual" value="manual">
                            <span>MANUAL (sin bloqueo)</span>
                        </label>
                        <div id="modo_warn" class="small text-warning" style="display:none;">AGENTE OFFLINE: solo disponible modo manual.</div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="small text-muted mb-2 d-block">TIEMPO_PREDEFINIDO</label>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-outline-secondary py-2 flex-grow-1" onclick="setT(15, 1500)">15 MIN</button>
                        <button type="button" class="btn btn-outline-secondary py-2 flex-grow-1" onclick="setT(30, 2500)">30 MIN</button>
                        <button type="button" class="btn btn-outline-secondary py-2 flex-grow-1" onclick="setT(60, 4500)">60 MIN</button>
                    </div>
                </div>

                <div class="row g-2 mb-4">
                    <div class="col-6"><input type="number" id="t_min" class="form-control bg-black border-secondary text-white text-center" placeholder="MINS"></div>
                    <div class="col-6"><input type="number" id="t_mon" class="form-control bg-black border-secondary text-danger text-center" placeholder="$$$"></div>
                </div>

                <button class="btn btn-danger w-100 py-3 fw-bold" onclick="iniciarGo()" style="border-radius: 10px; font-family: 'Orbitron'; font-size: 0.9rem;">DESPLEGAR SIMULADOR</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalExt" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content bg-dark border-0 p-4" style="border-radius: 15px;">
            <h6 id="ext_name" class="text-center mb-4 text-primary font-monospace fw-bold">> AÑADIR_TIEMPO_EXTRA</h6>
            <div class="mb-3">
                <input type="number" id="ext_t" class="form-control bg-black border-secondary text-white text-center py-2" placeholder="MINUTOS">
            </div>
            <div class="mb-4">
                <input type="number" id="ext_m" class="form-control bg-black border-secondary text-primary text-center py-2" placeholder="MONTO $$$">
            </div>
            <button class="btn btn-primary w-100 py-3 fw-bold" onclick="extenderGo()" style="border-radius: 10px;">AÑADIR_TIEMPO</button>
        </div>
    </div>
</div>

<script>
window.finalizarSesion = (id) => {
    Swal.fire({
        title: '¿Finalizar sesión?',
        text: "Se detendrá el cronómetro de esta unidad.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ff0000',
        cancelButtonColor: '#333',
        confirmButtonText: 'Sí, finalizar',
        background: '#111',
        color: '#fff'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`${BASE_URL}/api/simulador/finalizar`, { 
                method: 'POST', 
                headers: {'Content-Type': 'application/json'}, 
                body: JSON.stringify({equipo_id: id}) 
            })
            .then(r => r.json())
            .then(res => { if(res.success) location.reload(); });
        }
    });
};

window.alertNoConn = () => {
    Swal.fire({
        title: 'FALLO_DE_ENLACE',
        text: 'La unidad no detecta señal de red activa.',
        icon: 'error', background: '#0a0a0a', color: '#fff', confirmButtonColor: '#ff0000'
    });
};

window.abrirAsignar = (id, isOffline) => {
    window.curOffline = !!isOffline;
    document.getElementById('eq_id').value = id;
    document.getElementById('c_info').style.display = 'none';
    const warn = document.getElementById('modo_warn');
    const modoControlado = document.getElementById('modo_controlado');
    const modoManual = document.getElementById('modo_manual');
    if (window.curOffline) {
        warn.style.display = 'block';
        modoControlado.checked = false;
        modoControlado.disabled = true;
        modoManual.checked = true;
    } else {
        warn.style.display = 'none';
        modoControlado.disabled = false;
        modoControlado.checked = true;
        modoManual.checked = false;
    }
    const mEl = document.getElementById('modalAsignar');
    const m = bootstrap.Modal.getOrCreateInstance(mEl);
    m.show();
};

window.setT = (m, p) => {
    document.getElementById('t_min').value = m;
    document.getElementById('t_mon').value = p;
};

window.iniciarGo = () => {
    const modo = document.querySelector('input[name=\"modo_op\"]:checked')?.value || 'controlado';
    if (window.curOffline && modo === 'controlado') {
        Swal.fire({
            title: 'AGENTE OFFLINE',
            text: 'No se puede iniciar en modo controlado.',
            icon: 'error',
            background: '#0a0a0a',
            color: '#fff',
            confirmButtonColor: '#ff0000'
        });
        return;
    }
    const d = {
        equipo_id: document.getElementById('eq_id').value,
        tiempo: document.getElementById('t_min').value,
        monto: document.getElementById('t_mon').value,
        metodo: 'efectivo',
        telefono: document.getElementById('c_tel').value,
        rut: document.getElementById('c_rut').value,
        nombre_cliente: document.getElementById('c_nom').value,
        modo: modo
    };
    fetch(`${BASE_URL}/api/simulador/iniciar`, { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(d) })
    .then(r => r.json()).then(res => {
        if(res.success) {
            location.reload();
        } else {
            let msg = res.error || 'No se pudo iniciar la sesiÃ³n';
            if (msg === 'AGENTE_OFFLINE') {
                msg = 'Agente offline. Use modo manual o encienda el agente.';
            }
            Swal.fire({ icon: 'error', title: 'ERROR', text: msg, background: '#0a0a0a', color: '#fff', confirmButtonColor: '#ff0000' });
        }
    });
};

window.abrirExtender = (id, n) => {
    window.curId = id;
    document.getElementById('ext_name').innerText = n.toUpperCase();
    const mEl = document.getElementById('modalExt');
    const m = bootstrap.Modal.getOrCreateInstance(mEl);
    m.show();
};

window.extenderGo = () => {
    const d = {
        equipo_id: window.curId,
        tiempo: document.getElementById('ext_t').value,
        monto: document.getElementById('ext_m').value,
        metodo: 'efectivo'
    };
    fetch(`${BASE_URL}/api/simulador/extender`, { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(d) })
    .then(r => r.json()).then(res => { if(res.success) location.reload(); });
};

function checkCli(o) {
    const t = document.getElementById('c_tel').value;
    const r = document.getElementById('c_rut').value;
    if(t.length < 8 && r.length < 8) return;
    
    fetch(`${BASE_URL}/api/cliente/buscar?telefono=${t}&rut=${r}`)
    .then(res => res.json()).then(data => {
        if(data.found) {
            document.getElementById('c_info').style.display = 'block';
            document.getElementById('c_nom_display').innerText = data.cliente.nombre;
            document.getElementById('c_nom').value = data.cliente.nombre;
        } else {
            document.getElementById('c_info').style.display = 'none';
        }
    });
}

function updateClocks() {
    document.querySelectorAll('.sim-card.status-active').forEach(card => {
        const id = card.id.replace('card-', '');
        let sec = parseInt(card.dataset.seconds);
        if(sec > 0) {
            sec--;
            card.dataset.seconds = sec;
            const h = Math.floor(sec/3600).toString().padStart(2, '0');
            const m = Math.floor((sec%3600)/60).toString().padStart(2, '0');
            const s = (sec%60).toString().padStart(2, '0');
            const d = document.getElementById('timer-'+id);
            if(d) d.innerText = `${h}:${m}:${s}`;
        }
    });
}

async function monitorSync() {
    try {
        if(document.querySelector('.modal.show')) return;
        const res = await fetch(`${BASE_URL}/api/monitor/sync`);
        const data = await res.json();
        if(data.success) {
            data.equipos.forEach(eq => {
                const card = document.getElementById('card-' + eq.id);
                if(!card) return;
                const timer = document.getElementById('timer-' + eq.id);
                const isActive = (eq.estado === 'en_uso');
                if(isActive) {
                    card.classList.add('status-active');
                    card.dataset.seconds = eq.tiempo_restante;
                } else {
                    card.classList.remove('status-active');
                    card.dataset.seconds = 0;
                    if(timer) timer.innerText = "00:00:00";
                }
            });
        }
    } catch (e) {}
}

document.addEventListener('DOMContentLoaded', () => {
    setInterval(updateClocks, 1000);
    setInterval(monitorSync, 10000);
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
