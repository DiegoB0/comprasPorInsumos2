$(document).ready(function () {
	$('.dropdown-item').click(function (e) {
		e.preventDefault(); // Evita el comportamiento predeterminado del enlace

		var page = $(this).data('page'); // Obtiene el valor del atributo data-page del enlace

		// Realiza la solicitud Ajax para obtener el contenido de la página correspondiente
		$.ajax({
			url: page + '.html',
			type: 'GET',
			dataType: 'html',
			success: function (response) {
				$('.content').html(response); // Actualiza el contenido de la sección 'content' con el resultado de la solicitud Ajax
			},
		});
	});
});

function salir() {
	location.href = '../index.html';
}
