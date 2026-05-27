<?php
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != ROL_ADMIN) {
    header("Location: Index.php?accion=redireccion");
    exit();
}

require_once(__DIR__ . "../../../include/UH.php");

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="60">
    <title>Histórial de actividades</title>
    <link rel="stylesheet" href="Assets/css/Main.css">
</head>
<div class="btn-volver-container fade-slide">
  <button class="btn-volver" id="btnVolver">
    <i class="fa fa-arrow-left"></i> Volver
  </button>
</div>
<br>
    <div class="container3">
        <h1 class="inicio55">Histórial de actividades</h1>

        <p>En esta pagina encontraras todas las modificaciones hechas en la base de datos. <br> Por favor al elegir un rango de fechas no elijas un rango muy grande para no sobrecargar el servidor, es recomendado especificar lo maximo possible tu busqueda. Por default la fecha final es el dia actual.</p>

        <form action="Index.php" method="GET" class="filter-form">
            <input type="hidden" name="accion" value="mostrarHistorial">
            <div class="form-group">
                <label for="search">Buscar:</label>
                <input type="text" id="search" name="search" placeholder="Buscar por usuario, acción, item..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="start_date">Fecha Inicial:</label>
                <input required type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($_GET['start_date'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label required for="end_date">Fecha Final:</label>
                <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($_GET['end_date'] ?? ''); ?>">
            </div>

            <div class="button-container5">
                <button type="submit">Aplicar Filtros</button>
                <a href="Index.php?accion=mostrarHistorial" class="clear-button">Limpiar Filtros</a>
            </div>
    
        </form>

        <?php if (!empty($historial) && (empty($search) && empty($start_date) && empty($end_date))): ?>
            <p>Por favor, aplique un filtro para ver los registros.</p>
        <?php else: ?>
            <?php if (!empty($historial)): ?>
                <?php foreach ($historial as $registro): ?>
                    <div class="list-item">
                        <p>
                            <strong>
                                <?php echo htmlspecialchars($registro->usuario ? $registro->usuario : '[Sistema]'); ?>
                            </strong>
                            #<?php echo htmlspecialchars($registro->usuario_id ? $registro->usuario_id : 'N/A'); ?>
                            <?php echo htmlspecialchars(ucfirst($registro->accion)); ?>
                            <strong><?php echo htmlspecialchars(ucfirst($registro->item)); ?></strong>
                            <?php
                                if ($registro->item) {
                                    echo "#".  htmlspecialchars($registro->item_id);
                                }
                            ?>
                            a las <span class="fecha-hora"><?php echo date('H:i:s d/m/Y', strtotime($registro->fecha_hora)); ?>.</span>
                        </p>
                        <?php if (!empty($registro->obs)): ?>
                            <p><strong>Observación:</strong> <?php echo htmlspecialchars($registro->obs); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-records">No se encontró ningún registro que coincida con los filtros.</p>
            <?php endif; ?>
        <?php endif; ?>
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
    <br>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="Assets/js/paginacion.js"></script>
    <script src="Assets/js/transicion.js"></script>
    <script src="Assets/js/botonvolver.js"></script>
</body>
</html>