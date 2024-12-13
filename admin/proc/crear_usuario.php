<?php
session_start();
require_once '../../procesos/conexion.php';
require_once '../validations/php/validarUsuarios.php';

// Verificar autenticación
if (!isset($_SESSION['id_camarero']) || $_SESSION['rol'] !== 'Administrador') {
    header('Location: ../../index.php');
    exit();
}

// Obtener roles para el formulario
try {
    $roles = $conn->query("SELECT * FROM roles")->fetchAll();
} catch (PDOException $e) {
    $_SESSION['error'] = "Error al cargar los roles: " . $e->getMessage();
    header('Location: ../admin.php');
    exit();
}

// Si hay POST, procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger y limpiar datos del formulario
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $rol = isset($_POST['rol']) ? $_POST['rol'] : '';

    // Validar los datos
    $errores = validarUsuario($nombre, $usuario, $password, $rol);

    if (!empty($errores)) {
        $_SESSION['error'] = implode("<br>", $errores);
        header('Location: ../view/crear_usuario.php');
        exit();
    }

    try {
        // Verificar si el usuario ya existe
        $stmt = $conn->prepare("SELECT COUNT(*) FROM camarero WHERE usuario = :usuario");
        $stmt->execute([':usuario' => $usuario]);
        
        if ($stmt->fetchColumn() > 0) {
            $_SESSION['error'] = "El nombre de usuario ya existe";
            header('Location: ../view/crear_usuario.php');
            exit();
        }

        // Si no hay errores, proceder con la inserción
        $sql = "INSERT INTO camarero (nombre, usuario, password, id_roles) 
                VALUES (:nombre, :usuario, :password, :rol)";
        $stmt = $conn->prepare($sql);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt->execute([
            ':nombre' => $nombre,
            ':usuario' => $usuario,
            ':password' => $hashedPassword,
            ':rol' => $rol
        ]);
        
        $_SESSION['success'] = "Usuario creado correctamente";
        header('Location: ../admin.php');
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al crear usuario: " . $e->getMessage();
        header('Location: ../view/crear_usuario.php');
        exit();
    }
}

// Incluir la vista
require_once '../view/crear_usuario.php';