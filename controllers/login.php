<?php

require_once '../models/login.php';

//Instancia de la clase
$model = new Model();

$model->email = $_POST['email'];
$model->pass = $_POST['pass'];

$filaController = $model->Logear();

if ($filaController > 0) {
  echo '<h1> Bienvenido de nuevo </h1>';
  header('refresh:2; url=http://localhost/comprasPorInsumos2/pages/main.html');

} else {

  echo '<h1 style="text-align:center; color:gray; display:block; margin-top:4rem"> Usuario o contraseña incorrectos </h1><br><img src="../assets/img/primo_triste.gif" style="display: block; margin-left: auto; margin-right: auto; ">';

  header('refresh:2; url=http://localhost/comprasPorInsumos2/');
}