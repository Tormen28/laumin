<?php
// eliminar.php

require_once 'conexion.php';

function eliminarRegistro($id, $tabla) {
    $tablasPermitidas = [
        'cuarto', 'primero', 'profesor', 
        'quinto', 'segundo', 'sexto', 'tercero'
    ];
    
    if (!in_array($tabla, $tablasPermitidas)) {
        return false;
    }

    global $conex;
    
    $sql = "DELETE FROM `$tabla` WHERE id = ?";
    $stmt = $conex->prepare($sql);
    
    if (!$stmt) {
        error_log("Error en preparación: " . $conex->error);
        return false;
    }

    $stmt->bind_param('s', $id);
    
    if (!$stmt->execute()) {
        error_log("Error al eliminar: " . $stmt->error);
        return false;
    }

    $stmt->close();
    return true;
}

// Procesar solicitud
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'], $_GET['tabla'])) {
    $id = trim($_GET['id']);
    $tabla = htmlspecialchars($_GET['tabla']);
    
    // Validar y obtener página de origen
    $origen = 'index.php'; // Valor por defecto
    if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) !== false) {
        $origen = $_SERVER['HTTP_REFERER'];
    }
    
    // Eliminar parámetros existentes de la URL
    $origen = preg_replace('/\?.*/', '', $origen);

    if (empty($id) || empty($tabla)) {
        header("Location: $origen?error=datos_invalidos");
        exit;
    }

    if (eliminarRegistro($id, $tabla)) {
        header("Location: $origen?exito=eliminado");
    } else {
        header("Location: $origen?error=eliminacion");
    }
    exit;
    
} else {
    http_response_code(403);
    die('Acceso no autorizado');
}