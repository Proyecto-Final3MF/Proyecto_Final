<?php
require_once(__DIR__ . '/../Config/conexion.php');

class Mensaje {
    private $conexion;
    public function __construct() {
        $this->conexion = new mysqli('localhost', 'root', '', 'tecnicosasociados');
        if ($this->conexion->connect_error) {
            die("Error de conexión: " . $this->conexion->connect_error);
        }
    }

    public function obtenerMensajes($receptor_id = null, $esAdmin = false) {
        if ($esAdmin) {
            // Si es admin -> ver todos los mensajes
            $sql = "SELECT m.id, m.usuario_id, m.receptor_id, m.mensaje, m.fecha,
                    u.nombre AS usuario, r.nombre AS receptor
                    FROM mensaje m
                    JOIN usuario u ON m.usuario_id = u.id
                    LEFT JOIN usuario r ON m.receptor_id = r.id
                    ORDER BY m.fecha DESC";
            $stmt = $this->conexion->prepare($sql);
        } else {
            // Usuario normal -> solo ve sus mensajes
            $sql = "SELECT m.id, m.usuario_id, m.receptor_id, m.mensaje, m.fecha,
                    u.nombre AS emisor,
                    r.nombre AS receptor
                    FROM mensaje m
                    JOIN usuario u ON m.usuario_id = u.id
                    LEFT JOIN usuario r ON m.receptor_id = r.id
                    WHERE m.usuario_id = ? OR m.receptor_id = ?
                    ORDER BY m.fecha DESC";

            $stmt = $this->conexion->prepare($sql);
            // Se enlaza el ID del usuario dos veces
            $stmt->bind_param("ii", $usuario_id, $usuario_id);
        }
        // Si falla la preparación de la consulta → devuelve array vacío
        if (!$stmt) {
            return [];
        }
        // Ejecuta la consulta
        $stmt->execute();
        $result = $stmt->get_result();
        // Devuelve todos los resultados como array asociativo
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function obtenerMensajesConversacion($usuario_id, $otroUsuario_id, $solicitud_id = null) {
        $sql = "SELECT m.id, m.usuario_id, m.receptor_id, m.mensaje, m.fecha, m.solicitud_id,
                   u.nombre AS emisor,
                   r.nombre AS receptor
                FROM mensaje m
                JOIN usuario u ON m.usuario_id = u.id
                LEFT JOIN usuario r ON m.receptor_id = r.id
                WHERE ((m.usuario_id = ? AND m.receptor_id = ?)
                OR (m.usuario_id = ? AND m.receptor_id = ?))";

        // Si se proporciona un ID de solicitud, filtrar por él
        if ($solicitud_id !== null) {
            $sql .= " AND m.solicitud_id = ?";
        }

        $sql .= " ORDER BY m.fecha ASC";

        $stmt = $this->conexion->prepare($sql);

        if ($solicitud_id !== null) {
            $stmt->bind_param("iiiii", $usuario_id, $otroUsuario_id, $otroUsuario_id, $usuario_id, $solicitud_id);
        } else {
            $stmt->bind_param("iiii", $usuario_id, $otroUsuario_id, $otroUsuario_id, $usuario_id);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function obtenerConversacionesPorSolicitud($usuario_id, $idSolicitud) {
        $sql = "SELECT 
                CASE WHEN usuario_id = ? THEN receptor_id ELSE usuario_id END AS otro_usuario_id,
                COALESCE(
                    CASE WHEN usuario_id = ? THEN r.nombre ELSE u.nombre END,
                    'Usuario desconocido'
                ) AS otro_usuario,
                SUBSTRING_INDEX(GROUP_CONCAT(mensaje ORDER BY fecha DESC SEPARATOR '||'), '||', 1) AS ultimo_mensaje,
                MAX(fecha) AS ultima_fecha
                FROM mensaje
                LEFT JOIN usuario u ON mensaje.usuario_id = u.id
                LEFT JOIN usuario r ON mensaje.receptor_id = r.id
                WHERE (usuario_id = ? OR receptor_id = ?) 
                AND solicitud_id = ?
                GROUP BY otro_usuario_id
                ORDER BY ultima_fecha DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("iiiii", $usuario_id, $usuario_id, $usuario_id, $usuario_id, $idSolicitud);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    // Obtener conversación entre dos usuarios específicos
    public function obtenerConversacion($usuario_id, $otro_usuario_id, $solicitud_id = null) {
        $sql = "SELECT m.id, m.usuario_id, m.receptor_id, m.mensaje, m.fecha, m.solicitud_id,
                       u.nombre AS emisor, r.nombre AS receptor
                FROM mensaje m
                JOIN usuario u ON m.usuario_id = u.id
                LEFT JOIN usuario r ON m.receptor_id = r.id
                WHERE ((m.usuario_id = ? AND m.receptor_id = ?)
                OR (m.usuario_id = ? AND m.receptor_id = ?))";

        // Si se proporciona un ID de solicitud, filtrar por él
        if ($solicitud_id !== null) {
            $sql .= " AND m.solicitud_id = ?";
        }

        $sql .= " ORDER BY m.fecha ASC";

        $stmt = $this->conexion->prepare($sql);

        if ($solicitud_id !== null) {
            $stmt->bind_param("iiiii", $usuario_id, $otro_usuario_id, $otro_usuario_id, $usuario_id, $solicitud_id);
        } else {
            $stmt->bind_param("iiii", $usuario_id, $otro_usuario_id, $otro_usuario_id, $usuario_id);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    // Obtener todos los mensajes
    public function obtenerTodosLosMensajes() {
        $sql = "SELECT m.id, m.usuario_id, m.receptor_id, m.mensaje, m.fecha, 
                       u.nombre AS usuario, 
                       r.nombre AS receptor
                FROM mensaje AS m 
                JOIN usuario AS u ON m.usuario_id = u.id 
                LEFT JOIN usuario AS r ON m.receptor_id = r.id
                ORDER BY m.fecha ASC";

        $result = $this->conexion->query($sql);

        if (!$result) {
            echo "Error en query: " . $this->conexion->error;
            return [];
        }

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function obtenerTodasLasConversaciones() {
        $sql = "SELECT LEAST(usuario_id, receptor_id) AS u1,
                GREATEST(usuario_id, receptor_id) AS u2,
                COUNT(*) AS total_mensajes,
                MAX(fecha) AS ultima_fecha FROM mensaje WHERE receptor_id IS NOT NULL
                GROUP BY u1, u2 ORDER BY ultima_fecha DESC;";

        $result = $this->conexion->query($sql);

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function obtenerConversaciones($usuario_id) {
        $sql = "SELECT 
                    CASE WHEN m.usuario_id = ? THEN m.receptor_id ELSE m.usuario_id END AS otro_usuario_id,
                    COALESCE(
                        CASE WHEN m.usuario_id = ? THEN r.nombre ELSE u.nombre END,
                        'Usuario desconocido'
                    ) AS otro_usuario,
                    SUBSTRING_INDEX(GROUP_CONCAT(m.mensaje ORDER BY m.fecha DESC SEPARATOR '||'), '||', 1) AS ultimo_mensaje,
                    MAX(m.fecha) AS ultima_fecha,
                    m.solicitud_id,
                    s.titulo AS solicitud_titulo
                FROM mensaje m
                JOIN usuario u ON m.usuario_id = u.id
                LEFT JOIN usuario r ON m.receptor_id = r.id
                LEFT JOIN solicitud s ON m.solicitud_id = s.id
                WHERE (m.usuario_id = ? OR m.receptor_id = ?)
                GROUP BY otro_usuario_id, m.solicitud_id
                ORDER BY ultima_fecha DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("iiii", $usuario_id, $usuario_id, $usuario_id, $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    // Enviar mensaje
    public function enviarMensaje($emisor_id, $receptor_id, $mensaje, $solicitud_id) {
        if (!$solicitud_id) {
            return false;
        }

        $sql = "INSERT INTO mensaje (usuario_id, receptor_id, mensaje, fecha, solicitud_id) VALUES (?, ?, ?, NOW(), ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("iisi", $emisor_id, $receptor_id, $mensaje, $solicitud_id);

        return $stmt->execute();
    }

    // Guardar mensaje sin receptor
    public function guardarMensaje($usuario_id, $mensaje) {
        $sql = "INSERT INTO mensaje (usuario_id, mensaje) VALUES (?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("is", $usuario_id, $mensaje);
        $stmt->execute();
        $stmt->close();
    }

    public function borrarConversacion($usuario_id, $receptor_id) {
        $sql = "DELETE FROM mensaje
                WHERE (usuario_id = ? AND receptor_id = ?)
                   OR (usuario_id = ? AND receptor_id = ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("iiii", $usuario_id, $receptor_id, $receptor_id, $usuario_id);
        return $stmt->execute();
    }
}