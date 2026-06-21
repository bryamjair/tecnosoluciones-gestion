<?php
// Controlador del Dashboard - Muestra la pagina principal
class DashboardController {
    private $conn;

    // Constructor - Recibe la conexion a la base de datos
    public function __construct($db) {
        $this->conn = $db;
    }

    // Mostrar el dashboard
    public function index() {
        include_once __DIR__ . '/../views/dashboard/index.php';
    }
}
?>