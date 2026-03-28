<?php 
$appUrl = \App\Config\Config::getAppUrl();
$endsWith = function($haystack, $needle) {
    if ($needle === '') return true;
    return substr($haystack, -strlen($needle)) === $needle;
};
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$script = $_SERVER['SCRIPT_NAME'] ?? '';
$basePath = '';
if ($script) {
    if ($endsWith($script, '/public/index.php')) {
        $basePath = substr($script, 0, -strlen('/public/index.php'));
    } elseif ($endsWith($script, '/index.php')) {
        $basePath = substr($script, 0, -strlen('/index.php'));
    } else {
        $basePath = rtrim(dirname($script), '/');
    }
}
$requestBase = rtrim($scheme . '://' . $host . $basePath, '/');
include __DIR__ . '/../layouts/header.php'; 
?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<style>
/* CONSOLA ADS MASTER - PREMIUM HUD */
.config-container {
    background: #050505;
    min-height: calc(100vh - 70px);
    padding: 30px;
    color: #e0e0e0;
}

.cyber-panel {
    background: rgba(15, 15, 15, 0.8);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    height: 100%;
}

/* Tabs Estilo Neon */
.cyber-tabs {
    border-bottom: 1px solid rgba(255,255,255,0.1);
    gap: 10px;
}
.cyber-tabs .nav-link {
    border: none;
    background: transparent;
    color: #666;
    font-family: 'Orbitron', sans-serif;
    font-size: 0.75rem;
    letter-spacing: 2px;
    padding: 12px 25px;
    transition: 0.4s;
    border-radius: 8px 8px 0 0;
    position: relative;
}
.cyber-tabs .nav-link:hover { color: #aaa; }
.cyber-tabs .nav-link.active {
    color: #ff0000;
    background: rgba(255,0,0,0.05);
}
.cyber-tabs .nav-link.active::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0; width: 100%; height: 2px;
    background: #ff0000;
    box-shadow: 0 0 10px #ff0000;
}

