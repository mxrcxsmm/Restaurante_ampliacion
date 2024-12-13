// Validaciones del formulario de creación de usuarios
function validacionFormularioUsuario() {
    const nombre = document.getElementById('nombre').value.trim();
    const usuario = document.getElementById('usuario').value.trim();
    const password = document.getElementById('password').value.trim();
    const rol = document.getElementById('rol').value;

    // Validar nombre
    if (!nombre) {
        mostrarErrorUsuario('El nombre es obligatorio.');
        return false;
    } else if (nombre.length < 3 || nombre.length > 50) {
        mostrarErrorUsuario('El nombre debe tener entre 3 y 50 caracteres.');
        return false;
    }

    // Validar usuario
    if (!usuario) {
        mostrarErrorUsuario('El usuario es obligatorio.');
        return false;
    }

    // Validar contraseña
    if (!password) {
        mostrarErrorUsuario('La contraseña es obligatoria.');
        return false;
    }

    // Validar rol
    if (!rol) {
        mostrarErrorUsuario('El rol es obligatorio.');
        return false;
    }

    // Si todas las validaciones pasan, mostrar mensaje de éxito
    Swal.fire({
        icon: 'success',
        title: '¡Usuario Creado!',
        text: 'El usuario ha sido creado correctamente',
        confirmButtonColor: '#28a745'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formCrearUsuario').submit();
        }
    });

    return false; // Importante para prevenir el envío automático del formulario
}

// Función para mostrar errores de creación de usuario
function mostrarErrorUsuario(mensaje) {
    Swal.fire({
        icon: 'error',
        title: 'Error en la Creación de Usuario',
        text: mensaje,
        confirmButtonColor: '#dc3545'
    });
}

// Asignar validación al formulario de creación de usuarios
document.getElementById('formCrearUsuario').addEventListener('submit', function(e) {
    e.preventDefault();
    validacionFormularioUsuario();
});