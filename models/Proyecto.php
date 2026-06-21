<?php
// Modelo de Proyecto - Gestiona las operaciones con la tabla proyectos
class Proyecto {
    private $conn;
    private $table = 'proyectos';

    // Constructor - Recibe la conexion a la base de datos
    public function __construct($db) {
        $this->conn = $db;
    }

    // Listar todos los proyectos con el nombre del cliente asociado
    public function listar() {
        $query = "SELECT p.*, c.nombre as cliente_nombre FROM " . $this->table . " p LEFT JOIN clientes c ON p.cliente_id = c.id ORDER BY p.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtener un proyecto por su ID
    public function obtenerPorId($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Agregar un nuevo proyecto
    public function agregar($nombre, $descripcion, $cliente_id, $fecha_inicio, $fecha_fin, $estado) {
        $query = "INSERT INTO " . $this->table . " (nombre, descripcion, cliente_id, fecha_inicio, fecha_fin, estado) VALUES (:nombre, :descripcion, :cliente_id, :fecha_inicio, :fecha_fin, :estado)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':cliente_id', $cliente_id);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt->bindParam(':fecha_fin', $fecha_fin);
        $stmt->bindParam(':estado', $estado);
        return $stmt->execute();
    }

    // Actualizar un proyecto existente
    public function actualizar($id, $nombre, $descripcion, $cliente_id, $fecha_inicio, $fecha_fin, $estado) {
        $query = "UPDATE " . $this->table . " SET nombre = :nombre, descripcion = :descripcion, cliente_id = :cliente_id, fecha_inicio = :fecha_inicio, fecha_fin = :fecha_fin, estado = :estado WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':cliente_id', $cliente_id);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt->bindParam(':fecha_fin', $fecha_fin);
        $stmt->bindParam(':estado', $estado);
        return $stmt->execute();
    }

    // Eliminar un proyecto por su ID
    public function eliminar($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Contar el total de proyectos
    public function contar() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    // Contar proyectos por estado
    public function contarPorEstado($estado) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE estado = :estado";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':estado', $estado);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }
}
?>