<?php
class PerfilController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function index() {
        $query = "SELECT * FROM usuarios WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $_SESSION['user_id']);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$usuario) {
            $usuario = [
                'id' => $_SESSION['user_id'],
                'nombre' => $_SESSION['user_nombre'],
                'email' => $_SESSION['user_email'],
                'telefono' => '',
                'created_at' => date('Y-m-d H:i:s'),
                'ultimo_acceso' => null,
                'rol' => 'usuario'
            ];
        }
        include_once __DIR__ . '/../views/perfil/index.php';
    }

    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = trim($_POST['nombre']);
            $email = trim($_POST['email']);
            $telefono = trim($_POST['telefono']);
            $query = "UPDATE usuarios SET nombre = :nombre, email = :email, telefono = :telefono WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':id', $_SESSION['user_id']);
            if ($stmt->execute()) {
                $_SESSION['user_nombre'] = $nombre;
                $_SESSION['user_email'] = $email;
                $_SESSION['success'] = "Perfil actualizado correctamente";
            } else {
                $_SESSION['error'] = "Error al actualizar perfil";
            }
            header("Location: index.php?action=perfil");
            exit();
        }
    }

    public function cambiarPassword() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $password_actual = $_POST['password_actual'];
            $nueva_password = $_POST['nueva_password'];
            $confirmar_password = $_POST['confirmar_password'];
            $query = "SELECT password FROM usuarios WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $_SESSION['user_id']);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$user || !password_verify($password_actual, $user['password'])) {
                $_SESSION['error'] = "Contraseña actual incorrecta";
                header("Location: index.php?action=perfil");
                exit();
            }
            if ($nueva_password !== $confirmar_password) {
                $_SESSION['error'] = "Las contraseñas nuevas no coinciden";
                header("Location: index.php?action=perfil");
                exit();
            }
            if (strlen($nueva_password) < 4) {
                $_SESSION['error'] = "La contraseña debe tener al menos 4 caracteres";
                header("Location: index.php?action=perfil");
                exit();
            }
            $hash = password_hash($nueva_password, PASSWORD_DEFAULT);
            $query = "UPDATE usuarios SET password = :password WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':password', $hash);
            $stmt->bindParam(':id', $_SESSION['user_id']);
            if ($stmt->execute()) {
                $_SESSION['success'] = "Contraseña actualizada correctamente";
            } else {
                $_SESSION['error'] = "Error al cambiar contraseña";
            }
            header("Location: index.php?action=perfil");
            exit();
        }
    }
}
?>