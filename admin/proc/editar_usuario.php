<?php
session_start();
require_once '../../procesos/conexion.php';

// Verificar autenticación y rol
if (!isset($_SESSION['id_camarero']) || $_SESSION['rol'] !== 'Administrador') {
    header('Location: ../../index.php');
    exit();
}

// Obtener roles para el formulario
try {
    $roles = $conn->query("SELECT * FROM roles")->fetchAll();
} catch (PDOException $e) {
    $_SESSION['error'] = "Error al cargar los roles";
    header('Location: ../../admin/admin.php');
    exit();
}

// Si viene un ID por GET, cargar los datos del usuario
if (isset($_GET['id_camarero'])) {
    try {
        $stmt = $conn->prepare("SELECT * FROM camarero WHERE id_camarero = :id");
        $stmt->execute([':id' => $_GET['id_camarero']]);
        $usuario = $stmt->fetch();

        if (!$usuario) {
            $_SESSION['error'] = "Usuario no encontrado";
            header('Location: ../../admin/admin.php');
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al cargar los datos del usuario";
        header('Location: ../../admin/admin.php');
        exit();
    }
}

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Si se proporcionó una nueva contraseña
        if (!empty($_POST['password'])) {
            $sql = "UPDATE camarero SET 
                    nombre = :nombre, 
                    usuario = :usuario, 
                    password = :password,
                    id_roles = :rol 
                    WHERE id_camarero = :id";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':nombre' => $_POST['nombre'],
                ':usuario' => $_POST['usuario'],
                ':password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
                ':rol' => $_POST['rol'],
                ':id' => $_POST['id_camarero']
            ]);
        } else {
            // Si no se cambió la contraseña
            $sql = "UPDATE camarero SET 
                    nombre = :nombre, 
                    usuario = :usuario, 
                    id_roles = :rol 
                    WHERE id_camarero = :id";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':nombre' => $_POST['nombre'],
                ':usuario' => $_POST['usuario'],
                ':rol' => $_POST['rol'],
                ':id' => $_POST['id_camarero']
            ]);
        }

        $_SESSION['success'] = true;
        $_SESSION['message'] = "Usuario actualizado correctamente";
        header('Location: ../../admin/admin.php');
        exit();

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al actualizar el usuario: " . $e->getMessage();
    }
}

// Cargar la vista
require_once '../view/editar_usuario.php';
?> 