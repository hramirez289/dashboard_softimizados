<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>Sidebar</title>
	<link rel="stylesheet" href="assets/css/stylesidebar.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />

</head>
<body>
	<div class="app">
		<aside class="sidebar" id="sidebar" aria-label="Barra lateral">
			<div class="brand">
				<button id="toggleBtn" class="toggle-btn" aria-expanded="true" aria-label="Alternar barra lateral">
					<span class="hamburger" aria-hidden="true"></span>
				</button>
				<a href="#" class="logo">Menu</a>
			</div>

			<nav class="menu" role="navigation">
				<a href="#" class="menu-item dashboard active" role="link">
                    <span class="material-symbols-outlined">bar_chart</span>
					<span class="label">Dashboard</span>
				</a>
				<a href="#" class="menu-item products" role="link">
					<span class="material-symbols-outlined">inventory_2</span>
					<span class="label">Productos</span>
				</a>
			</nav>

			<div class="sidebar-footer">
				<a href="logout.php" class="menu-item" role="link">
					<span class="material-symbols-outlined">logout</span>
					<span class="label">Cerrar sesión</span>
				</a>
			</div>
		</aside>

	</div>

	<script src="assets/js/scriptsidebar.js" defer></script>
</body>
</html>