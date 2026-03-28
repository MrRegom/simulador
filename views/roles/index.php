<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<style>
/* ROLES LAYOUT ADAPTATION */
.roles-container { 
    background: #000; 
    min-height: calc(100vh - 65px); 
    padding: 30px;
}

.card-cyber {
    background: #0a0a0a;
    border: 1px solid rgba(255,255,255,0.05);
    border-radius: 12px;
    transition: 0.3s;
}

.card-cyber:hover { transform: translateY(-5px); border-color: rgba(255,0,0,0.3); }

.btn-add-role {
    background: #ff0000;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 0.8rem;
    transition: 0.3s;
    color: #fff;
}
.btn-add-role:hover { background: #fff; color: #000; }

/* 📱 ADAPTACIÓN RESPONSIVA CELULARES */
@media (max-width: 768px) {
    .roles-container { padding: 15px !important; }
    
    .roles-header-mobile {
        flex-direction: column !important;
        text-align: center;
        gap: 15px;
        align-items: stretch !important;
    }
    
    .roles-header-mobile h4 {
        font-size: 1.3rem;
    }
    
    .btn-add-role {
        width: 100%;
        padding: 15px;
        font-size: 1rem;
    }
    
    .role-actions-mobile {
        flex-direction: column !important;
        gap: 15px;
    }
    
    .role-actions-mobile > div {
        display: flex;
        justify-content: space-between;
        width: 100%;
    }
    
    .role-actions-mobile > a {
        width: 100%;
        text-align: center;
        padding: 10px;
    }
}
</style>

<div class="roles-container">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4 roles-header-mobile">
            <h4 class="mb-0">Perfiles y Niveles de Acceso</h4>
            <button class="btn btn-add-role" onclick="openRoleModal()">
                <i class="fas fa-plus-circle me-2"></i> CREAR_PERFIL
            </button>
        </div>
        
    <div class="row">
        <?php foreach($roles as $r): ?>
        <div class="col-md-4 mb-4">
            <div class="card card-cyber h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h4 class="text-white font-monospace mb-0"><?php echo strtoupper($r['nombre']); ?></h4>
                        <span class="badge bg-danger">ID_<?php echo $r['id']; ?></span>
                    </div>
                    <p class="text-muted small"><?php echo $r['descripcion'] ?: 'Sin descripción definida.'; ?></p>
                    <hr class="border-secondary">
                    <div class="d-flex justify-content-between role-actions-mobile">
                        <div>
                            <button class="btn btn-sm btn-outline-light border-0" onclick='editRole(<?php echo htmlspecialchars(json_encode($r)); ?>)'>
                                <i class="fas fa-edit"></i>
                            </button>
                            <?php if($r['id'] != 1): ?>
                            <button class="btn btn-sm btn-outline-danger border-0" onclick="deleteRole(<?php echo $r['id']; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                        <a href="roles/permisos?id=<?php echo $r['id']; ?>" class="btn btn-sm btn-outline-info font-monospace" style="font-size:0.7rem;">
                            <i class="fas fa-key me-1"></i> GESTIONAR_PERMISOS
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    </div>
</div>

<!-- Modal Rol -->
<div class="modal fade" id="roleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white border-secondary" style="border-radius:0;">
            <div class="modal-header border-secondary">
                <h5 class="modal-title font-monospace" id="modalTitle">DEFINICIÓN_DE_PERFIL</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="roleForm">
                    <input type="hidden" id="r_id" name="id">
                    <div class="mb-3">
                        <label class="form-label text-muted" style="font-size:0.7rem;">NOMBRE_PERFIL</label>
                        <input type="text" class="form-control bg-black text-white border-secondary" id="r_nombre" name="nombre" placeholder="Ej: CAJERO" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted" style="font-size:0.7rem;">DESCRIPCIÓN_FUNCIONAL</label>
                        <textarea class="form-control bg-black text-white border-secondary" id="r_descripcion" name="descripcion" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger w-100 font-monospace fw-bold mt-3">GUARDAR_SISTEMA</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const roleModal = new bootstrap.Modal(document.getElementById('roleModal'));

    window.openRoleModal = () => {
        document.getElementById('modalTitle').innerText = "DEFINIR_NUEVO_PERFIL";
        document.getElementById('roleForm').reset();
        document.getElementById('r_id').value = "";
        roleModal.show();
    };

    window.editRole = (r) => {
        document.getElementById('modalTitle').innerText = "MODIFICAR_PERFIL_" + r.nombre.toUpperCase();
        document.getElementById('r_id').value = r.id;
        document.getElementById('r_nombre').value = r.nombre;
        document.getElementById('r_descripcion').value = r.descripcion;
        roleModal.show();
    };

    document.getElementById('roleForm').onsubmit = async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const res = await fetch(`${BASE_URL}/api/roles/save`, { method: 'POST', body: formData });
        const data = await res.json();
        if(data.success) location.reload();
    };

    window.deleteRole = async (id) => {
        const result = await Swal.fire({
            title: '¿ELIMINAR PERFIL?',
            text: "Los usuarios asignados a este perfil perderán sus permisos.",
            icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#ff0000', cancelButtonColor: '#333',
            background: '#0a0a0a', color: '#fff'
        });

        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('id', id);
            const res = await fetch(`${BASE_URL}/api/roles/delete`, { method: 'POST', body: formData });
            const data = await res.json();
            if(data.success) location.reload();
            else Swal.fire('Error', data.error, 'error');
        }
    };
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
