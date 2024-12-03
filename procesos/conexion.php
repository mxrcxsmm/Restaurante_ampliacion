<?php
$servidor = "localhost";
$usuario = "root";
$pwd = "";
$db = "db_restaurante02";

try {
    // Crear conexión usando PDO
    $conn = new PDO("mysql:host=$servidor;dbname=$db;charset=utf8", $usuario, $pwd);

    // Configurar el modo de error de PDO a excepción
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Conexión exitosa";
} catch (PDOException $e) {
    // Manejo de errores
    echo "Error de conexión: " . $e->getMessage();
}
?>
