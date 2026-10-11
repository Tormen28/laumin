<?php
session_name("lau");
session_start();

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
// 1. Conexión a la base de datos
$host = 'localhost';
$usuario = 'root';
$password = '';
$base_datos = 'asistencia_lau';

$conn = new mysqli($host, $usuario, $password, $base_datos);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// 2. Manejar parámetro de búsqueda
$cedula_busqueda = isset($_GET['cedula_escolar']) ? $conn->real_escape_string($_GET['cedula_escolar']) : '';

// 3. Construir consulta con filtros
$query = "SELECT id, nombres, apellidos, cedula_escolar, 
                 fecha_nacimiento, genero, seccion 
          FROM segundo 
          WHERE seccion = 'A'"; 
          


if (!empty($cedula_busqueda)) {
    $query .= " AND cedula_escolar LIKE '%$cedula_busqueda%'";
}

$query .= " ORDER BY apellidos";

// Consulta para obtener el profesor del grado y sección
$query_profesor = "SELECT nombres, apellidos 
                   FROM profesor 
                   WHERE grado = 'segundo' AND seccion = 'A' 
                   LIMIT 1";
$result_profesor = $conn->query($query_profesor);

// Concatenar nombres y apellidos
if ($result_profesor->num_rows > 0) {
    $profesor = $result_profesor->fetch_assoc();
    $nombre_completo = $profesor['nombres'] . ' ' . $profesor['apellidos'];
} else {
    $nombre_completo = 'Profesor no asignado';
}

// 4. Ejecutar consulta y verificar errores
$result = $conn->query($query);
if (!$result) {
    die("Error en la consulta: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (isset($_GET['exito'])): ?>
        alert('Registro eliminado con éxito');
        document.querySelector('.mensaje-flotante.success').style.display = 'block';
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
        alert('Error: <?= addslashes($mensaje) ?>');
        document.querySelector('.mensaje-flotante.error').style.display = 'block';
    <?php endif; ?>
});
</script>
    <title>Asistencia</title>
    <link rel="stylesheet" href="stylessgrado.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="ultimate-export/jquery.min.js"></script>
    <script src="ultimate-export/libs/FileSaver.min.js"></script>
    <script src="ultimate-export/libs/xlsx.core.min.js"></script>
    <script src="ultimate-export/libs/jsPDF/polyfills.umd.js"></script>
    <script src="ultimate-export/libs/jsPDF/jspdf.umd.min.js"></script>
    <script src="ultimate-export/libs/html2canvas/html2canvas.min.js"></script>
    <script src="ultimate-export/tableExport.min.js"></script>
    <script src="ultimate-export/tableExport.js"></script>
    <style>
    /* Estilos mejorados del buscador */
        .search-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 10px;
            border-radius: 30px;
            background: linear-gradient(145deg, #ffffff, #f0f7ff);
            box-shadow: 0 4px 20px rgba(50, 130, 250, 0.1);
        }

        .search-group {
            display: flex;
            align-items: center;
            gap: 5px;
            position: relative;
        }

        .search-input {
            flex: 1;
            padding: 14px 25px;
            border: none;
            border-radius: 25px;
            background: #f8fbff;
            font-size: 1rem;
            color: #2d4059;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .search-input::placeholder {
            color: #a0b4c6;
            font-weight: 300;
        }

        .search-button {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 14px 25px;
            border: none;
            border-radius: 25px;
            background: #3282fa;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(50, 130, 250, 0.3);
        }

        /* Resto de estilos se mantienen igual */
        /* ... [Mantener los otros estilos existentes] ... */
    </style>
</head>
<body>

<div id="mensajeContainer" class="mensaje-flotante"></div>

<nav class="logo-nav">
        <a href="#" class="logo-link">Asistencia</a>
        <div class="logo-placeholder"></div>
        <div class="menu-item has-submenu">
                <a href="#" class="menu-link"><span class="submenu-icon">&#9660;</span></a>
                <div class="submenu">
                    <ul class="submenu-item">
                        <a href="../../login/cerrarsesion.php" class="submenu-link">Salir</a>
                        
                </ul>
            </div>
    </nav>

