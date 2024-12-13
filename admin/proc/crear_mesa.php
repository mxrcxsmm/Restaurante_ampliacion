<?php
session_start();
require_once '../../procesos/conexion.php';
require_once '../validations/php/validarMesa.php'; // Incluir validaciones

// Verificar autenticación
if (!isset($_SESSION['id_camarero']) || $_SESSION['rol'] !== 'Administrador') {
    header('Location: ../../index.php');
    exit();
}

// Obtener salas para el formulario
try {
    $salas = $conn->query("SELECT * FROM sala")->fetchAll();
} catch (PDOException $e) {
    $_SESSION['error'] = "Error al cargar las salas: " . $e->getMessage();
    header('Location: ../admin.php');
    exit();
}

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_sala = $_POST['id_sala'];
    $num_sillas = $_POST['num_sillas'];
    $error = "";

    // Validar sala
    $error .= validaSala($id_sala);
    // Validar número de sillas
    $error .= validaNumeroSillas($num_sillas);

    if ($error) {
        $_SESSION['error'] = $error;
        header('Location: ../view/crear_mesa.php');
        exit();
    }

    // Insertar en la base de datos
    try {
        $sql = "INSERT INTO mesa (id_sala, num_sillas, libre) VALUES (:sala, :sillas, 1)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':sala' => $id_sala,
            ':sillas' => $num_sillas
        ]);

        $_SESSION['success'] = true;
        $_SESSION['message'] = "Mesa creada correctamente";
        header('Location: ../admin.php');
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al crear mesa: " . $e->getMessage();
    }
}

// Incluir la vista
require_once '../view/crear_mesa.php';
?> 