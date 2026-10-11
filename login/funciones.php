<?php

    

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

//Metodo para iniciar sesion

class inicio_sesion extends conexion {
    public $id;
    public $rol;
    public $grado_asignado;
    public $seccion_asignada;

    public function inicioSession($usuario, $password) {
        $stmt = $this->conexion->prepare(
            "SELECT id, contraseña, grado, seccion, rol 
            FROM profesor 
            WHERE nombres = ?"
        );
        
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $profesor = $resultado->fetch_assoc();
            
            if (password_verify($password, $profesor['contraseña'])) {
                $this->id = $profesor['id'];
                $this->rol = $profesor['rol'];
                
                // Validar solo para profesores
                if ($this->rol == 'profesor') {
                    $grados_validos = ['primero', 'segundo', 'tercero', 'cuarto', 'quinto', 'sexto'];
                    $secciones_validas = ['A', 'B'];
                    
                    $grado = strtolower($profesor['grado']);
                    $seccion = strtoupper($profesor['seccion']);
                    
                    if (!in_array($grado, $grados_validos) || !in_array($seccion, $secciones_validas)) {
                        return 100;
                    }
                    
                    $this->grado_asignado = $grado;
                    $this->seccion_asignada = $seccion;
                }
                return 1;
            }
            return 10;
        }
        return 100;
    }
}

  

        
    



class select extends conexion
{

        public function selectuserbyuser($id)
        {
            $resultado = mysqli_query($this->conexion, "SELECT * FROM usuarios WHERE id = '$id'" );

            return mysqli_fetch_array($resultado);
        }


} 

class Registration extends conexion {
    public function docentes($cedula, $nombres, $apellidos, $correo, $contraseña) {
        // Verificar duplicados usando la cédula original
        $stmt = $this->conexion->prepare(
            "SELECT cedula FROM profesor WHERE cedula = ?"
        );
        $stmt->bind_param("s", $cedula);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            return 10; // Cédula ya existe
        }

        // Hash de contraseña
        $hash = password_hash($contraseña, PASSWORD_DEFAULT);
        
        // Insertar con la cédula original
        $stmt = $this->conexion->prepare(
            "INSERT INTO profesor 
            (cedula, nombres, apellidos, correo, contraseña, rol) 
            VALUES (?, ?, ?, ?, ?, 'visitante')"
        );
        
        $stmt->bind_param(
            "sssss",
            $cedula, // Valor original sin limpieza
            $nombres,
            $apellidos,
            $correo,
            $hash
        );
        
        return $stmt->execute() ? 1 : 100;
    }
}




?>

