document.addEventListener('DOMContentLoaded', function () {
    // Reloj Dashboard
    const timeBtns = document.querySelectorAll('.btn-time');
    let selectedTime = 0;

    // Selector de TIempo
    timeBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            // Reset
            timeBtns.forEach(b => b.classList.remove('active'));
            document.getElementById('tiempo_custom').value = '';

            // Activate
            this.classList.add('active');
            selectedTime = parseInt(this.getAttribute('data-time'));
        });
    });

    // Custom Time Reset
    const customInput = document.getElementById('tiempo_custom');
    if (customInput) {
        customInput.addEventListener('input', function () {
            timeBtns.forEach(b => b.classList.remove('active'));
            selectedTime = parseInt(this.value);
        });
    }
});

// Funcion Global para abrir modal
function iniciarSesion(equipoId) {
    const modalEl = document.getElementById('modalSesion');
    if (modalEl) {
        // Reset Form
        document.getElementById('formSesion').reset();
        document.querySelectorAll('.btn-time').forEach(b => b.classList.remove('active'));

        // Set ID
        // const idInput = document.getElementById('equipo_id');
        // if(idInput) idInput.value = equipoId;

        // Show Modal
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    } else {
        console.error('Modal no encontrado');
    }
}
