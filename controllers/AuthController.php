<?php
class AuthController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function mostrarLogin() {
        include_once __DIR__ . '/../views/auth/login.php';
    }

    public function mostrarRegistro() {
        include_once __DIR__ . '/../views/auth/registro.php';
    }

    public function mostrarRecuperar() {
        include_once __DIR__ . '/../views/auth/recuperar.php';
    }

    public function mostrarResetPassword() {
        $token = $_GET['token'] ?? '';
        $usuarioModel = new Usuario($this->conn);
        $usuario = $usuarioModel->obtenerPorToken($token);
        if (!$usuario) {
            $_SESSION['error'] = "Token inválido o expirado";
            header("Location: index.php?action=login");
            exit();
        }
        include_once __DIR__ . '/../views/auth/reset_password.php';
    }

    public function registrar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = trim($_POST['nombre']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            $errores = [];
            if (empty($nombre)) $errores[] = "El nombre es requerido";
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errores[] = "Email inválido";
            if (strlen($password) < 4) $errores[] = "La contraseña debe tener al menos 4 caracteres";
            if ($password !== $confirm_password) $errores[] = "Las contraseñas no coinciden";

            $usuarioModel = new Usuario($this->conn);
            if ($usuarioModel->emailExiste($email)) {
                $errores[] = "El email ya está registrado";
            }

            if (empty($errores)) {
                if ($usuarioModel->registrar($nombre, $email, $password)) {
                    $_SESSION['success'] = "Registro exitoso. Ahora puedes iniciar sesión.";
                    header("Location: index.php?action=login");
                    exit();
                } else {
                    $errores[] = "Error al registrar usuario";
                }
            }
            $_SESSION['errores'] = $errores;
            header("Location: index.php?action=registro");
            exit();
        }
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email']);
            $password = $_POST['password'];

            $usuarioModel = new Usuario($this->conn);
            $user = $usuarioModel->login($email, $password);

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nombre'] = $user['nombre'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_rol'] = $user['rol'] ?? 'usuario';
                $usuarioModel->actualizarUltimoAcceso($user['id']);
                header("Location: index.php?action=dashboard");
                exit();
            } else {
                $_SESSION['error'] = "Credenciales incorrectas";
                header("Location: index.php?action=login");
                exit();
            }
        }
    }

    public function recuperarPassword() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email']);
            $usuarioModel = new Usuario($this->conn);
            $user = $usuarioModel->obtenerPorEmail($email);
            if ($user) {
                $token = bin2hex(random_bytes(32));
                $usuarioModel->guardarToken($user['id'], $token);
                $link = "http://" . $_SERVER['HTTP_HOST'] . "/proyecto_final/public/index.php?action=reset_password&token=" . $token;
                $mensaje = "Haz clic en el siguiente enlace para restablecer tu contraseña:\n\n" . $link;
                mail($email, "Recuperación de Contraseña", $mensaje);
                $_SESSION['success'] = "Se ha enviado un enlace a tu correo";
            } else {
                $_SESSION['error'] = "Email no registrado";
            }
            header("Location: index.php?action=recuperar");
            exit();
        }
    }

    public function resetPassword() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $token = $_POST['token'];
            $password = $_POST['password'];
            $confirmar = $_POST['confirmar_password'];
            if ($password !== $confirmar) {
                $_SESSION['error'] = "Las contraseñas no coinciden";
                header("Location: index.php?action=reset_password&token=" . $token);
                exit();
            }
            $usuarioModel = new Usuario($this->conn);
            if ($usuarioModel->resetPassword($token, $password)) {
                $_SESSION['success'] = "Contraseña actualizada correctamente";
                header("Location: index.php?action=login");
            } else {
                $_SESSION['error'] = "Error al actualizar contraseña";
            }
            exit();
        }
    }

    public function logout() {
        session_destroy();
        header("Location: index.php?action=login");
        exit();
    }
}
?>