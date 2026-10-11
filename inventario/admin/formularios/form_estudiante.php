<?php
session_name("lau");
session_start();

// Agregar JavaScript para prevenir salida con cambios sin guardar
echo <<<EOT
<script>
// Variable para rastrear si el usuario está intentando navegar hacia atrás
let isNavigatingAway = false;

// Función para verificar cambios en el formulario
function hasFormChanges() {
    const form = document.querySelector("form");
    if (form) {
        const formData = new FormData(form);
        for (let [key, value] of formData.entries()) {
            if (value && key !== 'submit' && key !== '') {
                return true;
            }
        }
    }
    return false;
}

// Detectar cuando el usuario hace clic en el botón de regresar
window.onpopstate = function(event) {
    if (hasFormChanges()) {
        const confirmationMessage = "¡Tienes cambios sin guardar! ¿Estás seguro de que quieres salir?";
        if (!confirm(confirmationMessage)) {
            // Si el usuario cancela, mantenemos la página actual
            history.pushState(null, null, location.href);
        }
    }
};

// Evitar que el evento beforeunload se active en otros casos
window.addEventListener('beforeunload', function(e) {
    if (hasFormChanges() && !isNavigatingAway) {
        // Si hay cambios y no es una navegación controlada, prevenir el cierre
        e.preventDefault();
        e.returnValue = '';
    }
});

// Manejar clics en enlaces
window.addEventListener('click', function(e) {
    if (e.target.closest('a') && hasFormChanges()) {
        if (!confirm("¡Tienes cambios sin guardar! ¿Seguro que quieres salir?")) {
            e.preventDefault();
        }
    }
});

// Manejar el envío del formulario
document.addEventListener('submit', function() {
    // Marcar que el envío del formulario es intencional
    isNavigatingAway = true;
});
</script>
EOT;
// Resto de la verificación de sesión

// Verificar si el usuario está autenticado
if (!isset($_SESSION['id'])) {
    // Destruir cualquier sesión residual
    session_unset();
    session_destroy();
    
    // Redirigir inmediatamente al login
    header("Location: ../index.php");
    exit();
}

// Prevenir caching en navegadores
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies
?>


<?php
if (isset($_POST['submit'])) {
    // Configuración de conexión
    $host = "localhost";
    $user = "root";
    $pass = "";
    $db = "asistencia_lau";
    
    // Obtener datos del formulario
    $grado = $_POST['grado'] ?? '';
    $estudiantes = $_POST['estudiantes'] ?? [];

    // Crear conexión
    $conn = new mysqli($host, $user, $pass, $db);
    
    // Verificar conexión
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    if (!empty($estudiantes) && !empty($grado)) {
        // Validar que el nombre de la tabla sea seguro
        $tabla_grado = $conn->real_escape_string($grado);
        
        // Verificar si la tabla existe
        $result = $conn->query("SHOW TABLES LIKE '$tabla_grado'");
        if ($result->num_rows == 0) {
            die("<script>alert('La tabla para este grado no existe');</script>");
        }

        // Contador para estudiantes insertados
        $insertados = 0;
        
        foreach ($estudiantes as $est) {
            // Validar campos requeridos
            $camposRequeridos = [
                $est['nombres'] ?? '',
                $est['apellidos'] ?? '',
                $est['cedula_escolar'] ?? '',
                $est['fecha_nacimiento'] ?? '',
                $est['genero'] ?? '',
                $est['seccion'] ?? ''
            ];

            if (in_array('', $camposRequeridos, true)) {
                echo "<script>alert('Complete todos los campos requeridos para el estudiante ".($insertados+1)."');</script>";
                continue;
            }

            // Limpiar y validar datos
            $nombres = $conn->real_escape_string($est['nombres']);
$apellidos = $conn->real_escape_string($est['apellidos']);
$cedula_escolar = $conn->real_escape_string(preg_replace('/\D/', '', $est['cedula_escolar']));
$fecha_nacimiento = $conn->real_escape_string($est['fecha_nacimiento']);
$genero = $conn->real_escape_string($est['genero']);
$seccion = $conn->real_escape_string($est['seccion']);

// Preparar consulta
$sql = "INSERT INTO $tabla_grado (
    nombres, 
    apellidos, 
    cedula_escolar, 
    fecha_nacimiento, 
    genero, 
    seccion
) VALUES (?, ?, ?, ?, ?, ?)";

            
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                echo "<script>alert('Error en la preparación: ".$conn->error."');</script>";
                continue;
            }
            
            $stmt->bind_param("ssssss", 
                $nombres,
                $apellidos,
                $cedula_escolar,
                $fecha_nacimiento,
                $genero,
                $seccion
            );
            
            if ($stmt->execute()) {
                $insertados++;
            } else {
                echo "<script>alert('Error al guardar estudiante ".($insertados+1).": ".$stmt->error."');</script>";
            }
            
            $stmt->close();
        }

        if ($insertados > 0) {
            echo "<script>alert('Se registraron $insertados estudiantes en $grado');</script>";
        } else {
            echo "<script>alert('No se registraron estudiantes');</script>";
        }
        
    } else {
        echo "<script>alert('Debe seleccionar un grado y agregar estudiantes');</script>";
    }
    
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secretaria</title>
    <link rel="stylesheet" type="text/css" href="../inventario_styles.css">
  

