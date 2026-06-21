<?php 
// Asegurar que $clientes existe
$clientes = $clientes ?? [];
include_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="flex-between mb-4">
    <div>
        <h2 style="font-size: 1.25rem; font-weight: 600;">Agregar Proyecto</h2>
        <p style="font-size: 0.75rem; color: #94a3b8;">Complete el formulario</p>
    </div>
    <a href="index.php?action=proyectos" class="btn btn-outline">← Volver</a>
</div>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<div class="card" style="max-width: 700px; margin: 0 auto;">
    <div class="card-header">Formulario de Registro</div>
    <div class="card-body">
        <form method="POST">
            <div class="form-group">
                <label>Nombre del Proyecto *</label>
                <input type="text" name="nombre" required>
            </div>
            <div class="form-group">
                <label>Cliente *</label>
                <select name="cliente_id" required>
                    <option value="">Seleccione un cliente</option>
                    <?php if (!empty($clientes)): ?>
                        <?php foreach ($clientes as $c): ?>
                            <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['nombre']); ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Descripcion</label>
                <textarea name="descripcion" rows="3"></textarea>
            </div>
            <div style="display: flex; gap: 1rem;">
                <div class="form-group" style="flex: 1;">
                    <label>Fecha Inicio</label>
                    <input type="date" name="fecha_inicio">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Fecha Fin</label>
                    <input type="date" name="fecha_fin">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Estado</label>
                    <select name="estado">
                        <option value="pendiente">Pendiente</option>
                        <option value="en_progreso">En Progreso</option>
                        <option value="completado">Completado</option>
                    </select>
                </div>
            </div>
            <div class="flex-between" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <a href="index.php?action=proyectos" class="btn btn-outline">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>