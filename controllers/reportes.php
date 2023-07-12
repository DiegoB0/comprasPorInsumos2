<?php

require_once '../models/reportes.php';

//Instancia de la clase
$model = new Reporte();


if (isset($_POST['start_date']) && isset($_POST['end_date'])) {
  echo $_POST['start_date'];
  echo $_POST['end_date'];
}

$rows = $model->fetchData();

echo json_encode($rows);