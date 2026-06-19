document.addEventListener('DOMContentLoaded', function() {
    initDeleteConfirmations();
    autoCloseAlerts();
    initDynamicTareas();
});

function initDeleteConfirmations() {
    document.querySelectorAll('.btn-delete, .delete-confirm, a[onclick*="confirm"]').forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('¿Eliminar este elemento?')) {
                e.preventDefault();
            }
        });
    });
}

function autoCloseAlerts() {
    document.querySelectorAll('.alert:not(.alert-permanent)').forEach(alert => {
        setTimeout(() => {
            let bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
}

function initDynamicTareas() {
    let proyectoSelect = document.getElementById('proyecto_id');
    let tareaSelect = document.getElementById('tarea_id');
    if (proyectoSelect && tareaSelect) {
        proyectoSelect.addEventListener('change', function() {
            let proyectoId = this.value;
            if (proyectoId) {
                fetch(`index.php?action=tareas&sub=porProyecto&proyecto_id=${proyectoId}`)
                    .then(response => response.json())
                    .then(data => {
                        tareaSelect.innerHTML = '<option value="">Seleccione una tarea</option>';
                        data.forEach(tarea => {
                            tareaSelect.innerHTML += `<option value="${tarea.id}">${tarea.titulo}</option>`;
                        });
                    });
            } else {
                tareaSelect.innerHTML = '<option value="">Primero seleccione un proyecto</option>';
            }
        });
    }
}

function confirmarEliminar(nombre) {
    return confirm('¿Eliminar "' + nombre + '"? Esta acción no se puede deshacer.');
}

function filtrarClientes() {
    let busqueda = document.getElementById('buscarCliente').value.toLowerCase();
    let filas = document.querySelectorAll('#tablaClientes tbody tr');
    let contador = 0;
    filas.forEach(fila => {
        let texto = fila.textContent.toLowerCase();
        if (texto.includes(busqueda)) {
            fila.style.display = '';
            contador++;
        } else {
            fila.style.display = 'none';
        }
    });
    document.getElementById('resultadosCount').textContent = 'Mostrando ' + contador + ' de ' + filas.length + ' clientes';
}

function limpiarFiltro() {
    document.getElementById('buscarCliente').value = '';
    filtrarClientes();
}

function filtrarProyectos() {
    let busqueda = document.getElementById('buscarProyecto').value.toLowerCase();
    let estado = document.getElementById('filtroEstado').value.toLowerCase();
    let filas = document.querySelectorAll('#tablaProyectos tbody tr');
    let contador = 0;
    filas.forEach(fila => {
        let texto = fila.textContent.toLowerCase();
        let coincideBusqueda = texto.includes(busqueda);
        let coincideEstado = true;
        if (estado) {
            let estadoCelda = fila.querySelectorAll('td')[5];
            if (estadoCelda) {
                coincideEstado = estadoCelda.textContent.toLowerCase().includes(estado);
            }
        }
        if (coincideBusqueda && coincideEstado) {
            fila.style.display = '';
            contador++;
        } else {
            fila.style.display = 'none';
        }
    });
    document.getElementById('resultadosProyectos').textContent = 'Mostrando ' + contador + ' de ' + filas.length + ' proyectos';
}

function limpiarFiltroProyectos() {
    document.getElementById('buscarProyecto').value = '';
    document.getElementById('filtroEstado').value = '';
    filtrarProyectos();
}