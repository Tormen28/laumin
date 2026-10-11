<?php
session_name("lau");
session_start();
require 'login/funciones.php';

// Redirección si ya hay sesión activa
if (isset($_SESSION['id'])) {
    switch ($_SESSION['rol']) {
        case 'admin':
            header('Location: inventario/admin/index.php');
            break;
        case 'visitante':
            header('Location: inventario/index.php');
            break;
        case 'profesor':
            $grado = $_SESSION['grado_asignado'];
            $seccion = $_SESSION['seccion_asignada'];
            header("Location: inventario/profesores/grado_{$grado}{$seccion}.php");
            break;
    }
    exit();
}

$iniciosession = new inicio_sesion();

// Procesar formulario de login
if (isset($_POST['submit'])) {
    $usuario = $_POST['nombreUsuario__Correo'];
    $password = $_POST['contraseña'];

    $result = $iniciosession->inicioSession($usuario, $password);

    switch ($result) {
        case 1: // Login exitoso
            $_SESSION['id'] = $iniciosession->id;
            $_SESSION['rol'] = $iniciosession->rol;
            
            // Redirección basada en rol
            switch ($_SESSION['rol']) {
                case 'admin':
                    header('Location: inventario/admin/index.php');
                    break;
                case 'visitante':
                    header('Location: inventario/index.php');
                    break;
                case 'profesor':
                    $_SESSION['grado_asignado'] = $iniciosession->grado_asignado;
                    $_SESSION['seccion_asignada'] = $iniciosession->seccion_asignada;
                    $url = "inventario/profesores/grado_{$_SESSION['grado_asignado']}{$_SESSION['seccion_asignada']}.php";
                    header("Location: $url");
                    break;
            }
            exit();
            break;
            
        case 10:
            echo "<script>alert('Contraseña incorrecta');</script>";
            break;
            
        case 100:
            echo "<script>alert('Usuario no registrado');</script>";
            break;
    }
}
?>

<!-- 4. HTML del formulario (no cambia) -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Sistema Escolar</title>
    <link rel="stylesheet" href="styless.css">
</head>
<body>
    <form method="post">
        <h2>SISTEMA ESCOLAR</h2>
        <p>Inicio de Sesión</p>

        <div class="input-wrapper">
            <input type="text" name="nombreUsuario__Correo" required placeholder="Ingrese con su Nombre" autocomplete="off">
            <img class="input-icon" src="images/name.svg" alt="">
        </div>

        <div class="input-wrapper">
            <input type="password" name="contraseña" placeholder="Contraseña" required>
            <img class="input-icon" src="images/password.svg" alt="">
        </div>

        <div class="input-wrapper">
            <button type="submit" name="submit">Ingresar</button>
        </div>
        
        <div class="link">
            <a href="recuperar_clave/formulario_olvido.php" class="register-link">¿Olvidaste tu contraseña?</a><br> <br>
            <a href="registro.php" class="register-link">Registrarse</a>
        </div>
    </form>
</body>
</html>