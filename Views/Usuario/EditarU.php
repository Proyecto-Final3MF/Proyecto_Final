<?php
require_once(__DIR__ . "../../include/UH.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
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
        <h3>Editar Usuario</h3>
        
        <form method="POST" action="Index.php?accion=actualizarU" enctype="multipart/form-data">

            <input type="hidden" name="id" value="<?= $datos['id'] ?>">
            <input type="hidden" name="foto_actual" value="<?= htmlspecialchars($datos['foto_perfil']) ?>">
            
            <p class="fade-label">Foto de perfil</p>
            <img id="preview" src="<?= htmlspecialchars($datos['foto_perfil']) ?>" alt="Foto de perfil" class="foto-perfil">

            <div class="input-archivo">
            <input type="file" name="foto_perfil" accept="image/*" id="foto_perfil" hidden>
            <label for="foto_perfil" class="btn-boton3-input">Seleccionar Foto</label>
            <span class="nombre-archivo-seleccionado">Ningúna Foto seleccionada</span>
            </div>
            <br>
            
            <p class="fade-label">Nombre:</p>
            <input type="text" name="nombre" value="<?= htmlspecialchars($datos['nombre']) ?>" required><br><br>

             <p class="fade-label"> Email: </p> 
            <input type="email" name="email" value="<?= $datos['email'] ?>" pattern="^[\p{L}0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" 
            title="El dominio debe contener solo letras, números, puntos y guiones (sin tildes ni ñ)." required><br>

            <br><br>
            <input type="submit" value="Guardar cambios">

        </form>
    </section>

    <br>
</div>

<script>
    const inputFoto = document.getElementById('foto_perfil');
    const preview = document.getElementById('preview');

    inputFoto.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.setAttribute('src', e.target.result);
            }
            reader.readAsDataURL(file);
        } else {
            // Si no hay archivo seleccionado, volver a mostrar la foto actual
            preview.setAttribute('src', '<?= htmlspecialchars($datos['foto_perfil']) ?>');
        }
    });
</script>
<script src="Assets/js/imagenformulario.js"></script>
<script src="Assets/js/transicion.js"></script>
<script src="Assets/js/botonvolver.js"></script>
</body>
</html>
