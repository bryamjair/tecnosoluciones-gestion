<?php
// Clase de conexion a la base de datos
class Database {
    // Configuracion de la conexion
    private $host = 'localhost';
    private $db_name = 'proyectofinal';
    private $username = 'root';
    private $password = '';
    private $conn;

    // Metodo para obtener la conexion
    public function getConnection() {
        $this->conn = null;
        try {
            // Crear conexion PDO
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                $this->username,
                $this->password
            );
            // Configurar atributos de PDO
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            // Mostrar error si falla la conexion
            echo "Error de conexión: " . $e->getMessage();
        }
        return $this->conn;
    }
}
?>