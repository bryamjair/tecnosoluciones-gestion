<?php
session_start();

// ============================================
// SEGURIDAD - FUNCIONES DE SANITIZACION
// ============================================

// Funcion para sanitizar datos
function sanitizar($data) {
    if (is_array($data)) {
        return array_map('sanitizar', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Sanitizar GET y POST si existen
if (!empty($_GET)) {
    $_GET = sanitizar($_GET);
}
if (!empty($_POST)) {
    $_POST = sanitizar($_POST);
}

// Funciones para CSRF
function generarTokenCSRF() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validarTokenCSRF($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Headers de seguridad
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");

// ============================================
// AUTOLOADER PERSONALIZADO
// ============================================
spl_autoload_register(function ($class_name) {
    $base_dir = __DIR__ . '/../';
    $paths = [
        $base_dir . 'controllers/',
        $base_dir . 'models/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class_name . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
    return false;
});

// ============================================
// CONEXION A LA BASE DE DATOS
// ============================================
$config_path = __DIR__ . '/../config/database.php';
if (!file_exists($config_path)) {
    die("Error: No se encontró el archivo de configuración de base de datos");
}
require_once $config_path;
$database = new Database();
$conn = $database->getConnection();

// ============================================
// RUTEO
// ============================================
$action = isset($_GET['action']) ? $_GET['action'] : 'login';
$sub = isset($_GET['sub']) ? $_GET['sub'] : 'index';

// Verificar autenticacion
$public_actions = ['login', 'registro', 'recuperar', 'reset_password'];
$is_logged_in = isset($_SESSION['user_id']);
$is_public_action = in_array($action, $public_actions);

if (!$is_logged_in && !$is_public_action) {
    header("Location: index.php?action=login");
    exit();
}

try {
    switch ($action) {
        case 'login':
            if (!class_exists('AuthController')) {
                require_once __DIR__ . '/../controllers/AuthController.php';
            }
            $controller = new AuthController($conn);
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $controller->login();
            } else {
                $controller->mostrarLogin();
            }
            break;

        case 'registro':
            if (!class_exists('AuthController')) {
                require_once __DIR__ . '/../controllers/AuthController.php';
            }
            $controller = new AuthController($conn);
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $controller->registrar();
            } else {
                $controller->mostrarRegistro();
            }
            break;

        case 'recuperar':
            if (!class_exists('AuthController')) {
                require_once __DIR__ . '/../controllers/AuthController.php';
            }
            $controller = new AuthController($conn);
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $controller->recuperarPassword();
            } else {
                $controller->mostrarRecuperar();
            }
            break;

        case 'reset_password':
            if (!class_exists('AuthController')) {
                require_once __DIR__ . '/../controllers/AuthController.php';
            }
            $controller = new AuthController($conn);
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $controller->resetPassword();
            } else {
                $controller->mostrarResetPassword();
            }
            break;

        case 'logout':
            if (!class_exists('AuthController')) {
                require_once __DIR__ . '/../controllers/AuthController.php';
            }
            $controller = new AuthController($conn);
            $controller->logout();
            break;

        case 'dashboard':
            if (!class_exists('DashboardController')) {
                require_once __DIR__ . '/../controllers/DashboardController.php';
            }
            $controller = new DashboardController($conn);
            $controller->index();
            break;

        case 'clientes':
            if (!class_exists('ClienteController')) {
                require_once __DIR__ . '/../controllers/ClienteController.php';
            }
            $controller = new ClienteController($conn);
            if ($sub == 'agregar') {
                $controller->agregar();
            } elseif ($sub == 'editar') {
                $controller->editar();
            } elseif ($sub == 'eliminar') {
                $controller->eliminar();
            } elseif ($sub == 'buscar') {
                $controller->buscar();
            } else {
                $controller->listar();
            }
            break;

        case 'proyectos':
            if (!class_exists('ProyectoController')) {
                require_once __DIR__ . '/../controllers/ProyectoController.php';
            }
            $controller = new ProyectoController($conn);
            if ($sub == 'agregar') {
                $controller->agregar();
            } elseif ($sub == 'editar') {
                $controller->editar();
            } elseif ($sub == 'eliminar') {
                $controller->eliminar();
            } elseif ($sub == 'buscar') {
                $controller->buscar();
            } else {
                $controller->listar();
            }
            break;

        case 'tareas':
            if (!class_exists('TareaController')) {
                require_once __DIR__ . '/../controllers/TareaController.php';
            }
            $controller = new TareaController($conn);
            if ($sub == 'agregar') {
                $controller->agregar();
            } elseif ($sub == 'editar') {
                $controller->editar();
            } elseif ($sub == 'eliminar') {
                $controller->eliminar();
            } elseif ($sub == 'porProyecto') {
                $controller->porProyecto();
            } else {
                $controller->listar();
            }
            break;

        case 'reportes':
            if (!class_exists('ReporteController')) {
                require_once __DIR__ . '/../controllers/ReporteController.php';
            }
            $controller = new ReporteController($conn);
            if ($sub == 'clientes_pdf') {
                $controller->generarClientesPDF();
            } elseif ($sub == 'proyectos_pdf') {
                $controller->generarProyectosPDF();
            } elseif ($sub == 'tareas_pdf') {
                $controller->generarTareasPDF();
            } elseif ($sub == 'exportar_clientes') {
                $controller->exportarClientesExcel();
            } elseif ($sub == 'exportar_proyectos') {
                $controller->exportarProyectosExcel();
            } elseif ($sub == 'exportar_tareas') {
                $controller->exportarTareasExcel();
            } else {
                $controller->index();
            }
            break;

        case 'auditoria':
            if (!class_exists('AuditoriaController')) {
                require_once __DIR__ . '/../controllers/AuditoriaController.php';
            }
            $controller = new AuditoriaController($conn);
            $controller->index();
            break;

        case 'perfil':
            if (!class_exists('PerfilController')) {
                require_once __DIR__ . '/../controllers/PerfilController.php';
            }
            $controller = new PerfilController($conn);
            if ($sub == 'actualizar') {
                $controller->actualizar();
            } elseif ($sub == 'cambiar_password') {
                $controller->cambiarPassword();
            } else {
                $controller->index();
            }
            break;

        case 'usuarios':
            if (!class_exists('UsuarioController')) {
                require_once __DIR__ . '/../controllers/UsuarioController.php';
            }
            $controller = new UsuarioController($conn);
            if ($sub == 'cambiar_rol') {
                $controller->cambiarRol();
            } elseif ($sub == 'eliminar') {
                $controller->eliminar();
            } else {
                $controller->index();
            }
            break;

        case 'api':
            if (!class_exists('ApiController')) {
                require_once __DIR__ . '/../controllers/ApiController.php';
            }
            $controller = new ApiController($conn);
            if ($sub == 'stats') {
                $controller->stats();
            } elseif ($sub == 'dashboard') {
                $controller->dashboard();
            } elseif ($sub == 'actividad') {
                $controller->actividad();
            } elseif ($sub == 'buscar_clientes') {
                $controller->buscarClientes();
            } elseif ($sub == 'buscar_proyectos') {
                $controller->buscarProyectos();
            } else {
                echo json_encode(['error' => 'Subacción no válida']);
            }
            break;

        default:
            header("Location: index.php?action=dashboard");
            exit();
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
    header("Location: index.php?action=dashboard");
    exit();
}
?>