/* Card de Anuncio v3 */
.ad-card-v3 {
    background: #111;
    border: 1px solid #222;
    border-radius: 12px;
    overflow: hidden;
    transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.ad-card-v3:hover {
    border-color: #ff0000;
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(255,0,0,0.1);
}

.ad-thumb-container {
    height: 160px;
    position: relative;
    overflow: hidden;
}
.ad-thumb {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.ad-overlay {
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    background: linear-gradient(0deg, rgba(0,0,0,0.8) 0%, transparent 100%);
}

/* Inputs y Controles */
.cyber-input {
    background: #000 !important;
    border: 1px solid #333 !important;
    color: #00ff9d !important;
    font-family: 'JetBrains Mono', monospace;
    font-weight: 700;
}
.cyber-input:focus {
    border-color: #ff0000 !important;
    box-shadow: 0 0 10px rgba(255,0,0,0.2) !important;
}

.duration-badge {
    position: absolute;
    top: 10px; right: 10px;
    background: rgba(0,0,0,0.8);
    border: 1px solid #ff0000;
    color: #fff;
    padding: 3px 10px;
    border-radius: 5px;
    font-family: 'Orbitron';
    font-size: 0.65rem;
}

/* Botones */
.btn-cyber-red {
    background: #ff0000;
    border: none;
    color: #fff;
    font-family: 'Orbitron';
    font-weight: 700;
    letter-spacing: 1px;
    transition: 0.3s;
    text-transform: uppercase;
}
.btn-cyber-red:hover {
    background: #cc0000;
    box-shadow: 0 0 20px rgba(255,0,0,0.4);
    transform: scale(1.02);
}

.pagination-cyber .page-link {
    background: #0a0a0a;
    border-color: #222;
    color: #999;
}
.pagination-cyber .page-item.active .page-link {
    background: #ff0000;
    border-color: #ff0000;
    color: #fff;
}

/* 📱 ADAPTACIÓN RESPONSIVA CELULARES */
@media (max-width: 991px) {
    .app-wrapper .app-main { margin-left: 0 !important; }
}

@media (max-width: 768px) {
    .config-container {
        padding: 10px !important;
    }
    
    /* Expande el botón principal a todo el ancho y centra su contenido */
    .d-flex.justify-content-end.mb-4 {
        justify-content: center !important;
        width: 100%;
    }
    .d-flex.justify-content-end.mb-4 button {
        width: 100%;
        padding: 15px;
    }

    /* Transformación de los Tabs tipo Neón a Botones Apilados */
    .cyber-tabs {
        border-bottom: none !important;
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 8px !important;
    }
    
    .cyber-tabs .nav-link {
        width: 100%;
        border-radius: 8px !important;
        border: 1px solid rgba(255, 255, 255, 0.05);
        background: rgba(20, 20, 20, 0.8);
        padding: 15px;
        text-align: left; /* Alineado a la izquierda para parecer un menú real */
        display: flex;
        align-items: center;
    }
    
    .cyber-tabs .nav-link i {
        margin-right: 15px !important;
        font-size: 1.2rem;
    }

    /* Quita la raya roja de abajo y se la pone al borde izquierdo (como menú) */
    .cyber-tabs .nav-link.active::after {
        display: none;
    }
    .cyber-tabs .nav-link.active {
        border-left: 4px solid #ff0000 !important;
        border-top: 1px solid rgba(255,0,0,0.2) !important;
        border-bottom: 1px solid rgba(255,0,0,0.2) !important;
        border-right: 1px solid rgba(255,0,0,0.2) !important;
        box-shadow: inset 0 0 15px rgba(255,0,0,0.05);    
        padding-left: calc(15px - 4px); /* compemsar por el borde izquierdo grueso */
    }
}
</style>

<div class="config-container">
    <div class="container-fluid">
        <!-- HEADER DE SECCIÓN - Limpiado por redundancia -->
        <div class="mb-4 d-flex justify-content-end">
            <button class="btn btn-cyber-red px-4" data-bs-toggle="modal" data-bs-target="#modalAd">
                <i class="fas fa-plus-circle me-2"></i> NUEVO SLIDE
            </button>
        </div>

        <!-- TABS CORPORATIVOS -->
        <ul class="nav nav-tabs cyber-tabs mb-4 border-0" id="configTabs">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#active-slides" onclick="loadAds(1, 1)">
                    <i class="fas fa-play me-2"></i> REPRODUCCIÓN
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#inactive-slides" onclick="loadAds(0, 1)">
                    <i class="fas fa-archive me-2"></i> ARCHIVO
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#master-config">
                    <i class="fas fa-atom me-2"></i> AJUSTES
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#identity-config">
                    <i class="fas fa-id-card me-2"></i> IDENTIDAD
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#ticker-config">
                    <i class="fas fa-bullhorn me-2"></i> TICKER
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#software-config">
                    <i class="fas fa-laptop-code me-2"></i> SOFTWARE
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <!-- PESTAÑA ACTIVOS -->
            <div class="tab-pane fade show active" id="active-slides">
                <div class="row g-4" id="ads-active-list">
                    <!-- Dinámico -->
                </div>
                <div id="pagination-active" class="mt-5 d-flex justify-content-center"></div>
            </div>

            <!-- PESTAÑA INACTIVOS -->
            <div class="tab-pane fade" id="inactive-slides">
                <div class="row g-4" id="ads-inactive-list">
                    <!-- Dinámico -->
                </div>
                <div id="pagination-inactive" class="mt-5 d-flex justify-content-center"></div>
            </div>

            <!-- PESTAÑA CONFIGURACIÓN -->
            <div class="tab-pane fade" id="master-config">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="cyber-panel">
                            <h5 class="text-warning font-monospace mb-4"><i class="fas fa-clock me-2"></i> Tiempos del Motor</h5>
                            <form id="settingsForm">
                                <div class="mb-4">
                                    <label class="form-label font-monospace text-muted small text-uppercase">Duración Tabla Ranking (segundos)</label>
                                    <input type="number" id="ranking_duration" class="form-control cyber-input" value="<?php echo $ranking_duration ?? 15; ?>">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label font-monospace text-danger small text-uppercase fw-bold"><i class="fas fa-flag-checkered me-2"></i>PISTA ACTIVA EN PANTALLA</label>
                                    <select id="active_track" class="form-select cyber-input text-white border-danger">
                                        <option value="">MOSTRAR TODAS LAS PISTAS (GLOBAL)</option>
                                        <?php if(isset($pistas) && is_array($pistas)): foreach($pistas as $p): ?>
                                            <option value="<?php echo htmlspecialchars($p['nombre']); ?>" <?php echo (strcasecmp($active_track ?? '', $p['nombre']) === 0) ? 'selected' : ''; ?>>
                                                RACE ONLY: <?php echo htmlspecialchars(strtoupper($p['nombre'])); ?>
                                            </option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                    <small class="text-muted d-block mt-1" style="font-size: 0.6rem;">Si eliges "GLOBAL" competirán los mejores tiempos históricos de todas las pistas mezclados.</small>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label font-monospace text-muted small text-uppercase">Refresco de Datos RealTime (segundos)</label>
                                    <input type="number" id="data_refresh" class="form-control cyber-input" value="<?php echo isset($data_refresh) ? $data_refresh : 10; ?>">
                                </div>
                                <button type="submit" class="btn btn-warning w-100 fw-bold font-monospace text-dark">GUARDAR CONFIGURACIÓN</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="cyber-panel">
                            <h5 class="text-info font-monospace mb-4"><i class="fas fa-font me-2"></i> Textos del Visor</h5>
                            <form id="labelsForm">
                                <div class="mb-3">
                                    <label class="form-label font-monospace text-muted small text-uppercase">Título principal izquierda</label>
                                    <input type="text" id="ranking_title_main" class="form-control cyber-input" value="<?php echo $labels['ranking_title_main'] ?? ''; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label font-monospace text-muted small text-uppercase">Subtítulo izquierda</label>
                                    <input type="text" id="ranking_title_sub" class="form-control cyber-input" value="<?php echo $labels['ranking_title_sub'] ?? ''; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label font-monospace text-muted small text-uppercase">Línea superior banner</label>
                                    <input type="text" id="banner_top_text" class="form-control cyber-input" value="<?php echo $labels['banner_top_text'] ?? ''; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label font-monospace text-muted small text-uppercase">Título banner en pódium</label>
                                    <input type="text" id="banner_podium_text" class="form-control cyber-input" value="<?php echo $labels['banner_podium_text'] ?? ''; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label font-monospace text-muted small text-uppercase">Título banner en publicidad</label>
                                    <input type="text" id="banner_ads_text" class="form-control cyber-input" value="<?php echo $labels['banner_ads_text'] ?? ''; ?>">
                                </div>
                                <button type="submit" class="btn btn-info w-100 fw-bold font-monospace text-dark">ACTUALIZAR TEXTOS</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="cyber-panel">
                            <h5 class="text-primary font-monospace mb-4"><i class="fas fa-desktop me-2"></i> FOndO atmósfera</h5>
                            <?php if($viewer_background): ?>
                                <div class="mb-4 position-relative group">
                                    <img src="<?php echo \App\Config\Config::getAppUrl().'/'.$viewer_background; ?>" class="img-fluid rounded-3 border border-primary shadow-lg" style="height: 120px; width: 100%; object-fit: cover; opacity: 0.8;">
                                    <button onclick="removeBg()" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2" title="Volver a fondo negro"><i class="fas fa-trash"></i></button>
                                </div>
                            <?php else: ?>
                                <div class="p-3 border border-secondary border-dashed border-2 rounded-3 text-center mb-4 opacity-50 bg-black">
                                    <span class="small font-monospace fw-bold">SIN FONDO (PANTALLA NEGRA)</span>
                                </div>
                            <?php endif; ?>
                            
                            <form id="bgForm">
                                <div class="mb-3">
                                    <label class="form-label font-monospace text-muted small d-flex justify-content-between text-uppercase">
                                        Opacidad atmósfera <span id="opacity-val" class="text-primary"><?php echo $background_opacity * 100; ?>%</span>
                                    </label>
                                    <input type="range" id="bg_opacity" class="form-range" min="0" max="1" step="0.01" value="<?php echo $background_opacity; ?>" oninput="document.getElementById('opacity-val').innerText = Math.round(this.value*100)+'%'">
                                </div>
                                <input type="file" id="bg_file" class="form-control cyber-input mb-2" accept="image/*">
                                <button type="submit" class="btn btn-outline-primary btn-sm w-100 font-monospace">GUARDAR ATMÓSFERA</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PESTAÑA IDENTIDAD -->
            <div class="tab-pane fade" id="identity-config">
                <div class="row g-4 justify-content-center">
                    <div class="col-md-5">
                        <div class="cyber-panel">
                            <h5 class="text-info font-monospace mb-4"><i class="fas fa-user-circle me-2"></i> Foto piloto por defecto</h5>
                            <p class="small text-muted mb-4 opacity-75">Esta imagen aparecerá en el visor cuando un piloto no haya subido su propia fotografía.</p>
                            
                            <div class="text-center mb-4 py-3 bg-black rounded-3">
                                <img src="<?php echo \App\Config\Config::getAppUrl().'/'.$default_pilot_image; ?>" class="rounded-circle border border-info shadow-lg p-1" style="width: 150px; height: 150px; object-fit: cover;">
                            </div>
                            <form id="defaultPilotForm">
                                <div class="mb-4">
                                    <label class="form-label font-monospace text-muted small text-uppercase">Seleccionar nuevo avatar</label>
                                    <input type="file" id="pilot_file" class="form-control cyber-input" accept="image/*" required>
                                </div>
                                <button type="submit" class="btn btn-info w-100 fw-bold font-monospace text-dark py-3">CAMBIAR FOTO DE PILOTO</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PESTAÑA TICKER (MARQUESINA) -->
            <div class="tab-pane fade" id="ticker-config">
                <div class="row g-4">
                    <div class="col-md-5">
                        <div class="cyber-panel">
                            <h5 class="text-warning font-monospace mb-4"><i class="fas fa-plus-circle me-2"></i> NUEVO MENSAJE</h5>
                            <form id="marqueeForm">
                                <div class="mb-4">
                                    <label class="form-label font-monospace text-muted small text-uppercase">Texto de marquesina (ofertas, avisos, etc.)</label>
                                    <textarea id="marq_texto" class="form-control cyber-input" rows="3" placeholder="Ej: ¡RECORDA PEDIR TU BEBIDA EN EL BAR! - PROMO 2X1 EN SIMULADORES..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-warning w-100 fw-bold font-monospace text-dark py-3">CREAR MENSAJE</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="cyber-panel">
                            <h5 class="text-primary font-monospace mb-4"><i class="fas fa-list me-2"></i> MENSAJES ACTIVOS</h5>
                            <div class="table-responsive">
                                <table class="table table-hud">
                                    <thead>
                                        <tr>
                                            <th>CONTENIDO</th>
                                            <th class="text-center">ESTADO</th>
                                            <th class="text-end">ACCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody id="marq_list">
                                        <!-- Cargado vía JS -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PESTAÑA SOFTWARE (DESCARGAS) -->
            <div class="tab-pane fade" id="software-config">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="cyber-panel h-100">
                            <h5 class="text-danger font-monospace mb-4"><i class="fas fa-download me-2"></i> PAQUETE_DE_INSTALACIÓN</h5>
                            <p class="text-muted small mb-4">Descargue el Agente A4R para bloqueo y emparejamiento. Este software debe ejecutarse en cada PC de simulador.</p>
                            
                            <div class="d-grid gap-3">
                                <a href="<?php echo $requestBase; ?>/public/downloads/Agente_A4R.exe" class="btn btn-outline-danger py-3 fw-bold font-monospace" download>
                                    <i class="fas fa-file-exe me-2"></i> DESCARGAR_AGENTE (.EXE)
                                </a>
                                <a href="<?php echo $requestBase; ?>/public/downloads/Agente_A4R_Installer.exe" class="btn btn-outline-warning py-3 fw-bold font-monospace" download>
                                    <i class="fas fa-box-open me-2"></i> DESCARGAR_INSTALADOR (SETUP)
                                </a>
                                <a href="<?php echo $requestBase; ?>/public/downloads/config.ini" class="btn btn-outline-info py-3 fw-bold font-monospace" download>
                                    <i class="fas fa-file-code me-2"></i> DESCARGAR_CONFIG (.INI)
                                </a>
                            </div>

                            <div class="mt-4 p-3 bg-black border border-secondary rounded">
                                <h6 class="text-white font-monospace small mb-2 text-uppercase">Valores Sugeridos (Config.ini):</h6>
                                <p class="text-muted small mb-2">Si no usas QR, puedes configurar manualmente:</p>
                                </code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    loadAds(1, 1);
    loadAds(0, 1);
    loadMarquee();
});

