<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Sala</title>
    <link rel="stylesheet" href="../../css/form-styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<body class="body2">
    <?php include '../../headerAdmin.php'; ?>
    
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h2>Editar Sala</h2>
            </div>
            <div class="card-body">
                <form action="../proc/editar_sala.php" method="POST">
                    <input type="hidden" name="id_sala" value="<?php echo $sala['id_sala']; ?>">

                    <div class="form-group mb-3">
                        <label for="nombre_sala">Nombre de la Sala</label>
                        <input type="text" class="form-control" id="nombre_sala" name="nombre_sala" 
                               value="<?php echo $sala['nombre_sala']; ?>" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="id_tipoSala">Tipo de Sala</label>
                        <select class="form-control" id="id_tipoSala" name="id_tipoSala" required>
                            <?php foreach ($tipos_sala as $tipo): ?>
                                <option value="<?php echo $tipo['id_tipoSala']; ?>" 
                                    <?php echo ($tipo['id_tipoSala'] == $sala['id_tipoSala']) ? 'selected' : ''; ?>>
                                    <?php echo $tipo['tipo_sala']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
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