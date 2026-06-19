<?php
class UsuarioController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function index() {
        if (!in_array($_SESSION['user_rol'] ?? 'usuario', ['super_admin', 'admin'])) {
            $_SESSION['error'] = "No tienes permisos para gestionar usuarios";
            header("Location: index.php?action=dashboard");
            exit();
        }
        $usuarioModel = new Usuario($this->conn);
        $usuarios = $usuarioModel->obtenerUsuarios();
        include_once __DIR__ . '/../views/usuarios/listar.php';
    }

    public function cambiarRol() {
        if (!in_array($_SESSION['user_rol'] ?? 'usuario', ['super_admin'])) {
            $_SESSION['error'] = "Solo Super Admin puede cambiar roles";
            header("Location: index.php?action=usuarios");
            exit();
        }
        
        $id = $_GET['id'] ?? 0;
        $rol = $_GET['rol'] ?? 'usuario';
        
        $rolesPermitidos = ['usuario', 'admin', 'super_admin'];
        if (!in_array($rol, $rolesPermitidos)) {
            $_SESSION['error'] = "Rol no válido";
            header("Location: index.php?action=usuarios");
            exit();
        }
        
        if ($id == $_SESSION['user_id']) {
            $_SESSION['error'] = "No puedes cambiar tu propio rol";
            header("Location: index.php?action=usuarios");
            exit();
        }
        
        $query = "UPDATE usuarios SET rol = :rol WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':rol', $rol);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Rol actualizado correctamente a: " . ucfirst($rol);
        } else {
            $_SESSION['error'] = "Error al actualizar rol";
        }
        header("Location: index.php?action=usuarios");
        exit();
    }

    public function eliminar() {
        if (!in_array($_SESSION['user_rol'] ?? 'usuario', ['super_admin'])) {
            $_SESSION['error'] = "No tienes permisos para eliminar usuarios";
            header("Location: index.php?action=usuarios");
            exit();
        }
        
        $id = $_GET['id'] ?? 0;
        
        if ($id == $_SESSION['user_id']) {
            $_SESSION['error'] = "No puedes eliminar tu propio usuario";
            header("Location: index.php?action=usuarios");
            exit();
        }
        
        $check = $this->conn->prepare("SELECT id FROM usuarios WHERE id = :id");
        $check->bindParam(':id', $id);
        $check->execute();
        
        if ($check->rowCount() == 0) {
            $_SESSION['error'] = "Usuario no encontrado";
            header("Location: index.php?action=usuarios");
            exit();
        }
        
        $query = "DELETE FROM usuarios WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Usuario eliminado correctamente";
        } else {
            $_SESSION['error'] = "Error al eliminar usuario";
        }
        header("Location: index.php?action=usuarios");
        exit();
    }
}
?>