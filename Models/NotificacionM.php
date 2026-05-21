<?php
require_once(__DIR__ . '/../Config/conexion.php');

class NotificacionM {
    private $conn;

    public function __construct() {
        $this->conn = conectar();
    }

    // Crear notificación (agregamos $tipo, por defecto 'normal')
    public function crear($usuario_id, $mensaje, $tipo = 'normal') {
        $sql = "INSERT INTO notificacion (usuario_id, mensaje, tipo) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iss", $usuario_id, $mensaje, $tipo);
        return $stmt->execute();
    }

    // Obtener notificaciones no leídas (opcional: filtrar por tipo)
    public function obtenerNoLeidas($usuario_id, $tipo = null) {
        $sql = "SELECT * FROM notificacion WHERE usuario_id = ? AND leida = 0";
        $params = [$usuario_id];
        $types = "i";
        
        if ($tipo !== null) {
            $sql .= " AND tipo = ?";
            $params[] = $tipo;
            $types .= "s";
        }
        
        $sql .= " ORDER BY fecha DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Marcar todas como leídas (opcional: filtrar por tipo)
    public function marcarTodasLeidas($usuario_id, $tipo = null) {
        $sql = "UPDATE notificacion SET leida = 1 WHERE usuario_id = ?";
        $params = [$usuario_id];
        $types = "i";
        
        if ($tipo !== null) {
            $sql .= " AND tipo = ?";
            $params[] = $tipo;
            $types .= "s";
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        return $stmt->execute();
    }

    // Marcar como leída
    public function marcarLeida($id) {
        $sql = "UPDATE notificacion SET leida = 1 WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

}
?>