<?php
session_start();
include_once '../procesos/conexion.php';

// Función auxiliar para determinar la imagen correcta
function determinarImagenMesa($numSillas, $ocupada) {
    if ($numSillas <= 2) return $ocupada . "2";
    if ($numSillas <= 4) return $ocupada . "4";
    if ($numSillas <= 6) return $ocupada . "6";
    if ($numSillas <= 8) return $ocupada . "8";
    return $ocupada . "10";
}

if (!isset($_SESSION['id_camarero'])) {
    header('location:../index.php');
    exit();
}

if (!isset($_POST['id_tipoSala'])) {
    header('Location: ./index.php');
    exit();
} 

try {
    $id = htmlspecialchars(trim($_POST['id_tipoSala']), ENT_QUOTES, 'UTF-8');
    $id_sala = htmlspecialchars(trim($_POST['id_sala']), ENT_QUOTES, 'UTF-8');
    
    $query = "SELECT m.*, 
              COALESCE(h.hora_reserva_fin, '00:00:00') as hora_reserva_fin,
              COALESCE(h.hora_reserva_inicio, '00:00:00') as hora_reserva_inicio
              FROM mesa m 
              LEFT JOIN historial h ON m.id_mesa = h.id_mesa 
                AND h.hora_fin = '0000-00-00 00:00:00'
              WHERE m.id_sala = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id_sala]);
    
    if (isset($_SESSION['successOcupat']) && $_SESSION['successOcupat']) {
        unset($_SESSION['successOcupat']);
        echo "<script>let ocupat = true;</script>";
    }
    if (isset($_SESSION['successDesocupat']) && $_SESSION['successDesocupat']) {
        unset($_SESSION['successDesocupat']);
        echo "<script>let desocupat = true;</script>";
    }
    if (isset($_SESSION['errorStock'])) {
        echo "<script>let errorStock = true;</script>";
        unset($_SESSION['errorStock']);
    }
    if (isset($_SESSION['error'])) {
        echo "<script>let errorMessage = '" . htmlspecialchars($_SESSION['error']) . "';</script>";
        unset($_SESSION['error']);
    }
    
    $numero = $stmt->rowCount();
    $nuevoNumero = 4;
    switch ($numero) {
        case 4:
            $nuevoNumero = 5;
            break;
        case 5:
            $nuevoNumero = 4;
            break;
        case 6:
            $nuevoNumero = 4;
            break;
    }
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../css/imagenes.css">
        <link rel="stylesheet" href="../css/style.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <title>Selecciona una mesa</title>
    </head>

    <body class="body2">
        <?php include '../header.php'; ?>
        <div id="divMesa">
            <a href="../view/index.php">
                <button class="btn btn-danger">Volver</button>
            </a>
            <h1 id="centrar">Selecciona una ubicación!</h1>
            <p></p>
        </div>

        <div class="container">
            <div class="row">
                <?php
                while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<div class='col-md-$nuevoNumero mb-4'>";
                    echo "<div class='container_img'>";
                    
                    // Determinar si la mesa está realmente ocupada en este momento
                    $horaActual = date('H:i:s');
                    $estaOcupada = $fila['libre'] == 1 && 
                                  $horaActual >= $fila['hora_reserva_inicio'] && 
                                  $horaActual <= $fila['hora_reserva_fin'];
                    
                    if (!$estaOcupada) {
                        // Formulario para ocupar mesa
                        ?>
                        <form class="formImgComedor" action="../procesos/ocupar_mesa.php" method="POST">
                            <input type="hidden" name="id_tipoSala" value="<?php echo htmlspecialchars($id) ?>">
                            <input type="hidden" name="id_mesa" value="<?php echo htmlspecialchars($fila['id_mesa']) ?>">
                            <input type="hidden" name="id_sala" value="<?php echo htmlspecialchars($fila['id_sala']) ?>">
                            <input type="hidden" name="num_sillas_real" value="<?php echo htmlspecialchars($fila['num_sillas']) ?>">
                            <input type="hidden" name="num_sillas" value="<?php echo htmlspecialchars($fila['num_sillas']) ?>">
                            <button class="botonImg" type="button" onclick="confirmAction(this.form)">
                                <img class="imagen" src="../img/<?php echo determinarImagenMesa($fila['num_sillas'], 0); ?>.png" alt="">
                            </button>
                        </form>
                        <?php
                    } else {
                        // Formulario para desocupar mesa
                        ?>
                        <form class="formImgComedor" action="../procesos/desocupar_mesa.php" method="POST">
                            <input type="hidden" name="id_tipoSala" value="<?php echo htmlspecialchars($id) ?>">
                            <input type="hidden" name="id_mesa" value="<?php echo htmlspecialchars($fila['id_mesa']) ?>">
                            <input type="hidden" name="id_sala" value="<?php echo htmlspecialchars($fila['id_sala']) ?>">
                            <input type="hidden" name="num_sillas_real" value="<?php echo htmlspecialchars($fila['num_sillas']) ?>">
                            <input type="hidden" name="num_sillas" value="<?php echo htmlspecialchars($fila['num_sillas']) ?>">
                            <button class="botonImg" type="button" onclick="desocupar(this.form)">
                                <img class="imagen" src="../img/<?php echo determinarImagenMesa($fila['num_sillas'], 1); ?>.png" alt="">
                            </button>
                        </form>
                        <?php
                    }
                    echo "</div>";
                    echo "<label class='labelTipo'> Nº Sillas: " . htmlspecialchars($fila['num_sillas']) . "</label>";
                    if ($estaOcupada) {
                        echo "<br><small>Reservada hasta: " . htmlspecialchars($fila['hora_reserva_fin']) . "</small>";
                    }
                    echo "</div>";
                }
                ?>
            </div>
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js" integrity="sha256-1m4qVbsdcSU19tulVTbeQReg0BjZiW6yGffnlr/NJu4=" crossorigin="anonymous"></script>
        <script>
            function confirmAction(form) {
                Swal.fire({
                    title: 'Reservar Mesa',
                    html: `
                        <div class="form-group">
                            <label>Número de sillas:</label>
                            <input type="number" id="sillas" class="swal2-input" value="${form.num_sillas.value}" min="2" max="10">
                        </div>
                        <div class="form-group">
                            <label>Hora de inicio:</label>
                            <select id="hora_inicio" class="swal2-input">
                                ${generateHourOptions(8, 23)}
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Hora de fin:</label>
                            <select id="hora_fin" class="swal2-input">
                                ${generateHourOptions(9, 24)}
                            </select>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Reservar',
                    cancelButtonText: 'Cancelar',
                    preConfirm: () => {
                        const sillas = document.getElementById('sillas').value;
                        const horaInicio = document.getElementById('hora_inicio').value;
                        const horaFin = document.getElementById('hora_fin').value;

                        if (sillas < 2 || sillas > 10) {
                            Swal.showValidationMessage('El número de sillas debe estar entre 2 y 10');
                            return false;
                        }

                        if (horaInicio >= horaFin) {
                            Swal.showValidationMessage('La hora de fin debe ser posterior a la hora de inicio');
                            return false;
                        }

                        return {
                            sillas: sillas,
                            horaInicio: horaInicio,
                            horaFin: horaFin
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.num_sillas.value = result.value.sillas;
                        
                        // Agregar campos ocultos para las horas
                        const horaInicioInput = document.createElement('input');
                        horaInicioInput.type = 'hidden';
                        horaInicioInput.name = 'hora_inicio';
                        horaInicioInput.value = result.value.horaInicio;
                        form.appendChild(horaInicioInput);

                        const horaFinInput = document.createElement('input');
                        horaFinInput.type = 'hidden';
                        horaFinInput.name = 'hora_fin';
                        horaFinInput.value = result.value.horaFin;
                        form.appendChild(horaFinInput);

                        form.submit();
                    }
                });
            }

            function generateHourOptions(start, end) {
                let options = '';
                for (let i = start; i <= end; i++) {
                    const hour = i.toString().padStart(2, '0') + ':00';
                    options += `<option value="${hour}">${hour}</option>`;
                }
                return options;
            }

            function desocupar(form) {
                Swal.fire({
                    title: "Estás seguro de desocupar la mesa para " + form.num_sillas.value + "?",
                    text: "cambia el número de sillas aqui: ",
                    icon: "warning",
                    input: "text",
                    inputValue: form.num_sillas.value < 4 ? form.num_sillas.value : 2,
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Confirmar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (result.value <= 10 && result.value >= 2) {
                            form.num_sillas.value = result.value;
                            form.submit();
                        } else {
                            Swal.fire({
                                title: "Rango no aceptado",
                                text: "Solo puedes pedir de 2 a 10",
                                icon: "warning",
                                confirmButtonText: "Aceptar"
                            });
                        }
                    }
                });
            }
            if (typeof errorStock !== 'undefined' && errorStock) {
                Swal.fire({
                    title: "No disponemos de sillas!",
                    text: "En este momento no contamos con tantas sillas",
                    icon: "error",
                    confirmButtonText: "Aceptar"
                });
            }
            if (typeof ocupat !== "undefined" && ocupat) {
                Swal.fire({
                    title: "Mesa Ocupada!",
                    text: "La mesa ha sido ocupada exitosamente!",
                    icon: "success",
                    confirmButtonText: "Aceptar"
                });
            }
            if (typeof desocupat !== "undefined" && desocupat) {
                Swal.fire({
                    title: "Mesa Desocupada!",
                    text: "La mesa ha sido desocupada exitosamente!",
                    icon: "success",
                    confirmButtonText: "Aceptar"
                });
            }
            if (typeof errorMessage !== 'undefined') {
                Swal.fire({
                    title: "Error",
                    text: errorMessage,
                    icon: "error",
                    confirmButtonText: "Aceptar"
                });
            }
        </script>
        <footer></footer>
    </body>
    </html>
<?php
} catch(PDOException $e) {
    error_log("Error en mesa.php: " . $e->getMessage());
    header('Location: ../index.php?error=system');
    exit();
} finally {
    $conn = null;
}
?>