// --- FUNCIONES CONFIGURACIÓN (AJUSTES) ---
const settingsForm = document.getElementById('settingsForm');
if(settingsForm) {
    settingsForm.onsubmit = async (e) => {
        e.preventDefault();
        const fd = new FormData();
        fd.append('ranking_duration', document.getElementById('ranking_duration').value);
        fd.append('data_refresh', document.getElementById('data_refresh').value);
        fd.append('active_track', document.getElementById('active_track').value);
        
        try {
            const r = await fetch(`${BASE_URL}/api/ranking/config_save`, { method: 'POST', body: fd });
            const d = await r.json();
            if(d.success) {
                Swal.fire({ 
                    toast: true, position: 'top-end', icon: 'success', 
                    title: `Guardado: ${d.saved_track || 'Global'}`, 
                    showConfirmButton: false, timer: 2500 
                });
            } else {
                Swal.fire('ERROR', d.error || 'No se pudo guardar', 'error');
            }
        } catch(e) {
            Swal.fire('ERROR_CRÍTICO', 'Falla de enlace con el servidor', 'error');
        }
    };
}

// --- FUNCIONES TEXTOS (LABELS) ---
const labelsForm = document.getElementById('labelsForm');
if(labelsForm) {
    labelsForm.onsubmit = async (e) => {
        e.preventDefault();
        const fd = new FormData();
        fd.append('ranking_title_main', document.getElementById('ranking_title_main').value);
        fd.append('ranking_title_sub', document.getElementById('ranking_title_sub').value);
        fd.append('banner_top_text', document.getElementById('banner_top_text').value);
        fd.append('banner_podium_text', document.getElementById('banner_podium_text').value);
        fd.append('banner_ads_text', document.getElementById('banner_ads_text').value);
        
        try {
            const r = await fetch(`${BASE_URL}/api/ranking/config_save`, { method: 'POST', body: fd });
            const d = await r.json();
            if(d.success) {
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Textos actualizados', showConfirmButton: false, timer: 1500 });
            } else {
                Swal.fire('ERROR', d.error || 'No se pudo guardar', 'error');
            }
        } catch(e) {
            Swal.fire('ERROR_CRÍTICO', 'Falla de enlace con el servidor', 'error');
        }
    };
}

