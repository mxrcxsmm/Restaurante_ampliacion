<?php
session_start();
include_once './conexion.php';

if (isset($_SESSION['id_camarero'])) {
    // Sanitizar variables de entrada
    $id_tipoSala = htmlspecialchars(trim($_POST['id_tipoSala']), ENT_QUOTES, 'UTF-8');
    $idSala = htmlspecialchars(trim($_POST['id_sala']), ENT_QUOTES, 'UTF-8');
    $idMesa = htmlspecialchars(trim($_POST['id_mesa']), ENT_QUOTES, 'UTF-8');

    try {
        $conn->beginTransaction();

        // Buscar la reserva activa para esta mesa
        $sqlBuscarReserva = "SELECT id_historial 
                            FROM historial 
                            WHERE id_mesa = ? 
                            AND fecha_reserva >= CURRENT_DATE
                            ORDER BY fecha_reserva DESC, hora_reserva_inicio DESC 
                            LIMIT 1";
        
        $stmtBuscar = $conn->prepare($sqlBuscarReserva);
        $stmtBuscar->execute([$idMesa]);
        
        if ($reserva = $stmtBuscar->fetch(PDO::FETCH_ASSOC)) {
            // Eliminar la reserva
            $sqlEliminar = "DELETE FROM historial WHERE id_historial = ?";
            $stmtEliminar = $conn->prepare($sqlEliminar);
            $stmtEliminar->execute([$reserva['id_historial']]);

            // Actualizar estado de la mesa a libre
            $sqlMesa = "UPDATE mesa SET libre = 0 WHERE id_mesa = ?";
            $stmtMesa = $conn->prepare($sqlMesa);
            $stmtMesa->execute([$idMesa]);

            $conn->commit();
            $_SESSION['successDesocupat'] = true;
        } else {
            $_SESSION['error'] = "No se encontró una reserva activa para esta mesa";
        }

        header("Location: ../view/mesa.php?id_tipoSala=$id_tipoSala&id_sala=$idSala");
        exit();

    } catch (PDOException $e) {
        $conn->rollBack();
        error_log("Error en desocupar_mesa.php: " . $e->getMessage());
        $_SESSION['error'] = "Error al cancelar la reserva: " . $e->getMessage();
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