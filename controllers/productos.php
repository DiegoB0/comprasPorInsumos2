<?php

    require_once '../models/reportes.php';

    $model = new Reporte();

    if (isset($_GET['asada'])){
        $rows = $model->fetchAsada();
    } else {
        $rows = $model->fetchPastor();
    }

    if (isset($_GET['arrachera'])){
        $rows = $model->fetchArrachera();
    }

    if (isset($_GET['aguacate'])){
        $rows = $model->fetchAguacate();
    }

    if (isset($_GET['ajo'])){
        $rows = $model->fetchAjo();
    }

    if (isset($_GET['apio'])){
        $rows = $model->fetchApio();
    }

    if (isset($_GET['chile'])){
        $rows = $model->fetchChile();
    }

    if (isset($_GET['cebolla'])){
        $rows = $model->fetchCebolla();
    }

    if (isset($_GET['tomate'])){
        $rows = $model->fetchTomate();
    }

    if (isset($_GET['pina'])){
        $rows = $model->fetchPina();
    }

    if (isset($_GET['limon'])){
        $rows = $model->fetchLimon();
    }

    if (isset($_GET['azucar'])){
        $rows = $model->fetchAzucar();
    }

    if (isset($_GET['refrescos'])){
        $rows = $model->fetchRefrescos();
    }

    if (isset($_GET['maiz'])){
        $rows = $model->fetchMaiz();
    }

    if (isset($_GET['harina'])){
        $rows = $model->fetchHarina();
    }

    if (isset($_GET['pan'])){
        $rows = $model->fetchPan();
    }

    if (isset($_GET['queso'])){
        $rows = $model->fetchQueso();
    }

    echo json_encode($rows);