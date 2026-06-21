<!-- Incluir el header del layout -->
<?php include_once __DIR__ . '/../layouts/header.php'; ?>

<style>
    /* Estilos del dashboard - KPIs y graficos */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.25rem;
        margin-bottom: 2rem;
    }
    .kpi-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.25rem;
        transition: all 0.2s;
    }
    .kpi-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }
    .kpi-card .numero {
        font-size: 2rem;
        font-weight: 700;
    }
    .kpi-card .label {
        font-size: 0.7rem;
        text-transform: uppercase;
        color: #64748b;
        margin-top: 0.25rem;
    }
    .kpi-card .icono {
        float: right;
        font-size: 2rem;
        opacity: 0.2;
    }
    .kpi-card .verde { color: #10b981; }
    .kpi-card .azul { color: #3b82f6; }
    .kpi-card .naranja { color: #f59e0b; }
    .kpi-card .rojo { color: #ef4444; }
    
    .charts-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.25rem;
        margin-bottom: 2rem;
    }
    .chart-box {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.25rem;
    }
    .chart-box h3 {
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: #1e293b;
    }
    .chart-box canvas { max-height: 220px; }
    
    /* Responsive para dispositivos moviles */
    @media (max-width: 768px) {
        .kpi-grid { grid-template-columns: repeat(2, 1fr); }
        .charts-grid { grid-template-columns: 1fr; }
    }
</style>

<!-- Encabezado del dashboard -->
<div class="flex-between mb-4">
    <div>
        <h2 style="font-size: 1.5rem; font-weight: 600;">Dashboard</h2>
        <p style="font-size: 0.8rem; color: #64748b;">
            Bienvenido, <strong><?php echo $_SESSION['user_nombre']; ?></strong>
            <?php if (in_array($_SESSION['user_rol'] ?? 'usuario', ['admin', 'super_admin'])): ?>
                <span style="background: #0d9488; color: white; font-size: 0.6rem; padding: 0.1rem 0.5rem; border-radius: 20px;">Admin</span>
            <?php endif; ?>
        </p>
    </div>
    <div style="font-size: 0.8rem; color: #94a3b8;">
        <?php echo date('l, d \d\e F \d\e Y'); ?>
    </div>
</div>

<!-- KPIs - Indicadores clave de rendimiento -->
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="icono">👥</div>
        <div class="numero verde" id="kpiClientes">0</div>
        <div class="label">Total Clientes</div>
    </div>
    <div class="kpi-card">
        <div class="icono">📊</div>
        <div class="numero azul" id="kpiProyectos">0</div>
        <div class="label">Proyectos Activos</div>
    </div>
    <div class="kpi-card">
        <div class="icono">✅</div>
        <div class="numero naranja" id="kpiTareas">0</div>
        <div class="label">Tareas Pendientes</div>
    </div>
    <div class="kpi-card">
        <div class="icono">⚠️</div>
        <div class="numero rojo" id="kpiVencidas">0</div>
        <div class="label">Tareas Vencidas</div>
    </div>
</div>

<!-- Graficos estadisticos -->
<div class="charts-grid">
    <div class="chart-box">
        <h3>Proyectos por Estado</h3>
        <canvas id="proyectosChart"></canvas>
    </div>
    <div class="chart-box">
        <h3>Tareas por Prioridad</h3>
        <canvas id="tareasChart"></canvas>
    </div>
</div>

<!-- Actividad reciente -->
<div class="card">
    <div class="card-header">Actividad Reciente</div>
    <div class="table-responsive" style="border: none; border-radius: 0;">
        <table style="border: none;">
            <thead>
                <tr>
                    <th>Fecha/Hora</th>
                    <th>Usuario</th>
                    <th>Acción</th>
                    <th>Detalle</th>
                </tr>
            </thead>
            <tbody id="actividadReciente">
                <tr>
                    <td colspan="4" style="padding: 20px; text-align: center; color: #94a3b8;">Cargando actividad...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Libreria Chart.js para graficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Variables para controlar las instancias de los graficos
let proyectosChartInstance = null;
let tareasChartInstance = null;

// Cargar datos del dashboard al iniciar la pagina
document.addEventListener('DOMContentLoaded', function() {
    cargarDashboard();
});

// Funcion para cargar los datos del dashboard via API
function cargarDashboard() {
    fetch('index.php?action=api&sub=dashboard')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar KPIs
                document.getElementById('kpiClientes').textContent = data.stats.clientes || 0;
                document.getElementById('kpiProyectos').textContent = data.stats.proyectos || 0;
                document.getElementById('kpiTareas').textContent = data.stats.tareas_pendientes || 0;
                document.getElementById('kpiVencidas').textContent = data.stats.tareas_vencidas || 0;
                // Crear graficos
                crearGraficos(data.stats);
                // Cargar actividad reciente
                cargarActividad();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

// Funcion para crear los graficos
function crearGraficos(stats) {
    // Grafico de proyectos por estado
    const ctxProyectos = document.getElementById('proyectosChart').getContext('2d');
    if (proyectosChartInstance) proyectosChartInstance.destroy();
    proyectosChartInstance = new Chart(ctxProyectos, {
        type: 'doughnut',
        data: {
            labels: ['Pendientes', 'En Progreso', 'Completados'],
            datasets: [{
                data: [
                    stats.proyectos_pendientes || 0,
                    stats.proyectos_en_progreso || 0,
                    stats.proyectos_completados || 0
                ],
                backgroundColor: ['#f59e0b', '#3b82f6', '#10b981'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: { size: 11 }, usePointStyle: true }
                }
            }
        }
    });
    
    // Grafico de tareas por prioridad
    const ctxTareas = document.getElementById('tareasChart').getContext('2d');
    if (tareasChartInstance) tareasChartInstance.destroy();
    tareasChartInstance = new Chart(ctxTareas, {
        type: 'bar',
        data: {
            labels: ['Baja', 'Media', 'Alta'],
            datasets: [{
                label: 'Cantidad',
                data: [
                    stats.tareas_baja || 0,
                    stats.tareas_media || 0,
                    stats.tareas_alta || 0
                ],
                backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            },
            plugins: { legend: { display: false } }
        }
    });
}

// Funcion para cargar la actividad reciente
function cargarActividad() {
    fetch('index.php?action=api&sub=actividad')
        .then(response => response.json())
        .then(data => {
            let tbody = document.getElementById('actividadReciente');
            if (data.success && data.actividad.length > 0) {
                tbody.innerHTML = '';
                data.actividad.forEach(item => {
                    tbody.innerHTML += `
                        <tr>
                            <td style="padding: 10px;">${item.fecha}</td>
                            <td style="padding: 10px;"><strong>${item.usuario}</strong></td>
                            <td style="padding: 10px;">${item.accion}</td>
                            <td style="padding: 10px; color: #64748b;">${item.detalle || '-'}</td>
                        </tr>
                    `;
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="4" style="padding: 20px; text-align: center; color: #94a3b8;">No hay actividad reciente</td></tr>';
            }
        })
        .catch(() => {
            document.getElementById('actividadReciente').innerHTML = 
                '<tr><td colspan="4" style="padding: 20px; text-align: center; color: #94a3b8;">Error al cargar actividad</td></tr>';
        });
}
</script>

<!-- Incluir el footer del layout -->
<?php include_once __DIR__ . '/../layouts/footer.php'; ?>