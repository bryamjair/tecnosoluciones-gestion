<?php include_once __DIR__ . '/../layouts/header.php'; ?>

<div class="flex-between mb-4">
    <div>
        <h2 style="font-size: 1.25rem; font-weight: 600;">Clientes</h2>
        <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem;">Listado de clientes registrados</p>
    </div>
    <a href="index.php?action=clientes&sub=agregar" class="btn btn-primary">+ Nuevo Cliente</a>
</div>

<div class="card" style="margin-bottom: 1rem;">
    <div class="card-body">
        <div style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 1; min-width: 200px;">
                <label style="font-size: 0.65rem; text-transform: uppercase; color: #64748b; font-weight: 600;">Buscar</label>
                <input type="text" id="buscarCliente" placeholder="Nombre o empresa..." style="width: 100%; padding: 0.4rem 0.6rem; border: 1px solid #d4d4d8; border-radius: 6px; font-size: 0.8rem;">
            </div>
            <div>
                <button onclick="filtrarClientes()" class="btn btn-primary btn-sm">Buscar</button>
                <button onclick="limpiarFiltro()" class="btn btn-outline btn-sm">Limpiar</button>
            </div>
        </div>
        <div style="margin-top: 0.5rem; font-size: 0.7rem; color: #94a3b8;">
            <span id="resultadosCount">Mostrando todos los clientes</span>
        </div>
    </div>
</div>

<div class="table-responsive" id="tablaClientesContainer">
    <table style="width: 100%; border-collapse: collapse;" id="tablaClientes">
        <thead>
            <tr>
                <th style="text-align: left; padding: 12px; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">ID</th>
                <th style="text-align: left; padding: 12px; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">Nombre</th>
                <th style="text-align: left; padding: 12px; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">Email</th>
                <th style="text-align: left; padding: 12px; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">Teléfono</th>
                <th style="text-align: left; padding: 12px; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">Empresa</th>
                <th style="text-align: left; padding: 12px; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($clientes)): ?>
                <?php foreach ($clientes as $c): ?>
                <tr>
                    <td style="padding: 12px; border-bottom: 1px solid #f1f5f9;"><?php echo $c['id']; ?></td>
                    <td style="padding: 12px; border-bottom: 1px solid #f1f5f9;"><strong><?php echo htmlspecialchars($c['nombre']); ?></strong></td>
                    <td style="padding: 12px; border-bottom: 1px solid #f1f5f9;"><?php echo htmlspecialchars($c['email']); ?></td>
                    <td style="padding: 12px; border-bottom: 1px solid #f1f5f9;"><?php echo htmlspecialchars($c['telefono']); ?></td>
                    <td style="padding: 12px; border-bottom: 1px solid #f1f5f9;"><?php echo htmlspecialchars($c['empresa']); ?></td>
                    <td style="padding: 12px; border-bottom: 1px solid #f1f5f9;">
                        <a href="index.php?action=clientes&sub=editar&id=<?php echo $c['id']; ?>" class="btn btn-outline btn-sm">Editar</a>
                        <a href="index.php?action=clientes&sub=eliminar&id=<?php echo $c['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirmarEliminar('<?php echo htmlspecialchars($c['nombre']); ?>')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="padding: 32px; text-align: center; color: #94a3b8;">No hay clientes registrados</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('buscarCliente').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            filtrarClientes();
        }
    });
});
</script>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>