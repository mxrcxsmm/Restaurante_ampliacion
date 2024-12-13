<?php
session_start();
require_once '../../procesos/conexion.php';

// Verificar autenticación y rol
if (!isset($_SESSION['id_camarero']) || $_SESSION['rol'] !== 'Administrador') {
    header('Location: ../../index.php');
    exit();
}

// Verificar que se recibió un ID válido
if (!isset($_GET['id_camarero']) || !is_numeric($_GET['id_camarero'])) {
    $_SESSION['error'] = "ID de usuario no válido";
    header('Location: ../admin.php');
    exit();
}

$id_camarero = intval($_GET['id_camarero']);

try {
    // Prevenir la eliminación del propio usuario
    if ($id_camarero === intval($_SESSION['id_camarero'])) {
        throw new Exception("No puedes eliminar tu propio usuario");
    }

    // Verificar si el usuario existe
    $stmt = $conn->prepare("SELECT id_camarero FROM camarero WHERE id_camarero = :id");
    $stmt->execute([':id' => $id_camarero]);
    
    if (!$stmt->fetch()) {
        throw new Exception("El usuario no existe");
    }

    // Eliminar el usuario
    $stmt = $conn->prepare("DELETE FROM camarero WHERE id_camarero = :id");
    $stmt->execute([':id' => $id_camarero]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['success'] = true;
        $_SESSION['message'] = "Usuario eliminado correctamente";
    } else {
        throw new Exception("No se pudo eliminar el usuario");
    }

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
} catch (PDOException $e) {
    error_log("Error en la base de datos: " . $e->getMessage());
    $_SESSION['error'] = "Error al realizar la operación en la base de datos";
}

header('Location: ../admin.php');
exit();
?>
