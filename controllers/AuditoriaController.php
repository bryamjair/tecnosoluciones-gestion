<?php
class AuditoriaController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function index() {
        if (!in_array($_SESSION['user_rol'] ?? 'usuario', ['super_admin', 'admin'])) {
            $_SESSION['error'] = "No tienes permisos para ver la auditoría";
            header("Location: index.php?action=dashboard");
            exit();
        }
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        $filtros = [];
        if (!empty($_GET['usuario_id'])) {
            $filtros['usuario_id'] = (int)$_GET['usuario_id'];
        }
        if (!empty($_GET['accion'])) {
            $filtros['accion'] = trim($_GET['accion']);
        }
        if (!empty($_GET['fecha_desde'])) {
            $filtros['fecha_desde'] = $_GET['fecha_desde'];
        }
        if (!empty($_GET['fecha_hasta'])) {
            $filtros['fecha_hasta'] = $_GET['fecha_hasta'];
        }
        $auditoriaModel = new Auditoria($this->conn);
        $auditorias = $auditoriaModel->obtenerTodos($limit, $offset, $filtros);
        $total = $auditoriaModel->contar($filtros);
        $totalPages = ceil($total / $limit);
        $query = "SELECT id, nombre FROM usuarios ORDER BY nombre";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        include_once __DIR__ . '/../views/auditoria/index.php';
    }
}
?>