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
$cedula_busqueda = isset($_GET['cedula']) ? $conn->real_escape_string($_GET['cedula']) : '';

// 3. Consulta con filtros
$query = "
    SELECT id, cedula, nombres, apellidos, grado, seccion, 
           fecha_nac, genero, telefono, correo
    FROM profesor";

if (!empty($cedula_busqueda)) {
    $query .= " WHERE cedula LIKE '%$cedula_busqueda%'";
}

$query .= " ORDER BY apellidos";

// 4. Ejecutar consulta y verificar errores
// CORRECCIÓN: Cambiar $conex por $conn
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
    <title>Listado de Profesores</title>
    <link rel="stylesheet" href="./stylessprofesores.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="../profesores/ultimate-export/jquery.min.js"></script>
    <script src="../profesores/ultimate-export/libs/FileSaver.min.js"></script>
    <script src="../profesores/ultimate-export/libs/xlsx.core.min.js"></script>
    <script src="../profesores/ultimate-export/libs/jsPDF/polyfills.umd.js"></script>
    <script src="../profesores/ultimate-export/libs/jsPDF/jspdf.umd.min.js"></script>
    <script src="../profesores/ultimate-export/libs/html2canvas/html2canvas.min.js"></script>
    <script src="../profesores/ultimate-export/tableExport.min.js"></script>
    <script src="../profesores/ultimate-export/tableExport.js"></script>
   
</head>
<body>

<div id="mensajeContainer" class="mensaje-flotante"></div>

<nav class="logo-nav">
<a href="#" class="logo-link">Listado</a>
    <div class="logo-placeholder"></div>
    <div class="menu-item has-submenu">
        <a href="#" class="menu-link"><span class="submenu-icon">&#9660;</span></a>
        <div class="submenu">
            <ul class="submenu-item">
                <a href="../../login/cerrarsesion.php" class="submenu-link">Salir</a>
            </ul>
        </div>
    </div>
</nav>

<h2>Listado de Profesores</h2>

<!-- En la sección del menú donde está el buscador -->
<ul class="menu-item">
    
    <div class="search-container">
        <form action="" method="GET" class="search-form">
            <div class="search-group">
                <input type="text" 
                       name="cedula" 
                       placeholder="Buscar profesor..." 
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
                </li>
            </ul>
        </form>
    </div>
</ul>

<script>
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0');
    var yy = String(today.getFullYear()).slice(-2);
    var fecha_actual = dd + '_' + mm + '_' + yy;

    // A EXCEL
    function xls(){
        $('#lista_profesores').tableExport({
            type: 'xls',
            fileName: 'Listado_de_Profesores_' + fecha_actual,
            ignoreColumn: []
        });
    }
</script>

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
        

      </li>
      <li class="menu-item has-submenu">
      <button type="submit" name="regresar" onclick="window.location.href='../index.php'">Regresar</button> 
    
</nav>

<table id='lista_profesores'>
    <thead>
        <tr>
            <th>Cédula</th>
            <th>Nombres</th>
            <th>Apellidos</th>
            <th>Grado</th>
            <th>Sección</th>
            <th>Fecha Nac.</th>
            <th>Género</th>
            <th>Teléfono</th>
            <th>Correo</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['cedula']) ?></td>
            <td><?= htmlspecialchars($row['nombres']) ?></td>
            <td><?= htmlspecialchars($row['apellidos']) ?></td>
            <td><?= htmlspecialchars($row['grado']) ?></td>
            <td><?= htmlspecialchars($row['seccion']) ?></td>
            <td><?= date('d/m/Y', strtotime($row['fecha_nac'])) ?></td>
            <td><?= ($row['genero'] == 'M') ? 'Masculino' : 'Femenino' ?></td>
            <td><?= htmlspecialchars($row['telefono']) ?></td>
            <td><?= htmlspecialchars($row['correo']) ?></td>
            <td class="action-buttons">
                <a href="../admin/formularios/editar_profesor.php?id=<?= $row['id'] ?>" class="editar">Editar</a>
                <a href="../profesores/eliminar.php?tabla=profesor&id=<?= $row['id'] ?>" 
                    class="eliminar" 
                    onclick="return confirm('¿Eliminar registro de profesor?')">
                    Eliminar
                    </a>
                
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="10">
                <?= empty($cedula_busqueda) 
                    ? 'No hay profesores registrados' 
                    : 'No se encontraron resultados para la cédula: ' . htmlspecialchars($cedula_busqueda) ?>
            </td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>

<?php $conn->close(); ?>

</body>
</html>