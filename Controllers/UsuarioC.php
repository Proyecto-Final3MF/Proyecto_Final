<?php
require_once(__DIR__ . "/../Models/UsuarioM.php");
require_once(__DIR__ . "/../Views/include/popup.php");
require_once(__DIR__ . "/../Controllers/HistorialC.php");

class UsuarioC {
    private $historialController;
    private $reviewController;
    private $conn; // Propiedad para la conexión, necesaria para insert_id

    public function __construct() {
        $this->historialController = new HistorialController();
        $this->reviewController = new ReviewC();
        $this->conn = conectar();
    }

    public function login() {
        include(__DIR__ . "/../Views/Usuario/Login.php");
    }

    public function trabajo() {
        include(__DIR__ . "/../Views/Usuario/Tecnico/Trabajo.php");
    }

    public function TecnicoForm() {
        $usuario = new Usuario();
        $especializaciones = $usuario->obtenerEspecializaciones();
        include(__DIR__ . "/../Views/Usuario/Tecnico/TecnicoForm.php");
    }

    public function crear() {
        $usuario = new Usuario();
        $especializaciones = $usuario->obtenerEspecializaciones();
        include(__DIR__ . "/../Views/Usuario/Register.php");
    }

    public function guardarU() {
        $usuarioM = new Usuario();
        $usuario = trim($_POST['usuario']);
        $mail = trim($_POST['mail']);
        $rol_id = 2;
        $contrasena = $_POST['contrasena'];
        $confirm = $_POST['confirmar_contrasena'];

        if (!isset($_POST['terminos'])) {
        $_SESSION['mensaje'] = "Debes aceptar los términos y condiciones para registrarte.";
        $_SESSION['tipo_mensaje'] = "warning";
        header("Location: Index.php?accion=register");
        exit();
        }

        if (strlen($contrasena) < 8 || empty($contrasena) || $contrasena === '' || preg_match('/^\s*$/', $contrasena)) {
            $_SESSION['mensaje'] = "La contraseña debe tener al menos 8 caracteres.";
            $_SESSION['tipo_mensaje'] = "warning";
            header("Location: Index.php?accion=register");
            exit();
        }

        if ($contrasena !== $confirm) {
            $_SESSION['mensaje'] = "Las contraseñas no coinciden.";
            $_SESSION['tipo_mensaje'] = "warning";
            header("Location: Index.php?accion=register");
            exit();
        }

        $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

        // 2. Validaciones de Usuario y Email
        if (!preg_match('/^[\p{L}\s]+$/u', $usuario)) {
            $_SESSION['tipo_mensaje'] = "warning";
            $_SESSION['mensaje'] = "Caracteres inválidos en Nombre de Usuario. Solo se permiten letras y espacios.";
            header("Location: Index.php?accion=register");
            exit();
        }

        $existe = $usuarioM->obtenerPorEmail($mail);

        if ($existe) {
            $_SESSION['mensaje'] = "El correo electrónico ya está registrado.";
            $_SESSION['tipo_mensaje'] = "warning";

            header("Location: Index.php?accion=register");
            exit();
        }

        if (empty($usuario) || empty($mail)) {
            $_SESSION['tipo_mensaje'] = "warning";
            $_SESSION['mensaje'] = "El Nombre y Email de Usuario no pueden estar vacíos.";
            header("Location: Index.php?accion=register");
            exit();
        }

        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['tipo_mensaje'] = "warning";
            $_SESSION['mensaje'] = "El correo electrónico '$mail' es invalido";
            header("Location: Index.php?accion=register");
            exit();
        }

