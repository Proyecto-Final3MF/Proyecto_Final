<link rel="icon" type="image/png" href="Assets/imagenes/logonueva.png">

<?php
session_start();

require_once(__DIR__ . "/Config/conexion.php");
require_once(__DIR__ . "/Controllers/UsuarioC.php");
require_once(__DIR__ . "/Controllers/SolicitudC.php");
require_once(__DIR__ . "/Controllers/ProductoC.php");
require_once(__DIR__ . "/Controllers/CategoriaC.php");
require_once(__DIR__ . "/Controllers/ReviewC.php");
require_once(__DIR__ . "/Models/ProductoM.php");
require_once(__DIR__ . "/Controllers/ChatC.php");
require_once(__DIR__ . "/Controllers/NotificacionC.php");
require_once(__DIR__ . "/Controllers/PublicC.php");

$accion = $_GET['accion'] ?? 'inicio';

$acciones_publicas = ['login', 'autenticar', 'register', 'guardarU', 'guardarT', 'redireccion', 'espera', 'trabajo', 'TecnicoForm' , 'inicio', 'nosotros', 'contacto' , 'terminos' ];

if (!in_array($accion, $acciones_publicas)) {
  if (!isset($_SESSION['usuario'])) {
    header("Location: Index.php?accion=login");
    exit;
  }
}

const ROL_TECNICO = 1;
const ROL_CLIENTE = 2;
const ROL_ADMIN = 3;

