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

    require './funciones.php';

    include("conexion.php");

  
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Profesor</title>
    <link rel="stylesheet" href="stylessprofesores.css">
</head>
<body>
  <?php 

  if(isset($_POST['enviar'])){
    //aqui entra cuando presiona el boton de enviar 
    $id=$_POST['id'];
    $cedula=$_POST['cedula'];
    $nombres=$_POST['nombres'];
    $apellidos=$_POST['apellidos'];
    $correo=$_POST['correo'];
    $rol=$_POST['rol'];
    


    //update profesor en la base de datos
    $sql="update profesor set cedula='".$cedula."', nombres='".$nombres."', apellidos='".$apellidos."', 
         correo='".$correo."', rol='".$rol."' where id='".$id."'";

    $resultado=mysqli_query($conex,$sql);

    if($resultado){
      echo "<script languaje='JavaScript'>
            alert('Los datos se actualizaron correctamente');
            location.assign('../profesores/lista_usuarios.php');
            </script>";
    }else{
      echo "<script languaje='JavaScript'>
            alert('Los datos NO se actualizaron correctamente');
            location.assign('../profesores/lista_usuarios.php');
            </script>";
    }

    mysqli_close($conex);

  }else{
    //aqui entra cuando no presiona el boton de enviar
    $id=$_GET['id'];
    $sql="select * from profesor where id='".$id."'";
    $resultado=mysqli_query($conex,$sql);
    $row = mysqli_fetch_assoc($resultado);
    $cedula=$row['cedula'];
    $nombres=$row['nombres'];
    $apellidos=$row['apellidos'];
    $correo=$row['correo'];
    $rol=$row['rol'];

    mysqli_close($conex);
?>

<nav class="logo-nav">
    <a href="#" class="logo-link">Asistencia</a>
    <div class="logo-placeholder"></div>
</nav>

<h2>EDITAR REGISTRO DE PROFESOR</h2>
    
<form action="<?=$_SERVER['PHP_SELF'] ?>" method="POST">

    <div class="input-group">
      <label for="cedula">Cédula:</label>
      <input type="text" name="cedula" id="cedula" value="<?php echo $cedula; ?>" required autocomplete="off">
    </div>
    
    <div class="input-group">
      <label for="nombres">Nombres:</label>
      <input type="text" name="nombres" id="nombres" value="<?php echo $nombres; ?>" oninput="this.value = this.value.replace(/[^A-Za-z áéíóúÁÉÍÓÚñÑ]/g, '')" required autocomplete="off">
    </div>
    
    <div class="input-group">
      <label for="apellidos">Apellidos:</label>
      <input type="text" name="apellidos" id="apellidos" value="<?php echo $apellidos; ?>" oninput="this.value = this.value.replace(/[^A-Za-z áéíóúÁÉÍÓÚñÑ]/g, '')" required autocomplete="off">
    </div>

    

    <div class="input-group">
      <label for="rol">Rol:</label>
      <select name="rol" id="rol" required>
        <option value="<?php echo $rol; ?>" hidden=""><?php echo $rol; ?></option>
        <option value="admin">admin</option>
        <option value="visitante">visitante</option>
      </select>
    </div>

    <input type="hidden" name="id" value="<?php echo $id ?>">

    <div class="input-group">
      <div class="buttons">
        <button type="button" onclick="window.location.href='../profesores/lista_usuarios.php'">Cancelar</button>
        <button type="submit" name="enviar">Actualizar</button>
      </div>
    </div>

</form>
<?php
  }
?>
</body>
</html>