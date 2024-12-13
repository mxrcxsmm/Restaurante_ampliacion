// Validaciones del formulario de reservas
function validacionFormularioReservas() {
    const nombreCliente = document.getElementById('nombre_cliente').value.trim();
    const fecha = document.getElementById('fecha_reserva').value;
    const horaInicio = document.getElementById('hora_inicio').value;
    const horaFin = document.getElementById('hora_fin').value;

    // Validar nombre
    if (!nombreCliente) {
        mostrarErrorReserva('El nombre del cliente es obligatorio');
        return false;
    }

    if (nombreCliente.length < 3 || nombreCliente.length > 50) {
        mostrarErrorReserva('El nombre debe tener entre 3 y 50 caracteres');
        return false;
    }

    if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(nombreCliente)) {
        mostrarErrorReserva('El nombre solo puede contener letras y espacios');
        return false;
    }

    // Validar fecha
    if (!fecha) {
        mostrarErrorReserva('La fecha es obligatoria');
        return false;
    }

    const fechaActual = new Date().toISOString().split('T')[0];
    if (fecha < fechaActual) {
        mostrarErrorReserva('La fecha no puede ser anterior a hoy');
        return false;
    }

    // Validar horas
    if (!horaInicio || !horaFin) {
        mostrarErrorReserva('Debe seleccionar hora de inicio y fin');
        return false;
    }

    const inicio = parseInt(horaInicio);
    const fin = parseInt(horaFin);

    if (fin - inicio > 2) {
        mostrarErrorReserva('La reserva no puede durar más de 2 horas');
        return false;
    }

    if (fecha === fechaActual) {
        const horaActual = new Date().getHours();
        if (inicio <= horaActual) {
            mostrarErrorReserva('No se puede reservar para una hora anterior a la actual');
            return false;
        }
    }

    // Si todas las validaciones pasan, mostrar mensaje de éxito
    Swal.fire({
        icon: 'success',
        title: '¡Mesa Reservada!',
        text: 'La reserva se ha realizado correctamente',
        confirmButtonColor: '#28a745'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('reservaForm').submit();
        }
    });

    return false; // Importante para prevenir el envío automático del formulario
}

// Función para mostrar errores de reserva
function mostrarErrorReserva(mensaje) {
    Swal.fire({
        icon: 'error',
        title: 'Error en la Reserva',
        text: mensaje,
        confirmButtonColor: '#28a745'
    });
}

// Asignar validación al formulario de reservas
document.getElementById('reservaForm').addEventListener('submit', function(e) {
    e.preventDefault();
    validacionFormularioReservas();
});