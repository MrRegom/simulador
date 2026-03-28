<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<style>
/* Ajustes específicos para esta sección */
.app-wrapper .app-main { padding-top: 20px !important; margin-left: 260px; transition: all 0.3s ease; }
.admin-container {
    background: #000;
    min-height: calc(100vh - 70px);
}
/* 📱 ADAPTACIÓN RESPONSIVA */
@media (max-width: 991px) {
    .app-wrapper .app-main { margin-left: 0 !important; }
}

@media (max-width: 768px) {
    .admin-container { padding: 10px; }
    
    /* Buscador Cyber en Móvil */
    .cyber-card-header { flex-direction: column; align-items: center; gap: 15px; padding: 15px; }
    .cyber-card-header .card-title-hud { margin-bottom: 0; width: 100%; text-align: center; font-size: 1.1rem; }
    .cyber-card-header > div.d-flex { flex-direction: column; width: 100%; }
    .cyber-card-header input { width: 100% !important; text-align: center; }
    .cyber-card-header button { width: 100%; }

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
        justify-content: flex-end; /* Botones ubicados a la derecha */
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
}
</style>

<div class="admin-container">
    <div class="cyber-card">
        <div class="cyber-card-header">
            <h5 class="card-title-hud">> REGISTRO_INTEGRAL_A4R</h5>
            <div class="d-flex gap-2">
                <input type="text" id="searchInput" class="form-control input-cyber" style="width: 250px;" placeholder="Buscador por Piloto / RUT / Pista...">
                <button class="btn btn-cyber-search" onclick="currentOffset = 0; loadData();"><i class="fas fa-search me-2"></i>BUSCAR</button>
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hud text-center">
                    <thead>
                        <tr>
                            <th>IDENT</th>
                            <th>PILOTO_OFICIAL</th>
                            <th>RUT_ID</th>
                            <th>PISTA / CIRCUITO</th>
                            <th>TIEMPO_RÉCORD</th>
                            <th>FECHA_EXTRACCIÓN</th>
                            <th>OPC_ADMIN</th>
                        </tr>
                    </thead>
                    <tbody id="table-admin-body">
                        <tr><td colspan="7" class="py-5"><div class="spinner-border text-danger"></div><br><br>SINCRONIZANDO_DATOS...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- PAGINACIÓN HUD -->
        <div id="pagination-hud" class="pagination-hud">
            <!-- Se carga via JS -->
        </div>
    </div>
</div>

<!-- Modal Edición Cyber -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modal-cyber text-white">
      <div class="modal-header">
        <h5 class="modal-title font-monospace" style="color:#ff0000;"><i class="fas fa-shield-alt me-2"></i>RECALIBRACIÓN_DE_TELEMETRÍA</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <form id="editForm">
            <input type="hidden" id="edit_id">
            <div class="mb-4">
                <label class="font-monospace text-muted mb-2">IDENTIFICACIÓN_CIRCUITO</label>
                <input type="text" id="edit_pista" class="form-control input-cyber text-uppercase">
            </div>
            <div class="mb-4">
                <label class="font-monospace text-muted mb-2">TIEMPO_ASIGNADO (MM:SS.CCC)</label>
                <input type="text" id="edit_tiempo" class="form-control input-cyber font-monospace text-center fs-3 text-success" placeholder="00:00.000" required>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary font-monospace" data-bs-dismiss="modal">ABORTAR</button>
        <button type="button" class="btn btn-danger font-monospace fw-bold" onclick="saveEdit()">CONFIRMAR_REESCRITURA</button>
      </div>
    </div>
  </div>
</div>

<script>
let currentOffset = 0;
const recordsPerPage = 50;

// Auto format mask for time field in modal
document.getElementById('edit_tiempo').addEventListener('input', function (e) {
    let raw = this.value.replace(/[^0-9]/g, ''); 
    if (raw.length > 7) raw = raw.substring(0, 7); 
    
    let formatted = '';
    if (raw.length > 0) formatted += raw.substring(0, 2); 
    if (raw.length > 2) formatted += ':' + raw.substring(2, 4); 
    if (raw.length > 4) formatted += '.' + raw.substring(4, 7); 
    
    this.value = formatted;
});

document.getElementById('searchInput').addEventListener('keyup', (e) => {
    if(e.key === 'Enter') {
        currentOffset = 0;
        loadData();
    }
});

