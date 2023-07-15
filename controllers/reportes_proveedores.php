<?php

require_once '../models/reportes_proveedores.php';

$model = new Proveedores();

if (isset($_GET['fila'])) {
    $rows = $model->proveedores_reporte();
} else {
    $rows = $model->proveedores_reporte();
}

echo json_encode($rows);
?>