        // Nueva validación: El dominio debe ser ASCII (sin tildes, ñ, etc.)
        $domain = substr(strrchr($mail, "@"), 1);  // Extrae el dominio (ej: gmail.com)
        if (!preg_match('/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $domain)) {
        $_SESSION['tipo_mensaje'] = "warning";
        $_SESSION['mensaje'] = "El dominio del correo electrónico debe contener solo letras, números, puntos y guiones (sin tildes ni ñ).";
        header("Location: Index.php?accion=register");
        exit();
        }

        $success = $usuarioM->crearC($usuario, $mail, $rol_id, $contrasena_hash);

        if ($success) {
            $usuarioN = $usuarioM->obtenerPorEmail($mail);

            if ($usuarioN) {
                $_SESSION['id'] = $usuarioN['id'];
                $_SESSION['rol'] = 2;
                $_SESSION['email'] = $usuarioN['email'];
                $_SESSION['usuario'] = $usuarioN['nombre'];
                $this->historialController->registrarModificacion($_SESSION['usuario'], $_SESSION['id'], "Se registro", null, 0, "Usuario registrado como Cliente.");
                header("Location:Index.php?accion=redireccion");
            } else {
                header("Location: Index.php?accion=register");
                $_SESSION['mensaje'] = "Tu cuenta no pudo ser creada. Por favor, intenta de nuevo o revisa los datos.";
                $_SESSION['tipo_mensaje'] = "danger"; // Cambiado a 'danger' para un fallo de BD/inserción
                exit();
            }
        } else {
            header("Location: Index.php?accion=register");
            $_SESSION['mensaje'] = "Tu cuenta no pudo ser creada. Por favor, intenta de nuevo o revisa los datos.";
            $_SESSION['tipo_mensaje'] = "danger"; // Cambiado a 'danger' para un fallo de BD/inserción
            exit();
        }
    }

    public function guardarT() {
        $usuarioM = new Usuario();
        $usuario = trim($_POST['usuario']);
        $mail = trim($_POST['mail']);
        $rol_id = 1;
        $contrasena = $_POST['contrasena'];
        $especializaciones = $_POST['especializaciones'] ?? [];
        $otra_especialidad = trim($_POST['otra_especialidad']) ?: null;

         if (!isset($_POST['terminos'])) {
        $_SESSION['mensaje'] = "Debes aceptar los términos y condiciones para registrarte.";
        $_SESSION['tipo_mensaje'] = "warning";
        header("Location: Index.php?accion=TecnicoForm");
        exit();
        }

        if (strlen($contrasena) < 8 || empty($contrasena) || $contrasena === '' || preg_match('/^\s*$/', $contrasena)) {
            $_SESSION['mensaje'] = "La contraseña debe tener al menos 8 caracteres.";
            $_SESSION['tipo_mensaje'] = "warning";
            header("Location: Index.php?accion=TecnicoForm");
            exit();
        }

        $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

        // 2. Validaciones de Usuario y Email
        if (!preg_match('/^[\p{L}\s]+$/u', $usuario)) {
            $_SESSION['tipo_mensaje'] = "warning";
            $_SESSION['mensaje'] = "Caracteres inválidos en Nombre de Usuario. Solo se permiten letras y espacios.";
            header("Location: Index.php?accion=TecnicoForm");
            exit();
        }

        $existe = $usuarioM->obtenerPorEmail($mail);

        if ($existe) {
            $_SESSION['mensaje'] = "El correo electrónico ya está registrado.";
            $_SESSION['tipo_mensaje'] = "warning";

            header("Location: Index.php?accion=TecnicoForm");
            exit();
        }

        if (empty($usuario) || empty($mail)) {
            $_SESSION['tipo_mensaje'] = "warning";
            $_SESSION['mensaje'] = "El Nombre y Email de Usuario no pueden estar vacíos.";
            header("Location: Index.php?accion=TecnicoForm");
            exit();
        }

        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['tipo_mensaje'] = "warning";
            $_SESSION['mensaje'] = "El correo electrónico '$mail' es invalido";
            header("Location: Index.php?accion=TecnicoForm");
            exit();
        }

