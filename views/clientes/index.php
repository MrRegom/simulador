<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="admin-container">
    
    <style>
    /* 📱 CARD LAYOUT CELULAR PARA CLIENTES */
    @media (max-width: 768px) {
        .admin-container { padding: 10px; }
        
        /* Ajuste Buscador Cyber Móvil */
        .cyber-card-header .row { flex-direction: column; gap: 10px; align-items: stretch !important; }
        .cyber-card-header .col-auto i { display: none; }
        .cyber-card-header input { text-align: center; font-size: 0.75rem !important; background: rgba(50,0,0,0.1) !important; padding: 10px !important; border-radius: 5px; }
        .cyber-card-header .badge { display: block; width: 100%; text-align: center; padding: 8px; }

        /* Magia: Tabla a Tarjetas */
        .table-hud thead { display: none; }
        .table-hud tbody tr {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
            background: #050505;
            border: 1px solid #222;
            border-left: 3px solid #ff0000;
            border-radius: 8px;
            padding: 5px;
        }
        .table-hud tbody td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 10px !important;
            border-bottom: 1px dotted #111;
            text-align: right !important;
            width: 100%;
        }
        .table-hud tbody td:last-child {
            border-bottom: none;
            justify-content: flex-end; /* La acción a la derecha */
        }
        .table-hud tbody td::before {
            content: attr(data-label);
            font-family: 'Orbitron', sans-serif;
            font-size: 0.65rem;
            color: #ff0000;
            font-weight: 900;
            text-transform: uppercase;
            text-align: left;
            margin-right: 15px;
            flex-shrink: 0;
            max-width: 40%;
            word-wrap: break-word;
        }
        .cli-data-box {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 4px;
        }
    }
    </style>

    <!-- Buscador Cyber -->
    <div class="cyber-card mb-4" style="border-left: 4px solid #ff0000 !important;">
        <div class="cyber-card-header bg-black py-3 px-3">
             <div class="row w-100 align-items-center m-0">
                <div class="col-auto">
                    <i class="fas fa-search text-danger me-2"></i>
                </div>
                <div class="col">
                    <input type="text" id="bus_cli" class="form-control bg-transparent border-0 text-white font-monospace p-0" placeholder="ESCRIBA PARA FILTRAR POR NOMBRE, RUT O TELÉFONO..." style="box-shadow: none; font-size: 0.9rem;">
                </div>
                <div class="col-auto">
                    <span class="badge bg-dark text-danger border border-danger font-monospace" style="font-size: 0.7rem;">
                        <?php echo count($clientes); ?>_PILOTOS_EN_NÚCLEO
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="cyber-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hud align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">RUT / IDENTIFICADOR</th>
                            <th>DATOS DE CONTACTO (PILOTO)</th>
                            <th class="text-center">HISTORIAL VISITAS</th>
                            <th class="pe-4 text-end">GESTOR_SISTEMA</th>
                        </tr>
                    </thead>
                    <tbody id="cli_rows">
                        <?php foreach($clientes as $cli): ?>
                        <tr class="cli_row">
                            <td class="ps-md-4" data-label="IDENTIFICADOR">
                                <div class="cli-data-box text-end">
                                    <span class="id-badge">ID_#<?php echo str_pad($cli['id'], 3, '0', STR_PAD_LEFT); ?></span>
                                    <div class="text-white mt-1 fw-bold font-monospace small"><?php echo strtoupper($cli['rut'] ?? 'S/RUT'); ?></div>
                                </div>
                            </td>
                            <td data-label="CONTACTO_PILOTO">
                                <div class="cli-data-box text-end">
                                    <div class="text-white fw-bold" style="font-family: 'Orbitron'; font-size: 0.85rem; letter-spacing: 1px;"><?php echo strtoupper($cli['nombre']); ?></div>
                                    <div class="d-flex flex-column flex-md-row gap-md-3 mt-1 align-items-end align-items-md-center opacity-75">
                                        <small class="text-muted" style="font-size: 0.7rem;"><i class="fas fa-phone me-1"></i> <?php echo $cli['telefono'] ?? 'S/TÉL'; ?></small>
                                        <small class="text-muted" style="font-size: 0.7rem;"><i class="fas fa-envelope me-1"></i> <?php echo $cli['email'] ?? 'S/EMAIL'; ?></small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center" data-label="SESIONES">
                                <div class="text-success fw-bold font-monospace" style="font-size: 0.8rem;"><?php echo str_pad($cli['visitas'], 2, '0', STR_PAD_LEFT); ?>_SESIONES</div>
                            </td>
                            <td class="pe-md-4 text-end" data-label="GESTOR">
                                <button class="btn btn-action btn-action-edit me-2" onclick="abrirEditarCliente(<?php echo htmlspecialchars(json_encode($cli), ENT_QUOTES, 'UTF-8'); ?>)" title="Editar Piloto">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <button class="btn btn-action btn-action-delete" onclick="eliminarCliente(<?php echo $cli['id']; ?>)" title="Eliminar Piloto">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



