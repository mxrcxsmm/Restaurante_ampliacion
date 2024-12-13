<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Mesa</title>
    <link rel="stylesheet" href="../../css/form-styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<body class="body2">
    <?php include '../../headerAdmin.php'; ?>
    
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h2>Editar Mesa <?php echo $mesa['id_mesa']; ?></h2>
            </div>
            <div class="card-body">
                <form action="../proc/editar_mesa.php" method="POST">
                    <input type="hidden" name="id_mesa" value="<?php echo $mesa['id_mesa']; ?>">

                    <div class="form-group mb-3">
                        <label for="id_sala">Sala</label>
                        <select class="form-control" id="id_sala" name="id_sala" required>
                            <?php foreach ($salas as $sala): ?>
                                <option value="<?php echo $sala['id_sala']; ?>" 
                                    <?php echo ($sala['id_sala'] == $mesa['id_sala']) ? 'selected' : ''; ?>>
                                    <?php echo $sala['nombre_sala'] . ' (' . $sala['tipo_sala'] . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="num_sillas">Número de Sillas</label>
                        <input type="number" class="form-control" id="num_sillas" name="num_sillas" 
                               value="<?php echo $mesa['num_sillas']; ?>" min="1" required>
                    </div>

                    <div class="buttons-container">
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        <a href="../admin.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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