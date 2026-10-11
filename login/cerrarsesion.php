<?php
session_name("lau");
session_start();

// Destruir completamente la sesión
$_SESSION = array();
session_unset();
session_destroy();

// Eliminar cookies de sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Headers adicionales para prevenir caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Redirigir
header("Location: ../index.php");
exit();
?>