function fetch(proveedores) {
	$.ajax({
		url: '../controllers/reportes.php',
		type: 'GET',
		data: { proveedores: 'proveedores' },
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
					{ data: 'idcompra', title: 'Compra' },
					{ data: 'nombre', title: 'Proveedor' },
					{ data: 'fechaaplicacion', title: 'Fecha' },
					{ data: 'idinsumo_emergencia', title: 'ID Insumo' },
					{ data: 'descripcion_emergencia', title: 'Insumo' },
					{ data: 'costo', title: 'Costo' },
					{ data: 'cantidad_1', title: 'Cantidad' },
					{ data: 'unidad_emergencia', title: 'Unidad' },
				],
			});
		},
	});
}
fetch();
