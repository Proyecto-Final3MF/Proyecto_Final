<?php
require_once(__DIR__ . "/../../include/UH.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Solicitud</title>
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
    <h3>Nueva Solicitud</h3>
    <form method="POST" action="Index.php?accion=guardarS">
        
        <p class="fade-label">Titulo: </p>
        <label for="titulo" class="form-label"></label>
        <input type="text" placeholder="Escribi aca el problema del dispositivo" class="form-control" id="titulo" name="titulo" autocomplete="off" required> <br><br>
               
        <p class="fade-label">Producto:</p>
        <div class="producto-con-boton">
        <select id="producto" name="producto" required>
        <option value=""></option>
        <?php foreach ($productos as $producto): ?>
        <option value="<?= $producto['id'] ?>"><?= htmlspecialchars($producto['nombre'])?></option>
        <?php endforeach; ?>
        </select>

        <a href="Index.php?accion=formularioP" class="btn-boton4443">
        <i class="fa-solid fa-plus-circle"></i>
        </a>
        </div>

        <p class="fade-label">Descripcion:</p>
        <textarea class="form-control" placeholder="Detalles del problema" name="descripcion" id="descripcion" rows="5" required></textarea> <br><br>
                        
        <label for="prioridad"><p class="fade-label">Nivel de Prioridad: </p></label>
            <select name="prioridad" id="prioridad" required>
                <option value="baja">Baja</option>
                <option value="media">Media</option>
                <option value="alta">Alta</option>
                <option value="urgente">Urgente</option>
            </select><br><br>

        <input type="submit" value="Guardar">
        
    </form>
    </section>
    <br>
    </div>
    <script src="Assets/js/transicion.js"></script>
    <script src="Assets/js/botonvolver.js"></script>
</body>
</html>