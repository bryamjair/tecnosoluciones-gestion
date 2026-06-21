<?php
// Modelo de Usuario - Gestiona las operaciones con la tabla usuarios
class Usuario {
    private $conn;
    private $table = 'usuarios';

    // Constructor - Recibe la conexion a la base de datos
    public function __construct($db) {
        $this->conn = $db;
    }

    // Registrar un nuevo usuario
    public function registrar($nombre, $email, $password) {
        // Encriptar la contraseña
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO " . $this->table . " (nombre, email, password) VALUES (:nombre, :email, :password)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hash);
        return $stmt->execute();
    }

    // Login de usuario - Verifica credenciales
    public function login($email, $password) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        // Verificar contraseña
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    // Verificar si un email ya existe
    public function emailExiste($email) {
        $query = "SELECT id FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Obtener usuario por email
    public function obtenerPorEmail($email) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Guardar token de recuperacion de contraseña
    public function guardarToken($id, $token) {
        $query = "UPDATE " . $this->table . " SET reset_token = :token, token_expira = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Obtener usuario por token de recuperacion
    public function obtenerPorToken($token) {
        $query = "SELECT * FROM " . $this->table . " WHERE reset_token = :token AND token_expira > NOW()";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Restablecer contraseña usando token
    public function resetPassword($token, $nueva_password) {
        $hash = password_hash($nueva_password, PASSWORD_DEFAULT);
        $query = "UPDATE " . $this->table . " SET password = :password, reset_token = NULL, token_expira = NULL WHERE reset_token = :token AND token_expira > NOW()";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':password', $hash);
        $stmt->bindParam(':token', $token);
        return $stmt->execute();
    }

    // Actualizar perfil de usuario
    public function actualizarPerfil($id, $nombre, $email, $telefono) {
        $query = "UPDATE " . $this->table . " SET nombre = :nombre, email = :email, telefono = :telefono WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telefono', $telefono);
        return $stmt->execute();
    }

    // Cambiar contraseña de usuario
    public function cambiarPassword($id, $nueva_password) {
        $hash = password_hash($nueva_password, PASSWORD_DEFAULT);
        $query = "UPDATE " . $this->table . " SET password = :password WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':password', $hash);
        return $stmt->execute();
    }

    // Obtener lista de todos los usuarios
    public function obtenerUsuarios() {
        $query = "SELECT id, nombre, email, telefono, rol FROM " . $this->table . " ORDER BY id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Actualizar ultimo acceso del usuario
    public function actualizarUltimoAcceso($id) {
        $query = "UPDATE " . $this->table . " SET ultimo_acceso = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>