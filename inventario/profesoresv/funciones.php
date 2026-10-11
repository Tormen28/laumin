<?php
require 'conexion.php';

function obtenerEstudiantes($grado_tabla, $seccion) {
    global $conn;
    
    $tablas_validas = ['cuarto', 'primero', 'quinto', 'segundo', 'sexto', 'tercero'];
    
    if(!in_array($grado_tabla, $tablas_validas)) {
        die("Nombre de tabla no válido");
    }
    
    $stmt = $conn->prepare("SELECT * FROM $grado_tabla WHERE seccion = ?");
    $stmt->bind_param("s", $seccion);
    $stmt->execute();
    
    return $stmt->get_result();
}

function registrarAsistencia($datos) {
    global $conn;
    
    $stmt = $conn->prepare("INSERT INTO asistencia 
        (cedula_estudiante, fecha, estado, observaciones) 
        VALUES (?, ?, ?, ?)");
        
    $stmt->bind_param("ssss", 
        $datos['cedula'],
        $datos['fecha'],
        $datos['estado'],
        $datos['observaciones']
    );
    
    return $stmt->execute();
}
function obtenerDatosEstudiante($cedula) {
    global $conn;
    
    $tablas_grados = ['cuarto', 'primero', 'quinto', 'segundo', 'sexto', 'tercero'];
    
    foreach($tablas_grados as $tabla) {
        $stmt = $conn->prepare("SELECT * FROM $tabla WHERE cedula_escolar = ?");
        $stmt->bind_param("s", $cedula);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
    }
    
    return null; // Si no encuentra en ninguna tabla
}
?>