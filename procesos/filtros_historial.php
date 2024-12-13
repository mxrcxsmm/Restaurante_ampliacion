<?php
require_once "../procesos/conexion.php";

try {
    // Validación de "orden" (asc/desc)
    if (isset($_GET['orden']) && ($_GET['orden'] === "asc" || $_GET['orden'] === "desc")) {
        $orden = htmlspecialchars(trim($_GET['orden']), ENT_QUOTES, 'UTF-8');
        
        // Si existe un camarero en la sesión
        if (isset($_SESSION['camarero'])) {
            $camarero = htmlspecialchars(trim($_SESSION['camarero']), ENT_QUOTES, 'UTF-8');
            $sqlOrden = "SELECT c.nombre, tp.tipo_sala, s.nombre_sala, m.id_mesa, h.hora_inicio, h.hora_fin 
                         FROM historial h 
                         INNER JOIN camarero c ON h.id_camarero = c.id_camarero 
                         INNER JOIN mesa m ON m.id_mesa = h.id_mesa 
                         INNER JOIN sala s ON s.id_sala = m.id_sala 
                         INNER JOIN tipo_sala tp ON tp.id_tipoSala = s.id_tipoSala 
                         WHERE c.id_camarero = :camarero 
                         ORDER BY h.hora_inicio $orden";
            $stmt = $conn->prepare($sqlOrden);
            $stmt->bindParam(':camarero', $camarero, PDO::PARAM_INT);
        } else {
            $sqlOrden = "SELECT c.nombre, tp.tipo_sala, s.nombre_sala, m.id_mesa, h.hora_inicio, h.hora_fin 
                         FROM historial h 
                         INNER JOIN camarero c ON h.id_camarero = c.id_camarero 
                         INNER JOIN mesa m ON m.id_mesa = h.id_mesa 
                         INNER JOIN sala s ON s.id_sala = m.id_sala 
                         INNER JOIN tipo_sala tp ON tp.id_tipoSala = s.id_tipoSala 
                         ORDER BY h.hora_inicio $orden";
            $stmt = $conn->query($sqlOrden);
        }
    } 
    // Filtro por camarero
    elseif (isset($_GET['camarero'])) {
        $camarero = htmlspecialchars(trim($_GET['camarero']), ENT_QUOTES, 'UTF-8');
        $sqlCamarero = "SELECT c.id_camarero, c.nombre, tp.tipo_sala, s.nombre_sala, m.id_mesa, h.hora_inicio, h.hora_fin 
                        FROM historial h 
                        INNER JOIN camarero c ON h.id_camarero = c.id_camarero 
                        INNER JOIN mesa m ON m.id_mesa = h.id_mesa 
                        INNER JOIN sala s ON s.id_sala = m.id_sala 
                        INNER JOIN tipo_sala tp ON tp.id_tipoSala = s.id_tipoSala 
                        WHERE c.id_camarero = :camarero";
        $stmt = $conn->prepare($sqlCamarero);
        $stmt->bindParam(':camarero', $camarero, PDO::PARAM_INT);
    } 
    // Filtro por tipo de sala
    elseif (isset($_GET['tipoSala'])) {
        $tipoSala = htmlspecialchars(trim($_GET['tipoSala']), ENT_QUOTES, 'UTF-8');
        $_SESSION['tipoSala'] = $tipoSala;

        $sqlTipoSala = "SELECT c.nombre, tp.tipo_sala, s.nombre_sala, m.id_mesa, h.hora_inicio, h.hora_fin 
                        FROM historial h 
                        INNER JOIN camarero c ON h.id_camarero = c.id_camarero 
                        INNER JOIN mesa m ON m.id_mesa = h.id_mesa 
                        INNER JOIN sala s ON s.id_sala = m.id_sala 
                        INNER JOIN tipo_sala tp ON tp.id_tipoSala = s.id_tipoSala 
                        WHERE tp.id_tipoSala = :tipoSala";

        if (isset($_SESSION['camarero'])) {
            $sqlTipoSala .= " AND c.id_camarero = :camarero";
            $stmt = $conn->prepare($sqlTipoSala);
            $stmt->bindParam(':tipoSala', $tipoSala, PDO::PARAM_INT);
            $stmt->bindParam(':camarero', $_SESSION['camarero'], PDO::PARAM_INT);
        } else {
            $stmt = $conn->prepare($sqlTipoSala);
            $stmt->bindParam(':tipoSala', $tipoSala, PDO::PARAM_INT);
        }
    } 
    // Filtro por sala
    elseif (isset($_GET['sala'])) {
        $idSala = htmlspecialchars(trim($_GET['sala']), ENT_QUOTES, 'UTF-8');
        $_SESSION['sala'] = $idSala;
        $sqlSala = "SELECT c.nombre, tp.tipo_sala, s.nombre_sala, m.id_mesa, h.hora_inicio, h.hora_fin 
                    FROM historial h 
                    INNER JOIN camarero c ON h.id_camarero = c.id_camarero 
                    INNER JOIN mesa m ON m.id_mesa = h.id_mesa 
                    INNER JOIN sala s ON s.id_sala = m.id_sala 
                    INNER JOIN tipo_sala tp ON tp.id_tipoSala = s.id_tipoSala 
                    WHERE s.id_sala = :idSala";

        if (isset($_SESSION['camarero'])) {
            $sqlSala .= " AND c.id_camarero = :camarero";
            $stmt = $conn->prepare($sqlSala);
            $stmt->bindParam(':idSala', $idSala, PDO::PARAM_INT);
            $stmt->bindParam(':camarero', $_SESSION['camarero'], PDO::PARAM_INT);
        } else {
            $stmt = $conn->prepare($sqlSala);
            $stmt->bindParam(':idSala', $idSala, PDO::PARAM_INT);
        }
    } 
    // Filtro por búsqueda general (query)
    elseif (isset($_GET['query'])) {
        $busqueda = htmlspecialchars(trim($_GET['query']), ENT_QUOTES, 'UTF-8');
        $sqlBusqueda = "SELECT c.nombre, tp.tipo_sala, s.nombre_sala, m.id_mesa, h.hora_inicio, h.hora_fin 
                        FROM historial h 
                        INNER JOIN camarero c ON h.id_camarero = c.id_camarero 
                        INNER JOIN mesa m ON m.id_mesa = h.id_mesa 
                        INNER JOIN sala s ON s.id_sala = m.id_sala 
                        INNER JOIN tipo_sala tp ON tp.id_tipoSala = s.id_tipoSala 
                        WHERE c.nombre LIKE :query OR tp.tipo_sala LIKE :query OR s.nombre_sala LIKE :query OR m.id_mesa LIKE :query";
        $stmt = $conn->prepare($sqlBusqueda);
        $stmt->bindParam(':query', $busqueda, PDO::PARAM_STR);
    } 
    // Consulta general por defecto
    else {
        $sql = "SELECT c.nombre, tp.tipo_sala, s.nombre_sala, m.id_mesa, h.hora_inicio, h.hora_fin 
                FROM historial h 
                INNER JOIN camarero c ON h.id_camarero = c.id_camarero 
                INNER JOIN mesa m ON m.id_mesa = h.id_mesa 
                INNER JOIN sala s ON s.id_sala = m.id_sala 
                INNER JOIN tipo_sala tp ON tp.id_tipoSala = s.id_tipoSala";
        $stmt = $conn->query($sql);
    }

    // Ejecutar la consulta fuera del try
    if (isset($stmt)) {
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