        // Nueva validación: El dominio debe ser ASCII (sin tildes, ñ, etc.)
        $domain = substr(strrchr($mail, "@"), 1);  // Extrae el dominio (ej: gmail.com)
        if (!preg_match('/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $domain)) {
        $_SESSION['tipo_mensaje'] = "warning";
        $_SESSION['mensaje'] = "El dominio del correo electrónico debe contener solo letras, números, puntos y guiones (sin tildes ni ñ).";
        header("Location: Index.php?accion=TecnicoForm"); 
        exit();
        }

        if (empty($especializaciones) && empty($otra_especialidad)) {
            $_SESSION['tipo_mensaje'] = "warning";
            $_SESSION['mensaje'] = "Debe seleccionar al menos una especialización o especificar 'Otra Especialidad'.";
            header("Location: Index.php?accion=TecnicoForm");
            exit();
        }

        $success = $usuarioM->crearT($usuario, $mail, $rol_id, $contrasena_hash, $otra_especialidad);

        if ($success) {
            $usuarioN = $usuarioM->obtenerPorEmail($mail);

            if ($usuarioN) {
                $guardarEsp = $usuarioM->guardarEspecializaciones($usuarioN['id'], $especializaciones);
                if (!$guardarEsp) {
                    $_SESSION['tipo_mensaje'] = "error";
                    $_SESSION['mensaje'] = "Error: tuvimos problemas al guardar tus especializaciones, intente mas tader.";
                }

                $_SESSION['id'] = $usuarioN['id'];
                $_SESSION['rol'] = 1;
                $_SESSION['email'] = $usuarioN['email'];
                $_SESSION['usuario'] = $usuarioN['nombre'];
                $this->historialController->registrarModificacion($_SESSION['usuario'], $_SESSION['id'], "Se registro", null, 0, "Usuario registrado como Tecnico.");
                header("Location:Index.php?accion=redireccion");
            } else {
                header("Location: Index.php?accion=TecnicoForm");
                $_SESSION['mensaje'] = "Tu cuenta no pudo ser creada. Por favor, intenta de nuevo o revisa los datos.";
                $_SESSION['tipo_mensaje'] = "danger"; // Cambiado a 'danger' para un fallo de BD/inserción
                exit();
            }
        } else {
            header("Location: Index.php?accion=TecnicoForm");
            $_SESSION['mensaje'] = "Tu cuenta no pudo ser creada. Por favor, intenta de nuevo o revisa los datos.";
            $_SESSION['tipo_mensaje'] = "danger"; // Cambiado a 'danger' para un fallo de BD/inserción
            exit();
        }
    }

