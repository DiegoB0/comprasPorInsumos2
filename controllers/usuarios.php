<?php

require_once '../models/usuarios.php';

$model = new Usuario();

if (isset($_POST['registrar'])) {
  $fila_controller = $model->InsertData();
}

if (isset($_GET['usuarios'])) {
  $rows = $model->fetchUsuarios();
} else {
  $rows = [];
}

echo json_encode($rows);