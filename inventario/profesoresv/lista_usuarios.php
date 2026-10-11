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

// 3. Consulta con filtros
$query = "
    SELECT id, cedula, nombres, apellidos, correo, rol
    FROM profesor 
    WHERE rol IN ('admin', 'visitante') ";

    
    

  
    
    $query .= " ORDER BY apellidos";
    
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
 
    <style>
        .mensaje-flotante {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            color: white;
            border-radius: 5px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
            z-index: 9999;
            animation: slideIn 0.5s ease-out;
            display: none;
        }

        .success { background: #28a745; }
        .warning { background: #ffc107; color: #000; }
        .error { background: #dc3545; }

        @keyframes slideIn {
            from { transform: translateX(100%); }
            to { transform: translateX(0); }
        }
        /* Estilo base del botón */
.btn-completa {
    display: inline-flex;
    align-items: center;
    padding: 12px 25px;
    background: #2196F3;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.3s ease;
    margin: 15px 0;
    box-shadow: 0 2px 5px rgba(33, 150, 243, 0.3);
    border: none;
    cursor: pointer;
    font-size: 16px;
}

/* Efecto hover */
.btn-completa:hover {
    background: #1976D2;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(33, 150, 243, 0.3);
}

/* Efecto al hacer clic */
.btn-completa:active {
    transform: translateY(0);
    box-shadow: 0 1px 3px rgba(33, 150, 243, 0.3);
}

/* Icono de Font Awesome */
.btn-completa i {
    margin-right: 10px;
    font-size: 18px;
    transition: transform 0.3s ease;
}

/* Animación del icono al pasar el mouse */
.btn-completa:hover i {
    transform: scale(1.1);
}

/* Versión para pantallas pequeñas */
@media (max-width: 768px) {
    .btn-completa {
        padding: 10px 20px;
        font-size: 14px;
    }
    
    .btn-completa i {
        font-size: 16px;
    }
}
    </style>
</head>
<body>

<div id="mensajeContainer" class="mensaje-flotante"></div>

<nav class="logo-nav">
        <a href="#" class="logo-link">Moral y Luces</a>
        <div class="logo-placeholder"></div>
        <div class="menu-item has-submenu">
                <a href="#" class="menu-link"><span class="submenu-icon">&#9660;</span></a>
                <div class="submenu">
                    <ul class="submenu-item">
                        <a href="../../login/cerrarsesion.php" class="submenu-link">Salir</a>
                        
                </ul>
            </div>
    </nav>
    <h2>Listado de Usuarios</h2>
   <!-- En la sección del menú donde está el buscador -->

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
      fileName: 'Listado_de_Primero_Sección_A_' + fecha_actual,
      ignoreColumn: []
      });}
    </script>
    <script>
      // A PDF
      function pdf(){
      $('#lista_pasantes').tableExport({
      type:'pdf',
      fileName: 'Lista_de_pasantes_' + fecha_actual,
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

    


<table id='lista_pasantes'>
    <thead>
        <tr>
            <th>Cédula</th>
            <th>Nombres</th>
            <th>Apellidos</th>
            <th>Fecha Nac.</th>
            <th>Género</th>
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
            <td><?= htmlspecialchars($row['correo']) ?></td>
            <td><?= htmlspecialchars($row['rol']) ?></td>
            <td class="action-buttons">
            <a href="../formularios/editar_usuario.php?id=<?= $row['id'] ?>" class="editar">Editar</a>
            <a href="eliminar.php?tabla=profesor&id=<?= $row['id'] ?>" 
   class="eliminar" 
   onclick="return confirm('¿Eliminar registro de primero?')">
   Eliminar
</a>
            </td>
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