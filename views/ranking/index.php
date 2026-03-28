<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<style>
/* 1. ESTILO RANKING REGISTRATION */
.ranking-reg-container {
    max-width: 900px;
    margin: 40px auto;
    background: #0a0a0a;
    border: 1px solid #222;
    padding: 2rem;
    position: relative;
    overflow: hidden;
    clip-path: polygon(0 0, 100% 0, 100% 95%, 95% 100%, 0 100%);
}

.ranking-reg-container::after {
    content: '';
    position: absolute;
    top: 0; right: 0;
    width: 60px; height: 2px;
    background: #ff0000;
}

.input-rank {
    background: #000 !important;
    border: 1px solid #333 !important;
    color: #fff !important;
    font-family: 'JetBrains Mono', monospace;
    font-size: 1.2rem;
    padding: 15px;
    border-radius: 0;
}
.input-rank:focus {
    border-color: #ff0000 !important;
    box-shadow: 0 0 10px rgba(255,0,0,0.1) !important;
}

.label-rank {
    font-family: 'Orbitron', sans-serif;
    font-size: 0.7rem;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-bottom: 8px;
    display: block;
}

.btn-save-rank {
    background: #ff0000;
    color: #fff;
    border: none;
    font-family: 'Orbitron', sans-serif;
    font-weight: 900;
    font-size: 1.2rem;
    padding: 20px;
    width: 100%;
    margin-top: 20px;
    transition: 0.3s;
    text-transform: uppercase;
    letter-spacing: 2px;
}
.btn-save-rank:hover {
    background: #fff;
    color: #000;
}

.client-found-box {
    background: rgba(0, 255, 157, 0.05);
    border: 1px solid #00ff9d;
    padding: 15px;
    margin-bottom: 20px;
    display: none;
}

.time-input-group {
    display: flex;
    gap: 10px;
    align-items: center;
}
.time-part {
    text-align: center;
    font-weight: 900;
    font-size: 1.5rem;
}

@media (max-width: 768px) {
    .monitor-container {
        padding: 5px !important;
    }
    .ranking-reg-container {
        margin: 10px auto; 
        padding: 1.5rem 1rem;
        clip-path: none;
        border: 1px solid #222;
        border-top: 3px solid #ff0000;
        border-radius: 12px;
    }
    .ranking-reg-container::after { display: none; }
    
    .input-rank { 
        font-size: 1rem; 
        padding: 10px 12px; 
    }
    
    #r_tiempo { font-size: 1.3rem !important; }
    .label-rank { font-size: 0.65rem; margin-bottom: 5px; }
    
    h2.font-monospace { 
        font-size: 0.9rem !important; 
        letter-spacing: 0 !important;
        text-align: center; 
        margin-bottom: 15px !important;
        width: 100%;
        word-break: break-all; /* Quiebra el texto con guiones bajos si es necesario */
    }
    
    .btn-save-rank { 
        font-size: 0.85rem; 
        letter-spacing: 0 !important;
        white-space: normal; /* Permite dos líneas si falta espacio */
        word-wrap: break-word;
        padding: 15px; 
        margin-top: 15px; 
        border-radius: 8px;
    }
}

/* Eliminado bloque de Banner Master redundante */
/* Sugerencias Autocompletar */
.suggest-box {
    position: absolute;
    background: #111;
    border: 1px solid #ff0000;
    width: 100%;
    z-index: 999;
    display: none;
    max-height: 200px;
    overflow-y: auto;
    box-shadow: 0 5px 15px rgba(0,0,0,0.5);
}
.suggest-item {
    padding: 10px 15px;
    cursor: pointer;
    border-bottom: 1px solid #222;
    transition: 0.2s;
}
.suggest-item:hover {
    background: #ff0000;
    color: #fff;
}
.suggest-item b {
    color: #ff0000;
}
.suggest-item:hover b {
    color: #fff;
}
.suggest-item small {
    display: block;
    font-size: 0.7rem;
    opacity: 0.7;
}
</style>

<!-- Banner Master Removido para usar Header Global -->