// --- FUNCIONES MARQUESINA (TICKER) ---
async function loadMarquee() {
    const list = document.getElementById('marq_list');
    if(!list) return;
    try {
        const r = await fetch(`${BASE_URL}/api/ranking/marquee/list`);
        const d = await r.json();
        list.innerHTML = '';
        if(d.success && d.data.length > 0) {
            d.data.forEach(m => {
                list.innerHTML += `
                    <tr>
                        <td class="small opacity-75">${m.texto}</td>
                        <td class="text-center">
                            <button onclick="toggleMarq(${m.id}, ${m.is_active ? 0 : 1})" class="btn btn-sm ${m.is_active ? 'btn-success' : 'btn-dark opacity-50'} shadow-sm" style="font-size:0.6rem;">
                                ${m.is_active ? 'ACTIVO' : 'PAUSADO'}
                            </button>
                        </td>
                        <td class="text-end">
                            <button onclick="delMarq(${m.id})" class="btn btn-outline-danger btn-sm border-0"><i class="fas fa-trash-alt"></i></button>
                        </td>
                    </tr>
                `;
            });
        } else if (d.error) {
            list.innerHTML = `<tr><td colspan="3" class="text-center py-4 text-danger small font-monospace">ERROR_DB: ${d.error}</td></tr>`;
        } else {
            list.innerHTML = '<tr><td colspan="3" class="text-center py-4 text-muted small font-monospace">< SIN_MENSAJES_REGISTRADOS ></td></tr>';
        }
    } catch(e) {
        list.innerHTML = '<tr><td colspan="3" class="text-center py-4 text-danger small font-monospace">ERROR_CONEXIÓN_API</td></tr>';
    }
}

