// Obtener elementos del DOM
const sidebar = document.querySelector('.sidebar');
const content = document.querySelector('.content');
const sidebarToggle = document.getElementById('sidebar-toggle');

// Agregar evento de clic al botón para hacer más pequeño el sidebar
sidebarToggle.addEventListener('click', (e) => {
	sidebar.classList.toggle('sidebar-shrunk');
});

// Obtener todos los elementos <a> de la sidebar
const sidebarLinks = document.querySelectorAll('.sidebar-menu li a');

// Agregar evento de clic a cada enlace de la sidebar
sidebarLinks.forEach((link) => {
	link.addEventListener('click', (e) => {
		sidebarLinks.forEach((item) => {
			// Eliminar la clase 'active' de todos los enlaces
			item.classList.remove('active');
		});
		// Agregar la clase 'active' al enlace clicado
		e.target.classList.add('active');
	});
});

// Agregar evento de clic a los enlaces que tienen una clase 'dropdown-toggle'
const dropdownToggleLinks = document.querySelectorAll(
	'.sidebar-link.dropdown-toggle'
);
dropdownToggleLinks.forEach((toggleLink) => {
	toggleLink.addEventListener('click', (e) => {
		e.preventDefault();
		const dropdownMenu = toggleLink.nextElementSibling;
		dropdownMenu.classList.toggle('open');
		toggleLink.classList.toggle('open');
	});
});