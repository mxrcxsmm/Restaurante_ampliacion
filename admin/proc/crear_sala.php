<?php
session_start();
require_once '../../procesos/conexion.php';
require_once '../validations/php/validarSala.php'; // Incluir validaciones

// Verificar autenticación
if (!isset($_SESSION['id_camarero']) || $_SESSION['rol'] !== 'Administrador') {
    header('Location: ../../index.php');
    exit();
}

// Obtener tipos de sala para el formulario
try {
    $tiposSala = $conn->query("SELECT * FROM tipo_sala")->fetchAll();
} catch (PDOException $e) {
    $_SESSION['error'] = "Error al cargar los tipos de sala: " . $e->getMessage();
    header('Location: ../admin.php');
    exit();
}

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_sala = $_POST['tipo_sala'];
    $nombre_sala = $_POST['nombre_sala'];
    $error = "";

    // Validar tipo de sala
    $error .= validaTipoSala($tipo_sala);
    // Validar nombre de sala
    $error .= validaNombreSala($nombre_sala);

    if ($error) {
        $_SESSION['error'] = $error;
        header('Location: ../view/crear_sala.php');
        exit();
    }

    // Insertar en la base de datos
    try {
        $sql = "INSERT INTO sala (nombre_sala, id_tipoSala) VALUES (:nombre, :tipo)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre_sala,
            ':tipo' => $tipo_sala
        ]);

        $_SESSION['success'] = true;
        header('Location: ../view/crear_sala.php');
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al crear la sala: " . $e->getMessage();
        header('Location: ../view/crear_sala.php');
        exit();
    }
}

// Incluir la vista
require_once '../view/crear_sala.php';
?> 