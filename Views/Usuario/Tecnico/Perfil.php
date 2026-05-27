<?php require_once(__DIR__ . "../../../include/UH.php"); ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$DatosTecnico['nombre']?></title>
</head>
<body>
<link rel="stylesheet" href="Assets/css/main.css">

<div class="btn-volver-container fade-slide">
    <button class="btn-volver" id="btnVolver">
    <i class="fa fa-arrow-left"></i> Volver
</button>
</div>
<br>
<div class="profile-info fade-slide">
    <img src="<?=htmlspecialchars($DatosTecnico['foto_perfil'])?>" alt="Foto de perfil"/>
    <div class="profile-details">
        <p><?=$DatosTecnico['nombre']?><br> <?= $DatosTecnico['email']?></p>
        <p>Cantidad de Reviews: <?=$DatosTecnico['cant_review']?> Promedio: <?=$DatosTecnico['promedio']?>⭐</p>
        <p>Especialidad: 
            <?php if ($especializaciones):
                echo implode(", ", $especializaciones);
                if ($DatosTecnico['otra_especialidad']): ?>
                Y <?=$DatosTecnico['otra_especialidad']?>
                <?php endif ?>
            <?php else: ?>
            <?php if ($DatosTecnico['otra_especialidad']): ?>
                <?=$DatosTecnico['otra_especialidad']?>
            <?php endif; endif ?>
        </p>
    </div>
</div>
<br>
<?php
$i = 0; 
foreach ($ReviewsTecnico as $review): 
    $i++;
?>
<div class="reviews list-item">
    <?php
    echo $review['cliente']. "<br>"; 
    ?>
    <fieldset class="rate ratings-list" id="static-rating-<?= $i ?>">
        <input disabled type="radio" id="rating10-<?= $i ?>" name="rating-<?= $i ?>" value="10" <?= ($review['rating']*2 == 10) ? 'checked' : '' ?> /><label for="rating10-<?= $i ?>" title="5 stars"></label>
        <input disabled type="radio" id="rating9-<?= $i ?>" name="rating-<?= $i ?>" value="9" <?= ($review['rating']*2 == 9) ? 'checked' : '' ?> /><label class="half" for="rating9-<?= $i ?>" title="4 1/2 stars"></label>
        <input disabled type="radio" id="rating8-<?= $i ?>" name="rating-<?= $i ?>" value="8" <?= ($review['rating']*2 == 8) ? 'checked' : '' ?> /><label for="rating8-<?= $i ?>" title="4 stars"></label>
        <input disabled type="radio" id="rating7-<?= $i ?>" name="rating-<?= $i ?>" value="7" <?= ($review['rating']*2 == 7) ? 'checked' : '' ?> /><label class="half" for="rating7-<?= $i ?>" title="3 1/2 stars"></label>
        <input disabled type="radio" id="rating6-<?= $i ?>" name="rating-<?= $i ?>" value="6" <?= ($review['rating']*2 == 6) ? 'checked' : '' ?> /><label for="rating6-<?= $i ?>" title="3 stars"></label>
        <input disabled type="radio" id="rating5-<?= $i ?>" name="rating-<?= $i ?>" value="5" <?= ($review['rating']*2 == 5) ? 'checked' : '' ?> /><label class="half" for="rating5-<?= $i ?>" title="2 1/2 stars"></label>
        <input disabled type="radio" id="rating4-<?= $i ?>" name="rating-<?= $i ?>" value="4" <?= ($review['rating']*2 == 4) ? 'checked' : '' ?> /><label for="rating4-<?= $i ?>" title="2 stars"></label>
        <input disabled type="radio" id="rating3-<?= $i ?>" name="rating-<?= $i ?>" value="3" <?= ($review['rating']*2 == 3) ? 'checked' : '' ?> /><label class="half" for="rating3-<?= $i ?>" title="1 1/2 stars"></label>
        <input disabled type="radio" id="rating2-<?= $i ?>" name="rating-<?= $i ?>" value="2" <?= ($review['rating']*2 == 2) ? 'checked' : '' ?> /><label for="rating2-<?= $i ?>" title="1 star"></label>
        <input disabled type="radio" id="rating1-<?= $i ?>" name="rating-<?= $i ?>" value="1" <?= ($review['rating']*2 == 1) ? 'checked' : '' ?> /><label class="half" for="rating1-<?= $i ?>" title="1/2 star"></label>
    </fieldset>

    <?php
    echo '<div class="comentario">'.htmlspecialchars($review['comentario']).'</div>';
    echo '<div class="fecha-review">';
    echo '<span class="fecha-creacion">📅 '.date('d/m/Y', strtotime($review['fecha_creacion'])).'</span>';
    if ($review['fecha_edicion']) {
        echo '<span class="fecha-edicion"> (Editado: '.date('d/m/Y', strtotime($review['fecha_edicion'])).')</span>';
    }
    echo '</div>';
    ?>
</div>
<?php endforeach; ?>

<div class='pagination-container'>
    <nav>
        <ul class="pagination">
            <li data-page="prev">
                <span> &lt; <span class="sr-only">(anterior)</span></span>
            </li>
            <li data-page="next" id="prev">
                <span> &gt; <span class="sr-only">(próximo)</span></span>
            </li>
        </ul>
    </nav>
</div>

<script src="Assets/js/transicion.js"></script>
<script src="Assets/js/botonvolver.js"></script>
<script src="Assets/js/paginacion.js"></script>
</body>
</html>