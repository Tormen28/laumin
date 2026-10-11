<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['cedula'])) {
    $cedula = $_SESSION['cedula'];
    
    // Hashear respuestas
    $data = [
        'pregunta1' => $_POST['pregunta1'],
        'respuesta1' => password_hash($_POST['respuesta1'], PASSWORD_DEFAULT),
        'pregunta2' => $_POST['pregunta2'],
        'respuesta2' => password_hash($_POST['respuesta2'], PASSWORD_DEFAULT),
        'pregunta3' => $_POST['pregunta3'],
        'respuesta3' => password_hash($_POST['respuesta3'], PASSWORD_DEFAULT)
    ];

    $stmt = $conn->prepare("INSERT INTO preguntas_seguridad 
                          (cedula, pregunta1, respuesta1, pregunta2, respuesta2, pregunta3, respuesta3)
                          VALUES (?, ?, ?, ?, ?, ?, ?)
                          ON DUPLICATE KEY UPDATE
                          pregunta1 = VALUES(pregunta1),
                          respuesta1 = VALUES(respuesta1),
                          pregunta2 = VALUES(pregunta2),
                          respuesta2 = VALUES(respuesta2),
                          pregunta3 = VALUES(pregunta3),
                          respuesta3 = VALUES(respuesta3)");

    $stmt->bind_param("sssssss", 
        $cedula,
        $data['pregunta1'],
        $data['respuesta1'],
        $data['pregunta2'],
        $data['respuesta2'],
        $data['pregunta3'],
        $data['respuesta3']
    );

    $stmt->execute();
    header("Location: ../index.php");
    exit();
}
?>