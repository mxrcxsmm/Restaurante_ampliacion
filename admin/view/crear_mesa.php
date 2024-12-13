<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Mesa</title>
    <link rel="stylesheet" href="../../css/form-styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="../validations/js/validarMesa.js"></script>
</head>
<body class="body2">
    <?php include '../../headerAdmin.php'; ?>
    
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h2>Crear Nueva Mesa</h2>
            </div>
            <div class="card-body">
                <form action="../proc/crear_mesa.php" method="POST" id="formCrearMesa">
                    <div class="form-group mb-3">
                        <label for="id_sala">Sala</label>
                        <select class="form-control" id="id_sala" name="id_sala">
                            <option value="">Seleccione una sala</option>
                            <?php foreach ($salas as $sala): ?>
                                <option value="<?php echo $sala['id_sala']; ?>">
                                    <?php echo $sala['nombre_sala']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="num_sillas">Número de Sillas</label>
                        <input type="number" class="form-control" id="num_sillas" 
                               name="num_sillas" min="2" max="12"
                               placeholder="Número de sillas (2-12)">
                    </div>

                    <div class="buttons-container">
                        <button type="submit" class="btn btn-success">Crear Mesa</button>
                        <a href="../admin.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <?php if (isset($_SESSION['success'])): ?>
    <script>
        Swal.fire({
            title: '¡Éxito!',
            text: 'La mesa ha sido creada correctamente',
            icon: 'success',
            confirmButtonText: 'Aceptar',
            confirmButtonColor: '#28a745'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '../admin.php';
            }
        });
    </script>
    <?php 
        unset($_SESSION['success']);
    endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
    <script>
        Swal.fire({
            title: 'Error',
            text: '<?php echo $_SESSION['error']; ?>',
            icon: 'error',
            confirmButtonText: 'Aceptar',
            confirmButtonColor: '#dc3545'
        });
    </script>
    <?php 
        unset($_SESSION['error']);
    endif; ?>

</body>
</html> 