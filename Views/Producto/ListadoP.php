<?php
require_once(__DIR__ . "/../include/UH.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de mis dispositivos</title>
    <link rel="stylesheet" href="./Assets/css/Main.css" />
</head>
<body>
    <br>
    <h1 class="inicio55">Tus Dispositivos</h1>
<div class="botones-container fade-slide">
    <a href="Index.php?accion=formularioP"><button class="btn btn-boton4442 btn-crear"> <i class="fa-solid fa-plus-circle"></i></button></a>
</div>

<div class="btn-volver-container fade-slide">
  <button class="btn-volver" id="btnVolver">
    <i class="fa fa-arrow-left"></i> Volver
  </button>
</div>

<form action="Index.php" class="ordenar-form">
    <label for="search">Buscar: </label>
    <input type="text" id="search" name="search" placeholder="Buscar Dispositivo" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
    <label for="orden">Ordenar por:</label>
    <input type="hidden" name="accion" value="listarP">
    <select name="orden" id="orden">
        <option value="Más Recientes" <?php echo ($_GET['orden'] ?? 'Más Antiguos') == 'Más Recientes' ? 'selected' : ''; ?>>Más Recientes</option>
        <option value="Más Antiguos" <?php echo ($_GET['orden'] ?? 'Más Antiguos') == 'Más Antiguos' ? 'selected' : ''; ?>>Más Antiguos</option>
        <option value="A-Z" <?php echo ($_GET['orden'] ?? '') == 'A-Z' ? 'selected' : ''; ?>>A-Z</option>
        <option value="Z-A" <?php echo ($_GET['orden'] ?? '') == 'Z-A' ? 'selected' : ''; ?>>Z-A</option>
    </select>
    <button type="submit" class="btn-boton">Buscar</button>
</form>

<table>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Imagen</th>
            <th>Categoría</th>
            <th>Modificaciones</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $productoModel = new Producto();

        if (!empty($resultados)) {
            foreach ($resultados as $p): ?>
            <tr class="list-item">
                <td><?= htmlspecialchars($p['nombre']) ?></td>
                <td>
                    <img src="<?= htmlspecialchars($p['imagen']) ?>" 
                         alt="Imagen de dispositivo" 
                         class="zoom-img" 
                         style="max-width:100px; max-height:100px;" />
                </td>
                <td><?= htmlspecialchars($productoModel->obtenerCategoriaporId($p['id_cat'])) ?></td>
                <td>
    <div class="btn-group-actions">                
    <a href="Index.php?accion=editarP&id=<?= $p['id'] ?>" class="icon-btn edit">
    <i class="fa fa-edit"></i>
    </a>
    <a href="Index.php?accion=borrarP&id=<?= $p['id'] ?>" 
       onclick="return confirm('¿Seguro que quieres borrar este dispositivo?');" 
       class="icon-btn delete">
        <i class="fa fa-trash"></i>
    </a>
</div>
</td>

            </tr>
            <?php endforeach; ?>
        <?php } else { ?>
            <tr>
                <td colspan="4">No tienes dispositivos creados todavía</td>
            </tr>
        <?php } ?>
    </tbody>
</table>

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
    <script src="Assets/js/botonvolver.js"></script>
    <script src="Assets/js/zoomimagen.js"></script>
    <script src="Assets/js/animaciondetablas.js"></script>
    <script src="Assets/js/transicion.js"></script>
    <script src="Assets/js/paginacion.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>