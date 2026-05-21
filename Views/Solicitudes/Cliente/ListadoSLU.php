<?php
require_once(__DIR__ . "/../../include/UH.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sus Solicitudes</title>
    <link rel="stylesheet" href="./Assets/css/Main.css" />
</head>
<body>
    <div class="content-wrapper"> 
    <div class="btn-volver-container fade-slide">
  <button class="btn-volver" id="btnVolver">
    <i class="fa fa-arrow-left"></i> Volver
  </button>
</div>
<br>
    <div class="sticky-header">
    <div>
    <h1 class="inicio55">Solicitudes Recien Creadas</h1>
    <p class="fade-slide">Aqui se mostraran todas tus solicitudes que ningun tecnico a aceptado todavia</p>
    <div class="botones-container fade-slide">
    <a href="Index.php?accion=formularioS"><button class="btn btn-boton4442 btn-crear"> <i class="fa-solid fa-plus-circle"></i></button></a>
</div>
    </div>
    <table>
        <thead>
            <tr>
                <th>Titulo</th>
                <th>Producto</th>
                <th>Prioridad</th>
                <th>Descripcion</th>
                <th>Fecha de Creacion</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
    <?php
    if (!empty($resultados)) {
        foreach ($resultados as $resultado) {
            ?>
            <tr class="list-item">
                <td><?= htmlspecialchars($resultado['titulo']); ?></td>
                <td>
                    <img src="<?= htmlspecialchars($resultado['imagen']);?>" alt="Imagen del producto"class="zoom-img"/>
                    <?= htmlspecialchars($resultado['nombre']) ?>
                </td>
                <td><?= htmlspecialchars($resultado['prioridad']); ?></td>
                <td><?= htmlspecialchars($resultado['descripcion']); ?></td>
                <td><?= htmlspecialchars($resultado['fecha_creacion']); ?></td>
                <td>
                    <div class="btn-group-actions">  
                    <a href="Index.php?accion=borrarS&id=<?= $resultado['id']; ?>" class="icon-btn delete">
                    <i class="fa fa-trash"></i>
                    </a>
                    <a href="Index.php?accion=solicitud_historia&id_solicitud=<?= $resultado['id']; ?>" class="icon-btn historial">
                    <i class="fa fa-file-alt"></i>
                    </a>
                    </div>
                </td>
           
            <?php
        }
        ?> 
         </tr>
        <?php
    } else {
        ?>
        <tr>
            <td colspan="6">No hay solicitudes recien creadas</td>
        </tr>
        <?php
    }
    ?>
</tbody>
</table>
</div>

<div class='pagination-container'>
        <nav>
            <ul class="pagination">
                <li data-page="prev">
                    <span> &lt; <span class="sr-only">(anterior)</span></span>
                </li>
                <li data-page="next" id="prev">
                    <span> &gt; <span class="sr-only">(próximo)</span></span>
                </li>
            </ul>
        </nav>
    </div>

<div id="imageModal" class="image-modal">
  <span class="close">&times;</span>
  <img class="image-modal-content" id="modalImage">
</div>
</div>
    <script src="Assets/js/zoomimagen.js"></script>
    <script src="Assets/js/botonvolver.js"></script>
    <script src="Assets/js/animaciondetablas.js"></script>
    <script src="Assets/js/trancicion.js"></script>
    <script src="Assets/js/paginacion.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>