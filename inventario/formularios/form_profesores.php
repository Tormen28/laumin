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

    require '../funciones.php';

  

    $registracion = new Registration();

    if (isset($_POST['submit'])) {
       $resultadoregistro = $registracion->docentes(
        $_POST['cedula'],
        $_POST['nombre'],
        $_POST['apellidos'],
        $_POST['grado'],
        $_POST['seccion'],
        $_POST['fecha_nac'],
        $_POST['genero'],
        $_POST['telefono'],
        $_POST['correo'],
        $_POST['contraseña']
       
       );
       if ($resultadoregistro == 1) {
        echo "<script> alert('Registro exitoso'); </script>";
       } elseif ($resultadoregistro == 10){
        echo "<script> alert('Registro ya existe'); </script>";

       } elseif ($resultadoregistro == 100){
        echo "<script> alert('Hubo un fallo'); </script>";
    }
}
?>
   

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asistencia</title>
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
            <a href="../formularios/form_estudiante.php" class="menu-link">Registrar estudiantes<span class="submenu-icon">&#9660;</span></a>
        </li>
        

      </li>
      <li class="menu-item has-submenu">
      <button type="submit" name="regresar" onclick="window.location.href='../index.php'">Regresar</button> 
    </ul>
  </nav>

    <h2>REGISTRO DE PROFESORES</h2>
    
    <form action="" method="POST">

    <div class="input-group">
    <label for="tipo-doc">Tipo:</label>
<select id="tipo-doc">
  <option value="V">V</option>
  <option value="E">E</option>
</select>        
</div>

    <div class="input-group">
          <label for="cedula">C.I:</label>
          <input type="text" id="cedula" name="cedula" placeholder="Ingrese Cédula" required autocomplete="off">

<script>
const tipoDoc = document.getElementById('tipo-doc');
const cedula = document.getElementById('cedula');

function getPrefix() {
  return tipoDoc.value + '-';
}

cedula.addEventListener('focus', function(e) {
  const prefix = getPrefix();
  if (!this.value.startsWith(prefix)) {
    this.value = prefix + this.value.replace(/[^0-9]/g, '');
  }
});

cedula.addEventListener('keydown', function(e) {
  const prefix = getPrefix();
  const prefixLength = prefix.length;
  const selectionStart = this.selectionStart;
  
  if (selectionStart < prefixLength) {
    if (![9, 16, 17, 18, 37, 38, 39, 40].includes(e.keyCode)) {
      e.preventDefault();
      return;
    }
  }

  if (selectionStart >= prefixLength) {
    const allowedKeys = [8, 9, 13, 37, 38, 39, 46];
    const isNumber = (e.keyCode >= 48 && e.keyCode <= 57) || 
                    (e.keyCode >= 96 && e.keyCode <= 105);
    
    if (!allowedKeys.includes(e.keyCode) && !isNumber) {
      e.preventDefault();
    }
  }
});

cedula.addEventListener('input', function(e) {
  const prefix = getPrefix();
  const prefixLength = prefix.length;
  let input = e.target;
  let caret = input.selectionStart;
  
  let numbers = input.value.slice(prefixLength).replace(/[^0-9]/g, '');
  
  let formatted = numbers.split('').reverse().join('')
                       .match(/.{1,3}/g)
                       ?.join('.')
                       .split('').reverse().join('') || '';
  
  input.value = prefix + formatted;
  
  let numbersCaret = Math.max(caret - prefixLength, 0);
  let dotsAdded = Math.floor((numbers.length - 1) / 3);
  let newCaret = Math.min(
    prefixLength + numbersCaret + dotsAdded,
    input.value.length
  );
  
  input.setSelectionRange(newCaret, newCaret);
});

