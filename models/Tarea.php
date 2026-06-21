<?php
// Modelo de Tarea - Gestiona las operaciones con la tabla tareas
class Tarea {
    private $conn;
    private $table = 'tareas';

    // Constructor - Recibe la conexion a la base de datos
    public function __construct($db) {
        $this->conn = $db;
    }

    // Listar todas las tareas con proyecto y usuario asignado
    public function listar($proyecto_id = null) {
        $sql = "SELECT t.*, p.nombre as proyecto_nombre, u.nombre as asignado_nombre FROM " . $this->table . " t LEFT JOIN proyectos p ON t.proyecto_id = p.id LEFT JOIN usuarios u ON t.asignado_a = u.id";
        // Filtrar por proyecto si se especifica
        if ($proyecto_id) {
            $sql .= " WHERE t.proyecto_id = :proyecto_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':proyecto_id', $proyecto_id);
        } else {
            $stmt = $this->conn->prepare($sql);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtener una tarea por su ID
    public function obtenerPorId($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Agregar una nueva tarea
    public function agregar($proyecto_id, $titulo, $descripcion, $asignado_a, $fecha_limite, $prioridad, $estado) {
        $query = "INSERT INTO " . $this->table . " (proyecto_id, titulo, descripcion, asignado_a, fecha_limite, prioridad, estado) VALUES (:proyecto_id, :titulo, :descripcion, :asignado_a, :fecha_limite, :prioridad, :estado)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':proyecto_id', $proyecto_id);
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':asignado_a', $asignado_a);
        $stmt->bindParam(':fecha_limite', $fecha_limite);
        $stmt->bindParam(':prioridad', $prioridad);
        $stmt->bindParam(':estado', $estado);
        return $stmt->execute();
    }

    // Actualizar una tarea existente
    public function actualizar($id, $proyecto_id, $titulo, $descripcion, $asignado_a, $fecha_limite, $prioridad, $estado) {
        $query = "UPDATE " . $this->table . " SET proyecto_id = :proyecto_id, titulo = :titulo, descripcion = :descripcion, asignado_a = :asignado_a, fecha_limite = :fecha_limite, prioridad = :prioridad, estado = :estado WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':proyecto_id', $proyecto_id);
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':asignado_a', $asignado_a);
        $stmt->bindParam(':fecha_limite', $fecha_limite);
        $stmt->bindParam(':prioridad', $prioridad);
        $stmt->bindParam(':estado', $estado);
        return $stmt->execute();
    }

    // Eliminar una tarea por su ID
    public function eliminar($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Contar tareas por estado
    public function contarPorEstado($estado, $proyecto_id = null) {
        $sql = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE estado = :estado";
        if ($proyecto_id) {
            $sql .= " AND proyecto_id = :proyecto_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':proyecto_id', $proyecto_id);
        } else {
            $stmt = $this->conn->prepare($sql);
        }
        $stmt->bindParam(':estado', $estado);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    // Obtener tareas vencidas (fecha limite menor a hoy y no completadas)
    public function tareasVencidas() {
        $query = "SELECT t.*, p.nombre as proyecto_nombre FROM " . $this->table . " t LEFT JOIN proyectos p ON t.proyecto_id = p.id WHERE t.fecha_limite < CURDATE() AND t.estado != 'completada'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>