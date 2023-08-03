<?php

require_once('../db/db.php');

class Reporte
{

	function __construct()
	{

	}

	function fetchFechas()
	{
		$conexion = new Conexion();
		$db = $conexion->con();

		try {
			$query = $db->prepare(
				"WITH CTE_Movsinv AS (
					SELECT fecha,
						   CONVERT(DATE, fecha) AS fecha_sin_hora,
						   idinsumo,
						   idconcepto,
						   idcompra,
						   cantidad,
						   ROW_NUMBER() OVER (PARTITION BY idinsumo, CONVERT(DATE, fecha) ORDER BY fecha) AS row_num
					FROM movsinv
				),
				CTE_Comprasmovtos AS (
					SELECT idinsumo,
						   costo,
						   cantidad,
						   idcompra,
						   ROW_NUMBER() OVER (PARTITION BY idinsumo ORDER BY idcompra DESC) AS row_num
					FROM comprasmovtos
				),
				CTE_Acumulado AS (
					SELECT c.fecha,
						   c.fecha_sin_hora,
						   c.idinsumo,
						   i.descripcion,
						   c.cantidad,
				
						   CASE 
							   WHEN c.idinsumo = '004001' AND c.cantidad > 0 AND c.cantidad = 6 THEN 
								   (SELECT costo FROM CTE_Comprasmovtos WHERE idcompra = c.idcompra AND idinsumo = '004002')
							   WHEN c.idinsumo = '004001' AND c.cantidad > 0 AND c.cantidad = 12 THEN 
								   (SELECT costo FROM CTE_Comprasmovtos WHERE idcompra = c.idcompra AND idinsumo = '004002')
							   WHEN c.idinsumo = '004001' AND c.cantidad > 0 AND c.cantidad = 18 THEN 
								   (SELECT costo FROM CTE_Comprasmovtos WHERE idcompra = c.idcompra AND idinsumo = '004002')
							   WHEN c.idinsumo = '004001' AND c.cantidad > 0 AND c.cantidad < 6 THEN 
								   (SELECT costo FROM CTE_Comprasmovtos WHERE idcompra = c.idcompra AND idinsumo = '004003')
							   ELSE 
								   (SELECT costo FROM CTE_Comprasmovtos WHERE idinsumo = c.idinsumo AND c.cantidad > 0 AND row_num = 1)
						   END AS costo,
				
						   CASE
							   WHEN c.idinsumo = '004001' AND EXISTS (
								   SELECT cantidad FROM CTE_Comprasmovtos cm 
								   WHERE cm.idinsumo = '004002' AND cm.idcompra = c.idcompra
							   ) THEN 
								   (SELECT cantidad FROM CTE_Comprasmovtos cm 
								   WHERE cm.idinsumo = '004002' AND cm.idcompra = c.idcompra)
							   WHEN c.idinsumo = '004001' AND EXISTS (
								   SELECT cantidad FROM CTE_Comprasmovtos cm
								   WHERE cm.idinsumo = '004003' AND cm.idcompra = c.idcompra
							   ) THEN
								   (SELECT cantidad FROM CTE_Comprasmovtos cm
								   WHERE cm.idinsumo = '004003' AND cm.idcompra = c.idcompra)
							   WHEN c.idconcepto = 'EPC' AND EXISTS (
								   SELECT 1 FROM CTE_Comprasmovtos cm 
								   WHERE cm.idinsumo = c.idinsumo AND cm.idcompra = c.idcompra
							   ) THEN
								   (SELECT cantidad FROM CTE_Comprasmovtos cm 
								   WHERE cm.idinsumo = c.idinsumo AND cm.idcompra = c.idcompra)
							   ELSE 0
						   END AS cantidad_comprada,
				
						   CASE WHEN c.row_num = 1 THEN SUM(c.cantidad) OVER (PARTITION BY c.idinsumo, c.fecha) ELSE 0 END AS acumulado_por_dia,
				
				
						   CASE WHEN c.row_num = 1 THEN (SELECT SUM(cantidad) FROM CTE_Movsinv WHERE idinsumo = c.idinsumo AND fecha_sin_hora <= c.fecha_sin_hora) ELSE 0 END AS inventario_final,
						   
						   (CASE
								WHEN c.cantidad > 0 THEN c.cantidad
								ELSE 0
							END)
						   /NULLIF(
								  (CASE
									   WHEN c.idinsumo = '004001' AND EXISTS (
										   SELECT cantidad FROM CTE_Comprasmovtos cm 
											WHERE cm.idinsumo = '004002' AND cm.idcompra = c.idcompra
										  ) THEN
											(SELECT cantidad FROM CTE_Comprasmovtos cm 
											 WHERE cm.idinsumo = '004002' AND cm.idcompra = c.idcompra)
										WHEN c.idinsumo = '004001' AND EXISTS (
											SELECT cantidad FROM CTE_Comprasmovtos cm
											WHERE cm.idinsumo = '004003' AND cm.idcompra = c.idcompra
										  ) THEN
											(SELECT cantidad FROM CTE_Comprasmovtos cm
											 WHERE cm.idinsumo = '004003' AND cm.idcompra = c.idcompra)
										WHEN c.idconcepto = 'EPC' AND EXISTS (
											SELECT 1 FROM CTE_Comprasmovtos cm 
											WHERE cm.idinsumo = c.idinsumo AND cm.idcompra = c.idcompra
										) THEN
											(SELECT cantidad FROM CTE_Comprasmovtos cm 
											 WHERE cm.idinsumo = c.idinsumo AND cm.idcompra = c.idcompra)
										ELSE 0
								  END), 0) AS rendimiento
				
					FROM CTE_Movsinv c
					LEFT JOIN insumos i ON c.idinsumo = i.idinsumo
				)
				SELECT fecha,
					   idinsumo,
					   descripcion,
					   cantidad,
				
					   CASE WHEN costo > 0 THEN costo ELSE 0 END AS costo,
				
					   cantidad_comprada,
				
					   FORMAT(CASE WHEN rendimiento IS NOT NULL THEN rendimiento ELSE 0 END, 'N4') AS rendimiento,
				
					   CASE WHEN cantidad > 0 THEN cantidad ELSE 0 END AS cantidad_cocido,
					   
					   CASE WHEN cantidad < 0 THEN cantidad ELSE 0 END AS ventas,
					   acumulado_por_dia,
					   inventario_final
				FROM CTE_Acumulado a
				ORDER BY fecha, idinsumo");

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
			$query = $db->prepare(
				"WITH CTE_Movsinv AS (
					SELECT fecha,
						   CONVERT(DATE, fecha) AS fecha_sin_hora,
						   idinsumo,
						   idconcepto,
						   idcompra,
						   cantidad,
						   ROW_NUMBER() OVER (PARTITION BY idinsumo, CONVERT(DATE, fecha) ORDER BY fecha) AS row_num
					FROM movsinv
				),
				CTE_Comprasmovtos AS (
					SELECT idinsumo,
						   costo,
						   cantidad,
						   idcompra,
						   ROW_NUMBER() OVER (PARTITION BY idinsumo ORDER BY idcompra DESC) AS row_num
					FROM comprasmovtos
				),
				CTE_Acumulado AS (
					SELECT c.fecha,
						   c.fecha_sin_hora,
						   c.idinsumo,
						   i.descripcion,
						   c.cantidad,
				
						   CASE 
							   WHEN c.idinsumo = '004001' AND c.cantidad > 0 AND c.cantidad = 6 THEN 
								   (SELECT costo FROM CTE_Comprasmovtos WHERE idcompra = c.idcompra AND idinsumo = '004002')
							   WHEN c.idinsumo = '004001' AND c.cantidad > 0 AND c.cantidad = 12 THEN 
								   (SELECT costo FROM CTE_Comprasmovtos WHERE idcompra = c.idcompra AND idinsumo = '004002')
							   WHEN c.idinsumo = '004001' AND c.cantidad > 0 AND c.cantidad = 18 THEN 
								   (SELECT costo FROM CTE_Comprasmovtos WHERE idcompra = c.idcompra AND idinsumo = '004002')
							   WHEN c.idinsumo = '004001' AND c.cantidad > 0 AND c.cantidad < 6 THEN 
								   (SELECT costo FROM CTE_Comprasmovtos WHERE idcompra = c.idcompra AND idinsumo = '004003')
							   ELSE 
								   (SELECT costo FROM CTE_Comprasmovtos WHERE idinsumo = c.idinsumo AND c.cantidad > 0 AND row_num = 1)
						   END AS costo,
				
						   CASE
							   WHEN c.idinsumo = '004001' AND EXISTS (
								   SELECT cantidad FROM CTE_Comprasmovtos cm 
								   WHERE cm.idinsumo = '004002' AND cm.idcompra = c.idcompra
							   ) THEN 
								   (SELECT cantidad FROM CTE_Comprasmovtos cm 
								   WHERE cm.idinsumo = '004002' AND cm.idcompra = c.idcompra)
							   WHEN c.idinsumo = '004001' AND EXISTS (
								   SELECT cantidad FROM CTE_Comprasmovtos cm
								   WHERE cm.idinsumo = '004003' AND cm.idcompra = c.idcompra
							   ) THEN
								   (SELECT cantidad FROM CTE_Comprasmovtos cm
								   WHERE cm.idinsumo = '004003' AND cm.idcompra = c.idcompra)
							   WHEN c.idconcepto = 'EPC' AND EXISTS (
								   SELECT 1 FROM CTE_Comprasmovtos cm 
								   WHERE cm.idinsumo = c.idinsumo AND cm.idcompra = c.idcompra
							   ) THEN
								   (SELECT cantidad FROM CTE_Comprasmovtos cm 
								   WHERE cm.idinsumo = c.idinsumo AND cm.idcompra = c.idcompra)
							   ELSE 0
						   END AS cantidad_comprada,
				
						   CASE WHEN c.row_num = 1 THEN SUM(c.cantidad) OVER (PARTITION BY c.idinsumo, c.fecha) ELSE 0 END AS acumulado_por_dia,
				
				
						   CASE WHEN c.row_num = 1 THEN (SELECT SUM(cantidad) FROM CTE_Movsinv WHERE idinsumo = c.idinsumo AND fecha_sin_hora <= c.fecha_sin_hora) ELSE 0 END AS inventario_final,
						   
						   (CASE
								WHEN c.cantidad > 0 THEN c.cantidad
								ELSE 0
							END)
						   /NULLIF(
								  (CASE
									   WHEN c.idinsumo = '004001' AND EXISTS (
										   SELECT cantidad FROM CTE_Comprasmovtos cm 
											WHERE cm.idinsumo = '004002' AND cm.idcompra = c.idcompra
										  ) THEN
											(SELECT cantidad FROM CTE_Comprasmovtos cm 
											 WHERE cm.idinsumo = '004002' AND cm.idcompra = c.idcompra)
										WHEN c.idinsumo = '004001' AND EXISTS (
											SELECT cantidad FROM CTE_Comprasmovtos cm
											WHERE cm.idinsumo = '004003' AND cm.idcompra = c.idcompra
										  ) THEN
											(SELECT cantidad FROM CTE_Comprasmovtos cm
											 WHERE cm.idinsumo = '004003' AND cm.idcompra = c.idcompra)
										WHEN c.idconcepto = 'EPC' AND EXISTS (
											SELECT 1 FROM CTE_Comprasmovtos cm 
											WHERE cm.idinsumo = c.idinsumo AND cm.idcompra = c.idcompra
										) THEN
											(SELECT cantidad FROM CTE_Comprasmovtos cm 
											 WHERE cm.idinsumo = c.idinsumo AND cm.idcompra = c.idcompra)
										ELSE 0
								  END), 0) AS rendimiento
				
					FROM CTE_Movsinv c
					LEFT JOIN insumos i ON c.idinsumo = i.idinsumo
				)
				SELECT fecha,
					   idinsumo,
					   descripcion,
					   cantidad,
				
					   CASE WHEN costo > 0 THEN costo ELSE 0 END AS costo,
				
					   cantidad_comprada,
				
					   FORMAT(CASE WHEN rendimiento IS NOT NULL THEN rendimiento ELSE 0 END, 'N4') AS rendimiento,
				
					   CASE WHEN cantidad > 0 THEN cantidad ELSE 0 END AS cantidad_cocido,
					   
					   CASE WHEN cantidad < 0 THEN cantidad ELSE 0 END AS ventas,
					   acumulado_por_dia,
					   inventario_final
				FROM CTE_Acumulado a
				WHERE fecha >= '$start_date' AND fecha < DATEADD(day, 1, '$end_date')
				ORDER BY fecha, idinsumo");

			$query->execute();

			$fila = $query->fetchAll();

			return $fila;
		} catch (PDOException $e) {
			echo "Error en la consulta->" . $e;
		}

	}
}