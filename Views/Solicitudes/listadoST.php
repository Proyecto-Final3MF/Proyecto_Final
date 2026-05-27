<?php
require_once(__DIR__ . "../../include/UH.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sus Solicitudes</title>
    <link rel="stylesheet" href="./Assets/css/Main.css"/>
</head>
<body>
    <div class="content-wrapper"> 
    <br>
    <div class="sticky-header">
    <div>
        <h1 class="inicio552">Solicitudes Terminadas</h1>
    </div>
    </div>

    <?php if ($_SESSION['rol'] == ROL_TECNICO && !empty($resultados)): ?>
        <?php
        $totalGanancias = 0.0;
        foreach ($resultados as $resultado) {
            $totalGanancias += (float) ($resultado['precio'] ?? 0.0);
        }
        ?>
        <div class="contador-ganancias">
            <h2>Ganancias Totales</h2>
            <p>$<?= number_format($totalGanancias, 2, ',', '.'); ?></p>
            <small class="champions">Suma de precios de todas tus solicitudes finalizadas.</small>
        </div>
    <?php endif; ?>

    <div class="btn-volver-container fade-slide">
        <button class="btn-volver" id="btnVolver">
            <i class="fa fa-arrow-left"></i> Volver
        </button>
    </div>

    <form action="Index.php" method="GET" class="filter-form2 fade-slide">
        <input type="hidden" name="accion" value="listarST">
        <div class="form-group search-wrapper fade-slide">
            <label for="search">Buscar: </label>
            <input type="text" id="search" autocomplete="off" name="search" placeholder="Buscar..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
        </div>
    </form>
    
    <table>
        <thead>
            <tr>
                <th>Titulo</th>
                <th>Producto</th>
                <th>Prioridad</th>
                <th>Descripcion</th>
                <?php if ($_SESSION['rol'] == ROL_CLIENTE): ?>
                    <th>Técnico</th>
                <?php elseif ($_SESSION['rol'] == ROL_TECNICO): ?>
                    <th>Cliente</th>
                <?php endif; ?>
                <th>Precio</th>
                <th>Estado</th>
                <th>Calificación</th>
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
                        <img src="<?= htmlspecialchars($resultado['imagen'] ?? 'Assets/imagenes/perfil/fotodefault.webp'); ?>" 
                        alt="Imagen del producto" class="zoom-img"/><br>
                        <?= htmlspecialchars($resultado['producto_nombre'] ?? $resultado['nombre'] ?? 'Sin nombre') ?>
                    </td>
                    <td><?= htmlspecialchars($resultado['prioridad']); ?></td>
                    <td><?= htmlspecialchars($resultado['descripcion']); ?></td>
                    <?php if ($_SESSION['rol'] == ROL_CLIENTE): ?>
                            <td>
                            <a title="Perfil de <?=$resultado['nombre_tecnico']?>" href="Index.php?accion=PerfilTecnico&id=<?= $resultado['id_tecnico'] ?>" class="btn btn-perfil-tecnico">
                            <i class="fa fa-user"></i> <?= htmlspecialchars($resultado['nombre_tecnico'] ?? 'No asignado'); ?>
                            </a>
                            </td>
                        <?php elseif ($_SESSION['rol'] == ROL_TECNICO): ?>
                            <td><?= htmlspecialchars($resultado['nombre_cliente']); ?></td>
                        <?php endif; ?>
                    <td>$<?= htmlspecialchars($resultado['precio']); ?></td>
                    <td><?= htmlspecialchars($resultado['estado_nombre']); ?></td>
                    <?php if ($resultado['rating']): ?>
                        <td><?= htmlspecialchars($resultado['rating']."⭐"); ?></td>
                    <?php else: ?>
                        <td><?= htmlspecialchars("Sin calificaciones"); ?></td>
                    <?php endif ?>
                    <td><?= htmlspecialchars(date('d/m/Y H:i:s ', strtotime($resultado['fecha_creacion']))); ?></td>
                
                <td>
                <div class="btn-group-actions">
                <a href="Index.php?accion=solicitud_historia&id_solicitud=<?= $resultado['id']; ?>" class="icon-btn historial">
                <i class="fa fa-file-alt"></i>
                </a>
                <?php if ($_SESSION['rol'] == ROL_CLIENTE): ?>
                <a href="Index.php?accion=FormularioReview&id_solicitud=<?= $resultado['id']; ?>" class="icon-btn review">
                <i class="fa-solid fa-star"></i>
                <?php endif; ?>
                </a>
                </div>
                </td>
                </tr>
                <?php
            }
        } else {
            ?>
            <tr>
                <td colspan="10">
                    No se terminaron solicitudes
                    <div style="display:flex; justify-content:center; margin-top:15px;">
                        <?php if ($_SESSION['rol'] == 1): ?>
                            <a href="Index.php?accion=listarTL">
                                <button class="btn btn-boton777">Ver solicitudes disponibles</button>
                            </a>
                            <?php else: ?>
                                <a href="Index.php?accion=listarSLU">
                                <button class="btn btn-boton777">Ver tus solicitudes no asignadas</button>
                            </a>
                            <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>

    

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