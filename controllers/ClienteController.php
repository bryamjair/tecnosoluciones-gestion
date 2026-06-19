<?php
class ClienteController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function listar() {
        $clienteModel = new Cliente($this->conn);
        $clientes = $clienteModel->listar();
        include_once __DIR__ . '/../views/clientes/listar.php';
    }

    public function agregar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (empty($_POST['nombre'])) {
                $_SESSION['error'] = "El nombre del cliente es obligatorio";
                header("Location: index.php?action=clientes&sub=agregar");
                exit();
            }
            if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "El email no tiene un formato válido";
                header("Location: index.php?action=clientes&sub=agregar");
                exit();
            }
            if (!empty($_POST['telefono'])) {
                $telefono = preg_replace('/[\s\-]/', '', $_POST['telefono']);
                if (!preg_match('/^[+]?[0-9]{7,15}$/', $telefono)) {
                    $_SESSION['error'] = "El teléfono debe contener solo números (7 a 15 dígitos)";
                    header("Location: index.php?action=clientes&sub=agregar");
                    exit();
                }
            }
            $clienteModel = new Cliente($this->conn);
            if ($clienteModel->agregar($_POST['nombre'], $_POST['email'], $_POST['telefono'], $_POST['empresa'])) {
                $_SESSION['success'] = "Cliente agregado correctamente";
                header("Location: index.php?action=clientes");
                exit();
            } else {
                if (!isset($_SESSION['error'])) {
                    $_SESSION['error'] = "Error al agregar el cliente";
                }
                header("Location: index.php?action=clientes&sub=agregar");
                exit();
            }
        }
        include_once __DIR__ . '/../views/clientes/agregar.php';
    }

    public function editar() {
        $id = $_GET['id'] ?? 0;
        $clienteModel = new Cliente($this->conn);
        $cliente = $clienteModel->obtenerPorId($id);
        if (!$cliente) {
            $_SESSION['error'] = "Cliente no encontrado";
            header("Location: index.php?action=clientes");
            exit();
        }
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
            if (!empty($_POST['telefono'])) {
                $telefono = preg_replace('/[\s\-]/', '', $_POST['telefono']);
                if (!preg_match('/^[+]?[0-9]{7,15}$/', $telefono)) {
                    $_SESSION['error'] = "El teléfono debe contener solo números (7 a 15 dígitos)";
                    header("Location: index.php?action=clientes&sub=editar&id=" . $id);
                    exit();
                }
            }
            if ($clienteModel->actualizar($id, $_POST['nombre'], $_POST['email'], $_POST['telefono'], $_POST['empresa'])) {
                $_SESSION['success'] = "Cliente actualizado correctamente";
                header("Location: index.php?action=clientes");
                exit();
            } else {
                if (!isset($_SESSION['error'])) {
                    $_SESSION['error'] = "Error al actualizar el cliente";
                }
                header("Location: index.php?action=clientes&sub=editar&id=" . $id);
                exit();
            }
        }
        include_once __DIR__ . '/../views/clientes/editar.php';
    }

    public function eliminar() {
        $id = $_GET['id'] ?? 0;
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