<h2>Listado de Segundo Sección A</h2>
<h2>Profesor a cargo: <?php echo $nombre_completo; ?></h2>

<!-- Menú mejorado con buscador y exportación -->
<ul class="menu-item">
    <div class="search-container">
        <form action="" method="GET" class="search-form">
            <div class="search-group">
                <input type="text" 
                       name="cedula_escolar" 
                       placeholder="Buscar estudiante..." 
                       value="<?= htmlspecialchars($cedula_busqueda) ?>"
                       class="search-input">
                <button type="submit" class="search-button">
                    <i class="fas fa-search search-icon"></i>
                    <span class="search-text">Buscar</span>
                </button>
            </div>
            <ul class="menu-list">
      <li class="menu-item has-submenu">
        <a href="#" class="menu-link">Exportar<span class="submenu-icon">&#9660;</span></a>
        <ul class="submenu">
          
          <li class="submenu-item">
            <a onclick="xls()" href="#" class="submenu-link">XLS</a>
          </li>
        </ul>
        </form>
    </div>
    
</ul>
<!-- Scripts de exportación agregados -->
<script>
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0');
    var yy = String(today.getFullYear()).slice(-2);
    var fecha_actual = dd + '_' + mm + '_' + yy;

    function xls(){
        $('#lista_pasantes').tableExport({
            type:'xls',
            fileName: 'Listado_Segundo_Seccion_A_' + fecha_actual,
            ignoreColumn: []
        });
    }

    function pdf(){
        $('#lista_pasantes').tableExport({
            type:'pdf',
            fileName: 'Listado_Segundo_Seccion_A_' + fecha_actual,
            ignoreColumn: [],
            jspdf: {
                orientation: 'p',
                unit: 'pt',
                format: 'a3'
            }
        });
    }
</script>
<a href="../profesoresv/asistencias/asistencia_completa2A.php" class="btn-completa">
    <i class="fas fa-clipboard-list"></i> Asistencia Completa
</a>
<table id='lista_pasantes'>  
      <thead>
        <tr>
            <th>Cédula</th>
            <th>Nombres</th>
            <th>Apellidos</th>
            <th>Fecha Nac.</th>
            <th>Género</th>
        
        </tr>
    </thead>
    <tbody>
    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['cedula_escolar']) ?></td>
            <td><?= htmlspecialchars($row['nombres']) ?></td>
            <td><?= htmlspecialchars($row['apellidos']) ?></td>
            <td><?= date('d/m/Y', strtotime($row['fecha_nacimiento'])) ?></td>
            <td><?= ($row['genero'] == 'M') ? 'Masculino' : 'Femenino' ?></td>
           
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="6">
                <?= empty($cedula_busqueda) 
                    ? 'No hay estudiantes registrados en esta sección' 
                    : 'No se encontraron resultados para la cédula: ' . htmlspecialchars($cedula_busqueda) ?>
            </td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>

<script>
function registrarAsistencia(cedula_escolar) {
    const mensajeContainer = document.getElementById('mensajeContainer');
    
    fetch(`acciones.php?cedula_escolar=${cedula_escolar}`)
    .then(response => response.json())
    .then(data => {
        mensajeContainer.textContent = data.mensaje;
        mensajeContainer.className = `mensaje-flotante ${data.tipo}`;
        mensajeContainer.style.display = 'block';
        
        setTimeout(() => {
            mensajeContainer.style.display = 'none';
        }, 3000);
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
</script>

<?php $conn->close(); ?>

<div style="text-align: center; margin: 20px 0;">
    <a href="../index.php" style="background-color: #4CAF50; color: white; text-decoration: none; padding: 12px 25px; border-radius: 6px; display: inline-flex; align-items: center; box-shadow: 0 2px 5px rgba(76, 175, 80, 0.3);">
        <i class="fas fa-arrow-left" style="margin-right: 10px;"></i> Regresar al menú principal
    </a>
</div>

</body>
</html>