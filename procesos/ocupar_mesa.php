<?php
session_start();
include_once './conexion.php';

if (isset($_SESSION['id_camarero'])) {
    // Sanitizar variables de entrada
    $id_tipoSala = htmlspecialchars(trim($_POST['id_tipoSala']), ENT_QUOTES, 'UTF-8');
    $idCamarero = htmlspecialchars(trim($_SESSION['id_camarero']), ENT_QUOTES, 'UTF-8');
    $idSala = htmlspecialchars(trim($_POST['id_sala']), ENT_QUOTES, 'UTF-8');
    $idMesa = htmlspecialchars(trim($_POST['id_mesa']), ENT_QUOTES, 'UTF-8');
    $num_sillas = htmlspecialchars(trim($_POST['num_sillas']), ENT_QUOTES, 'UTF-8');
    $fecha_reserva = htmlspecialchars(trim($_POST['fecha_reserva']), ENT_QUOTES, 'UTF-8');
    $hora_inicio = htmlspecialchars(trim($_POST['hora_inicio']), ENT_QUOTES, 'UTF-8');
    $hora_fin = htmlspecialchars(trim($_POST['hora_fin']), ENT_QUOTES, 'UTF-8');

    try {
        $conn->beginTransaction();

        // Verificar si ya existe una reserva para esa mesa en ese horario
        $sqlVerificar = "SELECT COUNT(*) FROM historial 
                        WHERE id_mesa = ? 
                        AND fecha_reserva = ?
                        AND (
                            (hora_reserva_inicio BETWEEN ? AND ?) OR
                            (hora_reserva_fin BETWEEN ? AND ?) OR
                            (hora_reserva_inicio <= ? AND hora_reserva_fin >= ?)
                        )";
        $stmtVerificar = $conn->prepare($sqlVerificar);
        $stmtVerificar->execute([
            $idMesa, 
            $fecha_reserva, 
            $hora_inicio, 
            $hora_fin, 
            $hora_inicio, 
            $hora_fin, 
            $hora_inicio, 
            $hora_fin
        ]);

        if ($stmtVerificar->fetchColumn() > 0) {
            $_SESSION['error'] = "La mesa ya está reservada para este horario";
            header("Location: ../view/mesa.php?id_tipoSala=$id_tipoSala&id_sala=$idSala");
            exit();
        }

        // Insertar en historial
        $sqlOcupat = "INSERT INTO historial (id_camarero, id_mesa, fecha_reserva, hora_reserva_inicio, hora_reserva_fin) 
                     VALUES (?, ?, ?, ?, ?)";
        $stmtOcupat = $conn->prepare($sqlOcupat);

        // Debug: ver los valores antes de insertar
        error_log("Valores a insertar: " . print_r([
            'id_camarero' => $idCamarero,
            'id_mesa' => $idMesa,
            'fecha_reserva' => $fecha_reserva,
            'hora_inicio' => $hora_inicio,
            'hora_fin' => $hora_fin
        ], true));

        $stmtOcupat->execute([
            $idCamarero,
            $idMesa,
            $fecha_reserva,
            $hora_inicio,
            $hora_fin
        ]);

        // Actualizar estado de la mesa
        $sqlUpdateMesa = "UPDATE mesa SET libre = 1 WHERE id_mesa = ?";
        $stmtUpdateMesa = $conn->prepare($sqlUpdateMesa);
        $stmtUpdateMesa->execute([$idMesa]);

        $conn->commit();
        $_SESSION['successOcupat'] = true;

        header("Location: ../view/mesa.php?id_tipoSala=$id_tipoSala&id_sala=$idSala");
        exit();

    } catch (PDOException $e) {
        $conn->rollBack();
        error_log("Error en ocupar_mesa.php: " . $e->getMessage());
        $_SESSION['error'] = "Error al crear la reserva: " . $e->getMessage();
        header("Location: ../view/mesa.php?id_tipoSala=$id_tipoSala&id_sala=$idSala");
        exit();
    } finally {
        $conn = null;
    }
} else {
    header('Location: ../index.php');
    exit();
}
?>