<?php
$servidor = "localhost";
$usuario = "root";
$pwd = "";
$db = "db_restaurante02";  // Nombre actualizado de la base de datos

try {
    // Crear una conexión PDO
    $conn = new PDO("mysql:host=$servidor;dbname=$db", $usuario, $pwd);
    // Establecer el modo de error de PDO
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // En caso de error, mostrar mensaje
    echo "Conexión fallida: " . $e->getMessage();
}
