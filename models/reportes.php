<?php

require_once('../db/db.php');


class Reporte
{



  function __construct()
  {

  }

  function fetchData()
  {
    $conexion = new Conexion();
    $db = $conexion->con();

    try {
      $query = $db->prepare("SELECT i.descripcion,
	SUM(CASE
	  WHEN m.costo > 0 AND m.cantidad > 0 AND m.idcompra IS NOT NULL THEN m.costo * m.cantidad
	  ELSE 0
	END) AS costo,
	 SUM(CASE
	  WHEN c.cantidad > 0 AND m.idconcepto = 'EPC' THEN c.cantidad
	  ELSE 0
	END) AS cantidadcomprada,
	(SUM(CASE
	  WHEN m.cantidad > 0 THEN m.cantidad
	  ELSE 0
	END)
	/NULLIF(SUM(CASE
	  WHEN c.cantidad > 0 AND m.idconcepto = 'EPC' THEN c.cantidad
	  ELSE 0
	END), 0)) AS rendimiento,
	SUM(CASE
	  WHEN m.cantidad > 0 THEN m.cantidad
	  ELSE 0
	END) AS cantidadcocido,
	SUM(CASE
	  WHEN m.cantidad < 0 THEN m.cantidad
	  ELSE 0
	END) AS ventas,
	(SUM(CASE
	  WHEN m.cantidad > 0 THEN m.cantidad
	  ELSE 0
	END)
	+SUM(CASE
	  WHEN m.cantidad < 0 THEN m.cantidad
	  ELSE 0
	END)) AS inventario_final
  FROM insumos i
  LEFT JOIN movsinv m ON i.idinsumo = m.idinsumo
  LEFT JOIN comprasmovtos c ON i.idinsumo = c.idinsumo
  GROUP BY i.idinsumo, i.descripcion");

      $query->execute();

      $fila = $query->fetchAll();

      return $fila;

    } catch (PDOException $e) {
      echo "Error en la consulta->" . $e;
    }
  }

  function date_range($start_date, $end_date)
  {
    $conexion = new Conexion();
    $db = $conexion->con();

    try {
      $query = $db->prepare("SELECT i.descripcion,
	SUM(CASE
	  WHEN m.costo > 0 AND m.cantidad > 0 AND m.idcompra IS NOT NULL THEN m.costo * m.cantidad
	  ELSE 0
	END) AS costo,
	 SUM(CASE
	  WHEN c.cantidad > 0 AND m.idconcepto = 'EPC' THEN c.cantidad
	  ELSE 0
	END) AS cantidadcomprada,
	(SUM(CASE
	  WHEN m.cantidad > 0 THEN m.cantidad
	  ELSE 0
	END)
	/NULLIF(SUM(CASE
	  WHEN c.cantidad > 0 AND m.idconcepto = 'EPC' THEN c.cantidad
	  ELSE 0
	END), 0)) AS rendimiento,
	SUM(CASE
	  WHEN m.cantidad > 0 THEN m.cantidad
	  ELSE 0
	END) AS cantidadcocido,
	SUM(CASE
	  WHEN m.cantidad < 0 THEN m.cantidad
	  ELSE 0
	END) AS ventas,
	(SUM(CASE
	  WHEN m.cantidad > 0 THEN m.cantidad
	  ELSE 0
	END)
	+SUM(CASE
	  WHEN m.cantidad < 0 THEN m.cantidad
	  ELSE 0
	END)) AS inventario_final
  FROM insumos i
  LEFT JOIN movsinv m ON i.idinsumo = m.idinsumo
  LEFT JOIN comprasmovtos c ON i.idinsumo = c.idinsumo
  WHERE m.fecha > '$start_date' AND m.fecha < '$end_date'
  GROUP BY i.idinsumo, i.descripcion");

      $query->execute();

      $fila = $query->fetchAll();

      return $fila;
    } catch (PDOException $e) {
      echo "Error en la consulta->" . $e;
    }



  }
}