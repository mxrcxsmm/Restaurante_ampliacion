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
    $num_sillas_real = htmlspecialchars(trim($_POST['num_sillas_real']), ENT_QUOTES, 'UTF-8');
    $hora_inicio = htmlspecialchars(trim($_POST['hora_inicio']), ENT_QUOTES, 'UTF-8');
    $hora_fin = htmlspecialchars(trim($_POST['hora_fin']), ENT_QUOTES, 'UTF-8');

    try {
        $conn->beginTransaction();

        // Verificar que la hora de inicio no sea anterior a la actual
        $horaActual = date('H:i');
        if ($hora_inicio < $horaActual) {
            $_SESSION['error'] = "La hora de inicio debe ser posterior a la hora actual";
            header("Location: ../view/mesa.php?id_tipoSala=$id_tipoSala&id_sala=$idSala");
            exit();
        }

        // Verificar si la mesa ya está reservada para ese horario
        $sqlVerificar = "SELECT COUNT(*) FROM historial 
                        WHERE id_mesa = ? 
                        AND hora_fin = '0000-00-00 00:00:00'
                        AND (
                            (hora_reserva_inicio BETWEEN ? AND ?) OR
                            (hora_reserva_fin BETWEEN ? AND ?) OR
                            (hora_reserva_inicio <= ? AND hora_reserva_fin >= ?)
                        )";
        $stmtVerificar = $conn->prepare($sqlVerificar);
        $stmtVerificar->execute([$idMesa, $hora_inicio, $hora_fin, $hora_inicio, $hora_fin, $hora_inicio, $hora_fin]);
        
        if ($stmtVerificar->fetchColumn() > 0) {
            $_SESSION['error'] = "La mesa ya está reservada para este horario";
            header("Location: ../view/mesa.php?id_tipoSala=$id_tipoSala&id_sala=$idSala");
            exit();
        }

        // Verificar stock disponible
        $sqlRestaStock = "SELECT sillas_stock FROM stock";
        $stmtStock = $conn->query($sqlRestaStock);
        $VerificaStock = $stmtStock->fetch(PDO::FETCH_ASSOC)['sillas_stock'];

        if ($VerificaStock >= ($num_sillas - 2)) {
            if ($num_sillas != $num_sillas_real) {
                if ($num_sillas > $num_sillas_real) {
                    $nuevoStockSillas = $VerificaStock - ($num_sillas - $num_sillas_real);
                } else {
                    $nuevoStockSillas = $VerificaStock + ($num_sillas_real - $num_sillas);
                }

                $sqlLimitSillas = "UPDATE stock SET sillas_stock = ?";
                $stmtLimitSillas = $conn->prepare($sqlLimitSillas);
                $stmtLimitSillas->execute([$nuevoStockSillas]);
            }

            // Actualizar mesa a ocupada (1)
            $sql = "UPDATE mesa SET libre = 1, num_sillas = ? WHERE id_mesa = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$num_sillas, $idMesa]);

            // Insertar en historial con horas de reserva
            $sqlOcupat = "INSERT INTO historial (id_camarero, id_mesa, hora_inicio, hora_reserva_inicio, hora_reserva_fin) 
                         VALUES (?, ?, NOW(), ?, ?)";
            $stmtOcupat = $conn->prepare($sqlOcupat);
            $stmtOcupat->execute([$idCamarero, $idMesa, $hora_inicio, $hora_fin]);

            $conn->commit();
            $_SESSION['successOcupat'] = true;
            $_SESSION['num_sillas'] = $num_sillas;
            $_SESSION['id_mesa'] = $idMesa;
        } else {
            $_SESSION['errorStock'] = true;
        }

        header("Location: ../view/mesa.php?id_tipoSala=$id_tipoSala&id_sala=$idSala");
        exit();

    } catch (PDOException $e) {
        $conn->rollBack();
        error_log("Error en ocupar_mesa.php: " . $e->getMessage());
        $_SESSION['error'] = "Error del sistema";
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