switch ($accion) {

  //acciones publicas

  case 'login':
    $controller = new UsuarioC();
    $controller->login();
  break;

  case 'autenticar':
    $controller = new UsuarioC();
    $controller->autenticar();
  break;

  case 'register':
    $controller = new UsuarioC();
    $controller->crear();
  break;

  case 'inicio':
    $controller = new PublicC();
    $controller->inicio();
  break;

  case 'nosotros':
    $controller = new PublicC();
    $controller->nosotros();
  break;

  case 'contacto':
    $controller = new PublicC();
    $controller->contacto();
  break;

  case 'terminos':
    $controller = new PublicC();
    $controller->terminos();
  break;

  case 'trabajo':
    $controller = new UsuarioC();
    $controller->trabajo();
  break;

  case 'TecnicoForm':
    $controller = new UsuarioC();
    $controller->TecnicoForm();
  break;

  case 'guardarU':
    $controller = new UsuarioC();
    $controller->guardarU();
  break;

  case 'guardarT':
    $controller = new UsuarioC();
    $controller->guardarT();
  break;

  case 'redireccion':
    if (isset($_SESSION['usuario']) && isset($_SESSION['rol'])) {
      if ($_SESSION['rol'] == ROL_CLIENTE) {
        include(__DIR__ . "/Views/Usuario/Cliente/ClienteP.php");
      } elseif ($_SESSION['rol'] == ROL_TECNICO) {
        include(__DIR__ . "/Views/Usuario/Tecnico/TecnicoP.php");
      } elseif ($_SESSION['rol'] == ROL_ADMIN) {
        header("Location:Index.php?accion=panelA");
      } else {
        echo "<h1>Error: Rol no reconocido.</h1>";
        echo "<p><a href='Index.php?accion=logout'>Cerrar Sesión</a></p>";
      }
    } else {
      header("Location: Index.php?accion=login");
      exit();
    }
  break;


  //acciones para todos los roles
  case 'editarU':
    $controller = new UsuarioC();
    $controller->editarU();
  break;

  case 'actualizarU':
    $controller = new UsuarioC();
    $controller->actualizarU();
  break;

  case 'eliminarU':
    $controller = new UsuarioC();
    $controller->borrar();
  break;

  case 'logout':
    $controller = new UsuarioC();
    $controller->logout();
  break;

  case 'editarSF':
    $controller = new SolicitudC();
    $controller->editarSF();
  break;

  case 'actualizarSF':
    $controller = new SolicitudC();
    $controller->actualizarSF();
  break;

  case 'listarSA':
    $controller = new SolicitudC();
    $controller->listarSA();
  break;

  case 'cancelarS':
    $controller = new SolicitudC();
    $controller->cancelarS();
  break;

  case 'listarST':
    $controller = new SolicitudC();
    $controller->listarST();
  break;

  case 'solicitud_historia':
    $controller = new HistoriaC();
    $controller->mostrarHistoria();
  break;

  //acciones para el rol cliente

  //acciones para producto

  case 'guardarP':
    $controller = new ProductoC();
    $controller->guardarP();
  break;

  case 'borrarP':
    $controller = new ProductoC();
    $controller->borrarP();
  break;

  case 'editarP':
    $controller = new ProductoC();
    $controller->editarP();
  break;

  case 'actualizarP':
    $controller = new ProductoC();
    $controller->actualizarP();
  break;

  case 'listarP':
    $controller = new ProductoC();
    $controller->listarP();
  break;

  case 'formularioP':
    //crea una nueva instancia del objeto ProductoC
    $controller = new ProductoC();
    //ejecuta la funcion formularioP para mostrar formulario
    $controller->formularioP();
  break;

  case 'urgenteP':
    $controller = new ProductoC();
    $controller->urgentePF();
  break;

  case 'urgenteGP':
    $controller = new ProductoC();
    $controller->urgenteGP();
  break;

  //acciones para solicitudes

  case 'urgenteS':
    $controller = new SolicitudC();
    $controller->formularioUS();
    break;

  case 'formularioS':
    //crea una nueva instancia del objeto SolicitudC 
    $controller = new SolicitudC();
    //ejecuta la funcion formularioS para mostrar formulario
    $controller->formularioS();
  break;

  case 'guardarS':
    $controller = new SolicitudC();
    $controller->guardarS();
  break;
  
  case 'guardarSU':
    $controller = new SolicitudC();
    $controller->guardarSU();
  break;

  case 'borrarS':
    $controller = new SolicitudC();
    $controller->borrarS();
  break;

  case 'listarSLU':
    $controller = new SolicitudC();
    //ejecuta la funcion listarSLU para mostrar solicitudes de usuario
    $controller->listarSLC();
  break;

  case 'FormularioReview':
    $controller = new ReviewC();
    $controller->FormularioR();
  break;

  case 'AddReview':
    $controller = new ReviewC();
    $controller->AddReview();
  break;

  //acciones para el rol tecnico

  //acciones para solicitudes
  case 'asignarS':
    $controller = new SolicitudC();
    $controller->asignarS();
  break;

  case 'listarTL':
    $controller = new SolicitudC();
    $controller->ListarTL();
    include(__DIR__ . "/Views/Solicitudes/Tecnico/ListadoTL.php");
  break;

  case 'EditarSF':
    $controller = new SolicitudC();
    $controller->EditarSF();
  break;

  case 'PerfilTecnico':
    $controller = new UsuarioC();
    $controller->PerfilTecnico();
  break;

  //acciones para el rol admin

  case 'panelA':
    $PreviewUsuarios = new UsuarioC();
    $PreviewHistorial = new HistorialController();
    $usuarios = $PreviewUsuarios->PreviewU();
    $historial = $PreviewHistorial->PreviewH();
    include(__DIR__ . "/Views/Usuario/Admin/AdminP.php");
  break;

  case 'listarU':
    $controller = new UsuarioC();
    $controller->listarU();
  break;

  //acciones historial

  case 'mostrarHistorial':
    $controller = new HistorialController();
    $controller->mostrarHistorial();
  break;

  // acciones categoria

  case 'FormularioC':
    $controller = new CategoriaC();
    $controller->FormularioC();
    require_once(__DIR__ . "./Views/Usuario/Admin/Categoria/agregarC.php");
  break;

  case 'guardarC':
    $controller = new CategoriaC();
    $controller->guardarC();
  break;

  case 'listarC':
    $controller = new CategoriaC();
    $controller->listarC();
  break;

  case 'editarC':
    $controller = new CategoriaC();
    $controller->editarC();
  break;

  case 'actualizarC':
    $controller = new CategoriaC();
    $controller->actualizarC();
  break;

  case 'borrarC':
    $controller = new CategoriaC();
    $controller->borrarC();
  break;

  // case de Chat

  case 'mostrarChat':
    $controller = new ChatC();
    $controller->mostrarChat();
  break;

  case 'abrirChat':
    $controller = new ChatC();
    $controller->abrirChat();
  break;

  case 'enviarMensaje':
    $controller = new ChatC();
    $controller->enviar();
  break;

  case 'listarMensajes':
    $controller = new ChatC();
    $controller->listarMensajes();
  break;

  case 'mostrarConversacion':
    $controller = new ChatC();
    $controller->mostrarConversacion();
  break;

  case 'registroChats':
    $controller = new ChatC();
    $controller->registroChats();
  break;

  case 'listarConversaciones':
    $controller = new ChatC();
    $controller->listarConversaciones();
  break;

  case 'cargarMensajes':
    $controller = new ChatC();
    $controller->cargarMensajes();
  break;

  case 'borrarConversacion':
    $controller = new ChatC();
    $controller->borrar();
  break;

  case 'marcarNotificacionesLeidas':
    $controller = new NotificacionC();
    $controller->marcarTodasLeidas('urgente');  // Solo urgentes
    echo json_encode(['success' => true]);
  break;
  
  case 'contactanos':
    require_once(__DIR__ . "/Controllers/ContactoC.php");
    $controlador = new ContactoC();
    $controlador->mostrarFormulario();
  break;

  case 'enviarMensajeContacto':
    require_once(__DIR__ . "/Controllers/ContactoC.php");
    $controlador = new ContactoC();
    $controlador->enviarMensajeContacto();
  break;

  case 'confirmarEliminarU':
    $controller = new UsuarioC();
    $controller->confirmarEliminarU();
  break;

  case 'panelA':
    if ($_SESSION['rol'] == ROL_ADMIN) {
        include(__DIR__ . "/Views/Usuario/Admin/PanelA.php");
    } else {
        header("Location: Index.php?accion=redireccion");
    }
    break;
    
  default:
    if (!isset($_SESSION['usuario'])) {
      header("Location: Index.php?accion=inicio");
    } else {
      http_response_code(404);
      header("Location: Error.php");
    }
    exit();
}