<?php

    require_once '../models/reportes.php';

    $model = new Reporte();

    if (isset($_GET['lunes2023'])){
        $rows = $model->fetchLunes2023();
    } else {
        
    }

    if (isset($_GET['martes2023'])){
        $rows = $model->fetchMartes2023();
    } else {
    }

    if (isset($_GET['mier2023'])){
        $rows = $model->fetchMier2023();
    } else {
    }

    if (isset($_GET['jueves2023'])){
        $rows = $model->fetchJueves2023();
    } else {
    }

    if (isset($_GET['vier2023'])){
        $rows = $model->fetchVier2023();
    } else {
    }

    if (isset($_GET['sab2023'])){
        $rows = $model->fetchSab2023();
    } else {
    }

    if (isset($_GET['dom2023'])){
        $rows = $model->fetchDom2023();
    } else {
    }


    echo json_encode($rows);