<?php include_once __DIR__ . '/../layouts/header.php'; ?>

<div class="flex-between mb-4">
    <div>
        <h2 style="font-size: 1.25rem; font-weight: 600;">Editar Tarea</h2>
        <p style="font-size: 0.75rem; color: #94a3b8;">Modifique la informacion de la tarea</p>
    </div>
    <a href="index.php?action=tareas" class="btn btn-outline">← Volver</a>
</div>

<div class="card" style="max-width: 700px; margin: 0 auto;">
    <div class="card-header">Formulario de Edicion</div>
    <div class="card-body">
        <form method="POST">
            <div class="form-group">
                <label>Titulo *</label>
                <input type="text" name="titulo" value="<?php echo isset($tarea['titulo']) ? htmlspecialchars($tarea['titulo']) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label>Descripcion</label>
                <textarea name="descripcion" rows="3"><?php echo isset($tarea['descripcion']) ? htmlspecialchars($tarea['descripcion']) : ''; ?></textarea>
            </div>
            <div style="display: flex; gap: 1rem;">
                <div class="form-group" style="flex: 1;">
                    <label>Proyecto *</label>
                    <select name="proyecto_id" required>
                        <option value="">Seleccione un proyecto</option>
                        <?php if (!empty($proyectos)): ?>
                            <?php foreach ($proyectos as $p): ?>
                                <option value="<?php echo $p['id']; ?>" <?php echo (isset($tarea['proyecto_id']) && $p['id'] == $tarea['proyecto_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($p['nombre']); ?>
                                </option>
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
                                <option value="<?php echo $u['id']; ?>" <?php echo (isset($tarea['asignado_a']) && $u['id'] == $tarea['asignado_a']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($u['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            <div style="display: flex; gap: 1rem;">
                <div class="form-group" style="flex: 1;">
                    <label>Fecha Limite</label>
                    <input type="date" name="fecha_limite" value="<?php echo isset($tarea['fecha_limite']) ? $tarea['fecha_limite'] : ''; ?>">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Prioridad</label>
                    <select name="prioridad">
                        <option value="baja" <?php echo (isset($tarea['prioridad']) && $tarea['prioridad'] == 'baja') ? 'selected' : ''; ?>>Baja</option>
                        <option value="media" <?php echo (isset($tarea['prioridad']) && $tarea['prioridad'] == 'media') ? 'selected' : ''; ?>>Media</option>
                        <option value="alta" <?php echo (isset($tarea['prioridad']) && $tarea['prioridad'] == 'alta') ? 'selected' : ''; ?>>Alta</option>
                    </select>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Estado</label>
                    <select name="estado">
                        <option value="pendiente" <?php echo (isset($tarea['estado']) && $tarea['estado'] == 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                        <option value="en_progreso" <?php echo (isset($tarea['estado']) && $tarea['estado'] == 'en_progreso') ? 'selected' : ''; ?>>En Progreso</option>
                        <option value="completada" <?php echo (isset($tarea['estado']) && $tarea['estado'] == 'completada') ? 'selected' : ''; ?>>Completada</option>
                    </select>
                </div>
            </div>
            <hr>
            <div class="flex-between">
                <button type="submit" class="btn btn-primary">Actualizar Tarea</button>
                <a href="index.php?action=tareas" class="btn btn-outline">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>