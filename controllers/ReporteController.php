<?php
class ReporteController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function index() {
        $clienteModel = new Cliente($this->conn);
        $proyectoModel = new Proyecto($this->conn);
        
        $totalClientes = $clienteModel->contar();
        $totalProyectos = $proyectoModel->contar();
        $proyectosPendientes = $proyectoModel->contarPorEstado('pendiente');
        $proyectosEnProgreso = $proyectoModel->contarPorEstado('en_progreso');
        $proyectosCompletados = $proyectoModel->contarPorEstado('completado');
        
        $totalTareas = 0;
        $tareasPrioridadBaja = 0;
        $tareasPrioridadMedia = 0;
        $tareasPrioridadAlta = 0;
        
        try {
            $stmt = $this->conn->query("SHOW TABLES LIKE 'tareas'");
            if ($stmt->rowCount() > 0) {
                $tareaModel = new Tarea($this->conn);
                $tareas = $tareaModel->listar();
                $totalTareas = is_array($tareas) ? count($tareas) : 0;
                
                $query = "SELECT prioridad, COUNT(*) as total FROM tareas GROUP BY prioridad";
                $stmt = $this->conn->query($query);
                $prioridades = $stmt->fetchAll();
                foreach ($prioridades as $p) {
                    if ($p['prioridad'] == 'baja') $tareasPrioridadBaja = $p['total'];
                    if ($p['prioridad'] == 'media') $tareasPrioridadMedia = $p['total'];
                    if ($p['prioridad'] == 'alta') $tareasPrioridadAlta = $p['total'];
                }
            }
        } catch (PDOException $e) {
            $totalTareas = 0;
        }
        
