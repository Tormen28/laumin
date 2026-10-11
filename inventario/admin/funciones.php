<?php

//conexion a la base de datos
    class conexion
    {
        public $host = 'localhost';
        public $usuario = 'root';
        public $contraseña = '';
        public $base = 'asistencia_lau';
        public $conexion;

        public function __construct()
        {
            $this->conexion = mysqli_connect(
                $this->host,
                $this->usuario,
                $this->contraseña,
                $this->base
                );
        }

    }


    //registar docentes
    class Registration extends conexion
    {
        public function docentes($cedula, $nombre, $apellidos, $grado, $seccion, $fecha_nac, $genero, $telefono, $correo, $contraseña)
        {
            // Validar campos obligatorios
            if (empty($cedula) || empty($nombre) || empty($contraseña)) {
                return 100; // Campos requeridos faltantes
            }
    
            try {
                // 1. Verificar si la cédula ya existe
                $stmt = $this->conexion->prepare("SELECT cedula FROM profesor WHERE cedula = ?");
                $stmt->bind_param("s", $cedula);
                $stmt->execute();
                
                if ($stmt->get_result()->num_rows > 0) {
                    return 101; // Cédula ya registrada
                }
    
                // 2. Hashear la contraseña
                $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);
                
                // 3. Insertar con prepared statement
                $stmt = $this->conexion->prepare(
                    "INSERT INTO profesor (
                        cedula, 
                        nombres, 
                        apellidos, 
                        grado, 
                        seccion,
                        fecha_nac, 
                        genero, 
                        telefono, 
                        correo, 
                        contraseña,
                        rol
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'profesor')"
                );
                
                // 4. Bind de parámetros
                $stmt->bind_param(
                    "ssssssssss",
                    $cedula,
                    $nombre,
                    $apellidos,
                    $grado,
                    $seccion,
                    $fecha_nac,
                    $genero,
                    $telefono,
                    $correo,
                    $contraseña_hash
                );
    
                // 5. Ejecutar y verificar
                if ($stmt->execute()) {
                    return 1; // Éxito
                }
                
                return 100; // Error desconocido
    
            } catch (Exception $e) {
                error_log("Error en registro: " . $e->getMessage());
                return 100; // Error general
            }
        }

       
    }
    
    ?>