<?php

    class conexion{

        function con(){
            $host="127.0.0.1";
            $db="softrestaurant10pro";
            $servidor="sa";
            $pass="estadias";
            $puerto=1433;

            try{

                $con = new PDO("sqlsrv:Server=$host,$puerto;Database=$db",$servidor,$pass);
                echo "conexión lograda:D";

            }catch(PDOException $nono){
                echo "no se hizo :( a $db, error: $nono";
            }

            return $con;
        }
    }
    
?>