        include_once __DIR__ . '/../views/reportes/index.php';
    }

    public function generarClientesPDF() {
        $clienteModel = new Cliente($this->conn);
        $clientes = $clienteModel->listar();
        $this->mostrarReporteHTML($clientes, 'clientes', 'Reporte de Clientes', 'pdf');
    }
    
    public function generarProyectosPDF() {
        $proyectoModel = new Proyecto($this->conn);
        $proyectos = $proyectoModel->listar();
        $this->mostrarReporteHTML($proyectos, 'proyectos', 'Reporte de Proyectos', 'pdf');
    }
    
    public function generarTareasPDF() {
        $tareaModel = new Tarea($this->conn);
        $tareas = $tareaModel->listar();
        $this->mostrarReporteHTML($tareas, 'tareas', 'Reporte de Tareas', 'pdf');
    }

    public function imprimirClientes() {
        $clienteModel = new Cliente($this->conn);
        $clientes = $clienteModel->listar();
        $this->mostrarReporteHTML($clientes, 'clientes', 'Reporte de Clientes', 'print');
    }
    
    public function imprimirProyectos() {
        $proyectoModel = new Proyecto($this->conn);
        $proyectos = $proyectoModel->listar();
        $this->mostrarReporteHTML($proyectos, 'proyectos', 'Reporte de Proyectos', 'print');
    }
    
    public function imprimirTareas() {
        $tareaModel = new Tarea($this->conn);
        $tareas = $tareaModel->listar();
        $this->mostrarReporteHTML($tareas, 'tareas', 'Reporte de Tareas', 'print');
    }

    private function mostrarReporteHTML($datos, $tipo, $titulo, $modo = 'print') {
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title><?php echo $titulo; ?> - TecnoSoluciones</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { font-family: 'Inter', 'Segoe UI', Arial, sans-serif; background: white; padding: 20px; color: #1e293b; }
                .report-container { max-width: 1200px; margin: 0 auto; background: white; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; }
                .report-header { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white; padding: 30px; text-align: center; }
                .report-header h1 { font-size: 24px; margin-bottom: 8px; font-weight: 600; }
                .report-header p { opacity: 0.8; font-size: 13px; }
                .info-section { background: #f8fafc; padding: 12px 20px; display: flex; justify-content: space-between; border-bottom: 1px solid #e2e8f0; flex-wrap: wrap; }
                .info-box { text-align: center; padding: 4px 10px; }
                .info-box .label { font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
                .info-box .value { font-size: 18px; font-weight: bold; color: #0d9488; }
                .table-container { padding: 20px; overflow-x: auto; }
                table { width: 100%; border-collapse: collapse; font-size: 13px; }
                th { background: #f1f5f9; padding: 10px 12px; text-align: left; border-bottom: 2px solid #e2e8f0; font-weight: 600; color: #1e293b; }
                td { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; }
                tr:hover td { background: #f8fafc; }
                .badge { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 11px; font-weight: 500; }
                .badge-pendiente { background: #fef3c7; color: #d97706; }
                .badge-en_progreso { background: #dbeafe; color: #2563eb; }
                .badge-completado { background: #ccfbf1; color: #0d9488; }
                .badge-completada { background: #ccfbf1; color: #0d9488; }
                .badge-alta { background: #fee2e2; color: #dc2626; }
                .badge-media { background: #fed7aa; color: #ea580c; }
                .badge-baja { background: #dcfce7; color: #16a34a; }
                .footer { background: #f8fafc; padding: 12px; text-align: center; font-size: 10px; color: #64748b; border-top: 1px solid #e2e8f0; }
                .footer strong { color: #0d9488; }
                @media print { body { padding: 0; } .no-print { display: none; } }
                @media (max-width: 600px) { .info-section { flex-direction: column; align-items: center; gap: 8px; } th, td { padding: 6px 8px; font-size: 11px; } }
            </style>
            <?php if ($modo === 'pdf'): ?>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
            <?php endif; ?>
        </head>
        <body>
            <div class="report-container" id="reportContainer">
                <div class="report-header">
                    <h1><?php echo $titulo; ?></h1>
                    <p>Generado: <?php echo date('d/m/Y H:i:s'); ?></p>
                </div>
                <div class="info-section">
                    <div class="info-box"><div class="label">Total Registros</div><div class="value"><?php echo count($datos); ?></div></div>
                    <div class="info-box"><div class="label">Generado por</div><div class="value"><?php echo $_SESSION['user_nombre'] ?? 'Usuario'; ?></div></div>
                    <div class="info-box"><div class="label">Empresa</div><div class="value">TecnoSoluciones</div></div>
                </div>
                <div class="table-container">
                    <?php if ($tipo == 'clientes'): ?>
                    <table>
                        <thead><tr><th>ID</th><th>Nombre</th><th>Email</th><th>Teléfono</th><th>Empresa</th></tr></thead>
                        <tbody>
                            <?php if (!empty($datos)): ?>
                                <?php foreach ($datos as $item): ?>
                                <tr><td><?php echo $item['id']; ?></td><td><strong><?php echo htmlspecialchars($item['nombre']); ?></strong></td><td><?php echo htmlspecialchars($item['email'] ?? '-'); ?></td><td><?php echo htmlspecialchars($item['telefono'] ?? '-'); ?></td><td><?php echo htmlspecialchars($item['empresa'] ?? '-'); ?></td></tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" style="text-align:center; color:#94a3b8; padding:30px;">No hay clientes registrados</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                    
                    <?php if ($tipo == 'proyectos'): ?>
                    <table>
                        <thead><tr><th>ID</th><th>Proyecto</th><th>Cliente</th><th>Inicio</th><th>Fin</th><th>Estado</th></tr></thead>
                        <tbody>
                            <?php if (!empty($datos)): ?>
                                <?php foreach ($datos as $item): ?>
                                <tr><td><?php echo $item['id']; ?></td><td><strong><?php echo htmlspecialchars($item['nombre']); ?></strong></td><td><?php echo htmlspecialchars($item['cliente_nombre'] ?? 'Sin cliente'); ?></td><td><?php echo $item['fecha_inicio'] ?? '-'; ?></td><td><?php echo $item['fecha_fin'] ?? '-'; ?></td><td><span class="badge badge-<?php echo $item['estado']; ?>"><?php echo ucfirst(str_replace('_', ' ', $item['estado'])); ?></span></td></tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="6" style="text-align:center; color:#94a3b8; padding:30px;">No hay proyectos registrados</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                    
                    <?php if ($tipo == 'tareas'): ?>
                    <table>
                        <thead><tr><th>ID</th><th>Tarea</th><th>Proyecto</th><th>Asignado a</th><th>Fecha Límite</th><th>Prioridad</th><th>Estado</th></tr></thead>
                        <tbody>
                            <?php if (!empty($datos)): ?>
                                <?php foreach ($datos as $item): ?>
                                <tr><td><?php echo $item['id']; ?></td><td><strong><?php echo htmlspecialchars($item['titulo']); ?></strong></td><td><?php echo htmlspecialchars($item['proyecto_nombre'] ?? '-'); ?></td><td><?php echo htmlspecialchars($item['asignado_nombre'] ?? 'No asignado'); ?></td><td><?php echo $item['fecha_limite'] ?? '-'; ?></td><td><span class="badge badge-<?php echo $item['prioridad']; ?>"><?php echo ucfirst($item['prioridad'] ?? 'media'); ?></span></td><td><span class="badge badge-<?php echo $item['estado']; ?>"><?php echo ucfirst(str_replace('_', ' ', $item['estado'])); ?></span></td></tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="7" style="text-align:center; color:#94a3b8; padding:30px;">No hay tareas registradas</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
                <div class="footer"><strong>TecnoSoluciones</strong> - Sistema de Gestión de Proyectos<br>Copyright &copy; <?php echo date('Y'); ?> - Todos los derechos reservados</div>
            </div>
            
            <?php if ($modo === 'print'): ?>
            <script>window.onload = function() { window.print(); };</script>
            <?php endif; ?>
            
            <?php if ($modo === 'pdf'): ?>
            <button class="no-print" onclick="generarPDF()" style="position:fixed;bottom:20px;right:20px;padding:10px 24px;background:#0d9488;color:white;border:none;border-radius:8px;cursor:pointer;font-size:14px;font-weight:600;box-shadow:0 4px 12px rgba(13,148,136,0.3);z-index:999;">📄 Guardar PDF</button>
            <script>
                function generarPDF() {
                    var element = document.getElementById('reportContainer');
                    var opt = {
                        margin: [0.5, 0.5, 0.5, 0.5],
                        filename: '<?php echo $tipo; ?>_reporte_<?php echo date('Y-m-d'); ?>.pdf',
                        image: { type: 'jpeg', quality: 0.98 },
                        html2canvas: { scale: 2, useCORS: true, logging: false },
                        jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
                    };
                    html2pdf().set(opt).from(element).save();
                }
            </script>
            <?php endif; ?>
        </body>
        </html>
        <?php
        exit();
    }

    public function exportarClientesExcel() {
        $clienteModel = new Cliente($this->conn);
        $clientes = $clienteModel->listar();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="clientes_' . date('Y-m-d') . '.csv"');
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($output, ['ID', 'Nombre', 'Email', 'Teléfono', 'Empresa', 'Fecha Registro']);
        foreach ($clientes as $c) {
            fputcsv($output, [$c['id'], $c['nombre'], $c['email'], $c['telefono'], $c['empresa'], $c['created_at']]);
        }
        fclose($output);
        exit();
    }

    public function exportarProyectosExcel() {
        $proyectoModel = new Proyecto($this->conn);
        $proyectos = $proyectoModel->listar();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="proyectos_' . date('Y-m-d') . '.csv"');
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($output, ['ID', 'Proyecto', 'Cliente', 'Descripción', 'Fecha Inicio', 'Fecha Fin', 'Estado']);
        foreach ($proyectos as $p) {
            fputcsv($output, [$p['id'], $p['nombre'], $p['cliente_nombre'] ?? 'Sin cliente', $p['descripcion'] ?? '', $p['fecha_inicio'] ?? '', $p['fecha_fin'] ?? '', $p['estado']]);
        }
        fclose($output);
        exit();
    }

    public function exportarTareasExcel() {
        $tareaModel = new Tarea($this->conn);
        $tareas = $tareaModel->listar();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="tareas_' . date('Y-m-d') . '.csv"');
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($output, ['ID', 'Tarea', 'Proyecto', 'Asignado a', 'Fecha Límite', 'Prioridad', 'Estado']);
        foreach ($tareas as $t) {
            fputcsv($output, [$t['id'], $t['titulo'], $t['proyecto_nombre'] ?? '', $t['asignado_nombre'] ?? 'No asignado', $t['fecha_limite'] ?? '', $t['prioridad'], $t['estado']]);
        }
        fclose($output);
        exit();
    }
}
?>