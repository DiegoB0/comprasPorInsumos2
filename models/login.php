<?php

class Model
{
  var $id;
  var $usuario;
  var $contraseña;

  function __construct()
  {

  }
  function Logear()
  {

    //cambiar a informacion propia
    $cadenaCnx = "sqlsrv: Server=ROMAN\NATIONALSOFT2023;Database=softrestaurant10pro";
    $user = "sa";
    $password = "estadias"; //cambiar a informacion propia

    $conexion = new PDO($cadenaCnx, $user, $password);

    try {
      $consulta = $conexion->prepare("SELECT * FROM usuarios WHERE usuario=:parametro1 AND nombre=:parametro2");

      $consulta->bindValue(':parametro1', $this->usuario);
      $consulta->bindValue(':parametro2', $this->contraseña);

      $consulta->execute();

      $fila = $consulta->fetch();

      return $fila;
    } catch (PDOException $e) {
      echo "Error en la conexion->" . $e;
    }
  }

}