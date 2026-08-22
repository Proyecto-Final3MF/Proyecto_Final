<?php
require_once __DIR__ . '/../Models/Mensaje.php';
require_once __DIR__ . '/../Models/SolicitudM.php';

class ChatC {
    private $mensajeModel;

    public function __construct() {
        $this->mensajeModel = new Mensaje();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    public function mostrarChat() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $usuarioId = $_SESSION['id'] ?? null;
        $otroUsuarioId = $_GET['usuario_id'] ?? null;
        $solicitudId = $_GET['solicitud_id'] ?? null;

        if (!$usuarioId || !$otroUsuarioId) {
            echo "Usuario no especificado";
            return;
        }

        $mensajes = $this->mensajeModel->obtenerConversacion($usuarioId, $otroUsuarioId, $solicitudId);

        // Pasar el ID de solicitud a la vista
        $solicitud = null;
        if ($solicitudId) {
            $solicitudModel = new Solicitud();
            $solicitud = $solicitudModel->obtenerSolicitudPorId($solicitudId);
        }

        require_once __DIR__ . '/../Views/Chat.php';
    }

    public function cargarMensajes() {
        $usuarioId = $_SESSION['id'] ?? null;
        $otroUsuarioId = $_GET['usuario_id'] ?? null;
        $solicitudId = $_GET['solicitud_id'] ?? null;

        if (!$usuarioId || !$otroUsuarioId) {
            http_response_code(400);
            exit("Faltan paramentros.");
        }

        $mensajeModel = new Mensaje();
        $mensajes = $mensajeModel->obtenerConversacion($usuarioId, $otroUsuarioId, $solicitudId);

        include __DIR__ . "/../Views/Mensajes.php";
    }

    // Mostrar la vista de chat
    public function mostrarConversacion() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_GET['usuario_id'])) {
            echo "Error: no se especificó el usuario receptor.";
            return;
        }

        $usuarioId = $_SESSION['id'] ?? null;
        $otroUsuarioId = intval($_GET['usuario_id'] ?? null);
        $solicitudId = $_GET['solicitud_id'] ?? null;

        if (!$usuarioId || !$otroUsuarioId) {
            echo "Error: no se especificoel usuario receptor.";
            return;
        }

        // Crear instancia de Mensaje
        $mensajeModel = new Mensaje();

        // Obtener todos los mensajes entre los dos usuarios
        $mensajes = $mensajeModel->obtenerConversacion($usuarioId, $otroUsuarioId, $solicitudId);

        include __DIR__ . "/../Views/Conversaciones.php";
    }

    // Devolver solo los mensajes (para Ajax)
    public function listarMensajes() {
        $mensaje = new Mensaje();
        $usuario_id = $_SESSION['id'] ?? null;
        $rol = $_SESSION['rol'] ?? null;
        // Si no hay sesión, se devuelve error 401
        if (!$usuario_id) {
            http_response_code(401);
            exit("No autorizado");
        }

        $esAdmin = ($rol == 3);
        $mensajes = $mensaje->obtenerMensajes($usuario_id, $esAdmin);
        // Recorre los mensajes y los imprime en HTML
        foreach ($mensajes as $m) {
            echo "<p><strong>" . htmlspecialchars($m['usuario']) . ":</strong> " .
                nl2br(htmlspecialchars($m['mensaje'])) . "</p>";
        }
    }

    public function listarConversaciones() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $usuario_id = $_SESSION['id'] ?? null;
        if (!$usuario_id) {
            header("Location: Index.php?accion=login");
            exit();
        }

        $mensajeModel = new Mensaje();
        $conversaciones = $mensajeModel->obtenerConversaciones($usuario_id);

        include __DIR__ . "/../Views/Conversaciones.php";
    }
    // Lista todas las conversaciones de un usuario
    public function registroChats() {
        $mensaje = new Mensaje();
        $conversaciones = $mensaje->obtenerTodasLasConversaciones();
        include __DIR__ . "/../Views/RegistroChats.php";
    }

    public function abrirChat() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $idSolicitud = $_GET['id_solicitud'] ?? null;
        $usuarioId = $_SESSION['id'] ?? null;

        if (!$idSolicitud || !$usuarioId) {
            $_SESSION['mensaje'] = "Error: Solicitud o usuario no especificado.";
            header("Location: Index.php?accion=listarSA");
            exit();
        }

        // Obtener datos de la solicitud
        require_once __DIR__ . '/../Models/SolicitudM.php';
        $solicitud = new Solicitud();
        $datosSolicitud = $solicitud->obtenerSolicitudPorId($idSolicitud);

        if (!$datosSolicitud) {
            $_SESSION['mensaje'] = "Error: Solicitud no encontrada.";
            header("Location: Index.php?accion=listarSA");
            exit();
        }

        // Determinar con quién hablar según rol
        if ($_SESSION['rol'] == ROL_TECNICO) {
            $otroUsuarioId = $datosSolicitud['cliente_id'];
        } elseif ($_SESSION['rol'] == ROL_CLIENTE) {
            $otroUsuarioId = $datosSolicitud['tecnico_id'];
        } else {
            $_SESSION['mensaje'] = "Error: rol no válido.";
            header("Location: Index.php?accion=listarSA");
            exit();
        }

        // Redirigir a la conversación incluyendo el ID de solicitud
        header("Location: Index.php?accion=mostrarChat&usuario_id=" . $otroUsuarioId . "&solicitud_id=" . $idSolicitud);
        exit();
    }

    // Guardar nuevo mensaje
    public function enviar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $usuarioId = $_SESSION['id'] ?? null;
        $receptor_id = $_POST['receptor_id'] ?? null;
        $mensajeTexto = trim($_POST['mensaje'] ?? '');
        $solicitud_id = $_POST['solicitud_id'] ?? null;

        if (!$usuarioId || !$receptor_id || $mensajeTexto === '' || !$solicitud_id) {
            http_response_code(400);
            echo "Error: Faltan paramentros requeridos (usuario, receptor, mensaje o solicitud)";
            exit();
        }

        $resultado = $this->mensajeModel->enviarMensaje($usuarioId, $receptor_id, $mensajeTexto, $solicitud_id);

        if (!$resultado) {
            http_response_code(500);
            echo "Error al enviar el mensaje";
        }

        exit();
    }

    public function borrar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $receptor_id = $_POST['receptor_id'] ?? null;
        $usuario_Id = $_POST['usuario_id'] ?? $_SESSION['id'];

        if (!$receptor_id) {
            header("Location: Index.php?accion=listarConversaciones");
            exit();
        }

        $mensajeModel = new Mensaje();
        $mensajeModel->borrarConversacion($usuario_Id, $receptor_id);

        header("Location: Index.php?accion=listarConversaciones");
        exit();
    }
}