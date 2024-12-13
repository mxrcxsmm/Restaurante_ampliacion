// Validaciones del formulario de creación de salas
function validacionFormularioSala() {
    const tipoSala = document.getElementById('tipo_sala').value;
    const nombreSala = document.getElementById('nombre_sala').value;

    let error = '';

    // Validar tipo de sala
    if (!tipoSala) {
        error += 'El tipo de sala es obligatorio.\n';
    }

    // Validar nombre de sala
    if (!nombreSala) {
        error += 'El nombre de la sala es obligatorio.\n';
    }

    if (error) {
        mostrarErrorSala(error);
        return false;
    }

    // Si todas las validaciones pasan, mostrar mensaje de éxito
    Swal.fire({
        icon: 'success',
        title: '¡Sala Creada!',
        text: 'La sala se ha creado correctamente',
        confirmButtonColor: '#28a745'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formCrearSala').submit();
        }
    });

    return false; // Importante para prevenir el envío automático del formulario
}

// Función para mostrar errores de creación de sala
function mostrarErrorSala(mensaje) {
    Swal.fire({
        icon: 'error',
        title: 'Error en la Creación de Sala',
        text: mensaje,
        confirmButtonColor: '#dc3545'
    });
}

// Asignar validación al formulario de creación de salas
document.getElementById('formCrearSala').addEventListener('submit', function(e) {
    e.preventDefault();
    validacionFormularioSala();
});