<div class="monitor-container p-4">
    <div class="ranking-reg-container">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h2 class="font-monospace fw-bold m-0" style="color: #ff0000; letter-spacing: 2px;">
                > REGISTRO_DE_TIEMPOS_PILOTO
            </h2>
            <a href="ranking/display" target="_blank" class="btn btn-outline-danger btn-sm font-monospace" style="letter-spacing:1px; white-space:nowrap;">
                <i class="fas fa-tv me-2"></i> ABRIR_VISOR_TV
            </a>
        </div>

        <form id="rankForm">
            <div class="row g-4">
                <!-- Búsqueda / Datos Piloto -->
                <div class="col-md-6">
                    <div class="mb-3 position-relative">
                        <label class="label-rank">IDENTIFICACIÓN_RUT</label>
                        <input type="text" class="form-control input-rank" id="r_rut" placeholder="12.345.678-9" autocomplete="off" oninput="formatRut(this); buscarSemejantes(this, 'rut')">
                        <div id="suggest-rut" class="suggest-box"></div>
                    </div>
                    <div class="mb-3">
                        <label class="label-rank">NOMBRE_COMPLETO_PILOTO</label>
                        <input type="text" class="form-control input-rank" id="r_nom" placeholder="ESCRIBA EL NOMBRE AQUÍ" required>
                    </div>
                    <div id="box-reconocido" class="client-found-box">
                        <span class="label-rank" style="color: #00ff9d">PILOTO_DETECTADO_EN_NUCLEO:</span>
                        <div id="nom-display" class="fw-bold text-white fs-4" style="font-family:'Orbitron'">---</div>
                        <small class="text-muted">DATOS DESBLOQUEADOS PARA AUTO-RELLENO</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="row">
                        <div class="col-12 mb-3 position-relative">
                            <label class="label-rank">CORREO_ELECTRÓNICO</label>
                            <input type="email" class="form-control input-rank" id="r_email" placeholder="piloto@ejemplo.com" autocomplete="off" oninput="buscarSemejantes(this, 'email')">
                            <div id="suggest-email" class="suggest-box"></div>
                        </div>
                        <div class="col-12 mb-3 position-relative">
                            <label class="label-rank">TELÉFONO_CONTACTO</label>
                            <input type="tel" class="form-control input-rank" id="r_tel" placeholder="9..." autocomplete="off" oninput="formatTel(this); buscarSemejantes(this, 'tel')">
                            <div id="suggest-tel" class="suggest-box"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="label-rank">FOTO_PERFIL_PILOTO</label>
                        <div class="d-flex align-items-center gap-3">
                            <div id="preview-box" style="width:70px; height:70px; background:#111; border:1px dashed #444; display:flex; align-items:center; justify-content:center; border-radius: 8px;">
                                <i class="fas fa-camera text-muted"></i>
                            </div>
                            <input type="file" class="form-control input-rank" id="r_foto" accept="image/*" capture="user" style="font-size:0.8rem;">
                        </div>
                    </div>
                </div>

                <hr style="border-color: #222;">

                <div class="col-md-6">
                    <label class="label-rank">PISTA / CIRCUITO</label>
                    <select class="form-control input-rank text-uppercase" id="r_pista" style="font-size: 0.9rem;">
                        <?php foreach($pistas as $p): ?>
                            <option value="<?php echo $p['nombre']; ?>"><?php echo strtoupper($p['nombre']); ?></option>
                        <?php endforeach; ?>
                        <?php if(empty($pistas)): ?>
                            <option value="">[ SIN_PISTAS_REGISTRADAS ]</option>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="label-rank">TIEMPO_RECORD (MM:SS.CCC)</label>
                    <div class="time-input-group">
                        <input type="text" class="form-control input-rank text-center" id="r_tiempo" placeholder="01:24.567" style="font-size: 2rem;">
                    </div>
                    <small class="text-muted font-monospace" style="font-size: 0.6rem;">USE FORMATO MINUTOS:SEGUNDOS.MILISEGUNDOS</small>
                </div>
            </div>

            <button type="submit" class="btn-save-rank" id="btnSave">
                <i class="fas fa-trophy me-3"></i> REGISTRAR_EN_RANKING
            </button>
        </form>
    </div>
</div>

<script>
// Previsualización de Foto
document.getElementById('r_foto').onchange = (e) => {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (re) => {
            document.getElementById('preview-box').innerHTML = `<img src="${re.target.result}" style="width:100%; height:100%; object-fit:cover;">`;
        };
        reader.readAsDataURL(file);
    }
};

