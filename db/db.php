<?php

class Conexion
{

  function __construct()
  {

  }

  function con()
  {

    try {

      $cadenaCnx = "sqlsrv: Server=ROMAN\NATIONALSOFT2023;Database=softrestaurant10pro";
      $user = "sa";
      $password = "estadias";

      $conexion = new PDO($cadenaCnx, $user, $password);



    } catch (PDOException $error) {
      echo "Ocurrió un error: $error";
    }

    return $conexion;
  }

  function conCorrales()
  {
    try {

      $cadenaCnx = "sqlsrv: Server=ROMAN\NATIONALSOFT2023;Database=loscorrales";
      $user = "sa";
      $password = "estadias";

      $conexion = new PDO($cadenaCnx, $user, $password);

    } catch (PDOException $error) {
      echo "Ocurrió un error: $error";
    }

    return $conexion;
  }
}