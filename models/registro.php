<?php

require_once('../db/db.php');

class Registro
{

	function __construct()
	{

	}

	function InsertData()
	{
		$conexion = new Conexion();
		$db = $conexion->conCorrales();

		try {

			$nombre=$_POST["nombre"];
			$pass=$_POST["pass"];
			$email=$_POST["email"];
			$priv=$_POST["priv"];

			$consulta = $db->prepare("INSERT INTO usuarios(nombre, pass, email, privilegio) 
			VALUES (:nombre, (:nombre, CONVERT(binary, :pass), :email, :priv)");

			$consulta->bindParam(':nombre', $nombre, PDO::PARAM_STR);
			$consulta->bindParam(':pass', $pass, PDO::PARAM_STR);
			$consulta->bindParam(':email', $email, PDO::PARAM_STR);
			$consulta->bindParam(':priv', $priv, PDO::PARAM_STR);

			$consulta->execute();

			return true;

		} catch (PDOException $e) {
			echo "Error en la consulta: " . $e->getMessage();
			return false;
		}
	}
}
?>