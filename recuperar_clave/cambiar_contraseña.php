<?php
session_start();
if (!isset($_SESSION['autorizado']) || !$_SESSION['autorizado']) {
    header("Location: formulario_olvido.php");
    exit();
}

// Generar token CSRF
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña</title>
    <link rel="stylesheet" href="../styless.css">
    <style>
        .contenedor {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ddd;
        }
        .error { color: red; }
    </style>
</head>
<body>
    <div class="contenedor">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form action="actualizar_contraseña.php" method="post">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            
            <div>
                <label>Nueva Contraseña:</label>
                <input type="password" name="nueva_contraseña" required>
            </div>
            
            <div>
                <label>Confirmar Contraseña:</label>
                <input type="password" name="confirmar_contraseña" required>
            </div>
            
            <input class="btn" type="submit" name="submit" value="Cambiar Contraseña">
            </form>
    </div>
</body>
</html>