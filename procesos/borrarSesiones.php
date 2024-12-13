<?php
    session_start();
    if(!isset($_SESSION['id_camarero'])){
        header('Location:../index.php');
        exit();
    }
    if(isset($_SESSION['camarero']) && $_SESSION['camarero']){
        unset($_SESSION['camarero']);
    }
    if(isset($_SESSION['tipoSala']) && $_SESSION['tipoSala']){
        unset($_SESSION['tipoSala']);
    }
    if(isset($_SESSION['sala']) && $_SESSION['sala']){
        unset($_SESSION['sala']);
    }
    if(isset($_SESSION['tiempo']) && $_SESSION['tiempo']){
        unset($_SESSION['tiempo']);
    }
    if(isset($_SESSION['busqueda']) && $_SESSION['busqueda']){
        unset($_SESSION['busqueda']);
    }
    if(isset($_GET['borrarReservas']) && $_GET['borrarReservas']){
        header('Location:../view/filtros_reservas.php');
        exit();
    }
    if(isset($_GET['borrarHistorial']) && $_GET['borrarHistorial']){
        header('Location:../view/filtros_historial.php');
        exit();
    }
    if(isset($_GET['salir'])){
        header('Location:../view/index.php');
        exit();
    }