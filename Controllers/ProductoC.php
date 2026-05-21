<?php
require_once(__DIR__ . "/../Config/conexion.php");
require_once(__DIR__ . "/../Models/ProductoM.php");
require_once(__DIR__ . "/../Controllers/HistorialC.php");
require_once(__DIR__ . "/../Views/include/popup.php");

class ProductoC {

    private $historialController;

    public function __construct() {
        $this->historialController = new HistorialController();
    }

    public function formularioP() {
        $producto = new Producto();
        $categorias = $producto->obtenerCategorias();
        include(__DIR__ . "/../Views/Producto/FormularioP.php");
    }

    public function guardarP() {
        $producto = new Producto();
        $nombre = trim($_POST['nombre']) ?? '';
        $categoria_id = $_POST['categoria'] ?? '';
        $id_usuario = $_SESSION['id'];
        $usuarioNombre = $_SESSION['usuario'] ?? 'Desconocido';

        if (empty($nombre) || empty($categoria_id) || empty($_FILES['imagen']['name']) || $nombre === '') {
            $_SESSION['tipo_mensaje'] = "warning";
            $_SESSION['mensaje'] = "Error: Todos los campos son obligatorios.";
            header("Location: Index.php?accion=formularioP");
            exit();
        }

        if ($producto->existeProducto($nombre, $id_usuario)) {
            $_SESSION['tipo_mensaje'] = "warning";
            $_SESSION['mensaje'] = "Error: Ya has creado un producto con ese nombre.";
            header("Location: Index.php?accion=formularioP");
            exit();
        }

        if ($categoria_id === null || $categoria_id === '') {
            $_SESSION['tipo_mensaje'] = "warning";
            $_SESSION['mensaje'] = "Error: Producto sin categoria.";
            header("Location: Index.php?accion=formularioP");
            exit();
        }

        $nombreArchivo = $_FILES['imagen']['name'];
        $rutaTemporal = $_FILES['imagen']['tmp_name'];

        $tipoArchivo = mime_content_type($rutaTemporal);
        $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (!in_array($tipoArchivo, $tiposPermitidos)) {
            $_SESSION['tipo_mensaje'] = "warning";
            $_SESSION['mensaje'] = "Error: Solo se permiten archivos de imagen (JPG, PNG, GIF o WEBP).";
            header("Location: Index.php?accion=formularioP");
            exit();
        }

        $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
        $nombreArchivoSeguro = uniqid('producto_', true) . '.' . $extension;

        $rutaFinal = "Image/" . $nombreArchivoSeguro;

        if (move_uploaded_file($rutaTemporal, $rutaFinal)) {
            $id = $producto->crearP($nombre, $rutaFinal, $categoria_id, $id_usuario);

            if ($id) {
                $_SESSION['tipo_mensaje'] = "success";
                $_SESSION['mensaje'] = "Producto creado exitosamente.";
                $_SESSION['tipo_mensaje'] = "success";

                $this->historialController->registrarModificacion(
                    $usuarioNombre,
                    $id_usuario,
                    'guardó el producto',
                    $nombre,
                    $id,
                    null
                );
                header("Location: Index.php?accion=formularioP");
                exit();
            } else {
                $_SESSION['mensaje'] = "Error al crear el producto.";
                $_SESSION['tipo_mensaje'] = "error";
            }
        } else {
            $_SESSION['mensaje'] = "Error al subir la imagen.";
            $_SESSION['tipo_mensaje'] = "error";
        }
    }

    public function listarP() {
        $id_usuario = $_SESSION['id'] ?? null;
        if ($id_usuario === null) {
            header("Location: Index.php?accion=login");
            exit();
        }

        $producto = new Producto();
        $orden = $_GET['orden'] ?? 'Más Recientes';
        $search = $_GET['search'] ?? null;
        $resultados = $producto->listarP($id_usuario, $orden, $search);
        include(__DIR__ . "/../Views/Producto/ListadoP.php");
    }

