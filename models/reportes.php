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
					
						CASE WHEN m.fecha IS NOT NULL THEN (SELECT DATENAME(WEEKDAY, m.fecha))
		   					 ELSE '-' END AS dia_semana,
					
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
							MAX(fecha) as fecha,
							SUM(CASE WHEN cantidad > 0 THEN cantidad ELSE 0 END) AS cantidad_cocido,
							SUM(CASE WHEN cantidad < 0 THEN cantidad ELSE 0 END) AS ventas,
							SUM(cantidad) AS inventario_final
						FROM movsinv
						GROUP BY idinsumo
					) m ON i.idinsumo = m.idinsumo
					LEFT JOIN insumospresentaciones ip ON i.idinsumo = ip.idinsumo AND ip.idinsumospresentaciones = c.idinsumo
				");

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
					
						CASE WHEN m.fecha IS NOT NULL THEN (SELECT DATENAME(WEEKDAY, m.fecha))
		   					 ELSE '-' END AS dia_semana,
					
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
							MAX(fecha) as fecha,
							SUM(CASE WHEN cantidad > 0 THEN cantidad ELSE 0 END) AS cantidad_cocido,
							SUM(CASE WHEN cantidad < 0 THEN cantidad ELSE 0 END) AS ventas,
							SUM(cantidad) AS inventario_final
						FROM movsinv
						WHERE fecha >= '$start_date' AND fecha < DATEADD(day, 1, '$end_date')
						GROUP BY idinsumo
					) m ON i.idinsumo = m.idinsumo
					LEFT JOIN insumospresentaciones ip ON i.idinsumo = ip.idinsumo AND ip.idinsumospresentaciones = c.idinsumo
				");

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


	function fetchAsada()
	{

		$conexion = new Conexion();
		$db = $conexion->con();

		try {
			$query = $db->prepare(
				"SELECT
				c.idproducto,
				p.descripcion,
				i.unidad,
				c.cantidad AS porcion,
				c.cantidad_vendida,
		  
				CASE WHEN c.cantidad IS NOT NULL 
				THEN c.cantidad * c.cantidad_vendida ELSE 0 
				END AS insumo_utilizado
		  
				FROM productos p
				LEFT JOIN (
					SELECT c.idproducto, c.cantidad, c.idinsumo, COUNT(c.idproducto) AS cantidad_vendida
					FROM costos c 
					LEFT JOIN cheqdet cq ON c.idproducto = cq.idproducto
					WHERE c.idinsumo = 001001
					GROUP BY c.idproducto, c.cantidad, c.idinsumo
				)c ON p.idproducto = c.idproducto
				LEFT JOIN (
					SELECT idinsumo, unidad FROM insumos WHERE idinsumo = 001001
				)i ON c.idinsumo = i.idinsumo
				WHERE p.descripcion LIKE '%'+'ASADA'+'%'
				
				
				UNION ALL
				
				SELECT
					' ' as idproducto,
					' CARNE ASADA ' as descripcion,
					'TOTAL' as unidad,
					SUM(cantidad) as porcion,
					SUM(sum_productos) AS cantidad_vendida,
					SUM(CASE WHEN cantidad IS NOT NULL
						THEN cantidad * sum_productos ELSE 0
						END) AS insumo_utilizado
				FROM (
					SELECT c.idproducto, COUNT(c.idproducto) AS sum_productos, c.cantidad
					FROM costos c 
					LEFT JOIN cheqdet cq ON c.idproducto = cq.idproducto
					WHERE c.idinsumo = 001001
					GROUP BY c.idproducto, c.cantidad
				) as dk
			");

			$query->execute();

			$fila = $query->fetchAll();

			return $fila;

		} catch (PDOException $e) {
			echo "Error en la consulta->" . $e;
		}

	}


	function fetchPastor()
	{

		$conexion = new Conexion();
		$db = $conexion->con();

		try {
			$query = $db->prepare(
				"SELECT
				c.idproducto,
				p.descripcion,
				i.unidad,
				c.cantidad AS porcion,
				c.cantidad_vendida,
		  
				CASE WHEN c.cantidad IS NOT NULL 
				THEN c.cantidad * c.cantidad_vendida ELSE 0 
				END AS insumo_utilizado
		  
				FROM productos p
				LEFT JOIN (
					SELECT c.idproducto, c.cantidad, c.idinsumo, COUNT(c.idproducto) AS cantidad_vendida
					FROM costos c 
					LEFT JOIN cheqdet cq ON c.idproducto = cq.idproducto
					WHERE c.idinsumo = 001003 --CAMBIO AQUI
					GROUP BY c.idproducto, c.cantidad, c.idinsumo
				)c ON p.idproducto = c.idproducto
				LEFT JOIN (
					SELECT idinsumo, unidad FROM insumos WHERE idinsumo = 001003 --CAMBIO ACA
				)i ON c.idinsumo = i.idinsumo
				WHERE p.descripcion LIKE '%'+'PASTOR'+'%' --OTRO CAMBIO AQUI
				
				
				UNION ALL
				
				SELECT
					' ' as idproducto,
					' CARNE AL PASTOR ' as descripcion, --CAMBIECITO AQUI
					'TOTAL' as unidad,
					SUM(cantidad) as porcion,
					SUM(sum_productos) AS cantidad_vendida,
					SUM(CASE WHEN cantidad IS NOT NULL
						THEN cantidad * sum_productos ELSE 0
						END) AS insumo_utilizado
				FROM (
					SELECT c.idproducto, COUNT(c.idproducto) AS sum_productos, c.cantidad
					FROM costos c 
					LEFT JOIN cheqdet cq ON c.idproducto = cq.idproducto
					WHERE c.idinsumo = 001003 --ULTIMO CAMBIO ACA
					GROUP BY c.idproducto, c.cantidad
				) as dk
			");

			$query->execute();

			$fila = $query->fetchAll();

			return $fila;

		} catch (PDOException $e) {
			echo "Error en la consulta->" . $e;
		}

	}

	function fetchArrachera()
	{

		$conexion = new Conexion();
		$db = $conexion->con();

		try {
			$query = $db->prepare(
				"SELECT
				c.idproducto,
				p.descripcion,
				i.unidad,
				c.cantidad AS porcion,
				c.cantidad_vendida,
		  
				CASE WHEN c.cantidad IS NOT NULL 
				THEN c.cantidad * c.cantidad_vendida ELSE 0 
				END AS insumo_utilizado
		  
				FROM productos p
				LEFT JOIN (
					SELECT c.idproducto, c.cantidad, c.idinsumo, COUNT(c.idproducto) AS cantidad_vendida
					FROM costos c 
					LEFT JOIN cheqdet cq ON c.idproducto = cq.idproducto
					WHERE c.idinsumo = 001002
					GROUP BY c.idproducto, c.cantidad, c.idinsumo
				)c ON p.idproducto = c.idproducto
				LEFT JOIN (
					SELECT idinsumo, unidad FROM insumos WHERE idinsumo = 001002
				)i ON c.idinsumo = i.idinsumo
				WHERE p.descripcion LIKE '%'+'ARRACHERA'+'%'
				
				
				UNION ALL
				
				SELECT
					' ' as idproducto,
					' CARNE ARRACHERA ' as descripcion,
					'TOTAL' as unidad,
					SUM(cantidad) as porcion,
					SUM(sum_productos) AS cantidad_vendida,
					SUM(CASE WHEN cantidad IS NOT NULL
						THEN cantidad * sum_productos ELSE 0
						END) AS insumo_utilizado
				FROM (
					SELECT c.idproducto, COUNT(c.idproducto) AS sum_productos, c.cantidad
					FROM costos c 
					LEFT JOIN cheqdet cq ON c.idproducto = cq.idproducto
					WHERE c.idinsumo = 001002
					GROUP BY c.idproducto, c.cantidad
				) as dk
				");

			$query->execute();

			$fila = $query->fetchAll();

			return $fila;

		} catch (PDOException $e) {
			echo "Error en la consulta->" . $e;
		}

	}

	function fetchAguacate()
	{

		$conexion = new Conexion();
		$db = $conexion->con();

		try {
			$query = $db->prepare(
				"SELECT
				c.idproducto,
				p.descripcion,
				i.unidad,
				c.cantidad AS porcion,
				c.cantidad_vendida,
		  
				CASE WHEN c.cantidad IS NOT NULL 
				THEN c.cantidad * c.cantidad_vendida ELSE 0 
				END AS insumo_utilizado
		  
				FROM productos p
				LEFT JOIN (
					SELECT c.idproducto, c.cantidad, c.idinsumo, COUNT(c.idproducto) AS cantidad_vendida
					FROM costos c 
					LEFT JOIN cheqdet cq ON c.idproducto = cq.idproducto
					WHERE c.idinsumo = 003001 --CAMBIO AQUI
					GROUP BY c.idproducto, c.cantidad, c.idinsumo
				)c ON p.idproducto = c.idproducto
				LEFT JOIN (
					SELECT idinsumo, unidad FROM insumos WHERE idinsumo = 003001 --CAMBIO ACA
				)i ON c.idinsumo = i.idinsumo
				WHERE c.idinsumo = 003001 --OTRO CAMBIO AQUI
				
				
				UNION ALL
				
				SELECT
					' ' as idproducto,
					' AGUACATE ' as descripcion, --CAMBIECITO AQUI
					'TOTAL' as unidad,
					SUM(cantidad) as porcion,
					SUM(sum_productos) AS cantidad_vendida,
					SUM(CASE WHEN cantidad IS NOT NULL
						THEN cantidad * sum_productos ELSE 0
						END) AS insumo_utilizado
				FROM (
					SELECT c.idproducto, COUNT(c.idproducto) AS sum_productos, c.cantidad
					FROM costos c 
					LEFT JOIN cheqdet cq ON c.idproducto = cq.idproducto
					WHERE c.idinsumo = 003001 --ULTIMO CAMBIO ACA
					GROUP BY c.idproducto, c.cantidad
				) as dk
				");

			$query->execute();

			$fila = $query->fetchAll();

			return $fila;

		} catch (PDOException $e) {
			echo "Error en la consulta->" . $e;
		}

	}

	function fetchAjo()
	{

		$conexion = new Conexion();
		$db = $conexion->con();

		try {
			$query = $db->prepare(
				"SELECT
				c.idproducto,
				p.descripcion,
				i.unidad,
				c.cantidad AS porcion,
				c.cantidad_vendida,
		  
				CASE WHEN c.cantidad IS NOT NULL 
				THEN c.cantidad * c.cantidad_vendida ELSE 0 
				END AS insumo_utilizado
		  
				FROM productos p
				LEFT JOIN (
					SELECT c.idproducto, c.cantidad, c.idinsumo, COUNT(c.idproducto) AS cantidad_vendida
					FROM costos c 
					LEFT JOIN cheqdet cq ON c.idproducto = cq.idproducto
					WHERE c.idinsumo = 003002 --CAMBIO AQUI
					GROUP BY c.idproducto, c.cantidad, c.idinsumo
				)c ON p.idproducto = c.idproducto
				LEFT JOIN (
					SELECT idinsumo, unidad FROM insumos WHERE idinsumo = 003002 --CAMBIO ACA
				)i ON c.idinsumo = i.idinsumo
				WHERE c.idinsumo = 003002 --OTRO CAMBIO AQUI
				
				
				UNION ALL
				
				SELECT
					' ' as idproducto,
					' AJO ' as descripcion, --CAMBIECITO AQUI
					'TOTAL' as unidad,
					SUM(cantidad) as porcion,
					SUM(sum_productos) AS cantidad_vendida,
					SUM(CASE WHEN cantidad IS NOT NULL
						THEN cantidad * sum_productos ELSE 0
						END) AS insumo_utilizado
				FROM (
					SELECT c.idproducto, COUNT(c.idproducto) AS sum_productos, c.cantidad
					FROM costos c 
					LEFT JOIN cheqdet cq ON c.idproducto = cq.idproducto
					WHERE c.idinsumo = 003002 --ULTIMO CAMBIO ACA
					GROUP BY c.idproducto, c.cantidad
				) as dk
				");

			$query->execute();

			$fila = $query->fetchAll();

			return $fila;

		} catch (PDOException $e) {
			echo "Error en la consulta->" . $e;
		}

	}

	function fetchApio()
	{

		$conexion = new Conexion();
		$db = $conexion->con();

		try {
			$query = $db->prepare(
				"SELECT
				c.idproducto,
				p.descripcion,
				i.unidad,
				c.cantidad AS porcion,
				c.cantidad_vendida,
		  
				CASE WHEN c.cantidad IS NOT NULL 
				THEN c.cantidad * c.cantidad_vendida ELSE 0 
				END AS insumo_utilizado
		  
				FROM productos p
				LEFT JOIN (
					SELECT c.idproducto, c.cantidad, c.idinsumo, COUNT(c.idproducto) AS cantidad_vendida
					FROM costos c 
					LEFT JOIN cheqdet cq ON c.idproducto = cq.idproducto
					WHERE c.idinsumo = 003003 --CAMBIO AQUI
					GROUP BY c.idproducto, c.cantidad, c.idinsumo
				)c ON p.idproducto = c.idproducto
				LEFT JOIN (
					SELECT idinsumo, unidad FROM insumos WHERE idinsumo = 003003 --CAMBIO ACA
				)i ON c.idinsumo = i.idinsumo
				WHERE c.idinsumo = 003003 --OTRO CAMBIO AQUI
				
				
				UNION ALL
				
				SELECT
					' ' as idproducto,
					' APIO ' as descripcion, --CAMBIECITO AQUI
					'TOTAL' as unidad,
					SUM(cantidad) as porcion,
					SUM(sum_productos) AS cantidad_vendida,
					SUM(CASE WHEN cantidad IS NOT NULL
						THEN cantidad * sum_productos ELSE 0
						END) AS insumo_utilizado
				FROM (
					SELECT c.idproducto, COUNT(c.idproducto) AS sum_productos, c.cantidad
					FROM costos c 
					LEFT JOIN cheqdet cq ON c.idproducto = cq.idproducto
					WHERE c.idinsumo = 003003 --ULTIMO CAMBIO ACA
					GROUP BY c.idproducto, c.cantidad
				) as dk
				");

			$query->execute();

			$fila = $query->fetchAll();

			return $fila;

		} catch (PDOException $e) {
			echo "Error en la consulta->" . $e;
		}

	}

	function fetchChile()
	{

		$conexion = new Conexion();
		$db = $conexion->con();

		try {
			$query = $db->prepare(
				"SELECT
				c.idproducto,
				p.descripcion,
				i.unidad,
				c.cantidad AS porcion,
				c.cantidad_vendida,
		  
				CASE WHEN c.cantidad IS NOT NULL 
				THEN c.cantidad * c.cantidad_vendida ELSE 0 
				END AS insumo_utilizado
		  
				FROM productos p
				LEFT JOIN (
					SELECT c.idproducto, c.cantidad, c.idinsumo, COUNT(c.idproducto) AS cantidad_vendida
					FROM costos c 
					LEFT JOIN cheqdet cq ON c.idproducto = cq.idproducto
					WHERE c.idinsumo = 003004 --CAMBIO AQUI
					GROUP BY c.idproducto, c.cantidad, c.idinsumo
				)c ON p.idproducto = c.idproducto
				LEFT JOIN (
					SELECT idinsumo, unidad FROM insumos WHERE idinsumo = 003004 --CAMBIO ACA
				)i ON c.idinsumo = i.idinsumo
				WHERE c.idinsumo = 003004 --OTRO CAMBIO AQUI
				
				
				UNION ALL
				
				SELECT
					' ' as idproducto,
					' CHILE ' as descripcion, --CAMBIECITO AQUI
					'TOTAL' as unidad,
					SUM(cantidad) as porcion,
					SUM(sum_productos) AS cantidad_vendida,
					SUM(CASE WHEN cantidad IS NOT NULL
						THEN cantidad * sum_productos ELSE 0
						END) AS insumo_utilizado
				FROM (
					SELECT c.idproducto, COUNT(c.idproducto) AS sum_productos, c.cantidad
					FROM costos c 
					LEFT JOIN cheqdet cq ON c.idproducto = cq.idproducto
					WHERE c.idinsumo = 003004 --ULTIMO CAMBIO ACA
					GROUP BY c.idproducto, c.cantidad
				) as dk
				");

			$query->execute();

			$fila = $query->fetchAll();

			return $fila;

		} catch (PDOException $e) {
			echo "Error en la consulta->" . $e;
		}

	}

	function fetchCebolla()
	{

		$conexion = new Conexion();
		$db = $conexion->con();

		try {
			$query = $db->prepare(
				"SELECT
				c.idproducto,
				p.descripcion,
				i.unidad,
				c.cantidad AS porcion,
				c.cantidad_vendida,
		  
				CASE WHEN c.cantidad IS NOT NULL 
				THEN c.cantidad * c.cantidad_vendida ELSE 0 
				END AS insumo_utilizado
		  
				FROM productos p
				LEFT JOIN (
					SELECT c.idproducto, c.cantidad, c.idinsumo, COUNT(c.idproducto) AS cantidad_vendida
					FROM costos c 
					LEFT JOIN cheqdet cq ON c.idproducto = cq.idproducto
					WHERE c.idinsumo = 003005 --CAMBIO AQUI
					GROUP BY c.idproducto, c.cantidad, c.idinsumo
				)c ON p.idproducto = c.idproducto
				LEFT JOIN (
					SELECT idinsumo, unidad FROM insumos WHERE idinsumo = 003005 --CAMBIO ACA
				)i ON c.idinsumo = i.idinsumo
				WHERE c.idinsumo = 003005 --OTRO CAMBIO AQUI
				
				
				UNION ALL
				
				SELECT
					' ' as idproducto,
					' CEBOLLA ' as descripcion, --CAMBIECITO AQUI
					'TOTAL' as unidad,
					SUM(cantidad) as porcion,
					SUM(sum_productos) AS cantidad_vendida,
					SUM(CASE WHEN cantidad IS NOT NULL
						THEN cantidad * sum_productos ELSE 0
						END) AS insumo_utilizado
				FROM (
					SELECT c.idproducto, COUNT(c.idproducto) AS sum_productos, c.cantidad
					FROM costos c 
					LEFT JOIN cheqdet cq ON c.idproducto = cq.idproducto
					WHERE c.idinsumo = 003005 --ULTIMO CAMBIO ACA
					GROUP BY c.idproducto, c.cantidad
				) as dk
				");

			$query->execute();

			$fila = $query->fetchAll();

			return $fila;

		} catch (PDOException $e) {
			echo "Error en la consulta->" . $e;
		}

	}

	function fetchTomate()
	{

		$conexion = new Conexion();
		$db = $conexion->con();

		try {
			$query = $db->prepare(
				"SELECT
				c.idproducto,
				p.descripcion,
				i.unidad,
				c.cantidad AS porcion,
				c.cantidad_vendida,
		  
				CASE WHEN c.cantidad IS NOT NULL 
				THEN c.cantidad * c.cantidad_vendida ELSE 0 
				END AS insumo_utilizado
		  
				FROM productos p
				LEFT JOIN (
					SELECT c.idproducto, c.cantidad, c.idinsumo, COUNT(c.idproducto) AS cantidad_vendida
					FROM costos c 
					LEFT JOIN cheqdet cq ON c.idproducto = cq.idproducto
					WHERE c.idinsumo = 003006 --CAMBIO AQUI
					GROUP BY c.idproducto, c.cantidad, c.idinsumo
				)c ON p.idproducto = c.idproducto
				LEFT JOIN (
					SELECT idinsumo, unidad FROM insumos WHERE idinsumo = 003006 --CAMBIO ACA
				)i ON c.idinsumo = i.idinsumo
				WHERE c.idinsumo = 003006 --OTRO CAMBIO AQUI
				
				
				UNION ALL
				
				SELECT
					' ' as idproducto,
					' TOMATE ' as descripcion, --CAMBIECITO AQUI
					'TOTAL' as unidad,
					SUM(cantidad) as porcion,
					SUM(sum_productos) AS cantidad_vendida,
					SUM(CASE WHEN cantidad IS NOT NULL
						THEN cantidad * sum_productos ELSE 0
						END) AS insumo_utilizado
				FROM (
					SELECT c.idproducto, COUNT(c.idproducto) AS sum_productos, c.cantidad
					FROM costos c 
					LEFT JOIN cheqdet cq ON c.idproducto = cq.idproducto
					WHERE c.idinsumo = 003006 --ULTIMO CAMBIO ACA
					GROUP BY c.idproducto, c.cantidad
				) as dk
				");

			$query->execute();

			$fila = $query->fetchAll();

			return $fila;

		} catch (PDOException $e) {
			echo "Error en la consulta->" . $e;
		}

	}

	function fetchPina()
	{

		$conexion = new Conexion();
		$db = $conexion->con();

		try {
			$query = $db->prepare(
				"SELECT
				c.idproducto,
				p.descripcion,
				i.unidad,
				c.cantidad AS porcion,
				c.cantidad_vendida,
		  
				CASE WHEN c.cantidad IS NOT NULL 
				THEN c.cantidad * c.cantidad_vendida ELSE 0 
				END AS insumo_utilizado
		  
				FROM productos p
				LEFT JOIN (
					SELECT c.idproducto, c.cantidad, c.idinsumo, COUNT(c.idproducto) AS cantidad_vendida
					FROM costos c 
					LEFT JOIN cheqdet cq ON c.idproducto = cq.idproducto
					WHERE c.idinsumo = 003007 --CAMBIO AQUI
					GROUP BY c.idproducto, c.cantidad, c.idinsumo
				)c ON p.idproducto = c.idproducto
				LEFT JOIN (
					SELECT idinsumo, unidad FROM insumos WHERE idinsumo = 003007 --CAMBIO ACA
				)i ON c.idinsumo = i.idinsumo
				WHERE c.idinsumo = 003007 --OTRO CAMBIO AQUI
				
				
				UNION ALL
				
				SELECT
					' ' as idproducto,
					' PIÑA ' as descripcion, --CAMBIECITO AQUI
					'TOTAL' as unidad,
					SUM(cantidad) as porcion,
					SUM(sum_productos) AS cantidad_vendida,
					SUM(CASE WHEN cantidad IS NOT NULL
						THEN cantidad * sum_productos ELSE 0
						END) AS insumo_utilizado
				FROM (
					SELECT c.idproducto, COUNT(c.idproducto) AS sum_productos, c.cantidad
					FROM costos c 
					LEFT JOIN cheqdet cq ON c.idproducto = cq.idproducto
					WHERE c.idinsumo = 003007 --ULTIMO CAMBIO ACA
					GROUP BY c.idproducto, c.cantidad
				) as dk
				");

			$query->execute();

			$fila = $query->fetchAll();

			return $fila;

		} catch (PDOException $e) {
			echo "Error en la consulta->" . $e;
		}

	}

	function fetchLimon()
	{

		$conexion = new Conexion();
		$db = $conexion->con();

		try {
			$query = $db->prepare(
				"SELECT
				c.idproducto,
				p.descripcion,
				i.unidad,
				c.cantidad AS porcion,
				c.cantidad_vendida,
		  
				CASE WHEN c.cantidad IS NOT NULL 
				THEN c.cantidad * c.cantidad_vendida ELSE 0 
				END AS insumo_utilizado
		  
				FROM productos p
				LEFT JOIN (
					SELECT c.idproducto, c.cantidad, c.idinsumo, COUNT(c.idproducto) AS cantidad_vendida
					FROM costos c 
					LEFT JOIN cheqdet cq ON c.idproducto = cq.idproducto
					WHERE c.idinsumo = 003008 --CAMBIO AQUI
					GROUP BY c.idproducto, c.cantidad, c.idinsumo
				)c ON p.idproducto = c.idproducto
				LEFT JOIN (
					SELECT idinsumo, unidad FROM insumos WHERE idinsumo = 003008 --CAMBIO ACA
				)i ON c.idinsumo = i.idinsumo
				WHERE c.idinsumo = 003008 --OTRO CAMBIO AQUI
				
				
				UNION ALL
				
				SELECT
					' ' as idproducto,
					' LIMÓN ' as descripcion, --CAMBIECITO AQUI
					'TOTAL' as unidad,
					SUM(cantidad) as porcion,
					SUM(sum_productos) AS cantidad_vendida,
					SUM(CASE WHEN cantidad IS NOT NULL
						THEN cantidad * sum_productos ELSE 0
						END) AS insumo_utilizado
				FROM (
					SELECT c.idproducto, COUNT(c.idproducto) AS sum_productos, c.cantidad
					FROM costos c 
					LEFT JOIN cheqdet cq ON c.idproducto = cq.idproducto
					WHERE c.idinsumo = 003008 --ULTIMO CAMBIO ACA
					GROUP BY c.idproducto, c.cantidad
				) as dk
				");

			$query->execute();

			$fila = $query->fetchAll();

			return $fila;

		} catch (PDOException $e) {
			echo "Error en la consulta->" . $e;
		}

	}

	function fetchAzucar()
	{

		$conexion = new Conexion();
		$db = $conexion->con();

		try {
			$query = $db->prepare(
				"SELECT
				c.idproducto,
				p.descripcion,
				i.unidad,
				c.cantidad AS porcion,
				c.cantidad_vendida,
		  
				CASE WHEN c.cantidad IS NOT NULL 
				THEN c.cantidad * c.cantidad_vendida ELSE 0 
				END AS insumo_utilizado
		  
				FROM productos p
				LEFT JOIN (
					SELECT c.idproducto, c.cantidad, c.idinsumo, COUNT(c.idproducto) AS cantidad_vendida
					FROM costos c 
					LEFT JOIN cheqdet cq ON c.idproducto = cq.idproducto
					WHERE c.idinsumo = 005001 --CAMBIO AQUI
					GROUP BY c.idproducto, c.cantidad, c.idinsumo
				)c ON p.idproducto = c.idproducto
				LEFT JOIN (
					SELECT idinsumo, unidad FROM insumos WHERE idinsumo = 005001 --CAMBIO ACA
				)i ON c.idinsumo = i.idinsumo
				WHERE c.idinsumo = 005001 --OTRO CAMBIO AQUI
				
				
				UNION ALL
				
				SELECT
					' ' as idproducto,
					' AZÚCAR ' as descripcion, --CAMBIECITO AQUI
					'TOTAL' as unidad,
					SUM(cantidad) as porcion,
					SUM(sum_productos) AS cantidad_vendida,
					SUM(CASE WHEN cantidad IS NOT NULL
						THEN cantidad * sum_productos ELSE 0
						END) AS insumo_utilizado
				FROM (
					SELECT c.idproducto, COUNT(c.idproducto) AS sum_productos, c.cantidad
					FROM costos c 
					LEFT JOIN cheqdet cq ON c.idproducto = cq.idproducto
					WHERE c.idinsumo = 005001 --ULTIMO CAMBIO ACA
					GROUP BY c.idproducto, c.cantidad
				) as dk
				");

			$query->execute();

			$fila = $query->fetchAll();

			return $fila;

		} catch (PDOException $e) {
			echo "Error en la consulta->" . $e;
		}

	}

	function fetchRefrescos()
	{

		$conexion = new Conexion();
		$db = $conexion->con();

		try {
			$query = $db->prepare(
				"SELECT
				c.idproducto,
				p.descripcion,
				i.unidad,
				c.cantidad AS porcion,
				c.cantidad_vendida,
		  
				CASE WHEN c.cantidad IS NOT NULL 
				THEN c.cantidad * c.cantidad_vendida ELSE 0 
				END AS insumo_utilizado
		  
				FROM productos p
				LEFT JOIN (
					SELECT c.idproducto, c.cantidad, c.idinsumo, COUNT(c.idproducto) AS cantidad_vendida
					FROM costos c 
					LEFT JOIN cheqdet cq ON c.idproducto = cq.idproducto
					WHERE c.idinsumo = 004001 --CAMBIO AQUI
					GROUP BY c.idproducto, c.cantidad, c.idinsumo
				)c ON p.idproducto = c.idproducto
				LEFT JOIN (
					SELECT idinsumo, unidad FROM insumos WHERE idinsumo = 004001 --CAMBIO ACA
				)i ON c.idinsumo = i.idinsumo
				WHERE c.idinsumo = 004001 --OTRO CAMBIO AQUI
				
				
				UNION ALL
				
				SELECT
					' ' as idproducto,
					' REFRESCOS ' as descripcion, --CAMBIECITO AQUI
					'TOTAL' as unidad,
					SUM(cantidad) as porcion,
					SUM(sum_productos) AS cantidad_vendida,
					SUM(CASE WHEN cantidad IS NOT NULL
						THEN cantidad * sum_productos ELSE 0
						END) AS insumo_utilizado
				FROM (
					SELECT c.idproducto, COUNT(c.idproducto) AS sum_productos, c.cantidad
					FROM costos c 
					LEFT JOIN cheqdet cq ON c.idproducto = cq.idproducto
					WHERE c.idinsumo = 004001 --ULTIMO CAMBIO ACA
					GROUP BY c.idproducto, c.cantidad
				) as dk
				");

			$query->execute();

			$fila = $query->fetchAll();

			return $fila;

		} catch (PDOException $e) {
			echo "Error en la consulta->" . $e;
		}

	}

	function fetchMaiz()
	{

		$conexion = new Conexion();
		$db = $conexion->con();

		try {
			$query = $db->prepare(
				"SELECT
				c.idproducto,
				p.descripcion,
				i.unidad,
				c.cantidad AS porcion,
				c.cantidad_vendida,
		  
				CASE WHEN c.cantidad IS NOT NULL 
				THEN c.cantidad * c.cantidad_vendida ELSE 0 
				END AS insumo_utilizado
		  
				FROM productos p
				LEFT JOIN (
					SELECT c.idproducto, c.cantidad, c.idinsumo, COUNT(c.idproducto) AS cantidad_vendida
					FROM costos c 
					LEFT JOIN cheqdet cq ON c.idproducto = cq.idproducto
					WHERE c.idinsumo = 002001 --CAMBIO AQUI
					GROUP BY c.idproducto, c.cantidad, c.idinsumo
				)c ON p.idproducto = c.idproducto
				LEFT JOIN (
					SELECT idinsumo, unidad FROM insumos WHERE idinsumo = 002001 --CAMBIO ACA
				)i ON c.idinsumo = i.idinsumo
				WHERE (p.descripcion LIKE '%'+'ASADA'+'%' 
						OR p.descripcion LIKE '%'+'ARRACHERA'+'%' 
						OR p.descripcion LIKE '%'+'PASTOR'+'%') AND c.idinsumo = 002001 --OTRO CAMBIO AQUI
				
				
				UNION ALL
				
				SELECT
					' ' as idproducto,
					' TORTILLA DE MAIZ POR PZ ' as descripcion, --CAMBIECITO AQUI
					'TOTAL' as unidad,
					SUM(cantidad) as porcion,
					SUM(sum_productos) AS cantidad_vendida,
					SUM(CASE WHEN cantidad IS NOT NULL
						THEN cantidad * sum_productos ELSE 0
						END) AS insumo_utilizado
				FROM (
					SELECT c.idproducto, COUNT(c.idproducto) AS sum_productos, c.cantidad
					FROM costos c 
					LEFT JOIN cheqdet cq ON c.idproducto = cq.idproducto
					WHERE c.idinsumo = 002001 --ULTIMO CAMBIO ACA
					GROUP BY c.idproducto, c.cantidad
				) as dk
				");

			$query->execute();

			$fila = $query->fetchAll();

			return $fila;

		} catch (PDOException $e) {
			echo "Error en la consulta->" . $e;
		}

	}

	function fetchHarina()
	{

		$conexion = new Conexion();
		$db = $conexion->con();

		try {
			$query = $db->prepare(
				"SELECT
				c.idproducto,
				p.descripcion,
				i.unidad,
				c.cantidad AS porcion,
				c.cantidad_vendida,
		  
				CASE WHEN c.cantidad IS NOT NULL 
				THEN c.cantidad * c.cantidad_vendida ELSE 0 
				END AS insumo_utilizado
		  
				FROM productos p
				LEFT JOIN (
					SELECT c.idproducto, c.cantidad, c.idinsumo, COUNT(c.idproducto) AS cantidad_vendida
					FROM costos c 
					LEFT JOIN cheqdet cq ON c.idproducto = cq.idproducto
					WHERE c.idinsumo = 002002 --CAMBIO AQUI
					GROUP BY c.idproducto, c.cantidad, c.idinsumo
				)c ON p.idproducto = c.idproducto
				LEFT JOIN (
					SELECT idinsumo, unidad FROM insumos WHERE idinsumo = 002002 --CAMBIO ACA
				)i ON c.idinsumo = i.idinsumo
				WHERE (p.descripcion LIKE '%'+'ASADA'+'%' 
						OR p.descripcion LIKE '%'+'ARRACHERA'+'%' 
						OR p.descripcion LIKE '%'+'PASTOR'+'%') AND c.idinsumo = 002002 --OTRO CAMBIO AQUI
				
				
				UNION ALL
				
				SELECT
					' ' as idproducto,
					' TORTILLA DE HARINA POR PZ ' as descripcion, --CAMBIECITO AQUI
					'TOTAL' as unidad,
					SUM(cantidad) as porcion,
					SUM(sum_productos) AS cantidad_vendida,
					SUM(CASE WHEN cantidad IS NOT NULL
						THEN cantidad * sum_productos ELSE 0
						END) AS insumo_utilizado
				FROM (
					SELECT c.idproducto, COUNT(c.idproducto) AS sum_productos, c.cantidad
					FROM costos c 
					LEFT JOIN cheqdet cq ON c.idproducto = cq.idproducto
					WHERE c.idinsumo = 002002 --ULTIMO CAMBIO ACA
					GROUP BY c.idproducto, c.cantidad
				) as dk
				");

			$query->execute();

			$fila = $query->fetchAll();

			return $fila;

		} catch (PDOException $e) {
			echo "Error en la consulta->" . $e;
		}

	}

	function fetchPan()
	{

		$conexion = new Conexion();
		$db = $conexion->con();

		try {
			$query = $db->prepare(
				"SELECT
				c.idproducto,
				p.descripcion,
				i.unidad,
				c.cantidad AS porcion,
				c.cantidad_vendida,
		  
				CASE WHEN c.cantidad IS NOT NULL 
				THEN c.cantidad * c.cantidad_vendida ELSE 0 
				END AS insumo_utilizado
		  
				FROM productos p
				LEFT JOIN (
					SELECT c.idproducto, c.cantidad, c.idinsumo, COUNT(c.idproducto) AS cantidad_vendida
					FROM costos c 
					LEFT JOIN cheqdet cq ON c.idproducto = cq.idproducto
					WHERE c.idinsumo = 002003 --CAMBIO AQUI
					GROUP BY c.idproducto, c.cantidad, c.idinsumo
				)c ON p.idproducto = c.idproducto
				LEFT JOIN (
					SELECT idinsumo, unidad FROM insumos WHERE idinsumo = 002003 --CAMBIO ACA
				)i ON c.idinsumo = i.idinsumo
				WHERE (p.descripcion LIKE '%'+'ASADA'+'%' 
						OR p.descripcion LIKE '%'+'ARRACHERA'+'%' 
						OR p.descripcion LIKE '%'+'PASTOR'+'%') AND c.idinsumo = 002003 --OTRO CAMBIO AQUI
				
				
				UNION ALL
				
				SELECT
					' ' as idproducto,
					' PAN DE AGUA POR PZ ' as descripcion, --CAMBIECITO AQUI
					'TOTAL' as unidad,
					SUM(cantidad) as porcion,
					SUM(sum_productos) AS cantidad_vendida,
					SUM(CASE WHEN cantidad IS NOT NULL
						THEN cantidad * sum_productos ELSE 0
						END) AS insumo_utilizado
				FROM (
					SELECT c.idproducto, COUNT(c.idproducto) AS sum_productos, c.cantidad
					FROM costos c 
					LEFT JOIN cheqdet cq ON c.idproducto = cq.idproducto
					WHERE c.idinsumo = 002003 --ULTIMO CAMBIO ACA
					GROUP BY c.idproducto, c.cantidad
				) as dk
				");

			$query->execute();

			$fila = $query->fetchAll();

			return $fila;

		} catch (PDOException $e) {
			echo "Error en la consulta->" . $e;
		}

	}

	function fetchQueso()
	{

		$conexion = new Conexion();
		$db = $conexion->con();

		try {
			$query = $db->prepare(
				"SELECT
				c.idproducto,
				p.descripcion,
				i.unidad,
				c.cantidad AS porcion,
				c.cantidad_vendida,
		  
				CASE WHEN c.cantidad IS NOT NULL 
				THEN c.cantidad * c.cantidad_vendida ELSE 0 
				END AS insumo_utilizado
		  
				FROM productos p
				LEFT JOIN (
					SELECT c.idproducto, c.cantidad, c.idinsumo, COUNT(c.idproducto) AS cantidad_vendida
					FROM costos c 
					LEFT JOIN cheqdet cq ON c.idproducto = cq.idproducto
					WHERE c.idinsumo = 006001 --CAMBIO AQUI
					GROUP BY c.idproducto, c.cantidad, c.idinsumo
				)c ON p.idproducto = c.idproducto
				LEFT JOIN (
					SELECT idinsumo, unidad FROM insumos WHERE idinsumo = 006001 --CAMBIO ACA
				)i ON c.idinsumo = i.idinsumo
				WHERE c.idinsumo = 006001 --OTRO CAMBIO AQUI
				
				
				UNION ALL
				
				SELECT
					' ' as idproducto,
					' QUESO ' as descripcion, --CAMBIECITO AQUI
					'TOTAL' as unidad,
					SUM(cantidad) as porcion,
					SUM(sum_productos) AS cantidad_vendida,
					SUM(CASE WHEN cantidad IS NOT NULL
						THEN cantidad * sum_productos ELSE 0
						END) AS insumo_utilizado
				FROM (
					SELECT c.idproducto, COUNT(c.idproducto) AS sum_productos, c.cantidad
					FROM costos c 
					LEFT JOIN cheqdet cq ON c.idproducto = cq.idproducto
					WHERE c.idinsumo = 006001 --ULTIMO CAMBIO ACA
					GROUP BY c.idproducto, c.cantidad
				) as dk
				");

			$query->execute();

			$fila = $query->fetchAll();

			return $fila;

		} catch (PDOException $e) {
			echo "Error en la consulta->" . $e;
		}

	}

	function fetchLunes2023()
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
			
				CASE 
					WHEN m.fecha IS NOT NULL THEN (SELECT DATENAME(WEEKDAY, m.fecha))
				ELSE '-' END AS dia_semana,
			
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
					WHERE c.fechaaplicacion >= '20230101' AND c.fechaaplicacion <= DATEADD(day, 1, '20231231') 
					AND DATEPART(WEEKDAY, c.fechaaplicacion) = 1
					GROUP BY costo,
						CASE WHEN idinsumo >= '004001' AND idinsumo <= '004999' THEN '004001' ELSE idinsumo END
				) c ON i.idinsumo = c.idinsumo
				LEFT JOIN (
					SELECT 
						idinsumo,
						MAX(fecha) as fecha,
						SUM(CASE WHEN cantidad > 0 THEN cantidad ELSE 0 END) AS cantidad_cocido,
						SUM(CASE WHEN cantidad < 0 THEN cantidad ELSE 0 END) AS ventas,
						SUM(cantidad) AS inventario_final
					FROM movsinv
					WHERE fecha >= '20230101' AND fecha <= DATEADD(day, 1, '20231231')  AND
					DATEPART(WEEKDAY, fecha) = 1
					GROUP BY idinsumo
				) m ON i.idinsumo = m.idinsumo
				LEFT JOIN insumospresentaciones ip ON i.idinsumo = ip.idinsumo AND ip.idinsumospresentaciones = c.idinsumo
				WHERE DATEPART(WEEKDAY, m.fecha) = 1 
				");

			$query->execute();

			$fila = $query->fetchAll();

			return $fila;

		} catch (PDOException $e) {
			echo "Error en la consulta->" . $e;
		}

	}

	function fetchMartes2023()
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
			
				CASE 
					WHEN m.fecha IS NOT NULL THEN (SELECT DATENAME(WEEKDAY, m.fecha))
				ELSE '-' END AS dia_semana,
			
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
					WHERE c.fechaaplicacion >= '20230101' AND c.fechaaplicacion <= DATEADD(day, 1, '20231231') 
					AND DATEPART(WEEKDAY, c.fechaaplicacion) = 2 ---AHI
					GROUP BY costo,
						CASE WHEN idinsumo >= '004001' AND idinsumo <= '004999' THEN '004001' ELSE idinsumo END
				) c ON i.idinsumo = c.idinsumo
				LEFT JOIN (
					SELECT 
						idinsumo,
						MAX(fecha) as fecha,
						SUM(CASE WHEN cantidad > 0 THEN cantidad ELSE 0 END) AS cantidad_cocido,
						SUM(CASE WHEN cantidad < 0 THEN cantidad ELSE 0 END) AS ventas,
						SUM(cantidad) AS inventario_final
					FROM movsinv
					WHERE fecha >= '20230101' AND fecha <= DATEADD(day, 1, '20231231')  AND
					DATEPART(WEEKDAY, fecha) = 2 ---POR ACA
					GROUP BY idinsumo
				) m ON i.idinsumo = m.idinsumo
				LEFT JOIN insumospresentaciones ip ON i.idinsumo = ip.idinsumo AND ip.idinsumospresentaciones = c.idinsumo
				WHERE DATEPART(WEEKDAY, m.fecha) = 2 
				");

			$query->execute();

			$fila = $query->fetchAll();

			return $fila;

		} catch (PDOException $e) {
			echo "Error en la consulta->" . $e;
		}

	}

	function fetchMier2023()
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
			
				CASE 
					WHEN m.fecha IS NOT NULL THEN (SELECT DATENAME(WEEKDAY, m.fecha))
				ELSE '-' END AS dia_semana,
			
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
					WHERE c.fechaaplicacion >= '20230101' AND c.fechaaplicacion <= DATEADD(day, 1, '20231231') 
					AND DATEPART(WEEKDAY, c.fechaaplicacion) = 3 ---AHI
					GROUP BY costo,
						CASE WHEN idinsumo >= '004001' AND idinsumo <= '004999' THEN '004001' ELSE idinsumo END
				) c ON i.idinsumo = c.idinsumo
				LEFT JOIN (
					SELECT 
						idinsumo,
						MAX(fecha) as fecha,
						SUM(CASE WHEN cantidad > 0 THEN cantidad ELSE 0 END) AS cantidad_cocido,
						SUM(CASE WHEN cantidad < 0 THEN cantidad ELSE 0 END) AS ventas,
						SUM(cantidad) AS inventario_final
					FROM movsinv
					WHERE fecha >= '20230101' AND fecha <= DATEADD(day, 1, '20231231')  AND
					DATEPART(WEEKDAY, fecha) = 3 ---POR ACA
					GROUP BY idinsumo
				) m ON i.idinsumo = m.idinsumo
				LEFT JOIN insumospresentaciones ip ON i.idinsumo = ip.idinsumo AND ip.idinsumospresentaciones = c.idinsumo
				WHERE DATEPART(WEEKDAY, m.fecha) = 3 
				");

			$query->execute();

			$fila = $query->fetchAll();

			return $fila;

		} catch (PDOException $e) {
			echo "Error en la consulta->" . $e;
		}

	}

	function fetchJueves2023()
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
			
				CASE 
					WHEN m.fecha IS NOT NULL THEN (SELECT DATENAME(WEEKDAY, m.fecha))
				ELSE '-' END AS dia_semana,
			
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
					WHERE c.fechaaplicacion >= '20230101' AND c.fechaaplicacion <= DATEADD(day, 1, '20231231') 
					AND DATEPART(WEEKDAY, c.fechaaplicacion) = 4 ---AHI
					GROUP BY costo,
						CASE WHEN idinsumo >= '004001' AND idinsumo <= '004999' THEN '004001' ELSE idinsumo END
				) c ON i.idinsumo = c.idinsumo
				LEFT JOIN (
					SELECT 
						idinsumo,
						MAX(fecha) as fecha,
						SUM(CASE WHEN cantidad > 0 THEN cantidad ELSE 0 END) AS cantidad_cocido,
						SUM(CASE WHEN cantidad < 0 THEN cantidad ELSE 0 END) AS ventas,
						SUM(cantidad) AS inventario_final
					FROM movsinv
					WHERE fecha >= '20230101' AND fecha <= DATEADD(day, 1, '20231231')  AND
					DATEPART(WEEKDAY, fecha) = 4 ---POR ACA
					GROUP BY idinsumo
				) m ON i.idinsumo = m.idinsumo
				LEFT JOIN insumospresentaciones ip ON i.idinsumo = ip.idinsumo AND ip.idinsumospresentaciones = c.idinsumo
				WHERE DATEPART(WEEKDAY, m.fecha) = 4
				");

			$query->execute();

			$fila = $query->fetchAll();

			return $fila;

		} catch (PDOException $e) {
			echo "Error en la consulta->" . $e;
		}

	}

	function fetchVier2023()
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
			
				CASE 
					WHEN m.fecha IS NOT NULL THEN (SELECT DATENAME(WEEKDAY, m.fecha))
				ELSE '-' END AS dia_semana,
			
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
					WHERE c.fechaaplicacion >= '20230101' AND c.fechaaplicacion <= DATEADD(day, 1, '20231231') 
					AND DATEPART(WEEKDAY, c.fechaaplicacion) = 5 ---AHI
					GROUP BY costo,
						CASE WHEN idinsumo >= '004001' AND idinsumo <= '004999' THEN '004001' ELSE idinsumo END
				) c ON i.idinsumo = c.idinsumo
				LEFT JOIN (
					SELECT 
						idinsumo,
						MAX(fecha) as fecha,
						SUM(CASE WHEN cantidad > 0 THEN cantidad ELSE 0 END) AS cantidad_cocido,
						SUM(CASE WHEN cantidad < 0 THEN cantidad ELSE 0 END) AS ventas,
						SUM(cantidad) AS inventario_final
					FROM movsinv
					WHERE fecha >= '20230101' AND fecha <= DATEADD(day, 1, '20231231')  AND
					DATEPART(WEEKDAY, fecha) = 5 ---POR ACA
					GROUP BY idinsumo
				) m ON i.idinsumo = m.idinsumo
				LEFT JOIN insumospresentaciones ip ON i.idinsumo = ip.idinsumo AND ip.idinsumospresentaciones = c.idinsumo
				WHERE DATEPART(WEEKDAY, m.fecha) = 5 
				");

			$query->execute();

			$fila = $query->fetchAll();

			return $fila;

		} catch (PDOException $e) {
			echo "Error en la consulta->" . $e;
		}

	}

	function fetchSab2023()
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
			
				CASE 
					WHEN m.fecha IS NOT NULL THEN (SELECT DATENAME(WEEKDAY, m.fecha))
				ELSE '-' END AS dia_semana,
			
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
					WHERE c.fechaaplicacion >= '20230101' AND c.fechaaplicacion <= DATEADD(day, 1, '20231231') 
					AND DATEPART(WEEKDAY, c.fechaaplicacion) = 6 ---AHI
					GROUP BY costo,
						CASE WHEN idinsumo >= '004001' AND idinsumo <= '004999' THEN '004001' ELSE idinsumo END
				) c ON i.idinsumo = c.idinsumo
				LEFT JOIN (
					SELECT 
						idinsumo,
						MAX(fecha) as fecha,
						SUM(CASE WHEN cantidad > 0 THEN cantidad ELSE 0 END) AS cantidad_cocido,
						SUM(CASE WHEN cantidad < 0 THEN cantidad ELSE 0 END) AS ventas,
						SUM(cantidad) AS inventario_final
					FROM movsinv
					WHERE fecha >= '20230101' AND fecha <= DATEADD(day, 1, '20231231')  AND
					DATEPART(WEEKDAY, fecha) = 6 ---POR ACA
					GROUP BY idinsumo
				) m ON i.idinsumo = m.idinsumo
				LEFT JOIN insumospresentaciones ip ON i.idinsumo = ip.idinsumo AND ip.idinsumospresentaciones = c.idinsumo
				WHERE DATEPART(WEEKDAY, m.fecha) = 6 
				");

			$query->execute();

			$fila = $query->fetchAll();

			return $fila;

		} catch (PDOException $e) {
			echo "Error en la consulta->" . $e;
		}

	}

	function fetchDom2023()
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
			
				CASE 
					WHEN m.fecha IS NOT NULL THEN (SELECT DATENAME(WEEKDAY, m.fecha))
				ELSE '-' END AS dia_semana,
			
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
					WHERE c.fechaaplicacion >= '20230101' AND c.fechaaplicacion <= DATEADD(day, 1, '20231231') 
					AND DATEPART(WEEKDAY, c.fechaaplicacion) = 7 ---AHI
					GROUP BY costo,
						CASE WHEN idinsumo >= '004001' AND idinsumo <= '004999' THEN '004001' ELSE idinsumo END
				) c ON i.idinsumo = c.idinsumo
				LEFT JOIN (
					SELECT 
						idinsumo,
						MAX(fecha) as fecha,
						SUM(CASE WHEN cantidad > 0 THEN cantidad ELSE 0 END) AS cantidad_cocido,
						SUM(CASE WHEN cantidad < 0 THEN cantidad ELSE 0 END) AS ventas,
						SUM(cantidad) AS inventario_final
					FROM movsinv
					WHERE fecha >= '20230101' AND fecha <= DATEADD(day, 1, '20231231')  AND
					DATEPART(WEEKDAY, fecha) = 7 ---POR ACA
					GROUP BY idinsumo
				) m ON i.idinsumo = m.idinsumo
				LEFT JOIN insumospresentaciones ip ON i.idinsumo = ip.idinsumo AND ip.idinsumospresentaciones = c.idinsumo
				WHERE DATEPART(WEEKDAY, m.fecha) = 7
				");

			$query->execute();

			$fila = $query->fetchAll();

			return $fila;

		} catch (PDOException $e) {
			echo "Error en la consulta->" . $e;
		}

	}


}