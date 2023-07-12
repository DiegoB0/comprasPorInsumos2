<?php

require_once("../db/db.php");

class Model
{
  var $usuario;
  var $contraseña;

  function __construct()
  {

  }
  function Logear()
  {

    $conexion = new Conexion();
    $db = $conexion->con();


    try {
      $consulta = $db->prepare("SELECT * FROM usuarios WHERE usuario=:parametro1 AND nombre=:parametro2");

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