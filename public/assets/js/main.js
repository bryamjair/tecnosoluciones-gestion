// ============================================
// INICIALIZACION - Se ejecuta al cargar la pagina
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    initDeleteConfirmations();
    autoCloseAlerts();
    initDynamicTareas();
    initFormValidation();
    initPasswordStrength();
});

// ============================================
// CONFIRMACIONES DE ELIMINACION
// ============================================
function initDeleteConfirmations() {
    document.querySelectorAll('.btn-delete, .delete-confirm, a[onclick*="confirm"]').forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('¿Eliminar este elemento?')) {
                e.preventDefault();
            }
        });
    });
}

// ============================================
// CIERRE AUTOMATICO DE ALERTAS
// ============================================
function autoCloseAlerts() {
    document.querySelectorAll('.alert:not(.alert-permanent)').forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 5000);
    });
}

// ============================================
// TAREAS DINAMICAS POR PROYECTO
// ============================================
function initDynamicTareas() {
    let proyectoSelect = document.getElementById('proyecto_id');
    let tareaSelect = document.getElementById('tarea_id');
    if (proyectoSelect && tareaSelect) {
        proyectoSelect.addEventListener('change', function() {
            let proyectoId = this.value;
            if (proyectoId) {
                fetch('index.php?action=tareas&sub=porProyecto&proyecto_id=' + proyectoId)
                    .then(response => response.json())
                    .then(data => {
                        tareaSelect.innerHTML = '<option value="">Seleccione una tarea</option>';
                        data.forEach(tarea => {
                            tareaSelect.innerHTML += '<option value="' + tarea.id + '">' + tarea.titulo + '</option>';
                        });
                    });
            } else {
                tareaSelect.innerHTML = '<option value="">Primero seleccione un proyecto</option>';
            }
        });
    }
}

// ============================================
// VALIDACION DE FORMULARIOS EN TIEMPO REAL
// ============================================
function initFormValidation() {
    // Validar telefono
    document.querySelectorAll('input[name="telefono"]').forEach(input => {
        input.addEventListener('input', function() {
            let value = this.value;
            let cleanValue = value.replace(/[\s\-\(\)]/g, '');
            let isValid = /^[+]?[0-9]{7,15}$/.test(cleanValue);
            if (value && !isValid) {
                this.style.borderBottomColor = '#dc2626';
                this.setAttribute('title', 'El teléfono debe tener 7-15 dígitos');
            } else {
                this.style.borderBottomColor = '#0d9488';
                this.removeAttribute('title');
            }
        });
    });

    // Validar email
    document.querySelectorAll('input[type="email"]').forEach(input => {
        input.addEventListener('input', function() {
            let isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.value);
            if (this.value && !isValid) {
                this.style.borderBottomColor = '#dc2626';
            } else {
                this.style.borderBottomColor = '#0d9488';
            }
        });
    });

    // Confirmar contraseña
    document.querySelectorAll('input[name="confirm_password"], input[name="confirmar_password"]').forEach(input => {
        input.addEventListener('input', function() {
            let passwordField = document.querySelector('input[name="password"]') || 
                               document.querySelector('input[name="nueva_password"]');
            if (passwordField && this.value) {
                if (this.value !== passwordField.value) {
                    this.style.borderBottomColor = '#dc2626';
                    this.setAttribute('title', 'Las contraseñas no coinciden');
                } else {
                    this.style.borderBottomColor = '#0d9488';
                    this.removeAttribute('title');
                }
            }
        });
    });
}

// ============================================
// INDICADOR DE FORTALEZA DE CONTRASEÑA
// ============================================
function initPasswordStrength() {
    document.querySelectorAll('input[type="password"]').forEach(input => {
        input.addEventListener('input', function() {
            let password = this.value;
            let strength = getPasswordStrength(password);
            showPasswordStrength(this, strength);
        });
    });
}

function getPasswordStrength(password) {
    let score = 0;
    if (password.length >= 4) score++;
    if (password.length >= 8) score++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score++;
    if (/\d/.test(password)) score++;
    if (/[^a-zA-Z0-9]/.test(password)) score++;
    return score;
}

