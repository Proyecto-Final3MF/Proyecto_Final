<?php
require_once(__DIR__ . '/../Config/conexion.php');

class Producto {
    private $conn;

    public function __construct() {
        $this->conn = conectar();
    }

    public function obtenerCategorias() {
        $sql = "SELECT * FROM categoria";
        $resultado = $this->conn->query($sql);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    public function obtenerCategoriaporId($categoria_id) {
        $sql = "SELECT nombre FROM categoria WHERE id = ? LIMIT 1";

        $stmt = $this->conn->prepare($sql);

        if ($stmt === false) {
            return null; // O manejar el error
        }
        $stmt->bind_param("i", $categoria_id);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result && $row = $result->fetch_assoc()) {
            $stmt->close();
            return $row['nombre'];
        }
        $stmt->close();
        return null;
    }

    public function checkProducto($id, $id_usuario) {
        $sql = "SELECT* FROM producto WHERE id = ? AND id_usuario = ?";
        $stmt = $this->conn->prepare($sql);

        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param("ii", $id, $id_usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    public function listarP($id_usuario, $orden, $search) {
        $id_usuario = (int)$id_usuario;
        $sql = "SELECT p.*, c.nombre AS categoria_nombre FROM producto p INNER JOIN categoria c ON p.id_cat = c.id WHERE p.id_usuario = ? ";

        $params = [$id_usuario];
        $param_types = 'i';
        if (!empty($search)) {
            $sql .= "AND ";
            $search_terms = explode(" ", $search);
            $conditions = [];

            foreach ($search_terms as $palabra) {
                $conditions[] = "(p.nombre LIKE ? OR c.nombre LIKE ?)";
                $search_term = "%" . $palabra . "%";
                $params[] = $search_term;
                $params[] = $search_term;
                $param_types .= 'ss';
            }
            $sql .= implode(" AND ", $conditions);
        }
        
        switch ($orden) {
            case "A-Z":
                $sql .= "ORDER BY p.nombre ASC";
                break;
            case "Z-A":
                $sql .= "ORDER BY p.nombre DESC";
                break;
            case "Más Recientes":
                $sql .= "ORDER BY p.id DESC";
                break;
            case "Más Antiguos":
                $sql .= "ORDER BY p.id ASC";
                break;
            default:
                $sql .= "ORDER BY p.id ASC";
                break;
        }
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            error_log("MySQLi Prepare Error: " . $this->conn->error);
            return [];
        }

        $stmt->bind_param($param_types, ...$params);
        $success = $stmt->execute();
        
        if ($success) {
            $resultado = $stmt->get_result();
            $data = $resultado->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            
            return $data;
        } else {
            error_log("MySQLi Execute Error: " . $stmt->error);
            $stmt->close();
            return [];
        }
    }

    public function existeProducto($nombre, $id_usuario) {
        $sql = "SELECT COUNT(*) AS count FROM producto WHERE nombre = ? AND id_usuario = ?";

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param("si", $nombre, $id_usuario);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result && $row = $result->fetch_assoc()) {
            $stmt->close();
            return $row['count'] > 0;
        }
        $stmt->close();
        return false;
    }

    public function obtenerProductoPorId($id) {
        $id = (int)$id;
        $sql = "SELECT p.*, c.nombre as categoria, c.id as id_cat FROM producto p INNER JOIN categoria c ON p.id_cat = c.id WHERE p.id = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $row = $result->fetch_assoc()) {
            return $row;
        }
        return null;
    }

    public function actualizarProducto($id, $nombre, $imagen, $categoria_id) {
        $sql = "UPDATE producto SET nombre = ?, imagen = ?, id_cat = ? WHERE id = ?";

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param("ssii", $nombre, $imagen, $categoria_id, $id);
        $resultado = $stmt->execute();
        $stmt->close();

        return $resultado;
    }

    public function crearP($nombre, $rutaFinal, $categoria_id, $id_usuario) {
        $sql = "INSERT INTO producto (nombre, imagen, id_cat, id_usuario) VALUES (?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("ssii", $nombre, $rutaFinal, $categoria_id, $id_usuario);
        
        if ($stmt->execute()) {
            return $stmt->insert_id;
        } else {
            return false;
        }
    }

    public function borrarP($id) {
        $sql = "DELETE FROM producto WHERE id = ?";

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param("i", $id);
        $resultado = $stmt->execute();
        $stmt->close();

        return $resultado;
    }
}
?>