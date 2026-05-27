<?php
if (!isset($_SESSION['usuario'])) {
    header("Location: Index.php?accion=login");
    exit();
}
require_once(__DIR__ . "../../include/UH.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Eliminación de Usuario</title>
    <link rel="stylesheet" href="./Assets/css/Main.css">
</head>
<body>
    <br>
    <div class="confirmar-eliminacion-container">  
        <h1 class="inicio55">Confirmar Eliminación de Cuenta</h1>

        <div class="btn-volver-container fade-slide">
            <button class="btn-volver" id="btnVolver">
                <i class="fa fa-arrow-left"></i> Volver
            </button>
        </div>

        <div class="alert alert-warning fade-slide">
    <h2>¿Estás seguro de que quieres eliminar esta cuenta?</h2>
    
    <div class="usuario-info">
        <p class="usuario-label"><strong>Usuario:</strong></p>
        <img src="<?= htmlspecialchars($usuario['foto_perfil'] ?? 'Assets/imagenes/perfil/fotodefault.webp') ?>" alt="Foto de perfil" class="foto-perfil-confirmacion">
        <p class="usuario-detalles"><?= htmlspecialchars($usuario['nombre']) ?> (<?= htmlspecialchars($usuario['email']) ?>)</p>
    </div>
    
    <?php if (!$dependencias['puede_eliminar']): ?>
        <p class="text-danger"><i class="fa fa-times-circle"></i> <strong>Advertencia:</strong> <?= $dependencias['mensaje'] ?></p>
        <p>No se puede eliminar hasta resolver las dependencias.</p>
    <?php else: ?>
        <p><i class="fa fa-info-circle"></i> Esta acción es irreversible y eliminará todos los datos asociados (mensajes, notificaciones, etc.).</p>
    <?php endif; ?>
</div>

        <?php if ($dependencias['puede_eliminar']): ?>
            <form action="Index.php?accion=eliminarU&id=<?= $usuario['id'] ?>" method="POST" class="form-eliminar">
                <button type="submit" class="btn btn-danger btn-eliminar" onclick="return confirm('¿Confirmas la eliminación?')">
                    <i class="fa fa-trash"></i> Eliminar Cuenta
                </button>
            </form>
        <?php endif; ?>
    </div>

    <script src="Assets/js/botonvolver.js"></script>
    <script src="Assets/js/transicion.js"></script>
</body>
</html>