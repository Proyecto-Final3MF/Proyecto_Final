<?php
if (isset($_SESSION['rol']) == 1 or isset($_SESSION['rol']) == 2) {
    header("Location: Index.php?accion=redireccion");
}

require_once(__DIR__ . "../../include/UH.php");

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="./Assets/css/Main.css">
</head>
<body>
    <div class="contenedor-formulario">
        <section class="formularios99">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <h3>Iniciar Sesion</h3>
            <form method="POST" action="Index.php?accion=autenticar">

                <p class="fade-label">Correo electrónico</p>
                <input type="email" class="form-control" id="usuario" name="usuario" autocomplete="off" required> <br><br>

                <p class="fade-label">Contraseña</p>
                <div class="password-container">
                    <input type="password" class="form-control" id="contrasena" name="contrasena" autocomplete="off" required>
                    <i class="fa-solid fa-eye toggle-password" onclick="togglePassword('contrasena', this)"></i>

                </div>
                <br>

                <button class="button" type="submit">Entrar</button>
                <a href="Index.php?accion=register" class="login9">¿No tiene una cuenta? Regístrese</a>
            </form>

        </section>
        <br>
    </div>
    <script src="Assets/js/transicion.js"></script>
    <script src="Assets/js/vercontrasena.js"></script>
</body>

</html>