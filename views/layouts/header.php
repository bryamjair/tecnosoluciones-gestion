<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TecnoSoluciones - Sistema de Gestion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            line-height: 1.5;
        }
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 260px;
            height: 100vh;
            background: #ffffff;
            border-right: 1px solid #d4d4d8;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            z-index: 100;
        }
        .sidebar-header {
            padding: 1.75rem 1.5rem;
            border-bottom: 1px solid #d4d4d8;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        }
        .logo { font-size: 1.25rem; font-weight: 600; color: #ffffff; letter-spacing: -0.3px; }
        .logo span { font-weight: 300; color: #2dd4bf; }
        .logo-sub { font-size: 0.65rem; color: #94a3b8; margin-top: 0.25rem; }
        .sidebar-nav { flex: 1; padding: 1.5rem; }
        .nav-section { margin-bottom: 2rem; }
        .nav-title {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
            margin-bottom: 0.75rem;
            font-weight: 600;
        }
        .nav-item {
            display: block;
            padding: 0.5rem 0;
            color: #475569;
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.2s;
            border-left: 2px solid transparent;
            padding-left: 0.75rem;
        }
        .nav-item:hover { color: #0d9488; border-left-color: #0d9488; background: linear-gradient(90deg, #f0fdfa 0%, transparent 100%); }
        .nav-item.active { color: #0d9488; border-left-color: #0d9488; font-weight: 500; background: linear-gradient(90deg, #f0fdfa 0%, transparent 100%); }
        .sidebar-footer {
            padding: 1.5rem;
            border-top: 1px solid #d4d4d8;
            background: #f8fafc;
        }
        .user-name { font-size: 0.85rem; font-weight: 600; color: #1e293b; }
        .user-role { font-size: 0.7rem; color: #0d9488; margin-top: 0.2rem; }
        .logout-link {
            display: block;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #d4d4d8;
            font-size: 0.8rem;
            color: #64748b;
            text-decoration: none;
            transition: color 0.2s;
        }
        .logout-link:hover { color: #dc2626; }
        .main-content { margin-left: 260px; padding: 2rem; min-height: 100vh; }
        .btn {
            padding: 0.5rem 1.25rem;
            font-size: 0.8rem;
            font-family: inherit;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
            font-weight: 500;
            border: 1px solid;
        }
        .btn-primary { background: #0d9488; color: white; border-color: #0f766e; }
        .btn-primary:hover { background: #0f766e; transform: translateY(-1px); box-shadow: 0 2px 8px rgba(13,148,136,0.2); }
        .btn-outline { background: transparent; border: 1px solid #d4d4d8; color: #475569; }
        .btn-outline:hover { border-color: #0d9488; color: #0d9488; background: #f0fdfa; }
        .btn-sm { padding: 0.25rem 0.75rem; font-size: 0.7rem; border-radius: 6px; }
        .btn-danger { background: transparent; border: 1px solid #fecaca; color: #dc2626; }
        .btn-danger:hover { background: #fef2f2; border-color: #dc2626; color: #b91c1c; }
        .card { background: white; border: 1px solid #d4d4d8; border-radius: 16px; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,0.03); }
        .card-header { padding: 1rem 1.25rem; background: #fafafa; border-bottom: 1px solid #e4e4e7; font-size: 0.85rem; font-weight: 600; color: #0d9488; }
        .card-body { padding: 1.25rem; }
        .table-responsive { overflow-x: auto; border: 1px solid #d4d4d8; border-radius: 16px; background: white; }
        table { width: 100%; border-collapse: collapse; font-size: 0.8rem; }
        th { text-align: left; padding: 0.875rem; background: #fafafa; font-weight: 600; color: #1e293b; border-bottom: 1px solid #e4e4e7; }
        td { padding: 0.875rem; border-bottom: 1px solid #f1f5f9; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #f8fafc; }
        .form-group { margin-bottom: 1.25rem; }
        label {
            display: block;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0d9488;
            margin-bottom: 0.35rem;
            font-weight: 600;
        }
        input, select, textarea {
            width: 100%;
            padding: 0.6rem 0;
            font-size: 0.85rem;
            font-family: inherit;
            border: none;
            border-bottom: 1px solid #d4d4d8;
            background: transparent;
            outline: none;
            transition: border-color 0.2s;
        }
        input:focus, select:focus, textarea:focus { border-bottom-color: #0d9488; }
        .badge {
            display: inline-block;
            padding: 0.2rem 0.7rem;
            font-size: 0.65rem;
            border-radius: 20px;
            font-weight: 500;
        }
        .badge-pendiente { background: #fef3c7; color: #b45309; }
        .badge-progreso { background: #dbeafe; color: #1d4ed8; }
        .badge-completado { background: #ccfbf1; color: #0f766e; }
        .badge-alta { background: #fee2e2; color: #dc2626; }
        .badge-media { background: #fed7aa; color: #ea580c; }
        .badge-baja { background: #dcfce7; color: #16a34a; }
        .alert { padding: 0.75rem 1rem; border-radius: 12px; font-size: 0.75rem; margin-bottom: 1rem; }
        .alert-success { background: #ccfbf1; border-left: 3px solid #0d9488; color: #115e59; }
        .alert-error { background: #fee2e2; border-left: 3px solid #ef4444; color: #991b1b; }
        .flex-between { display: flex; justify-content: space-between; align-items: center; }
        .mb-4 { margin-bottom: 1.5rem; }
        .mb-3 { margin-bottom: 1rem; }
        .mt-4 { margin-top: 1.5rem; }
        .text-center { text-align: center; }
        .text-muted { color: #94a3b8; }
        .fw-bold { font-weight: 600; }
        .pagination { display: flex; justify-content: center; gap: 0.5rem; list-style: none; margin-top: 1.5rem; }
        .page-link {
            padding: 0.4rem 0.75rem;
            border: 1px solid #d4d4d8;
            color: #475569;
            text-decoration: none;
            font-size: 0.75rem;
            border-radius: 8px;
            transition: all 0.2s;
            background: white;
        }
        .page-link:hover { border-color: #0d9488; color: #0d9488; }
        .page-item.active .page-link { background: #0d9488; color: white; border-color: #0d9488; }
        hr { margin: 1.5rem 0; border: none; border-top: 1px solid #e4e4e7; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); position: fixed; z-index: 1000; }
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0; padding: 1rem; }
            .mobile-toggle {
                display: block;
                position: fixed;
                bottom: 1rem;
                right: 1rem;
                background: #0d9488;
                color: white;
                padding: 0.5rem 1rem;
                border-radius: 30px;
                font-size: 0.8rem;
                cursor: pointer;
                z-index: 1001;
                border: none;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            }
        }
        @media (min-width: 769px) { .mobile-toggle { display: none; } }
    </style>
</head>
<body>

<?php if (isset($_SESSION['user_id'])): 
    $current_action = $_GET['action'] ?? 'dashboard';
?>
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo">Tecno<span>Soluciones</span></div>
        <div class="logo-sub">Gestion de Proyectos</div>
    </div>
    <div class="sidebar-nav">
        <div class="nav-section">
            <div class="nav-title">Principal</div>
            <a href="index.php?action=dashboard" class="nav-item <?php echo ($current_action == 'dashboard') ? 'active' : ''; ?>">Dashboard</a>
        </div>
        <div class="nav-section">
            <div class="nav-title">Gestion</div>
            <a href="index.php?action=clientes" class="nav-item <?php echo ($current_action == 'clientes') ? 'active' : ''; ?>">Clientes</a>
            <a href="index.php?action=proyectos" class="nav-item <?php echo ($current_action == 'proyectos') ? 'active' : ''; ?>">Proyectos</a>
            <a href="index.php?action=tareas" class="nav-item <?php echo ($current_action == 'tareas') ? 'active' : ''; ?>">Tareas</a>
        </div>
        <div class="nav-section">
            <div class="nav-title">Reportes</div>
            <a href="index.php?action=reportes" class="nav-item <?php echo ($current_action == 'reportes') ? 'active' : ''; ?>">Reportes</a>
            <?php if (in_array($_SESSION['user_rol'] ?? 'usuario', ['admin', 'super_admin'])): ?>
            <a href="index.php?action=auditoria" class="nav-item <?php echo ($current_action == 'auditoria') ? 'active' : ''; ?>">Auditoria</a>
            <?php endif; ?>
        </div>
        <div class="nav-section">
            <div class="nav-title">Configuracion</div>
            <?php if (in_array($_SESSION['user_rol'] ?? 'usuario', ['admin', 'super_admin'])): ?>
            <a href="index.php?action=usuarios" class="nav-item <?php echo ($current_action == 'usuarios') ? 'active' : ''; ?>">Usuarios</a>
            <?php endif; ?>
            <a href="index.php?action=perfil" class="nav-item <?php echo ($current_action == 'perfil') ? 'active' : ''; ?>">Mi Perfil</a>
        </div>
    </div>
    <div class="sidebar-footer">
        <div class="user-name"><?php echo $_SESSION['user_nombre']; ?></div>
        <div class="user-role"><?php echo ucfirst($_SESSION['user_rol'] ?? 'Usuario'); ?></div>
        <a href="index.php?action=logout" class="logout-link">Cerrar Sesion</a>
    </div>
</div>

<div class="mobile-toggle" onclick="document.getElementById('sidebar').classList.toggle('active')">Menu</div>

<div class="main-content">
<?php endif; ?>