<?php
class Usuario {
    private $conn;
    private $rolId;

    public function __construct() {
        $this->conn = conectar();
    }

    public function crearT($usuario, $mail, $rol_id, $contrasena_hash, $otra_especialidad) {
        $foto_perfil_default = "Assets/imagenes/perfil/fotodefault.webp"; 
        
        $sql = "INSERT INTO usuario (nombre, contrasena, email, rol_id, otra_especialidad, foto_perfil) 
                VALUES (?, ?, ?, ?, ?, ?)"; 
        
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            error_log("MySQLi Prepare Error (crearT): " . $this->conn->error);
            return false;
        }

        $stmt->bind_param("sssiss", 
            $usuario, 
            $contrasena_hash,
            $mail, 
            $rol_id,
            $otra_especialidad, 
            $foto_perfil_default
        );
        
        $success = $stmt->execute();
        
        if (!$success) {
            error_log("MySQLi Execute Error (crearT): " . $stmt->error);
        }
        
        $stmt->close();
        return $success;
    }

    public function crearC($usuario, $mail, $rol_id, $contrasena_hash) {
        $estado_verificacion = 'aprobado'; 
        $ruta_evidencia = null; 
        $otra_especialidad = null; 
        $foto_perfil_default = "Assets/imagenes/perfil/fotodefault.webp"; 
        
        $sql = "INSERT INTO usuario (nombre, contrasena, email, rol_id, otra_especialidad, foto_perfil) 
                VALUES (?, ?, ?, ?, ?, ?)"; 
        
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            die("❌ ERROR AL PREPARAR LA CONSULTA (PREPARE): " . $this->conn->error . " | SQL: " . $sql);
            return false;
        }

        $stmt->bind_param("sssiss", 
            $usuario, 
            $contrasena_hash, 
            $mail, 
            $rol_id,
            $otra_especialidad, 
            $foto_perfil_default
        );
        
        $success = $stmt->execute();
        
        if (!$success) {
            die("❌ ERROR AL EJECUTAR LA CONSULTA (EXECUTE): " . $stmt->error);
        }
        
        $stmt->close();
        return $success;
    }

    /**
     * Obtiene el email de un usuario dado su ID.
     * Es ideal para usar en servicios de notificación.
     * @param int $id El ID del usuario.    
     * @return string|null El email del usuario o null si no existe.
     */
    public function obtenerEmailUsuarioPorId($id) {
        $sql = "SELECT email FROM usuario WHERE id = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log("MySQLi Prepare Error (obtenerEmailUsuarioPorId): " . $this->conn->error);
            return null;
        }

        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            error_log("MySQLi Execute Error (obtenerEmailUsuarioPorId): " . $stmt->error);
            $stmt->close();
            return null;
        }

        $resultado = $stmt->get_result();
        $data = $resultado->fetch_assoc();
        $stmt->close();

        // Devuelve el email o null si no encuentra la fila
        return $data ? $data['email'] : null; 
    }

    // Método para obtener emails de todos los técnicos aprobados
    public function obtenerEmailsTecnicos() {
        $sql = "SELECT email FROM usuario WHERE rol_id = 1";
        $resultado = $this->conn->query($sql);
        if ($resultado) {
            $emails = [];
            while ($row = $resultado->fetch_assoc()) {
                $emails[] = $row['email'];
            }
            return $emails;
        }
        return [];
    }

    public function obtenerEmailsTecnicosPorEspecializacion($especializacion_id) {
        $sql = "SELECT DISTINCT u.email 
                FROM usuario u 
                JOIN usuario_especializacion ue ON u.id = ue.usuario_id 
                WHERE u.rol_id = 1 AND u.estado_verificacion = 'aprobado' AND ue.especializacion_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $especializacion_id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $emails = [];
        while ($row = $resultado->fetch_assoc()) {
            $emails[] = $row['email'];
        }
        $stmt->close();
        return $emails;
    }

    public function obtenerEspecializaciones() {
        $sql = "SELECT id, nombre FROM especializacion ORDER BY id ASC";
        $resultado = $this->conn->query($sql);
        if ($resultado) {
            return $resultado->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    public function guardarEspecializaciones($usuario_id, $ids_array) {
        if (empty($ids_array)) return true;
        
        $sql = "INSERT INTO usuario_especializacion (usuario_id, especializacion_id) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            error_log("MySQLi Prepare Error (guardarEspecializaciones): " . $this->conn->error);
            return false;
        }

        $success = true;
        foreach ($ids_array as $especializacion_id) {
            $especializacion_id = (int)$especializacion_id;
            if ($usuario_id > 0 && $especializacion_id > 0) {
                $stmt->bind_param("ii", $usuario_id, $especializacion_id);
                if (!$stmt->execute()) {
                    error_log("MySQLi Execute Error (guardarEspecializaciones): " . $stmt->error);
                    $success = false;
                }
            }
        }
        $stmt->close();
        return $success;
    }

    public function check($id_logeado) {
        $sql = "SELECT id FROM usuario WHERE id = ?";
        $stmt = $this->conn->prepare($sql);

        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param("i", $id_logeado);
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

    public function obtenerPorEmail($email) {
        $email = $this->conn->real_escape_string($email);
        $sql = "SELECT * FROM usuario WHERE email = '$email' LIMIT 1";
        $res = $this->conn->query($sql);
        if ($res && $res->num_rows > 0) {
            return $res->fetch_assoc();
        }
        return false;
    }

    // Verificar login
    
    public function verificarU($usuario, $contrasena) {
        $usuario = $this->conn->real_escape_string($usuario);
        $sql = "SELECT * FROM usuario WHERE nombre='$usuario' LIMIT 1";
        $res = $this->conn->query($sql);

        if ($row = $res->fetch_assoc()) {
            if (password_verify($contrasena, $row['contrasena'])) {
                return $row;
            }
        }
        return false;
    }
    
    // CRUD básico

    public function editarU($id, $nombre, $email, $foto_perfil = null) {
        if ($foto_perfil) {
            $sql = "UPDATE usuario SET nombre = ?, email = ?, foto_perfil = ? WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sssi", $nombre, $email, $foto_perfil, $id);
        } else {
            $sql = "UPDATE usuario SET nombre = ?, email = ? WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssi", $nombre, $email, $id);
        }
        return $stmt->execute(); 
    }

    // Listado de usuarios con filtros
    
    public function listarU($orden, $rol_filter, $search) {
        $sql = "SELECT u.*, r.nombre as rol FROM usuario u INNER JOIN rol r ON u.rol_id = r.id ";
        $conditions = [];
        $params = [];
        $param_types = '';

        if (!empty($search)) {
            $search_terms = explode(" ", $search);
            foreach ($search_terms as $palabra) {
                $conditions[] = "(u.nombre LIKE ? OR u.email LIKE ?)";
                $search_term = "%" . $palabra . "%";
                $params[] = $search_term;
                $params[] = $search_term;
                $param_types .= 'ss';
            } 
        }

        switch ($rol_filter) {
            case 'Clientes': $conditions[] = "u.rol_id = 2"; break;
            case 'Tecnicos': $conditions[] = "u.rol_id = 1"; break;
            case 'Administradores': $conditions[] = "u.rol_id = 3"; break;
            case 'Todos':
            default: break;
        }

        if (!empty($conditions)) $sql .= " WHERE " . implode(" AND ", $conditions);

        switch ($orden) {
            case "A-Z": $sql .= " ORDER BY u.nombre ASC"; break;
            case "Z-A": $sql .= " ORDER BY u.nombre DESC"; break;
            case "Más Recientes": $sql .= " ORDER BY u.id DESC"; break;
            case "Más Antiguos": $sql .= " ORDER BY u.id ASC"; break;
            default: $sql .= " ORDER BY u.id ASC"; break;
        }

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("MySQLi Prepare Error: " . $this->conn->error);
            return [];
        }

        if (!empty($param_types)) $stmt->bind_param($param_types, ...$params);
        if (!$stmt->execute()) {
            error_log("MySQLi Execute Error: " . $stmt->error);
            $stmt->close();
            return [];
        }

        $resultado = $stmt->get_result();
        $data = $resultado->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $data;
    }

    // Vista previa (últimos usuarios)
    
    public function PreviewU() {
        $sql = "SELECT u.*, r.nombre as rol FROM usuario u INNER JOIN rol r ON u.rol_id = r.id ORDER BY id DESC LIMIT 10";
        $resultado = $this->conn->query($sql);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Verifica si el usuario tiene dependencias activas que impidan su eliminación.
     * @param int $id ID del usuario.
     * @return array Array con 'puede_eliminar' (bool) y 'mensaje' (string) si no se puede.
     */
    public function verificarDependencias($id) {
        // Verificar solicitudes activas (estado_id < 5, es decir, no finalizadas)
        $sql_solicitudes = "SELECT COUNT(*) AS count FROM solicitud WHERE (cliente_id = ? OR tecnico_id = ?) AND estado_id < 5";
        $stmt_solicitudes = $this->conn->prepare($sql_solicitudes);
        $stmt_solicitudes->bind_param("ii", $id, $id);
        $stmt_solicitudes->execute();
        $result_solicitudes = $stmt_solicitudes->get_result();
        $solicitudes_activas = $result_solicitudes->fetch_assoc()['count'];
        $stmt_solicitudes->close();

        if ($solicitudes_activas > 0) {
            return ['puede_eliminar' => false, 'mensaje' => "No se puede eliminar el usuario porque tiene {$solicitudes_activas} solicitud(es) activa(s). Finaliza o reasigna las solicitudes primero."];
        }

        // Verificar productos asociados
        $sql_productos = "SELECT COUNT(*) AS count FROM producto WHERE id_usuario = ?";
        $stmt_productos = $this->conn->prepare($sql_productos);
        $stmt_productos->bind_param("i", $id);
        $stmt_productos->execute();
        $result_productos = $stmt_productos->get_result();
        $productos = $result_productos->fetch_assoc()['count'];
        $stmt_productos->close();

        if ($productos > 0) {
            return ['puede_eliminar' => false, 'mensaje' => "No se puede eliminar el usuario porque tiene {$productos} producto(s) asociado(s). Elimina o reasigna los productos primero."];
        }

        // Si no hay dependencias críticas, permitir eliminación
        return ['puede_eliminar' => true, 'mensaje' => ''];
    }

    public function borrarU($id) {
        // Verificar dependencias
        $dependencias = $this->verificarDependencias($id);
        if (!$dependencias['puede_eliminar']) {
            return false;
        }

        // Obtener datos del usuario ANTES de eliminar
        $usuario = $this->buscarUsuarioId($id);
        if (!$usuario) {
            return false;
        }

        // Eliminar foto de perfil si no es la default
        if ($usuario['foto_perfil'] !== "Assets/imagenes/perfil/fotodefault.webp" && file_exists($usuario['foto_perfil'])) {
            unlink($usuario['foto_perfil']);
        }

        // Iniciar transacción
        $this->conn->begin_transaction();

        try {
            // Eliminar registros relacionados
            $sql_notif = "DELETE FROM notificacion WHERE usuario_id = ?";
            $stmt_notif = $this->conn->prepare($sql_notif);
            $stmt_notif->bind_param("i", $id);
            $stmt_notif->execute();
            $stmt_notif->close();

            $sql_reviews = "DELETE FROM reviews WHERE id_tecnico = ? OR id_cliente = ?";
            $stmt_reviews = $this->conn->prepare($sql_reviews);
            $stmt_reviews->bind_param("ii", $id, $id);
            $stmt_reviews->execute();
            $stmt_reviews->close();

            // Finalmente, eliminar el usuario
            $sql_usuario = "DELETE FROM usuario WHERE id = ?";
            $stmt_usuario = $this->conn->prepare($sql_usuario);
            $stmt_usuario->bind_param("i", $id);
            $success = $stmt_usuario->execute();
            $stmt_usuario->close();

            if ($success) {
                $this->conn->commit();
                return true;
            } else {
                $this->conn->rollback();
                return false;
            }
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Error al eliminar usuario: " . $e->getMessage());
            return false;
        }
    }

    public function buscarUsuarioId($id) {
        $sql = "SELECT * FROM usuario WHERE id = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log("MySQLi Prepare Error: " . $this->conn->error);
            return [];
        }

        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            error_log("MySQLi Execute Error: " . $stmt->error);
            $stmt->close();
            return [];
        }

        $resultado = $stmt->get_result();
        $data = $resultado->fetch_assoc();
        $stmt->close();
        return $data;
    }

    public function checkEmail($email, $id) {
        $sql = "SELECT* FROM usuario WHERE id != ? AND email = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log("MySQLi Prepare Error: " . $this->conn->error);
            return [];
        }

        $stmt->bind_param("is", $id, $email);
        if (!$stmt->execute()) {
            error_log("MySQLi Execute Error: " . $stmt->error);
            $stmt->close();
            return [];
        }

        $resultado = $stmt->get_result();
        $data = $resultado->fetch_assoc();
        $stmt->close();
        return $data;
    }

    public function getEspecializacion($id_tecnico) {
        $sql = "SELECT e.nombre FROM especializacion e JOIN usuario_especializacion ue ON e.id = ue.especializacion_id WHERE ue.usuario_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_tecnico);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        $especializaciones = [];
        while ($fila = $resultado->fetch_assoc()) {
            $especializaciones[] = $fila['nombre'];
        }
        return $especializaciones;
    }
}
?>