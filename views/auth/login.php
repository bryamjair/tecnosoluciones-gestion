<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesion - TecnoSoluciones</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f0fdfa 0%, #e0f2fe 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .container { max-width: 400px; width: 100%; }
        .card {
            background: #ffffff;
            border-radius: 24px;
            padding: 2rem;
            box-shadow: 0 20px 35px -10px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(13, 148, 136, 0.1);
        }
        .header { text-align: center; margin-bottom: 2rem; }
        .logo { font-size: 1.75rem; font-weight: 600; color: #1e293b; letter-spacing: -0.5px; }
        .logo span { color: #0d9488; font-weight: 500; }
        .subtitle { font-size: 0.8rem; color: #64748b; margin-top: 0.5rem; }
        .form-group { margin-bottom: 1.5rem; }
        label {
            display: block;
            font-size: 0.7rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        input {
            width: 100%;
            padding: 0.75rem 0;
            font-size: 0.9rem;
            font-family: inherit;
            background: transparent;
            border: none;
            border-bottom: 2px solid #e2e8f0;
            outline: none;
            transition: all 0.2s ease;
            color: #1e293b;
        }
        input:focus { border-bottom-color: #0d9488; }
        input::placeholder { color: #cbd5e1; }
        .btn {
            width: 100%;
            padding: 0.85rem;
            font-size: 0.85rem;
            font-weight: 600;
            font-family: inherit;
            background: #0d9488;
            color: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 0.5rem;
        }
        .btn:hover { background: #0f766e; transform: translateY(-2px); }
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.5rem 0;
            font-size: 0.7rem;
            color: #94a3b8;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e2e8f0;
        }
        .divider::before { margin-right: 1rem; }
        .divider::after { margin-left: 1rem; }
        .register-link { text-align: center; }
        .register-link a { font-size: 0.8rem; color: #0d9488; text-decoration: none; font-weight: 500; }
        .register-link a:hover { text-decoration: underline; }
        .recuperar-link { text-align: center; margin-top: 0.5rem; }
        .recuperar-link a { font-size: 0.7rem; color: #94a3b8; text-decoration: none; }
        .recuperar-link a:hover { color: #0d9488; }
        .alert {
            padding: 0.75rem 1rem;
            border-radius: 12px;
            font-size: 0.75rem;
            margin-bottom: 1.25rem;
        }
        .alert-error { background: #fef2f2; color: #dc2626; border-left: 3px solid #dc2626; }
        .alert-success { background: #ccfbf1; color: #0d9488; border-left: 3px solid #0d9488; }
        .demo-info {
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid #e2e8f0;
            font-size: 0.7rem;
            text-align: center;
            color: #94a3b8;
        }
        .demo-info code {
            background: #f1f5f9;
            padding: 0.2rem 0.4rem;
            border-radius: 6px;
            color: #0d9488;
            font-size: 0.7rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <div class="logo">Tecno<span>Soluciones</span></div>
                <div class="subtitle">Sistema de Gestion de Proyectos</div>
            </div>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label>Correo Electronico</label>
                    <input type="email" name="email" placeholder="usuario@ejemplo.com" required autofocus>
                </div>
                <div class="form-group">
                    <label>Contraseña</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn">Iniciar Sesion</button>
            </form>
            <div class="recuperar-link">
                <a href="index.php?action=recuperar">¿Olvidaste tu contraseña?</a>
            </div>
            <div class="divider">o</div>
            <div class="register-link">
                <a href="index.php?action=registro">Crear una cuenta nueva</a>
            </div>
            <div class="demo-info">
                <p>Demo: <code>admin@tecnosoluciones.com</code> / <code>admin123</code></p>
            </div>
        </div>
    </div>
</body>
</html>