async function loadData() {
    const search = document.getElementById('searchInput').value;
    const res = await fetch(`${BASE_URL}/api/ranking/admin_list?search=${encodeURIComponent(search)}&offset=${currentOffset}`);
    const data = await res.json();
    
    const tbody = document.getElementById('table-admin-body');
    tbody.innerHTML = '';
    
    if(data.success && data.data.length > 0) {
        data.data.forEach(row => {
            tbody.innerHTML += `
                <tr class="ranking_row">
                    <td data-label="IDENTIFICADOR"><span class="id-badge">#${row.id}</span></td>
                    <td data-label="PILOTO_OFICIAL" class="text-uppercase fw-bold text-white text-md-center text-end">${row.piloto_nombre}</td>
                    <td data-label="RUT_ID" class="text-muted text-md-center text-end">${row.piloto_rut || '---'}</td>
                    <td data-label="PISTA / CIRCUITO" class="text-info text-md-center text-end">${row.pista.toUpperCase()}</td>
                    <td data-label="TIEMPO_RÉCORD" class="text-success fw-bold text-md-center text-end" style="font-size: 1.1rem;">${row.tiempo}</td>
                    <td data-label="FECHA_REGISTRO" class="text-muted text-md-center text-end" style="font-size:0.75rem;">${row.created_at}</td>
                    <td data-label="OPCIONES_ADMIN" class="text-md-center text-end">
                        <button class="btn-action btn-action-edit" onclick="openEdit(${row.id}, '${row.pista}', '${row.tiempo}')" title="Editar"><i class="fas fa-sliders-h"></i></button>
                        <button class="btn-action btn-action-delete ms-2" onclick="deleteRec(${row.id})" title="Eliminar"><i class="fas fa-trash-alt"></i></button>
                    </td>
                </tr>
            `;
        });
        renderPagination(data.total, data.limit, data.offset);
    } else {
        tbody.innerHTML = `<tr><td colspan="7" class="py-5 text-muted">X_X NO_SE_ENCONTRARON_REGISTROS_EN_ESTE_SECTOR</td></tr>`;
        document.getElementById('pagination-hud').innerHTML = '';
    }
}

function renderPagination(total, limit, offset) {
    const totalPages = Math.ceil(total / limit);
    const currentPage = Math.floor(offset / limit) + 1;
    const pagRoot = document.getElementById('pagination-hud');
    
    let html = `
        <button class="btn-page" onclick="changePage(${offset - limit})" ${offset === 0 ? 'disabled' : ''}>
            <i class="fas fa-chevron-left me-1"></i> ANTERIOR
        </button>
    `;
    
    // Mostrar 5 páginas alrededor de la actual
    let start = Math.max(1, currentPage - 2);
    let end = Math.min(totalPages, start + 4);
    if (end - start < 4) start = Math.max(1, end - 4);

    for (let i = start; i <= end; i++) {
        html += `
            <button class="btn-page ${i === currentPage ? 'active' : ''}" onclick="changePage(${(i - 1) * limit})">
                ${i}
            </button>
        `;
    }

    html += `
        <button class="btn-page" onclick="changePage(${offset + limit})" ${offset + limit >= total ? 'disabled' : ''}>
            SIGUIENTE <i class="fas fa-chevron-right ms-1"></i>
        </button>
    `;
    
    pagRoot.innerHTML = html;
}

function changePage(newOffset) {
    currentOffset = newOffset;
    loadData();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function openEdit(id, pista, tiempo) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_pista').value = pista;
    document.getElementById('edit_tiempo').value = tiempo;
    
    const modal = new bootstrap.Modal(document.getElementById('editModal'));
    modal.show();
}

async function saveEdit() {
    const formData = new FormData();
    formData.append('id', document.getElementById('edit_id').value);
    formData.append('pista', document.getElementById('edit_pista').value);
    formData.append('tiempo', document.getElementById('edit_tiempo').value);

    const res = await fetch(`${BASE_URL}/api/ranking/admin_update`, { method: 'POST', body: formData });
    const data = await res.json();
    
    if(data.success) {
        Swal.fire({
            title: 'TELEMETERÍA_REGRABADA',
            text: 'La base de datos central ha sido actualizada exitosamente.',
            icon: 'success',
            background: '#0a0a0a',
            color: '#fff',
            confirmButtonColor: '#ff0000'
        });
        const modalEl = document.getElementById('editModal');
        const modalObj = bootstrap.Modal.getInstance(modalEl);
        modalObj.hide();
        loadData();
    } else {
        Swal.fire('ERROR_DE_SISTEMA', data.error, 'error');
    }
}

async function deleteRec(id) {
    const result = await Swal.fire({
        title: '¿ESTÁS SEGURO?',
        text: "EL REGISTRO SERÁ BORRADO PERMANENTEMENTE DE LA MATRIZ.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ff0000',
        cancelButtonColor: '#333',
        confirmButtonText: 'ELIMINAR',
        cancelButtonText: 'CANCELAR',
        background: '#0a0a0a',
        color: '#fff'
    });

    if (result.isConfirmed) {
        const formData = new FormData();
        formData.append('id', id);
        const res = await fetch(`${BASE_URL}/api/ranking/admin_delete`, { method: 'POST', body: formData });
        const data = await res.json();
        if(data.success) {
            loadData();
        } else {
            Swal.fire('ERROR_DE_SISTEMA', data.error, 'error');
        }
    }
}

// Carga Inicial
loadData();
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
