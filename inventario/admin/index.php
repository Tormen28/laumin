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



<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <title>Asistencia</title>
    <link rel="stylesheet" href="styless3.css" />
  </head>
  <body>
    <header class="header">
      <div class="container">
        <div class="btn-menu">
          <label for="btn-menu">☰</label>
        </div>
        <div class="logo">
          <h1>Moral y Luces</h1>
        </div>
        <nav class="menu">
          
          <a href="../../login/cerrarsesion.php">Salir</a>
        </nav>
      </div>
    </header>
    <div class="capa"></div>
    
    <!-- MENÚ LATERAL ACTUALIZADO -->
<input type="checkbox" id="btn-menu" />
<div class="container-menu">
  <div class="cont-menu">
    <nav>
    <div class="menu-item">
        <a href="profesores/lista_usuarios.php">Lista Usuario</a>
      </div>
      <div class="menu-item">
        <a href="#">Grados ▼</a>
        <div class="sub-menu">
          <!-- Primer Grado -->
          <div class="menu-item">
            <a href="#">Primer Grado ▼</a>
            <div class="sub-menu">
              <a href="profesores/grado_primeroA.php">Sección A</a>
              <a href="profesores/grado_primeroB.php">Sección B</a>
            </div>
          </div>
          
          <!-- Segundo Grado -->
          <div class="menu-item">
            <a href="#">Segundo Grado ▼</a>
            <div class="sub-menu">
              <a href="profesores/grado_segundoA.php">Sección A</a>
              <a href="profesores/grado_segundoB.php">Sección B</a>
            </div>
          </div>
          
          <!-- Tercer Grado -->
          <div class="menu-item">
            <a href="#">Tercer Grado ▼</a>
            <div class="sub-menu">
              <a href="profesores/grado_terceroA.php">Sección A</a>
              <a href="profesores/grado_terceroB.php">Sección B</a>
            </div>
          </div>
          
          <!-- Cuarto Grado -->
          <div class="menu-item">
            <a href="#">Cuarto Grado ▼</a>
            <div class="sub-menu">
              <a href="profesores/grado_cuartoA.php">Sección A</a>
              <a href="profesores/grado_cuartoB.php">Sección B</a>
            </div>
          </div>
          
          <!-- Quinto Grado -->
          <div class="menu-item">
            <a href="#">Quinto Grado ▼</a>
            <div class="sub-menu">
              <a href="profesores/grado_quintoA.php">Sección A</a>
              <a href="profesores/grado_quintoB.php">Sección B</a>
            </div>
          </div>
          
          <!-- Sexto Grado -->
          <div class="menu-item">
            <a href="#">Sexto Grado ▼</a>
            <div class="sub-menu">
              <a href="profesores/grado_sextoA.php">Sección A</a>
              <a href="profesores/grado_sextoB.php">Sección B</a>
            </div>
          </div>
        </div>
      </div>

      <div class="menu-item">
        <a href="formularios/form_estudiante.php">Registro de Estudiantes</a>
      </div>
      
      <div class="menu-item">
        <a href="formularios/form_profesores.php">Registro de Profesores</a>
      </div>
      <div class="menu-item">
        <a href="formularios/listado_de_Profesores.php">Listado de Profesores </a>
      </div>
      <div class="menu-item">
        <a href="RespaldoRestore/php/index.php">Respaldo de Base de datos </a>
      </div>
    </nav>
    <label for="btn-menu">✖️</label>
  </div>
</div>
  </body>
</html>