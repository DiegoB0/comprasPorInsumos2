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
		url: '../controllers/r.fechas.php',
		type: 'POST',
		data: {
			start_date: start_date,
			end_date: end_date,
		},
		dataType: 'json',
		success: function (data) {
			//Datatable
			console.log(data);
			var i = '1';

			$('#example').DataTable({
				data: data,
				dom: 'Bfrtilp',
				buttons: [
					{
						extend: 'excelHtml5',
						text: '<i class="fas fa-file-excel"></i> ',
						titleAttr: 'Exportar a Excel',
						className: 'btn btn-success',
					},
					{
						extend: 'pdfHtml5',
						text: '<i class="fas fa-file-pdf"></i> ',
						titleAttr: 'Exportar a PDF',
						className: 'btn btn-danger',
					},
					{
						extend: 'print',
						text: '<i class="fa fa-print"></i> ',
						titleAttr: 'Imprimir',
						className: 'btn btn-info',
					},
				],
				responsive: true,
				columns: [
					{ data: 'fecha', title: 'Fecha' },
					{ data: 'descripcion', title: 'Insumo' },
					{ data: 'costo', title: 'Costo' },
					{ data: 'cantidad_comprada', title: 'Cantidad Comprada' },
					{ data: 'rendimiento', title: 'Rendimiento' },
					{ data: 'cantidad_cocido', title: 'Cantidad Cocido' },
					{ data: 'ventas', title: 'Ventas' },
					{ data: 'inventario_final', title: 'Inventario Final' },
				],
			});
		},
	});
}
fetch();

// Filtrar
$(document).on('click', '#filter', function (e) {
	e.preventDefault();

	var start_date = $('#start_date').val();
	var end_date = $('#end_date').val();

	if (start_date == '' || end_date == '') {
		alert('Debe ingresar las fechas');
	} else {
		$('#example').DataTable().destroy();
		fetch(start_date, end_date);
		console.log(start_date, end_date);
	}
});

//Resetear
$(document).on('click', '#reset', function (e) {
	e.preventDefault();

	$('#start_date').val('');
	$('#end_date').val('');

	$('#example').DataTable().destroy();
	fetch();
});
