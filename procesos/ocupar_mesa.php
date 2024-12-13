<?php
session_start();
include_once './conexion.php';

if (isset($_SESSION['id_camarero'])) {
    $id_tipoSala = trim($_POST['id_tipoSala']);
    $idCamarero = trim($_SESSION['id_camarero']);
    $idSala = trim($_POST['id_sala']);
    $idMesa = trim($_POST['id_mesa']);
    $num_sillas = trim($_POST['num_sillas']);
    $num_sillas_real = trim($_POST['num_sillas_real']);

    try {
        $conn->beginTransaction();

        // Verificar stock
        $sqlRestaStock = "SELECT sillas_stock FROM stock";
        $stmtRestaStock = $conn->prepare($sqlRestaStock);
        $stmtRestaStock->execute();
        $row = $stmtRestaStock->fetch(PDO::FETCH_ASSOC);
        $VerificaStock = $row['sillas_stock'];

        if ($VerificaStock >= ($num_sillas - 2)) {
            // Actualizar el stock si el número de sillas cambia
            if ($num_sillas != $num_sillas_real) {
                if ($num_sillas > $num_sillas_real) {
                    // Si se aumenta el número de sillas, resta el stock
                    $nuevoStockSillas = $VerificaStock - ($num_sillas - $num_sillas_real);
                } else {
                    // Si se reduce el número de sillas, suma el stock
                    $nuevoStockSillas = $VerificaStock + ($num_sillas_real - $num_sillas);
                }

                $sqlLimitSillas = "UPDATE stock SET sillas_stock = :nuevoStockSillas";
                $stmtLimitSillas = $conn->prepare($sqlLimitSillas);
                $stmtLimitSillas->bindParam(':nuevoStockSillas', $nuevoStockSillas, PDO::PARAM_INT);
                $stmtLimitSillas->execute();
            }

            // Actualizar mesa
            $sqlMesa = "UPDATE mesa SET libre = :reservado, num_sillas = :num_sillas WHERE id_mesa = :id_mesa";
            $stmtMesa = $conn->prepare($sqlMesa);
            $reservado = 1;
            $stmtMesa->bindParam(':reservado', $reservado, PDO::PARAM_INT);
            $stmtMesa->bindParam(':num_sillas', $num_sillas, PDO::PARAM_INT);
            $stmtMesa->bindParam(':id_mesa', $idMesa, PDO::PARAM_INT);
            $stmtMesa->execute();

            // Insertar en historial
            $sqlHistorial = "INSERT INTO historial (id_camarero, id_mesa, hora_inicio) VALUES (:id_camarero, :id_mesa, NOW())";
            $stmtHistorial = $conn->prepare($sqlHistorial);
            $stmtHistorial->bindParam(':id_camarero', $idCamarero, PDO::PARAM_INT);
            $stmtHistorial->bindParam(':id_mesa', $idMesa, PDO::PARAM_INT);
            $stmtHistorial->execute();

            // Confirmar transacción
            $conn->commit();

            $_SESSION['errorStock'] = false;
            $_SESSION['successOcupat'] = true;
        } else {
            $_SESSION['errorStock'] = true;
?>
            <form action="../view/mesa.php" method="POST" name="formulario">
                <input type="hidden" name="id_tipoSala" value="<?php echo $id_tipoSala ?>">
                <input type="hidden" name="id_sala" value="<?php echo $idSala ?>">
            </form>
            <script language="JavaScript">
                document.formulario.submit();
            </script>
        <?php
        }

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
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
        exit();
    }
} else {
    header('Location: ../index.php');
    exit();
}
?>
