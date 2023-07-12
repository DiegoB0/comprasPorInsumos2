<?php

require_once '../models/login.php';

//Instancia de la clase
$model = new Model();

$model->usuario = $_POST['txtUsuario'];
$model->contraseña = $_POST['txtPassword'];

$filaController = $model->Logear();

if ($filaController > 0) {
  echo '<h1 style="text-align:center; color:gray; display:block; margin-top:4rem">Bienvenido de Nuevo!</h1>';
  header('refresh:2; url=http://localhost/comprasPorInsumos/client/pages/reportes.php');

} else {

  echo '<h1 style="text-align:center; color:gray; display:block; margin-top:4rem"> Usuario o contraseña incorrectos </h1><br><img src="../assets/img/primo_triste.gif" style="display: block; margin-left: auto; margin-right: auto; ">';

  header('refresh:2; url=http://localhost/comprasPorInsumos/client/');
}
