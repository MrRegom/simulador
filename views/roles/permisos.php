<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<style>
.permisos-container { margin-top: 60px; padding: 30px; background: #000; min-height: calc(100vh - 60px); }
.card-modulo { background: #050505; border: 1px solid #111; border-top: 2px solid #ff0000; margin-bottom: 20px; transition: 0.3s; }
.card-modulo:hover { border-color: #333; }
.modulo-title { font-family: 'Orbitron'; font-size: 0.8rem; color: #ff0000; letter-spacing: 2px; padding: 15px; border-bottom: 1px solid #111; background: rgba(255,0,0,0.02); }
.permiso-item { padding: 12px 15px; border-bottom: 1px solid #111; display: flex; align-items: center; transition: 0.2s; }
.permiso-item:hover { background: #0a0a0a; }
.permiso-item:last-child { border-bottom: none; }

/* Custom Checkbox Cyber */
.cyber-check { width: 18px; height: 18px; cursor: pointer; accent-color: #ff0000; }

/* 📱 ADAPTACIÓN RESPONSIVA CELULARES */
@media (max-width: 768px) {
    .permisos-container { 
        padding: 15px !important; 
        margin-top: 10px !important; 
    }
    .header-mobile { 
        flex-direction: column !important; 
        align-items: stretch !important; 
        gap: 20px; 
    }
    .header-mobile .title-container {
        flex-direction: column;
        text-align: center;
        width: 100%;
    }
    .header-mobile .header-title { 
        font-size: 1rem !important; 
        line-height: 1.5;
        margin-top: 10px !important;
    }
    .btn-sync { 
        width: 100%; 
        clip-path: none !important; /* Apagar el corte cyberpunk en móvil para no deformar */
        padding: 15px !important;
        font-size: 1rem;
        display: flex;
        justify-content: center;
        align-items: center;
    }
}
</style>

<div class="permisos-container">
    <div class="d-flex justify-content-between align-items-center mb-4 header-mobile">
        <div class="d-flex align-items-center gap-3 title-container">
            <a href="<?php echo \App\Config\Config::getAppUrl(); ?>/roles" class="btn btn-outline-light border-0"><i class="fas fa-arrow-left"></i> VOLVER</a>
            <h1 class="header-title" style="font-family:'Orbitron'; font-size:1.2rem; letter-spacing:2px; margin:0; color:#fff;">
                PERMISOS_DEL_PERFIL: <span class="text-danger d-block d-md-inline mt-1 mt-md-0"><?php echo strtoupper($rol['nombre']); ?></span>
            </h1>
        </div>
        <div class="w-100-mobile">
            <button class="btn btn-danger font-monospace fw-bold btn-sync" onclick="savePermisos()" style="clip-path: polygon(10% 0, 100% 0, 90% 100%, 0 100%); padding: 10px 25px;">
                <i class="fas fa-save me-2"></i> SINCRONIZAR_PRIVILEGIOS
            </button>
        </div>
    </div>

    <p class="text-muted small mb-4 font-monospace text-center text-md-start">
        Marque los módulos y acciones a los que este perfil tendrá acceso autorizado. La sincronización es instantánea para los usuarios activos.
    </p>

    <form id="permisosForm">
        <input type="hidden" name="rol_id" value="<?php echo $rol['id']; ?>">
        <div class="row">
            <?php foreach($permisos as $modulo => $items): ?>
            <div class="col-md-4">
                <div class="card card-modulo">
                    <div class="modulo-title"><?php echo strtoupper($modulo); ?></div>
                    <div class="card-body p-0">
                        <?php foreach($items as $p): ?>
                        <div class="permiso-item">
                            <input type="checkbox" name="permisos[]" value="<?php echo $p['id']; ?>" 
                                   class="cyber-check me-3" 
                                   <?php echo in_array($p['nombre'], $actuales) ? 'checked' : ''; ?>>
                            <div>
                                <div class="text-white small fw-bold"><?php echo strtoupper($p['nombre']); ?></div>
                                <div class="text-muted" style="font-size: 0.65rem;"><?php echo $p['descripcion']; ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </form>
</div>

<script>
async function savePermisos() {
    const formData = new FormData(document.getElementById('permisosForm'));
    const res = await fetch(`${BASE_URL}/api/roles/save_permisos`, {
        method: 'POST',
        body: formData
    });
    
    const data = await res.json();
    if(data.success) {
        Swal.fire({
            title: 'SISTEMA ACTUALIZADO',
            text: 'Los privilegios han sido reconfigurados correctamente.',
            icon: 'success', background: '#0a0a0a', color: '#fff', confirmButtonColor: '#ff0000'
        });
    }
}
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
