<?php
// Validar campo vacío
function validaCampoVacio($campo) {
    return empty(trim($campo));
}

// Validar número de sillas
function validaNumeroSillas($num_sillas) {
    if (validaCampoVacio($num_sillas) || !is_numeric($num_sillas) || $num_sillas < 2 || $num_sillas > 12) {
        return "El número de sillas debe ser un valor entre 2 y 12.";
    }
    return "";
}

// Validar sala
function validaSala($id_sala) {
    if (validaCampoVacio($id_sala)) {
        return "La sala es obligatoria.";
    }
    return "";
}
?> 