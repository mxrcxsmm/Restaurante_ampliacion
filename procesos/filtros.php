<?php
require_once "../procesos/conexion.php";

try {
    if (isset($_GET['orden']) && ($_GET['orden'] == "asc" || $_GET['orden'] == "desc")) {
        $orden = htmlspecialchars(trim($_GET['orden']), ENT_QUOTES, 'UTF-8');
        
        if(isset($_SESSION['camarero'])){
            $camarero = htmlspecialchars(trim($_SESSION['camarero']), ENT_QUOTES, 'UTF-8');
            $sqlOrden = "SELECT c.nombre, tp.tipo_sala, s.nombre_sala, m.id_mesa, h.hora_inicio, h.hora_fin 
                        FROM historial h 
                        INNER JOIN camarero c ON h.id_camarero = c.id_camarero 
                        INNER JOIN mesa m ON m.id_mesa = h.id_mesa 
                        INNER JOIN sala s ON s.id_sala = m.id_sala 
                        INNER JOIN tipo_sala tp ON tp.id_tipoSala = s.id_tipoSala 
                        WHERE c.id_camarero = ? 
                        ORDER BY h.hora_inicio " . $orden;
            $stmt = $conn->prepare($sqlOrden);
            $stmt->execute([$camarero]);
        } else {
            $sqlOrden = "SELECT c.nombre, tp.tipo_sala, s.nombre_sala, m.id_mesa, h.hora_inicio, h.hora_fin 
                        FROM historial h 
                        INNER JOIN camarero c ON h.id_camarero = c.id_camarero 
                        INNER JOIN mesa m ON m.id_mesa = h.id_mesa 
                        INNER JOIN sala s ON s.id_sala = m.id_sala 
                        INNER JOIN tipo_sala tp ON tp.id_tipoSala = s.id_tipoSala 
                        ORDER BY h.hora_inicio " . $orden;
            $stmt = $conn->query($sqlOrden);
        }
        $result = $stmt;
    } else if (isset($_GET['camarero'])) {
        $camarero = htmlspecialchars(trim($_GET['camarero']), ENT_QUOTES, 'UTF-8');
        $sqlCamarero = "SELECT c.id_camarero, c.nombre, tp.tipo_sala, s.nombre_sala, m.id_mesa, h.hora_inicio, h.hora_fin 
                        FROM historial h 
                        INNER JOIN camarero c ON h.id_camarero = c.id_camarero 
                        INNER JOIN mesa m ON m.id_mesa = h.id_mesa 
                        INNER JOIN sala s ON s.id_sala = m.id_sala 
                        INNER JOIN tipo_sala tp ON tp.id_tipoSala = s.id_tipoSala 
                        WHERE c.id_camarero = ?";
        $stmt = $conn->prepare($sqlCamarero);
        $stmt->execute([$camarero]);
        $result = $stmt;
        $_SESSION['camarero'] = htmlspecialchars($camarero);

    } else if (isset($_GET['tipoSala'])) {
        $tipoSala = htmlspecialchars(trim($_GET['tipoSala']), ENT_QUOTES, 'UTF-8');
        $_SESSION['tipoSala'] = $tipoSala;

        if (!isset($_SESSION['camarero'])) {
            $sqlTipoSala = "SELECT c.nombre, tp.tipo_sala, s.nombre_sala, m.id_mesa, h.hora_inicio, h.hora_fin, tp.id_tipoSala 
                           FROM historial h 
                           INNER JOIN camarero c ON h.id_camarero = c.id_camarero 
                           INNER JOIN mesa m ON m.id_mesa = h.id_mesa 
                           INNER JOIN sala s ON s.id_sala = m.id_sala 
                           INNER JOIN tipo_sala tp ON tp.id_tipoSala = s.id_tipoSala 
                           WHERE tp.id_tipoSala = ?";
            $stmt = $conn->prepare($sqlTipoSala);
            $stmt->execute([$tipoSala]);
        } else {
            $camarero = htmlspecialchars(trim($_SESSION['camarero']), ENT_QUOTES, 'UTF-8');
            $sqlTipoSala = "SELECT c.nombre, tp.tipo_sala, s.nombre_sala, m.id_mesa, h.hora_inicio, h.hora_fin, tp.id_tipoSala 
                           FROM historial h 
                           INNER JOIN camarero c ON h.id_camarero = c.id_camarero 
                           INNER JOIN mesa m ON m.id_mesa = h.id_mesa 
                           INNER JOIN sala s ON s.id_sala = m.id_sala 
                           INNER JOIN tipo_sala tp ON tp.id_tipoSala = s.id_tipoSala 
                           WHERE tp.id_tipoSala = ? AND c.id_camarero = ?";
            $stmt = $conn->prepare($sqlTipoSala);
            $stmt->execute([$tipoSala, $camarero]);
        }
        $result = $stmt;

        if (isset($_SESSION['sala'])) {
            unset($_SESSION['sala']);
        }
    } else if (isset($_GET['sala'])) {
        $id_sala = htmlspecialchars(trim($_GET['sala']), ENT_QUOTES, 'UTF-8');
        $_SESSION['sala'] = $id_sala;
        $salaID = htmlspecialchars(trim($_SESSION['tipoSala']), ENT_QUOTES, 'UTF-8');

        if (!isset($_SESSION['camarero'])) {
            $sqlSala = "SELECT c.nombre, tp.tipo_sala, s.nombre_sala, m.id_mesa, h.hora_inicio, h.hora_fin, s.id_sala 
                       FROM historial h 
                       INNER JOIN camarero c ON h.id_camarero = c.id_camarero 
                       INNER JOIN mesa m ON m.id_mesa = h.id_mesa 
                       INNER JOIN sala s ON s.id_sala = m.id_sala 
                       INNER JOIN tipo_sala tp ON tp.id_tipoSala = s.id_tipoSala 
                       WHERE tp.id_tipoSala = ? AND s.id_sala = ?";
            $stmt = $conn->prepare($sqlSala);
            $stmt->execute([$salaID, $id_sala]);
        } else {
            $camarero = htmlspecialchars(trim($_SESSION['camarero']), ENT_QUOTES, 'UTF-8');
            $sqlSala = "SELECT c.nombre, tp.tipo_sala, s.nombre_sala, m.id_mesa, h.hora_inicio, h.hora_fin, s.id_sala 
                       FROM historial h 
                       INNER JOIN camarero c ON h.id_camarero = c.id_camarero 
                       INNER JOIN mesa m ON m.id_mesa = h.id_mesa 
                       INNER JOIN sala s ON s.id_sala = m.id_sala 
                       INNER JOIN tipo_sala tp ON tp.id_tipoSala = s.id_tipoSala 
                       WHERE tp.id_tipoSala = ? AND s.id_sala = ? AND c.id_camarero = ?";
            $stmt = $conn->prepare($sqlSala);
            $stmt->execute([$salaID, $id_sala, $camarero]);
        }
        $result = $stmt;
    } else if (isset($_GET['mesa'])) {
        $idMesa = htmlspecialchars(trim($_GET['mesa']), ENT_QUOTES, 'UTF-8');
        
        if (!isset($_SESSION['sala'])) {
            $sqlMesa = "SELECT c.nombre, tp.tipo_sala, s.nombre_sala, m.id_mesa, h.hora_inicio, h.hora_fin 
                       FROM historial h 
                       INNER JOIN camarero c ON h.id_camarero = c.id_camarero 
                       INNER JOIN mesa m ON m.id_mesa = h.id_mesa 
                       INNER JOIN sala s ON s.id_sala = m.id_sala 
                       INNER JOIN tipo_sala tp ON tp.id_tipoSala = s.id_tipoSala 
                       WHERE m.id_mesa = ?";
            $stmt = $conn->prepare($sqlMesa);
            $stmt->execute([$idMesa]);
        } else {
            $sala = htmlspecialchars(trim($_SESSION['sala']), ENT_QUOTES, 'UTF-8');
            $sqlMesaS = "SELECT c.nombre, tp.tipo_sala, s.nombre_sala, m.id_mesa, h.hora_inicio, h.hora_fin, s.id_sala 
                        FROM historial h 
                        INNER JOIN camarero c ON h.id_camarero = c.id_camarero 
                        INNER JOIN mesa m ON m.id_mesa = h.id_mesa 
                        INNER JOIN sala s ON s.id_sala = m.id_sala 
                        INNER JOIN tipo_sala tp ON tp.id_tipoSala = s.id_tipoSala 
                        WHERE m.id_mesa = ? AND s.id_sala = ?";
            $stmt = $conn->prepare($sqlMesaS);
            $stmt->execute([$idMesa, $sala]);
        }
        $result = $stmt;
    } else if (isset($_GET['tiempo'])) {
        $tiempo = htmlspecialchars(trim($_GET['tiempo']), ENT_QUOTES, 'UTF-8');
        $_SESSION['tiempo'] = $tiempo;

        if(!isset($_SESSION['camarero'])){
            $sqlTiempo = "SELECT c.nombre, tp.tipo_sala, s.nombre_sala, m.id_mesa, h.hora_inicio, h.hora_fin 
                         FROM historial h 
                         INNER JOIN camarero c ON h.id_camarero = c.id_camarero 
                         INNER JOIN mesa m ON m.id_mesa = h.id_mesa 
                         INNER JOIN sala s ON s.id_sala = m.id_sala 
                         INNER JOIN tipo_sala tp ON tp.id_tipoSala = s.id_tipoSala 
                         WHERE (h.hora_inicio BETWEEN DATE_SUB(?, INTERVAL 24 HOUR) AND DATE_ADD(?, INTERVAL 24 HOUR))
                         OR (h.hora_fin BETWEEN DATE_SUB(?, INTERVAL 24 HOUR) AND DATE_ADD(?, INTERVAL 24 HOUR))";
            $stmt = $conn->prepare($sqlTiempo);
            $stmt->execute([$tiempo, $tiempo, $tiempo, $tiempo]);
        } else {
            $camarero = htmlspecialchars(trim($_SESSION['camarero']), ENT_QUOTES, 'UTF-8');
            $sqlTiempo = "SELECT c.nombre, tp.tipo_sala, s.nombre_sala, m.id_mesa, h.hora_inicio, h.hora_fin 
                         FROM historial h 
                         INNER JOIN camarero c ON h.id_camarero = c.id_camarero 
                         INNER JOIN mesa m ON m.id_mesa = h.id_mesa 
                         INNER JOIN sala s ON s.id_sala = m.id_sala 
                         INNER JOIN tipo_sala tp ON tp.id_tipoSala = s.id_tipoSala 
                         WHERE ((h.hora_inicio BETWEEN DATE_SUB(?, INTERVAL 24 HOUR) AND DATE_ADD(?, INTERVAL 24 HOUR))
                         OR (h.hora_fin BETWEEN DATE_SUB(?, INTERVAL 24 HOUR) AND DATE_ADD(?, INTERVAL 24 HOUR)))
                         AND (c.id_camarero = ?)";
            $stmt = $conn->prepare($sqlTiempo);
            $stmt->execute([$tiempo, $tiempo, $tiempo, $tiempo, $camarero]);
        }
        $result = $stmt;
    } else if (isset($_GET['query'])) {
        $busqueda = htmlspecialchars(trim($_GET['query']), ENT_QUOTES, 'UTF-8');
        $sqlBusqueda = "SELECT c.nombre, tp.tipo_sala, s.nombre_sala, m.id_mesa, h.hora_inicio, h.hora_fin 
                        FROM historial h 
                        INNER JOIN camarero c ON h.id_camarero = c.id_camarero 
                        INNER JOIN mesa m ON m.id_mesa = h.id_mesa 
                        INNER JOIN sala s ON s.id_sala = m.id_sala 
                        INNER JOIN tipo_sala tp ON tp.id_tipoSala = s.id_tipoSala 
                        WHERE c.nombre = ? OR tp.tipo_sala = ? OR s.nombre_sala = ? OR m.id_mesa = ?";
        $stmt = $conn->prepare($sqlBusqueda);
        $stmt->execute([$busqueda, $busqueda, $busqueda, $busqueda]);
        $result = $stmt;
        $_SESSION['busqueda'] = $busqueda;
    } else {
        $sql = "SELECT c.nombre, tp.tipo_sala, s.nombre_sala, m.id_mesa, h.hora_inicio, h.hora_fin 
                FROM historial h 
                INNER JOIN camarero c ON h.id_camarero = c.id_camarero 
                INNER JOIN mesa m ON m.id_mesa = h.id_mesa 
                INNER JOIN sala s ON s.id_sala = m.id_sala 
                INNER JOIN tipo_sala tp ON tp.id_tipoSala = s.id_tipoSala";
        $stmt = $conn->query($sql);
        $result = $stmt;
    }
} catch(PDOException $e) {
    error_log("Error en filtros.php: " . $e->getMessage());
    header('Location: ../index.php?error=system');
    exit();
}
