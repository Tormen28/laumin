<?php
session_start();
require 'db.php';

if (!isset($_SESSION['cedula'])) {
    header("Location: formulario_olvido.php");
    exit();
}

// Generar nuevo token CSRF
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

$cedula = $_SESSION['cedula'];
$stmt = $conn->prepare("SELECT pregunta1, pregunta2, pregunta3 FROM preguntas_seguridad WHERE cedula = ?");
$stmt->bind_param("s", $cedula);
$stmt->execute();
$result = $stmt->get_result();
$preguntas = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../styless.css">
    <title>Preguntas de Seguridad</title>
    <style>
        .error { color: red; }
    </style>
</head>
<body>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="error"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form action="verificar_respuestas.php" method="post">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <div class="titulo">
                <h1>Verificación de seguridad</h1>
            </div>
        
        <?php foreach(['pregunta1', 'pregunta2', 'pregunta3'] as $key => $pregunta): ?>
        <div>
            <label><?= htmlspecialchars($preguntas[$pregunta]) ?></label>
            <input type="text" name="respuesta<?= $key + 1 ?>" required>
        </div>
        <?php endforeach; ?>
        
        <input class="btn" type="submit" name="submit" value="Enviar">
        </form>
</body>
</html>