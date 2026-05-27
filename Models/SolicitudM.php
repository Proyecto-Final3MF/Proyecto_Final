<?php
require_once(__DIR__ . '/../Config/conexion.php');

class Solicitud {

    private $conn;

    public function __construct() {  
        $this->conn = conectar();
    }

    public function obtenerProductos($id_usuario) {
        $id_usuario = (int)$id_usuario;
        $productos_ocupados_sql = " SELECT DISTINCT producto_id FROM solicitud WHERE estado_id BETWEEN 1 AND 4"; 
        $sql = "SELECT id, nombre FROM producto WHERE id_usuario = $id_usuario AND id NOT IN ($productos_ocupados_sql) ORDER BY nombre ASC";
        
        $resultado = $this->conn->query($sql);
        
        if ($resultado) {
            return $resultado->fetch_all(MYSQLI_ASSOC);
        } else {
            error_log("Error al obtener productos disponibles: " . $this->conn->error);
            return [];
        }
    }

    public function obtenerProductoporId($producto_id) {
        $sql = "SELECT nombre FROM producto WHERE id = ? LIMIT 1";

        $stmt = $this->conn->prepare($sql);

        if ($stmt === false) {
            return null;
        }

        $stmt->bind_param("i", $producto_id);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result && $row = $result->fetch_assoc()) {
            $stmt->close();
            return $row['nombre'];
        }

