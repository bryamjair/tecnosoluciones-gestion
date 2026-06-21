<?php
// Controlador de Proyectos - Gestiona las operaciones CRUD de proyectos
class ProyectoController {
    private $conn;

    // Constructor - Recibe la conexion a la base de datos
    public function __construct($db) {
        $this->conn = $db;
    }

    // Listar todos los proyectos
    public function listar() {
        $proyectoModel = new Proyecto($this->conn);
        $proyectos = $proyectoModel->listar();
        if (!$proyectos) {
            $proyectos = [];
        }
        include_once __DIR__ . '/../views/proyectos/listar.php';
    }

    // Buscar proyectos via AJAX
    public function buscar() {
        header('Content-Type: application/json');
        try {
            $proyectoModel = new Proyecto($this->conn);
            $proyectos = $proyectoModel->listar();
            
            $busqueda = isset($_GET['q']) ? $_GET['q'] : '';
            // Filtrar proyectos por nombre o cliente
            if ($busqueda) {
                $proyectos = array_filter($proyectos, function($p) use ($busqueda) {
                    return stripos($p['nombre'], $busqueda) !== false || 
                           stripos($p['cliente_nombre'] ?? '', $busqueda) !== false;
                });
            }
            
            echo json_encode([
                'success' => true,
                'proyectos' => array_values($proyectos),
                'total' => count($proyectos)
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit();
    }

    // Mostrar formulario para agregar proyecto
    public function agregar() {
        // Obtener lista de clientes para el select
        $clienteModel = new Cliente($this->conn);
        $clientes = $clienteModel->listar();
        if (!$clientes) {
            $clientes = [];
        }
        
        // Procesar envio del formulario
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (empty($_POST['nombre'])) {
                $_SESSION['error'] = "El nombre del proyecto es obligatorio";
                header("Location: index.php?action=proyectos&sub=agregar");
                exit();
            }
            if (empty($_POST['cliente_id'])) {
                $_SESSION['error'] = "Debe seleccionar un cliente";
                header("Location: index.php?action=proyectos&sub=agregar");
                exit();
            }
            $proyectoModel = new Proyecto($this->conn);
            if ($proyectoModel->agregar($_POST['nombre'], $_POST['descripcion'], $_POST['cliente_id'], $_POST['fecha_inicio'], $_POST['fecha_fin'], $_POST['estado'])) {
                $_SESSION['success'] = "Proyecto agregado correctamente";
                header("Location: index.php?action=proyectos");
                exit();
            } else {
                $_SESSION['error'] = "Error al agregar el proyecto";
            }
        }
        include_once __DIR__ . '/../views/proyectos/agregar.php';
    }

    // Mostrar formulario para editar proyecto
    public function editar() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $proyectoModel = new Proyecto($this->conn);
        $proyecto = $proyectoModel->obtenerPorId($id);
        
        // Verificar que el proyecto existe
        if (!$proyecto) {
            $_SESSION['error'] = "Proyecto no encontrado";
            header("Location: index.php?action=proyectos");
            exit();
        }
        
        // Obtener lista de clientes para el select
        $clienteModel = new Cliente($this->conn);
        $clientes = $clienteModel->listar();
        if (!$clientes) {
            $clientes = [];
        }
        
        // Procesar envio del formulario
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (empty($_POST['nombre'])) {
                $_SESSION['error'] = "El nombre del proyecto es obligatorio";
                header("Location: index.php?action=proyectos&sub=editar&id=" . $id);
                exit();
            }
            if (empty($_POST['cliente_id'])) {
                $_SESSION['error'] = "Debe seleccionar un cliente";
                header("Location: index.php?action=proyectos&sub=editar&id=" . $id);
                exit();
            }
            if ($proyectoModel->actualizar($id, $_POST['nombre'], $_POST['descripcion'], $_POST['cliente_id'], $_POST['fecha_inicio'], $_POST['fecha_fin'], $_POST['estado'])) {
                $_SESSION['success'] = "Proyecto actualizado correctamente";
                header("Location: index.php?action=proyectos");
                exit();
            } else {
                $_SESSION['error'] = "Error al actualizar el proyecto";
            }
        }
        include_once __DIR__ . '/../views/proyectos/editar.php';
    }

    // Eliminar un proyecto
    public function eliminar() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $proyectoModel = new Proyecto($this->conn);
        if ($proyectoModel->eliminar($id)) {
            $_SESSION['success'] = "Proyecto eliminado correctamente";
        } else {
            $_SESSION['error'] = "Error al eliminar el proyecto";
        }
        header("Location: index.php?action=proyectos");
        exit();
    }
}
?>