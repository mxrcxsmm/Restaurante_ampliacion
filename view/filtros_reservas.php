<?php
session_start();
if (!isset($_SESSION['id_camarero'])) {
    header('Location: ../index.php');
    exit();
}
require_once '../procesos/conexion.php';
require_once "../procesos/filtros_reservas.php";
$filtrarSalas = isset($_SESSION['tipoSala']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Salas y Mesas</title>
  <link rel="stylesheet" href="../css/style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script>
    function cambiarTabla() {
      if (window.matchMedia("(max-width: 900px)").matches) {
        document.getElementById("nombreSala").textContent = 'Sala';
        document.getElementById("numeroMesa").textContent = 'Nº';
        document.getElementById("horaIni").textContent = 'Hora ini.';
      } else {
        document.getElementById("nombreSala").textContent = 'Nombre de sala';
        document.getElementById("numeroMesa").textContent = 'Número de mesa';
        document.getElementById("horaIni").textContent = 'Hora inicio';
      }
    }
    window.onload = cambiarTabla;
    window.onresize = cambiarTabla;
  </script>
</head>

<body class="body2">
  <?php

  include '../header.php';

  ?>
  <div id="divReiniciar">
  <a href="../procesos/borrarSesiones.php?salir=1">
    <button class="btn btn-danger">Volver</button>
  </a>
  <a href="../procesos/borrarSesiones.php?borrarReservas=5">
    <button class="btn btn-warning">Reiniciar Filtros</button>
  </a>
  </div>

  <nav class="navbar navbar-expand-lg barra">
    <div class="container-fluid">
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle enlace-barra" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Orden de tiempo</a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="filtros_reservas.php?orden=asc">Más antiguo</a></li>
              <li><a class="dropdown-item" href="filtros_reservas.php?orden=desc">Último</a></li>
            </ul>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Camarero</a>
            <ul class="dropdown-menu">
              <?php
              try {
                  $sqlCamarero = "SELECT * FROM camarero";
                  $stmtCamarero = $conn->query($sqlCamarero);
                  while ($fila = $stmtCamarero->fetch(PDO::FETCH_ASSOC)) {
                      $idCamarero = htmlspecialchars($fila['id_camarero']);
                      $nomCamarero = htmlspecialchars($fila['nombre']);
                      echo "<li><a class='dropdown-item enlace-barra' href='filtros_reservas.php?camarero={$idCamarero}'>$nomCamarero</a></li>";
                  }
              } catch(PDOException $e) {
                  error_log("Error al obtener camareros: " . $e->getMessage());
              }
              ?>
            </ul>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Tipo de sala</a>
            <ul class="dropdown-menu">
              <?php
              try {
                  $sqlNumTipoSala = 'SELECT * FROM tipo_Sala';
                  $stmtTipoSala = $conn->query($sqlNumTipoSala);
                  while ($fila = $stmtTipoSala->fetch(PDO::FETCH_ASSOC)) {
                      $idTipoSala = htmlspecialchars($fila['id_tipoSala']);
                      $nombreTipoSala = htmlspecialchars($fila['tipo_sala']);
                      echo "<li><a class='dropdown-item enlace-barra' href='filtros_reservas.php?tipoSala={$idTipoSala}'>$nombreTipoSala</a>";
                  }
              } catch(PDOException $e) {
                  error_log("Error al obtener tipos de sala: " . $e->getMessage());
              }
              ?>
            </ul>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" <?php echo !isset($_SESSION['tipoSala']) ? 'disabled' : ''; ?> href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Salas</a>
            <ul class="dropdown-menu">
              <?php
              if (isset($_SESSION['tipoSala'])) {
                  try {
                      $tipoSala = htmlspecialchars(trim($_SESSION['tipoSala']), ENT_QUOTES, 'UTF-8');
                      $sqlSalas = "SELECT * FROM sala WHERE id_tipoSala = ?";
                      $stmtSalas = $conn->prepare($sqlSalas);
                      $stmtSalas->execute([$tipoSala]);
                      
                      while ($fila = $stmtSalas->fetch(PDO::FETCH_ASSOC)) {
                          $idSala = htmlspecialchars($fila['id_sala']);
                          $nombreSala = htmlspecialchars($fila['nombre_sala']);
                          echo "<li><a class='dropdown-item enlace-barra' href='filtros_reservas.php?sala={$idSala}'>$nombreSala</a></li>";
                      }
                  } catch(PDOException $e) {
                      error_log("Error al obtener salas: " . $e->getMessage());
                  }
              } else {
                  echo "<li class='dropdown-item disabled'>Seleccione un tipo de sala primero</li>";
              }
              ?>
            </ul>
          </li>

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle enlace-barra" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Numero Mesa</a>
            <ul class="dropdown-menu scrollable-dropdown">
              <?php
              try {
                  if(isset($_SESSION['sala'])){
                      $sala = htmlspecialchars(trim($_SESSION['sala']), ENT_QUOTES, 'UTF-8');
                      $sqlMesaS = "SELECT m.id_mesa FROM mesa m INNER JOIN sala s ON m.id_sala = s.id_sala WHERE s.id_sala = ?";
                      $stmtMesaS = $conn->prepare($sqlMesaS);
                      $stmtMesaS->execute([$sala]);
                      
                      while($fila = $stmtMesaS->fetch(PDO::FETCH_ASSOC)){
                          $idMesa = htmlspecialchars($fila['id_mesa']);
                          echo "<li><a class='dropdown-item' href='filtros.php?mesa=$idMesa'>$idMesa</a></li>";
                      }
                  } else {
                      $sqlMesas = "SELECT * FROM mesa";
                      $stmtMesas = $conn->query($sqlMesas);
                      while($fila = $stmtMesas->fetch(PDO::FETCH_ASSOC)){
                          $idMesa = htmlspecialchars($fila['id_mesa']);
                          echo "<li><a class='dropdown-item' href='filtros.php?mesa=$idMesa'>$idMesa</a></li>";
                      }
                  }
              } catch(PDOException $e) {
                  error_log("Error al obtener mesas: " . $e->getMessage());
              }
              ?>
            </ul>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle enlace-barra" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Tiempo</a>
            <ul id="bajar" class="dropdown-menu">
              <li class="centrar">Introduce una fecha</li>
              <li><form id="formFecha" method="GET" action="">
                <input id="inputFecha" type="datetime-local" name="tiempo" id="tiempo">
                <button id="btnFecha" class="btn btn-outline-success" type="submit">Buscar</button>
              </form></li>
            </ul>
        </li>
        </ul>
        <form class="d-flex" role="search" method="get" action="">
          <input class="form-control me-2" type="search" name="query" placeholder="Buscar" aria-label="Search">
          <button class="btn btn-outline-success" type="submit">Buscar</button>
        </form>
        <div id="resultados">
        </div>
      </div>
    </div>
  </nav>
  <?php
echo "<h1 id=historial>Estado de Mesas</h1>";
try {
    if ($result && count($result) > 0) {
        echo "<table>";
        echo "<thead>";
        echo "<tr>";
        echo "<th class='ocultarSala'>Tipo Sala</th>";
        echo "<th id='nombreSala'>Nombre Sala</th>";
        echo "<th id='numeroMesa'>Nº Mesa</th>";
        echo "<th>Fecha</th>";
        echo "<th>Cliente</th>";
        echo "<th>Estado</th>";
        echo "<th>Hora Reserva</th>";
        echo "<th>Camarero</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        foreach ($result as $fila) {
            echo "<tr>";
            echo "<td class='ocultarSala'>" . htmlspecialchars($fila['tipo_sala']) . "</td>";
            echo "<td>" . htmlspecialchars($fila['nombre_sala']) . "</td>";
            echo "<td>" . htmlspecialchars($fila['id_mesa']) . "</td>";
            echo "<td>" . ($fila['fecha_reserva'] ?? '-') . "</td>";
            echo "<td>" . ($fila['nombre_cliente'] ?? '-') . "</td>";
            
            // Mostrar estado con color
            $estado_class = '';
            switch($fila['estado']) {
                case 'Ocupada':
                    $estado_class = 'text-danger';
                    break;
                case 'Reservada':
                    $estado_class = 'text-warning';
                    break;
                case 'Libre':
                    $estado_class = 'text-success';
                    break;
            }
            echo "<td class='{$estado_class}'><strong>" . htmlspecialchars($fila['estado']) . "</strong></td>";
            
            echo "<td>" . ($fila['hora_reserva'] ?? '-') . "</td>";
            echo "<td>" . ($fila['nombre_camarero'] ?? '-') . "</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
    } else {
        echo "<p id='noResultado'>No hay mesas disponibles</p>";
    }
} catch (PDOException $e) {
    error_log("Error al mostrar resultados: " . $e->getMessage());
    echo "<p id='noResultado'>Error al cargar los resultados</p>";
}
?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script src="../validations/js/filtrosMesas.js"></script>
  <footer></footer>
</body>

</html>