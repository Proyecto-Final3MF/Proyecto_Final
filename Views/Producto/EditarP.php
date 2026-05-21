<?php
    require_once(__DIR__ . "/../include/UH.php");
?>

<title>Editar Dispositivo</title>

<div class="btn-volver-container fade-slide">
    <button class="btn-volver" id="btnVolver">
    <i class="fa fa-arrow-left"></i> Volver
</button>
</div>

<div class="contenedor-formulario">
<section class="formularios99">
    <h3>Editar Dispositivo</h3>
    <form method="POST" action="Index.php?accion=actualizarP" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= htmlspecialchars($datosProducto['id']) ?>">
        <input type="hidden" name="imagen_actual" value="<?= htmlspecialchars($datosProducto['imagen']) ?>">

        <p class="fade-label">Nombre del Dispositivo: </p>
        <label for="nombre" class="form-label"></label>
        <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($datosProducto['nombre']) ?>" autocomplete="off" required> <br><br>

        <p class="fade-label">Imagen actual:</p>
        <div id="vista-previa-contenedor" style="margin-top: 15px;">
    <img id="vista-previa"
         src="<?= htmlspecialchars($datosProducto['imagen']) ?>"
         alt="Vista previa"
         style="max-width:150px; max-height:150px;">
</div>

        <p class="fade-label">Seleccionar nueva Imagen (opcional):</p>
<div class="input-archivo">
    <input type="file" id="imagen" name="imagen" accept="image/*" autocomplete="off" hidden>
    <label for="imagen" class="btn-boton3-input">Seleccionar Foto</label>
    <span id="nombre-archivo-seleccionado">Ningúna Foto seleccionada</span>
</div>

    

<br><br>


        <p class="fade-label">Categoria:</p>
        <select id="categoria" name="categoria" required>
            <?php foreach ($categorias as $categoria): ?>
                <option value="<?= $categoria['id'] ?>" <?= ($categoria['id'] == $datosProducto['id_cat']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($categoria['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select> <br><br>

        <button type="submit">Actualizar</button>
    </form>
</section>
<br>
</div>
<script src="Assets/js/imagenformulario.js"></script>
<script src="Assets/js/trancicion.js"></script>
<script src="Assets/js/botonvolver.js"></script>
<script src="Assets/js/vistapreviafoto.js"></script>
</body>
</html>