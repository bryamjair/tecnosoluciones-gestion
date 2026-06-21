-- ============================================
-- SISTEMA DE GESTIÓN DE PROYECTOS
-- Base de datos: proyectofinal
-- ============================================

CREATE DATABASE IF NOT EXISTS proyectofinal;
USE proyectofinal;

-- ============================================
-- TABLA: usuarios
-- ============================================
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    telefono VARCHAR(20) NULL,
    rol ENUM('usuario', 'admin', 'super_admin') DEFAULT 'usuario',
    reset_token VARCHAR(255) NULL,
    token_expira DATETIME NULL,
    ultimo_acceso DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABLA: clientes
-- ============================================
CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NULL,
    telefono VARCHAR(20) NULL,
    empresa VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABLA: proyectos
-- ============================================
CREATE TABLE IF NOT EXISTS proyectos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT NULL,
    cliente_id INT NOT NULL,
    fecha_inicio DATE NULL,
    fecha_fin DATE NULL,
    estado ENUM('pendiente', 'en_progreso', 'completado') DEFAULT 'pendiente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
    INDEX idx_cliente (cliente_id),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABLA: tareas
-- ============================================
CREATE TABLE IF NOT EXISTS tareas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    proyecto_id INT NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    descripcion TEXT NULL,
    asignado_a INT NULL,
    fecha_limite DATE NULL,
    prioridad ENUM('baja', 'media', 'alta') DEFAULT 'media',
    estado ENUM('pendiente', 'en_progreso', 'completada') DEFAULT 'pendiente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (proyecto_id) REFERENCES proyectos(id) ON DELETE CASCADE,
    FOREIGN KEY (asignado_a) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_proyecto (proyecto_id),
    INDEX idx_asignado (asignado_a),
    INDEX idx_estado (estado),
    INDEX idx_prioridad (prioridad)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABLA: auditoria
-- ============================================
CREATE TABLE IF NOT EXISTS auditoria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NULL,
    accion VARCHAR(50) NOT NULL,
    tabla_afectada VARCHAR(50) NULL,
    registro_id INT NULL,
    datos_anteriores TEXT NULL,
    datos_nuevos TEXT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    navegador VARCHAR(50) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_usuario (usuario_id),
    INDEX idx_accion (accion),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- DATOS DE PRUEBA
-- ============================================

-- Usuario Super Admin
INSERT INTO usuarios (nombre, email, password, rol, created_at) VALUES
('Administrador', 'admin@tecnosoluciones.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin', NOW());
-- Password: admin123

-- Usuarios de prueba
INSERT INTO usuarios (nombre, email, password, rol, telefono, created_at) VALUES
('Juan Pérez', 'juan@ejemplo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '+56912345678', NOW()),
('María González', 'maria@ejemplo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'usuario', '+56987654321', NOW());

-- Clientes de prueba
INSERT INTO clientes (nombre, email, telefono, empresa, created_at) VALUES
('Empresa ABC', 'contacto@empresaabc.cl', '+56912345678', 'ABC Ltda.', NOW()),
('Corporación XYZ', 'info@corporacionxyz.cl', '+56998765432', 'XYZ Corp.', NOW()),
('Startup Tech', 'hola@startuptech.cl', '+56955555555', 'Tech Startups', NOW());

-- Proyectos de prueba
INSERT INTO proyectos (nombre, descripcion, cliente_id, fecha_inicio, fecha_fin, estado, created_at) VALUES
('Sistema de Ventas', 'Desarrollo de sistema de ventas en línea', 1, '2024-01-15', '2024-06-30', 'en_progreso', NOW()),
('App Móvil', 'Aplicación móvil para gestión de clientes', 2, '2024-02-01', '2024-08-15', 'pendiente', NOW()),
('Sitio Web Corporativo', 'Rediseño del sitio web corporativo', 3, '2024-03-01', '2024-05-30', 'completado', NOW());

-- Tareas de prueba
INSERT INTO tareas (proyecto_id, titulo, descripcion, asignado_a, fecha_limite, prioridad, estado, created_at) VALUES
(1, 'Diseñar base de datos', 'Diseño de esquema de base de datos para el sistema de ventas', 2, '2024-02-15', 'alta', 'completada', NOW()),
(1, 'Desarrollar API REST', 'Crear endpoints para el sistema de ventas', 2, '2024-04-30', 'media', 'en_progreso', NOW()),
(2, 'Diseñar UI/UX', 'Diseño de interfaz de usuario para la app móvil', 3, '2024-03-31', 'alta', 'pendiente', NOW()),
(2, 'Implementar autenticación', 'Sistema de login y registro para usuarios', 3, '2024-05-15', 'media', 'pendiente', NOW()),
(3, 'Migrar contenido', 'Migrar contenido del sitio web antiguo', 2, '2024-04-01', 'baja', 'completada', NOW());

-- Registros de auditoría de prueba
INSERT INTO auditoria (usuario_id, accion, tabla_afectada, registro_id, ip_address, navegador, created_at) VALUES
(1, 'login', NULL, NULL, '127.0.0.1', 'Chrome', NOW()),
(1, 'registro', 'clientes', 1, '127.0.0.1', 'Chrome', NOW()),
(2, 'creacion', 'proyectos', 1, '127.0.0.1', 'Firefox', NOW()),
(2, 'actualizacion', 'proyectos', 1, '127.0.0.1', 'Firefox', NOW()),
(3, 'creacion', 'tareas', 1, '127.0.0.1', 'Safari', NOW());
