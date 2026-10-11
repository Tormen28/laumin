<?php
session_start();
require 'db.php';

// Verificar sesión y autorización
if (!isset($_SESSION['autorizado']) || !$_SESSION['autorizado'] || !isset($_SESSION['cedula'])) {
    header("Location: formulario_olvido.php");
    exit();
}

// Validar token CSRF
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Acceso no autorizado");
}

// Validar que las contraseñas coincidan
if ($_POST['nueva_contraseña'] !== $_POST['confirmar_contraseña']) {
    $_SESSION['error'] = "Las contraseñas no coinciden";
    header("Location: cambiar_contraseña.php");
    exit();
}

try {
    // Hashear la nueva contraseña
    $hash_contraseña = password_hash($_POST['nueva_contraseña'], PASSWORD_DEFAULT);
    
    // Actualizar en la base de datos
    $stmt = $conn->prepare("UPDATE profesor SET contraseña = ? WHERE cedula = ?");
    $stmt->bind_param("ss", $hash_contraseña, $_SESSION['cedula']);
    $stmt->execute();

    // Limpiar sesión y redirigir
    session_unset();
    session_destroy();
    header("Location: ../index.php?exito=1");
    exit();

} catch (Exception $e) {
    $_SESSION['error'] = "Error al actualizar la contraseña";
    header("Location: cambiar_contraseña.php");
    exit();
}
?>