function showPasswordStrength(input, strength) {
    let indicator = input.parentElement.querySelector('.password-strength');
    if (!indicator) {
        indicator = document.createElement('div');
        indicator.className = 'password-strength';
        indicator.style.cssText = 'height:4px;border-radius:4px;margin-top:4px;transition:all 0.3s;';
        input.parentElement.appendChild(indicator);
    }
    
    let colors = ['#e2e8f0', '#ef4444', '#f59e0b', '#3b82f6', '#10b981'];
    let widths = ['0%', '25%', '50%', '75%', '100%'];
    let labels = ['', 'Debil', 'Media', 'Fuerte', 'Muy fuerte'];
    
    indicator.style.width = widths[strength] || '0%';
    indicator.style.background = colors[strength] || '#e2e8f0';
    indicator.style.height = '4px';
    indicator.style.borderRadius = '4px';
    indicator.style.marginTop = '4px';
    indicator.style.transition = 'all 0.3s';
    
    if (strength > 0) {
        let label = input.parentElement.querySelector('.strength-label');
        if (!label) {
            label = document.createElement('span');
            label.className = 'strength-label';
            label.style.cssText = 'font-size:0.6rem;color:#94a3b8;margin-top:2px;display:block;';
            input.parentElement.appendChild(label);
        }
        label.textContent = 'Fortaleza: ' + labels[strength];
    }
}

// ============================================
// CONFIRMAR ELIMINACION CON NOMBRE
// ============================================
function confirmarEliminar(nombre) {
    return confirm('¿Eliminar "' + nombre + '"? Esta acción no se puede deshacer.');
}

// ============================================
// BUSQUEDA EN TIEMPO REAL (CLIENTES)
// ============================================
let searchTimeout = null;

function buscarClientes() {
    clearTimeout(searchTimeout);
    let busqueda = document.getElementById('buscarCliente').value;
    
    searchTimeout = setTimeout(function() {
        let tableBody = document.querySelector('#tablaClientes tbody');
        if (!tableBody) return;
        
        tableBody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:20px;">Buscando...</td></tr>';
        
        fetch('index.php?action=api&sub=buscar_clientes&q=' + encodeURIComponent(busqueda))
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    actualizarTablaClientes(data.clientes, data.total);
                } else {
                    tableBody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:20px;color:#94a3b8;">Error al buscar</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                tableBody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:20px;color:#94a3b8;">Error al buscar</td></tr>';
            });
    }, 300);
}

function actualizarTablaClientes(clientes, total) {
    let tableBody = document.querySelector('#tablaClientes tbody');
    if (!tableBody) return;
    
    if (!clientes || clientes.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:20px;color:#94a3b8;">No se encontraron clientes</td></tr>';
        document.getElementById('resultadosCount').textContent = 'Mostrando 0 de ' + total + ' clientes';
        return;
    }
    
    let html = '';
    clientes.forEach(c => {
        html += `
            <tr>
                <td style="padding:12px;border-bottom:1px solid #f1f5f9;">${c.id}</td>
                <td style="padding:12px;border-bottom:1px solid #f1f5f9;"><strong>${escapeHtml(c.nombre)}</strong></td>
                <td style="padding:12px;border-bottom:1px solid #f1f5f9;">${escapeHtml(c.email)}</td>
                <td style="padding:12px;border-bottom:1px solid #f1f5f9;">${escapeHtml(c.telefono)}</td>
                <td style="padding:12px;border-bottom:1px solid #f1f5f9;">${escapeHtml(c.empresa)}</td>
                <td style="padding:12px;border-bottom:1px solid #f1f5f9;">
                    <a href="index.php?action=clientes&sub=editar&id=${c.id}" class="btn btn-outline btn-sm">Editar</a>
                    <a href="index.php?action=clientes&sub=eliminar&id=${c.id}" class="btn btn-danger btn-sm" onclick="return confirmarEliminar('${escapeHtml(c.nombre)}')">Eliminar</a>
                </td>
            </tr>
        `;
    });
    
    tableBody.innerHTML = html;
    document.getElementById('resultadosCount').textContent = 'Mostrando ' + clientes.length + ' de ' + total + ' clientes';
}

