<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Titulo de la pagina de restablecer contraseña actualizado -->
    <title>Restablecer Contraseña - TecnoSoluciones-Gestor</title>
    <style>
        /* Reset y estilos globales */
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
        /* Logo actualizado con el nuevo nombre */
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
        .row { display: flex; gap: 1.5rem; }
        .row .form-group { flex: 1; }
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
        .alert {
            padding: 0.75rem 1rem;
            border-radius: 12px;
            font-size: 0.75rem;
            margin-bottom: 1.25rem;
        }
        .alert-error { background: #fef2f2; color: #dc2626; border-left: 3px solid #dc2626; }
        .password-hint { font-size: 0.6rem; color: #94a3b8; margin-top: 0.35rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <!-- Logo con el nombre actualizado -->
                <div class="logo">TecnoSoluciones<span>-Gestor</span></div>
                <div class="subtitle">Restablecer Contraseña</div>
            </div>
            <!-- Mostrar mensajes de error -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            <!-- Formulario de restablecimiento con token CSRF -->
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">
                <div class="row">
                    <div class="form-group">
                        <label>Nueva Contraseña</label>
                        <input type="password" name="password" placeholder="••••••••" required>
                        <div class="password-hint">Minimo 4 caracteres</div>
                    </div>
                    <div class="form-group">
                        <label>Confirmar</label>
                        <input type="password" name="confirmar_password" placeholder="••••••••" required>
                    </div>
                </div>
                <button type="submit" class="btn">Actualizar Contraseña</button>
            </form>
        </div>
    </div>
</body>
</html>