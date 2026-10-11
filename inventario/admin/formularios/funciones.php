<?php
/**
 * Archivo de funciones para el sistema de asistencia escolar
 * Contiene funciones utilitarias para interactuar con la base de datos y validar formularios
 */

// Variables globales de conexión
$host = 'localhost';
$usuario = 'root';
$password = '';
$base_datos = 'asistencia_lau';

/**
 * Crea una conexión a la base de datos
 * @return mysqli Objeto de conexión a la base de datos
 */
function conectar_db() {
    global $host, $usuario, $password, $base_datos;
    
    $conexion = new mysqli($host, $usuario, $password, $base_datos);
    
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }
    
    // Establecer charset para evitar problemas con caracteres especiales
    $conexion->set_charset("utf8");
    
    return $conexion;
}

/**
 * Escapa cadenas para evitar inyección SQL
 * @param string $texto Texto a escapar
 * @return string Texto escapado
 */
function limpiar_cadena($texto) {
    $conexion = conectar_db();
    $texto = $conexion->real_escape_string($texto);
    $conexion->close();
    return $texto;
}

/**
 * Valida que una cédula no esté duplicada en una tabla específica
 * @param string $cedula Cédula a validar
 * @param string $tabla Tabla donde buscar
 * @param string $id_excluir ID a excluir de la validación (para ediciones)
 * @return bool True si la cédula ya existe, False si no existe
 */
function cedula_existe($cedula, $tabla, $id_excluir = null) {
    $conexion = conectar_db();
    
    $sql = "SELECT id FROM $tabla WHERE cedula = '$cedula'";
    if ($id_excluir) {
        $sql .= " AND id != '$id_excluir'";
    }
    
    $resultado = $conexion->query($sql);
    $existe = $resultado->num_rows > 0;
    
    $conexion->close();
    return $existe;
}

/**
 * Valida que una cédula escolar no esté duplicada (para estudiantes)
 * @param string $cedula_escolar Cédula escolar a validar
 * @param string $tabla Tabla donde buscar
 * @param string $id_excluir ID a excluir de la validación (para ediciones)
 * @return bool True si la cédula ya existe, False si no existe
 */
function cedula_escolar_existe($cedula_escolar, $tabla, $id_excluir = null) {
    $conexion = conectar_db();
    
    $sql = "SELECT id FROM $tabla WHERE cedula_escolar = '$cedula_escolar'";
    if ($id_excluir) {
        $sql .= " AND id != '$id_excluir'";
    }
    
    $resultado = $conexion->query($sql);
    $existe = $resultado->num_rows > 0;
    
    $conexion->close();
    return $existe;
}

/**
 * Valida el formato de una fecha (YYYY-MM-DD)
 * @param string $fecha Fecha a validar
 * @return bool True si el formato es válido, False si no es válido
 */
function validar_fecha($fecha) {
    return preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $fecha);
}

/**
 * Valida que una fecha no sea mayor a la fecha actual
 * @param string $fecha Fecha a validar (formato YYYY-MM-DD)
 * @return bool True si la fecha es válida, False si es mayor a la actual
 */
function validar_fecha_no_futura($fecha) {
    $fecha_actual = date('Y-m-d');
    return $fecha <= $fecha_actual;
}

/**
 * Valida que un texto solo contenga letras y espacios
 * @param string $texto Texto a validar
 * @return bool True si es válido, False si no es válido
 */
function validar_solo_texto($texto) {
    return preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $texto);
}

/**
 * Valida un correo electrónico
 * @param string $correo Correo a validar
 * @return bool True si es válido, False si no es válido
 */
function validar_correo($correo) {
    return filter_var($correo, FILTER_VALIDATE_EMAIL);
}

/**
 * Valida un número telefónico (11 dígitos para formato venezolano)
 * @param string $telefono Teléfono a validar
 * @return bool True si es válido, False si no es válido
 */
function validar_telefono($telefono) {
    return preg_match('/^[0-9]{11}$/', $telefono);
}

/**
 * Formatea una fecha de MySQL (YYYY-MM-DD) a formato legible (DD/MM/YYYY)
 * @param string $fecha Fecha en formato MySQL
 * @return string Fecha formateada
 */
function formatear_fecha($fecha) {
    return date('d/m/Y', strtotime($fecha));
}

/**
 * Obtiene un listado de registros de una tabla
 * @param string $tabla Nombre de la tabla
 * @param string $orden Campo por el que ordenar
 * @param string $filtro Condición WHERE opcional
 * @return array Registros encontrados
 */
function obtener_registros($tabla, $orden = 'id', $filtro = '') {
    $conexion = conectar_db();
    
    $sql = "SELECT * FROM $tabla";
    if (!empty($filtro)) {
        $sql .= " WHERE $filtro";
    }
    $sql .= " ORDER BY $orden";
    
    $resultado = $conexion->query($sql);
    $registros = [];
    
    if ($resultado && $resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            $registros[] = $fila;
        }
    }
    
    $conexion->close();
    return $registros;
}

/**
 * Obtiene un registro específico por ID
 * @param string $tabla Nombre de la tabla
 * @param int $id ID del registro
 * @return array|null Registro encontrado o null si no existe
 */
function obtener_registro_por_id($tabla, $id) {
    $conexion = conectar_db();
    
    $sql = "SELECT * FROM $tabla WHERE id = $id";
    $resultado = $conexion->query($sql);
    
    if ($resultado && $resultado->num_rows > 0) {
        $registro = $resultado->fetch_assoc();
    } else {
        $registro = null;
    }
    
    $conexion->close();
    return $registro;
}

/**
 * Inserta un nuevo registro en una tabla
 * @param string $tabla Nombre de la tabla
 * @param array $datos Arreglo asociativo con los datos a insertar
 * @return int|bool ID del registro insertado o False en caso de error
 */