    public function borrarP() {
        $producto = new Producto();
        $id = $_GET['id'];
        $id_usuario = $_SESSION['id'];
        $usuarioNombre = $_SESSION['usuario'] ?? 'Desconocido';

        $producto->borrarP($id);
        $_SESSION['tipo_mensaje'] = "success";
        $_SESSION['mensaje'] = "Producto eliminado exitosamente.";
        $_SESSION['tipo_mensaje'] = "success";

        $obs = "Producto eliminado";
        $this->historialController->registrarModificacion(
            $usuarioNombre,
            $id_usuario,
            'eliminó el producto',
            $nombre ?? '',
            $id,
            $obs
        );

        header("Location: Index.php?accion=listarP");
    }

    public function editarP() {
        $producto = new Producto();
        $id = $_GET['id'] ?? null;

        if (!$id) {
            echo "ID de producto no especificado.";
            return;
        }

        $datosProducto = $producto->obtenerProductoPorId($id);
        if (!$datosProducto) {
            $_SESSION['tipo_mensaje'] = "warning";
            $_SESSION['mensaje'] = "Producto no encontrado.";
            return;
        }

        $categorias = $producto->obtenerCategorias();
        include(__DIR__ . "/../Views/Producto/EditarP.php");
    }

    public function actualizarP() {
        $producto = new Producto();
        $id = $_POST['id'] ?? null;
        $nombre = trim($_POST['nombre']) ?? '';
        $categoria_id = $_POST['categoria'] ?? '';
        $imagenActual = $_POST['imagen_actual'] ?? '';
        $id_usuario = $_SESSION['id'];
        $usuarioNombre = $_SESSION['usuario'] ?? 'Desconocido';

        $checkP = $producto->checkProducto($id, $id_usuario);
        if (!$checkP) {
            $_SESSION['tipo_mensaje'] = "warning";
            $_SESSION['mensaje'] = "ACCESO Negado.";
            header("Location: Index.php?accion=listarP");
            exit();
        }

        if (!$id || empty($nombre) || empty($categoria_id) || $nombre === '') {
            $_SESSION['tipo_mensaje'] = "warning";
            $_SESSION['mensaje'] = "Error: Todos los campos son obligatorios.";
            header("Location: Index.php?accion=editarP&id=" . $id);
            exit();
        }

        if ($categoria_id === null || $categoria_id === '') {
            $_SESSION['tipo_mensaje'] = "warning";
            $_SESSION['mensaje'] = "Error: Producto sin categoria.";
            header("Location: Index.php?accion=editarP&id=" . $id);
            exit();
        }

        if (!empty($_FILES['imagen']['name'])) {
            $nombreArchivo = $_FILES['imagen']['name'];
            $rutaTemporal = $_FILES['imagen']['tmp_name'];

            $tipoArchivo = mime_content_type($rutaTemporal);
            $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

            if (!in_array($tipoArchivo, $tiposPermitidos)) {
                $_SESSION['tipo_mensaje'] = "warning";
                $_SESSION['mensaje'] = "Error: Solo se permiten archivos de imagen (JPG, PNG, GIF o WEBP).";
                header("Location: Index.php?accion=editarP&id=" . $id);
                exit();
            }

            $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
            $nombreArchivoSeguro = uniqid('producto_', true) . '.' . $extension;

            $rutaFinal = __DIR__ ."/../Image/" . $nombreArchivoSeguro;

            if (!move_uploaded_file($rutaTemporal, $rutaFinal)) {
                $_SESSION['tipo_mensaje'] = "warning";
                $_SESSION['mensaje'] = "Error al subir la imagen.";
                header("Location: Index.php?accion=editarP&id=" . $id);
                exit();
            }
        } else {
            $rutaFinal = $imagenActual;
        }

        $productoModel = new Producto();
        $ProductoAntiguo = $productoModel->obtenerProductoPorId($id);
        $nombreAntiguo = $ProductoAntiguo['nombre'] ?? 'Nombre desconocido';
        $id_catAntiguo = $ProductoAntiguo['id_cat'] ?? 'Id desconocido';
        $categoriaAntigua = $ProductoAntiguo['categoria'] ?? 'Categoria desconocida';


        $categoria_nombre = $productoModel->obtenerCategoriaporId($categoria_id);
        $nuevaCat = $categoria_nombre ?? "categoria desconocida";

        if ($producto->actualizarProducto($id, $nombre, $rutaFinal, $categoria_id)) {
            $_SESSION['tipo_mensaje'] = "success";
            $_SESSION['mensaje'] = "Producto actualizado exitosamente.";

            if ($nombre == $nombreAntiguo && $id_catAntiguo == $categoria_id) {
                $obs = "Ningun cambio detectado";
            } else {
                $obs = "";
                if ($nombre !== $nombreAntiguo) {
                    $obs1 = "Nombre: " . $nombreAntiguo . " ⟶ " . $nombre . " ‎ ";
                    $obs .= $obs1;
                }

                if ($id_catAntiguo !== $categoria_id) {
                    $obs2 = "Categoria: " . $categoriaAntigua . " ⟶ " . $nuevaCat;
                    $obs .= $obs2;
                }
            }

            $this->historialController->registrarModificacion(
                $usuarioNombre,
                $id_usuario,
                'actualizó el producto',
                $nombre,
                $id,
                $obs
            );

            header("Location: Index.php?accion=listarP");
            exit();
        } else {
            $_SESSION['mensaje'] = "Error al actualizar el producto.";
            $_SESSION['tipo_mensaje'] = "error";
            header("Location: Index.php?accion=editarP&id=" . $id);
            exit();
        }
    }

