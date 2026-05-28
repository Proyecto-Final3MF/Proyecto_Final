<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// En la vista (ej. redireccion.php o listarU.php)
if (isset($_SESSION['mensaje'])) {
    include(__DIR__ . "/../Views/include/popup.php"); // Asegúrate de que popup.php muestre $_SESSION['mensaje']
    unset($_SESSION['mensaje']); // Limpiar después de mostrar
    unset($_SESSION['tipo_mensaje']);
}

require_once(dirname(__DIR__, 2) . '/Controllers/NotificacionC.php');

$notifC = new NotificacionC();
$notificaciones = $notifC->listarNoLeidas('urgente');  // Solo urgentes
?>

<header>
    <title>C O S M O S</title>
    <link rel="stylesheet" href="./Assets/css/Main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <nav class="navbar">
        <div class="navbar-left">
            <a href="Index.php?accion=inicio" class="logo-link" title="Inicio">
                <img src="Assets/imagenes/logonueva.png" height="50px" alt="logo de la app">
            </a>

            <button id="togglethemeBtn" class="btn-modo" title="Cambiar tema">
                <i class="fa-solid fa-moon"></i>
            </button>

            <select onchange="location.reload();"name="idioma" id="language-select">
                <option value="es">Español</option>
                <option value="en">English</option>
                <option value="pt">Portugues</option>
            </select>

            <ul class="nav-links" id="nav-links">
                <!-- Botón de Inicio -->
                <li>
                    <a href="Index.php?accion=inicio">
                        <i class="fa fa-home"></i> Inicio
                    </a>
                </li>

                <!-- Si el usuario está logueado, mostrar "Mi Unidad" -->
                <?php if (isset($_SESSION['usuario'])): ?>
                    <li>
                        <a href="Index.php?accion=redireccion">
                            <i class="fa fa-tools"></i> Mi Unidad
                        </a>
                    </li>
                <?php endif; ?>
                
                <!-- Estos dos botones siempre se muestran -->
                <!-- Estos dos botones solo se muestran en inicio, nosotros y contacto -->
                <?php
                $current_action = $_GET['accion'] ?? '';
                $allowed_pages = ['inicio', 'nosotros', 'contacto'];
                if (in_array($current_action, $allowed_pages)):
                ?>
                    <li>
                        <a href="Index.php?accion=nosotros">
                            <i class="fa fa-users"></i> Info
                        </a>
                    </li>
                    <li>
                        <a href="Index.php?accion=contacto">
                            <i class="fa fa-envelope"></i> Contáctanos
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="navbar-right">

            <?php if (!isset($_SESSION['usuario'])): ?>
                <div class="action-buttons">
                    <a href="Index.php?accion=login">
                        <button class="btn btn-boton">Iniciar sesión</button>
                    </a>
                    <a href="Index.php?accion=register">
                        <button class="btn btn-boton">Registrarse</button>
                    </a>
                </div>
            <?php else: ?>
                <!-- Notificaciones -->
                <div class="notificaciones" id="notificaciones">
                    <i class="fa fa-bell"></i>
                    <?php if (count($notificaciones) > 0): ?>
                        <span class="contador" id="notifContador"><?= count($notificaciones) ?></span>
                    <?php endif; ?>

                    <div class="dropdown">
    <?php if (count($notificaciones) > 0): ?>
        <?php foreach ($notificaciones as $n): ?>
            <?php
            $url = '';
            if (stripos($n['mensaje'], 'aceptada') !== false) {
                $url = 'Index.php?accion=listarSA';
            } elseif (stripos($n['mensaje'], 'urgente') !== false) {
                $url = 'Index.php?accion=listarTL';
            } elseif (stripos($n['mensaje'], 'verificar') !== false) {
                $url = 'Index.php?accion=verificarTecnicos';
            } elseif (stripos($n['mensaje'], 'cambió de estado') !== false) {
                $url = (stripos($n['mensaje'], 'Finalizado') !== false)
                    ? 'Index.php?accion=listarST'
                    : 'Index.php?accion=listarSA';
            } elseif (stripos($n['mensaje'], 'calificación') !== false) {
                $url = 'Index.php?accion=listarST';
            }

            // Formatear la fecha
            $fecha_formateada = '';
            if (!empty($n['fecha'])) {
                try {
                    $fecha = new DateTime($n['fecha']);
                    $fecha_formateada = $fecha->format('H:i d/m/Y');
                } catch (Exception $e) {
                    $fecha_formateada = $n['fecha']; // Fallback si hay error
                }
            }
            ?>
            <div class="notif-item">
                <span class="notif-text"><?= htmlspecialchars($n['mensaje']) ?> <small><?= $fecha_formateada ?></small></span>
                <?php if ($url): ?>
                    <a href="<?= $url ?>" class="verlasnoti">Ver</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="notif-item">Sin notificaciones nuevas</p>
    <?php endif; ?>