function insertar_registro($tabla, $datos) {
    $conexion = conectar_db();
    
    $campos = implode(", ", array_keys($datos));
    $valores = "'" . implode("', '", array_values($datos)) . "'";
    
    $sql = "INSERT INTO $tabla ($campos) VALUES ($valores)";
    
    if ($conexion->query($sql)) {
        $id = $conexion->insert_id;
        $conexion->close();
        return $id;
    } else {
        $conexion->close();
        return false;
    }
}

/**
 * Actualiza un registro existente
 * @param string $tabla Nombre de la tabla
 * @param int $id ID del registro a actualizar
 * @param array $datos Arreglo asociativo con los datos a actualizar
 * @return bool True si se actualizó correctamente, False en caso de error
 */
function actualizar_registro($tabla, $id, $datos) {
    $conexion = conectar_db();
    
    $actualizaciones = [];
    foreach ($datos as $campo => $valor) {
        $actualizaciones[] = "$campo = '$valor'";
    }
    
    $sql = "UPDATE $tabla SET " . implode(", ", $actualizaciones) . " WHERE id = $id";
    
    $resultado = $conexion->query($sql);
    $conexion->close();
    
    return $resultado;
}

/**
 * Elimina un registro de una tabla
 * @param string $tabla Nombre de la tabla
 * @param int $id ID del registro a eliminar
 * @return bool True si se eliminó correctamente, False en caso de error
 */
function eliminar_registro($tabla, $id) {
    $conexion = conectar_db();
    
    $sql = "DELETE FROM $tabla WHERE id = $id";
    
    $resultado = $conexion->query($sql);
    $conexion->close();
    
    return $resultado;
}

/**
 * Genera un mensaje de alerta con redirección
 * @param string $mensaje Mensaje a mostrar
 * @param string $url URL de redirección
 * @return string Código JavaScript con la alerta y redirección
 */
function alerta_redireccionar($mensaje, $url) {
    return "<script languaje='JavaScript'>
            alert('$mensaje');
            location.assign('$url');
            </script>";
}

/**
 * Redirecciona a una página sin mostrar mensaje
 * @param string $url URL de redirección
 */
function redireccionar($url) {
    header("Location: $url");
    exit;
}

/**
 * Obtiene el total de registros de una tabla
 * @param string $tabla Nombre de la tabla
 * @param string $filtro Condición WHERE opcional
 * @return int Total de registros
 */
function contar_registros($tabla, $filtro = '') {
    $conexion = conectar_db();
    
    $sql = "SELECT COUNT(*) as total FROM $tabla";
    if (!empty($filtro)) {
        $sql .= " WHERE $filtro";
    }
    
    $resultado = $conexion->query($sql);
    $fila = $resultado->fetch_assoc();
    
    $conexion->close();
    return $fila['total'];
}

/**
 * Verifica si un usuario está autenticado
 * @return bool True si está autenticado, False si no lo está
 */
function esta_autenticado() {
    session_start();
    return isset($_SESSION['usuario_id']);
}

/**
 * Registra la asistencia de un estudiante
 * @param int $estudiante_id ID del estudiante
 * @param string $fecha Fecha de la asistencia (YYYY-MM-DD)
 * @param string $estado Estado de asistencia (Presente, Ausente, Justificado)
 * @return bool True si se registró correctamente, False en caso de error
 */
function registrar_asistencia($estudiante_id, $fecha, $estado) {
    $conexion = conectar_db();
    
    // Verificar si ya existe un registro para este estudiante y fecha
    $sql = "SELECT id FROM asistencia WHERE estudiante_id = $estudiante_id AND fecha = '$fecha'";
    $resultado = $conexion->query($sql);
    
    if ($resultado->num_rows > 0) {
        // Actualizar registro existente
        $fila = $resultado->fetch_assoc();
        $id = $fila['id'];
        $sql = "UPDATE asistencia SET estado = '$estado' WHERE id = $id";
    } else {
        // Crear nuevo registro
        $sql = "INSERT INTO asistencia (estudiante_id, fecha, estado) VALUES ($estudiante_id, '$fecha', '$estado')";
    }
    
    $resultado = $conexion->query($sql);
    $conexion->close();
    
    return $resultado;
}

/**
 * Obtiene el historial de asistencia de un estudiante
 * @param int $estudiante_id ID del estudiante
 * @param string $desde Fecha inicial (YYYY-MM-DD)
 * @param string $hasta Fecha final (YYYY-MM-DD)
 * @return array Registros de asistencia
 */
function obtener_asistencia_estudiante($estudiante_id, $desde = null, $hasta = null) {
    $conexion = conectar_db();
    
    $sql = "SELECT a.*, e.nombres, e.apellidos 
            FROM asistencia a 
            JOIN estudiantes e ON a.estudiante_id = e.id 
            WHERE a.estudiante_id = $estudiante_id";
    
    if ($desde) {
        $sql .= " AND a.fecha >= '$desde'";
    }
    
    if ($hasta) {
        $sql .= " AND a.fecha <= '$hasta'";
    }
    
    $sql .= " ORDER BY a.fecha DESC";
    
    $resultado = $conexion->query($sql);
    $registros = [];
    
    if ($resultado && $resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            $registros[] = $fila;
        }
    }
    
    $conexion->close();
    return $registros;
}

/**
 * Genera una contraseña aleatoria segura
 * @param int $longitud Longitud de la contraseña
 * @return string Contraseña generada
 */
function generar_password($longitud = 8) {
    $caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()';
    $password = '';
    $max = strlen($caracteres) - 1;
    
    for ($i = 0; $i < $longitud; $i++) {
        $password .= $caracteres[random_int(0, $max)];
    }
    
    return $password;
}
?>