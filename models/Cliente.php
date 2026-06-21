<?php
// Modelo de Cliente - Gestiona las operaciones con la tabla clientes
class Cliente {
    private $conn;
    private $table = 'clientes';

    // Constructor - Recibe la conexion a la base de datos
    public function __construct($db) {
        $this->conn = $db;
    }

    // Listar todos los clientes ordenados por ID descendente
    public function listar() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtener un cliente por su ID
    public function obtenerPorId($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Validar formato de telefono (7 a 15 digitos, opcional +)
    public function validarTelefono($telefono) {
        if (empty($telefono)) return true;
        $limpio = preg_replace('/[\s\-\(\)]/', '', $telefono);
        return preg_match('/^[+]?[0-9]{7,15}$/', $limpio);
    }

    // Limpiar telefono eliminando espacios, guiones y parentesis
    public function limpiarTelefono($telefono) {
        if (empty($telefono)) return null;
        return preg_replace('/[\s\-\(\)]/', '', $telefono);
    }

    // Agregar un nuevo cliente
    public function agregar($nombre, $email, $telefono, $empresa) {
        // Validar telefono antes de insertar
        if (!$this->validarTelefono($telefono)) {
            return ['success' => false, 'message' => 'El teléfono debe tener 7 a 15 dígitos'];
        }
        $telefonoLimpio = $this->limpiarTelefono($telefono);
        
        $query = "INSERT INTO " . $this->table . " (nombre, email, telefono, empresa) VALUES (:nombre, :email, :telefono, :empresa)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telefono', $telefonoLimpio);
        $stmt->bindParam(':empresa', $empresa);
        
        if ($stmt->execute()) {
            return ['success' => true, 'id' => $this->conn->lastInsertId()];
        }
        return ['success' => false, 'message' => 'Error al agregar el cliente'];
    }

    // Actualizar un cliente existente
    public function actualizar($id, $nombre, $email, $telefono, $empresa) {
        // Validar telefono antes de actualizar
        if (!$this->validarTelefono($telefono)) {
            return ['success' => false, 'message' => 'El teléfono debe tener 7 a 15 dígitos'];
        }
        $telefonoLimpio = $this->limpiarTelefono($telefono);
        
        $query = "UPDATE " . $this->table . " SET nombre = :nombre, email = :email, telefono = :telefono, empresa = :empresa WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telefono', $telefonoLimpio);
        $stmt->bindParam(':empresa', $empresa);
        
        if ($stmt->execute()) {
            return ['success' => true];
        }
        return ['success' => false, 'message' => 'Error al actualizar el cliente'];
    }

    // Eliminar un cliente por su ID
    public function eliminar($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Contar el total de clientes
    public function contar() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }
}
?>