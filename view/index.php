<?php
session_start();
include '../procesos/conexion.php';

if (!isset($_SESSION['id_camarero'])) {
    header("Location: ../index.php");
    exit();
}

try {
    // Consulta para obtener el nombre del usuario autenticado
    $query = "SELECT * FROM tipo_sala";
    $stmt = $conn->query($query);
    
    if(isset($_SESSION['success']) && $_SESSION['success']){
        unset($_SESSION['success']);
        $user = htmlspecialchars($_SESSION['usuario']);
        echo "<script>let loginSuccess = true; let user='$user';</script>";
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido <?php echo htmlspecialchars($_SESSION['nombre']); ?>!</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.min.css" integrity="sha256-qWVM38RAVYHA4W8TAlDdszO1hRaAq0ME7y2e9aab354=" crossorigin="anonymous">
</head>

<body class="body2">
    <?php include '../header.php' ?>
    <div class="container">
        <h1 class="bienvenido">Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?>!</h1>
        <h3>Selecciona una sala:</h3>
        <div class="row">
            <?php
            while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<div class='col-md-4 mb-4'>";
                echo "<div class='container_img grow'>";
                switch($fila['id_tipoSala']){
                    case '1':
                        ?>
                        <form class="formImg" action="tipoSala.php" method="post">
                            <input type="hidden" name="id_tipoSala" value="<?php echo htmlspecialchars($fila['id_tipoSala'])?>">
                            <button class="botonImg" type="submit"><img src="../img/terraza 1.webp" alt=""></button>
                        </form>
                        <?php
                        break;
                    case '2':
                        ?>
                        <form class="formImg" action="tipoSala.php" method="post">
                            <input type="hidden" name="id_tipoSala" value="<?php echo htmlspecialchars($fila['id_tipoSala'])?>">
                            <button class="botonImg" type="submit"><img src="../img/comedor1.webp" alt=""></button>
                        </form>
                        <?php
                        break;
                    case '3':
                        ?>
                        <form class="formImg" action="tipoSala.php" method="post">
                            <input type="hidden" name="id_tipoSala" value="<?php echo htmlspecialchars($fila['id_tipoSala'])?>">
                            <button class="botonImg" type="submit"><img src="../img/salapriv.png" alt=""></button>
                        </form>
                        <?php
                        break;
                }
                echo "</div>";
                echo "<label class='labelTipo'>" . htmlspecialchars($fila['tipo_sala']) . "</label>";
                echo "</div>";
            }
            ?>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js" integrity="sha256-1m4qVbsdcSU19tulVTbeQReg0BjZiW6yGffnlr/NJu4=" crossorigin="anonymous"></script>
    <script>
        if(typeof loginSuccess !== 'undefined' && loginSuccess){
            Swal.fire({
                title: 'Sesion iniciada',
                text: "Bienvenido " + user + "!",
                icon: 'success'
            });
        }
    </script>
    <footer></footer>
</body>
</html>
<?php
} catch(PDOException $e) {
    error_log("Error en index.php: " . $e->getMessage());
    header('Location: ../index.php?error=system');
    exit();
} finally {
    $conn = null;
}
?>