</div>


                </div>

                <!-- Menú de roles -->
                <div class="menu-rol-container">
                    <button class="dropdown-button" onclick="toggleRolMenu()">
                        <i class="fa-solid fa-bars"></i> Menú
                    </button>

                    <div id="rolDropdown" class="dropdown-menu">
                        <?php if ($_SESSION['rol'] == 2): ?>
                            <a href="Index.php?accion=listarP" class="dropdown-item">
                                <i class="fa-solid fa-box"></i> Mis Dispositivos
                            </a>
                            <a href="Index.php?accion=formularioS" class="dropdown-item">
                                <i class="fa-solid fa-plus-circle"></i> Crear Nueva Solicitud
                            </a>
                            <a href="Index.php?accion=listarSLU" class="dropdown-item">
                                <i class="fa-solid fa-hourglass-half"></i> Solicitudes Sin Asignar
                            </a>
                            <a href="Index.php?accion=listarSA" class="dropdown-item">
                                <i class="fa-solid fa-check"></i> Solicitudes Aceptadas
                            </a>
                            <a href="Index.php?accion=listarST" class="dropdown-item">
                                <i class="fa-solid fa-flag-checkered"></i> Solicitudes Terminadas
                            </a>
                        <?php elseif ($_SESSION['rol'] == 1): ?>
                            <a href="Index.php?accion=listarTL" class="dropdown-item">
                                <i class="fa-solid fa-list"></i> Solicitudes Disponibles
                            </a>
                            <a href="Index.php?accion=listarSA" class="dropdown-item">
                                <i class="fa-solid fa-check-circle"></i> Solicitudes Aceptadas
                            </a>
                            <a href="Index.php?accion=listarST" class="dropdown-item">
                                <i class="fa-solid fa-flag-checkered"></i> Solicitudes Terminadas
                            </a>
                        <?php elseif ($_SESSION['rol'] == 3): ?>
                            <a href="Index.php?accion=FormularioC" class="dropdown-item">
                                <i class="fa-solid fa-plus-circle"></i> Crear Nueva Categoría
                            </a>
                            <a href="Index.php?accion=listarC" class="dropdown-item">
                                <i class="fa-solid fa-list"></i> Todas Las Categorías
                            </a>
                            <a href="Index.php?accion=mostrarHistorial" class="dropdown-item">
                                <i class="fa-solid fa-clock-rotate-left"></i> Historial de Actividades
                            </a>
                            <a href="Index.php?accion=listarU" class="dropdown-item">
                                <i class="fa-solid fa-users"></i> Lista de Usuarios
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Menú de usuario -->
                <div class="user-menu-container">
                    <div class="dropdown">
                        <button class="dropdown-button" onclick="toggleDropdown()">
                            <img src="<?= htmlspecialchars($_SESSION['foto_perfil'] ?? 'Assets/imagenes/perfil/fotodefault.webp') ?>" alt="Perfil" class="foto-mini2">
                            <svg class="dropdown-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div id="userDropdown" class="dropdown-menu">
                            <div class="user-info">
                                <img src="<?= htmlspecialchars($_SESSION['foto_perfil'] ?? 'Assets/imagenes/perfil/fotodefault.webp') ?>" alt="Perfil" width="100px" class="foto-perfil">
                                <p class="user-name"><?= htmlspecialchars($_SESSION['usuario']) ?></p>
                                <p class="user-email"><?= htmlspecialchars($_SESSION['email'] ?? 'Sin correo') ?></p>
                            </div>

                            <?php if ($_SESSION['rol'] == 1): ?>
                                <a href="Index.php?accion=PerfilTecnico&id=<?= $_SESSION['id'] ?>" class="dropdown-item">
                                    <i class="fa fa-user"></i> Mi Perfil
                                </a>
                            <?php endif; ?>

                            <a href="Index.php?accion=editarU&id=<?= htmlspecialchars($_SESSION['id']) ?>" class="dropdown-item">
                                <i class="fa fa-edit"></i> Editar Perfil
                            </a>
                            <a href="Index.php?accion=listarConversaciones" class="dropdown-item">
                                <i class="fa fa-comments"></i> Mis Conversaciones
                            </a>
                            <a href="Index.php?accion=logout" class="dropdown-item">
                                <i class="fa fa-sign-out-alt"></i> Cerrar Sesión
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </nav>
</header>

<script src="Assets/js/menudeusuario.js"></script>
<script src="Assets/js/transicion.js"></script>
<script src="Assets/js/modoOscuro.js"></script>
<script type="module" src="Assets/js/traduccion/Traduccion.js"></script>