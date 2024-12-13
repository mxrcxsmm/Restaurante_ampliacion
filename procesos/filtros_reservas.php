<?php
require_once "../procesos/conexion.php";

try {
    // Consulta base para obtener todas las mesas con sus reservas
    $sql_base = "SELECT DISTINCT
        m.id_mesa,
        s.nombre_sala,
        tp.tipo_sala,
        r.id_reserva,
        DATE_FORMAT(r.fecha_reserva, '%d/%m/%Y') as fecha_reserva,
        DATE_FORMAT(r.hora_reserva_inicio, '%H:%i') as hora_reserva,
        r.nombre_cliente,
        c.nombre as nombre_camarero,
        CASE 
            WHEN EXISTS (
                SELECT 1 
                FROM reservas r2 
                WHERE r2.id_mesa = m.id_mesa 
                AND r2.fecha_reserva = CURDATE() 
                AND NOW() BETWEEN r2.hora_reserva_inicio AND r2.hora_reserva_fin
            ) THEN 'Ocupada'
            WHEN EXISTS (
                SELECT 1 
                FROM reservas r3 
                WHERE r3.id_mesa = m.id_mesa 
                AND r3.fecha_reserva > CURDATE() 
            ) THEN 'Reservada'
            ELSE 'Libre'
        END as estado
        FROM mesa m 
        INNER JOIN sala s ON m.id_sala = s.id_sala 
        INNER JOIN tipo_sala tp ON s.id_tipoSala = tp.id_tipoSala 
        LEFT JOIN reservas r ON m.id_mesa = r.id_mesa
        LEFT JOIN camarero c ON r.id_camarero = c.id_camarero
        WHERE (r.fecha_reserva IS NULL OR r.fecha_reserva >= CURDATE())"; // Filtrar reservas pasadas

    // Aplicar filtros según los parámetros recibidos
    $whereConditions = [];
    $params = [];

    if (isset($_GET['tipoSala'])) {
        $tipoSala = htmlspecialchars(trim($_GET['tipoSala']), ENT_QUOTES, 'UTF-8');
        $whereConditions[] = "tp.id_tipoSala = :tipoSala";
        $params[':tipoSala'] = $tipoSala;
        $_SESSION['tipoSala'] = $tipoSala;
    }

    if (isset($_GET['camarero'])) {
        $camarero = htmlspecialchars(trim($_GET['camarero']), ENT_QUOTES, 'UTF-8');
        $whereConditions[] = "c.id_camarero = :camarero";
        $params[':camarero'] = $camarero;
    }

    if (isset($_GET['sala'])) {
        $sala = htmlspecialchars(trim($_GET['sala']), ENT_QUOTES, 'UTF-8');
        $whereConditions[] = "s.id_sala = :sala";
        $params[':sala'] = $sala;
        $_SESSION['sala'] = $sala;
    }

    if (isset($_GET['mesa'])) {
        $mesa = htmlspecialchars(trim($_GET['mesa']), ENT_QUOTES, 'UTF-8');
        $whereConditions[] = "m.id_mesa = :mesa";
        $params[':mesa'] = $mesa;
    }

    // Añadir condiciones WHERE si existen
    if (!empty($whereConditions)) {
        $sql_base .= " AND " . implode(" AND ", $whereConditions);
    }

    // Ordenar resultados
    $sql_base .= " ORDER BY m.id_mesa ASC, r.fecha_reserva ASC";

    // Preparar y ejecutar la consulta
    $stmt = $conn->prepare($sql_base);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Error en la consulta: " . $e->getMessage());
    $result = [];
}
?>