// MÁSCARA INTELIGENTE PARA TIEMPO (MM:SS.CCC)
document.getElementById('r_tiempo').oninput = (e) => {
    let v = e.target.value.replace(/\D/g, ''); // Solo números
    if (v.length > 7) v = v.substring(0, 7); // Max 7 dígitos
    
    let res = "";
    if (v.length > 0) {
        // Minutos
        res += v.substring(0, 2);
        if (v.length > 2) {
            res += ":" + v.substring(2, 4);
            if (v.length > 4) {
                res += "." + v.substring(4, 7);
            }
        }
    }
    e.target.value = res;
};

// MÁSCARA Y VALIDACIÓN DE RUT CHILENO
function formatRut(el) {
    // Solo permitimos números y K
    let v = el.value.replace(/[^0-9kK]/g, '');
    if (v.length > 9) v = v.slice(0, 9);
    
    // Solo ponemos el guion si ya tiene al menos 8 caracteres (RUTs de 8 o 9 dígitos)
    if (v.length >= 8) {
        let dv = v.slice(-1).toUpperCase();
        let body = v.slice(0, -1);
        el.value = body + '-' + dv;
    } else {
        el.value = v;
    }
}

// MÁSCARA TELÉFONO (+56 9 inicial, solo números)
const telInput = document.getElementById('r_tel');
if (!telInput.value) telInput.value = "+56 9 ";

function formatTel(el) {
    let v = el.value;
    let cleaned = v.replace(/[^\d+]/g, '');
    if (cleaned.startsWith("+569") && cleaned.length > 4) {
        el.value = cleaned.slice(0, 4) + " " + cleaned.slice(4);
    } else {
        el.value = cleaned;
    }
}

// Función de validación de RUT (Algoritmo Módulo 11)
function validarRut(rut) {
    if (!rut || rut.length < 8) return false;
    let clean = rut.replace(/[^0-9kK]/g, '');
    if (clean.length < 8) return false;
    
    let body = clean.slice(0, -1);
    let dv = clean.slice(-1).toUpperCase();
    
    let sum = 0;
    let mul = 2;
    for (let i = body.length - 1; i >= 0; i--) {
        sum += body[i] * mul;
        mul = (mul === 7) ? 2 : mul + 1;
    }
    
    let res = 11 - (sum % 11);
    let expectedDv = (res === 11) ? '0' : (res === 10) ? 'K' : res.toString();
    return dv === expectedDv;
}

function buscarSemejantes(el, tipo) {
    const term = el.value.trim();
    const boxId = `suggest-${tipo}`;
    const box = document.getElementById(boxId);

    if (term.length < 1) {
        box.style.display = 'none';
        return;
    }

    console.log(`Buscando ${tipo}: ${term}`);

    fetch(`${BASE_URL}/api/cliente/sugerir?term=${encodeURIComponent(term)}`)
    .then(r => r.json())
    .then(data => {
        console.log("Respuesta BD:", data);
        if (data && data.length > 0) {
            box.innerHTML = data.map(cli => `
                <div class="suggest-item" onclick='seleccionarPiloto(${JSON.stringify(cli)})'>
                    <strong>${cli.nombre.toUpperCase()}</strong>
                    <small>RUT: ${cli.rut || 'S/RUT'} | TEL: ${cli.telefono || 'S/TEL'}</small>
                </div>
            `).join('');
            box.style.display = 'block';
        } else {
            box.style.display = 'none';
        }
    })
    .catch(err => console.error("Error Autocomplete:", err));
}

function seleccionarPiloto(cli) {
    document.getElementById('r_nom').value = cli.nombre;
    document.getElementById('r_rut').value = cli.rut || '';
    document.getElementById('r_tel').value = cli.telefono || '';
    document.getElementById('r_email').value = cli.email || '';
    
    document.getElementById('box-reconocido').style.display = 'block';
    document.getElementById('nom-display').innerText = cli.nombre;
    
    // Ocultar todas las cajas de sugerencias
    document.querySelectorAll('.suggest-box').forEach(b => b.style.display = 'none');
}

// Cerrar sugerencias al hacer clic fuera
document.addEventListener('click', (e) => {
    if (!e.target.closest('.position-relative')) {
        document.querySelectorAll('.suggest-box').forEach(b => b.style.display = 'none');
    }
});