    public function urgentePF() {
        $producto = new Producto();
        $categorias = $producto->obtenerCategorias();
        include(__DIR__ . "/../Views/Producto/FormularioUP.php");
    }

    public function urgenteGP() {
        $producto = new Producto();
        $nombre = $_POST['nombre'] ?? '';
        $categoria_id = $_POST['categoria'] ?? '';
        $id_usuario = $_SESSION['id'];
        $usuarioNombre = $_SESSION['usuario'] ?? 'Desconocido';

        if (empty($nombre) || empty($categoria_id) || empty($_FILES['imagen']['name'])) {
            $_SESSION['tipo_mensaje'] = "warning";
            $_SESSION['mensaje'] = "Error: Todos los campos son obligatorios.";
            header("Location: Index.php?accion=urgenteP");
            exit();
        }

        if ($producto->existeProducto($nombre, $id_usuario)) {
            $_SESSION['tipo_mensaje'] = "warning";
            $_SESSION['mensaje'] = "Error: Ya has creado un producto con ese nombre.";
            header("Location: Index.php?accion=urgenteP");
            exit();
        }

        $nombreArchivo = $_FILES['imagen']['name'];
        $rutaTemporal = $_FILES['imagen']['tmp_name'];

        $tipoArchivo = mime_content_type($rutaTemporal);
        $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (!in_array($tipoArchivo, $tiposPermitidos)) {
            $_SESSION['tipo_mensaje'] = "warning";
            $_SESSION['mensaje'] = "Error: Solo se permiten archivos de imagen (JPG, PNG, GIF o WEBP).";
            header("Location: Index.php?accion=urgenteP");
            exit();
        }

        $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
        $nombreArchivoSeguro = uniqid('producto_', true) . '.' . $extension;

        $rutaFinal = "Image/" . $nombreArchivoSeguro;

        if (move_uploaded_file($rutaTemporal, $rutaFinal)) {
            $id = $producto->crearP($nombre, $rutaFinal, $categoria_id, $id_usuario);

            if ($id) {
                $this->historialController->registrarModificacion(
                    $usuarioNombre,
                    $id_usuario,
                    'guardó el producto de manera urgente',
                    $nombre,
                    $id,
                    null
                );
                header("Location: Index.php?accion=urgenteS");
                exit();
            } else {
                $_SESSION['mensaje'] = "Error al crear el producto.";
                $_SESSION['tipo_mensaje'] = "error";
                 header("Location: Index.php?accion=urgenteP");
            }
        } else {
            $_SESSION['mensaje'] = "Error al subir la imagen.";
            $_SESSION['tipo_mensaje'] = "error";
             header("Location: Index.php?accion=urgenteP");
        }
    }
}