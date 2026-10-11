<?php
// login/funciones.php (Asegúrate de que esta ruta es correcta)
require 'login/funciones.php';

$registracion = new Registration();

if (isset($_POST['submit'])) {
    // Validar coincidencia de contraseñas en el servidor
    if ($_POST['password'] !== $_POST['confirmar_contraseña']) {
        echo "<script> alert('Las contraseñas no coinciden'); </script>";
        exit;
    }
    
    $resultadoregistro = $registracion->docentes(
        $_POST['cedula'],
        $_POST['nombres'], // Nombre corregido
        $_POST['apellidos'],
        $_POST['correo'],
        $_POST['password']
    );
    
    if ($resultadoregistro == 1) {
        echo "<script> alert('Registro exitoso'); 
        location.assign('index.php');</script>";
    } elseif ($resultadoregistro == 10) {
        echo "<script> alert('Usuario o correo ya existen'); </script>";
    } else {
        echo "<script> alert('Error en el registro'); </script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="styless.css">
    <script>
document.querySelector('form').addEventListener('submit', function(e) {
    const pass = document.querySelector('input[name="password"]').value;
    const confirmPass = document.querySelector('input[name="confirmar_contraseña"]').value;
    
    if (pass !== confirmPass) {
        e.preventDefault();
        alert('Las contraseñas no coinciden!');
    }
});
</script>
</head>
<body>

    <form method="post">

    <h2>Hola</h2>
    <p>Inicia tu Formulario</p>

    <div class="input-wrapper" >
  
  <select id="tipo-doc">
  <option value="">Seleccione tipo de cedula</option>
    <option value="V">V</option>
    <option value="E">E</option>
  </select>        
  </div>
       
            <div class="input-wrapper" >
<img class="input-icon" src="../images/name.svg" alt="">
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

    <div class="input-wrapper" >
        <input type="text" name="nombres" placeholder="Nombres" oninput="this.value = this.value.replace(/[^A-Za-z áéíóúÁÉÍÓÚñÑ]/g, '')" required autocomplete="off" required>
        <img class="input-icon" src="images/name.svg" alt="">
    </div>

    <div class="input-wrapper" >
        <input type="text" name="apellidos" placeholder="Apellidos" oninput="this.value = this.value.replace(/[^A-Za-z áéíóúÁÉÍÓÚñÑ]/g, '')" required autocomplete="off" required>
        <img class="input-icon" src="images/name.svg" alt="">
    </div>

    <div class="input-wrapper" >
    <input type="email" name="correo" placeholder="Correo" autocomplete="off"> <!-- Antes era name="email" -->
    <img class="input-icon" src="images/email.svg" alt="">
    </div>


    <div class="input-wrapper" >
    <input type="password" name="password" placeholder="Contraseña">
        <img class="input-icon" src="images/password.svg" alt="">
    </div>

    <div class="input-wrapper" >
        <input type="password" name="confirmar_contraseña" placeholder="Confirmar Contraseña">
        <img class="input-icon" src="images/password.svg" alt="">
    </div>

    <input class="btn" type="submit" name="submit" value="Enviar">

    <div class="link">
            <a href="index.php" class="register-link">Iniciar Sesión</a>

    </form>

    
</body>
</html>