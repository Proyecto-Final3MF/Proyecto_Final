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
    <title>Chat</title>
</head>

<div class="btn-volver-container fade-slide">

    <button class="btn-volver" id="btnVolver">
        <i class="fa fa-arrow-left"></i> Volver
    </button>

</div>

<body>
    <br>
    <?php
    // Obtener datos de la solicitud y el interlocutor
    $solicitudNombre = 'Sin solicitud';
    $interlocutorNombre = 'Usuario desconocido';
    $rolLabel = '';
    if (isset($solicitud) && $solicitud) {
        $solicitudNombre = htmlspecialchars($solicitud['titulo']);
    }
    // Obtener nombre del interlocutor
    require_once(__DIR__ . "/../Models/UsuarioM.php");
    $usuarioModel = new Usuario();
    $interlocutor = $usuarioModel->buscarUsuarioId($otroUsuarioId);
    if ($interlocutor) {
        $interlocutorNombre = htmlspecialchars($interlocutor['nombre']);
    }
    // Determinar etiqueta según rol
    if ($_SESSION['rol'] == 1) { // Técnico
        $rolLabel = 'Cliente:';
    } elseif ($_SESSION['rol'] == 2) { // Cliente
        $rolLabel = 'Técnico:';
    }
    ?>
    <div class="chat-header" style="text-align: center; margin: 20px 0; color: var(--text-color);">
        <h1>Chat de la solicitud: <?php echo $solicitudNombre; ?></h1>
        <h2><?php echo $rolLabel; ?> <?php echo $interlocutorNombre; ?></h2>

        <div class="chat-container">
            <div class="chat-box" id="chat-box"></div>
        </div>

        <form id="form-chat" class="chat-input" method="POST" action="Index.php?accion=enviarMensaje">
            <input type="hidden" name="usuario_id" value="<?= $_SESSION['id'] ?>">
            <input type="hidden" name="receptor_id" value="<?= $otroUsuarioId ?>">
            <?php if (isset($solicitudId) && $solicitudId): ?>
                <input type="hidden" name="solicitud_id" value="<?= $solicitudId ?>">
            <?php endif; ?>

            <input type="text" autocomplete="off" name="mensaje" placeholder="Escribe tu mensaje..." required>
            <button type="submit"><i class="fa fa-upload"></i></button>
        </form>
</body>

</html>
<script src="Assets/js/botonvolver.js"></script>
<script src="Assets/js/trancicion.js"></script>
<script>
    // Cargar mensajes
    async function cargarMensajes() {
        let res = await fetch("Index.php?accion=cargarMensajes&usuario_id=<?= $otroUsuarioId ?><?= isset($solicitudId) && $solicitudId ? '&solicitud_id=' . $solicitudId : '' ?>");
        let html = await res.text();
        document.getElementById("chat-box").innerHTML = html;
    }

    // Enviar mensaje
    document.getElementById("form-chat").addEventListener("submit", async function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        await fetch("Index.php?accion=enviarMensaje", {
            method: "POST",
            body: formData
        });
        this.reset();
        cargarMensajes();
    });

    setInterval(cargarMensajes, 3000);
    cargarMensajes();
</script>