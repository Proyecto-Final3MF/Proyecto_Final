<?php 
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != ROL_ADMIN) {
    header("Location: Index.php?accion=redireccion");
    exit();
} 

require_once(__DIR__ . "/../../../include/UH.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Categorias</title>
    <link rel="stylesheet" href="./Assets/css/Main.css">
</head>
<body>
    <br>
<h1 class="inicio55">Listado de todas las categorias</h1>
<div class="botones-container fade-slide">
    <a href="Index.php?accion=FormularioC"><button class="btn btn-boton4442 btn-crear"> <i class="fa-solid fa-plus-circle"></i></button></a>
</div>
<div class="btn-volver-container fade-slide">
  <button class="btn-volver" id="btnVolver">
    <i class="fa fa-arrow-left"></i> Volver
  </button>
</div>
<form action="Index.php" class="ordenar-form">
    <label for="search">Buscar: </label>
    <input type="text" id="search" name="search" placeholder="Buscar Categoria" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
    <label for="orden">Ordenar por:</label>
    <input type="hidden" name="accion" value="listarC">
    <select name="orden" id="orden">
        <option value="Más Recientes" <?php echo ($_GET['orden'] ?? 'Más Recientes') == 'Más Recientes' ? 'selected' : ''; ?>>Más Recientes</option>
        <option value="Más Antiguas" <?php echo ($_GET['orden'] ?? 'Más Antiguas') == 'Más Antiguas' ? 'selected' : ''; ?>>Más Antiguas</option>
        <option value="A-Z" <?php echo ($_GET['orden'] ?? '') == 'A-Z' ? 'selected' : ''; ?>>A-Z</option>
        <option value="Z-A" <?php echo ($_GET['orden'] ?? '') == 'Z-A' ? 'selected' : ''; ?>>Z-A</option>
    </select>
    <button type="submit" class="btn-boton">Buscar</button>
</form>

<?php if (empty($resultados)): ?>
    <div class="alert alert-info">
        No hay categorias registradas. <a href="Index.php?accion=FormularioC" class="btn btn-boton777">Crear la primera</a>
    </div>
<?php else: ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($resultados as $c): ?>
                <tr class="list-item">
                    <td><?= $c['id'] ?></td>
                    <td><?= htmlspecialchars($c['nombre']) ?></td>
                    <td>
                        <div class="btn-group-actions">  
                            <a href="Index.php?accion=editarC&id=<?= $c['id'] ?>" class="icon-btn edit">
                            <i class="fa fa-edit"></i></a>
                            <a href="Index.php?accion=borrarC&id=<?= $c['id'] ?>" class="icon-btn delete">
                            <i class="fa fa-trash"></i></a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

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
    <script src="Assets/js/botonvolver.js"></script>
    <script src="Assets/js/transicion.js"></script>
    <script src="Assets/js/animaciondetablas.js"></script>
    <script src="Assets/js/paginacion.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>