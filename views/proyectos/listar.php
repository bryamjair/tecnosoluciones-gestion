<?php include_once __DIR__ . '/../layouts/header.php'; ?>

<div class="flex-between mb-4">
    <div>
        <h2 style="font-size: 1.25rem; font-weight: 600;">Proyectos</h2>
        <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem;">Listado de proyectos</p>
    </div>
    <a href="index.php?action=proyectos&sub=agregar" class="btn btn-primary">+ Nuevo Proyecto</a>
</div>

<div class="card" style="margin-bottom: 1rem;">
    <div class="card-body">
        <div style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 1; min-width: 150px;">
                <label style="font-size: 0.65rem; text-transform: uppercase; color: #64748b; font-weight: 600;">Buscar</label>
                <input type="text" id="buscarProyecto" placeholder="Nombre o cliente..." style="width: 100%; padding: 0.4rem 0.6rem; border: 1px solid #d4d4d8; border-radius: 6px; font-size: 0.8rem;">
            </div>
            <div>
                <label style="font-size: 0.65rem; text-transform: uppercase; color: #64748b; font-weight: 600;">Estado</label>
                <select id="filtroEstado" style="padding: 0.4rem 0.6rem; border: 1px solid #d4d4d8; border-radius: 6px; font-size: 0.8rem;">
                    <option value="">Todos</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="en_progreso">En Progreso</option>
                    <option value="completado">Completado</option>
                </select>
            </div>
            <div>
                <button onclick="filtrarProyectos()" class="btn btn-primary btn-sm">Buscar</button>
                <button onclick="limpiarFiltroProyectos()" class="btn btn-outline btn-sm">Limpiar</button>
            </div>
        </div>
        <div style="margin-top: 0.5rem; font-size: 0.7rem; color: #94a3b8;">
            <span id="resultadosProyectos">Mostrando todos los proyectos</span>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table style="width: 100%; border-collapse: collapse;" id="tablaProyectos">
        <thead>
            <tr>
                <th style="text-align: left; padding: 12px; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">ID</th>
                <th style="text-align: left; padding: 12px; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">Proyecto</th>
                <th style="text-align: left; padding: 12px; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">Cliente</th>
                <th style="text-align: left; padding: 12px; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">Inicio</th>
                <th style="text-align: left; padding: 12px; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">Fin</th>
                <th style="text-align: left; padding: 12px; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">Estado</th>
                <th style="text-align: left; padding: 12px; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($proyectos)): ?>
                <?php foreach ($proyectos as $p): ?>
                <tr>
                    <td style="padding: 12px; border-bottom: 1px solid #f1f5f9;"><?php echo $p['id']; ?></td>
                    <td style="padding: 12px; border-bottom: 1px solid #f1f5f9;"><strong><?php echo htmlspecialchars($p['nombre']); ?></strong></td>
                    <td style="padding: 12px; border-bottom: 1px solid #f1f5f9;"><?php echo isset($p['cliente_nombre']) ? htmlspecialchars($p['cliente_nombre']) : 'Sin cliente'; ?></td>
                    <td style="padding: 12px; border-bottom: 1px solid #f1f5f9;"><?php echo $p['fecha_inicio'] ?? '-'; ?></td>
                    <td style="padding: 12px; border-bottom: 1px solid #f1f5f9;"><?php echo $p['fecha_fin'] ?? '-'; ?></td>
                    <td style="padding: 12px; border-bottom: 1px solid #f1f5f9;">
                        <?php
                        $estadoClass = '';
                        $estadoTexto = '';
                        if ($p['estado'] == 'pendiente') {
                            $estadoClass = 'badge-pendiente';
                            $estadoTexto = 'Pendiente';
                        } elseif ($p['estado'] == 'en_progreso') {
                            $estadoClass = 'badge-progreso';
                            $estadoTexto = 'En Progreso';
                        } else {
                            $estadoClass = 'badge-completado';
                            $estadoTexto = 'Completado';
                        }
                        ?>
                        <span class="badge <?php echo $estadoClass; ?>"><?php echo $estadoTexto; ?></span>
                    </td>
                    <td style="padding: 12px; border-bottom: 1px solid #f1f5f9;">
                        <a href="index.php?action=proyectos&sub=editar&id=<?php echo $p['id']; ?>" class="btn btn-outline btn-sm">Editar</a>
                        <a href="index.php?action=proyectos&sub=eliminar&id=<?php echo $p['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Eliminar este proyecto?')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="padding: 32px; text-align: center; color: #94a3b8;">No hay proyectos registrados</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('buscarProyecto').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            filtrarProyectos();
        }
    });
    document.getElementById('filtroEstado').addEventListener('change', filtrarProyectos);
});
</script>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>