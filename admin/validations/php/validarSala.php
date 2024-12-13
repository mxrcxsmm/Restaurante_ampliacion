<?php
// Validar campo vacío
function validaCampoVacio($campo) {
    return empty(trim($campo));
}

// Validar nombre de sala
function validaNombreSala($nombre_sala) {
    if (validaCampoVacio($nombre_sala)) {
        return "El nombre de la sala es obligatorio.";
    }
    return "";
}

// Validar tipo de sala
function validaTipoSala($tipo_sala) {
    if (validaCampoVacio($tipo_sala)) {
        return "El tipo de sala es obligatorio.";
    }
    return "";
}
?> 