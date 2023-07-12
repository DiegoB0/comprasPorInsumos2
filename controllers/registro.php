<?php

require_once "../models/registro.php";

$model = new Registro();

$filaController = $model->InsertData();

if(isset($filaController))
{

    echo "se inserto correctamente";

} else {

    echo "no se inserto correctamente";

}

?>