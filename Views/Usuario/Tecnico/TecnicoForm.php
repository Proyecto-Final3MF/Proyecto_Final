<?php
if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 3])) {
  header("Location: index.php?accion=redireccion");
}

require_once(__DIR__ . "../../../include/UH.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
</head>
<body>
    
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="contenedor-formulario">
  <section class="formularios99">

    <h3>Registrarse como Tecnico</h3>

    <form class="tecnico-fields" method="POST" action="Index.php?accion=guardarT" enctype="multipart/form-data">
        
        <p class="fade-label">Nombre</p>
        <input type="text" class="form-control" id="usuario" name="usuario" autocomplete="off" placeholder="sin caracteres especiales" required> <br><br>

        <p class="fade-label">Email</p>
        <label for="mail" class="form-label"></label>
        <input type="email" pattern="^[\p{L}0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" class="form-control" id="mail" name="mail" autocomplete="off" required> <br><br>

        <p class="fade-label">Especializaciones (Seleccione por lo menos 1)</p>
            
        <select class="form-control select2" name="especializaciones[]" multiple>
        <?php foreach ($especializaciones as $esp): ?>
            <option value="<?php echo htmlspecialchars($esp['id']); ?>">
            <?php echo htmlspecialchars($esp['nombre']); ?>
            </option>
        <?php endforeach; ?>
        </select>
        <br><br>
        
        <p class="fade-label">Otra Especialidad (Opcional)</p>
        <input type="text" class="form-control" name="otra_especialidad" placeholder="Ej: Reparación GPU">
        <br><br>

        <p class="fade-label">Contraseña</p>
        <div class="password-container">
        <input type="password" class="form-control" id="contrasena" name="contrasena" minlength="8" placeholder="mínimo 8 caracteres" required>
        <i class="fa-solid fa-eye toggle-password" onclick="togglePassword('contrasena', this)"></i>
        </div>
        <br>

        <p class="fade-label">Confirmar Contraseña</p>
        <div class="password-container">
        <input type="password" class="form-control" id="confirmar_contrasena" name="confirmar_contrasena" minlength="8" required>
        <i class="fa-solid fa-eye toggle-password" onclick="togglePassword('confirmar_contrasena', this)"></i>
        </div>
        <p id="error-password" class="error-text"></p>
        <br>

        <input type="checkbox" id="terminos" name="terminos" required>
        <label for="terminos">Acepto los <a id="link-terminos-cliente" class="link-terminos-cliente" href="javascript:void(0);">términos y condiciones</a></label>

        <input class="button" type="submit" value="Guardar">
        <a href="Index.php?accion=login" class="login9" >¿Ya tiene una cuenta? Inicie sesión</a>

    </form>
  </section>
  <br>
</div>

<div id="modal-terminos-cliente" class="modal-terminos-overlay-cliente" style="display: none;">
    <div class="modal-terminos-content-cliente">
        <span class="modal-terminos-close-cliente" id="close-modal-terminos-cliente">&times;</span>
        <h2>Términos y Condiciones</h2>
        <div id="modal-terminos-body-cliente">

        </div>
    </div>
</div>

<script src="Assets/js/terminos.js"></script>
<script>
$(document).ready(function() {
    var selectedOrder = [];  // Array para trackear el orden de selección

    $('.select2').select2({
        placeholder: "Selecciona una o más especializaciones",
        allowClear: true,
        width: '100%',
        closeOnSelect: false,
        scrollAfterSelect: true,  // Scroll automático hacia la selección
        theme: 'bootstrap',
        language: {
            noResults: function() {
                return "No se encontraron resultados";
            }
        },
        sorter: function(data) {  // ✅ Custom sorter para orden de selección
            return data.sort(function(a, b) {
                var aSelected = selectedOrder.indexOf(a.id) !== -1;
                var bSelected = selectedOrder.indexOf(b.id) !== -1;
                if (aSelected && !bSelected) return -1;  // Seleccionadas primero
                if (!aSelected && bSelected) return 1;
                if (aSelected && bSelected) {
                    // Entre seleccionadas, orden por selección
                    return selectedOrder.indexOf(a.id) - selectedOrder.indexOf(b.id);
                }
                // No seleccionadas: orden alfabético
                return a.text.localeCompare(b.text);
            });
        }
    });

    // Trackear selecciones para el orden
    $('.select2').on('select2:select', function(e) {
        if (selectedOrder.indexOf(e.params.data.id) === -1) {
            selectedOrder.push(e.params.data.id);  // Agrega al orden si no está
        }
    });

    $('.select2').on('select2:unselect', function(e) {
        var index = selectedOrder.indexOf(e.params.data.id);
        if (index > -1) {
            selectedOrder.splice(index, 1);  // Remueve del orden
        }
    });

    $('.select2').on('select2:select select2:unselect', function() {
        var $selection = $(this).next('.select2-container').find('.select2-selection__rendered');
        var choices = $selection.find('.select2-selection__choice');
        // Reordenar las tags basadas en selectedOrder
        choices.sort(function(a, b) {
            var aId = $(a).attr('title'); // Asumiendo que 'title' tiene el ID o texto; ajusta si es 'data-id'
            var bId = $(b).attr('title');
            return selectedOrder.indexOf(aId) - selectedOrder.indexOf(bId);
        });
        $selection.append(choices); // Reaplicar el orden
    });
});
</script>

<script src="Assets/js/imagenformulario.js"></script>
<script src="Assets/js/vercontrasena.js"></script>

</body>
</html>