<?php
// Controlador de Autenticacion - Maneja login, registro y recuperacion
class AuthController {
    private $conn;

    // Constructor - Recibe la conexion a la base de datos
    public function __construct($db) {
        $this->conn = $db;
    }

    // Mostrar la pagina de login
    public function mostrarLogin() {
        include_once __DIR__ . '/../views/auth/login.php';
    }

    // Mostrar la pagina de registro
    public function mostrarRegistro() {
        include_once __DIR__ . '/../views/auth/registro.php';
    }

    // Mostrar la pagina de recuperacion de contraseña
    public function mostrarRecuperar() {
        include_once __DIR__ . '/../views/auth/recuperar.php';
    }

    // Mostrar la pagina de restablecimiento de contraseña
    public function mostrarResetPassword() {
        $token = $_GET['token'] ?? '';
        $usuarioModel = new Usuario($this->conn);
        $usuario = $usuarioModel->obtenerPorToken($token);
        // Verificar que el token sea valido
        if (!$usuario) {
            $_SESSION['error'] = "Token inválido o expirado";
            header("Location: index.php?action=login");
            exit();
        }
        include_once __DIR__ . '/../views/auth/reset_password.php';
    }

    // Procesar el registro de un nuevo usuario
    public function registrar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validar token CSRF
            if (!isset($_POST['csrf_token']) || !validarTokenCSRF($_POST['csrf_token'])) {
                $_SESSION['error'] = "Token de seguridad inválido";
                header("Location: index.php?action=registro");
                exit();
            }

            $nombre = trim($_POST['nombre']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            // Validar campos
            $errores = [];
            if (empty($nombre)) $errores[] = "El nombre es requerido";
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errores[] = "Email inválido";
            if (strlen($password) < 4) $errores[] = "La contraseña debe tener al menos 4 caracteres";
            if ($password !== $confirm_password) $errores[] = "Las contraseñas no coinciden";

            $usuarioModel = new Usuario($this->conn);
            if ($usuarioModel->emailExiste($email)) {
                $errores[] = "El email ya está registrado";
            }

            // Si no hay errores, registrar usuario
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

    // Procesar el login de usuario
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validar token CSRF
            if (!isset($_POST['csrf_token']) || !validarTokenCSRF($_POST['csrf_token'])) {
                $_SESSION['error'] = "Token de seguridad inválido";
                header("Location: index.php?action=login");
                exit();
            }

            $email = trim($_POST['email']);
            $password = $_POST['password'];

            $usuarioModel = new Usuario($this->conn);
            $user = $usuarioModel->login($email, $password);

            // Si las credenciales son correctas, iniciar sesion
            if ($user) {
                // Regenerar ID de sesion para prevenir fijacion de sesion
                session_regenerate_id(true);
                
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

    // Procesar la recuperacion de contraseña
    public function recuperarPassword() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validar token CSRF
            if (!isset($_POST['csrf_token']) || !validarTokenCSRF($_POST['csrf_token'])) {
                $_SESSION['error'] = "Token de seguridad inválido";
                header("Location: index.php?action=recuperar");
                exit();
            }

            $email = trim($_POST['email']);
            $usuarioModel = new Usuario($this->conn);
            $user = $usuarioModel->obtenerPorEmail($email);
            
            if ($user) {
                // Generar token y guardarlo
                $token = bin2hex(random_bytes(32));
                $usuarioModel->guardarToken($user['id'], $token);
                $link = "http://" . $_SERVER['HTTP_HOST'] . "/TecnoSoluciones-Gestor/public/index.php?action=reset_password&token=" . $token;
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

    // Procesar el restablecimiento de contraseña
    public function resetPassword() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validar token CSRF
            if (!isset($_POST['csrf_token']) || !validarTokenCSRF($_POST['csrf_token'])) {
                $_SESSION['error'] = "Token de seguridad inválido";
                header("Location: index.php?action=login");
                exit();
            }

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

    // Cerrar sesion
    public function logout() {
        session_destroy();
        header("Location: index.php?action=login");
        exit();
    }
}
?>