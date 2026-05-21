<?php
require_once(__DIR__ . '/../Models/SolicitudM.php');
require_once(__DIR__ . '/solicitud_historiaC.php');
require_once(__DIR__ . "/../Views/include/popup.php");
require_once(__DIR__ . "/HistorialC.php");
require_once(__DIR__ . '/../Services/EmailService.php');

class SolicitudC {
    private $solicitudModel;
    private $historiaC;
    private $historialController;
    private $emailService; // Propiedad para el servicio de email
    private $adminEmail; // Propiedad para el email del admin

    public function __construct() {
        $this->solicitudModel = new Solicitud();
        $this->historiaC = new HistoriaC();
        $this->historialController = new HistorialController();

        // Inicializar EmailService y obtener la configuración
        $this->emailService = new EmailService();
        $config = include(__DIR__ . '/../Config/email.php');
        $this->adminEmail = $config['notifications']['admin_email'];
    }

    public function formularioS() {
        $id_usuario = $_SESSION['id'] ?? null;

        if ($id_usuario == null) {
            header("Location: Index.php?accion=login");
            exit();
        }

        $solicitud = new Solicitud();
        $productos = $solicitud->obtenerProductos($id_usuario);
        $producto_preseleccionado_id = null;
        include(__DIR__ . "/../Views/Solicitudes/Cliente/FormularioS.php");
    }

    public function guardarS() {
        $solicitud = new Solicitud();
        $titulo = trim($_POST['titulo']) ?? '';
        $producto = $_POST['producto'] ?? '';
        $descripcion = trim($_POST['descripcion']) ?? '';
        $prioridad = $_POST['prioridad'] ?? '';
        $usuario_id = $_SESSION['id'] ?? '';

        if (empty($titulo) || empty($producto) || empty($descripcion) || empty($usuario_id) || $titulo === '' || $descripcion === '' || $producto === '') {
            $_SESSION['tipo_mensaje'] = "warning";
            $_SESSION['mensaje'] = "Error: Faltan campos obligatorios en la solicitud urgente.";
            header("Location: Index.php?accion=FormularioS");
            exit();
        }

        $id_solicitud = $solicitud->crearS($titulo, $descripcion, $producto, $usuario_id, $prioridad);

        if ($id_solicitud) {
            $_SESSION['tipo_mensaje'] = "success";
            $_SESSION['mensaje'] = "Solicitud guardada existosamente";

            $this->historiaC->registrarEvento($id_solicitud, "Solicitud creada");
            $this->historialController->registrarModificacion($_SESSION['usuario'], $_SESSION['id'], "Creo la solicitud", $titulo, $id_solicitud, null);

            require_once(__DIR__ . '/NotificacionC.php');
            $notificacion = new NotificacionC();

            // Notificar a todos los técnicos (rol_id = 1)
            $conn = conectar();
            $result = $conn->query("SELECT id FROM usuario WHERE rol_id = 1");
            while ($row = $result->fetch_assoc()) {
                $tipo = ($prioridad === 'urgente') ? 'urgente' : 'normal';
                $notificacion->crearNotificacion($row['id'], "Nueva solicitud creada: $titulo", $tipo);
            }

            header("Location: Index.php?accion=formularioS");
        } else {
            $_SESSION['error'] = "Error al guardar la solicitud.";
            $_SESSION['tipo_mensaje'] = "error";
            header("Location: Index.php?accion=formularioS");
        }
    }