</head>
<body>

<nav class="logo-nav">
    <a href="../index.php" class="logo-link">Asistencia</a>
    <div class="logo-placeholder"></div>
</nav>

<nav class="menu">
<ul class="menu-list">
        
            
        </li>
        <li class="menu-item has-submenu">
            <a href="#" class="menu-link">Grados<span class="submenu-icon">&#9660;</span></a>
            <ul class="submenu">
              <li class="submenu-item">
                <a href="../profesores/grado_primeroA.php" class="submenu-link">Primer Grado A</a>
              </li>
              <li class="submenu-item">
                <a href="../profesores/grado_primeroB.php" class="submenu-link">Primer Grado B</a>
              </li>
              <li class="submenu-item">
                <a href="../profesores/grado_segundoA.php" class="submenu-link">Segundo Grado A</a>
              </li>
              <li class="submenu-item">
                <a href="../profesores/grado_segundoB.php" class="submenu-link">Segundo Grado B</a>
              </li>
              <li class="submenu-item">
                <a href="../profesores/grado_terceroA.php" class="submenu-link">Tercer Grado A</a>
              </li>
              <li class="submenu-item">
                <a href="../profesores/grado_terceroB.php" class="submenu-link">Tercer Grado B</a>
              </li>
              <li class="submenu-item">
                <a href="../profesores/grado_cuartoA.php" class="submenu-link">Cuarto Grado A</a>
              </li>
              <li class="submenu-item">
                <a href="../profesores/grado_cuartoB.php" class="submenu-link">Cuarto Grado B</a>
              </li>
              <li class="submenu-item">
                <a href="../profesores/grado_quintoA.php" class="submenu-link">Quinto Grado A</a>
              </li>
              <li class="submenu-item">
                <a href="../profesores/grado_quintoB.php" class="submenu-link">Quinto Grado B</a>
              </li>
              <li class="submenu-item">
                <a href="../profesores/grado_sextoA.php" class="submenu-link">Sexto Grado A</a>
              </li>
              <li class="submenu-item">
                <a href="../profesores/grado_sextoB.php" class="submenu-link">Sexto Grado B</a>
              </li>
            </ul>
        </li>
        <li class="menu-item has-submenu">
            <a href="../formularios/form_profesores.php" class="menu-link">Registrar Profesores<span class="submenu-icon">&#9660;</span></a>
        </li>
        
    
</nav>

    <h2>REGISTRO DE ESTUDIANTES</h2>
    
    <form action="" method="post" autocomplete="off" id="estudianteForm" onsubmit="window.onbeforeunload = null;">


        <div class="input-group">
            <label for="grado">Grado:</label>
            <select id="selectorGrado" name="grado" required>
                <option value="">-- Elija un Grado --</option>
                <option value="primero">Primer Grado</option>
                <option value="segundo">Segundo Grado</option>
                <option value="tercero">Tercer Grado</option>
                <option value="cuarto">Cuarto Grado</option>
                <option value="quinto">Quinto grado</option>
                <option value="sexto">sexto grado</option>

            </select>
        </div>

        <div class="input-group">
            <label for="selectProfesor">Profesor:</label>
            <select id="selectorProfesor" name="materia" required>
                <option value="">-- Primero seleccione un grado --</option>
            </select>
        </div>

        

        <script>
