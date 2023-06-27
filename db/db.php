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
    $servidor = "sa"; //cambiar al propio
    $pass = "estadias"; //cambiar al propio
    $puerto = 1433;

    try {

        //cambiar al propio info de server
      $cadenaCnx = "sqlsrv: Server=ROMAN\NATIONALSOFT2023;Database=softrestaurant10pro";
      $user = "sa";
      $password = "estadias"; //cambiar al propio

      $conexion = new PDO($cadenaCnx, $user, $password);

      echo "conexión lograda:D";

    } catch (PDOException $nono) {
      echo "no se hizo :( a $db, error: $nono";
    }

    return $conexion;
  }
}