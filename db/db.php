<?php

    class conexion{

        function con(){
            $host="localhost";
            $db="database";
            $servidor="sa";
            $pass="pacochato";
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