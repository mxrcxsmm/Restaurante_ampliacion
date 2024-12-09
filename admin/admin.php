<?php
session_start();
require_once '../procesos/conexion.php';

// Verificar si el usuario está autenticado y es administrador
if (!isset($_SESSION['id_camarero']) || $_SESSION['rol'] !== 'Administrador') {
    header('Location: ../index.php');
    exit();
}

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Gestión de Usuarios
        if (isset($_POST['action_user'])) {
            switch ($_POST['action_user']) {
                case 'create':
                    $sql = "INSERT INTO camarero (nombre, usuario, password, id_roles) 
                            VALUES (:nombre, :usuario, :password, :rol)";
                    $stmt = $conn->prepare($sql);
                    $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $stmt->execute([
                        ':nombre' => $_POST['nombre'],
                        ':usuario' => $_POST['usuario'],
                        ':password' => $hashedPassword,
                        ':rol' => $_POST['rol']
                    ]);
                    break;

                case 'delete':
                    $sql = "DELETE FROM camarero WHERE id_camarero = :id";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([':id' => $_POST['id_camarero']]);
                    break;
            }
        }

        // Gestión de Recursos
        if (isset($_POST['action_resource'])) {
            switch ($_POST['action_resource']) {
                case 'create_sala':
                    $sql = "INSERT INTO sala (id_tipoSala, nombre_sala) 
                            VALUES (:tipo, :nombre)";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([
                        ':tipo' => $_POST['tipo_sala'],
                        ':nombre' => $_POST['nombre_sala']
                    ]);
                    break;

                case 'create_mesa':
                    $sql = "INSERT INTO mesa (id_sala, libre, num_sillas) 
                            VALUES (:sala, :libre, :sillas)";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([
                        ':sala' => $_POST['id_sala'],
                        ':libre' => 0,
                        ':sillas' => $_POST['num_sillas']
                    ]);
                    break;
            }
        }
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Obtener datos para las tablas
$usuarios = $conn->query("SELECT c.*, r.tipo_roles FROM camarero c 
                         INNER JOIN roles r ON c.id_roles = r.id_roles")->fetchAll();
$salas = $conn->query("SELECT s.*, t.tipo_sala FROM sala s 
                       INNER JOIN tipo_sala t ON s.id_tipoSala = t.id_tipoSala")->fetchAll();
$mesas = $conn->query("SELECT m.*, s.nombre_sala FROM mesa m 
                       INNER JOIN sala s ON m.id_sala = s.id_sala")->fetchAll();
$roles = $conn->query("SELECT * FROM roles")->fetchAll();
$tiposSala = $conn->query("SELECT * FROM tipo_sala")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/form-styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="body2">
    <?php include '../header.php'; ?>
    
    <div class="container mt-4">
        <h1>Panel de Administración</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Gestión de Usuarios -->
        <div class="card mt-4">
            <div class="card-header">
                <h2>Gestión de Usuarios</h2>
            </div>
            <div class="card-body">
                <!-- Formulario para crear usuario -->
                <form method="POST" class="mb-4">
                    <input type="hidden" name="action_user" value="create">
                    <div class="row">
                        <div class="col-md-3">
                            <input type="text" name="nombre" class="form-control" placeholder="Nombre" required>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="usuario" class="form-control" placeholder="Usuario" required>
                        </div>
                        <div class="col-md-2">
                            <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
                        </div>
                        <div class="col-md-2">
                            <select name="rol" class="form-control" required>
                                <?php foreach ($roles as $rol): ?>
                                    <option value="<?php echo $rol['id_roles']; ?>">
                                        <?php echo $rol['tipo_roles']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">Crear Usuario</button>
                        </div>
                    </div>
                </form>

                <!-- Tabla de usuarios -->
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?php echo $usuario['id_camarero']; ?></td>
                                <td><?php echo $usuario['nombre']; ?></td>
                                <td><?php echo $usuario['usuario']; ?></td>
                                <td><?php echo $usuario['tipo_roles']; ?></td>
                                <td>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action_user" value="delete">
                                        <input type="hidden" name="id_camarero" value="<?php echo $usuario['id_camarero']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" 
                                                onclick="return confirm('¿Estás seguro?')">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Gestión de Recursos -->
        <div class="card mt-4">
            <div class="card-header">
                <h2>Gestión de Recursos</h2>
            </div>
            <div class="card-body">
                <!-- Formulario para crear sala -->
                <form method="POST" class="mb-4">
                    <input type="hidden" name="action_resource" value="create_sala">
                    <div class="row">
                        <div class="col-md-4">
                            <select name="tipo_sala" class="form-control" required>
                                <?php foreach ($tiposSala as $tipo): ?>
                                    <option value="<?php echo $tipo['id_tipoSala']; ?>">
                                        <?php echo $tipo['tipo_sala']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="nombre_sala" class="form-control" placeholder="Nombre de la sala" required>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">Crear Sala</button>
                        </div>
                    </div>
                </form>

                <!-- Formulario para crear mesa -->
                <form method="POST" class="mb-4">
                    <input type="hidden" name="action_resource" value="create_mesa">
                    <div class="row">
                        <div class="col-md-4">
                            <select name="id_sala" class="form-control" required>
                                <?php foreach ($salas as $sala): ?>
                                    <option value="<?php echo $sala['id_sala']; ?>">
                                        <?php echo $sala['nombre_sala']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="number" name="num_sillas" class="form-control" 
                                   placeholder="Número de sillas" min="2" max="10" required>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">Crear Mesa</button>
                        </div>
                    </div>
                </form>

                <!-- Tabla de salas -->
                <h3>Salas</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Tipo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($salas as $sala): ?>
                            <tr>
                                <td><?php echo $sala['id_sala']; ?></td>
                                <td><?php echo $sala['nombre_sala']; ?></td>
                                <td><?php echo $sala['tipo_sala']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Tabla de mesas -->
                <h3>Mesas</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Sala</th>
                            <th>Sillas</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mesas as $mesa): ?>
                            <tr>
                                <td><?php echo $mesa['id_mesa']; ?></td>
                                <td><?php echo $mesa['nombre_sala']; ?></td>
                                <td><?php echo $mesa['num_sillas']; ?></td>
                                <td><?php echo $mesa['libre'] ? 'Libre' : 'Ocupada'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
