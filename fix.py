import os

file_path = r"c:\xampp\htdocs\servirec\views\ranking\config.php"

with open(file_path, "r", encoding="utf-8") as f:
    lines = f.readlines()

new_lines = lines[:279] # Keep up to line 279 (0-indexed 278, which is <div class="col-md-4">)

replacement = """                        <div class="cyber-panel">
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
                                            <option value="<?php echo htmlspecialchars($p['nombre']); ?>" <?php echo ((isset($active_track) ? $active_track : '') === $p['nombre']) ? 'selected' : ''; ?>>
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
"""

new_lines.append(replacement)

# extend with the rest of the lines starting from `<div class="col-md-4">` (which is line 513, so index 512)
new_lines.extend(lines[512:])

with open(file_path, "w", encoding="utf-8") as f:
    f.writelines(new_lines)

print("Repair complete.")
