document.addEventListener('DOMContentLoaded', function(){
	const sidebar = document.getElementById('sidebar');
	const toggleBtn = document.getElementById('toggleBtn');
	const yearEl = document.getElementById('year');

	// restaurar estado
	if (localStorage.getItem('sidebar-collapsed') === 'true') {
		sidebar.classList.add('collapsed');
		toggleBtn.setAttribute('aria-expanded','false');
	} else {
		toggleBtn.setAttribute('aria-expanded','true');
	}

	// toggle
	toggleBtn.addEventListener('click', function(e){
		const collapsed = sidebar.classList.toggle('collapsed');
		localStorage.setItem('sidebar-collapsed', collapsed);
		toggleBtn.setAttribute('aria-expanded', (!collapsed).toString());
		e.stopPropagation();
	});

	// clic fuera para cerrar (en móvil)
	document.addEventListener('click', function(e){
		if (window.innerWidth <= 768) {
			if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
				if (!sidebar.classList.contains('collapsed')) {
					sidebar.classList.add('collapsed');
					localStorage.setItem('sidebar-collapsed', true);
					toggleBtn.setAttribute('aria-expanded','false');
				}
			}
		}
	});

	// accesibilidad: tecla B para alternar
	document.addEventListener('keydown', function(e){
		if (e.key === 'b' || e.key === 'B') {
			toggleBtn.click();
		}
	});

	// año en footer
	if (yearEl) yearEl.textContent = new Date().getFullYear();

	// Permitir el uso de toggleSidebar desde el HTML
	window.toggleSidebar = function() {
		const sidebar = document.querySelector('.sidebar');
		const mainContainer = document.querySelector('.main-container');
		if (sidebar && mainContainer) {
			sidebar.classList.toggle('oculto');
			mainContainer.classList.toggle('sidebar-oculta');
		}
	};
});