<?php
session_start();
require 'db.php';

// Validar CSRF y sesión
if (!isset($_SESSION['cedula']) || 
    !isset($_POST['csrf_token']) || 
    $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header("Location: formulario_olvido.php");
    exit();
}

// Obtener respuestas del usuario
$respuestas = [
    '1' => trim($_POST['respuesta1'] ?? ''),
    '2' => trim($_POST['respuesta2'] ?? ''),
    '3' => trim($_POST['respuesta3'] ?? '')
];

try {
    // Consultar respuestas hasheadas
    $stmt = $conn->prepare("SELECT respuesta1, respuesta2, respuesta3 
                          FROM preguntas_seguridad 
                          WHERE cedula = ?");
    $stmt->bind_param("s", $_SESSION['cedula']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Usuario no registrado");
    }
    
    $respuestas_db = $result->fetch_assoc();

    // Verificar cada respuesta con password_verify()
    $correctas = true;
    foreach (['1', '2', '3'] as $key) {
        if (!password_verify($respuestas[$key], $respuestas_db['respuesta'.$key])) {
            $correctas = false;
            break;
        }
    }

    // Redirección
    if ($correctas) {
        $_SESSION['autorizado'] = true;
        header("Location: cambiar_contraseña.php");
    } else {
        $_SESSION['error'] = "Respuestas incorrectas";
        header("Location: preguntas_seguridad.php");
    }
    exit();

} catch (Exception $e) {
    error_log($e->getMessage());
    $_SESSION['error'] = "Error en el sistema";
    header("Location: formulario_olvido.php");
    exit();
}
?>