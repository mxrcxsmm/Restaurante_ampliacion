// Validaciones del formulario de creación de mesas
function validacionFormularioMesa() {
    const idSala = document.getElementById('id_sala').value;
    const numSillas = document.getElementById('num_sillas').value;

    let error = '';

    // Validar sala
    if (!idSala) {
        error += 'La sala es obligatoria.\n';
    }

    // Validar número de sillas
    if (!numSillas || isNaN(numSillas) || numSillas < 2 || numSillas > 12) {
        error += 'El número de sillas debe ser un valor entre 2 y 12.\n';
    }

    if (error) {
        mostrarErrorMesa(error);
        return false;
    }

    // Si todas las validaciones pasan, mostrar mensaje de éxito
    Swal.fire({
        icon: 'success',
        title: '¡Mesa Creada!',
        text: 'La mesa se ha creado correctamente',
        confirmButtonColor: '#28a745'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formCrearMesa').submit();
        }
    });

    return false; // Importante para prevenir el envío automático del formulario
}

// Función para mostrar errores de creación de mesa
function mostrarErrorMesa(mensaje) {
    Swal.fire({
        icon: 'error',
        title: 'Error en la Creación de Mesa',
        text: mensaje,
        confirmButtonColor: '#dc3545'
    });
}

// Asignar validación al formulario de creación de mesas
document.getElementById('formCrearMesa').addEventListener('submit', function(e) {
    e.preventDefault();
    validacionFormularioMesa();
});