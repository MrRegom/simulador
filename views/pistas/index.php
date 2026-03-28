<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<style>
/* 📱 ADAPTACIÓN RESPONSIVA PISTAS */
@media (max-width: 768px) {
    .admin-container { padding: 15px !important; }
    
    .header-actions-mobile {
        width: 100%;
        justify-content: center !important;
    }
    .header-actions-mobile button {
        width: 100%;
        padding: 15px;
        font-size: 1.1rem;
    }
    
    /* Auto-Stack de la Tarjeta */
    .pista-card-body {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 15px;
    }
    
    /* Empuja las acciones a la derecha cuando se apila */
    .pista-actions {
        width: 100%;
        justify-content: flex-end;
    }
    
    /* Evita desbordamiento de nombre */
    .pista-name-text {
        font-size: 1.2rem;
        word-wrap: break-word;
        word-break: break-all;
    }
}
</style>

<div class="admin-container px-3 py-4">
    <div class="d-flex justify-content-end align-items-center mb-4 header-actions-mobile">
        <button class="btn btn-danger font-monospace border-0" onclick="abrirModalNueva()">
            <i class="fas fa-plus me-2"></i> NUEVA_PISTA
        </button>
    </div>

    <div class="row" id="pistas_container">
        <?php foreach($pistas as $pista): ?>
        <div class="col-md-4 mb-4">
            <div class="card bg-black border-secondary h-100" style="border-left: 4px solid #ff0000 !important;">
                <div class="card-body d-flex justify-content-between align-items-center pista-card-body">
                    <div class="w-100">
                        <small class="text-muted font-monospace ls-1">IDENTIFICADOR: #<?php echo $pista['id']; ?></small>
                        <h4 class="text-white mt-1 fw-bold pista-name-text"><?php echo strtoupper($pista['nombre']); ?></h4>
                    </div>
                    <div class="d-flex gap-2 pista-actions">
                        <button class="btn btn-sm btn-outline-light border-0" onclick='abrirModalEditar(<?php echo json_encode($pista); ?>)'>
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger border-0" onclick="eliminarPista(<?php echo $pista['id']; ?>)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal Pistas -->
<div class="modal fade" id="modalPista" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-black border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-white font-monospace">> CONFIG_CIRCUITO</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formPista">
                    <input type="hidden" id="pista_id">
                    <div class="mb-3">
                        <label class="small text-muted mb-2 font-monospace">NOMBRE_OFICIAL_DE_PISTA</label>
                        <input type="text" id="pista_nombre" class="form-control bg-black border-secondary text-white" placeholder="Ej: Interlagos (Brasil)" required>
                    </div>
                    <button type="submit" class="btn btn-danger w-100 py-2 fw-bold font-monospace mt-3">GUARDAR_REGISTRO</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let modalInstance;
function getModal() {
    if (!modalInstance) {
        modalInstance = new bootstrap.Modal(document.getElementById('modalPista'));
    }
    return modalInstance;
}

function abrirModalNueva() {
    document.getElementById('formPista').reset();
    document.getElementById('pista_id').value = '';
    getModal().show();
}

function abrirModalEditar(pista) {
    document.getElementById('pista_id').value = pista.id;
    document.getElementById('pista_nombre').value = pista.nombre;
    getModal().show();
}

async function eliminarPista(id) {
    const { isConfirmed } = await Swal.fire({
        title: 'ELIMINAR PISTA',
        text: '¿Está seguro de ocultar esta pista de la selección del ranking?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'SÍ, ELIMINAR',
        cancelButtonText: 'CANCELAR'
    });

    if (!isConfirmed) return;

    try {
        const res = await fetch(`${BASE_URL}/api/pistas/delete`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id: id})
        });
        const data = await res.json();
        if (data.success) location.reload();
        else Swal.fire('ERROR', data.error || 'No se pudo eliminar', 'error');
    } catch (e) {
        Swal.fire('ERROR', 'Fallo técnico de conexión', 'error');
    }
}

document.getElementById('formPista').onsubmit = async (e) => {
    e.preventDefault();
    const id = document.getElementById('pista_id').value;
    const nombre = document.getElementById('pista_nombre').value;

    try {
        const res = await fetch(`${BASE_URL}/api/pistas/save`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id: id, nombre: nombre})
        });
        const data = await res.json();
        if (data.success) location.reload();
        else Swal.fire('ERROR', data.error || 'No se pudo guardar', 'error');
    } catch (e) {
        Swal.fire('ERROR', 'Fallo técnico de conexión', 'error');
    }
};
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
