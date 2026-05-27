<?php 
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != ROL_ADMIN) {
    header("Location: Index.php?accion=redireccion");
    exit();
} 

require_once(__DIR__ . "../../../include/UH.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Usuarios</title>
    <link rel="stylesheet" href="./Assets/css/Main.css">
</head>
<body>
    <div class="content-wrapper">
    <br>
<div class="sticky-header">
<h1 class="inicio55">Listado de todos los Usuarios</h1>
</div>

<div class="btn-volver-container fade-slide">
  <button class="btn-volver" id="btnVolver">
    <i class="fa fa-arrow-left"></i> Volver
  </button>
</div>

<form action="Index.php" class="ordenar-form">
    <label for="search">Buscar: </label>
    <input type="text" id="search" name="search" placeholder="Buscar Usuario" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" class="milanesa">
    <label for="orden">Ordenar por:</label>
    <input type="hidden" name="accion" value="listarU">
    <select name="orden" id="orden">
        <option value="Más Recientes" <?php echo ($_GET['orden'] ?? 'Más Recientes') == 'Más Recientes' ? 'selected' : ''; ?>>Más Recientes</option>
        <option value="Más Antiguos" <?php echo ($_GET['orden'] ?? 'Más Antiguos') == 'Más Antiguos' ? 'selected' : ''; ?>>Más Antiguos</option>
        <option value="A-Z" <?php echo ($_GET['orden'] ?? '') == 'A-Z' ? 'selected' : ''; ?>>A-Z</option>
        <option value="Z-A" <?php echo ($_GET['orden'] ?? '') == 'Z-A' ? 'selected' : ''; ?>>Z-A</option>
    </select>
    <label for="rol_filter">Tipo de Usuarios:</label>
    <select name="rol_filter" id="rol_filter">
        <option value="Todos" <?php echo ($_GET['rol_filter'] ?? 'Todos') == 'Todos' ? 'selected' : ''; ?>>Todos</option>
        <option value="Clientes" <?php echo ($_GET['rol_filter'] ?? '') == 'Clientes' ? 'selected' : ''; ?>>Clientes</option>
        <option value="Tecnicos" <?php echo ($_GET['rol_filter'] ?? '') == 'Tecnicos' ? 'selected' : ''; ?>>Tecnicos</option>
        <option value="Administradores" <?php echo ($_GET['rol_filter'] ?? '') == 'Administradores' ? 'selected' : ''; ?>>Administradores</option>
    </select>
    <button type="submit" class="btn-boton">Buscar</button>
</form>

<?php if (empty($resultados)): ?>
    <div class="alert alert-info1">
        No hay usuarios registrados con ese nombre
    </div>
<?php else: ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Foto</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($resultados as $u): ?>
                <tr class="list-item">
                    <td><?= $u['id'] ?></td>
                    <td>
                    <img src="<?= htmlspecialchars($u['foto_perfil'] ?? 'Assets/imagenes/perfil/fotodefault.webp') ?>" 
                     alt="Foto de <?= htmlspecialchars($u['nombre']) ?>" 
                     class="foto-mini">
                    </td>
                    <td><?= htmlspecialchars($u['nombre']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= htmlspecialchars($u['rol']) ?></td>
                    <td>
                        <div class="btn-group-actions">  
                            <a href="Index.php?accion=editarU&id=<?= $u['id'] ?>" class="icon-btn edit">
                            <i class="fa fa-edit"></i></a>
                            <?php if ($_SESSION['rol'] == ROL_ADMIN): ?>
                            <?php endif; ?>
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
    </div>
    <script src="Assets/js/animaciondetablas.js"></script>
    <script src="Assets/js/botonvolver.js"></script>
    <script src="Assets/js/transicion.js"></script>
    <script src="Assets/js/paginacion.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>