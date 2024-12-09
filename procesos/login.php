<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require_once "./conexion.php";
    
    try {
        // Preparar y sanitizar los datos de entrada
        $user = htmlspecialchars(trim($_POST['user']), ENT_QUOTES, 'UTF-8');
        $pwd = htmlspecialchars(trim($_POST['pwd']), ENT_QUOTES, 'UTF-8');

        // Consulta preparada para obtener el usuario
        $sql = "SELECT c.*, r.tipo_roles 
                FROM camarero c 
                INNER JOIN roles r ON c.id_roles = r.id_roles 
                WHERE c.usuario = :user";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user', $user, PDO::PARAM_STR);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (password_verify($pwd, $usuario['password'])) {
                session_start();
                $_SESSION['id_camarero'] = $usuario['id_camarero'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['usuario'] = $usuario['usuario'];
                $_SESSION['rol'] = $usuario['tipo_roles'];
                $_SESSION['success'] = true;

                // Redirección según el rol
                switch ($usuario['tipo_roles']) {
                    case 'Administrador':
                        header('Location: ../admin/admin.php');
                        break;
                    case 'Camarero':
                        header('Location: ../view/index.php');
                        break;
                }
                exit();
            } else {
                header('Location: ../index.php?error=5');
                exit();
            }
        } else {
            header('Location: ../index.php?error=5');
            exit();
        }

    } catch (PDOException $e) {
        // Log del error
        error_log("Error en login.php: " . $e->getMessage());
        header('Location: ../index.php?error=system');
        exit();
    } finally {
        // Cerrar la conexión
        $conn = null;
    }
} else {
    header('Location: ../index.php');
    exit();
}