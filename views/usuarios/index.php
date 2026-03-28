<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<style>
/* ============================================
   MÓDULO USUARIOS — HUD RESPONSIVE v2
   Desktop: tabla. Mobile: tarjetas.
   ============================================ */
.users-container {
    background: #000;
    min-height: calc(100vh - 65px);
    padding: 20px;
}

/* ---- Header ---- */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    flex-wrap: wrap;
    gap: 15px;
}
.page-header h4 {
    font-size: 1.1rem;
    font-weight: 700;
    margin: 0;
    color: #fff;
}
.btn-add-user {
    background: #ff0000;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 0.8rem;
    color: #fff;
    white-space: nowrap;
    transition: 0.2s;
}
.btn-add-user:hover { background: #cc0000; color: #fff; }

/* ---- Tabla Desktop ---- */
.card-cyber {
    background: #0a0a0a;
    border: 1px solid rgba(255,255,255,0.05);
    border-radius: 12px;
    overflow: hidden;
}
.table-cyber { margin: 0; }
.table-cyber th {
    background: #111;
    border-bottom: 1px solid #222;
    color: #555;
    font-size: 0.65rem;
    padding: 16px 20px;
    letter-spacing: 2px;
    text-transform: uppercase;
    white-space: nowrap;
}
.table-cyber td {
    padding: 16px 20px;
    border-bottom: 1px solid rgba(255,255,255,0.03);
    vertical-align: middle;
}
.table-cyber tbody tr:last-child td { border-bottom: none; }

/* ---- Tarjetas Mobile ---- */
.user-card {
    background: #0d0d0d;
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 14px;
    padding: 18px;
    margin-bottom: 12px;
    position: relative;
}
.user-card .user-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
}
.user-card .u-name {
    font-weight: 800;
    font-size: 1rem;
    color: #fff;
}
.user-card .u-username {
    font-family: monospace;
    color: #ff4444;
    font-size: 0.85rem;
    margin-top: 2px;
}
.user-card .card-actions {
    display: flex;
    gap: 8px;
}
.user-card .card-meta {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid rgba(255,255,255,0.05);
}
.user-card .meta-item {
    font-size: 0.7rem;
    color: #666;
    display: flex;
    align-items: center;
    gap: 5px;
}
.user-card .meta-item i { color: #444; }

/* 📱 ADAPTACIÓN RESPONSIVA CELULARES */
@media (max-width: 768px) {
    .users-container { padding: 15px !important; }
    
    .page-header {
        flex-direction: column;
        align-items: stretch;
        text-align: center;
        gap: 15px;
    }
    .page-header h4 { font-size: 1.3rem; }
    .btn-add-user {
        width: 100%;
        padding: 15px;
        font-size: 1rem;
    }

    .user-card-header {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 12px;
    }
    .user-card .card-actions {
        width: 100%;
        justify-content: flex-end;
    }
}
</style>

<div class="users-container">
    <div class="container-fluid px-0">

        <!-- Header -->
        <div class="page-header">
            <h4><i class="fas fa-users-cog me-2 text-danger"></i>Administración de Usuarios</h4>
            <button class="btn btn-add-user" onclick="openModal()">
                <i class="fas fa-user-plus me-2"></i>AGREGAR_USUARIO
            </button>
        </div>

        <!-- ============================
             VISTA DESKTOP (tabla)
             Oculta en móviles < 768px
             ============================ -->
        <div class="card card-cyber d-none d-md-block">
            <div class="table-responsive">
                <table class="table table-dark table-hover table-cyber">
                    <thead>
                        <tr>
                            <th>NOMBRE_COMPLETO</th>
                            <th>IDENTIFICADOR</th>
                            <th>ROL_SISTEMA</th>
                            <th>ÚLTIMO_ACCESO</th>
                            <th>ESTADO</th>
                            <th class="text-end">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($usuarios as $u): ?>
                        <tr>
                            <td>
                                <div class="fw-bold text-white"><?php echo htmlspecialchars($u['nombre']); ?></div>
                            </td>
                            <td>
                                <code class="text-danger"><?php echo htmlspecialchars($u['usuario']); ?></code>
                            </td>
                            <td>
                                <span class="badge <?php echo $u['rol'] == 'admin' ? 'bg-danger' : 'bg-secondary'; ?> font-monospace">
                                    <?php echo strtoupper($u['rol']); ?>
                                </span>
                            </td>
                            <td>
                                <small class="text-muted"><?php echo $u['ultimo_login'] ?? 'NUNCA'; ?></small>
                            </td>
                            <td>
                                <?php if($u['estado']): ?>
                                    <span class="text-success small"><i class="fas fa-circle me-1" style="font-size:0.5rem;vertical-align:middle;"></i>ACTIVO</span>
                                <?php else: ?>
                                    <span class="text-muted small"><i class="fas fa-circle me-1" style="font-size:0.5rem;vertical-align:middle;"></i>INACTIVO</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-light border-0" onclick="editUser(<?php echo htmlspecialchars(json_encode($u)); ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger border-0" onclick="deleteUser(<?php echo $u['id']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ============================
             VISTA MOBILE (tarjetas)
             Oculta en desktop >= 768px
             ============================ -->
        <div class="d-md-none">
            <?php foreach($usuarios as $u): ?>
            <div class="user-card">
                <div class="user-card-header">
                    <div>
                        <div class="u-name"><?php echo htmlspecialchars($u['nombre']); ?></div>
                        <div class="u-username">@<?php echo htmlspecialchars($u['usuario']); ?></div>
                    </div>
                    <div class="card-actions">
                        <button class="btn btn-sm btn-outline-secondary border-0 px-2"
                                onclick="editUser(<?php echo htmlspecialchars(json_encode($u)); ?>)">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger border-0 px-2"
                                onclick="deleteUser(<?php echo $u['id']; ?>)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="card-meta">
                    <span class="badge <?php echo $u['rol'] == 'admin' ? 'bg-danger' : 'bg-secondary'; ?> font-monospace">
                        <?php echo strtoupper($u['rol']); ?>
                    </span>
                    <?php if($u['estado']): ?>
                        <span class="meta-item text-success"><i class="fas fa-check-circle"></i> ACTIVO</span>
                    <?php else: ?>
                        <span class="meta-item text-muted"><i class="fas fa-times-circle"></i> INACTIVO</span>
                    <?php endif; ?>
                    <span class="meta-item">
                        <i class="fas fa-clock"></i>
                        <?php echo $u['ultimo_login'] ?? 'Sin acceso'; ?>
                    </span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <!-- /VISTA MOBILE -->

    </div>
</div>

<!-- ============================================================
     MODAL — Crear / Editar Usuario
     ============================================================ -->
<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white border-secondary" style="border-radius:12px;">
            <div class="modal-header border-secondary">
                <h5 class="modal-title font-monospace" id="modalTitle">DATOS_DE_USUARIO</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="userForm">
                    <input type="hidden" id="u_id" name="id">

                    <div class="mb-3">
                        <label class="form-label text-muted" style="font-size:0.7rem;">NOMBRE_COMPLETO</label>
                        <input type="text" class="form-control bg-black text-white border-secondary" id="u_nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted" style="font-size:0.7rem;">IDENTIFICADOR_LOGIN</label>
                        <input type="text" class="form-control bg-black text-white border-secondary" id="u_usuario" name="usuario" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted" style="font-size:0.7rem;">NUEVA_CONTRASEÑA <span class="text-muted">(Dejar en blanco si no cambia)</span></label>
                        <input type="password" class="form-control bg-black text-white border-secondary" id="u_password" name="password">
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-12 mb-3">
                            <label class="form-label text-muted" style="font-size:0.7rem;">ROL_DE_ACCESO</label>
                            <select class="form-select bg-black text-white border-secondary" id="u_rol" name="rol" required>
                                <?php foreach($roles as $r): ?>
                                <option value="<?php echo htmlspecialchars($r['nombre']); ?>">
                                    <?php echo strtoupper(htmlspecialchars($r['nombre'])); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 col-12 mb-3">
                            <label class="form-label text-muted" style="font-size:0.7rem;">ESTADO_CUENTA</label>
                            <select class="form-select bg-black text-white border-secondary" id="u_estado" name="estado" required>
                                <option value="1">ACTIVO</option>
                                <option value="0">INACTIVO</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-danger w-100 font-monospace fw-bold mt-2">
                        <i class="fas fa-save me-2"></i>GUARDAR_CAMBIOS
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const userModalEl = document.getElementById('userModal');
    const myModal = new bootstrap.Modal(userModalEl);

    /* Abrir modal para CREAR */
    window.openModal = function() {
        document.getElementById('modalTitle').innerText = "NUEVO_USUARIO_SISTEMA";
        document.getElementById('userForm').reset();
        document.getElementById('u_id').value = "";
        myModal.show();
    };

    /* Abrir modal para EDITAR */
    window.editUser = function(u) {
        document.getElementById('modalTitle').innerText = "EDITAR_USUARIO: " + u.usuario.toUpperCase();
        document.getElementById('u_id').value    = u.id;
        document.getElementById('u_nombre').value  = u.nombre;
        document.getElementById('u_usuario').value = u.usuario;
        document.getElementById('u_password').value = "";
        document.getElementById('u_estado').value  = u.estado;
        // Seleccionar el rol dinámico correcto
        const rolSelect = document.getElementById('u_rol');
        for (let opt of rolSelect.options) {
            if (opt.value === u.rol) { opt.selected = true; break; }
        }
        myModal.show();
    };

    /* Guardar usuario (AJAX) */
    document.getElementById('userForm').onsubmit = async (e) => {
        e.preventDefault();
        try {
            const res  = await fetch(`${BASE_URL}/api/usuario/save`, { method: 'POST', body: new FormData(e.target) });
            const data = await res.json();
            if (data.success) {
                Swal.fire({
                    title: 'USUARIO GUARDADO',
                    text: 'Datos sincronizados correctamente.',
                    icon: 'success', background: '#0a0a0a', color: '#fff', confirmButtonColor: '#ff0000'
                }).then(() => location.reload());
            } else {
                Swal.fire({ title: 'ERROR', text: data.error || 'No se pudo guardar.', icon: 'error', background: '#0a0a0a', color: '#fff', confirmButtonColor: '#ff0000' });
            }
        } catch (err) {
            console.error(err);
            alert("Error de conexión al guardar usuario.");
        }
    };

    /* Eliminar usuario */
    window.deleteUser = async function(id) {
        const result = await Swal.fire({
            title: '¿REVOCAR ACCESO?',
            text: "Esta acción eliminará permanentemente el acceso del usuario al sistema.",
            icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#ff0000', cancelButtonColor: '#333',
            confirmButtonText: 'SÍ, ELIMINAR', cancelButtonText: 'CANCELAR',
            background: '#0a0a0a', color: '#fff'
        });
        if (result.isConfirmed) {
            const fd = new FormData();
            fd.append('id', id);
            const res  = await fetch(`${BASE_URL}/api/usuario/delete`, { method: 'POST', body: fd });
            const data = await res.json();
            if (data.success) {
                location.reload();
            } else {
                Swal.fire({ title: 'BLOQUEADO', text: data.error, icon: 'error', background: '#0a0a0a', color: '#fff', confirmButtonColor: '#ff0000' });
            }
        }
    };
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
