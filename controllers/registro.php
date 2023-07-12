<?php

require_once "../models/registro.php";

$model = new Registro();

$filaController = $model->InsertData();

if(isset($filaController))
{
    echo '<h1 style="text-align:center; color:gray; display:block; margin-top:4rem">Se insertó correctamente :D</h1>';
    header('refresh:2; url=http://localhost/comprasPorInsumos2/pages/registro.php');

} else {
    echo '<h1 style="text-align:center; color:gray; display:block; margin-top:4rem">No se insertó correctamente :(</h1>';
    header('refresh:2; url=http://localhost/comprasPorInsumos2/pages/registro.php');
}

?>