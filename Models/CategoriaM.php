<?php

class Categoria {

    private $conn;

    public function __construct() {
        $this->conn = conectar();
    }

    public function verificarExistencia($nombre) {
        $sql = "SELECT COUNT(*) as count FROM categoria WHERE nombre = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param('s', $nombre);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $row = $resultado->fetch_assoc();
        $stmt->close();

        return $row['count'] > 0;
    }

    public function guardarC($nombre) {
        $sql = "INSERT INTO categoria (nombre) VALUES (?)";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param('s', $nombre);
        $result = $stmt->execute();
        $newId = $result ? $this->conn->insert_id : false;

        $stmt->close();
        return $newId;
    }

    public function listarC($orden, $search) {
        $sql = "SELECT * FROM categoria c ";

        $conditions = [];
        $params = [];
        $param_types = '';

        $search = trim($search);

        if (!empty($search) || $search ==='') {
            $search_terms = explode(" ", $search);

            foreach ($search_terms as $palabra) {

                $conditions[] = "(c.nombre LIKE ?) "; 
                            
                $search_term = "%" . $palabra . "%";
                $params[] = $search_term;
                $param_types = 's';
            } 
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        switch ($orden) {
            case "A-Z": $sql .= "ORDER BY nombre ASC"; break;
            case "Z-A": $sql .= "ORDER BY nombre DESC"; break;
            case "Más Recientes": $sql .= "ORDER BY id DESC"; break;
            case "Más Antiguas": $sql .= "ORDER BY id ASC"; break;
            default: $sql .= "ORDER BY id ASC"; break;
        }

        $stmt = $this->conn->prepare($sql);
            
        if (!$stmt) {
            error_log("MySQLi Prepare Error: " . $this->conn->error);
            return [];
        }

        if (!empty($param_types)) {
            $stmt->bind_param($param_types, ...$params);
        }
            
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

    public function buscarPorId($id) {
        $sql = "SELECT * FROM categoria WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $categoria = $result->fetch_assoc();
        $stmt->close();

        return $categoria;
    }

    public function actualizarC($id, $nombre) {
        $sql = "UPDATE categoria SET nombre = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param("si", $nombre, $id);
        $resultado = $stmt->execute();
        $stmt->close();

        return $resultado;
    }
    
    public function borrarC($id) {
        $sql = "DELETE FROM categoria WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param('i', $id);
        $resultado = $stmt->execute();
        $stmt->close();

        return $resultado;
    }
}
?>