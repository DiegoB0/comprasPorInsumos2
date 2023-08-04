function fetch(pina) {
	$.ajax({
		url: '../controllers/productos.php',
		type: 'GET',
		data: { pina: 'pina' },
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
					{ data: 'descripcion', title: 'Producto' },
					{ data: 'unidad', title: 'Unidad' },
					{ data: 'porcion', title: 'Porción' },
					{ data: 'cantidad_vendida', title: 'Cantidad Vendida' },
					{ data: 'insumo_utilizado', title: 'Insumo Utilizado' },
				],
			});
		},
	});
}
fetch();