    public function guardarSU() {
        $solicitud = new Solicitud();
        $titulo = trim($_POST['titulo']) ?? '';
        $producto = $_POST['producto'] ?? '';
        $descripcion = trim($_POST['descripcion']) ?? '';
        $prioridad = 'urgente';

        $usuario_id = $_SESSION['id'] ?? '';

        if (empty($titulo) || empty($producto) || empty($descripcion) || empty($usuario_id) || $titulo === '' || $descripcion === '' || $producto === '') {
            $_SESSION['tipo_mensaje'] = "warning";
            $_SESSION['mensaje'] = "Error: Faltan campos obligatorios en la solicitud urgente.";
            header("Location: Index.php?accion=urgenteS");
            exit();
        }

        $id_solicitud = $solicitud->crearS($titulo, $descripcion, $producto, $usuario_id, $prioridad);

        if ($id_solicitud) {
            $_SESSION['mensaje'] = "Solicitud urgente guardada exitosamente";
            $_SESSION['tipo_mensaje'] = "success";

            $this->historiaC->registrarEvento($id_solicitud, "Solicitud Urgente creada");
            $this->historialController->registrarModificacion($_SESSION['usuario'], $_SESSION['id'], "Creo la solicitud urgente", $titulo, $id_solicitud, null);

            $usuarioModel = new Usuario();  // Instancia del modelo
            $emailsTecnicos = $usuarioModel->obtenerEmailsTecnicos();
            $asunto = "🚨 Nueva Solicitud Urgente Creada: $titulo";
            $mensaje = "Se ha creado una nueva solicitud urgente:<br><strong>Título:</strong> {$titulo}<br><strong>Descripción:</strong> {$descripcion}<br><br>";
            foreach ($emailsTecnicos as $email) {
                $this->emailService->enviarNotificacion($email, $asunto, $mensaje, 'urgente');
            }

            require_once(__DIR__ . '/NotificacionC.php');
            $notificacion = new NotificacionC();

            // Notificar a todos los técnicos (rol_id = 1)
            $conn = conectar();
            $result = $conn->query("SELECT id FROM usuario WHERE rol_id = 1");
            while ($row = $result->fetch_assoc()) {
                $tipo = 'urgente';
                $notificacion->crearNotificacion($row['id'], "Nueva solicitud Urgente creada: $titulo", $tipo);
            }

            header("Location: Index.php?accion=listarSLU");
        } else {
            $_SESSION['mensaje'] = "Error al guardar la solicitud urgente.";
            $_SESSION['tipo_mensaje'] = "error";
            header("Location: Index.php?accion=urgenteS");
        }
    }

    public function borrarS() {
        $solicitud = new Solicitud();
        $id = $_GET['id'];

        $datosSolicitud = $solicitud->obtenerSolicitudPorId($id);
        if (!$datosSolicitud) {
            $_SESSION['mensaje'] = "no se pudo actualizar la solicitud";
            $_SESSION['tipo_mensaje'] = "error";
            header("Location: Index.php?accion=redireccion");
            exit();
        }

        $solicitud->borrarS($id);
        if ($solicitud) {
            $_SESSION['mensaje'] = "Solicitud eliminada existosamente";
            $_SESSION['tipo_mensaje'] = "success";

            $this->historialController->registrarModificacion($_SESSION['usuario'], $_SESSION['id'], "eliminó la solicitud", $datosSolicitud['titulo'], $datosSolicitud['id'], null);
            header("Location: Index.php?accion=redireccion");
            exit();
        } else {
            $_SESSION['mensaje'] = "no se pudo actualizar la solicitud";
            $_SESSION['tipo_mensaje'] = "error";
            header("Location: Index.php?accion=redireccion");
            exit();
        }
    }

    public function listarSLC() {
        $id_usuario = $_SESSION['id'] ?? null;
        if ($id_usuario == null) {
            header("Location: Index.php?accion=login");
            exit();
        }
        $solicitud = new Solicitud();
        $resultados = $solicitud->listarSLC($id_usuario);
        include(__DIR__ . "/../Views/Solicitudes/Cliente/ListadoSLU.php");
    }

    public function ListarTL() {
        if (isset($_SESSION['id'])) {
            $usuarioId = $_SESSION['id'];
            $search = $_GET['search'] ?? null;

            $notifC = new NotificacionC();
            $notifC->marcarTodasLeidas('normal');

            return $this->solicitudModel->ListarTL($usuarioId, $search);
        } else {
            echo "paso algo mal";
            return [];
        }
    }

