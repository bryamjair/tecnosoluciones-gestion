<?php
session_start();

// ============================================
// SEGURIDAD
// ============================================

function sanitizar($data) {
    if (is_array($data)) {
        return array_map('sanitizar', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

$_GET = sanitizar($_GET);
$_POST = sanitizar($_POST);

function generarTokenCSRF() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validarTokenCSRF($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

session_regenerate_id(true);

header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");

// ============================================
// AUTOLOADER
// ============================================
spl_autoload_register(function ($class_name) {
    $paths = [
        __DIR__ . '/../controllers/',
        __DIR__ . '/../models/'
    ];
    foreach ($paths as $path) {
        $file = $path . $class_name . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// ============================================
// CONEXIÓN A LA BASE DE DATOS
// ============================================
require_once __DIR__ . '/../config/database.php';
$database = new Database();
$conn = $database->getConnection();

// ============================================
// RUTEO
// ============================================
$action = $_GET['action'] ?? 'login';
$sub = $_GET['sub'] ?? 'index';

if (!isset($_SESSION['user_id']) && !in_array($action, ['login', 'registro', 'recuperar', 'reset_password'])) {
    header("Location: index.php?action=login");
    exit();
}

try {
    switch ($action) {
        case 'login':
            $controller = new AuthController($conn);
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $controller->login();
            } else {
                $controller->mostrarLogin();
            }
            break;

        case 'registro':
            $controller = new AuthController($conn);
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $controller->registrar();
            } else {
                $controller->mostrarRegistro();
            }
            break;

        case 'recuperar':
            $controller = new AuthController($conn);
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $controller->recuperarPassword();
            } else {
                $controller->mostrarRecuperar();
            }
            break;

        case 'reset_password':
            $controller = new AuthController($conn);
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $controller->resetPassword();
            } else {
                $controller->mostrarResetPassword();
            }
            break;

        case 'logout':
            $controller = new AuthController($conn);
            $controller->logout();
            break;

        case 'dashboard':
            $controller = new DashboardController($conn);
            $controller->index();
            break;

        case 'clientes':
            $controller = new ClienteController($conn);
            if ($sub == 'agregar') {
                $controller->agregar();
            } elseif ($sub == 'editar') {
                $controller->editar();
            } elseif ($sub == 'eliminar') {
                $controller->eliminar();
            } else {
                $controller->listar();
            }
            break;

        case 'proyectos':
            $controller = new ProyectoController($conn);
            if ($sub == 'agregar') {
                $controller->agregar();
            } elseif ($sub == 'editar') {
                $controller->editar();
            } elseif ($sub == 'eliminar') {
                $controller->eliminar();
            } else {
                $controller->listar();
            }
            break;

        case 'tareas':
            $controller = new TareaController($conn);
            if ($sub == 'agregar') {
                $controller->agregar();
            } elseif ($sub == 'editar') {
                $controller->editar();
            } elseif ($sub == 'eliminar') {
                $controller->eliminar();
            } else {
                $controller->listar();
            }
            break;

        case 'reportes':
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
            $controller = new AuditoriaController($conn);
            $controller->index();
            break;

        case 'perfil':
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
            $controller = new ApiController($conn);
            if ($sub == 'stats') {
                $controller->stats();
            } elseif ($sub == 'dashboard') {
                $controller->dashboard();
            } elseif ($sub == 'actividad') {
                $controller->actividad();
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