<?php

require_once '../models/reportes.php';

//Instancia de la clase
$model = new Reporte();


if (isset($_POST['start_date']) && isset($_POST['end_date'])) {
  $start_date = $_POST['start_date'];
  $end_date = $_POST['end_date'];

  $rows = $model->date_range($start_date, $end_date);
} else {
  if (isset($_GET['proveedores'])) {
    $rows = $model->fetchProveedores();
  } else {
    $rows = $model->fetchInsumos();
  }
}


echo json_encode($rows);