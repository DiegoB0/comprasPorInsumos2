//Obtener los datos de los inputs de fecha
function fetch(proveedores) {
	$.ajax({
		url: '../controllers/reportes_proveedores.php',
		type: 'GET',
		data: {
			
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
					{ data: 'idcompra', title: 'Compra' },
					{ data: 'nombre', title: 'Proveedor' },
					{ data: 'fechaaplicacion', title: 'Fecha' },
					{ data: 'idinsumo', title: 'ID Insumo' },
					{ data: 'descripcion', title: 'Insumo' },
					{ data: 'costo', title: 'Costo' },
					{ data: 'cantidad', title: 'Cantidad' },
                    { data: 'unidad', title: 'Unidad' },
				],
			});
		},
	});
}
fetch();