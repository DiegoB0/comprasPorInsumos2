<?php

require_once('../db/db.php');

class Reporte
{

	function __construct()
	{

	}

	function fetchInsumos()
	{
		$conexion = new Conexion();
		$db = $conexion->con();

		try {
			$query = $db->prepare(
				"SELECT
				CASE 
					WHEN c.idinsumo >= '004001' AND c.idinsumo <= '004999' THEN '004001'
					ELSE i.idinsumo
				END AS idinsumo,
				i.descripcion,
				
				CASE WHEN c.costo IS NOT NULL THEN c.costo ELSE 0 END AS costo,
				
				COALESCE(c.cantidad_comprada, 0) AS cantidad_comprada,
				
				CASE WHEN ip.rendimiento IS NOT NULL THEN	
					(CASE WHEN c.costo = 13.33 AND i.descripcion = 'REFRESCOS' THEN (SELECT rendimiento FROM insumospresentaciones WHERE rendimiento = 1 AND idinsumo = 004001)
						 WHEN c.costo = 80 AND i.descripcion = 'REFRESCOS' THEN (SELECT rendimiento FROM insumospresentaciones WHERE rendimiento = 6 AND idinsumo = 004001)
						 WHEN c.costo = 230 AND i.descripcion = 'REFRESCOS' THEN (SELECT rendimiento FROM insumospresentaciones WHERE rendimiento = 24 AND idinsumo = 004001)
					ELSE ip.rendimiento END)
				ELSE 0 END AS rendimiento,
				
				COALESCE(m.cantidad_cocido, 0) AS cantidad_cocido,
				COALESCE(m.ventas, 0) AS ventas,
				COALESCE(m.inventario_final, 0) AS inventario_final
			FROM insumos i
			LEFT JOIN (
				SELECT cm.costo,
						CASE 
							WHEN idinsumo >= '004001' AND idinsumo <= '004999' THEN '004001'
							ELSE idinsumo
						END AS idinsumo,
						SUM(cm.cantidad) AS cantidad_comprada
			
				FROM comprasmovtos cm LEFT JOIN compras c ON cm.idcompra = c.idcompra
				GROUP BY costo,
					CASE WHEN idinsumo >= '004001' AND idinsumo <= '004999' THEN '004001' ELSE idinsumo END
			) c ON i.idinsumo = c.idinsumo
			LEFT JOIN (
				SELECT 
					idinsumo,
					SUM(CASE WHEN cantidad > 0 THEN cantidad ELSE 0 END) AS cantidad_cocido,
					SUM(CASE WHEN cantidad < 0 THEN cantidad ELSE 0 END) AS ventas,
					SUM(cantidad) AS inventario_final
				FROM movsinv
				GROUP BY idinsumo
			) m ON i.idinsumo = m.idinsumo
			LEFT JOIN insumospresentaciones ip ON i.idinsumo = ip.idinsumo AND ip.idinsumospresentaciones = c.idinsumo");

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
				"SELECT
				CASE 
					WHEN c.idinsumo >= '004001' AND c.idinsumo <= '004999' THEN '004001'
					ELSE i.idinsumo
				END AS idinsumo,
				i.descripcion,
				
				CASE WHEN c.costo IS NOT NULL THEN c.costo ELSE 0 END AS costo,
				
				COALESCE(c.cantidad_comprada, 0) AS cantidad_comprada,
				
				CASE WHEN ip.rendimiento IS NOT NULL THEN	
					(CASE WHEN c.costo = 13.33 AND i.descripcion = 'REFRESCOS' THEN (SELECT rendimiento FROM insumospresentaciones WHERE rendimiento = 1 AND idinsumo = 004001)
						 WHEN c.costo = 80 AND i.descripcion = 'REFRESCOS' THEN (SELECT rendimiento FROM insumospresentaciones WHERE rendimiento = 6 AND idinsumo = 004001)
						 WHEN c.costo = 230 AND i.descripcion = 'REFRESCOS' THEN (SELECT rendimiento FROM insumospresentaciones WHERE rendimiento = 24 AND idinsumo = 004001)
					ELSE ip.rendimiento END)
				ELSE 0 END AS rendimiento,
				
				COALESCE(m.cantidad_cocido, 0) AS cantidad_cocido,
				COALESCE(m.ventas, 0) AS ventas,
				COALESCE(m.inventario_final, 0) AS inventario_final
			FROM insumos i
			LEFT JOIN (
				SELECT cm.costo,
						CASE 
							WHEN idinsumo >= '004001' AND idinsumo <= '004999' THEN '004001'
							ELSE idinsumo
						END AS idinsumo,
						SUM(cm.cantidad) AS cantidad_comprada
			
				FROM comprasmovtos cm LEFT JOIN compras c ON cm.idcompra = c.idcompra
				WHERE c.fechaaplicacion >= '$start_date' AND c.fechaaplicacion <= DATEADD(day, 1, '$end_date')
				GROUP BY costo,
					CASE WHEN idinsumo >= '004001' AND idinsumo <= '004999' THEN '004001' ELSE idinsumo END
			) c ON i.idinsumo = c.idinsumo
			LEFT JOIN (
				SELECT 
					idinsumo,
					SUM(CASE WHEN cantidad > 0 THEN cantidad ELSE 0 END) AS cantidad_cocido,
					SUM(CASE WHEN cantidad < 0 THEN cantidad ELSE 0 END) AS ventas,
					SUM(cantidad) AS inventario_final
				FROM movsinv
				WHERE fecha >= '$start_date' AND fecha < DATEADD(day, 1, '$end_date')
				GROUP BY idinsumo
			) m ON i.idinsumo = m.idinsumo
			LEFT JOIN insumospresentaciones ip ON i.idinsumo = ip.idinsumo AND ip.idinsumospresentaciones = c.idinsumo");

			$query->execute();

			$fila = $query->fetchAll();

			return $fila;
		} catch (PDOException $e) {
			echo "Error en la consulta->" . $e;
		}

	}

	function fetchProveedores()
	{

		$conexion = new Conexion();
		$db = $conexion->con();

		try {
			$query = $db->prepare(
				"SELECT
				ROW_NUMBER() OVER (ORDER BY cm.idcompra) AS id_emergencia,
				i.idinsumo,
				CASE
					WHEN cm.idinsumo >= 004001 AND cm.idinsumo <= 004999 THEN 004001
					ELSE i.idinsumo
				END AS idinsumo_emergencia,
				CASE 
					WHEN cm.idinsumo >= 004001 AND cm.idinsumo <= 004999 THEN 'REFRESCOS' 
					ELSE i.descripcion 
				END AS descripcion_emergencia,
				cm.costo,
				cm.cantidad AS cantidad_1,
				CASE 
					WHEN cm.idinsumo >= 004001 AND cm.idinsumo <= 004999 THEN 'PZA' 
					ELSE i.unidad 
				END AS unidad_emergencia,
			
				cm.idcompra,
				p.nombre,
				c.fechaaplicacion
			
			FROM comprasmovtos cm
			LEFT JOIN insumos i ON cm.idinsumo = i.idinsumo
			LEFT JOIN compras c ON c.idcompra = cm.idcompra
			LEFT JOIN proveedores p ON c.idproveedor = p.idproveedor
			");

			$query->execute();

			$fila = $query->fetchAll();

			return $fila;

		} catch (PDOException $e) {
			echo "Error en la consulta->" . $e;
		}

	}
}