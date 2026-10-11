<?php
session_name("lau");
session_start();

header('Content-Type: application/json');

// 1. Conexión a BD
$host = 'localhost';
$usuario = 'root';
$password = '';
$base_datos = 'asistencia_lau';

$conn = new mysqli($host, $usuario, $password, $base_datos);

if ($conn->connect_error) {
    echo json_encode([
        'tipo' => 'error',
        'mensaje' => '❌ Error de conexión a BD'
    ]);
    exit();
}

// 2. Procesar asistencia
if(isset($_GET['cedula_escolar'])) {
    $cedula_escolar = intval($_GET['cedula_escolar']);
    $fecha_actual = date('Y-m-d');

    try {
        // Verificar duplicados
        $check = $conn->prepare("SELECT id_asistencia FROM asistencias 
                               WHERE cedula_escolar = ? AND fecha_asistencia = ?");
        $check->bind_param("is", $cedula_escolar, $fecha_actual);
        
        if(!$check->execute()) {
            throw new Exception("Error en verificación: ".$conn->error);
        }
        
        if($check->get_result()->num_rows > 0) {
            echo json_encode([
                'tipo' => 'warning',
                'mensaje' => '⚠️ Asistencia ya registrada hoy'
            ]);
        } else {
            // Insertar registro
            $insert = $conn->prepare("INSERT INTO asistencias (cedula_escolar, fecha_asistencia) 
                                    VALUES (?, ?)");
            $insert->bind_param("is", $cedula_escolar, $fecha_actual);
            
            if($insert->execute()) {
                echo json_encode([
                    'tipo' => 'success',
                    'mensaje' => '✅ Asistencia registrada!'
                ]);
            } else {
                throw new Exception("Error al insertar: ".$conn->error);
            }
        }
        
    } catch(Exception $e) {
        echo json_encode([
            'tipo' => 'error',
            'mensaje' => '❌ Error: '.$e->getMessage()
        ]);
    }
    exit();
}