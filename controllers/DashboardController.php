<?php
class DashboardController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function index() {
        include_once __DIR__ . '/../views/dashboard/index.php';
    }
}
?>