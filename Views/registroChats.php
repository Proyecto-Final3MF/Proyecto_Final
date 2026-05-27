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
    <title>Registro de Chats</title>
</head>

<body>
    <div class="registro-container">
    <h2>Registro de Conversaciones</h2>

    <table border="1" cellpadding="5">
        <tr>
            <th>Usuario 1</th>
            <th>Usuario 2</th>
            <th>Total de Mensajes</th>
            <th>Última Actividad</th>
            <th>Acción</th>
        </tr>
        <?php foreach ($conversaciones as $c): ?>
            <tr>
                <td><?= htmlspecialchars($c['u1']) ?></td>
                <td><?= htmlspecialchars($c['u2']) ?></td>
                <td><?= $c['total_mensajes'] ?></td>
                <td><?= $c['ultima_fecha'] ?></td>
                <td>
                    <a href="Index.php?accion=mostrarConversacion&usuario=<?= $c['u1'] ?>&otro=<?= $c['u2'] ?>">
                        Ver Conversación
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    </div>
    <script src="Assets/js/transicion.js"></script>
</body>
</html>