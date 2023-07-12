<?php

require_once('../db/loscorrales.php');


class Registro
{

	function __construct()
	{

	}

	function InsertData()
	{
		$conexion = new Corrales();
		$db = $conexion->con();

		try {

			$nombre=$_POST["nombre"];
			$pass=$_POST["pass"];
			$email=$_POST["email"];
			$priv=$_POST["priv"];

			$consulta = $db->prepare("INSERT INTO usuarios(nombre, pass, email, privilegio) 
			VALUES (:nombre, (SELECT dbo.fun_encriptar(:pass)), :email, :priv)");
			//$consulta = $db->prepare("INSERT INTO usuarios(nombre, pass, email, privilegio) 
			//VALUES (:nombre, :pass, :email, :priv)");
			$consulta->bindParam(':nombre', $nombre, PDO::PARAM_STR);
			$consulta->bindParam(':pass', $pass, PDO::PARAM_STR);
			$consulta->bindParam(':email', $email, PDO::PARAM_STR);
			$consulta->bindParam(':priv', $priv, PDO::PARAM_STR);

			$consulta->execute();

			$fila = $consulta->fetch();

			return $fila;

		} catch (PDOException $e) {
			echo "Error en la consulta->" . $e;
		}
	}

}

?>