<?php

class Conexion
{

  function __construct()
  {

  }

  function con()
  {
    $host = "127.0.0.1";
    $db = "softrestaurant10pro";
    $servidor = "sa";
    $pass = "Naional09";
    $puerto = 1433;

    try {

      $cadenaCnx = "sqlsrv: Server=KIM\NATIONALSOFT2023;Database=softrestaurant10pro";
      $user = "sa";
      $password = "National09";

      $conexion = new PDO($cadenaCnx, $user, $password);

      echo "conexión lograda:D";

    } catch (PDOException $nono) {
      echo "no se hizo :( a $db, error: $nono";
    }

    return $conexion;
  }
}