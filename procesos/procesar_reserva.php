<?php
session_start();
include_once './conexion.php';
require_once '../functions/validacionFormularioReservas.php';

if (!isset($_SESSION['id_camarero'])) {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Sanitizar entradas
        $id_camarero = filter_var($_POST['id_camarero'], FILTER_SANITIZE_NUMBER_INT);
        $id_mesa = filter_var($_POST['id_mesa'], FILTER_SANITIZE_NUMBER_INT);
        $nombre_cliente = htmlspecialchars(trim($_POST['nombre_cliente']));
        $fecha_reserva = htmlspecialchars(trim($_POST['fecha_reserva']));
        $hora_inicio = htmlspecialchars(trim($_POST['hora_reserva_inicio']));
        $hora_fin = htmlspecialchars(trim($_POST['hora_reserva_fin']));

        // Validar campos usando las funciones de validación
        $errorNombre = validaNombreClienteReserva($nombre_cliente);
        $errorFecha = validaFechaReserva($fecha_reserva);
        $errorHoras = validaHorasReserva($hora_inicio, $hora_fin, $fecha_reserva);

        // Si hay algún error, redirigir con mensaje
        if ($errorNombre || $errorFecha || $errorHoras) {
            $_SESSION['error'] = $errorNombre ?: $errorFecha ?: $errorHoras;
            header("Location: ../view/formulario_reserva.php?id_mesa=$id_mesa");
            exit();
        }

        // Verificar si ya existe una reserva para esa mesa en ese horario
        $sql_verificar = "SELECT COUNT(*) FROM reservas 
                         WHERE id_mesa = :id_mesa 
                         AND fecha_reserva = :fecha_reserva 
                         AND (
                             (hora_reserva_inicio BETWEEN :hora_inicio AND :hora_fin)
                             OR 
                             (hora_reserva_fin BETWEEN :hora_inicio AND :hora_fin)
                             OR 
                             (:hora_inicio BETWEEN hora_reserva_inicio AND hora_reserva_fin)
                         )";

        $stmt_verificar = $conn->prepare($sql_verificar);
        $stmt_verificar->bindParam(':id_mesa', $id_mesa, PDO::PARAM_INT);
        $stmt_verificar->bindParam(':fecha_reserva', $fecha_reserva);
        $stmt_verificar->bindParam(':hora_inicio', $hora_inicio);
        $stmt_verificar->bindParam(':hora_fin', $hora_fin);
        $stmt_verificar->execute();

        if ($stmt_verificar->fetchColumn() > 0) {
            $_SESSION['error'] = "Ya existe una reserva para esta mesa en el horario seleccionado";
            header("Location: ../view/formulario_reserva.php?id_mesa=$id_mesa");
            exit();
        }

        // Iniciar transacción
        $conn->beginTransaction();

        // Insertar la reserva
        $sql_insertar = "INSERT INTO reservas (id_camarero, id_mesa, fecha_reserva, 
                        hora_reserva_inicio, hora_reserva_fin, nombre_cliente) 
                        VALUES (:id_camarero, :id_mesa, :fecha_reserva, 
                        :hora_inicio, :hora_fin, :nombre_cliente)";

        $stmt_insertar = $conn->prepare($sql_insertar);
        $stmt_insertar->bindParam(':id_camarero', $id_camarero, PDO::PARAM_INT);
        $stmt_insertar->bindParam(':id_mesa', $id_mesa, PDO::PARAM_INT);
        $stmt_insertar->bindParam(':fecha_reserva', $fecha_reserva);
        $stmt_insertar->bindParam(':hora_inicio', $hora_inicio);
        $stmt_insertar->bindParam(':hora_fin', $hora_fin);
        $stmt_insertar->bindParam(':nombre_cliente', $nombre_cliente);
        $stmt_insertar->execute();

        // Actualizar estado de la mesa
        $sql_actualizar = "UPDATE mesa SET libre = 1 WHERE id_mesa = :id_mesa";
        $stmt_actualizar = $conn->prepare($sql_actualizar);
        $stmt_actualizar->bindParam(':id_mesa', $id_mesa, PDO::PARAM_INT);
        $stmt_actualizar->execute();

        // Confirmar transacción
        $conn->commit();

        $_SESSION['success'] = "Reserva realizada con éxito";
        header("Location: ../view/mesa.php");
        exit();

    } catch (PDOException $e) {
        // Revertir transacción en caso de error
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        error_log("Error en procesar_reserva.php: " . $e->getMessage());
        $_SESSION['error'] = "Error al procesar la reserva";
        header("Location: ../view/formulario_reserva.php?id_mesa=$id_mesa");
        exit();
    }
} else {
    header('Location: ../index.php');
    exit();
}
?> 