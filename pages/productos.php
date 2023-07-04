<?php
	require_once('../db/db.php');
	$db = new Conexion();
	$conn = $db->con();
?>
<?php
	$consulta="";
	$stmt = $conn->query($consulta);
	//$registros = $stmt->fetchAll(PDO::FETCH_OBJ);
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
		<title>Productos</title>

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
			</h1>

			<center>
				<a href="../pages/reportes.php">
					<button type="button" class="btn btn-outline-info mt-3">
						Reportes
					</button>
				</a>
			</center>
		</header>

		<div style="height: 30px"></div>

		<!--Ejemplo tabla con DataTables-->
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					<div class="table-responsive">

						<!-- Datepicker -->
					<!-- <div class="input-daterange input-group mb-4" id="datepicker">
						De:
						<input type="text" class="input-sm form-control border-bottom border rounded" name="start" />
						<span class="input-group-addon">a: </span>
						<input type="text" class="input-sm form-control border rounded" name="end" />
					</div> -->

						<table
							id="example"
							class="table table-striped table-bordered"
							cellspacing="0"
							width="100%">
							<thead>
								<tr>
									<th>Producto</th>
									<th>Unidad</th>
									<th>Porción</th>
									<th>Cantidad Venida</th>
									<th>Insumo Utilizado</th>
								</tr>
							</thead>
							<tbody>
								<!-- <?php foreach($registros as $fila) : ?>
								<tr>
									<td><?php echo $fila->descripcion ?></td>
									<td><?php echo $fila->costo ?></td>
									<td><?php echo $fila->cantidadcomprada ?></td>
									<td><?php echo $fila->rendimiento ?></td>
									<td><?php echo $fila->cantidadcocido ?></td>
									<td><?php echo $fila->ventas ?></td>	
								</tr> -->
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
