<?php
header('Content-Type: application/json');

$host = "localhost";
$user = "root";
$pass = "";
$db = "asistencia_lau"; // Asegúrate que coincide con tu BD

$grado = $_GET['grado'] ?? '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta corregida con alias para grado
    $stmt = $conn->prepare("SELECT cedula, nombres, apellidos FROM profesor WHERE grado = :grado");
    $stmt->bindParam(':grado', $grado);
    $stmt->execute();
    
    $profesores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if(empty($profesores)) {
        echo json_encode(['error' => 'No hay profesores para este grado']);
    } else {
        echo json_encode($profesores);
    }

} catch(PDOException $e) {
    echo json_encode(['error' => "Error de base de datos: " . $e->getMessage()]);
}
exit();
?>