// Cargar profesores al seleccionar grado
// Código corregido en el <script> del formulario
document.getElementById('selectorGrado').addEventListener('change', function() {
    const grado = this.value;
    const selectorProfesor = document.getElementById('selectorProfesor');
    
    if (grado) {
        fetch(`get_profesores.php?grado=${encodeURIComponent(grado)}`)
            .then(response => {
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    console.error(data.error);
                    selectorProfesor.innerHTML = `<option value="">${data.error}</option>`;
                    return;
                }
                
                selectorProfesor.innerHTML = '<option value="">-- Seleccione profesor --</option>';
                data.forEach(prof => {
                    const option = document.createElement('option');
                    option.value = prof.cedula;
                    option.textContent = `${prof.nombres} ${prof.apellidos} (${prof.cedula})`;
                    selectorProfesor.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Fetch error:', error);
                selectorProfesor.innerHTML = '<option value="">Error al cargar profesores</option>';
            });
    }
});
</script>


        <div class="input-group">
            <label for="nombre">Número de estudiantes:</label>
            <input type="number" id="numStudents" min="1" value="1">
            <button type="button" onclick="generarEstudiantes()">Generar Campos Estudiantes</button>
        </div>

        <div id="formContainer"></div>

        <script>

// Función para generar estudiantes con el nuevo formato
// Función para generar estudiantes con names corregidos
function generarEstudiantes() {
    const numStudents = document.getElementById('numStudents').value;
    const container = document.getElementById('formContainer');
    container.innerHTML = '';

    for (let i = 0; i < numStudents; i++) {
        const studentDiv = document.createElement('div');
        studentDiv.className = 'student-section';
        studentDiv.innerHTML = `
            <h3>Estudiante ${i + 1}</h3>
            <div class="input-group">
                <label>Nombres:</label>
                <input type="text" name="estudiantes[${i}][nombres]" required oninput="this.value = this.value.replace(/[^A-Za-z áéíóúÁÉÍÓÚñÑ]/g, '')">
            </div>
            <div class="input-group">
                <label>Apellidos:</label>
                <input type="text" name="estudiantes[${i}][apellidos]" required oninput="this.value = this.value.replace(/[^A-Za-z áéíóúÁÉÍÓÚñÑ]/g, '')">
            </div>
             <div class="input-group">
        <label>Cédula Escolar:</label>
        <input type="text" 
               name="estudiantes[${i}][cedula_escolar]" 
               required 
               onkeypress="return isNumberKey(event)"
               oninput="formatCedula(this)"
               maxlength="11"> <!-- Permite hasta 8 dígitos + 2 puntos -->
    </div>
            
            <div class="input-group">
                <label>Fecha Nacimiento:</label>
                <input type="date" name="estudiantes[${i}][fecha_nacimiento]" required>
            </div>
            <div class="input-group">
                <label>Género:</label>
                <select name="estudiantes[${i}][genero]">
                    <option value="M">Masculino</option>
                    <option value="F">Femenino</option>
                </select>
            </div>
           
            <div class="input-group">
                <label>Seccion:</label>
                <select name="estudiantes[${i}][seccion]">
                    <option value="A">A</option>
                    <option value="B">B</option>
                </select>
            </div>
        `;
        container.appendChild(studentDiv);
    }
}

        </script>

<script>
// Función para permitir solo números
function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        evt.preventDefault();
        return false;
    }
    return true;
}
 
// Función para formatear con puntos cada 3 dígitos
function formatCedula(input) {
    let value = input.value.replace(/[^\d]/g, '');
    let formatted = '';
    for (let i = 0; i < value.length; i++) {
        if (i > 0 && i % 3 === 0) {
            formatted += '.';
        }
        formatted += value[i];
    }
    input.value = formatted;
}
</script>

        <div class="input-group">
        </div>

        <div class="input-group">
    <input type="hidden" id="tipoMateria" name="tipo_materia">
</div>

        <div class="input-group">
          <div class="buttons">
            <button type="reset">Limpiar</button>
            <button type="submit" name="submit">Registrar</button>
          </div>
        </div>
    </form>

    <div style="text-align: center; margin: 20px 0;">
        <a href="../index.php" style="background-color: #4CAF50; color: white; text-decoration: none; padding: 12px 25px; border-radius: 6px; display: inline-flex; align-items: center; box-shadow: 0 2px 5px rgba(76, 175, 80, 0.3);">
            <i class="fas fa-arrow-left" style="margin-right: 10px;"></i> Regresar
        </a>
    </div>

</body>
</html>
