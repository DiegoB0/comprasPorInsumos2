<?php

require_once "../models/registro.php";

$model = new Registro();

$filaController = $model->InsertData();

if(isset($filaController))
{
    echo '<h1 style="text-align:center; color:gray; display:block; margin-top:4rem">Se insertó correctamente :D</h1>
     <a href="#" data-page="u.administrar"> Regresar</a>
    ';

} else {
    echo '<h1 style="text-align:center; color:gray; display:block; margin-top:4rem">No se insertó correctamente :(</h1>
     <a href="#" data-page="u.administrar"> Regresar</a>
    ';
}

?>