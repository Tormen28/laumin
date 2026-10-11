<?php
session_start();
require 'preguntas_predefinidas.php';

if (!isset($_SESSION['cedula'])) {
    header("Location: recuperar.php");
    exit();
}
$cedula = $_SESSION['cedula'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registrar Preguntas de Seguridad</title>
    <link rel="stylesheet" href="../styless.css">
</head>
<body>
    <form action="guardar_preguntas.php" method="POST">
        <h2>Preguntas de Seguridad para <?= htmlspecialchars($cedula) ?></h2>

        <?php for($i = 1; $i <= 3; $i++): ?>
            <div class="input-wrapper" >
            <label>Pregunta <?= $i ?>:</label>
            <select name="pregunta<?= $i ?>" required class="select-pregunta">
                <option value="">Selecciona una pregunta</option>
                <?php foreach($preguntas_predefinidas as $pregunta): ?>
                    <option value="<?= htmlspecialchars($pregunta) ?>"><?= $pregunta ?></option>
                <?php endforeach; ?>
            </select>
            
            <label>Respuesta <?= $i ?>:</label>
            <input type="text" name="respuesta<?= $i ?>" required>
        </div>
        <?php endfor; ?>

        <input class="btn" type="submit" name="submit" value="Enviar">
        </form>

    <script>
        // Validar que no se repitan preguntas
        document.querySelector('form').addEventListener('submit', function(e) {
            const selects = document.querySelectorAll('.select-pregunta');
            const valores = new Set();
            
            selects.forEach(select => {
                if(select.value) {
                    if(valores.has(select.value)) {
                        e.preventDefault();
                        alert('¡No puedes seleccionar la misma pregunta más de una vez!');
                        return;
                    }
                    valores.add(select.value);
                }
            });
        });
    </script>
</body>
</html>