<?php
require_once(__DIR__ . "/../../include/UH.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Solicitudes Libres</title>
</head>
<body>
<div class="content-wrapper"> 
<br>
  <div class="sticky-header">
    <div>
      <h1 class="inicio55">Solicitudes no asignadas</h1>
    </div>
  </div>

    <div class="btn-volver-container fade-slide">
      <button class="btn-volver" id="btnVolver">
        <i class="fa fa-arrow-left"></i> Volver
      </button>
    </div>

  <form action="Index.php" method="GET" class="filter-form2 fade-slide">
    <input type="hidden" name="accion" value="listarTL">
      <div class="form-group search-wrapper fade-slide">
        <label for="search">Buscar: </label>
        <input type="text" id="search" autocomplete="off" name="search" placeholder="Buscar por titulo, producto o descripción" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
      </div>
  </form>

  <table>
    <thead>
      <tr>
        <th>Titulo</th>
        <th>Cliente</th>
        <th>Producto</th>
        <th>Prioridad</th>
        <th>Descripcion</th>
        <th>Fecha de Creacion</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
  <?php
  $solicitudC = new SolicitudC();
  $resultados = $solicitudC->ListarTL();

  if (!empty($resultados)) {
    foreach ($resultados as $resultado) {
      ?>
      <tr class="list-item">
        <td><?= htmlspecialchars($resultado['titulo']); ?></td>
        <td><?= htmlspecialchars($resultado['cliente_nombre']); ?></td>
        <td>
          <img src="<?= htmlspecialchars($resultado['imagen']);?>" alt="Imagen del producto" class="zoom-img" /><br>
          <?= htmlspecialchars($resultado['producto']) ?>
        </td>
        <td><?= htmlspecialchars($resultado['prioridad']); ?></td>
        <td><?= htmlspecialchars($resultado['descripcion']); ?></td>
        <td><?= htmlspecialchars(date('d/m/Y H:i:s ', strtotime($resultado['fecha_creacion']))); ?></td>
        <td>
          <div class="btn-group-actions">
            <a href="Index.php?accion=asignarS&id_solicitud=<?php echo $resultado['id'];?>" class="icon-btn aceptar" >  
              <i class="fa fa-check"></i>
            </a>
            <a href="Index.php?accion=solicitud_historia&id_solicitud=<?= $resultado['id']; ?>" class="icon-btn historial">
              <i class="fa fa-file-alt"></i>
            </a>
          </div>
        </td>
      </tr>
      <?php
    }
  } else {
    ?>
    <tr>
      <td colspan="7">No hay solicitudes disponibles en este momento<br><br><a href="Index.php?accion=listarSA"><button class="btn btn-boton777">Ver solicitudes aceptadas</button></a></td>
    </tr>
    <?php
  }
  ?>
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
</div>
<script src="Assets/js/zoomimagen.js"></script>
<script src="Assets/js/botonvolver.js"></script>
<script src="Assets/js/animaciondetablas.js"></script>
<script src="Assets/js/transicion.js"></script>
<script src="Assets/js/paginacion.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</body>
</html>