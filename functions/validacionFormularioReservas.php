<?php
// Validar campo vacío
function validaCampoVacio($campo) {
    return empty(trim($campo));
}

// Validar nombre del cliente
function validaNombreClienteReserva($nombre) {
    if (validaCampoVacio($nombre)) {
        return "El nombre del cliente es obligatorio";
    }
    if (strlen($nombre) < 3 || strlen($nombre) > 50) {
        return "El nombre debe tener entre 3 y 50 caracteres";
    }
    if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $nombre)) {
        return "El nombre solo puede contener letras y espacios";
    }
    return "";
}

// Validar fecha
function validaFechaReserva($fecha) {
    if (validaCampoVacio($fecha)) {
        return "La fecha es obligatoria";
    }
    if ($fecha < date('Y-m-d')) {
        return "La fecha no puede ser anterior a hoy";
    }
    return "";
}

// Validar horas
function validaHorasReserva($horaInicio, $horaFin, $fecha) {
    if (validaCampoVacio($horaInicio) || validaCampoVacio($horaFin)) {
        return "Las horas son obligatorias";
    }

    $inicio = (int)substr($horaInicio, 0, 2);
    $fin = (int)substr($horaFin, 0, 2);

    if ($inicio < 12 || $inicio > 23 || $fin < 13 || $fin > 24) {
        return "Las horas deben estar entre las 12:00 y las 24:00";
    }

    if (($fin - $inicio) > 2) {
        return "La reserva no puede durar más de 2 horas";
    }

    if ($fecha === date('Y-m-d') && $inicio <= (int)date('H')) {
        return "No se puede reservar para una hora anterior a la actual";
    }

    return "";
} 