function fetch(usuarios) {
	$.ajax({
		url: '../controllers/usuarios.php',
		type: 'GET',
		data: { usuarios: 'usuarios' },
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
					{ data: 'id', title: 'Id Usuario' },
					{ data: 'nombre', title: 'Nombre' },
					{ data: 'pass', title: 'Clave' },
					{ data: 'email', title: 'E-mail' },
					{ data: 'privilegio', title: 'Privilegio' },
				],
			});
		},
	});
}
fetch();