<!-- MODAL EDICION CLIENTE -->
<div class="modal fade" id="modalCliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border-0" style="border-radius: 12px;">
            <div class="modal-header border-bottom border-secondary">
                <h6 class="modal-title font-monospace fw-bold text-white">> EDITAR PILOTO</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formCliente">
                    <input type="hidden" id="cli_id">
                    <div class="mb-3">
                        <label class="small text-muted mb-1">Nombre</label>
                        <input type="text" id="cli_nombre" class="form-control bg-black border-secondary text-white" required>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted mb-1">Telefono</label>
                        <input type="text" id="cli_telefono" class="form-control bg-black border-secondary text-white">
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted mb-1">RUT</label>
                        <input type="text" id="cli_rut" class="form-control bg-black border-secondary text-white">
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted mb-1">Email</label>
                        <input type="email" id="cli_email" class="form-control bg-black border-secondary text-white">
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted mb-1">Foto Piloto</label>
                        <input type="file" id="cli_foto" class="form-control bg-black border-secondary text-white" accept="image/*">
                        <div class="d-flex gap-2 mt-2">
                            <button type="button" class="btn btn-outline-info w-100" onclick="subirFotoCliente()">SUBIR FOTO</button>
                            <button type="button" class="btn btn-outline-warning w-100" onclick="resetFotoCliente()">RESET FOTO</button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-danger w-100 py-2 fw-bold">GUARDAR CAMBIOS</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Filtrado rápido en JS para no saturar con FETCH (Básico para mantenedor local)
document.getElementById('bus_cli').addEventListener('input', (e) => {
    const term = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('.cli_row');
    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(term) ? '' : 'none';
    });
});

async function eliminarCliente(id) {
    const { isConfirmed } = await Swal.fire({
        title: 'ELIMINAR PILOTO',
        text: '¡Atención! Esto borrará permanentemente al piloto de la matriz central.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ELIMINAR PERMANENTEMENTE',
        cancelButtonText: 'CANCELAR',
        confirmButtonColor: '#ff0000',
        background: '#0a0a0a',
        color: '#fff'
    });

    if (!isConfirmed) return;

    try {
        const res = await fetch(`${BASE_URL}/api/clientes/delete`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id: id})
        });
        const data = await res.json();
        if (data.success) location.reload();
        else Swal.fire('ERROR', data.error || 'No se pudo eliminar al cliente', 'error');
    } catch (e) {
        Swal.fire('ERROR', 'Fallo técnico de conexión', 'error');
    }
}

function abrirEditarCliente(cliente) {
    document.getElementById('cli_id').value = cliente.id;
    document.getElementById('cli_nombre').value = cliente.nombre || '';
    document.getElementById('cli_telefono').value = cliente.telefono || '';
    document.getElementById('cli_rut').value = cliente.rut || '';
    document.getElementById('cli_email').value = cliente.email || '';
    document.getElementById('cli_foto').value = '';
    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalCliente')).show();
}

document.getElementById('formCliente').addEventListener('submit', async (e) => {
    e.preventDefault();
    const payload = {
        id: document.getElementById('cli_id').value,
        nombre: document.getElementById('cli_nombre').value.trim(),
        telefono: document.getElementById('cli_telefono').value.trim(),
        rut: document.getElementById('cli_rut').value.trim(),
        email: document.getElementById('cli_email').value.trim()
    };
    try {
        const res = await fetch(`${BASE_URL}/api/clientes/save`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (data.success) {
            location.reload();
        } else {
            Swal.fire('ERROR', data.error || 'No se pudo guardar', 'error');
        }
    } catch (e) {
        Swal.fire('ERROR', 'Fallo tecnico de conexion', 'error');
    }
});

async function subirFotoCliente() {
    const id = document.getElementById('cli_id').value;
    const file = document.getElementById('cli_foto').files[0];
    if (!id) return;
    if (!file) {
        Swal.fire('ERROR', 'Seleccione una foto', 'error');
        return;
    }
    const fd = new FormData();
    fd.append('id', id);
    fd.append('foto', file);
    try {
        const res = await fetch(`${BASE_URL}/api/clientes/foto`, { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
            Swal.fire('OK', 'Foto actualizada', 'success');
        } else {
            Swal.fire('ERROR', data.error || 'No se pudo subir la foto', 'error');
        }
    } catch (e) {
        Swal.fire('ERROR', 'Fallo tecnico de conexion', 'error');
    }
}

async function resetFotoCliente() {
    const id = document.getElementById('cli_id').value;
    if (!id) return;
    const fd = new FormData();
    fd.append('id', id);
    fd.append('reset', '1');
    try {
        const res = await fetch(`${BASE_URL}/api/clientes/foto`, { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
            Swal.fire('OK', 'Foto restablecida a default', 'success');
        } else {
            Swal.fire('ERROR', data.error || 'No se pudo resetear la foto', 'error');
        }
    } catch (e) {
        Swal.fire('ERROR', 'Fallo tecnico de conexion', 'error');
    }
}

</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
