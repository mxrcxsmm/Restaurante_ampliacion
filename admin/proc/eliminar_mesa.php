<?php
session_start();
require_once '../../procesos/conexion.php';

// Verificar autenticación y rol
if (!isset($_SESSION['id_camarero']) || $_SESSION['rol'] !== 'Administrador') {
    header('Location: ../../index.php');
    exit();
}

// Verificar que se recibió un ID válido
if (!isset($_GET['id_mesa']) || !is_numeric($_GET['id_mesa'])) {
    $_SESSION['error'] = "ID de mesa no válido";
    header('Location: ../admin.php');
    exit();
}

$id_mesa = intval($_GET['id_mesa']);

try {
    // Verificar si la mesa existe y obtener información
    $stmt = $conn->prepare("SELECT m.*, s.nombre_sala 
                           FROM mesa m 
                           INNER JOIN sala s ON m.id_sala = s.id_sala 
                           WHERE m.id_mesa = :id");
    $stmt->execute([':id' => $id_mesa]);
    $mesa = $stmt->fetch();
    
    if (!$mesa) {
        throw new Exception("La mesa no existe");
    }

    // Eliminar la mesa (las reservas se eliminarán por CASCADE)
    $stmt = $conn->prepare("DELETE FROM mesa WHERE id_mesa = :id");
    $stmt->execute([':id' => $id_mesa]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['success'] = true;
        $_SESSION['message'] = "Mesa eliminada correctamente";
    } else {
        throw new Exception("No se pudo eliminar la mesa");
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