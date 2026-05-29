<?php
require_once(__DIR__ . "../../include/UH.php");
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
    <br>
    <div class="sticky-header">
    <div>
        <h1 class="inicio552">Solicitudes aceptadas</h1>
    </div>
    </div>

    <div class="btn-volver-container fade-slide">
        <button class="btn-volver" id="btnVolver">
            <i class="fa fa-arrow-left"></i> Volver
        </button>
    </div>

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
                <th>Estado</th>
                <th>Fecha de Creacion</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($resultados)): ?>
                <?php foreach ($resultados as $resultado): ?>
                    <tr class="list-item">
                        <td><?= htmlspecialchars($resultado['titulo']); ?></td>
                        <td>
                            <img src="<?= htmlspecialchars($resultado['imagen'] ?? 'Assets/imagenes/perfil/fotodefault.webp'); ?>"
                                alt="Imagen del producto" class="zoom-img" /><br>
                            <?= htmlspecialchars($resultado['producto_nombre'] ?? $resultado['nombre'] ?? 'Sin nombre'); ?>
                        </td>
                        <td><?= htmlspecialchars($resultado['prioridad']); ?></td>
                        <td><?= htmlspecialchars($resultado['descripcion']); ?></td>
                        <?php if ($_SESSION['rol'] == ROL_CLIENTE): ?>
                            <td>
                                <a title="<?=$resultado['nombre_tecnico']?>" href="Index.php?accion=PerfilTecnico&id=<?= $resultado['id_tecnico'] ?>" class="btn btn-perfil-tecnico">
                                    <i class="fa fa-user"></i> <?= htmlspecialchars($resultado['nombre_tecnico'] ?? 'No asignado'); ?>
                                </a>
                            </td>
                        <?php elseif ($_SESSION['rol'] == ROL_TECNICO): ?>
                            <td><?= htmlspecialchars($resultado['nombre_cliente']); ?></td>
                        <?php endif; ?>
                        <td data-translate="<?= htmlspecialchars($resultado['estado_nombre']); ?>"><?= htmlspecialchars($resultado['estado_nombre']); ?></td>
                        <td><?= htmlspecialchars(date('d/m/Y H:i:s ', strtotime($resultado['fecha_creacion']))); ?></td>
                        <td>
                            <div class="btn-group-actions d-flex">
                                <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] == 1): ?>
                                    <a href="Index.php?accion=editarSF&id=<?= $resultado['id']; ?>" class="icon-btn edit">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                <?php endif; ?>

                                <?php
                                $usuarioDestino = 0;
                                if (isset($_SESSION['rol'])) {
                                    if ($_SESSION['rol'] == ROL_TECNICO) {
                                        $usuarioDestino = $resultado['cliente_id'];
                                    } elseif ($_SESSION['rol'] == ROL_CLIENTE || $_SESSION['rol'] == 1) { // Asumiendo rol 1 es Admin
                                        $usuarioDestino = $resultado['tecnico_id'];
                                    }
                                }
                                ?>

                                <?php if ($usuarioDestino): ?>
                                    <a href="Index.php?accion=mostrarChat&usuario_id=<?= $usuarioDestino ?>&solicitud_id=<?= $resultado['id'] ?>"
                                        class="icon-btn chat">
                                        <i class="fa fa-comments"></i>
                                    </a>
                                <?php endif; ?>
    
                                <a href="Index.php?accion=solicitud_historia&id_solicitud=<?= $resultado['id']; ?>" class="icon-btn historial">
                                    <i class="fa fa-file-alt"></i>
                                </a>

                                <a href="Index.php?accion=cancelarS&id_solicitud=<?= $resultado['id']; ?>"
                                    onclick="return confirm('¿Estás seguro de que quieres cancelar esta solicitud?');"
                                    class="icon-btn delete">
                                    <i class="fa fa-times"></i>
                                </a>
                            </div>
                        </td>

                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="<?= (isset($_SESSION['rol']) && ($_SESSION['rol'] == ROL_CLIENTE || $_SESSION['rol'] == ROL_TECNICO)) ? 9 : 8; ?>">
                        No hay solicitudes aceptadas
                        <div style="display:flex; justify-content:center; margin-top:15px;">
                            <?php if ($_SESSION['rol'] == ROL_TECNICO): ?>
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
            <?php endif; ?>
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
