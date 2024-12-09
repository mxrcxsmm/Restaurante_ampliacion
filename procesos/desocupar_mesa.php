<?php
session_start();
include_once './conexion.php';

if (isset($_SESSION['id_camarero'])) {
    // Sanitizar variables de entrada
    $id_tipoSala = htmlspecialchars(trim($_POST['id_tipoSala']), ENT_QUOTES, 'UTF-8');
    $idSala = htmlspecialchars(trim($_POST['id_sala']), ENT_QUOTES, 'UTF-8');
    $idMesa = htmlspecialchars(trim($_POST['id_mesa']), ENT_QUOTES, 'UTF-8');
    $num_sillas = htmlspecialchars(trim($_POST['num_sillas']), ENT_QUOTES, 'UTF-8');
    $num_sillas_real = htmlspecialchars(trim($_POST['num_sillas_real']), ENT_QUOTES, 'UTF-8');

    try {
        $conn->beginTransaction();

        // Consultar stock actual
        $sqlRestaStock = "SELECT sillas_stock FROM stock";
        $stmtStock = $conn->query($sqlRestaStock);
        $VerificaStock = $stmtStock->fetch(PDO::FETCH_ASSOC)['sillas_stock'];

        if ($num_sillas != $num_sillas_real) {
            $nuevoStockSillas = $num_sillas_real - $num_sillas + $VerificaStock;
            $sqlLimitSillas = "UPDATE stock SET sillas_stock = ?";
            $stmtLimitSillas = $conn->prepare($sqlLimitSillas);
            $stmtLimitSillas->execute([$nuevoStockSillas]);
        }

        // Actualizar mesa
        $sql = "UPDATE mesa SET libre = ?, num_sillas = ? WHERE id_mesa = ?";
        $stmt = $conn->prepare($sql);
        $reservado = 0;
        $stmt->execute([$reservado, $num_sillas, $idMesa]);

        // Actualizar historial
        $sqlIDH = "SELECT id_historial FROM historial WHERE id_mesa = ? AND hora_fin = '0000-00-00 00:00:00'";
        $stmtIDH = $conn->prepare($sqlIDH);
        $stmtIDH->execute([$idMesa]);
        $idH = $stmtIDH->fetch(PDO::FETCH_ASSOC)['id_historial'];

        $sqlDesocupat = "UPDATE historial SET hora_fin = NOW() WHERE id_mesa = ? AND id_historial = ?";
        $stmtDesocupat = $conn->prepare($sqlDesocupat);
        $stmtDesocupat->execute([$idMesa, $idH]);

        $conn->commit();
        $_SESSION['successDesocupat'] = true;
?>
        <form action="../view/mesa.php" method="POST" name="formulario">
            <input type="hidden" name="id_tipoSala" value="<?php echo $id_tipoSala ?>">
            <input type="hidden" name="id_sala" value="<?php echo $idSala ?>">
        </form>
        <script language="JavaScript">
            document.formulario.submit();
        </script>
<?php
    } catch (PDOException $e) {
        $conn->rollBack();
        error_log("Error en desocupar_mesa.php: " . $e->getMessage());
        header('Location: ../index.php?error=system');
        exit();
    } finally {
        $conn = null;
    }
} else {
    header('Location: ../index.php');
    exit();
}
?>