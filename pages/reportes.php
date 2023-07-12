<!DOCTYPE html>
<html lang="en">

<head>
	<!-- Required meta tags -->
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
	<link rel="shortcut icon" href="#" />
	<title>Reporte de Compras por Insumo</title>

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="../libs/bootstrap/css/bootstrap.min.css" />
	<!-- CSS personalizado -->
	<link rel="stylesheet" href="../assets/css/reportes.css" />

	<!--datables CSS básico-->
	<link rel="stylesheet" type="text/css" href="../libs/datatables/datatables.min.css" />
	<!--datables estilo bootstrap 4 CSS-->
	<link rel="stylesheet" type="text/css"
		href="../libs/datatables/DataTables-1.10.18/css/dataTables.bootstrap4.min.css" />

	<!-- Datepicker style -->
	<link rel="stylesheet" href="../libs/bootstrap-datepicker/css/bootstrap-datepicker.css" />

	<!--font awesome con CDN-->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css"
		integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous" />
</head>

<body>
	<header>
		<h1 class="text-center text-secondary mt-3">
			Reporte de Compras por Insumo en Taquerías
		</h1>
	</header>
	<div style="height: 50px"></div>

	<!--Ejemplo tabla con DataTables-->
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="table-responsive">
					<!-- Datepicker -->

					<div class="input-daterange input-group mb-4 col-md-12" id="datepicker">
						<div class="col-md-6">
							<div class="input-group mb-4">
								<div class="input-group-prepend">
									<span class="input-group-text bg-info text-white" id="basic-addon1"><i
											class="fas fa-calendar-alt"></i></span>
								</div>
								<input type="text" class="form-control datepicker" placeholder="Fecha Inicial" id="start_date"
									readonly />
							</div>
						</div>

						<div class="col-md-6">
							<div class="input-group mb-4">
								<div class="input-group-prepend">
									<span class="input-group-text bg-info text-white" id="basic-addon1"><i
											class="fas fa-calendar-alt"></i></span>
								</div>
								<input type="text" class="form-control datepicker" placeholder="Fecha Final" id="end_date" readonly />
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="input-group mb-4">
							<button class="btn btn-outline-info btn-sm" id="filter">Filtrar</button>
							<button class="btn btn-outline-warning btn-sm" id="reset">Resetear</button>
						</div>
					</div>
					<!-- Inicia la tabla -->
					<table id="example" class="table table-striped table-bordered estilos-tabla" cellspacing="0" width="100%">
						<thead>
							<tr>
								<th>Insumo</th>
								<th>Costo</th>
								<th>Cantidad Comprada</th>
								<th>Rendimiento</th>
								<th>Cantidad Cocido</th>
								<th>Ventas</th>
								<th>Inventario Final</th>
							</tr>
						</thead>
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
	<script type="text/javascript" src="../libs/datatables/datatables.min.js"></script>

	<!-- datepicker JS -->
	<script src="../libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>

	<!-- Moment JS -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"
		integrity="sha512-CryKbMe7sjSCDPl18jtJI5DR5jtkUWxPXWaLCst6QjH8wxDexfRJic2WRmRXmstr2Y8SxDDWuBO6CQC6IE4KTA=="
		crossorigin="anonymous" referrerpolicy="no-referrer"></script>

	<!-- para usar botones en datatables JS -->
	<script src="../libs/datatables/Buttons-1.5.6/js/dataTables.buttons.min.js"></script>
	<script src="../libs/datatables/JSZip-2.5.0/jszip.min.js"></script>
	<script src="../libs/datatables/pdfmake-0.1.36/pdfmake.min.js"></script>
	<script src="../libs/datatables/pdfmake-0.1.36/vfs_fonts.js"></script>
	<script src="../libs/datatables/Buttons-1.5.6/js/buttons.html5.min.js"></script>

	<!-- código JS propio-->
	<script type="text/javascript" src="../assets/js/main.js"></script>

	<script>
		/* Datepicker js */
		$('#start_date').datepicker({
			format: 'dd/mm/yyyy',
		});
		$('#end_date').datepicker({
			format: 'dd/mm/yyyy',
		});

		//Obtener los datos de los inputs de fecha
		function fetch(start_date, end_date) {
			$.ajax({
				url: '../controllers/reportes.php',
				type: 'POST',
				data: {
					start_date: start_date,
					end_date: end_date,
				},
				dataType: 'json',
				columnDefs: [{
					"defaultContent": "-",
					"targets": "_all"
				}],
				success: function (data) {
					//Datatable
					console.log(data);
					var i = '1';

					$('#example').DataTable({
						'data': data,
						columns: [
							{ data: 'descripcion', title: 'Insumo' },
							{ data: 'costo', title: 'Costo' },
							{ data: 'cantidadcomprada', title: 'Cantidad Comprada' },
							{ data: 'rendimiento', title: 'Rendimiento' },
							{ data: 'cantidadcocido', title: 'Cantidad Cocido' },
							{ data: 'ventas', title: 'Ventas' },
							{ data: 'inventario_final', title: 'Inventario Final' },
						],
					});
				},
			});
		}
		fetch();


		// Filtrar
		$(document).on("click", "#filter", function (e) {
			e.preventDefault();

			var start_date = $("#start_date").val();
			var end_date = $("#end_date").val();

			if (start_date == "" || end_date == "") {
				alert("Debe ingresar las fechas")
			} else {
				$('#example').DataTable().destroy();
				fetch(start_date, end_date);
			}
		})
	</script>
</body>

</html>
