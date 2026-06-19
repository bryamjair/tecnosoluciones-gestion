<?php
if (!isset($page)) $page = 1;
if (!isset($totalPages)) $totalPages = 1;
if (!isset($total)) $total = 0;
if (!isset($auditorias)) $auditorias = [];
if (!isset($usuarios)) $usuarios = [];

$limit = 20;
$offset = ($page - 1) * $limit;

include_once __DIR__ . '/../layouts/header.php';
?>

<div class="flex-between mb-4">
    <div>
        <h2 style="font-size: 1.25rem; font-weight: 600;">Auditoría del Sistema</h2>
        <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem;">Registro de actividades del sistema</p>
    </div>
    <a href="index.php?action=dashboard" class="btn btn-outline">← Volver</a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="index.php">
            <input type="hidden" name="action" value="auditoria">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Usuario</label>
                    <select name="usuario_id" class="form-select" style="border: 1px solid #d4d4d8; padding: 0.4rem; border-radius: 6px;">
                        <option value="">Todos</option>
                        <?php if (!empty($usuarios)): ?>
                            <?php foreach ($usuarios as $u): ?>
                            <option value="<?php echo $u['id']; ?>" <?php echo (isset($_GET['usuario_id']) && $_GET['usuario_id'] == $u['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($u['nombre']); ?>
                            </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Acción</label>
                    <input type="text" name="accion" style="width: 100%; border: 1px solid #d4d4d8; padding: 0.4rem; border-radius: 6px;" value="<?php echo isset($_GET['accion']) ? htmlspecialchars($_GET['accion']) : ''; ?>" placeholder="Buscar acción...">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Fecha desde</label>
                    <input type="date" name="fecha_desde" style="width: 100%; border: 1px solid #d4d4d8; padding: 0.4rem; border-radius: 6px;" value="<?php echo isset($_GET['fecha_desde']) ? htmlspecialchars($_GET['fecha_desde']) : ''; ?>">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Fecha hasta</label>
                    <input type="date" name="fecha_hasta" style="width: 100%; border: 1px solid #d4d4d8; padding: 0.4rem; border-radius: 6px;" value="<?php echo isset($_GET['fecha_hasta']) ? htmlspecialchars($_GET['fecha_hasta']) : ''; ?>">
                </div>
                <div style="display: flex; gap: 0.5rem; align-items: flex-end;">
                    <button type="submit" class="btn btn-primary" style="padding: 0.4rem 1rem;">Filtrar</button>
                    <a href="index.php?action=auditoria" class="btn btn-outline" style="padding: 0.4rem 1rem;">Limpiar</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="table-responsive">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th style="text-align: left; padding: 12px; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">Fecha/Hora</th>
                <th style="text-align: left; padding: 12px; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">Usuario</th>
                <th style="text-align: left; padding: 12px; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">Acción</th>
                <th style="text-align: left; padding: 12px; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">Tabla</th>
                <th style="text-align: left; padding: 12px; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">IP</th>
                <th style="text-align: left; padding: 12px; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">Navegador</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($auditorias)): ?>
                <tr>
                    <td colspan="6" style="padding: 32px; text-align: center; color: #94a3b8;">No hay registros de auditoría</td>
                </tr>
            <?php else: ?>
                <?php foreach ($auditorias as $a): ?>
                <tr>
                    <td style="padding: 12px; border-bottom: 1px solid #f1f5f9;"><?php echo date('d/m/Y H:i:s', strtotime($a['created_at'])); ?></td>
                    <td style="padding: 12px; border-bottom: 1px solid #f1f5f9;"><strong><?php echo htmlspecialchars($a['usuario_nombre'] ?? 'Desconocido'); ?></strong></td>
                    <td style="padding: 12px; border-bottom: 1px solid #f1f5f9;"><span style="background: #dbeafe; padding: 2px 8px; border-radius: 12px; font-size: 0.7rem;"><?php echo htmlspecialchars($a['accion']); ?></span></td>
                    <td style="padding: 12px; border-bottom: 1px solid #f1f5f9;"><?php echo htmlspecialchars($a['tabla_afectada'] ?? '-'); ?></td>
                    <td style="padding: 12px; border-bottom: 1px solid #f1f5f9;"><code style="font-size: 0.7rem;"><?php echo htmlspecialchars($a['ip_address']); ?></code></td>
                    <td style="padding: 12px; border-bottom: 1px solid #f1f5f9;"><small style="color: #94a3b8;"><?php echo htmlspecialchars($a['navegador'] ?? '-'); ?></small></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($totalPages > 1): ?>
<nav style="margin-top: 1.5rem;">
    <ul style="display: flex; justify-content: center; gap: 0.5rem; list-style: none;">
        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
            <a class="page-link" href="<?php
                $params = [];
                if ($page > 1) $params['page'] = $page - 1;
                if (isset($_GET['usuario_id'])) $params['usuario_id'] = $_GET['usuario_id'];
                if (isset($_GET['accion'])) $params['accion'] = $_GET['accion'];
                if (isset($_GET['fecha_desde'])) $params['fecha_desde'] = $_GET['fecha_desde'];
                if (isset($_GET['fecha_hasta'])) $params['fecha_hasta'] = $_GET['fecha_hasta'];
                $params['action'] = 'auditoria';
                echo '?action=auditoria&' . http_build_query($params);
            ?>">Anterior</a>
        </li>
        <?php
        $startPage = max(1, $page - 2);
        $endPage = min($totalPages, $page + 2);
        for ($i = $startPage; $i <= $endPage; $i++):
        ?>
            <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                <a class="page-link" href="?action=auditoria&page=<?php echo $i; ?><?php
                    if (isset($_GET['usuario_id'])) echo '&usuario_id=' . $_GET['usuario_id'];
                    if (isset($_GET['accion'])) echo '&accion=' . urlencode($_GET['accion']);
                    if (isset($_GET['fecha_desde'])) echo '&fecha_desde=' . $_GET['fecha_desde'];
                    if (isset($_GET['fecha_hasta'])) echo '&fecha_hasta=' . $_GET['fecha_hasta'];
                ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>
        <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
            <a class="page-link" href="<?php
                $params = [];
                if ($page < $totalPages) $params['page'] = $page + 1;
                if (isset($_GET['usuario_id'])) $params['usuario_id'] = $_GET['usuario_id'];
                if (isset($_GET['accion'])) $params['accion'] = $_GET['accion'];
                if (isset($_GET['fecha_desde'])) $params['fecha_desde'] = $_GET['fecha_desde'];
                if (isset($_GET['fecha_hasta'])) $params['fecha_hasta'] = $_GET['fecha_hasta'];
                $params['action'] = 'auditoria';
                echo '?action=auditoria&' . http_build_query($params);
            ?>">Siguiente</a>
        </li>
    </ul>
</nav>
<div style="text-align: center; color: #94a3b8; font-size: 0.75rem; margin-top: 0.5rem;">
    Mostrando registros del <?php echo ($offset + 1); ?> al <?php echo min($offset + $limit, $total); ?> de <?php echo $total; ?> totales
</div>
<?php endif; ?>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>