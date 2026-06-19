<?php
class ProyectoController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function listar() {
        $proyectoModel = new Proyecto($this->conn);
        $proyectos = $proyectoModel->listar();
        if (!$proyectos) {
            $proyectos = [];
        }
        include_once __DIR__ . '/../views/proyectos/listar.php';
    }

    public function agregar() {
        $clienteModel = new Cliente($this->conn);
        $clientes = $clienteModel->listar();
        if (!$clientes) {
            $clientes = [];
        }
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

    public function editar() {
        $id = $_GET['id'] ?? 0;
        $proyectoModel = new Proyecto($this->conn);
        $proyecto = $proyectoModel->obtenerPorId($id);
        if (!$proyecto) {
            $_SESSION['error'] = "Proyecto no encontrado";
            header("Location: index.php?action=proyectos");
            exit();
        }
        $clienteModel = new Cliente($this->conn);
        $clientes = $clienteModel->listar();
        if (!$clientes) {
            $clientes = [];
        }
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

    public function eliminar() {
        $id = $_GET['id'] ?? 0;
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