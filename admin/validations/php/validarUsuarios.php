<?php
// Validaciones para usuarios
function validarUsuario($nombre, $usuario, $password, $rol) {
    $errores = [];
    
    // Validar nombre
    if (empty(trim($nombre))) {
        $errores[] = "El nombre es obligatorio";
    } elseif (strlen($nombre) < 3 || strlen($nombre) > 50) {
        $errores[] = "El nombre debe tener entre 3 y 50 caracteres";
    } elseif (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $nombre)) {
        $errores[] = "El nombre solo puede contener letras y espacios";
    }
    
    // Validar usuario
    if (empty(trim($usuario))) {
        $errores[] = "El usuario es obligatorio";
    } elseif (strlen($usuario) < 4 || strlen($usuario) > 20) {
        $errores[] = "El usuario debe tener entre 4 y 20 caracteres";
    } elseif (!preg_match("/^[a-zA-Z0-9]+$/", $usuario)) {
        $errores[] = "El usuario solo puede contener letras y números";
    }
    
    // Validar contraseña
    if (empty(trim($password))) {
        $errores[] = "La contraseña es obligatoria";
    } elseif (strlen($password) < 6) {
        $errores[] = "La contraseña debe tener al menos 6 caracteres";
    }
    
    // Validar rol
    if (empty($rol)) {
        $errores[] = "Debe seleccionar un rol";
    }
    
    return $errores;
}
