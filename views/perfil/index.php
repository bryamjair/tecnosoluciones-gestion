<?php
// Definir variables por defecto
$usuario = $usuario ?? [
    'nombre' => $_SESSION['user_nombre'] ?? 'Usuario',
    'email' => $_SESSION['user_email'] ?? '',
    'telefono' => '',
    'created_at' => date('Y-m-d H:i:s'),
    'ultimo_acceso' => null,
    'rol' => 'usuario'
];

include_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="flex-between mb-4">
    <div>
        <h2 style="font-size: 1.25rem; font-weight: 600;">Mi Perfil</h2>
        <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem;">Informacion personal y configuracion de cuenta</p>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.25rem; margin-bottom: 2rem;">
    <div class="card" style="text-align: center;">
        <div class="card-body">
            <div style="width: 80px; height: 80px; background: #0d9488; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 2rem; font-weight: 600;">
                <?php echo strtoupper(substr($usuario['nombre'], 0, 2)); ?>
            </div>
            <div style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">Iniciales</div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div style="font-size: 2rem; font-weight: 700; color: #0d9488; margin-bottom: 0.25rem;">
                <?php echo ucfirst($usuario['rol'] ?? 'Usuario'); ?>
            </div>
            <div style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">Rol</div>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header">Informacion Personal</div>
    <div class="card-body">
        <form method="POST" action="index.php?action=perfil&sub=actualizar">
            <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
            </div>
            <div class="form-group">
                <label>Telefono</label>
                <input type="text" name="telefono" value="<?php echo htmlspecialchars($usuario['telefono'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Miembro desde</label>
                <input type="text" value="<?php echo isset($usuario['created_at']) ? date('d/m/Y', strtotime($usuario['created_at'])) : date('d/m/Y'); ?>" disabled style="color: #94a3b8;">
            </div>
            <div class="flex-between" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">Cambiar Contraseña</div>
    <div class="card-body">
        <form method="POST" action="index.php?action=perfil&sub=cambiar_password">
            <div class="form-group">
                <label>Contraseña Actual</label>
                <input type="password" name="password_actual" required>
            </div>
            <div class="form-group">
                <label>Nueva Contraseña</label>
                <input type="password" name="nueva_password" required>
                <div class="password-strength"></div>
            </div>
            <div class="form-group">
                <label>Confirmar Nueva Contraseña</label>
                <input type="password" name="confirmar_password" required>
            </div>
            <div class="flex-between" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary">Actualizar Contraseña</button>
            </div>
        </form>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>