        $stmt->close();
        return null;
    }

    public function obtenerProductoUrgente($id_usuario) {
        $sql = "SELECT id, nombre FROM producto WHERE id_usuario = ? ORDER BY id DESC LIMIT 1";

        $stmt = $this->conn->prepare($sql);

        if ($stmt === false) {
            return null;
        }

        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado && $resultado->num_rows > 0) {
            $data = $resultado->fetch_assoc();
            $stmt->close();
            return $data;
        } else {
            $stmt->close();
            return null;
        }
    }

    public function borrarS($id) {
        try {
            $sql1 = "DELETE FROM historia_solicitud WHERE id_solicitud = $id";
            $this->conn->query($sql1);

            $sql2 = "DELETE FROM solicitud WHERE id=$id AND tecnico_id IS NULL";
            $result = $this->conn->query($sql2); 

            $this->conn->commit();
            return $result;

        } catch (mysqli_sql_exception $e) {
            throw $e; 
        }
    }

    public function ListarSLC($id_usuario) {
        $id_usuario = (int)$id_usuario;
        $sql = "SELECT s.*, p.nombre, p.imagen FROM solicitud s 
                inner join producto p on s.producto_id = p.id 
                WHERE s.cliente_id = ? AND s.estado_id = 1;";
        $stmt = $this->conn->prepare($sql);

        if ($stmt === false) {
            error_log("Error al preparar la consulta: " . $this->conn->error);
            return [];
        }

        $stmt->bind_param("i", $id_usuario);

        $ejecucion_exitosa = $stmt->execute();

        if ($ejecucion_exitosa) {
            $resultado = $stmt->get_result();

            if ($resultado) {
                $datos = $resultado->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
                return $datos;
            }
        } else {
            error_log("Error al ejecutar la consulta: " . $stmt->error);
        }

        if ($stmt) {
            $stmt->close();
        }
        return [];
    }

    public function ListarTL($usuarioId, $search = null) {
        $sql = "SELECT s.*, p.nombre as producto, p.imagen, u.nombre
                FROM solicitud s
                INNER JOIN producto p ON s.producto_id = p.id
                INNER JOIN usuario u ON s.cliente_id = u.id
                WHERE s.estado_id = 1 ";
        $params = [];
        $param_types = '';
        $stmt = null;

        $sql = "SELECT 
                s.*, 
                p.nombre AS producto, 
                p.imagen, 
                u.nombre AS cliente_nombre
            FROM solicitud s
            INNER JOIN producto p ON s.producto_id = p.id
            INNER JOIN usuario u ON s.cliente_id = u.id
            WHERE s.estado_id = 1 
              AND s.tecnico_id IS NULL
              AND s.cliente_id != ?";

        $params = [$usuarioId];
        $param_types = 'i';

        if (!empty($search)) {
            $search_terms = explode(" ", $search);
            foreach ($search_terms as $palabra) {
                $sql .= " AND (s.titulo LIKE ? OR s.descripcion LIKE ? OR p.nombre LIKE ?)";
                $search_term = "%" . $palabra . "%";
                $params[] = $search_term;
                $params[] = $search_term;
                $params[] = $search_term;
                $param_types .= 'sss';
            }
        }

        $sql .= " ORDER BY FIELD(s.prioridad, 'urgente', 'alta', 'media', 'baja'), s.fecha_creacion DESC";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Error en prepare: " . $this->conn->error);
            return [];
        }

        $stmt->bind_param($param_types, ...$params);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $data = $resultado->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $data;
    }


    public function asignarS($id_tecnico, $id_soli) {
        $sql = "UPDATE solicitud SET tecnico_id = ?, estado_id = 2 WHERE id = ?";

        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            error_log("Error al preparar la declaración: " . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param("ii", $id_tecnico, $id_soli);
        $success = $stmt->execute();
        $stmt->close();
        
        return $success;
    }

    public function ListarSA($id_usuario) {
        $sql = "SELECT s.*, p.nombre AS producto_nombre, p.imagen, e.nombre AS estado_nombre,
                    u_cliente.nombre AS nombre_cliente, u_tecnico.id AS id_tecnico, u_tecnico.nombre AS nombre_tecnico
                FROM solicitud s
                INNER JOIN producto p ON s.producto_id = p.id
                INNER JOIN estado e ON s.estado_id = e.id
                INNER JOIN usuario u_cliente ON s.cliente_id = u_cliente.id
                LEFT JOIN usuario u_tecnico ON s.tecnico_id = u_tecnico.id
                WHERE (s.tecnico_id = ? OR s.cliente_id = ?)
                AND s.estado_id > 1 AND s.estado_id < 5
                ORDER BY FIELD(s.prioridad, 'urgente', 'alta', 'media', 'baja'), s.fecha_actualizacion DESC";

        $stmt = $this->conn->prepare($sql);

        if ($stmt === false) {
            error_log("Error al preparar la consulta: " . $this->conn->error);
            return [];
        }

        $stmt->bind_param("ii", $id_usuario, $id_usuario);

        $ejecucion_exitosa = $stmt->execute();

        if ($ejecucion_exitosa) {
            $resultado = $stmt->get_result();

            if ($resultado) {
                $datos = $resultado->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
                return $datos;
            }
        } else {
            error_log("Error al ejecutar la consulta: " . $stmt->error);
        }

        if ($stmt) {
            $stmt->close();
        }
        return [];
    }

    public function ListarST($id_usuario, $search) {
        $id_usuario = (int)$id_usuario;

        $params = [];
        $param_types = '';
        $stmt = null;

        $sql = "SELECT s.*, p.nombre AS producto_nombre, p.imagen, r.rating, e.nombre AS estado_nombre,
                    u_cliente.nombre AS nombre_cliente, u_tecnico.id AS id_tecnico, u_tecnico.nombre AS nombre_tecnico
                FROM solicitud s
                INNER JOIN producto p ON s.producto_id = p.id
                INNER JOIN estado e ON s.estado_id = e.id
                LEFT JOIN reviews r ON s.id = r.id_solicitud
                INNER JOIN usuario u_cliente ON s.cliente_id = u_cliente.id
                LEFT JOIN usuario u_tecnico ON s.tecnico_id = u_tecnico.id
                WHERE (s.tecnico_id = ? OR s.cliente_id = ?)
                AND s.estado_id = 5";

        $params[] = $id_usuario;
        $params[] = $id_usuario;
        $param_types = "ii";

        if (!empty($search)) {
            $search_terms = explode(" ", $search);
            foreach ($search_terms as $palabra) {
                $sql .= " AND (s.titulo LIKE ? OR s.descripcion LIKE ? OR p.nombre LIKE ?";

                if ($_SESSION['rol'] == ROL_CLIENTE) {
                    $sql .= " OR u_tecnico.nombre LIKE ?)";
                } else {
                    $sql .= " OR u_cliente.nombre LIKE ?)";
                }

                $search_term = "%".$palabra."%";
                $params[] = $search_term;
                $params[] = $search_term;
                $params[] = $search_term;
                $params[] = $search_term;
                $param_types .= "ssss";
            }
        }

        $sql .= " ORDER BY FIELD(s.prioridad, 'urgente', 'alta', 'media', 'baja'), s.fecha_creacion DESC";
        
        $stmt = $this->conn->prepare($sql);

        if ($stmt === false) {
            error_log("Error al preparar la consulta: " . $this->conn->error);
            return [];
        }

        $stmt->bind_param($param_types, ...$params);

        $ejecucion_exitosa = $stmt->execute();

        if ($ejecucion_exitosa) {
            $resultado = $stmt->get_result();

            if ($resultado) {
                $datos = $resultado->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
                return $datos;
            }
        } else {
            error_log("Error al ejecutar la consulta: " . $stmt->error);
        }

        if ($stmt) {
            $stmt->close();
        }
        return [];
    }

    public function crearS($titulo, $descripcion, $producto, $usuario_id, $prioridad) {
        $titulo = $this->conn->real_escape_string($titulo);
        $descripcion = $this->conn->real_escape_string($descripcion);

        $sql = "INSERT INTO solicitud (titulo, cliente_id, fecha_creacion, prioridad, producto_id, estado_id, descripcion) VALUES ('$titulo', $usuario_id, NOW(), '$prioridad', $producto, 1, '$descripcion')";

        if ($this->conn->query($sql) === TRUE) {
            return $this->conn->insert_id;
        } else {
            return false;
        }
    }

    public function obtenerSolicitudPorId($id) {
        $sql = "SELECT * FROM solicitud WHERE id = ? LIMIT 1";

        $stmt = $this->conn->prepare($sql);

        if ($stmt === false) {
            return null;
        }

        $stmt->bind_param("i", $id);

        $stmt->execute();

        $resultado = $stmt->get_result();

        $data = $resultado ? $resultado->fetch_assoc() : null;

        $stmt->close();
        
        return $data;
    }

    public function obtenerEstados() {
        $sql = "SELECT * FROM estado WHERE id > 1";
        $resultado = $this->conn->query($sql);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    public function obtenerNombreEstadoPorId($estado_id) {
        $sql = "SELECT nombre FROM estado WHERE id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $estado_id);
        $stmt->execute();
        $resultado = $stmt->get_result();

        return $resultado->fetch_assoc(); 
    }

    public function checkUsuario($id_solicitud, $id_tecnico, $id_cliente) {
        $sql = "SELECT id FROM solicitud WHERE id = ? AND cliente_id = ? AND tecnico_id = ?";
        $stmt = $this->conn->prepare($sql);

        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param("iii", $id_solicitud, $id_cliente, $id_tecnico);
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

    public function actualizarS($id, $descripcion, $estado_id, $precio) {
        $sql = "UPDATE solicitud SET descripcion = ?, estado_id = ?, precio = ?, fecha_actualizacion = NOW() WHERE id = ?";

        $stmt = $this->conn->prepare($sql);

        if ($stmt === false) {
            return false;
        }
        $stmt->bind_param("sidi", $descripcion, $estado_id, $precio, $id);

        $resultado = $stmt->execute();

        $stmt->close();

        return $resultado;
    }

    public function cancelarS($id_soli) {
        $id_soli = (int)$id_soli;
        $sql = "UPDATE solicitud SET tecnico_id = NULL, estado_id = 1 WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Error al preparar la cancelación de la solicitud: " . $this->conn->error);
            return false;
        }
        $stmt->bind_param("i", $id_soli);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function __destruct() {
        
    }
}
?>
