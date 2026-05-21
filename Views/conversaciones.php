<?php
require_once(__DIR__ . "/../Views/include/UH.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conversaciones</title>
</head>

<body>
    <br>
    <div class="conversaciones-container">
        <h2>Mis conversaciones</h2>
        <div class="btn-volver-container fade-slide">
            <button class="btn-volver" id="btnVolver">
                <i class="fa fa-arrow-left"></i> Volver
            </button>
        </div>
        <?php if (!empty($conversaciones)): ?>
            <ul>
                <?php foreach ($conversaciones as $c): ?>
                    <li>
                        <strong><?= htmlspecialchars($c['otro_usuario']) ?></strong>
                        <?php if (!empty($c['solicitud_titulo'])): ?>
                            <span class="solicitud-badge">Solicitud: <?= htmlspecialchars($c['solicitud_titulo']) ?></span>
                        <?php endif; ?>
                        <br>
                        <em><?= htmlspecialchars($c['ultimo_mensaje']) ?></em><br>
                        <small><?= $c['ultima_fecha'] ?></small><br>
                        <a class="miraresaconve" href="Index.php?accion=mostrarChat&usuario_id=<?= $c['otro_usuario_id'] ?><?= !empty($c['solicitud_id']) ? '&solicitud_id=' . $c['solicitud_id'] : '' ?>">
                            Ver conversacion
                        </a>

                        <form method="POST" action="Index.php?accion=borrarConversacion" style="display:inline" onsubmit="return confirm('¿Seguro que deseas borrar esta conversación?');">
                            <input type="hidden" name="usuario_id" value="<?= $_SESSION['id'] ?>">
                            <input type="hidden" name="receptor_id" value="<?= $c['otro_usuario_id'] ?>">
                            <?php if (!empty($c['solicitud_id'])): ?>
                                <input type="hidden" name="solicitud_id" value="<?= $c['solicitud_id'] ?>">
                            <?php endif; ?>
                            <button type="submit">Borrar</button>
                        </form>
                    </li>
                    <hr>
                <?php endforeach; ?>
            </ul>

        <?php else: ?>
            <p>No tienes conversaciones aun.</p>
        <?php endif; ?>
    </div>
    <script src="Assets/js/botonvolver.js"></script>
    <script src="Assets/js/trancicion.js"></script>
</body>
</html>