<?php
// Controlador de API - Maneja las peticiones AJAX
class ApiController {
    private $conn;

    // Constructor - Recibe la conexion a la base de datos
    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener estadisticas para los reportes
    public function stats() {
        header('Content-Type: application/json');
        try {
            $clienteModel = new Cliente($this->conn);
            $proyectoModel = new Proyecto($this->conn);
            $stats = [
                'clientes' => $clienteModel->contar(),
                'proyectos' => $proyectoModel->contar(),
                'proyectos_pendientes' => $proyectoModel->contarPorEstado('pendiente'),
                'proyectos_en_progreso' => $proyectoModel->contarPorEstado('en_progreso'),
                'proyectos_completados' => $proyectoModel->contarPorEstado('completado'),
                'tareas' => 0
            ];
            try {
                $stmt = $this->conn->query("SHOW TABLES LIKE 'tareas'");
                if ($stmt->rowCount() > 0) {
                    $tareaModel = new Tarea($this->conn);
                    $tareas = $tareaModel->listar();
                    $stats['tareas'] = is_array($tareas) ? count($tareas) : 0;
                }
            } catch (PDOException $e) {
                $stats['tareas'] = 0;
            }
            echo json_encode(['success' => true, 'stats' => $stats]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit();
    }

    // Obtener datos para el dashboard
    public function dashboard() {
        header('Content-Type: application/json');
        try {
            $clienteModel = new Cliente($this->conn);
            $proyectoModel = new Proyecto($this->conn);
            $stats = [
                'clientes' => $clienteModel->contar(),
                'proyectos' => $proyectoModel->contar(),
                'proyectos_pendientes' => $proyectoModel->contarPorEstado('pendiente'),
                'proyectos_en_progreso' => $proyectoModel->contarPorEstado('en_progreso'),
                'proyectos_completados' => $proyectoModel->contarPorEstado('completado'),
                'tareas_pendientes' => 0,
                'tareas_vencidas' => 0,
                'tareas_baja' => 0,
                'tareas_media' => 0,
                'tareas_alta' => 0
            ];
            try {
                $stmt = $this->conn->query("SHOW TABLES LIKE 'tareas'");
                if ($stmt->rowCount() > 0) {
                    $tareaModel = new Tarea($this->conn);
                    $stats['tareas_pendientes'] = $tareaModel->contarPorEstado('pendiente');
                    $stats['tareas_vencidas'] = count($tareaModel->tareasVencidas());
                    $query = "SELECT prioridad, COUNT(*) as total FROM tareas GROUP BY prioridad";
                    foreach ($this->conn->query($query) as $row) {
                        if ($row['prioridad'] == 'baja') $stats['tareas_baja'] = $row['total'];
                        if ($row['prioridad'] == 'media') $stats['tareas_media'] = $row['total'];
                        if ($row['prioridad'] == 'alta') $stats['tareas_alta'] = $row['total'];
                    }
                }
            } catch (PDOException $e) {}
            echo json_encode(['success' => true, 'stats' => $stats]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit();
    }

    // Obtener actividad reciente (auditoria)
    public function actividad() {
        header('Content-Type: application/json');
        try {
            $query = "SELECT a.*, u.nombre as usuario 
                      FROM auditoria a 
                      LEFT JOIN usuarios u ON a.usuario_id = u.id 
                      ORDER BY a.created_at DESC 
                      LIMIT 10";
            $stmt = $this->conn->query($query);
            $actividad = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $resultado = array_map(function($item) {
                return [
                    'fecha' => date('d/m/Y H:i', strtotime($item['created_at'])),
                    'usuario' => $item['usuario'] ?? 'Sistema',
                    'accion' => $item['accion'],
                    'detalle' => $item['tabla_afectada'] ?? ''
                ];
            }, $actividad);
            echo json_encode(['success' => true, 'actividad' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'actividad' => []]);
        }
        exit();
    }

    // Buscar clientes en tiempo real
    public function buscarClientes() {
        header('Content-Type: application/json');
        try {
            $query = "SELECT * FROM clientes WHERE 1=1";
            $params = [];
            
            if (!empty($_GET['q'])) {
                $query .= " AND (nombre LIKE :busqueda OR empresa LIKE :busqueda OR email LIKE :busqueda)";
                $params[':busqueda'] = '%' . $_GET['q'] . '%';
            }
            
            $query .= " ORDER BY id DESC";
            
            $stmt = $this->conn->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            $clientes = $stmt->fetchAll();
            
            $countQuery = "SELECT COUNT(*) as total FROM clientes";
            $countStmt = $this->conn->query($countQuery);
            $total = $countStmt->fetch()['total'];
            
            echo json_encode([
                'success' => true,
                'clientes' => $clientes,
                'total' => $total
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit();
    }

    // Buscar proyectos en tiempo real
    public function buscarProyectos() {
        header('Content-Type: application/json');
        try {
            $query = "SELECT p.*, c.nombre as cliente_nombre 
                      FROM proyectos p 
                      LEFT JOIN clientes c ON p.cliente_id = c.id 
                      WHERE 1=1";
            $params = [];
            
            if (!empty($_GET['q'])) {
                $query .= " AND (p.nombre LIKE :busqueda OR c.nombre LIKE :busqueda)";
                $params[':busqueda'] = '%' . $_GET['q'] . '%';
            }
            
            if (!empty($_GET['estado'])) {
                $query .= " AND p.estado = :estado";
                $params[':estado'] = $_GET['estado'];
            }
            
            $query .= " ORDER BY p.id DESC";
            
            $stmt = $this->conn->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            $proyectos = $stmt->fetchAll();
            
            $countQuery = "SELECT COUNT(*) as total FROM proyectos";
            $countStmt = $this->conn->query($countQuery);
            $total = $countStmt->fetch()['total'];
            
            echo json_encode([
                'success' => true,
                'proyectos' => $proyectos,
                'total' => $total
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit();
    }
}
?>