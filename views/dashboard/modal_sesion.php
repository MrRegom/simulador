<?php
// Extracción del Modal a archivo separado
?>
<!-- QUANTUM MODAL: Start Session -->
<div class="modal fade" id="modalSesion" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: #141c26; border: 1px solid #253040; box-shadow: 0 0 30px rgba(0,0,0,0.5);">
            <div class="modal-header border-bottom border-secondary" style="border-color: #253040 !important;">
                <h5 class="modal-title font-monospace text-white fw-bold">
                    <i class="fas fa-rocket text-success me-2"></i> INITIALIZE SESSION
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formSesion">
                    <input type="hidden" id="equipo_id" name="equipo_id">
                    
                    <!-- Time Selection -->
                    <label class="text-muted text-uppercase x-small fw-bold mb-2">Duration</label>
                    <div class="d-flex gap-2 mb-4">
                        <button type="button" class="btn btn-outline-success font-weight-bold" onclick="selectTime(15)">15 MIN</button>
                        <button type="button" class="btn btn-outline-success font-weight-bold" onclick="selectTime(30)">30 MIN</button>
                        <button type="button" class="btn btn-outline-success font-weight-bold" onclick="selectTime(60)">60 MIN</button>
                    </div>
                    <div class="input-group mb-4" style="border: 1px solid #253040;">
                        <span class="input-group-text bg-dark border-0 text-muted">CUSTOM</span>
                        <input type="number" class="form-control bg-dark border-0 text-white font-monospace" id="tiempo_custom" placeholder="Minutes">
                    </div>

                    <!-- Payment -->
                    <label class="text-muted text-uppercase x-small fw-bold mb-2">Payment</label>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="input-group" style="border: 1px solid #253040;">
                                <span class="input-group-text bg-dark border-0 text-muted">$</span>
                                <input type="number" class="form-control bg-dark border-0 text-white font-monospace" placeholder="Amount">
                            </div>
                        </div>
                        <div class="col-6">
                            <select class="form-select bg-dark border-0 text-white" style="border: 1px solid #253040;">
                                <option value="efectivo">CASH</option>
                                <option value="tarjeta">CARD</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top border-secondary p-3" style="border-color: #253040 !important;">
                <button type="button" class="btn btn-secondary w-auto px-4" data-bs-dismiss="modal">CANCEL</button>
                <button type="button" class="btn btn-success w-auto px-4 font-weight-bold">
                    <i class="fas fa-power-off me-2"></i> START ENGINE
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    function selectTime(min) {
        document.getElementById('tiempo_custom').value = min;
    }
</script>
