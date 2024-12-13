<?php
session_start();
require_once '../../procesos/conexion.php';

// Verificar autenticación y rol
if (!isset($_SESSION['id_camarero']) || $_SESSION['rol'] !== 'Administrador') {
    header('Location: ../../index.php');
    exit();
}

// Obtener tipos de sala para el formulario
try {
    $tipos_sala = $conn->query("SELECT * FROM tipo_sala")->fetchAll();
} catch (PDOException $e) {
    $_SESSION['error'] = "Error al cargar los tipos de sala";
    header('Location: ../admin.php');
    exit();
}

// Si viene un ID por GET, cargar los datos de la sala
if (isset($_GET['id_sala'])) {
    try {
        $stmt = $conn->prepare("SELECT s.*, t.tipo_sala 
                               FROM sala s 
                               INNER JOIN tipo_sala t ON s.id_tipoSala = t.id_tipoSala 
                               WHERE s.id_sala = :id");
        $stmt->execute([':id' => $_GET['id_sala']]);
        $sala = $stmt->fetch();

        if (!$sala) {
            $_SESSION['error'] = "Sala no encontrada";
            header('Location: ../admin.php');
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al cargar los datos de la sala";
        header('Location: ../admin.php');
        exit();
    }
}

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $sql = "UPDATE sala SET 
                nombre_sala = :nombre,
                id_tipoSala = :tipo
                WHERE id_sala = :id";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':nombre' => $_POST['nombre_sala'],
            ':tipo' => $_POST['id_tipoSala'],
            ':id' => $_POST['id_sala']
        ]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['success'] = true;
            $_SESSION['message'] = "Sala actualizada correctamente";
            header('Location: ../../admin/admin.php');
            exit();
        } else {
            throw new Exception("No se realizaron cambios en la sala");
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al actualizar la sala: " . $e->getMessage();
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}

// Cargar la vista
require_once '../view/editar_sala.php';
?> 