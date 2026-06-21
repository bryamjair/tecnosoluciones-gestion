<?php 
// Asegurar que $proyectos y $usuarios existen
$proyectos = $proyectos ?? [];
$usuarios = $usuarios ?? [];
include_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="flex-between mb-4">
    <div>
        <h2 style="font-size: 1.25rem; font-weight: 600;">Agregar Tarea</h2>
        <p style="font-size: 0.75rem; color: #94a3b8;">Complete la informacion de la nueva tarea</p>
    </div>
    <a href="index.php?action=tareas" class="btn btn-outline">← Volver</a>
</div>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<div class="card" style="max-width: 700px; margin: 0 auto;">
    <div class="card-header">Formulario de Registro</div>
    <div class="card-body">
        <form method="POST">
            <div class="form-group">
                <label>Titulo *</label>
                <input type="text" name="titulo" placeholder="Ej: Disenar base de datos" required>
            </div>
            <div class="form-group">
                <label>Descripcion</label>
                <textarea name="descripcion" rows="3" placeholder="Descripcion de la tarea..."></textarea>
            </div>
            <div style="display: flex; gap: 1rem;">
                <div class="form-group" style="flex: 1;">
                    <label>Proyecto *</label>
                    <select name="proyecto_id" required>
                        <option value="">Seleccione un proyecto</option>
                        <?php if (!empty($proyectos)): ?>
                            <?php foreach ($proyectos as $p): ?>
                                <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['nombre']); ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Asignar a</label>
                    <select name="asignado_a">
                        <option value="">No asignar</option>
                        <?php if (!empty($usuarios)): ?>
                            <?php foreach ($usuarios as $u): ?>
                                <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['nombre']); ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            <div style="display: flex; gap: 1rem;">
                <div class="form-group" style="flex: 1;">
                    <label>Fecha Limite</label>
                    <input type="date" name="fecha_limite">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Prioridad</label>
                    <select name="prioridad">
                        <option value="baja">Baja</option>
                        <option value="media" selected>Media</option>
                        <option value="alta">Alta</option>
                    </select>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Estado</label>
                    <select name="estado">
                        <option value="pendiente">Pendiente</option>
                        <option value="en_progreso">En Progreso</option>
                        <option value="completada">Completada</option>
                    </select>
                </div>
            </div>
            <hr>
            <div class="flex-between">
                <button type="submit" class="btn btn-primary">Guardar Tarea</button>
                <a href="index.php?action=tareas" class="btn btn-outline">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>