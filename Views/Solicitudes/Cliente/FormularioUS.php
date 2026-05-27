<?php
require_once(__DIR__ . "/../../include/UH.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Solicitud Urgente</title>
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
    <form method="POST" action="Index.php?accion=guardarSU">
        
        <p class="fade-label">Titulo: </p>
        <label for="titulo" class="form-label"></label>
        <input type="text" placeholder="Escribi aca el problema del dispositivo" class="form-control" id="titulo" name="titulo" autocomplete="off" required> <br><br>
               
        <p class="fade-label">Producto:</p>
        <div class="producto-con-boton">
            
            <?php 
                $nombre_producto = 'No disponible'; // Default value
                $producto_id_valido = $producto_preseleccionado_id ?? null;
                
                // Find the product name based on the pre-selected ID
                if ($producto_id_valido && isset($productos) && is_array($productos)) {
                    foreach ($productos as $p) {
                        if ($p['id'] == $producto_id_valido) {
                            $nombre_producto = htmlspecialchars($p['nombre']);
                            break;
                        }
                    }
                }
            ?>
            
            <input type="text" class="form-control" value="<?= $nombre_producto ?>" disabled>
            
            <input type="hidden" name="producto" value="<?= $producto_id_valido ?>" required>
            
            </div>

        <p class="fade-label">Descripcion:</p>
        <textarea class="form-control" placeholder="Detalles del problema" name="descripcion" id="descripcion" rows="5" required></textarea> <br><br>
                        
        <label for="prioridad"><p class="fade-label">Nivel de Prioridad: </p></label>
            <input type="text" class="form-control" value="Urgente" disabled>
            <input type="hidden" name="prioridad" value="urgente">
            <br><br>

        <input type="submit" value="Guardar">
        
    </form>
    </section>
    <br>
    </div>
    <script src="Assets/js/transicion.js"></script>
    <script src="Assets/js/botonvolver.js"></script>
</body>
</html>