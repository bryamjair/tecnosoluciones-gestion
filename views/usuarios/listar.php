<?php include_once __DIR__ . '/../layouts/header.php'; ?>

<div class="flex-between mb-4">
    <div>
        <h2 style="font-size: 1.25rem; font-weight: 600;">Usuarios del Sistema</h2>
        <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem;">Gestion de usuarios y permisos</p>
    </div>
</div>

<!-- Mostrar mensajes de éxito/error -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success" style="background: #ccfbf1; border-left: 3px solid #0d9488; color: #115e59; padding: 0.75rem 1rem; border-radius: 12px; font-size: 0.75rem; margin-bottom: 1rem;">
        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-error" style="background: #fee2e2; border-left: 3px solid #ef4444; color: #991b1b; padding: 0.75rem 1rem; border-radius: 12px; font-size: 0.75rem; margin-bottom: 1rem;">
        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<div class="table-responsive">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th style="text-align: left; padding: 12px; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">ID</th>
                <th style="text-align: left; padding: 12px; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">Nombre</th>
                <th style="text-align: left; padding: 12px; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">Email</th>
                <th style="text-align: left; padding: 12px; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">Teléfono</th>
                <th style="text-align: left; padding: 12px; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">Rol</th>
                <th style="text-align: left; padding: 12px; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($usuarios)): ?>
                <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td style="padding: 12px; border-bottom: 1px solid #f1f5f9;"><?php echo $u['id']; ?></td>
                    <td style="padding: 12px; border-bottom: 1px solid #f1f5f9;">
                        <strong><?php echo htmlspecialchars($u['nombre']); ?></strong>
                        <?php if ($u['id'] == $_SESSION['user_id']): ?>
                            <span style="background: #0d9488; color: white; font-size: 0.6rem; padding: 0.1rem 0.4rem; border-radius: 4px; margin-left: 0.5rem;">Tú</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 12px; border-bottom: 1px solid #f1f5f9;"><?php echo htmlspecialchars($u['email']); ?></td>
                    <td style="padding: 12px; border-bottom: 1px solid #f1f5f9;"><?php echo htmlspecialchars($u['telefono'] ?? '-'); ?></td>
                    <td style="padding: 12px; border-bottom: 1px solid #f1f5f9;">
                        <?php
                        $rolClass = '';
                        $rolTexto = ucfirst($u['rol'] ?? 'usuario');
                        if ($u['rol'] == 'super_admin') {
                            $rolClass = 'style="background: #dcfce7; color: #16a34a; padding: 2px 8px; border-radius: 12px; font-size: 0.7rem;"';
                        } elseif ($u['rol'] == 'admin') {
                            $rolClass = 'style="background: #dbeafe; color: #1d4ed8; padding: 2px 8px; border-radius: 12px; font-size: 0.7rem;"';
                        } else {
                            $rolClass = 'style="background: #f1f5f9; color: #475569; padding: 2px 8px; border-radius: 12px; font-size: 0.7rem;"';
                        }
                        ?>
                        <span <?php echo $rolClass; ?>><?php echo $rolTexto; ?></span>
                    </td>
                    <td style="padding: 12px; border-bottom: 1px solid #f1f5f9;">
                        <?php if ($u['id'] != $_SESSION['user_id']): ?>
                            <?php if ($_SESSION['user_rol'] == 'super_admin'): ?>
                                <select onchange="cambiarRol(<?php echo $u['id']; ?>, this.value)" style="padding: 0.25rem 0.5rem; border: 1px solid #d4d4d8; border-radius: 4px; font-size: 0.7rem; margin-right: 0.5rem;">
                                    <option value="usuario" <?php echo ($u['rol'] == 'usuario') ? 'selected' : ''; ?>>Usuario</option>
                                    <option value="admin" <?php echo ($u['rol'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                    <option value="super_admin" <?php echo ($u['rol'] == 'super_admin') ? 'selected' : ''; ?>>Super Admin</option>
                                </select>
                                <a href="index.php?action=usuarios&sub=eliminar&id=<?php echo $u['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este usuario? Esta acción no se puede deshacer.')" style="background: transparent; border: 1px solid #fecaca; color: #dc2626; padding: 0.25rem 0.75rem; border-radius: 6px; font-size: 0.7rem; text-decoration: none; display: inline-block;">Eliminar</a>
                            <?php else: ?>
                                <span style="color: #94a3b8; font-size: 0.7rem;">Solo Super Admin</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span style="color: #94a3b8; font-size: 0.7rem;">Usuario actual</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="padding: 32px; text-align: center; color: #94a3b8;">No hay usuarios registrados</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Leyenda de roles -->
<div style="margin-top: 1rem; padding: 1rem; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0;">
    <h4 style="font-size: 0.8rem; font-weight: 600; margin-bottom: 0.5rem;">Leyenda de Roles</h4>
    <div style="display: flex; gap: 1.5rem; flex-wrap: wrap;">
        <div>
            <span style="background: #dcfce7; color: #16a34a; padding: 2px 8px; border-radius: 12px; font-size: 0.7rem;">Super Admin</span>
            <span style="font-size: 0.7rem; color: #64748b; margin-left: 0.5rem;">Puede gestionar usuarios y tiene todos los permisos</span>
        </div>
        <div>
            <span style="background: #dbeafe; color: #1d4ed8; padding: 2px 8px; border-radius: 12px; font-size: 0.7rem;">Admin</span>
            <span style="font-size: 0.7rem; color: #64748b; margin-left: 0.5rem;">Puede ver usuarios y auditoría</span>
        </div>
        <div>
            <span style="background: #f1f5f9; color: #475569; padding: 2px 8px; border-radius: 12px; font-size: 0.7rem;">Usuario</span>
            <span style="font-size: 0.7rem; color: #64748b; margin-left: 0.5rem;">Acceso básico al sistema</span>
        </div>
    </div>
</div>

<script>
function cambiarRol(id, rol) {
    if (confirm('¿Cambiar rol de este usuario a "' + rol.replace('_', ' ').toUpperCase() + '"?')) {
        window.location.href = 'index.php?action=usuarios&sub=cambiar_rol&id=' + id + '&rol=' + rol;
    }
}
</script>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>