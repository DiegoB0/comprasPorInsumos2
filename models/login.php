<?php

require_once('../db/loscorrales.php');

class Model
{
  var $email;
  var $pass;

  function __construct()
  {

  }
  function Logear()
  {

    $conexion = new Corrales();
    $db = $conexion->con();


    try {
      $consulta = $db->prepare("SELECT * FROM usuarios WHERE 
      email=:parametro1 AND pass=(SELECT dbo.fun_encriptar(:parametro2))");

      $consulta->bindValue(':parametro1', $this->email);
      $consulta->bindValue(':parametro2', $this->pass);

      $consulta->execute();

      $fila = $consulta->fetch();

      return $fila;
    } catch (PDOException $e) {
      echo "Error en la conexion->" . $e;
    }
  }

}
