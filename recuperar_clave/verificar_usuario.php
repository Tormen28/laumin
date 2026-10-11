<?php
session_start();
require 'db.php';

// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener cédula formateada correctamente
    $cedula = filter_input(INPUT_POST, 'cedula', FILTER_SANITIZE_STRING);
    
    // Validar formato de cédula
    if (!preg_match('/^[VE]-\d{1,3}(?:\.\d{3})*$/', $cedula)) {
        die("Formato de cédula inválido");
    }

    // Verificar existencia en la base de datos
    $stmt = $conn->prepare("SELECT cedula FROM preguntas_seguridad WHERE cedula = ?");
    $stmt->bind_param("s", $cedula);
    $stmt->execute();
    
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['cedula'] = $cedula;
        header("Location: preguntas_seguridad.php");
        exit();
    } else {
        $_SESSION['cedula'] = $cedula;
        header("Location: formulario_preguntas.php");
        exit();
    }
}
?>