<?php
// Controlador de Clientes - Gestiona las operaciones CRUD de clientes
class ClienteController {
    private $conn;

    // Constructor - Recibe la conexion a la base de datos
    public function __construct($db) {
        $this->conn = $db;
    }

    // Listar todos los clientes
    public function listar() {
        $clienteModel = new Cliente($this->conn);
        $clientes = $clienteModel->listar();
        include_once __DIR__ . '/../views/clientes/listar.php';
    }

    // Buscar clientes via AJAX
    public function buscar() {
        header('Content-Type: application/json');
        try {
            $clienteModel = new Cliente($this->conn);
            $clientes = $clienteModel->listar();
            
            $busqueda = isset($_GET['q']) ? $_GET['q'] : '';
            // Filtrar clientes por nombre, empresa o email
            if ($busqueda) {
                $clientes = array_filter($clientes, function($c) use ($busqueda) {
                    return stripos($c['nombre'], $busqueda) !== false || 
                           stripos($c['empresa'], $busqueda) !== false ||
                           stripos($c['email'], $busqueda) !== false;
                });
            }
            
            echo json_encode([
                'success' => true,
                'clientes' => array_values($clientes),
                'total' => count($clientes)
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit();
    }

    // Mostrar formulario para agregar cliente
    public function agregar() {
        // Procesar envio del formulario
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validar nombre obligatorio
            if (empty($_POST['nombre'])) {
                $_SESSION['error'] = "El nombre del cliente es obligatorio";
                header("Location: index.php?action=clientes&sub=agregar");
                exit();
            }
            
            // Validar formato de email
            if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "El email no tiene un formato válido";
                header("Location: index.php?action=clientes&sub=agregar");
                exit();
            }
            
            $clienteModel = new Cliente($this->conn);
            $result = $clienteModel->agregar(
                $_POST['nombre'], 
                $_POST['email'], 
                $_POST['telefono'], 
                $_POST['empresa']
            );
            
            if ($result['success']) {
                $_SESSION['success'] = "Cliente agregado correctamente";
                header("Location: index.php?action=clientes");
                exit();
            } else {
                $_SESSION['error'] = $result['message'];
                header("Location: index.php?action=clientes&sub=agregar");
                exit();
            }
        }
        include_once __DIR__ . '/../views/clientes/agregar.php';
    }

    // Mostrar formulario para editar cliente
    public function editar() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $clienteModel = new Cliente($this->conn);
        $cliente = $clienteModel->obtenerPorId($id);
        
        // Verificar que el cliente existe
        if (!$cliente) {
            $_SESSION['error'] = "Cliente no encontrado";
            header("Location: index.php?action=clientes");
            exit();
        }
        
        // Procesar envio del formulario
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (empty($_POST['nombre'])) {
                $_SESSION['error'] = "El nombre del cliente es obligatorio";
                header("Location: index.php?action=clientes&sub=editar&id=" . $id);
                exit();
            }
            
            if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "El email no tiene un formato válido";
                header("Location: index.php?action=clientes&sub=editar&id=" . $id);
                exit();
            }
            
            $result = $clienteModel->actualizar(
                $id, 
                $_POST['nombre'], 
                $_POST['email'], 
                $_POST['telefono'], 
                $_POST['empresa']
            );
            
            if ($result['success']) {
                $_SESSION['success'] = "Cliente actualizado correctamente";
                header("Location: index.php?action=clientes");
                exit();
            } else {
                $_SESSION['error'] = $result['message'];
                header("Location: index.php?action=clientes&sub=editar&id=" . $id);
                exit();
            }
        }
        include_once __DIR__ . '/../views/clientes/editar.php';
    }

    // Eliminar un cliente
    public function eliminar() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $clienteModel = new Cliente($this->conn);
        if ($clienteModel->eliminar($id)) {
            $_SESSION['success'] = "Cliente eliminado correctamente";
        } else {
            $_SESSION['error'] = "Error al eliminar el cliente";
        }
        header("Location: index.php?action=clientes");
        exit();
    }
}
?>