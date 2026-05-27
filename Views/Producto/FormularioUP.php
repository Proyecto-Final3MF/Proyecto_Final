<?php
require_once(__DIR__ . "/../include/UH.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Dispositivo Urgente</title>
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

    <h3>Nuevo Dispositivo Urgente</h3>

    <form method="POST" action="Index.php?accion=urgenteGP" enctype="multipart/form-data">

        <p class="fade-label">Nombre del Dispositivo: </p>

        <label for="nombre" class="form-label"></label>
        <input type="text" class="form-control" id="nombre" name="nombre" autocomplete="off" required> <br><br>

        <p class="fade-label">Imagen: </p>

        <div id="vista-previa-contenedor" style="margin-top: 15px;">
            
            <img id="vista-previa" src="./Assets/imagenes/sincargas4.png" alt="Vista previa" style="max-width:150px; max-height:150px; display:center;">
    
        </div>

        <div class="input-archivo">

            <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*" autocomplete="off" required hidden>
            <label for="imagen" class="btn-boton3-input">Seleccionar Foto</label>
            <span id="nombre-archivo-seleccionado">Ningúna Foto seleccionada</span>
    
        </div>

        <br>

        <p class="fade-label">Tipo de Equipo:</p>
       
        <select id="categoria" name="categoria" class="js-example-basic-single" name="state" required>

            <option value="">-- Seleccione el tipo de equipo --</option>

            <?php foreach ($categorias as $categoria): ?>
                <option value="<?= $categoria['id'] ?>"><?= htmlspecialchars($categoria['nombre']) ?></option>
            <?php endforeach; ?>

        </select>  

        <br><br>
        
        <button type="submit">Crear</button>

    </form>

</section>

<br>
</div>

    <script src="Assets/js/vistapreviafoto.js"></script>
    <script src="Assets/js/imagenformulario.js"></script>
    <script src="Assets/js/transicion.js"></script>
    <script src="Assets/js/botonvolver.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="Assets/js/inicializarselect2.js"></script> 

</body>
</html>