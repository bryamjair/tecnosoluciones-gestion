<?php
class Cliente {
    private $conn;
    private $table = 'clientes';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function listar() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function obtenerPorId($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    private function validarTelefono($telefono) {
        return preg_match('/^[\+\d][\d\s\-]{6,}$/', $telefono);
    }

    private function limpiarTelefono($telefono) {
        return preg_replace('/[\s\-]/', '', $telefono);
    }

    public function agregar($nombre, $email, $telefono, $empresa) {
        if (!empty($telefono) && !$this->validarTelefono($telefono)) {
            $_SESSION['error'] = "El teléfono solo debe contener números, espacios o guiones. Ejemplo: +56912345678 o 912345678";
            return false;
        }
        $telefonoLimpio = !empty($telefono) ? $this->limpiarTelefono($telefono) : null;
        $query = "INSERT INTO " . $this->table . " (nombre, email, telefono, empresa) VALUES (:nombre, :email, :telefono, :empresa)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telefono', $telefonoLimpio);
        $stmt->bindParam(':empresa', $empresa);
        return $stmt->execute();
    }

    public function actualizar($id, $nombre, $email, $telefono, $empresa) {
        if (!empty($telefono) && !$this->validarTelefono($telefono)) {
            $_SESSION['error'] = "El teléfono solo debe contener números, espacios o guiones. Ejemplo: +56912345678 o 912345678";
            return false;
        }
        $telefonoLimpio = !empty($telefono) ? $this->limpiarTelefono($telefono) : null;
        $query = "UPDATE " . $this->table . " SET nombre = :nombre, email = :email, telefono = :telefono, empresa = :empresa WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telefono', $telefonoLimpio);
        $stmt->bindParam(':empresa', $empresa);
        return $stmt->execute();
    }

    public function eliminar($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function contar() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }
}
?>