    public function asignarS() {
        $id_tecnico = $_SESSION['id'] ?? null;
        $id_soli = $_GET['id_solicitud'] ?? null;

        if ($id_tecnico === null || $id_soli === null) {
            $_SESSION['mensaje'] = "Error: ID de usuario o solicitud no proporcionado.";
            $_SESSION['tipo_mensaje'] = "error";
            header("Location: Index.php?accion=listarTL");
            exit();
        }

        $success = $this->solicitudModel->asignarS($id_tecnico, $id_soli);

        if ($success) {
            $_SESSION['mensaje'] = "Solicitud aceptada exitosamente";
            $_SESSION['tipo_mensaje'] = "success";
            $this->historiaC->registrarEvento($id_soli, "Solicitud asignada");
            require_once(__DIR__ . '/NotificacionC.php');
            $notificacion = new NotificacionC();

            $solicitud = $this->solicitudModel->obtenerSolicitudPorId($id_soli);
            $notificacion->crearNotificacion($solicitud['cliente_id'], "Tu solicitud '{$solicitud['titulo']}' fue aceptada por un técnico.", 'urgente');

            $usuarioModel = new Usuario();
            $emailCliente = $usuarioModel->obtenerEmailUsuarioPorId($solicitud['cliente_id']);
            $asunto = "✅ Tu Solicitud Fue Aceptada: {$solicitud['titulo']}";
            $mensaje = "Tu solicitud ha sido aceptada por un técnico.<br><br>";
            $this->emailService->enviarNotificacion($emailCliente, $asunto, $mensaje, 'normal');

            $this->historialController->registrarModificacion($_SESSION['usuario'], $id_tecnico, "seleccionó a la solicitud", $solicitud['titulo'], $id_soli, null);

            header("Location: Index.php?accion=listarTL");
            exit();
        } else {
            $_SESSION['mensaje'] = "Error al aceptar la solicitud.";
            $_SESSION['tipo_mensaje'] = "error";
            header("Location: Index.php?accion=listarTL");
            exit();
        }
    }

    public function listarSA() {
        $id_usuario = $_SESSION['id'] ?? null;
        if ($id_usuario == null) {
            header("Location: Index.php?accion=login");
            exit();
        }
        $solicitud = new Solicitud();
        $resultados = $solicitud->listarSA($id_usuario);
        include(__DIR__ . "/../Views/Solicitudes/listadoSA.php");
    }

    public function listarST() {
        $id_usuario = $_SESSION['id'] ?? null;
        if ($id_usuario == null) {
            header("Location: Index.php?accion=login");
            exit();
        }
        $search = $_GET['search'] ?? null;
        $solicitud = new Solicitud();
        $resultados = $solicitud->listarST($id_usuario, $search);
        include(__DIR__ . "/../Views/Solicitudes/listadoST.php");
    }

