<?php
class Auditoria {
    private $conn;
    private $table = 'auditoria';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function registrar($usuario_id, $accion, $tabla_afectada = null, $registro_id = null, $datos_anteriores = null, $datos_nuevos = null) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $navegador = $this->getBrowser($user_agent);
        $query = "INSERT INTO " . $this->table . " (usuario_id, accion, tabla_afectada, registro_id, datos_anteriores, datos_nuevos, ip_address, user_agent, navegador) VALUES (:usuario_id, :accion, :tabla_afectada, :registro_id, :datos_anteriores, :datos_nuevos, :ip, :user_agent, :navegador)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':accion', $accion);
        $stmt->bindParam(':tabla_afectada', $tabla_afectada);
        $stmt->bindParam(':registro_id', $registro_id);
        $stmt->bindParam(':datos_anteriores', $datos_anteriores);
        $stmt->bindParam(':datos_nuevos', $datos_nuevos);
        $stmt->bindParam(':ip', $ip);
        $stmt->bindParam(':user_agent', $user_agent);
        $stmt->bindParam(':navegador', $navegador);
        return $stmt->execute();
    }

    private function getBrowser($user_agent) {
        if (strpos($user_agent, 'Chrome') !== false) return 'Chrome';
        if (strpos($user_agent, 'Firefox') !== false) return 'Firefox';
        if (strpos($user_agent, 'Safari') !== false) return 'Safari';
        if (strpos($user_agent, 'Edge') !== false) return 'Edge';
        if (strpos($user_agent, 'Opera') !== false) return 'Opera';
        return 'Otro';
    }

    public function obtenerTodos($limite = 100, $offset = 0, $filtros = []) {
        $sql = "SELECT a.*, u.nombre as usuario_nombre FROM " . $this->table . " a LEFT JOIN usuarios u ON a.usuario_id = u.id WHERE 1=1";
        $params = [];
        if (!empty($filtros['usuario_id'])) {
            $sql .= " AND a.usuario_id = :usuario_id";
            $params[':usuario_id'] = $filtros['usuario_id'];
        }
        if (!empty($filtros['accion'])) {
            $sql .= " AND a.accion LIKE :accion";
            $params[':accion'] = '%' . $filtros['accion'] . '%';
        }
        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND DATE(a.created_at) >= :fecha_desde";
            $params[':fecha_desde'] = $filtros['fecha_desde'];
        }
        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND DATE(a.created_at) <= :fecha_hasta";
            $params[':fecha_hasta'] = $filtros['fecha_hasta'];
        }
        $sql .= " ORDER BY a.created_at DESC LIMIT :limite OFFSET :offset";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contar($filtros = []) {
        $sql = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE 1=1";
        $params = [];
        if (!empty($filtros['usuario_id'])) {
            $sql .= " AND usuario_id = :usuario_id";
            $params[':usuario_id'] = $filtros['usuario_id'];
        }
        if (!empty($filtros['accion'])) {
            $sql .= " AND accion LIKE :accion";
            $params[':accion'] = '%' . $filtros['accion'] . '%';
        }
        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND DATE(created_at) >= :fecha_desde";
            $params[':fecha_desde'] = $filtros['fecha_desde'];
        }
        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND DATE(created_at) <= :fecha_hasta";
            $params[':fecha_hasta'] = $filtros['fecha_hasta'];
        }
        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['total'] : 0;
    }
}
?>