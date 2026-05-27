<?php require_once(__DIR__ . "../../include/UH.php"); ?>
<link rel="stylesheet" href="./Assets/css/Main.css">

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calificar al Tecnico</title>
</head>
<body>
    
<div class="btn-volver-container fade-slide">
    <button class="btn-volver" id="btnVolver">
    <i class="fa fa-arrow-left"></i> Volver
</button>
</div>

<div class="contenedor-formulario">
    <section class="formularios99">    
        <h2>Review de la Solicitud <?=$titulo_solicitud?></h2>
        <form action="Index.php?accion=AddReview" method="post">
            <input type="hidden" name="id_tecnico" value="<?=$id_tecnico ?>">
            <input type="hidden" name="titulo_solicitud" value="<?=$titulo_solicitud ?>">
            <input type="hidden" name="id_solicitud" value="<?=$id ?>">
            <fieldset class="rate" id="interactive-rating">
                <input type="radio" id="rating10" name="rating" value="10" <?= ($rating == 10) ? 'checked' : '' ?> /><label for="rating10" title="5 stars"></label>
                <input type="radio" id="rating9" name="rating" value="9" <?= ($rating == 9) ? 'checked' : '' ?> /><label class="half" for="rating9" title="4 1/2 stars"></label>
                <input type="radio" id="rating8" name="rating" value="8" <?= ($rating == 8) ? 'checked' : '' ?> /><label for="rating8" title="4 stars"></label>
                <input type="radio" id="rating7" name="rating" value="7" <?= ($rating == 7) ? 'checked' : '' ?> /><label class="half" for="rating7" title="3 1/2 stars"></label>
                <input type="radio" id="rating6" name="rating" value="6" <?= ($rating == 6) ? 'checked' : '' ?> /><label for="rating6" title="3 stars"></label>
                <input type="radio" id="rating5" name="rating" value="5" <?= ($rating == 5) ? 'checked' : '' ?> /><label class="half" for="rating5" title="2 1/2 stars"></label>
                <input type="radio" id="rating4" name="rating" value="4" <?= ($rating == 4) ? 'checked' : '' ?> /><label for="rating4" title="2 stars"></label>
                <input type="radio" id="rating3" name="rating" value="3" <?= ($rating == 3) ? 'checked' : '' ?> /><label class="half" for="rating3" title="1 1/2 stars"></label>
                <input type="radio" id="rating2" name="rating" value="2" <?= ($rating == 2) ? 'checked' : '' ?> /><label for="rating2" title="1 star"></label>
                <input type="radio" id="rating1" name="rating" value="1" <?= ($rating == 1) ? 'checked' : '' ?> /><label class="half" for="rating1" title="1/2 star"></label>
            </fieldset>
            <textarea class="form-control" rows="3" name="Comentario" placeholder="Describi tu experiencia." id="Comentario"><?= htmlspecialchars($Comentario) ?? ''?></textarea>
            <button type="submit">Enviar</button>
        </form>
    </section>
</div>
<script src="Assets/js/transicion.js"></script>
<script src="Assets/js/botonvolver.js"></script>
<script src="Assets/js/botonvolver.js"></script>

</body>
</html>