    public function editarSF() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['mensaje'] = "Error: ID de solicitud no proporcionado.";
            $_SESSION['tipo_mensaje'] = "error";
            header("Location: Index.php?accion=redireccion");
            exit();
        }

        $datosSolicitud = $this->solicitudModel->obtenerSolicitudPorId($id);

        if (!$datosSolicitud) {
            $_SESSION['mensaje'] = "Error: Solicitud no encontrada.";
            $_SESSION['tipo_mensaje'] = "error";
            header("Location: Index.php?accion=redireccion");
            exit();
        }

        // Obtener la lista de estados para el select
        $estados = $this->solicitudModel->obtenerEstados();

        include(__DIR__ . "/../Views/Solicitudes/editarSF.php");
    }

    public function actualizarSF() {
        $id = $_POST['id'] ?? null;
        $descripcion = trim($_POST['descripcion']) ?? '';
        $estado_id = $_POST['estado'] ?? null;
        $precio = $_POST['precio'] ?? 0.0;
        $id_cliente = $_POST['id_cliente'];
        $id_tecnico = $_SESSION['id'];

        $checkU = $this->solicitudModel->checkUsuario($id, $id_tecnico, $id_cliente);
        if (!$checkU) {
            $_SESSION['tipo_mensaje'] = "error";
            $_SESSION['mensaje'] = "Accesso negado";
            header("Location:Index.php?accion=listarSA");
            exit();
        }

        if ($precio < 0) {
            $_SESSION['tipo_mensaje'] = "error";
            $_SESSION['mensaje'] = "El precio no puede ser negativo.";
            header("Location:Index.php?accion=listarSA");
            exit();
        }

        $datosSolicitud = $this->solicitudModel->obtenerSolicitudPorId($id);
        $estadoAntiguo = $datosSolicitud['estado_id'];
        $descAntigua = $datosSolicitud['descripcion'];

        if (!$id || empty($descripcion) || !$estado_id || $descripcion === '') {
            $_SESSION['mensaje'] = "Error: Todos los campos son obligatorios.";
            $_SESSION['tipo_mensaje'] = "error";
            header("Location: Index.php?accion=redireccion");
            exit();
        }

        $nuevoEstado = $this->solicitudModel->obtenerNombreEstadoPorId($estado_id);

        if ($this->solicitudModel->actualizarS($id, $descripcion, $estado_id, $precio)) {
            $_SESSION['tipo_mensaje'] = "success";
            $_SESSION['mensaje'] = "Solicitud actualizada exitosamente.";

            // REGISTRAR CAMBIO DE ESTADO
            $obs = "";
            if ($estadoAntiguo !== $estado_id) {
                // Se usa $nuevoEstado['nombre'] que viene de la nueva función
                $evento = "Estado cambiado a " . strtolower($nuevoEstado['nombre']);
                $this->historiaC->registrarEvento($id, $evento);
                $obs .= $evento . ". ‎ ";

                $usuarioModel = new Usuario();
                $emailCliente = $usuarioModel->obtenerEmailUsuarioPorId($datosSolicitud['cliente_id']);
                $asunto = "🔄 Estado de Tu Solicitud Cambió: {$datosSolicitud['titulo']}";
                 // Finalizado -> listarST, otros -> listarSA
                $mensaje = "El estado de tu solicitud cambió a: <strong>{$nuevoEstado['nombre']}</strong>.<br><br>";
                $this->emailService->enviarNotificacion($emailCliente, $asunto, $mensaje, 'normal');
            }

            if ($descAntigua !== $descripcion) {
                $this->historiaC->registrarEvento($id, "Descripción modificada");
                $obs .= "Desc: '$descAntigua' ⟶ '$descripcion'.";
            }

            $this->historialController->registrarModificacion($_SESSION['usuario'], $_SESSION['id'], "Editó la solicitud", $datosSolicitud['titulo'], $id, $obs);

            require_once(__DIR__ . '/NotificacionC.php');
            $notificacion = new NotificacionC();

            $solicitud = $this->solicitudModel->obtenerSolicitudPorId($id);
            $notificacion->crearNotificacion($solicitud['cliente_id'], "Tu solicitud '{$solicitud['titulo']}' cambió de estado a '{$nuevoEstado['nombre']}'.", 'urgente');

            header("Location: Index.php?accion=redireccion");
            exit();
        } else {
            $_SESSION['mensaje'] = "Error al actualizar la solicitud.";
            $_SESSION['tipo_mensaje'] = "error";
            header("Location: Index.php?accion=redireccion");
            exit();
        }
    }

    public function cancelarS() {
        $id_soli = $_GET['id_solicitud'] ?? null;
        $usuarioId = $_SESSION['id'] ?? null;

        if ($id_soli === null || $usuarioId === null) {
            $_SESSION['mensaje'] = "Error: ID de solicitud o usuario no proporcionado.";
            $_SESSION['tipo_mensaje'] = "error";
            header("Location: Index.php?accion=listarSA");
            exit();
        }

        $solicitud = $this->solicitudModel->obtenerSolicitudPorId($id_soli);
        if (!$solicitud) {
            $_SESSION['mensaje'] = "Error: La solicitud no existe.";
            $_SESSION['tipo_mensaje'] = "error";
            header("Location: Index.php?accion=listarSA");
            exit();
        }

        // Asegurarse de que el usuario es el técnico asignado o el cliente que la creó
        if ($solicitud['tecnico_id'] != $usuarioId && $solicitud['cliente_id'] != $usuarioId) {
            $_SESSION['mensaje'] = "No tienes permiso para cancelar esta solicitud.";
            $_SESSION['tipo_mensaje'] = "error";
            header("Location: Index.php?accion=listarSA");
            exit();
        }

        if ($this->solicitudModel->cancelarS($id_soli)) {
            $_SESSION['tipo_mensaje'] = "success";
            $_SESSION['mensaje'] = "Solicitud cancelada exitosamente.";

            $this->historiaC->registrarEvento($id_soli, "Solicitud cancelada");
            $this->historialController->registrarModificacion($_SESSION['usuario'], $_SESSION['id'], "canceló la solicitud", $solicitud['titulo'], $id_soli, null);

            $usuarioModel = new Usuario();
            if ($_SESSION['rol'] == 1) {  // Técnico canceló -> notificar al cliente
                $emailDestinatario = $usuarioModel->obtenerEmailUsuarioPorId($solicitud['cliente_id']);
                $asunto = "❌ Tu Solicitud Fue Cancelada por el Técnico: {$solicitud['titulo']}";
                $mensaje = "El técnico canceló tu solicitud ya aceptada.<br><br>";
            } elseif ($_SESSION['rol'] == 2) {  // Cliente canceló -> notificar al técnico
                $emailDestinatario = $usuarioModel->obtenerEmailUsuarioPorId($solicitud['tecnico_id']);
                $asunto = "❌ Solicitud Cancelada por el Cliente: {$solicitud['titulo']}";
                $mensaje = "El cliente canceló la solicitud ya aceptada.<br><br>";
            }

            $this->emailService->enviarNotificacion($emailDestinatario, $asunto, $mensaje, 'urgente');

            // Asumo que tienes definidas estas constantes
            define('ROL_TECNICO', 1);
            define('ROL_CLIENTE', 2);

            if (isset($_SESSION['rol']) && $_SESSION['rol'] == ROL_TECNICO) {
                header("Location: Index.php?accion=listarTL"); // Redirigir a solicitudes disponibles
            } elseif (isset($_SESSION['rol']) && $_SESSION['rol'] == ROL_CLIENTE) {
                header("Location: Index.php?accion=listarSLU"); // Redirigir a sus solicitudes
            } else {
                // Si el rol no es ni técnico ni cliente, puedes redirigir a una página predeterminada
                header("Location: Index.php?accion=redireccion");
            }
            exit();
        } else {
            $_SESSION['mensaje'] = "Error al cancelar la solicitud.";
            $_SESSION['tipo_mensaje'] = "error";
            header("Location: Index.php?accion=listarSA");
            exit();
        }
    }

    public function formularioUS() {
        $id_usuario = $_SESSION['id'] ?? null;

        if ($id_usuario == null) {
            header("Location: Index.php?accion=login");
            exit();
        }

        $solicitud = new Solicitud();
        $productos = $solicitud->obtenerProductos($id_usuario);
        $ultimo_producto = $solicitud->obtenerProductoUrgente($id_usuario);
        $producto_preseleccionado_id = $ultimo_producto['id'] ?? null;
        include(__DIR__ . "/../Views/Solicitudes/Cliente/FormularioUS.php");
    }
}
?>