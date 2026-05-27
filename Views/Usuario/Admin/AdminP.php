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
    <meta http-equiv="refresh" content="30">
    <title>Panel de Admin</title>
    <link rel="stylesheet" href="./Assets/css/Main.css">
</head>
<body>
    <br><br>
    <main>
        <h1 class="inicio55">Aquí podrás gestionar tus tareas como Admin.</h1>
    </main>

    <div class="admin-dashboard">

        <section class="admin-panel-column fade-slide">
            <h3><a href="Index.php?accion=listarU" class="titulo3 fade-slide" >Ultimos Usuarios registrados</a></h3>
            <?php if (empty($usuarios)): ?>
                <div class="alert alert-info">
                    No hay usuarios registrados.
                    <a href="Index.php?accion=crear" class="btn btn-boton777">Crear el primero</a>
                </div>
            <?php else: ?>
                <div class="table-responsive-panel">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $u): ?>
                                <tr class="list-item">
                                    <td><?= $u['id'] ?></td>
                                    <td><?= htmlspecialchars($u['nombre']) ?></td>
                                    <td><?= htmlspecialchars($u['email']) ?></td>
                                    <td><?= htmlspecialchars($u['rol']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>

        <section class="admin-panel-column fade-slide">
            <h3><a href="Index.php?accion=mostrarHistorial" class="titulo3 fade-slide" >Ultimos registros en el Historial</a></h3>
            <div class="historial-panel">
                <?php if (!empty($historial)): ?>
                    <?php foreach ($historial as $registro): ?>
                        <div class="list-item">
                            <p>
                                <strong> <?php echo htmlspecialchars($registro['usuario'] ? $registro['usuario'] : '[Sistema]'); ?> </strong> #<?php echo htmlspecialchars($registro['usuario_id'] ? $registro['usuario_id'] : 'N/A'); ?>
                                <?php echo htmlspecialchars(ucfirst($registro['accion'])); ?> <strong><?php echo htmlspecialchars(ucfirst($registro['item'])); ?></strong>
                                <?php if ($registro['item']) { echo "#". htmlspecialchars($registro['item_id']); } ?> a las <span class="fecha-hora"><?php echo date('H:i:s d/m/Y', strtotime($registro['fecha_hora'])); ?>.</span>
                            </p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-records">No hay registro en el historial.</p>
                <?php endif; ?>
            </div>
        </section>

    </div>
    <script src="Assets/js/transicion.js"></script>
</body>
</html>