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

    require '../../funciones.php';

    include("conexion.php");

  
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asistencia</title>
    <link rel="stylesheet" type="text/css" href="../../inventario_styles.css">

</head>
<body>
  <?php 

  if(isset($_POST['enviar'])){
    //aqui entra cuando presiona el boton de enviar 
    $id=$_POST['id'];
    $nombres=$_POST['nombres'];
    $apellidos=$_POST['apellidos'];
    $cedula_escolar=$_POST['cedula_escolar'];
    $fecha_nacimiento=$_POST['fecha_nacimiento'];
    $genero=$_POST['genero'];
    $seccion=$_POST['seccion'];
    //validar campos vacios
    if($nombres=="" || $apellidos=="" || $cedula_escolar=="" || $fecha_nacimiento=="" || $genero=="" || $seccion==""){
      echo "<script languaje='JavaScript'>
            alert('Los campos no pueden estar vacios');
           location.assign('../grado_quintoA.php');
            </script>";
    }
    //validar cedula escolar
    $sql="select * from quinto where cedula_escolar='".$cedula_escolar."' and id<>'".$id."'";
    $resultado=mysqli_query($conex,$sql);
    $row=mysqli_fetch_assoc($resultado);
    if($row>0){
      echo "<script languaje='JavaScript'>
            alert('La cedula escolar ya existe');
           location.assign('../grado_quintoA.php');
            </script>";
    }
    //validar fecha de nacimiento
    $fecha_nacimiento= date('Y-m-d', strtotime($fecha_nacimiento));
    $fecha_actual = date('Y-m-d');
    if($fecha_nacimiento > $fecha_actual){
      echo "<script languaje='JavaScript'>
            alert('La fecha de nacimiento no puede ser mayor a la fecha actual');
            location.assign('../grado_quintoA.php');
            </script>";
    }


  
    //validar fecha de nacimiento
    if(!preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $fecha_nacimiento)){
      echo "<script languaje='JavaScript'>
            alert('La fecha de nacimiento no es valida');
            location.assign('../grado_quintoA.php');
            </script>";
    }
    //validar genero
    if(!preg_match('/^[a-zA-Z\s]+$/', $genero)){
      echo "<script languaje='JavaScript'>
            alert('El genero no es valido');
              location.assign('../grado_quintoA.php');
            </script>";
    }
    //validar seccion
    if(!preg_match('/^[a-zA-Z\s]+$/', $seccion)){
      echo "<script languaje='JavaScript'>
            alert('La seccion no es valida');
             location.assign('../grado_quintoA.php');
            </script>";
    }

    //update estudiantes de las ../../funciones.php
    $sql="update quinto set nombres='".$nombres."', apellidos='".$apellidos."', cedula_escolar='".$cedula_escolar."',fecha_nacimiento='".$fecha_nacimiento."',
    genero='".$genero."',seccion='".$seccion."' where id='".$id."'";

    $resultado=mysqli_query($conex,$sql);

    if($resultado){
      echo "<script languaje='JavaScript'>
            alert('Los datos se actualizaron correctamente');
            location.assign('../grado_quintoA.php');
            </script>";

    }else{
      echo "<script languaje='JavaScript'>
            alert('Los datos No se actualizaron correctamente');
            location.assign('../grado_quintoA.php');
            </script>";

    }

    mysqli_close($conex);

  }else{
    //aqui entra cuando no presiona el boton de enviar
    $id=$_GET['id'];
    $sql="select * from quinto where id='".$id."'";
    $resultado=mysqli_query($conex,$sql);
    $row = mysqli_fetch_assoc($resultado);
    $nombres=$row['nombres'];
    $apellidos=$row['apellidos'];
    $cedula_escolar=$row['cedula_escolar'];
    $fecha_nacimiento=$row['fecha_nacimiento'];
    $genero=$row['genero'];
    $seccion=$row['seccion'];

    mysqli_close($conex);


?>

<nav class="logo-nav">
    <a href="#" class="logo-link">Asistencia</a>
    <div class="logo-placeholder"></div>
  </nav>

  

    <h2>REGISTRO DE ESTUDIANTES DE QUINTO</h2>
    
    <form action="<?=$_SERVER['PHP_SELF'] ?>" method="POST">

    <div class="input-group">
      <label for="nombres">Nombres:</label>
      <input type="text" name="nombres" id="nombres" value="<?php echo $nombres;  ?>" oninput="this.value = this.value.replace(/[^A-Za-z áéíóúÁÉÍÓÚñÑ]/g, '')" required autocomplete="off" required><br><br>
      </div>
    
    <div class="input-group">
      <label for="apellidos">Apellidos:</label>
      <input type="text" name="apellidos" id="apellidos" value="<?php echo $apellidos;  ?>" oninput="this.value = this.value.replace(/[^A-Za-z áéíóúÁÉÍÓÚñÑ]/g, '')" required autocomplete="off" required><br><br>
      </div>

<div class="input-group">
<label for="cedula_escolar">C.I:</label>
<input type="text" id="cedula_escolar" name="cedula_escolar" placeholder="Ingrese Cedula" value="<?php echo $cedula_escolar;  ?>" required autocomplete="off" required>

<script>
document.getElementById('cedula').addEventListener('focus', function(e) {
    // Agregar "V-" al enfocar si no existe
    if (!this.value.startsWith('V-')) {
        this.value = 'V-' + this.value.replace(/[^0-9]/g, '');
    }
});

document.getElementById('cedula').addEventListener('keydown', function(e) {
    const prefix = 'V-';
    const prefixLength = prefix.length;
    const selectionStart = this.selectionStart;
    
    // Bloquear edición antes del prefijo
    if (selectionStart < prefixLength) {
        if (![9, 16, 17, 18, 37, 38, 39, 40].includes(e.keyCode)) {
            e.preventDefault();
            return;
        }
    }

    // Permitir solo números y teclas de control después del prefijo
    if (selectionStart >= prefixLength) {
        const allowedKeys = [8, 9, 13, 37, 38, 39, 46]; // Teclas de control
        const isNumber = (e.keyCode >= 48 && e.keyCode <= 57) || 
                        (e.keyCode >= 96 && e.keyCode <= 105);
        
        if (!allowedKeys.includes(e.keyCode) && !isNumber) {
            e.preventDefault();
        }
    }
});

document.getElementById('cedula').addEventListener('input', function(e) {
    const prefix = 'V-';
    const prefixLength = prefix.length;
    let input = e.target;
    let caret = input.selectionStart;
    
    // Separar prefijo y números
    let numbers = input.value.slice(prefixLength).replace(/[^0-9]/g, '');
    
    // Formatear números con puntos
    let formatted = numbers.split('').reverse().join('')
                         .match(/.{1,3}/g)
                         ?.join('.')
                         .split('').reverse().join('') || '';
    
    // Reconstruir valor completo
    input.value = prefix + formatted;
    
    // Calcular nueva posición del cursor
    let numbersCaret = Math.max(caret - prefixLength, 0);
    let dotsAdded = Math.floor((numbers.length - 1) / 3);
    let newCaret = Math.min(
        prefixLength + numbersCaret + dotsAdded,
        input.value.length
    );
    
    input.setSelectionRange(newCaret, newCaret);
});
</script>

        </div>

        


    <div class="input-group">
      <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
      <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" required autocomplete="off" required value="<?php echo $fecha_nacimiento;  ?>"><br><br>
    </div>

    <div class="input-group">
      <label for="Genero">Genero:</label>
      <select name="genero" id="genero" >
        <option value="<?php echo $genero;  ?>" hidden=""><?php echo $genero;  ?></option>
        <option value="Masculino">Masculino</option>
        <option value="Femenino">Femenino</option>
      </select><br><br>
    </div>

    

    <div class="input-group">
      <label for="seccion">Seccion:</label>
      <select name="seccion" id="seccion" required autocomplete="off" required>
        <option value="<?php echo $seccion;  ?>" hidden=""><?php echo $seccion;  ?></option>
        <option value="A">A</option>
        <option value="B">B</option>
      </select><br><br>
    </div>

    <div class="input-group">
    </div>
    <input type="hidden" name="id" value="<?php echo $id ?>">

    <div class="input-group">
      <div class="buttons">
        <button type="reset">Limpiar</button>
        <button type="submit" name="enviar">Registrar</button>
      </div>
    </div>

</form>
<?php
  }
    ?>
</body>
</html>