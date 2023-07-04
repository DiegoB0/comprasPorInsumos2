<?php

    class insumos_fecha
    {

        function __construct()
        {

        }

        function i_fecha()
        {
            require_once('../db/db.php');
            $db = new Conexion();
            $conn = $db->con();

            try
            {
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $result = $conn->query("SELECT i.idinsumo, i.descripcion,
                SUM(CASE
                WHEN c.costo > 0 AND m.idconcepto = 'EPC' THEN c.costo
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
                WHERE (m.fecha BETWEEN :start_date AND :end_date)
                GROUP BY i.idinsumo, i.descripcion");
                $stmt->execute(array(':start_date' => $_POST['start_date'], ':end_date' => $_POST['end_date']));

            }
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<!-- Required meta tags -->
		<meta charset="utf-8" />
		<meta
			name="viewport"
			content="width=device-width, initial-scale=1, shrink-to-fit=no"
		/>
		<link rel="shortcut icon" href="#" />
		<title>Reporte de Compras por Insumo</title>

		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="../libs/bootstrap/css/bootstrap.min.css" />
		<!-- CSS personalizado -->
		<link rel="stylesheet" href="../assets/css/main.css" />

		<!--datables CSS básico-->
		<link
			rel="stylesheet"
			type="text/css"
			href="../libs/datatables/datatables.min.css"
		/>
		<!--datables estilo bootstrap 4 CSS-->
		<link
			rel="stylesheet"
			type="text/css"
			href="../libs/datatables/DataTables-1.10.18/css/dataTables.bootstrap4.min.css"
		/>

		<!--font awesome con CDN-->
		<link
			rel="stylesheet"
			href="https://use.fontawesome.com/releases/v5.8.2/css/all.css"
			integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay"
			crossorigin="anonymous"
		/>
		<!-- Datepicker style -->
	<link rel="stylesheet" href="../libs/bootstrap-datepicker/css/bootstrap-datepicker.css" />
	</head>

	<body>
		<header>
			<h1 class="text-center text-secondary mt-3">
				Reporte de Compras por Insumo en Taquerías
			</h1>

			<center><a href="../pages/productos.php"><button type="button" class="btn btn-outline-info mt-3">Productos</button></a></center>
		</header>

		<div style="height: 30px"></div>

		<!--Ejemplo tabla con DataTables-->
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					<div class="table-responsive">

						<!-- Datepicker -->
					<div class="input-daterange input-group mb-4" id="datepicker">
						De:
						<input type="text" class="input-sm form-control border-bottom border rounded" id="start_date" />
						<span class="input-group-addon">a: </span>
						<input type="text" class="input-sm form-control border rounded" id="end_date" />
					</div>

						<table
							id="example"
							class="table table-striped table-bordered"
							cellspacing="0"
							width="100%">
							<thead>
								<tr>
									<th>Insumo</th>
									<th>Costo</th>
									<th>Cantidad Comprada</th>
									<th>Rendimiento</th>
									<th>Cantidad Cocido</th>
									<th>Venta</th>
									<th>Inventario Final</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($registros as $fila) : ?>
								<tr>
									<td><?php echo $fila->descripcion ?></td>
									<td><?php echo $fila->costo ?></td>
									<td><?php echo $fila->cantidadcomprada ?></td>
									<td><?php echo $fila->rendimiento ?></td>
									<td><?php echo $fila->cantidadcocido ?></td>
									<td><?php echo $fila->ventas ?></td>
									<td><?php echo $fila->inventario_final ?></td>	
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>

		<!-- jQuery, Popper.js, Bootstrap JS -->
		<script src="../libs/jquery/jquery-3.3.1.min.js"></script>
		<script src="../libs/popper/popper.min.js"></script>
		<script src="../libs/bootstrap/js/bootstrap.min.js"></script>

		<!-- datatables JS -->
		<script
			type="text/javascript"
			src="../libs/datatables/datatables.min.js"
		></script>

		<!-- para usar botones en datatables JS -->
		<script src="../libs/datatables/Buttons-1.5.6/js/dataTables.buttons.min.js"></script>
		<script src="../libs/datatables/JSZip-2.5.0/jszip.min.js"></script>
		<script src="../libs/datatables/pdfmake-0.1.36/pdfmake.min.js"></script>
		<script src="../libs/datatables/pdfmake-0.1.36/vfs_fonts.js"></script>
		<script src="../libs/datatables/Buttons-1.5.6/js/buttons.html5.min.js"></script>

		<!-- código JS propio-->
		<script type="text/javascript" src="../assets/js/main.js"></script>
		<script src="../libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
	</body>
</html>
