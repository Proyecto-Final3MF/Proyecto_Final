<?php
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != ROL_TECNICO) {
    header("Location: Index.php?accion=redireccion");
    exit();
}
    require_once(__DIR__ . "../../../include/UH.php");

$notifC = new NotificacionC();
$contador_normales = count($notifC->listarNoLeidas('normal'));

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Técnico</title>
    <link rel="stylesheet" href="./Assets/css/Main.css"> </head>
<body>
    <br><br>

    <h1 class="inicio55">Aquí podrás gestionar tus tareas como técnico.</h1>


<div class="btn-container fade-slide">
    <a href="Index.php?accion=listarTL">
    <button class="btn btn-boton444">
        <i class="fa-solid fa-list btn-disponibles"></i> 
        <span class="title">Solicitudes<br>Disponibles</span>
        <span class="desc">Aqui Podra Encontrar Solicitudes en Espera de un Tecnico</span>
        <?php if ($contador_normales > 0): ?>
            <span class="contador" id="contador-normales"><?= $contador_normales ?></span>
        <?php endif; ?>
    </button>
</a>
    <a href="Index.php?accion=listarSA">
        <button class="btn btn-boton444">
            <i class="fa-solid fa-check-circle btn-aceptadas"></i> 
            <span class="title">Solicitudes<br>Aceptadas</span>
            <span class="desc">Vea Las Solicitudes que Acepto y Esta Trabajando en Ella</span>
        </button>
    </a>
    <a href="Index.php?accion=listarST">
        <button class="btn btn-boton444">
            <i class="fa-solid fa-flag-checkered btn-terminadas"></i> 
            <span class="title">Solicitudes<br>Terminadas</span>
            <span class="desc">Consulta sus Solicitudes Terminadas y la Califiacion del Cliente</span>
        </button>
    </a>
</div>
<script src="Assets/js/transicion.js"></script>
</body>
</html>