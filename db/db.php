<?php

class Conexion
{

  function __construct()
  {

  }

  function con()
  {


    try {

      $cadenaCnx = "sqlsrv: Server=KIM\NATIONALSOFT2023;Database=softrestaurant10pro";
      $user = "sa";
      $password = "National09";

      $conexion = new PDO($cadenaCnx, $user, $password);



    } catch (PDOException $error) {
      echo "Ocurrió un error: $error";
    }

    return $conexion;
  }

  function con2()
  {
    try {
      $cadenacnx = "";
      $user = "";
      $password = "";

      $conexion = "";
    } catch (PDOException $error) {
      echo "Ocurrio un error: $error";
    }
  }
}