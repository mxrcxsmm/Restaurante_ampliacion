<?php
session_start();
require_once '../procesos/conexion.php';

// Verificar si el usuario está autenticado y es administrador
if (!isset($_SESSION['id_camarero']) || $_SESSION['rol'] !== 'Administrador') {
    header('Location: ../index.php');
    exit();
}

// Obtener datos para las tablas ordenados por ID de forma ascendente
$usuarios = $conn->query("SELECT c.*, r.tipo_roles FROM camarero c 
                         INNER JOIN roles r ON c.id_roles = r.id_roles
                         ORDER BY c.id_camarero ASC")->fetchAll();
$salas = $conn->query("SELECT s.*, t.tipo_sala 
                       FROM sala s 
                       INNER JOIN tipo_sala t ON s.id_tipoSala = t.id_tipoSala 
                       ORDER BY 
                           CASE 
                               WHEN t.tipo_sala = 'Terraza' THEN 1
                               WHEN t.tipo_sala = 'Comedor' THEN 2
                               WHEN t.tipo_sala = 'Sala Privada' THEN 3
                               ELSE 4 
                           END,
                           s.id_sala ASC")->fetchAll();
$mesas = $conn->query("SELECT m.*, s.nombre_sala FROM mesa m 
                       INNER JOIN sala s ON m.id_sala = s.id_sala
                       ORDER BY m.id_mesa ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="../css/form-styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<body class="body2">
    <?php include '../headerAdmin.php'; ?>
    
    <div class="container mt-4">
        <h1 class="text-center mb-4">Panel de Administración</h1>

        <!-- Gestión de Usuarios -->
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2>Gestión de Usuarios</h2>
                <a href="proc/crear_usuario.php" class="btn btn-success btn-create">Nuevo Usuario</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nombre</th>
                                <th>Usuario</th>
                                <th>Rol</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td><?php echo $usuario['nombre']; ?></td>
                                    <td><?php echo $usuario['usuario']; ?></td>
                                    <td><?php echo $usuario['tipo_roles']; ?></td>
                                    <td>
                                        <a href="proc/editar_usuario.php?id_camarero=<?php echo $usuario['id_camarero']; ?>" 
                                           class="btn btn-primary btn-sm">Editar</a>
                                        <button onclick="confirmarEliminarUsuario(<?php echo $usuario['id_camarero']; ?>)" 
                                                class="btn btn-danger btn-sm">Eliminar</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Gestión de Salas -->
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2>Gestión de Salas</h2>
                <a href="proc/crear_sala.php" class="btn btn-success">Nueva Sala</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nombre</th>
                                <th>Tipo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($salas as $sala): ?>
                                <tr>
                                    <td><?php echo $sala['nombre_sala']; ?></td>
                                    <td><?php echo $sala['tipo_sala']; ?></td>
                                    <td>
                                        <a href="proc/editar_sala.php?id_sala=<?php echo $sala['id_sala']; ?>" 
                                           class="btn btn-primary btn-sm">Editar</a>
                                        <button onclick="confirmarEliminarSala(<?php echo $sala['id_sala']; ?>)" 
                                                class="btn btn-danger btn-sm">Eliminar</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Gestión de Mesas -->
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2>Gestión de Mesas</h2>
                <a href="proc/crear_mesa.php" class="btn btn-success">Nueva Mesa</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Num Mesa</th>
                                <th>Sala</th>
                                <th>Sillas</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($mesas as $mesa): ?>
                                <tr>
                                    <td><?php echo $mesa['id_mesa']; ?></td>
                                    <td><?php echo $mesa['nombre_sala']; ?></td>
                                    <td><?php echo $mesa['num_sillas']; ?></td>
                                    <td>
                                        <span class="badge bg-success">Libre</span>
                                    </td>
                                    <td>
                                        <a href="proc/editar_mesa.php?id_mesa=<?php echo $mesa['id_mesa']; ?>" 
                                           class="btn btn-primary btn-sm">Editar</a>
                                        <button onclick="confirmarEliminarMesa(<?php echo $mesa['id_mesa']; ?>)" 
                                                class="btn btn-danger btn-sm">Eliminar</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
    function confirmarEliminarUsuario(id_camarero) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: '¿Deseas eliminar este usuario? Se eliminarán también todas sus reservas asociadas',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `proc/eliminar_usuario.php?id_camarero=${id_camarero}`;
            }
        });
    }

    function confirmarEliminarSala(id_sala) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: '¿Deseas eliminar esta sala? Se eliminarán también todas las mesas asociadas',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `proc/eliminar_sala.php?id_sala=${id_sala}`;
            }
        });
    }

    function confirmarEliminarMesa(id_mesa) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: '¿Deseas eliminar esta mesa? Se eliminarán también todas las reservas asociadas',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `proc/eliminar_mesa.php?id_mesa=${id_mesa}`;
            }
        });
    }
    </script>

    <?php if (isset($_SESSION['success'])): ?>
    <script>
        Swal.fire({
            title: '¡Éxito!',
            text: '<?php echo $_SESSION['message']; ?>',
            icon: 'success',
            confirmButtonText: 'Aceptar',
            confirmButtonColor: '#28a745'
        });
    </script>
    <?php 
        unset($_SESSION['success']);
        unset($_SESSION['message']);
    endif; ?>

</body>
</html>
