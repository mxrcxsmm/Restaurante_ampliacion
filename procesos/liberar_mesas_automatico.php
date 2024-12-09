<?php
require_once './conexion.php';

try {
    $conn->beginTransaction();

    // Obtener mesas que deberían ser liberadas
    $sql = "SELECT h.id_historial, h.id_mesa, m.num_sillas 
            FROM historial h 
            INNER JOIN mesa m ON h.id_mesa = m.id_mesa 
            WHERE h.hora_fin = '0000-00-00 00:00:00' 
            AND h.hora_reserva_fin <= CURRENT_TIME()";
    
    $stmt = $conn->query($sql);
    $mesasParaLiberar = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($mesasParaLiberar as $mesa) {
        // Actualizar estado de la mesa
        $sqlMesa = "UPDATE mesa SET libre = 0 WHERE id_mesa = ?";
        $stmtMesa = $conn->prepare($sqlMesa);
        $stmtMesa->execute([$mesa['id_mesa']]);

        // Actualizar historial
        $sqlHistorial = "UPDATE historial SET hora_fin = CURRENT_TIMESTAMP 
                        WHERE id_historial = ?";
        $stmtHistorial = $conn->prepare($sqlHistorial);
        $stmtHistorial->execute([$mesa['id_historial']]);

        // Actualizar stock de sillas
        $sqlStock = "UPDATE stock SET sillas_stock = sillas_stock + ? 
                    WHERE id_stock = 1";
        $stmtStock = $conn->prepare($sqlStock);
        $stmtStock->execute([$mesa['num_sillas']]);

        error_log("Mesa {$mesa['id_mesa']} liberada automáticamente");
    }

    $conn->commit();
} catch (PDOException $e) {
    $conn->rollBack();
    error_log("Error en liberar_mesas_automatico.php: " . $e->getMessage());
} finally {
    $conn = null;
}
?> 