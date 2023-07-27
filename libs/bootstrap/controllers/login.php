<?php

require_once '../models/login.php';

//Instancia de la clase
$model = new Model();

$model->email = $_POST['txtUsuario'];
$model->pass = $_POST['txtPassword'];

$filaController = $model->Logear();

if ($filaController > 0) {
  echo '<h1> Bienvenido de nuevo </h1>';

  header('refresh:2; url=http://localhost/comprasPorInsumos/client/pages/reportes.html');

} else {

  echo '<h1> Usuario o contraseña incorrectos </h1>';

  header('refresh:2; url=http://localhost/comprasPorInsumos/client/pages/reportes.html');
}