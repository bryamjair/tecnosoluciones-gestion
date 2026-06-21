<?php 
// Definir variables por defecto si no existen
$totalClientes = $totalClientes ?? 0;
$totalProyectos = $totalProyectos ?? 0;
$totalTareas = $totalTareas ?? 0;
$proyectosPendientes = $proyectosPendientes ?? 0;
$proyectosEnProgreso = $proyectosEnProgreso ?? 0;
$proyectosCompletados = $proyectosCompletados ?? 0;
$tareasPrioridadBaja = $tareasPrioridadBaja ?? 0;
$tareasPrioridadMedia = $tareasPrioridadMedia ?? 0;
$tareasPrioridadAlta = $tareasPrioridadAlta ?? 0;

include_once __DIR__ . '/../layouts/header.php'; 
?>

<style>
    .reports-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        color: white;
    }
    .reports-header h1 { font-size: 1.5rem; font-weight: 600; margin-bottom: 0.5rem; }
    .reports-header p { font-size: 0.85rem; opacity: 0.8; margin-bottom: 0; }
    
    .reports-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    .report-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.5rem;
        transition: all 0.2s;
    }
    .report-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        border-color: #0d9488;
    }
    .report-count {
        font-size: 2.5rem;
        font-weight: 700;
        color: #0d9488;
        line-height: 1;
        margin-bottom: 0.5rem;
    }
    .report-title {
        font-size: 1rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.5rem;
    }
    .report-desc {
        font-size: 0.75rem;
        color: #64748b;
        margin-bottom: 1.25rem;
        line-height: 1.4;
    }
    .report-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .btn-pdf {
        background: #dc2626;
        color: white;
        border: none;
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
        font-size: 0.7rem;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-pdf:hover { background: #b91c1c; }
    .btn-excel {
        background: #16a34a;
        color: white;
        border: none;
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
        font-size: 0.7rem;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-excel:hover { background: #15803d; }
    .btn-print {
        background: #475569;
        color: white;
        border: none;
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
        font-size: 0.7rem;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-print:hover { background: #334155; }
    
    @media (max-width: 768px) {
        .reports-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="reports-header">
    <h1>Centro de Reportes</h1>
    <p>Genera reportes profesionales en PDF, exporta a Excel o imprime tus datos facilmente</p>
</div>

<div class="reports-grid">
    <!-- Tarjeta Clientes -->
    <div class="report-card">
        <div class="report-count" id="clientesCount"><?php echo $totalClientes; ?></div>
        <div class="report-title">Clientes</div>
        <div class="report-desc">Reporte completo de todos los clientes registrados en el sistema</div>
        <div class="report-buttons">
            <button onclick="generarReporte('clientes', 'pdf')" class="btn-pdf">PDF</button>
            <button onclick="generarReporte('clientes', 'excel')" class="btn-excel">Excel</button>
            <button onclick="generarReporte('clientes', 'print')" class="btn-print">Imprimir</button>
        </div>
    </div>
    
    <!-- Tarjeta Proyectos -->
    <div class="report-card">
        <div class="report-count" id="proyectosCount"><?php echo $totalProyectos; ?></div>
        <div class="report-title">Proyectos</div>
        <div class="report-desc">Reporte detallado de proyectos con estado, fechas y clientes asociados</div>
        <div class="report-buttons">
            <button onclick="generarReporte('proyectos', 'pdf')" class="btn-pdf">PDF</button>
            <button onclick="generarReporte('proyectos', 'excel')" class="btn-excel">Excel</button>
            <button onclick="generarReporte('proyectos', 'print')" class="btn-print">Imprimir</button>
        </div>
    </div>
    
    <!-- Tarjeta Tareas -->
    <div class="report-card">
        <div class="report-count" id="tareasCount"><?php echo $totalTareas; ?></div>
        <div class="report-title">Tareas</div>
        <div class="report-desc">Reporte de tareas por prioridad, estado y responsables asignados</div>
        <div class="report-buttons">
            <button onclick="generarReporte('tareas', 'pdf')" class="btn-pdf">PDF</button>
            <button onclick="generarReporte('tareas', 'excel')" class="btn-excel">Excel</button>
            <button onclick="generarReporte('tareas', 'print')" class="btn-print">Imprimir</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    cargarEstadisticas();
});

function cargarEstadisticas() {
    fetch('index.php?action=api&sub=stats')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (document.getElementById('clientesCount')) {
                    document.getElementById('clientesCount').textContent = data.stats.clientes || 0;
                }
                if (document.getElementById('proyectosCount')) {
                    document.getElementById('proyectosCount').textContent = data.stats.proyectos || 0;
                }
                if (document.getElementById('tareasCount')) {
                    document.getElementById('tareasCount').textContent = data.stats.tareas || 0;
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function generarReporte(tipo, formato) {
    let url = '';
    if (formato === 'pdf') {
        if (tipo === 'clientes') url = 'index.php?action=reportes&sub=clientes_pdf';
        else if (tipo === 'proyectos') url = 'index.php?action=reportes&sub=proyectos_pdf';
        else if (tipo === 'tareas') url = 'index.php?action=reportes&sub=tareas_pdf';
        window.open(url, '_blank');
    } else if (formato === 'excel') {
        if (tipo === 'clientes') url = 'index.php?action=reportes&sub=exportar_clientes';
        else if (tipo === 'proyectos') url = 'index.php?action=reportes&sub=exportar_proyectos';
        else if (tipo === 'tareas') url = 'index.php?action=reportes&sub=exportar_tareas';
        window.location.href = url;
    } else if (formato === 'print') {
        if (tipo === 'clientes') url = 'index.php?action=reportes&sub=clientes_pdf';
        else if (tipo === 'proyectos') url = 'index.php?action=reportes&sub=proyectos_pdf';
        else if (tipo === 'tareas') url = 'index.php?action=reportes&sub=tareas_pdf';
        var ventana = window.open(url, '_blank');
        if (ventana) {
            ventana.onload = function() { ventana.print(); };
        }
    }
}
</script>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>