function escapeHtml(text) {
    if (!text) return '';
    return text.toString().replace(/[&<>"]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        if (m === '"') return '&quot;';
        return m;
    });
}

function limpiarFiltro() {
    document.getElementById('buscarCliente').value = '';
    buscarClientes();
}

// ============================================
// BUSQUEDA EN TIEMPO REAL (PROYECTOS)
// ============================================
let searchProyectosTimeout = null;

function buscarProyectos() {
    clearTimeout(searchProyectosTimeout);
    let busqueda = document.getElementById('buscarProyecto').value;
    let estado = document.getElementById('filtroEstado').value;
    
    searchProyectosTimeout = setTimeout(function() {
        let tableBody = document.querySelector('#tablaProyectos tbody');
        if (!tableBody) return;
        
        tableBody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:20px;">Buscando...</td></tr>';
        
        let url = 'index.php?action=api&sub=buscar_proyectos&q=' + encodeURIComponent(busqueda);
        if (estado) url += '&estado=' + encodeURIComponent(estado);
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    actualizarTablaProyectos(data.proyectos, data.total);
                } else {
                    tableBody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:20px;color:#94a3b8;">Error al buscar</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                tableBody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:20px;color:#94a3b8;">Error al buscar</td></tr>';
            });
    }, 300);
}

function actualizarTablaProyectos(proyectos, total) {
    let tableBody = document.querySelector('#tablaProyectos tbody');
    if (!tableBody) return;
    
    if (!proyectos || proyectos.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:20px;color:#94a3b8;">No se encontraron proyectos</td></tr>';
        document.getElementById('resultadosProyectos').textContent = 'Mostrando 0 de ' + total + ' proyectos';
        return;
    }
    
    let html = '';
    proyectos.forEach(p => {
        let estadoClass = '';
        let estadoTexto = '';
        if (p.estado == 'pendiente') {
            estadoClass = 'badge-pendiente';
            estadoTexto = 'Pendiente';
        } else if (p.estado == 'en_progreso') {
            estadoClass = 'badge-progreso';
            estadoTexto = 'En Progreso';
        } else {
            estadoClass = 'badge-completado';
            estadoTexto = 'Completado';
        }
        
        html += `
            <tr>
                <td style="padding:12px;border-bottom:1px solid #f1f5f9;">${p.id}</td>
                <td style="padding:12px;border-bottom:1px solid #f1f5f9;"><strong>${escapeHtml(p.nombre)}</strong></td>
                <td style="padding:12px;border-bottom:1px solid #f1f5f9;">${escapeHtml(p.cliente_nombre || 'Sin cliente')}</td>
                <td style="padding:12px;border-bottom:1px solid #f1f5f9;">${p.fecha_inicio || '-'}</td>
                <td style="padding:12px;border-bottom:1px solid #f1f5f9;">${p.fecha_fin || '-'}</td>
                <td style="padding:12px;border-bottom:1px solid #f1f5f9;">
                    <span class="badge ${estadoClass}">${estadoTexto}</span>
                </td>
                <td style="padding:12px;border-bottom:1px solid #f1f5f9;">
                    <a href="index.php?action=proyectos&sub=editar&id=${p.id}" class="btn btn-outline btn-sm">Editar</a>
                    <a href="index.php?action=proyectos&sub=eliminar&id=${p.id}" class="btn btn-danger btn-sm" onclick="return confirm('Eliminar este proyecto?')">Eliminar</a>
                </td>
            </tr>
        `;
    });
    
    tableBody.innerHTML = html;
    document.getElementById('resultadosProyectos').textContent = 'Mostrando ' + proyectos.length + ' de ' + total + ' proyectos';
}

function limpiarFiltroProyectos() {
    document.getElementById('buscarProyecto').value = '';
    document.getElementById('filtroEstado').value = '';
    buscarProyectos();
}

// ============================================
// FUNCIONES DE LEGADO PARA COMPATIBILIDAD
// ============================================
function filtrarClientes() {
    buscarClientes();
}

function filtrarProyectos() {
    buscarProyectos();
}