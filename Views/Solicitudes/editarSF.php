<?php
require_once(__DIR__ . "../../include/UH.php");
// Defensivo: evitar errores si $datosSolicitud no existe
if (!isset($datosSolicitud) || $datosSolicitud === null) {
    $datosSolicitud = [
        'id' => '',
        'titulo' => '',
        'descripcion' => '',
        'estado_id' => ''
    ];
}

// Defensivo: evitar errores si $estados no existe
if (!isset($estados) || $estados === null) {
    $estados = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Solicitud</title>
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
    <h3>Editar Solicitud</h3>
    <form method="POST" action="Index.php?accion=actualizarSF">
        <input type="hidden" name="id" value="<?= htmlspecialchars($datosSolicitud['id']) ?>">
        <input type="hidden" name="id_cliente" value="<?= htmlspecialchars($datosSolicitud['cliente_id']) ?>">

        <p class="fade-label">Título:</p>
        <input type="text" class="form-control" name="titulo" value="<?= htmlspecialchars($datosSolicitud['titulo']) ?>" disabled>

        <p class="fade-label">Descripción:</p>
        <textarea class="form-control" name="descripcion" rows="5" required><?= htmlspecialchars($datosSolicitud['descripcion']) ?></textarea><br><br>

        <!-- Campo de precio envuelto en un div para mostrar/ocultar -->
        <div id="campo-precio" style="display: none;">
            <p class="fade-label">Precio:</p>
            <div class="input-precio">
                <span class="simbolo">$</span>
                <input type="number" min="0.00" step="0.01" id="precio" name="precio" value="<?= htmlspecialchars($datosSolicitud['precio'] ?? 0.0) ?>">
            </div>
            <br><br>
        </div>

        <p class="fade-label">Estado:</p>
        <select name="estado" id="estado-select" required>
            <?php foreach ($estados as $estado): ?>
                <option value="<?= htmlspecialchars($estado['id']) ?>" 
                <?= ($estado['id'] == $datosSolicitud['estado_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($estado['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <button type="submit">Actualizar Solicitud</button>
    </form>
</section>
<br>
</div>
<script src="Assets/js/transicion.js"></script>
<script src="Assets/js/botonvolver.js"></script>
<script>
    // Función para mostrar/ocultar el campo de precio
    function toggleCampoPrecio() {
        const estadoSelect = document.getElementById('estado-select');
        const campoPrecio = document.getElementById('campo-precio');
        const estadoSeleccionado = estadoSelect.value;

        // Mostrar solo si el estado es "Finalizado" (ID 5)
        if (estadoSeleccionado === '5') {
            campoPrecio.style.display = 'block';
        } else {
            campoPrecio.style.display = 'none';
        }
    }

    // Ejecutar al cargar la página para verificar el estado inicial
    document.addEventListener('DOMContentLoaded', function() {
        toggleCampoPrecio();
    });

    // Escuchar cambios en el select de estado
    document.getElementById('estado-select').addEventListener('change', function() {
        toggleCampoPrecio();
    });
</script>
</body>
</html>