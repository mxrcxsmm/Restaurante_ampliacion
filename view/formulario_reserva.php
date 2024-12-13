<?php
session_start();
include_once '../procesos/conexion.php';

if (!isset($_SESSION['id_camarero']) || !isset($_GET['id_mesa'])) {
    $_SESSION['error'] = "No tienes permisos para acceder a esta página";
    header('Location: ../index.php');
    exit();
}

$id_mesa = htmlspecialchars($_GET['id_mesa']);

// Obtener las reservas existentes para esta mesa
try {
    $sql = "SELECT fecha_reserva, hora_reserva_inicio, hora_reserva_fin 
            FROM reservas 
            WHERE id_mesa = :id_mesa 
            AND fecha_reserva >= CURRENT_DATE";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_mesa', $id_mesa, PDO::PARAM_INT);
    $stmt->execute();
    $reservas_existentes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error al obtener reservas: " . $e->getMessage());
    $reservas_existentes = [];
}

$error_msg = isset($_SESSION['error']) ? $_SESSION['error'] : '';
$success_msg = isset($_SESSION['success']) ? $_SESSION['success'] : '';
unset($_SESSION['error']);
unset($_SESSION['success']);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservar Mesa</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/reservas.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
</head>

<body class="body2">
    <?php include '../header.php'; ?>

    <div class="container mt-5">
        <div class="form-header">
            <h2>Reservar Mesa <?php echo $id_mesa; ?></h2>
            <p class="text-muted">Camarero: <?php echo $_SESSION['nombre']; ?></p>
        </div>
        
        <form action="../procesos/procesar_reserva.php" method="POST" class="reservation-form" id="reservaForm">
            <input type="hidden" name="id_mesa" value="<?php echo $id_mesa; ?>">
            <input type="hidden" name="id_camarero" value="<?php echo $_SESSION['id_camarero']; ?>">
            
            <div class="form-group">
                <label for="nombre_cliente" class="form-label">Nombre del Cliente</label>
                <input type="text" class="form-control" id="nombre_cliente" name="nombre_cliente">
            </div>

            <div class="form-group">
                <label for="fecha_reserva" class="form-label">Fecha de Reserva</label>
                <input type="date" class="form-control" id="fecha_reserva" name="fecha_reserva" 
                       min="<?php echo date('Y-m-d'); ?>">
            </div>

            <div class="form-group">
                <label for="hora_inicio" class="form-label">Hora de Inicio</label>
                <select class="form-select" id="hora_inicio" name="hora_reserva_inicio">
                    <option value="">Seleccione hora de inicio</option>
                </select>
            </div>

            <div class="form-group">
                <label for="hora_fin" class="form-label">Hora de Fin</label>
                <select class="form-select" id="hora_fin" name="hora_reserva_fin">
                    <option value="">Seleccione hora de fin</option>
                </select>
            </div>

            <div class="buttons-container">
                <button type="submit" class="btn btn-success">Confirmar Reserva</button>
                
            </div>
        </form>
    <a href="./mesa.php" class="btn btn-secondary">Cancelar</a>
</div>




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    <script src="../validations/js/validacionFormularioReservas.js"></script>
    <script>
        // Almacenar las reservas existentes
        const reservasExistentes = <?php echo json_encode($reservas_existentes); ?>;

        document.getElementById('fecha_reserva').addEventListener('change', actualizarHorasDisponibles);
        document.getElementById('hora_inicio').addEventListener('change', actualizarHorasFin);

        function actualizarHorasDisponibles() {
            const fechaSeleccionada = document.getElementById('fecha_reserva').value;
            const horaActual = new Date().getHours();
            const fechaActual = new Date().toISOString().split('T')[0];
            const selectHoraInicio = document.getElementById('hora_inicio');

            selectHoraInicio.innerHTML = '<option value="">Seleccione hora de inicio</option>';

            const horasOcupadas = reservasExistentes
                .filter(r => r.fecha_reserva === fechaSeleccionada)
                .map(r => ({
                    inicio: parseInt(r.hora_reserva_inicio),
                    fin: parseInt(r.hora_reserva_fin)
                }));

            for (let hora = 12; hora <= 23; hora++) {
                const horaOcupada = horasOcupadas.some(r =>
                    hora >= r.inicio && hora < r.fin
                );

                const esHoraPasada = fechaSeleccionada === fechaActual && hora <= horaActual;

                if (!horaOcupada && !esHoraPasada) {
                    const horaFormateada = String(hora).padStart(2, "0") + ":00";
                    const option = new Option(horaFormateada, horaFormateada);
                    selectHoraInicio.add(option);
                }
            }

            document.getElementById('hora_fin').innerHTML = '<option value="">Seleccione hora de fin</option>';
        }

        function actualizarHorasFin() {
            const horaInicio = document.getElementById('hora_inicio').value;
            const fechaSeleccionada = document.getElementById('fecha_reserva').value;
            const horaFin = document.getElementById('hora_fin');

            if (!horaInicio) {
                horaFin.innerHTML = '<option value="">Seleccione hora de fin</option>';
                return;
            }

            const [horaInicioValue] = horaInicio.split(':');
            horaFin.innerHTML = '<option value="">Seleccione hora de fin</option>';

            const reservasDelDia = reservasExistentes
                .filter(r => r.fecha_reserva === fechaSeleccionada)
                .filter(r => parseInt(r.hora_reserva_inicio) > parseInt(horaInicioValue))
                .sort((a, b) => parseInt(a.hora_reserva_inicio) - parseInt(b.hora_reserva_inicio));

            const proximaReserva = reservasDelDia[0];
            const horaMaximaPosible = parseInt(horaInicioValue) + 2;
            const horaMaximaPorReserva = proximaReserva ?
                Math.min(parseInt(proximaReserva.hora_reserva_inicio), horaMaximaPosible) :
                Math.min(24, horaMaximaPosible);

            for (let hora = parseInt(horaInicioValue) + 1; hora <= horaMaximaPorReserva; hora++) {
                const horaFormateada = String(hora).padStart(2, '0') + ':00';
                const option = new Option(horaFormateada, horaFormateada);
                horaFin.add(option);
            }
        }
    </script>
</body>

</html>