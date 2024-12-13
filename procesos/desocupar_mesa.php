<?php
session_start();
include_once './conexion.php';

if (isset($_SESSION['id_camarero'])) {
    // Escapar variables de entrada
    $id_tipoSala = trim($_POST['id_tipoSala']);
    $idSala = trim($_POST['id_sala']);
    $idMesa = trim($_POST['id_mesa']);
    $num_sillas = trim($_POST['num_sillas']);
    $num_sillas_real = trim($_POST['num_sillas_real']);

    $conn->beginTransaction();

    try {
        // Consulta para obtener el stock de sillas
        $sqlRestaStock = "SELECT * FROM stock";
        $stmtRestaStock = $conn->query($sqlRestaStock);
        $VerificaStock = 0;
        while ($row = $stmtRestaStock->fetch(PDO::FETCH_ASSOC)) {
            $VerificaStock = $row['sillas_stock'];
        }

        if ($num_sillas != $num_sillas_real) {
            // Calculando nuevo stock
            $nuevoStockSillas = $num_sillas_real - $num_sillas + $VerificaStock;
            $sqlLimitSillas = "UPDATE stock SET sillas_stock = :nuevoStockSillas";
            $stmtLimitSillas = $conn->prepare($sqlLimitSillas);
            $stmtLimitSillas->bindParam(':nuevoStockSillas', $nuevoStockSillas, PDO::PARAM_INT);
            $stmtLimitSillas->execute();
        }

        // Actualizando la mesa
        $sqlMesa = "UPDATE mesa SET libre = :libre, num_sillas = :num_sillas WHERE id_mesa = :id_mesa";
        $stmtMesa = $conn->prepare($sqlMesa);
        $reservado = 0;
        $stmtMesa->bindParam(':libre', $reservado, PDO::PARAM_INT);
        $stmtMesa->bindParam(':num_sillas', $num_sillas, PDO::PARAM_INT);
        $stmtMesa->bindParam(':id_mesa', $idMesa, PDO::PARAM_INT);
        $stmtMesa->execute();

        // Verificando historial
        $null = '0000-00-00 00:00:00';
        $sqlIDH = "SELECT * FROM historial WHERE id_mesa = :id_mesa AND hora_fin = :hora_fin";
        $stmtIDH = $conn->prepare($sqlIDH);
        $stmtIDH->bindParam(':id_mesa', $idMesa, PDO::PARAM_INT);
        $stmtIDH->bindParam(':hora_fin', $null, PDO::PARAM_STR);
        $stmtIDH->execute();
        $rowIDH = $stmtIDH->fetch(PDO::FETCH_ASSOC);
        $idH = $rowIDH['id_historial'] ?? null;

        if ($idH) {
            // Actualizando el historial para marcar la hora de fin
            $sqlDesocupat = "UPDATE historial SET hora_fin = NOW() WHERE id_mesa = :id_mesa AND id_historial = :id_historial";
            $stmtDesocupat = $conn->prepare($sqlDesocupat);
            $stmtDesocupat->bindParam(':id_mesa', $idMesa, PDO::PARAM_INT);
            $stmtDesocupat->bindParam(':id_historial', $idH, PDO::PARAM_INT);
            $stmtDesocupat->execute();
        }

        // Commit de la transacción
        $conn->commit();

        $_SESSION['successDesocupat'] = true;

        // Redirigir
        ?>
        <form action="../view/mesa.php" method="POST" name="formulario">
            <input type="hidden" name="id_tipoSala" value="<?php echo $id_tipoSala ?>">
            <input type="hidden" name="id_sala" value="<?php echo $idSala ?>">
        </form>
        <script language="JavaScript">
            document.formulario.submit();
        </script>
        <?php
    } catch (Exception $e) {
        // Rollback en caso de error
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
        exit();
    }
} else {
    header('Location: ../index.php');
    exit();
}
?>
