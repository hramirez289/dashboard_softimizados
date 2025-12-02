<?php

include('database.php');

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Mostrar mensaje de error si existe
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dashboard Profesional</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/stylelogin.css">
</head>
<body>

    <div class="login-container">
        <form id="login-form" class="login-form" method="POST" action="process_login.php">
            <h2>Login</h2>
            <p class="form-description">Bienvenido. Por favor, introduce tus credenciales.</p>

            <?php if($error): ?>
                <div class="error-message" style="color: #e74c3c; background: #fadbd8; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <div class="input-group">
                <label for="user">Usuario</label>
                <input type="text" id="user" name="user" placeholder="Ingrese su nombre de usuario" required>
            </div>

            <div class="input-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <input type="submit" class="login-button" name="btningresar" value="Iniciar Sesión">

            <div class="form-footer">
                <a href="#">¿Olvidaste tu contraseña?</a>
            </div>
        </form>
    </div>

</body>
</html>
