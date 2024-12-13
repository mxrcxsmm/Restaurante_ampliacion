<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Usuario</title>
    <link rel="stylesheet" href="../../css/form-styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="../validations/js/validarUsuario.js"></script>
</head>

<body class="body2">
    <?php include '../../headerAdmin.php'; ?>

    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h2>Crear Nuevo Usuario</h2>
            </div>
            <div class="card-body">
                <form action="../proc/crear_usuario.php" method="POST" id="formCrearUsuario">
                    <div class="form-group mb-3">
                        <label for="nombre">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre">
                    </div>

                    <div class="form-group mb-3">
                        <label for="usuario">Usuario</label>
                        <input type="text" class="form-control" id="usuario" name="usuario">
                    </div>

                    <div class="form-group mb-3">
                        <label for="password">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>

                    <div class="form-group mb-3">
                        <label for="rol">Rol</label>
                        <select class="form-control" id="rol" name="rol">
                            <?php foreach ($roles as $rol): ?>
                                <option value="<?php echo $rol['id_roles']; ?>">
                                    <?php echo $rol['tipo_roles']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="buttons-container">
                        <button type="submit" class="btn btn-success">Crear Usuario</button>
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
                text: 'El usuario ha sido creado correctamente',
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