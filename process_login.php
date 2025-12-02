<?php

include('database.php');

if (!empty($_POST['btningresar'])) {

    
    if (empty($_POST['user']) || empty($_POST['password'])) {
        $_SESSION['error'] = "Por favor complete todos los campos.";
        header("Location: login.php");
        exit();
    }

    $usuario = trim($_POST['user']);
    $contraseña = trim($_POST['password']);

    
    $stmt = $mysqli->prepare("SELECT Id_usuario, Nombre, Password FROM usuario WHERE Nombre = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado && $resultado->num_rows > 0) {
        $row = $resultado->fetch_assoc();

        
        if ($contraseña === $row['Password']) {
            $_SESSION['user_id'] = $row['Id_usuario'];
            $_SESSION['nombre'] = $row['Nombre'];
            header("Location: index.php");
            exit();
        }

        $_SESSION['error'] = "Contraseña incorrecta.";
    } else {
        $_SESSION['error'] = "Usuario no encontrado.";
    }

    header("Location: login.php");
    exit();
} else {
    $_SESSION['error'] = "Acceso no permitido.";
    header("Location: login.php");
    exit();
}
?>
