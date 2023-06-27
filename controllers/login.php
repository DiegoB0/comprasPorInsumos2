<?php

require_once '../models/login.php';

//Instancia de la clase
$model = new Model();

$model->usuario = $_POST['txtUsuario'];
$model->contraseña = $_POST['txtPassword'];

$filaController = $model->Logear();

if ($filaController > 0) {
  echo '<h1> Bienvenido de nuevo </h1>';
  header('refresh:2; url=http://localhost/comprasPorInsumos2/pages/reportes.html');

} else {

  echo '<h1> Usuario o contraseña incorrectos </h1>';

  header('refresh:2; url=http://localhost/comprasPorInsumos2/index.php');
}