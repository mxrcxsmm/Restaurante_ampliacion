<?php
session_start();
include_once '../procesos/conexion.php';

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
    
    $query = "SELECT * FROM mesa WHERE id_sala = :id_sala";
    $stmt = $conn->prepare($query);
    $stmt->execute([':id_sala' => $id_sala]);
    
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
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
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
                <?php foreach ($result as $fila): ?>
                    <div class='col-md-<?php echo $nuevoNumero; ?> mb-4'>
                        <div class='container_img'>
                            <?php if ($fila['libre'] == 0): ?>
                                <form class="formImgComedor" action="../procesos/ocupar_mesa.php" method="POST">
                                    <input type="hidden" name="id_tipoSala" value="<?php echo $id ?>">
                                    <input type="hidden" name="id_mesa" value="<?php echo $fila['id_mesa'] ?>">
                                    <input type="hidden" name="id_sala" value="<?php echo $fila['id_sala'] ?>">
                                    <input type="hidden" name="num_sillas_real" value="<?php echo $fila['num_sillas'] ?>">
                                    <input type="hidden" name="num_sillas" value="<?php echo $fila['num_sillas'] ?>">
                                    <button class="botonImg" type="button" onclick="confirmAction(this.form)">
                                        <img class="imagen" src="../img/<?php 
                                            if ($fila['num_sillas'] <= 2) {
                                                echo $fila['libre'] . '2';
                                            } elseif ($fila['num_sillas'] <= 4) {
                                                echo $fila['libre'] . '4';
                                            } elseif ($fila['num_sillas'] <= 6) {
                                                echo $fila['libre'] . '6';
                                            } elseif ($fila['num_sillas'] <= 8) {
                                                echo $fila['libre'] . '8';
                                            } else {
                                                echo $fila['libre'] . '10';
                                            }
                                        ?>.png" alt="Mesa">
                                    </button>
                                </form>
                            <?php else: ?>
                                <form class="formImgComedor" action="../procesos/desocupar_mesa.php" method="POST">
                                    <input type="hidden" name="id_tipoSala" value="<?php echo $id ?>">
                                    <input type="hidden" name="id_mesa" value="<?php echo $fila['id_mesa'] ?>">
                                    <input type="hidden" name="id_sala" value="<?php echo $fila['id_sala'] ?>">
                                    <input type="hidden" name="num_sillas_real" value="<?php echo $fila['num_sillas'] ?>">
                                    <input type="hidden" name="num_sillas" value="<?php echo $fila['num_sillas'] ?>">
                                    <button class="botonImg" type="button" onclick="desocupar(this.form)">
                                        <img class="imagen" src="../img/<?php 
                                            if ($fila['num_sillas'] <= 2) {
                                                echo $fila['libre'] . '2';
                                            } elseif ($fila['num_sillas'] <= 4) {
                                                echo $fila['libre'] . '4';
                                            } elseif ($fila['num_sillas'] <= 6) {
                                                echo $fila['libre'] . '6';
                                            } elseif ($fila['num_sillas'] <= 8) {
                                                echo $fila['libre'] . '8';
                                            } else {
                                                echo $fila['libre'] . '10';
                                            }
                                        ?>.png" alt="Mesa">
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                        <label class='labelTipo'>Nº Sillas: <?php echo $fila['num_sillas']; ?></label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js" integrity="sha256-1m4qVbsdcSU19tulVTbeQReg0BjZiW6yGffnlr/NJu4=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js"></script>
        <script>
            function confirmAction(form) {
                Swal.fire({
                    title: 'Reservar Mesa',
                    html: `
                        <div class="form-group mb-3">
                            <label>Número de sillas:</label>
                            <input type="number" id="sillas" class="swal2-input" value="${form.num_sillas.value}" min="2" max="10">
                        </div>
                        <div class="form-group mb-3">
                            <label>Fecha:</label>
                            <input type="date" id="fecha" class="swal2-input" min="${new Date().toISOString().split('T')[0]}">
                        </div>
                        <div class="form-group mb-3">
                            <label>Hora de inicio:</label>
                            <select id="hora_inicio" class="swal2-input">
                                <option value="">Selecciona hora inicio</option>
                                ${generateHourOptions(8, 23)}
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label>Hora de fin:</label>
                            <select id="hora_fin" class="swal2-input">
                                <option value="">Selecciona hora fin</option>
                                ${generateHourOptions(9, 24)}
                            </select>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Reservar',
                    cancelButtonText: 'Cancelar',
                    preConfirm: () => {
                        const sillas = document.getElementById('sillas').value;
                        const fecha = document.getElementById('fecha').value;
                        const horaInicio = document.getElementById('hora_inicio').value;
                        const horaFin = document.getElementById('hora_fin').value;

                        if (!sillas || !fecha || !horaInicio || !horaFin) {
                            Swal.showValidationMessage('Todos los campos son obligatorios');
                            return false;
                        }

                        if (sillas < 2 || sillas > 10) {
                            Swal.showValidationMessage('El número de sillas debe estar entre 2 y 10');
                            return false;
                        }

                        if (horaInicio >= horaFin) {
                            Swal.showValidationMessage('La hora de fin debe ser posterior a la hora de inicio');
                            return false;
                        }

                        const fechaActual = new Date();
                        const fechaSeleccionada = new Date(fecha);
                        if (fechaSeleccionada < fechaActual) {
                            Swal.showValidationMessage('La fecha debe ser igual o posterior a hoy');
                            return false;
                        }

                        return {
                            sillas: sillas,
                            fecha: fecha,
                            horaInicio: horaInicio,
                            horaFin: horaFin
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        console.log('Datos a enviar:', {
                            sillas: result.value.sillas,
                            fecha: result.value.fecha,
                            horaInicio: result.value.horaInicio,
                            horaFin: result.value.horaFin
                        });

                        // Agregar campos ocultos para fecha y horas
                        const fechaInput = document.createElement('input');
                        fechaInput.type = 'hidden';
                        fechaInput.name = 'fecha_reserva';
                        fechaInput.value = result.value.fecha;
                        form.appendChild(fechaInput);

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

                        // Verificar que los campos se han añadido correctamente
                        console.log('Formulario antes de enviar:', {
                            fecha: form.fecha_reserva.value,
                            horaInicio: form.hora_inicio.value,
                            horaFin: form.hora_fin.value,
                            sillas: form.num_sillas.value
                        });

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