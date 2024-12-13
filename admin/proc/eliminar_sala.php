<?php
session_start();
require_once '../../procesos/conexion.php';

// Verificar autenticación y rol
if (!isset($_SESSION['id_camarero']) || $_SESSION['rol'] !== 'Administrador') {
    header('Location: ../../index.php');
    exit();
}

// Verificar que se recibió un ID válido
if (!isset($_GET['id_sala']) || !is_numeric($_GET['id_sala'])) {
    $_SESSION['error'] = "ID de sala no válido";
    header('Location: ../admin.php');
    exit();
}

$id_sala = intval($_GET['id_sala']);

try {
    // Verificar si la sala existe
    $stmt = $conn->prepare("SELECT id_sala FROM sala WHERE id_sala = :id");
    $stmt->execute([':id' => $id_sala]);
    
    if (!$stmt->fetch()) {
        throw new Exception("La sala no existe");
    }

    // Iniciamos una transacción
    $conn->beginTransaction();

    // Primero eliminamos las reservas de las mesas de esta sala
    $stmt = $conn->prepare("DELETE FROM reservas WHERE id_mesa IN 
                           (SELECT id_mesa FROM mesa WHERE id_sala = :id)");
    $stmt->execute([':id' => $id_sala]);

    // Eliminamos registros del historial
    $stmt = $conn->prepare("DELETE FROM historial WHERE id_mesa IN 
                           (SELECT id_mesa FROM mesa WHERE id_sala = :id)");
    $stmt->execute([':id' => $id_sala]);

    // Eliminamos las mesas de la sala
    $stmt = $conn->prepare("DELETE FROM mesa WHERE id_sala = :id");
    $stmt->execute([':id' => $id_sala]);

    // Finalmente eliminamos la sala
    $stmt = $conn->prepare("DELETE FROM sala WHERE id_sala = :id");
    $stmt->execute([':id' => $id_sala]);

    // Confirmamos la transacción
    $conn->commit();

    $_SESSION['success'] = true;
    $_SESSION['message'] = "Sala eliminada correctamente";

} catch (Exception $e) {
    // Revertimos la transacción en caso de error
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    $_SESSION['error'] = $e->getMessage();
} catch (PDOException $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    error_log("Error en la base de datos: " . $e->getMessage());
    $_SESSION['error'] = "Error al realizar la operación en la base de datos";
}

header('Location: ../admin.php');
exit();
?> 