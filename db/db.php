<?php

class Conexion
{

  function __construct()
  {

  }

  function con()
  {
    $db = "softrestaurant10pro";

    try {

        //cambiar al propio info de server
      $cadenaCnx = "sqlsrv: Server=ROMAN\NATIONALSOFT2023;Database=softrestaurant10pro";
      $user = "sa";
      $password = "estadias"; //cambiar al propio

      $conexion = new PDO($cadenaCnx, $user, $password);

      //echo "conexión lograda:D";

    } catch (PDOException $nono) {
      echo "no se hizo :( a $db, error: $nono";
    }

    return $conexion;
  }
}