document.getElementById('rankForm').onsubmit = (e) => {
    e.preventDefault();
    const btn = document.getElementById('btnSave');
    const rut = document.getElementById('r_rut').value.trim();
    const tel = document.getElementById('r_tel').value.trim();
    const email = document.getElementById('r_email').value.trim();
    const tiempoVal = document.getElementById('r_tiempo').value.trim();

    // VALIDACIÓN SENIOR EXIGIDA: Al menos RUT, TEL o EMAIL
    if (!rut && !tel && !email) {
        Swal.fire({
            title: 'IDENTIFICACIÓN_REQUERIDA',
            text: 'Debes ingresar al menos un dato del piloto (RUT, Teléfono o Correo) para registrarlo.',
            icon: 'error'
        });
        return;
    }

    // VALIDACIÓN ESTRICTA DE RUT SI SE INGRESA
    if (rut && !validarRut(rut)) {
        Swal.fire({
            title: 'RUT INVÁLIDO',
            text: 'El RUT ingresado no es válido. Por favor verifícalo (Ej: 12345678-9).',
            icon: 'warning'
        });
        return;
    }

    if (!tiempoVal || tiempoVal.length < 5) { 
        Swal.fire({
            title: 'TIEMPO REQUERIDO',
            text: 'Debes ingresar un tiempo válido para registrar al piloto.',
            icon: 'warning', background: '#0a0a0a', color: '#fff', confirmButtonColor: '#ff0000'
        });
        return;
    }

    btn.disabled = true;
    btn.innerText = "PROCESANDO_DATOS...";

    const formData = new FormData();
    formData.append('telefono', document.getElementById('r_tel').value);
    formData.append('rut', document.getElementById('r_rut').value);
    formData.append('email', document.getElementById('r_email').value);
    formData.append('nombre', document.getElementById('r_nom').value);
    formData.append('pista', document.getElementById('r_pista').value);
    formData.append('tiempo', document.getElementById('r_tiempo').value);
    
    const fileInput = document.getElementById('r_foto');
    if (fileInput.files[0]) {
        formData.append('foto', fileInput.files[0]);
    }

    fetch(`${BASE_URL}/api/ranking/save`, {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            // ANIMACIÓN ÉPICA - HUD TELEMETRÍA (EN ESPAÑOL)
            document.body.insertAdjacentHTML('beforeend', `
                <div id="epic-overlay" style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.95); z-index:9999; display:flex; flex-direction:column; align-items:center; justify-content:center; font-family:'Orbitron'; color:#ff0000; overflow:hidden;">
                    <div style="font-size:10rem; opacity:0.1; position:absolute;">A4R</div>
                    <div id="scan-line" style="width:100%; height:2px; background:#ff0000; box-shadow:0 0 20px #ff0000; position:absolute; top:0; animation:scan 1.5s linear infinite;"></div>
                    <div style="border:2px solid #ff0000; padding:40px; text-align:center; position:relative; background:rgba(225,6,0,0.05); clip-path:polygon(10% 0, 100% 0, 90% 100%, 0 100%);">
                        <div style="font-size:1rem; letter-spacing:10px; margin-bottom:10px;">CARGA_DE_DATOS_EXITOSA</div>
                        <div style="font-size:4rem; font-weight:900; color:#fff; text-shadow:0 0 20px #ff0000;">RÉCORD_GUARDADO</div>
                        <div style="font-size:1.5rem; color:#00ff9d; margin-top:20px;">[ SINCRONIZACIÓN_CON_LEADERBOARD_COMPLETA ]</div>
                    </div>
                    <style>
                        @keyframes scan { 0% { top:-10%; } 100% { top:110%; } }
                    </style>
                </div>
            `);
            
            setTimeout(() => {
                location.reload();
            }, 2500);
        } else {
            Swal.fire({
                title: 'ERROR_SISTEMA',
                text: res.error || 'Fallo en la sincronización.',
                icon: 'error', background: '#0a0a0a', color: '#fff', confirmButtonColor: '#ff0000'
            });
            btn.disabled = false;
            btn.innerText = "REGISTRAR_EN_RANKING";
        }
    })
    .catch(err => {
        console.error(err);
        btn.disabled = false;
        btn.innerText = "REGISTRAR_EN_RANKING";
    });
};
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
