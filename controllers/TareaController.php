<?php
// Controlador de Tareas - Gestiona las operaciones CRUD de tareas
class TareaController {
    private $conn;

    // Constructor - Recibe la conexion a la base de datos
    public function __construct($db) {
        $this->conn = $db;
    }

    // Listar todas las tareas
    public function listar() {
        $tareaModel = new Tarea($this->conn);
        $tareas = $tareaModel->listar();
        if (!$tareas) {
            $tareas = [];
        }
        include_once __DIR__ . '/../views/tareas/listar.php';
    }

    // Mostrar formulario para agregar tarea
    public function agregar() {
        // Obtener lista de proyectos y usuarios para los selects
        $proyectoModel = new Proyecto($this->conn);
        $proyectos = $proyectoModel->listar();
        if (!$proyectos) {
            $proyectos = [];
        }
        $usuarioModel = new Usuario($this->conn);
        $usuarios = $usuarioModel->obtenerUsuarios();
        if (!$usuarios) {
            $usuarios = [];
        }
        
        // Procesar envio del formulario
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (empty($_POST['titulo'])) {
                $_SESSION['error'] = "El título de la tarea es obligatorio";
                header("Location: index.php?action=tareas&sub=agregar");
                exit();
            }
            if (empty($_POST['proyecto_id'])) {
                $_SESSION['error'] = "Debe seleccionar un proyecto";
                header("Location: index.php?action=tareas&sub=agregar");
                exit();
            }
            $tareaModel = new Tarea($this->conn);
            if ($tareaModel->agregar($_POST['proyecto_id'], $_POST['titulo'], $_POST['descripcion'], $_POST['asignado_a'], $_POST['fecha_limite'], $_POST['prioridad'], $_POST['estado'])) {
                $_SESSION['success'] = "Tarea agregada correctamente";
                header("Location: index.php?action=tareas");
                exit();
            } else {
                $_SESSION['error'] = "Error al agregar la tarea";
            }
        }
        include_once __DIR__ . '/../views/tareas/agregar.php';
    }

    // Mostrar formulario para editar tarea
    public function editar() {
        $id = $_GET['id'] ?? 0;
        $tareaModel = new Tarea($this->conn);
        $tarea = $tareaModel->obtenerPorId($id);
        
        // Verificar que la tarea existe
        if (!$tarea) {
            $_SESSION['error'] = "Tarea no encontrada";
            header("Location: index.php?action=tareas");
            exit();
        }
        
        // Obtener lista de proyectos y usuarios para los selects
        $proyectoModel = new Proyecto($this->conn);
        $proyectos = $proyectoModel->listar();
        if (!$proyectos) {
            $proyectos = [];
        }
        $usuarioModel = new Usuario($this->conn);
        $usuarios = $usuarioModel->obtenerUsuarios();
        if (!$usuarios) {
            $usuarios = [];
        }
        
        // Procesar envio del formulario
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (empty($_POST['titulo'])) {
                $_SESSION['error'] = "El título de la tarea es obligatorio";
                header("Location: index.php?action=tareas&sub=editar&id=" . $id);
                exit();
            }
            if (empty($_POST['proyecto_id'])) {
                $_SESSION['error'] = "Debe seleccionar un proyecto";
                header("Location: index.php?action=tareas&sub=editar&id=" . $id);
                exit();
            }
            if ($tareaModel->actualizar($id, $_POST['proyecto_id'], $_POST['titulo'], $_POST['descripcion'], $_POST['asignado_a'], $_POST['fecha_limite'], $_POST['prioridad'], $_POST['estado'])) {
                $_SESSION['success'] = "Tarea actualizada correctamente";
                header("Location: index.php?action=tareas");
                exit();
            } else {
                $_SESSION['error'] = "Error al actualizar la tarea";
            }
        }
        include_once __DIR__ . '/../views/tareas/editar.php';
    }

    // Eliminar una tarea
    public function eliminar() {
        $id = $_GET['id'] ?? 0;
        $tareaModel = new Tarea($this->conn);
        if ($tareaModel->eliminar($id)) {
            $_SESSION['success'] = "Tarea eliminada correctamente";
        } else {
            $_SESSION['error'] = "Error al eliminar la tarea";
        }
        header("Location: index.php?action=tareas");
        exit();
    }

    // Obtener tareas por proyecto (para selects dinamicos)
    public function porProyecto() {
        $proyecto_id = $_GET['proyecto_id'] ?? 0;
        $tareaModel = new Tarea($this->conn);
        $tareas = $tareaModel->listar($proyecto_id);
        header('Content-Type: application/json');
        echo json_encode($tareas);
        exit();
    }
}
?>