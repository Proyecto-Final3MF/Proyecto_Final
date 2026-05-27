<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Chats</title>
</head>

<body>
    <div class="chat-admin-container">
    <h1>Historial de chats</h1>

    <table border="1" cellpadding="5">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Usuario</th>
                <th>Receptor</th>
                <th>Mensaje</th>
            </tr>
        </thead>

        <tbody>
            <?php if (!empty($mensajes)): ?>
                <?php foreach ($mensajes as $m): ?>
                    <tr>
                        <td><?= htmlspecialchars($m['fecha'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($m['usuario'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($m['receptor'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= nl2br(htmlspecialchars($m['mensaje'] ?? '', ENT_QUOTES, 'UTF-8')) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No hay mensajes para mostrar</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
    <script src="Assets/js/transicion.js"></script>
</body>
</html>