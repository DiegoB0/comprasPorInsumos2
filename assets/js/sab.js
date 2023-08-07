function fetch(sab2023) {
	$.ajax({
		url: '../controllers/dias.php',
		type: 'GET',
		data: { sab2023: 'sab2023' },
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
					{ data: 'dia_semana', title: 'Día' },
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
