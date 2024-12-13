<?php
session_start();
require_once '../../procesos/conexion.php';

// Verificar autenticación y rol
if (!isset($_SESSION['id_camarero']) || $_SESSION['rol'] !== 'Administrador') {
    header('Location: ../../index.php');
    exit();
}

// Obtener salas para el formulario
try {
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
} catch (PDOException $e) {
    $_SESSION['error'] = "Error al cargar las salas";
    header('Location: ../admin.php');
    exit();
}

// Si viene un ID por GET, cargar los datos de la mesa
if (isset($_GET['id_mesa'])) {
    try {
        $stmt = $conn->prepare("SELECT m.*, s.nombre_sala, s.id_tipoSala 
                               FROM mesa m 
                               INNER JOIN sala s ON m.id_sala = s.id_sala 
                               WHERE m.id_mesa = :id");
        $stmt->execute([':id' => $_GET['id_mesa']]);
        $mesa = $stmt->fetch();

        if (!$mesa) {
            $_SESSION['error'] = "Mesa no encontrada";
            header('Location: ../admin.php');
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al cargar los datos de la mesa";
        header('Location: ../admin.php');
        exit();
    }
}

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validar número de sillas
        if (!is_numeric($_POST['num_sillas']) || $_POST['num_sillas'] < 1) {
            throw new Exception("El número de sillas debe ser un valor válido");
        }

        $sql = "UPDATE mesa SET 
                id_sala = :sala,
                num_sillas = :sillas
                WHERE id_mesa = :id";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':sala' => $_POST['id_sala'],
            ':sillas' => $_POST['num_sillas'],
            ':id' => $_POST['id_mesa']
        ]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['success'] = true;
            $_SESSION['message'] = "Mesa actualizada correctamente";
            header('Location: ../../admin/admin.php');
            exit();
        } else {
            throw new Exception("No se realizaron cambios en la mesa");
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al actualizar la mesa: " . $e->getMessage();
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}

// Cargar la vista
require_once '../view/editar_mesa.php';
?> 