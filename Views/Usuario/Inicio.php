<link rel="icon" type="image/png" href="Assets/imagenes/logonueva.png">

<?php
require_once(__DIR__ . "../../include/UH.php");

require_once(__DIR__ . "/../../Models/ReviewM.php");
$reviewModel = new Review();
$topTecnicos = $reviewModel->obtenerTopTecnicos();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>C O S M O S</title>
</head>
<body>
    
<br><br>

<h1 data-translate="welcome" class="inicio55">Bienvenido a</h1>

<h1 class="inicio55">C<img src="Assets/imagenes/logonueva.png" class="logoeninicio" height="50px" alt="logo de la app">SMOS</h1>

<?php if (!isset($_SESSION['usuario'])): ?>
    <p class="inicio44">Te ayudamos a encontrar un técnico para arreglar tu dispositivo en tiempo récord</p>

    <div class="btn-container2 fade-slide">
        <a href="Index.php?accion=login">
            <button class="btn btn-boton44">Empezar Ahora</button>
        </a>
    </div>

    <br>

<?php else: ?>
    <p class="inicio44">Nos alegra verte de nuevo, <?= htmlspecialchars($_SESSION['usuario']) ?>.</p>

    <div class="btn-container2 fade-slide">
        <a href="Index.php?accion=redireccion">
            <button class="btn btn-boton44">Ver mi Unidad</button>
        </a>
    </div>
<?php endif; ?>

<section class="top-tecnicos fade-slide">
    <h2 class="inicio55"> Top 3 Técnicos Mejor Calificados</h2>
    <br>
    <?php if (!empty($topTecnicos)): ?>
        <div class="top-tecnicos-container">
            <?php foreach ($topTecnicos as $tecnico): ?>
                <div class="card-tecnico">
                    <img src="<?= htmlspecialchars($tecnico['foto_perfil']) ?>" alt="Foto de <?= htmlspecialchars($tecnico['nombre']) ?>" class="foto-tecnico">
                    <h2><?= htmlspecialchars($tecnico['nombre']) ?></h2>
                    <p class="puntuacion">⭐ <?= number_format($tecnico['promedio'], 1) ?> / 5</p>
                    <p class="reviews">(<?= $tecnico['cant_review'] ?> reseñas)</p>

                    <a title="Perfil de <?= $tecnico['nombre'] ?>" href="Index.php?accion=PerfilTecnico&id=<?= $tecnico['id'] ?>" class="btn-ver-perfil">Ver Perfil</a>

                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="inicio44 fade-slide">Aún no hay técnicos calificados.</p>
    <?php endif; ?>
</section>

<h2 class="inicio44 fade-slide">¿Estás interesado en trabajar como técnico con nosotros?</h2>

<div class="btn-container2 fade-slide">
    <a href="Index.php?accion=trabajo">
        <button class="btn btn-boton44 fade-slide">Trabajar con Nosotros</button>
    </a>
</div>
<br><br><br>

<h2 class="inicio44 fade-slide">¿Necesitas Ayuda Para Moverte por la App?</h2>

<div class="btn-container2 fade-slide">
    <a href="https://github.com/Proyecto-Final3MF/COSMOS/blob/main/manualdeusuario.md" target="_blank">
        <button class="btn btn-boton44 fade-slide">Manual de Usuario</button>
    </a>
    <a href="https://github.com/Proyecto-Final3MF/COSMOS/blob/main/READMEIN.md" target="_blank">
        <button class="btn btn-boton44 fade-slide">User Manual</button>
    </a>
</div>
<br><br><br><br>

<script src="Assets/js/transicion.js"></script>

<script>
    window.addEventListener("load", () => {
        document.body.classList.add("loaded");
    });
</script>

</body>
</html>