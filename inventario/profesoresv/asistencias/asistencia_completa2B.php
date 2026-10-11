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

require_once 'conexion.php';

// Configuración específica
$grado = 'segundo';
$seccion = 'B';
$titulo = "SEGUNDO GRADO SECCIÓN B";

// Obtener días con asistencia
$dias_query = "SELECT DISTINCT fecha_asistencia FROM asistencias ORDER BY fecha_asistencia";
$dias_result = $conn->query($dias_query);

// Obtener estudiantes
$estudiantes_query = "SELECT cedula_escolar, CONCAT(nombres, ' ', apellidos) AS nombre_completo 
                     FROM $grado WHERE seccion = '$seccion' ORDER BY apellidos";
$estudiantes_result = $conn->query($estudiantes_query);

// Obtener asistencias
$asistencias = [];
$asist_data = $conn->query("SELECT cedula_escolar, fecha_asistencia FROM asistencias");
while($row = $asist_data->fetch_assoc()) {
    $asistencias[$row['cedula_escolar']][$row['fecha_asistencia']] = true;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Asistencia <?= $titulo ?></title>
    <link rel="stylesheet" href="styless_asistencia.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="ultimate-export/jquery.min.js"></script>
    <script src="ultimate-export/libs/FileSaver.min.js"></script>
    <script src="ultimate-export/libs/xlsx.core.min.js"></script>
    <script src="ultimate-export/libs/jsPDF/polyfills.umd.js"></script>
    <script src="ultimate-export/libs/jsPDF/jspdf.umd.min.js"></script>
    <script src="ultimate-export/libs/html2canvas/html2canvas.min.js"></script>
    <script src="ultimate-export/tableExport.min.js"></script>
    <script src="ultimate-export/tableExport.js"></script>
</head>
<body>
    <div class="contenedor-principal">
        <h1><?= $titulo ?></h1>
        <ul class="menu-item">
   
   <ul class="menu-list">
<li class="menu-item has-submenu">
<a href="#" class="menu-link">Exportar<span class="submenu-icon">&#9660;</span></a>
<ul class="submenu">
 
 <li class="submenu-item">
   <a onclick="xls()" href="#" class="submenu-link">XLS</a>
 </li>
</ul>

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
$('#lista_pasantes').tableExport({
type:'xls',
fileName: 'Asistencia_de_Segundo_Sección_B_' + fecha_actual,
ignoreColumn: []
});}
</script>
<script>
// A PDF
function pdf(){
$('#lista_pasantes').tableExport({
type:'pdf',
fileName: 'Asistencia_de_Primero_Sección_A_' + fecha_actual,
ignoreColumn: [],
jspdf: {
   orientation: 'p',  // 'l' para horizontal, 'p' para vertical (por defecto)
   unit: 'pt',  // Unidad de medida: puntos (por defecto), mm, cm, in
   format: 'a3' // Formato de página
   // Otras opciones jspdf según tu requisito
}
});
}
</script>


<div class="contenedor-tabla">
   <table class="tabla-asistencia" id='lista_pasantes'>
                <thead>
                    <tr>
                        <th class="nombre-estudiante">Estudiante</th>
                        <?php while($dia = $dias_result->fetch_assoc()): ?>
                        <th><?= date('d/m', strtotime($dia['fecha_asistencia'])) ?></th>
                        <?php endwhile; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php while($estudiante = $estudiantes_result->fetch_assoc()): ?>
                    <tr>
                        <td class="nombre-estudiante"><?= htmlspecialchars($estudiante['nombre_completo']) ?></td>
                        <?php 
                        $dias_result->data_seek(0);
                        while($dia = $dias_result->fetch_assoc()): 
                            $presente = isset($asistencias[$estudiante['cedula_escolar']][$dia['fecha_asistencia']]);
                        ?>
                        <td class="<?= $presente ? 'presente' : 'ausente' ?>">
                            <?= $presente ? '✓' : '✗' ?>
                        </td>
                        <?php endwhile; ?>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div style="text-align: center; margin: 20px 0;">
            <a href="../grado_segundoB.php" style="background-color: #4CAF50; color: white; text-decoration: none; padding: 12px 25px; border-radius: 6px; display: inline-flex; align-items: center; box-shadow: 0 2px 5px rgba(76, 175, 80, 0.3);">
                <i class="fas fa-arrow-left" style="margin-right: 10px;"></i> Regresar a la lista de estudiantes
            </a>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>