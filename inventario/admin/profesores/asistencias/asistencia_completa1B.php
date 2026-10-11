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

// Obtener todos los días con asistencia registrada
$dias_query = "SELECT DISTINCT fecha_asistencia FROM asistencias ORDER BY fecha_asistencia";
$dias_result = $conn->query($dias_query);
$dias = $dias_result->fetch_all(MYSQLI_ASSOC);

// Obtener todos los estudiantes de sección B
$estudiantes_query = "SELECT cedula_escolar, CONCAT(nombres, ' ', apellidos) AS nombre_completo 
                     FROM primero WHERE seccion = 'B' ORDER BY apellidos";
$estudiantes_result = $conn->query($estudiantes_query);
$estudiantes = $estudiantes_result->fetch_all(MYSQLI_ASSOC);

// Obtener todas las asistencias
$asistencias_query = "SELECT cedula_escolar, fecha_asistencia FROM asistencias";
$asistencias_result = $conn->query($asistencias_query);
$asistencias = [];

while($row = $asistencias_result->fetch_assoc()) {
    $asistencias[$row['cedula_escolar']][$row['fecha_asistencia']] = true;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Asistencia por Días - Primero B</title>
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
    <div class="card">
        <h2>LISTA DE ASISTENCIA - PRIMERO SECCIÓN B</h2>
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
      fileName: 'Asistencia_de_Primero_Sección_B_' + fecha_actual,
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
                        <?php foreach($dias as $dia): ?>
                            <th class="fecha-header">
                                <?= date('d/m', strtotime($dia['fecha_asistencia'])) ?>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($estudiantes as $estudiante): ?>
                        <tr>
                            <td class="nombre-estudiante"><?= htmlspecialchars($estudiante['nombre_completo']) ?></td>
                            <?php foreach($dias as $dia): ?>
                                <?php $presente = isset($asistencias[$estudiante['cedula_escolar']][$dia['fecha_asistencia']]); ?>
                                <td class="<?= $presente ? 'presente' : 'ausente' ?>">
                                    <?= $presente ? '✓' : '✗' ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        
    </div>
    <div style="text-align: center; margin: 20px 0;">
        <button type="submit" name="regresar" onclick="window.location.href='../grado_primeroB.php'" style="padding: 10px 20px; font-size: 16px; cursor: pointer; background-color: #4CAF50; color: white; border: none; border-radius: 4px;">Regresar</button>
    </div>
</body>
</html>