<?php

    class Corrales
    {
        function __construct(){}

        function con()
        {
            try{

                $cadenaCnx = "sqlsrv: Server=ROMAN\NATIONALSOFT2023;Database=loscorrales";
                $user = "sa";
                $password = "estadias";

                $conexion = new PDO($cadenaCnx, $user, $password);

            } catch (PDOException $error) {
                echo "Ocurrió un error: $error";
            } return $conexion;
        }
    }



?>