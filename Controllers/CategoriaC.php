<?php
require_once(__DIR__ . "/../Config/conexion.php");
require_once(__DIR__ . "/../Models/CategoriaM.php");
require_once(__DIR__ . "/../Views/include/popup.php");
require_once(__DIR__ . "/../Controllers/HistorialC.php");

class CategoriaC {

    private $historialController;

    public function __construct() {
        $this->historialController = new HistorialController();
    }

    public function FormularioC() {
        $categoria = new Categoria();
        include(__DIR__ . "/../Views/Usuario/Admin/Categoria/agregarC.php");
    }

    public function guardarC() {
        $categoria = new Categoria();
        $nombre = trim($_POST['nombre']) ?? '';

        $usuarioNombre = $_SESSION['usuario'] ?? 'Desconocido';
        $usuarioId = $_SESSION['id'] ?? 0;

        if (empty($nombre) || $nombre === '') {
            $_SESSION['tipo_mensaje'] = "warning";
            $_SESSION['mensaje'] = "La categoría no puede tener un nombre vacío.";
            header("Location: Index.php?accion=FormularioC");
            exit();
        }

        if ($categoria->verificarExistencia($nombre)) {
            $_SESSION['tipo_mensaje'] = "warning";
            $_SESSION['mensaje'] = "La categoría '{$nombre}' ya existe.";
        } else {
            $id = $categoria->guardarC($nombre);
            if ($id !== false) {
                $_SESSION['tipo_mensaje'] = "success";
                $_SESSION['mensaje'] = "Categoría '{$nombre}' fue guardada.";
                $obs = "Categoría creada";
                $this->historialController->registrarModificacion(
                    $usuarioNombre,
                    $usuarioId,
                    'guardó la categoría',
                    $nombre,
                    $id,
                    $obs
                );
            } else {
                $_SESSION['tipo_mensaje'] = "warning";
                $_SESSION['mensaje'] = "Error al guardar la categoría.";
            }
        }

        header("Location: Index.php?accion=FormularioC");
        exit();
    }

    public function listarC() {
        $categoria = new Categoria();

        $orden = $_GET['orden'] ?? 'Más Antiguas';

        $search = $_GET['search'] ?? '';

        $resultados = $categoria->listarC($orden, $search);

        include(__DIR__ . "/../Views/Usuario/Admin/Categoria/listarC.php");
    }

    public function editarC() {
        $categoria_modelo = new Categoria();
        $id = $_GET['id'] ?? 0;
        if ($id <= 0) {
            $_SESSION['tipo_mensaje'] = "warning";
            $_SESSION['mensaje'] = "ID de categoría no válido.";
            header("Location: Index.php?accion=listarC");
            exit();
        }

        $categoria = $categoria_modelo->buscarPorId($id);
        if (!$categoria) {
            $_SESSION['tipo_mensaje'] = "warning";
            $_SESSION['mensaje'] = "Categoría no encontrada.";
            header("Location: Index.php?accion=listarC");
            exit();
        }

        include(__DIR__ . "/../Views/Usuario/Admin/Categoria/editarC.php");
    }

    public function actualizarC() {
        $usuarioNombre = $_SESSION['usuario'] ?? 'Desconocido';
        $usuarioId = $_SESSION['id'] ?? 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
            $nuevoNombre = trim($_POST['nombre']) ?? '';

            if ($id > 0 && !empty($nuevoNombre) && $nuevoNombre !== '') {
                $categoria_modelo = new Categoria();
                $categoriaAntigua = $categoria_modelo->buscarPorId($id);
                $nombreAntiguo = $categoriaAntigua['nombre'] ?? 'Nombre desconocido';

                if ($categoria_modelo->actualizarC($id, $nuevoNombre)) {
                    $_SESSION['tipo_mensaje'] = "success";
                    $_SESSION['mensaje'] = "Categoría '{$nombreAntiguo}' fue cambiada a '{$nuevoNombre}'.";
                    $obs = "La categoría '{$nombreAntiguo}' fue renombrada a '{$nuevoNombre}'";
                    $this->historialController->registrarModificacion(
                        $usuarioNombre,
                        $usuarioId,
                        'renombró la categoría',
                        $nuevoNombre,
                        $id,
                        $obs
                    );
                } else {
                    $_SESSION['tipo_mensaje'] = "error";
                    $_SESSION['mensaje'] = "Error al actualizar la categoría.";
                }
            } else {
                $_SESSION['tipo_mensaje'] = "error";
                $_SESSION['mensaje'] = "Error: Datos no válidos para la actualización.";
            }

            header("Location: Index.php?accion=listarC");
            exit();
        } else {
            header("Location: Index.php?accion=listarC");
            exit();
        }
    }

    public function borrarC() {
        $usuarioNombre = $_SESSION['usuario'] ?? 'Desconocido';
        $usuarioId = $_SESSION['id'] ?? 0;

        $id = $_GET['id'] ?? 0;
        if ($id <= 0) {
            $_SESSION['tipo_mensaje'] = "error";
            $_SESSION['mensaje'] = "ID de categoría no válido.";
            header("Location: Index.php?accion=listarC");
            exit();
        }

        $categoria_modelo = new Categoria();
        $categoria = $categoria_modelo->buscarPorId($id);

        if (!$categoria) {
            $_SESSION['tipo_mensaje'] = "error";
            $_SESSION['mensaje'] = "Categoría no encontrada.";
            header("Location: Index.php?accion=listarC");
            exit();
        }

        $nombre = $categoria['nombre'] ?? 'Nombre desconocido';

        if ($categoria_modelo->borrarC($id)) {
            $_SESSION['tipo_mensaje'] = "success";
            $_SESSION['mensaje'] = "Categoría '{$nombre}' eliminada exitosamente.";
            $obs = "La categoría '{$nombre}' fue eliminada";
            $this->historialController->registrarModificacion(
                $usuarioNombre,
                $usuarioId,
                'eliminó la categoría',
                $nombre,
                $id,
                $obs
            );
        } else {
            $_SESSION['tipo_mensaje'] = "error";
            $_SESSION['mensaje'] = "Error: No se pudo eliminar la categoría.";
        }

        header("Location: Index.php?accion=listarC");
        exit();
    }
}