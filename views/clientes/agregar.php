<?php include_once __DIR__ . '/../layouts/header.php'; ?>

<div class="flex-between mb-4">
    <div>
        <h2 style="font-size: 1.25rem; font-weight: 600;">Agregar Cliente</h2>
        <p style="font-size: 0.75rem; color: #94a3b8;">Complete el formulario</p>
    </div>
    <a href="index.php?action=clientes" class="btn btn-outline">← Volver</a>
</div>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">Formulario de Registro</div>
    <div class="card-body">
        <form method="POST">
            <div class="form-group">
                <label>Nombre *</label>
                <input type="text" name="nombre" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email">
            </div>
            <div class="form-group">
                <label>Telefono</label>
                <input type="tel" name="telefono" placeholder="Ej: 912345678 o +56912345678">
            </div>
            <div class="form-group">
                <label>Empresa</label>
                <input type="text" name="empresa">
            </div>
            <div class="flex-between" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <a href="index.php?action=clientes" class="btn btn-outline">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>