const marqueeForm = document.getElementById('marqueeForm');
if(marqueeForm) {
    marqueeForm.onsubmit = async (e) => {
        e.preventDefault();
        const fd = new FormData();
        fd.append('texto', document.getElementById('marq_texto').value);
        try {
            const r = await fetch(`${BASE_URL}/api/ranking/marquee/save`, { method: 'POST', body: fd });
            const d = await r.json();
            if(d.success) {
                document.getElementById('marq_texto').value = '';
                loadMarquee();
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Mensaje transmitido', showConfirmButton: false, timer: 1500 });
            } else {
                Swal.fire('ERROR', d.error || 'No se pudo guardar', 'error');
            }
        } catch(e) {
            Swal.fire('ERROR_CRÍTICO', 'Falla de enlace con el servidor', 'error');
        }
    };
}

async function toggleMarq(id, status) {
    const fd = new FormData(); fd.append('id', id); fd.append('status', status);
    await fetch(`${BASE_URL}/api/ranking/marquee/toggle`, { method: 'POST', body: fd });
    loadMarquee();
}

async function delMarq(id) {
    const { isConfirmed } = await Swal.fire({ title: '¿ELIMINAR MENSAJE?', text: 'Se borrará de la marquesina.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ff0000' });
    if(isConfirmed) {
        const fd = new FormData(); fd.append('id', id);
        await fetch(`${BASE_URL}/api/ranking/marquee/delete`, { method: 'POST', body: fd });
        loadMarquee();
    }
}
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
