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

require './funciones.php';
include("conexion.php");

// Verificar si se proporcionó un ID y es numérico
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "ID de profesor no válido";
    header("Location: listado_de_Profesores.php");
    exit();
}

$id = intval($_GET['id']);

// Obtener los datos del profesor
$sql = "SELECT * FROM profesor WHERE id = ?";
$stmt = $conex->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    $_SESSION['error'] = "Profesor no encontrado";
    header("Location: listado_de_Profesores.php");
    exit();
}

$profesor = $resultado->fetch_assoc();

// Inicializar variables con los datos del profesor
$cedula = $profesor['cedula'];
$nombres = $profesor['nombres'];
$apellidos = $profesor['apellidos'];
$grado = $profesor['grado'];
$seccion = $profesor['seccion'];
$fecha_nac = $profesor['fecha_nac'];
$genero = $profesor['genero'];
$telefono = $profesor['telefono'];
$correo = $profesor['correo'];

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
    $grado=$_POST['grado'];
    $seccion=$_POST['seccion'];
    $fecha_nac=$_POST['fecha_nac'];
    $genero=$_POST['genero'];
    $telefono=$_POST['telefono'];
    $correo=$_POST['correo'];
    
    //validar campos vacios
    if($cedula=="" || $nombres=="" || $apellidos=="" || $grado=="" || $seccion=="" || $fecha_nac=="" || $genero=="" || $telefono=="" || $correo==""){
      echo "<script languaje='JavaScript'>
            alert('Los campos no pueden estar vacíos');
            location.assign('listado_de_Profesores.php');
            </script>";
    }
    
    //validar cedula
    $sql="select * from profesor where cedula='".$cedula."' and id<>'".$id."'";
    $resultado=mysqli_query($conex,$sql);
    $row=mysqli_fetch_assoc($resultado);
    if($row>0){
      echo "<script languaje='JavaScript'>
            alert('La cédula ya existe');
            location.assign('listado_de_Profesores.php');
            </script>";
    }
    
    //validar fecha de nacimiento
    $fecha_nac= date('Y-m-d', strtotime($fecha_nac));
    $fecha_actual = date('Y-m-d');
    if($fecha_nac > $fecha_actual){
      echo "<script languaje='JavaScript'>
            alert('La fecha de nacimiento no puede ser mayor a la fecha actual');
            location.assign('listado_de_profesores.php');
            </script>";
    }

    //validar formato fecha de nacimiento
    if(!preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $fecha_nac)){
      echo "<script languaje='JavaScript'>
            alert('La fecha de nacimiento no es válida');
            location.assign('listado_de_profesores.php');
            </script>";
    }
    
    //validar genero
    if($genero != 'M' && $genero != 'F'){
      echo "<script languaje='JavaScript'>
            alert('El género no es válido');
            location.assign('listado_de_profesores.php');
            </script>";
    }
    
 
    
    //validar correo
    if(!filter_var($correo, FILTER_VALIDATE_EMAIL)){
      echo "<script languaje='JavaScript'>
            alert('El correo electrónico no es válido');
            location.assign('listado_de_profesores.php');
            </script>";
    }

    //update profesor en la base de datos
    $sql="update profesor set cedula='".$cedula."', nombres='".$nombres."', apellidos='".$apellidos."', 
          grado='".$grado."', seccion='".$seccion."', fecha_nac='".$fecha_nac."',
          genero='".$genero."', telefono='".$telefono."', correo='".$correo."' where id='".$id."'";

    $resultado=mysqli_query($conex,$sql);

    if($resultado){
      echo "<script languaje='JavaScript'>
            alert('Los datos se actualizaron correctamente');
            location.assign('listado_de_profesores.php');
            </script>";
    }else{
      echo "<script languaje='JavaScript'>
            alert('Los datos NO se actualizaron correctamente');
            location.assign('listado_de_profesores.php');
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
    $grado=$row['grado'];
    $seccion=$row['seccion'];
    $fecha_nac=$row['fecha_nac'];
    $genero=$row['genero'];
    $telefono=$row['telefono'];
    $correo=$row['correo'];

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
      <label for="grado">Grado:</label>
      <select name="grado" id="grado" required>
        <option value="<?php echo $grado; ?>" hidden=""><?php echo $grado; ?></option>
        <option value="Primero">Primero</option>
        <option value="Segundo">Segundo</option>
        <option value="Tercero">Tercero</option>
        <option value="Cuarto">Cuarto</option>
        <option value="Quinto">Quinto</option>
        <option value="Sexto">Sexto</option>
      </select>
    </div>

    <div class="input-group">
      <label for="seccion">Sección:</label>
      <select name="seccion" id="seccion" required>
        <option value="<?php echo $seccion; ?>" hidden=""><?php echo $seccion; ?></option>
        <option value="A">A</option>
        <option value="B">B</option>
      </select>
    </div>

    <div class="input-group">
      <label for="fecha_nac">Fecha de Nacimiento:</label>
      <input type="date" name="fecha_nac" id="fecha_nac" value="<?php echo $fecha_nac; ?>" required autocomplete="off">
    </div>

    <div class="input-group">
      <label for="genero">Género:</label>
      <select name="genero" id="genero" required>
        <option value="<?php echo $genero; ?>" hidden=""><?php echo ($genero == 'M') ? 'Masculino' : 'Femenino'; ?></option>
        <option value="M">Masculino</option>
        <option value="F">Femenino</option>
      </select>
    </div>

    <div class="input-group">
      <label for="telefono">Teléfono:</label>
      <input type="text" name="telefono" id="telefono" value="<?php echo $telefono; ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '')" maxlength="11" required autocomplete="off">
    </div>

    <div class="input-group">
      <label for="correo">Correo Electrónico:</label>
      <input type="email" name="correo" id="correo" value="<?php echo $correo; ?>" required autocomplete="off">
    </div>

    <input type="hidden" name="id" value="<?php echo $id ?>">

    <div class="input-group">
      <div class="buttons">
        <button type="button" onclick="window.location.href='./listado_de_Profesores.php'">Cancelar</button>
        <button type="submit" name="enviar">Actualizar</button>
      </div>
    </div>

</form>
<?php
  }
?>
</body>
</html>