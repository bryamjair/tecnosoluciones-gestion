<?php include_once __DIR__ . '/../layouts/header.php'; ?>

<div class="flex-between mb-4">
    <div>
        <h2 style="font-size: 1.25rem; font-weight: 600;">Tareas</h2>
        <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem;">Listado de tareas</p>
    </div>
    <a href="index.php?action=tareas&sub=agregar" class="btn btn-primary">+ Nueva Tarea</a>
</div>

<div class="table-responsive">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th style="text-align: left; padding: 12px; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">ID</th>
                <th style="text-align: left; padding: 12px; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">Título</th>
                <th style="text-align: left; padding: 12px; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">Proyecto</th>
                <th style="text-align: left; padding: 12px; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">Asignado a</th>
                <th style="text-align: left; padding: 12px; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">Fecha Límite</th>
                <th style="text-align: left; padding: 12px; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">Prioridad</th>
                <th style="text-align: left; padding: 12px; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">Estado</th>
                <th style="text-align: left; padding: 12px; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($tareas)): ?>
                <?php foreach ($tareas as $t): ?>
                <tr>
                    <td style="padding: 12px; border-bottom: 1px solid #f1f5f9;"><?php echo $t['id']; ?></td>
                    <td style="padding: 12px; border-bottom: 1px solid #f1f5f9;"><strong><?php echo htmlspecialchars($t['titulo']); ?></strong></td>
                    <td style="padding: 12px; border-bottom: 1px solid #f1f5f9;"><?php echo htmlspecialchars($t['proyecto_nombre']); ?></td>
                    <td style="padding: 12px; border-bottom: 1px solid #f1f5f9;"><?php echo htmlspecialchars($t['asignado_nombre'] ?? 'No asignado'); ?></td>
                    <td style="padding: 12px; border-bottom: 1px solid #f1f5f9;">
                        <?php 
                        if (!empty($t['fecha_limite']) && $t['fecha_limite'] < date('Y-m-d') && $t['estado'] != 'completada') {
                            echo '<span style="color: #dc2626;">' . $t['fecha_limite'] . '</span>';
                        } else {
                            echo $t['fecha_limite'] ?? '-';
                        }
                        ?>
                    </td>
                    <td style="padding: 12px; border-bottom: 1px solid #f1f5f9;">
                        <?php
                        $prioridadClass = '';
                        $prioridadTexto = '';
                        switch($t['prioridad']) {
                            case 'alta': 
                                $prioridadClass = 'badge-alta'; 
                                $prioridadTexto = 'Alta';
                                break;
                            case 'media': 
                                $prioridadClass = 'badge-media'; 
                                $prioridadTexto = 'Media';
                                break;
                            default: 
                                $prioridadClass = 'badge-baja'; 
                                $prioridadTexto = 'Baja';
                        }
                        ?>
                        <span class="badge <?php echo $prioridadClass; ?>"><?php echo $prioridadTexto; ?></span>
                    </td>
                    <td style="padding: 12px; border-bottom: 1px solid #f1f5f9;">
                        <?php
                        $estadoClass = '';
                        $estadoTexto = '';
                        switch($t['estado']) {
                            case 'pendiente': 
                                $estadoClass = 'badge-pendiente'; 
                                $estadoTexto = 'Pendiente';
                                break;
                            case 'en_progreso': 
                                $estadoClass = 'badge-progreso'; 
                                $estadoTexto = 'En Progreso';
                                break;
                            default: 
                                $estadoClass = 'badge-completado'; 
                                $estadoTexto = 'Completada';
                        }
                        ?>
                        <span class="badge <?php echo $estadoClass; ?>"><?php echo $estadoTexto; ?></span>
                    </td>
                    <td style="padding: 12px; border-bottom: 1px solid #f1f5f9;">
                        <a href="index.php?action=tareas&sub=editar&id=<?php echo $t['id']; ?>" class="btn btn-outline btn-sm">Editar</a>
                        <a href="index.php?action=tareas&sub=eliminar&id=<?php echo $t['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Eliminar esta tarea?')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="padding: 32px; text-align: center; color: #94a3b8;">No hay tareas registradas</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>