<?php if (!empty($mensajes)): ?>
    <?php foreach ($mensajes as $m): ?>
        <?php
        // Determinar si el mensaje es del usuario actual
        $esPropio = ($m['usuario_id'] == $_SESSION['id']);
        $clase = $esPropio ? 'mensaje-emisor' : 'mensaje-receptor';
        $nombre = $esPropio ? 'Tú' : htmlspecialchars($m['emisor'] ?? $m['receptor'] ?? '???');
        ?>
        <div class="mensaje <?= $clase ?>">
            <p class="texto">
                <strong><?= $nombre ?>:</strong>
                <?= htmlspecialchars($m['mensaje'] ?? '') ?>
            </p>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p class="sin-mensajes">No hay mensajes aún.</p>
<?php endif; ?>