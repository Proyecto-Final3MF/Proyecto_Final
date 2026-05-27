<?php 
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != ROL_ADMIN) {
    header("Location: Index.php?accion=redireccion");
} 

require_once(__DIR__ . "/../../../include/UH.php");

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nueva Categoria</title>
    <link rel="stylesheet" href="./Assets/css/Main.css">
</head>

<body>
   <div class="btn-volver-container fade-slide">
  <button class="btn-volver" id="btnVolver">
    <i class="fa fa-arrow-left"></i> Volver
  </button>
</div>
    <div class="contenedor-formulario">
    <section class="formularios99">
    <h3>Crear Nueva Categoria</h3>
    <form action="Index.php?accion=guardarC" method="post">
        <label for="nombre">Nueva Categoria: </label>
        <input type="text" id="nombre" name="nombre"/>
        <button type="submit">Agregar Categoria</button>
    </form>
    </section>
</div>
<script src="Assets/js/transicion.js"></script>
<script src="Assets/js/botonvolver.js"></script>
</body>
</html>