// Actualizar el prefijo cuando cambia el tipo de documento
tipoDoc.addEventListener('change', function() {
  const currentNumbers = cedula.value.replace(/^[VE]-/, '').replace(/[^0-9]/g, '');
  cedula.value = getPrefix() + currentNumbers;
  cedula.dispatchEvent(new Event('input'));
});
</script>


            </div> 


            <div class="input-group">
          <label for="nombre">Nombres:</label>
          <input type="text" name="nombre" placeholder="Ingrese sus Nombres" id="nombre" oninput="this.value = this.value.replace(/[^A-Za-z áéíóúÁÉÍÓÚñÑ]/g, '')" required autocomplete="off" required><br><br>
        </div>
        

            <div class="input-group">
          <label for="Apellidos">Apellidos:</label>
          <input type="text" name="apellidos" id="apellidos" placeholder="Ingrese sus Apellidos" oninput="this.value = this.value.replace(/[^A-Za-z áéíóúÁÉÍÓÚñÑ]/g, '')" required autocomplete="off" required><br><br>
        </div>

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

            </select><br><br>
        </div>

        <div class="input-group">
          <label for="seccion">Seccion:</label>
          <select id="selectorseccion" name="seccion" required>
                <option value="">-- Elija una Sección --</option>
                <option value="A">A</option>
                <option value="B">B</option>
                

            </select><br><br>
        </div>

        <div class="input-group">
          <label for="fecha_nac">Fecha de Nacimiento:</label>
          <input type="date" name="fecha_nac" id="fecha_nac" required autocomplete="off" required><br><br>
        </div>

        <div class="input-group">
          <label for="Genero">Genero:</label>
          <select name="genero" id="genero" required>
            <option value="" hidden="">Seleccione un genero</option>
            <option value="Masculino">Masculino</option>
            <option value="Femenino">Femenino</option>
          </select><br><br>
        </div>

     

        <div class="input-group">
          <label for="telefono">Teléfono:</label>
          <input type="text" id="telefono" name="telefono" placeholder="Ingresar Numero de Telefono"  autocomplete="off">

<script>
document.getElementById('telefono').addEventListener('keydown', function(e) {
    // Permitir solo números y teclas de control
    if (
        [8, 9, 13, 37, 38, 39, 46].includes(e.keyCode) ||  // Teclas de control
        (e.keyCode >= 48 && e.keyCode <= 57) ||            // Números teclado principal
        (e.keyCode >= 96 && e.keyCode <= 105)               // Números teclado numérico
    ) {
        return;
    }
    e.preventDefault();
});

document.getElementById('telefono').addEventListener('input', function(e) {
    const input = e.target;
    const caret = input.selectionStart;
    const numbers = input.value.replace(/[^0-9]/g, '');
    
    // Formatear con guiones cada 4 dígitos
    const formatted = numbers.split('').reduce((acc, digit, index) => {
        return (index % 4 === 0 && index !== 0) 
            ? acc + '-' + digit 
            : acc + digit;
    }, '');
    
    // Ajustar posición del cursor
    const dashesInValue = (formatted.match(/-/g) || []).length;
    const dashesBeforeCaret = (input.value.slice(0, caret).match(/-/g) || []).length;
    const newCaret = caret + (dashesInValue - dashesBeforeCaret);
    
    input.value = formatted;
    input.setSelectionRange(newCaret, newCaret);
});
</script>

        </div>

        <div class="input-group">
          <label for="Correo">Correo Gmail:</label>
          <input type="email" name="correo" placeholder="Ingrese su Correo" id="correo" required autocomplete="off" required><br><br>
        </div>

        <div class="input-group">
          <label for="Contraseña">Contraseña:</label>
          <input type="text" name="contraseña" placeholder="Ingrese su Contraseña" id="correo" required autocomplete="off" required><br><br>
        </div>


        <div class="input-group">
        </div>

        <div class="input-group">
          <div class="buttons">
            <button type="reset">Limpiar</button>
            <button type="submit" name="submit">Registrar</button>
          </div>
        </div>

    </form>

</body>
</html>