    public function actualizarU() {
        $usuarioM = new Usuario();

        $id = $_POST['id'];
        $nombre = trim($_POST['nombre']);
        $email = trim($_POST['email']);
        $foto_actual = $_POST['foto_actual'] ?? "Assets/imagenes/perfil/fotodefault.webp";

        if ($_SESSION['rol'] != ROL_ADMIN) {
        // Si NO es admin, solo puede editar su propio perfil
            if ($_SESSION['id'] != $id) {
                $_SESSION['tipo_mensaje'] = "warning";
                $_SESSION['mensaje'] = "Acceso denegado: no puedes editar el perfil de otro usuario.";
                header("Location: Index.php?accion=redireccion");
                exit();
            }
        }

        $checkEmail = $usuarioM->checkEmail($email, $id);
        if ($checkEmail) {
            $_SESSION['tipo_mensaje'] = "warning";
            $_SESSION['mensaje'] = "Esse email ya esta en uso.";
            header("Location: Index.php?accion=redireccion");
            exit();
        }

        if (!preg_match('/^[\p{L}\s]+$/u', $nombre)) {
            $_SESSION['tipo_mensaje'] = "warning";
            $_SESSION['mensaje'] = "Caracteres inválidos en el nombre. Solo se permiten letras y espacios.";
            header("Location: Index.php?accion=editarU&id=$id");
            exit();
        }

        if (empty($nombre)) {
            $_SESSION['tipo_mensaje'] = "warning";
            $_SESSION['mensaje'] = "El nombre no puede estar vacío.";
            header("Location: Index.php?accion=editarU&id=$id");
            exit();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['tipo_mensaje'] = "warning";
            $_SESSION['mensaje'] = "El correo electrónico '$email' es inválido.";
            header("Location: Index.php?accion=editarU&id=$id");
            exit();
        }

        // Nueva validación: El dominio debe ser ASCII (sin tildes, ñ, etc.)
        $domain = substr(strrchr($email, "@"), 1);  // Extrae el dominio (ej: gmail.com)
        if (!preg_match('/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $domain)) {
            $_SESSION['tipo_mensaje'] = "warning";
            $_SESSION['mensaje'] = "El dominio del correo electrónico debe contener solo letras, números, puntos y guiones (sin tildes ni ñ).";
            header("Location: Index.php?accion=editarU&id=$id");
            exit();
        }

        $nombreAntiguo = $_SESSION['usuario'] ?? 'Nombre Desconocido';
        $emailAntiguo = $_SESSION['email'] ?? 'Email Desconocido';

        // En UsuarioC::actualizarU(), justo después de las validaciones iniciales:
        $foto_perfil = $foto_actual;  // Mantener la actual por defecto
        if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
            // Ruta absoluta al directorio (ajusta si tu estructura es diferente)
            $directorio = __DIR__ . "/../Assets/imagenes/perfil/";
            if (!is_dir($directorio)) {
                mkdir($directorio, 0755, true);  // Crear directorio si no existe
            }
        
            // Sanitizar el nombre del archivo (evitar caracteres problemáticos)
            $nombre_original = basename($_FILES['foto_perfil']['name']);
            $extension = pathinfo($nombre_original, PATHINFO_EXTENSION);
            $nombre_seguro = preg_replace('/[^a-zA-Z0-9._-]/', '', $nombre_original);  // Remover caracteres no seguros
            $nombre_archivo = uniqid() . "_" . $nombre_seguro;
            $ruta_completa = $directorio . $nombre_archivo;
            
            // Ruta relativa para guardar en BD (como antes)
            $foto_perfil = "Assets/imagenes/perfil/" . $nombre_archivo;
            
            if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $ruta_completa)) {
                // Éxito: eliminar la foto anterior si no es la default
                if ($foto_actual !== "Assets/imagenes/perfil/fotodefault.webp" && file_exists($directorio . basename($foto_actual))) {
                    unlink($directorio . basename($foto_actual));
                }
            } else {
                // Error: no actualizar la foto, mostrar mensaje y mantener la actual
                $_SESSION['tipo_mensaje'] = "danger";
                $_SESSION['mensaje'] = "Error al subir la foto de perfil. Verifica el tamaño del archivo o permisos del servidor.";
                $foto_perfil = $foto_actual;
            }
        }

        if ($usuarioM->editarU($id, $nombre, $email, $foto_perfil)) {
            if ($id == $_SESSION['id']) {
                $_SESSION['usuario'] = $nombre;
                $_SESSION['email'] = $email;
                $_SESSION['foto_perfil'] = $foto_perfil;
            }

            if ($nombreAntiguo == $nombre && $emailAntiguo == $email) {
                $obs = "Ningún cambio detectado";
            } else {
                $obs = "";
                if ($nombreAntiguo !== $nombre) {
                    $obs .= "Nombre: $nombreAntiguo ⟶ $nombre. ‎ ";
                }
                if ($emailAntiguo !== $email) {
                    $obs .= "Email: $emailAntiguo ⟶ $email.";
                }
            }

            $this->historialController->registrarModificacion($nombre, $id, 'fue actualizado', null, 0, $obs);

            $_SESSION['tipo_mensaje'] = "success";
            if ($_SESSION['rol'] == ROL_ADMIN && $_SESSION['id'] != $id) {
            
                $_SESSION['mensaje'] = "Actualizaste el perfil con éxito.";
                header("Location: Index.php?accion=listarU");
            } else {
                $_SESSION['mensaje'] = "Actualizaste tu perfil con éxito.";
                header("Location: Index.php?accion=redireccion");
            }
            exit();
        } else {
            $_SESSION['tipo_mensaje'] = "danger";
            $_SESSION['mensaje'] = "Error al actualizar el usuario.";
            header("Location: Index.php?accion=editarU&id=$id");
            exit();
        }
    }

    public function borrar() {
        $usuarioM = new Usuario();
        $id = $_GET["id"];

        // Verificar si es auto-eliminación o eliminación por admin
        $es_auto_eliminacion = ($_SESSION['id'] == $id);

        if (!$es_auto_eliminacion && $_SESSION['rol'] !== ROL_ADMIN) {
            $_SESSION['tipo_mensaje'] = "danger";
            $_SESSION['mensaje'] = "Acceso denegado. Solo administradores pueden eliminar cuentas de otros usuarios.";
            header("Location: Index.php?accion=redireccion");
            exit();
        }

        // Para auto-eliminación, verificar que no sea admin
        if ($es_auto_eliminacion && $_SESSION['rol'] == ROL_ADMIN) {
            $_SESSION['tipo_mensaje'] = "warning";
            $_SESSION['mensaje'] = "Como administrador, no puedes eliminar tu propia cuenta desde aquí. Contacta a otro admin.";
            header("Location: Index.php?accion=redireccion");
            exit();
        }

        // Obtener datos del usuario ANTES de intentar eliminar (para historial)
        $usuarioBorrado = $usuarioM->buscarUsuarioId($id);
        if (!$usuarioBorrado) {
            $_SESSION['tipo_mensaje'] = "danger";
            $_SESSION['mensaje'] = "Usuario no encontrado.";
            $redirect = $es_auto_eliminacion ? "Index.php?accion=redireccion" : "Index.php?accion=listarU";
            header("Location: $redirect");
            exit();
        }
        $nombre = $usuarioBorrado['nombre'];

        // Intentar eliminar
        $success = $usuarioM->borrarU($id);

        if ($success) {
            $this->historialController->registrarModificacion($nombre, $id, 'fue eliminado', null, 0, $es_auto_eliminacion ? 'Auto-eliminación por el usuario.' : 'Eliminado por administrador.');

            if ($es_auto_eliminacion) {
                session_unset();
                session_destroy();
                $_SESSION['tipo_mensaje'] = "success";
                $_SESSION['mensaje'] = "Tu cuenta ha sido eliminada exitosamente.";
                header("Location: Index.php?accion=inicio");
                exit();
            } else {
                $_SESSION['tipo_mensaje'] = "success";
                $_SESSION['mensaje'] = "Usuario eliminado exitosamente.";
                header("Location: Index.php?accion=listarU");
                exit();
            }
        } else {
            $dependencias = $usuarioM->verificarDependencias($id);
            if (!$dependencias['puede_eliminar']) {
                $_SESSION['tipo_mensaje'] = "warning";
                $_SESSION['mensaje'] = $dependencias['mensaje'];
            } else {
                $_SESSION['tipo_mensaje'] = "danger";
                $_SESSION['mensaje'] = "Error al eliminar el usuario. Inténtalo de nuevo.";
            }

            $redirect = $es_auto_eliminacion ? "Index.php?accion=redireccion" : "Index.php?accion=listarU";
            header("Location: $redirect");
            exit();
        }
    }

    // Nuevo método para mostrar confirmación de eliminación
    public function confirmarEliminarU() {
        $id = $_GET['id'] ?? null;
        if (!$id || !is_numeric($id)) {
            $_SESSION['tipo_mensaje'] = "danger";
            $_SESSION['mensaje'] = "ID de usuario inválido.";
            header("Location: Index.php?accion=redireccion");
            exit();
        }

        $es_auto_eliminacion = ($_SESSION['id'] == $id);
        if (!$es_auto_eliminacion && $_SESSION['rol'] != ROL_ADMIN) {
            $_SESSION['tipo_mensaje'] = "danger";
            $_SESSION['mensaje'] = "Acceso denegado.";
            header("Location: Index.php?accion=redireccion");
            exit();
        }

        $usuarioM = new Usuario();
        $usuario = $usuarioM->buscarUsuarioId($id);
        if (!$usuario) {
            $_SESSION['tipo_mensaje'] = "danger";
            $_SESSION['mensaje'] = "Usuario no encontrado.";
            header("Location: Index.php?accion=redireccion");
            exit();
        }

        // Verificar dependencias para mostrar advertencias
        $dependencias = $usuarioM->verificarDependencias($id);
        include(__DIR__ . "/../Views/Usuario/ConfirmarEliminarU.php"); // Nueva vista
    }

    public function editarU($id = null) {
        $usuarioM = new Usuario();
        $id = $id ?? $_GET['id'];
        $datos = $usuarioM->buscarUsuarioId($id);
        include(__DIR__ . "/../Views/Usuario/EditarU.php");
    }

    public function autenticar() {
        $email = trim($_POST['usuario']);
        $contrasena = $_POST['contrasena'];
        $modelo = new Usuario();

        $user = $modelo->obtenerPorEmail($email);
        $ROL_TECNICO_ID = 1;

        if ($user && password_verify($contrasena, $user['contrasena'])) {

            if ($user['rol_id'] == $ROL_TECNICO_ID) {
                switch ($user['estado_verificacion']) {
                    case 'pendiente':
                        $_SESSION['tipo_mensaje'] = "warning";
                        $_SESSION['mensaje'] = "Tu cuenta de técnico está pendiente de verificación.";
                        header("Location: Index.php?accion=espera&email=" . urlencode($user['email']));
                        exit();
                    case 'rechazado':
                        $_SESSION['tipo_mensaje'] = "danger";
                        $_SESSION['mensaje'] = "Tu solicitud como técnico fue rechazada. Contacta al administrador.";
                        header("Location: Index.php?accion=login");
                        exit();
                    case 'aprobado':
                        break;
                }
            }

            session_start();
            $_SESSION['usuario'] = $user['nombre'];
            $_SESSION['rol'] = $user['rol_id'];
            $_SESSION['id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['foto_perfil'] = $user['foto_perfil'] ?? "Assets/imagenes/perfil/fotodefault.webp";

            header("Location: Index.php?accion=redireccion");
            exit();
        } else {
            $error = "Correo o contraseña incorrectos";
            include(__DIR__ . "/../Views/Usuario/Login.php");
        }
    }

    public function listarU() {
        $orden = $_GET['orden'] ?? '';
        $rol_filter = $_GET['rol_filter'] ?? 'Todos';
        $search = $_GET['search'] ?? '';

        $usuario = new Usuario();
        $resultados = $usuario->listarU($orden, $rol_filter, $search);
        include(__DIR__ . "/../Views/Usuario/Admin/listarU.php");
    }

    public function PreviewU() {
        $usuario = new Usuario();
        return $usuario->PreviewU();
    }

    public function PerfilTecnico() {
        $Tecnico = new Usuario();
        $Reviews = new Review();
        $id_tecnico = $_GET['id'] ?? null;

        if (!$id_tecnico || !is_numeric($id_tecnico)) {
            // Redirige a una acción inexistente para activar el default
            header("Location:Index.php?accion=notfound");
            exit();
        }

        $DatosTecnico = $Tecnico->buscarUsuarioId($id_tecnico);

        if (!$DatosTecnico) {
            // Redirige a una acción inexistente para activar el default
            header("Location:Index.php?accion=notfound");
            exit();
        }

        $especializaciones = $Tecnico->getEspecializacion($id_tecnico);
        $ReviewsTecnico = $Reviews->listarReviewsTecnico($id_tecnico);
        include(__DIR__ . "/../Views/Usuario/Tecnico/Perfil.php");
    }

    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        header("Location: Index.php");
        exit();
    }
}
