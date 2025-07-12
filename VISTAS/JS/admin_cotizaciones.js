// admin_cotizaciones.js - Versión Mejorada

// --- FUNCIONES AUXILIARES DE JAVASCRIPT MEJORADAS ---
/**
 * Escapa caracteres HTML para prevenir XSS.
 * @param {string | null | undefined} str La cadena a escapar.
 * @returns {string} La cadena escapada y segura.
 */
function escapeHTML(str) {
    if (str === null || str === undefined) return '';
    return String(str).replace(/[&<>"']/g, function(m) {
        switch (m) {
            case '&': return '&amp;';
            case '<': return '&lt;';
            case '>': return '&gt;';
            case '"': return '&quot;';
            default: return '&#39;';
        }
    });
}

/**
 * Reemplaza los saltos de línea (\n) con etiquetas <br>.
 * @param {string | null | undefined} str La cadena con saltos de línea.
 * @returns {string} La cadena con etiquetas <br>.
 */
function nl2br(str) {
    if (str === null || str === undefined) return '';
    return String(str).replace(/(\r\n|\n\r|\r|\n)/g, '<br>');
}

/**
 * Muestra una notificación toast mejorada
 * @param {string} mensaje El mensaje a mostrar
 * @param {string} tipo Tipo de notificación: 'success', 'error', 'warning', 'info'
 * @param {number} duracion Duración en milisegundos (0 = permanente)
 */
function mostrarNotificacion(mensaje, tipo = 'info', duracion = 4000) {
    const iconos = {
        'success': 'bi-check-circle-fill',
        'error': 'bi-exclamation-triangle-fill',
        'warning': 'bi-exclamation-circle-fill',
        'info': 'bi-info-circle-fill'
    };
    
    const colores = {
        'success': 'success',
        'error': 'danger',
        'warning': 'warning',
        'info': 'primary'
    };
    
    // Crear contenedor de notificaciones si no existe
    let contenedor = document.getElementById('notificaciones-container');
    if (!contenedor) {
        contenedor = document.createElement('div');
        contenedor.id = 'notificaciones-container';
        contenedor.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
        `;
        document.body.appendChild(contenedor);
    }
    
    const notificacion = document.createElement('div');
    notificacion.className = `alert alert-${colores[tipo]} alert-dismissible fade show mb-2`;
    notificacion.style.cssText = `
        animation: slideInRight 0.5s ease-out;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border: none;
        border-radius: 8px;
    `;
    
    notificacion.innerHTML = `
        <i class="bi ${iconos[tipo]} me-2"></i>
        ${escapeHTML(mensaje)}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    `;
    
    contenedor.appendChild(notificacion);
    
    // Auto-remover si se especifica duración
    if (duracion > 0) {
        setTimeout(() => {
            if (notificacion.parentNode) {
                notificacion.style.animation = 'slideOutRight 0.3s ease-in';
                setTimeout(() => {
                    if (notificacion.parentNode) {
                        notificacion.remove();
                    }
                }, 300);
            }
        }, duracion);
    }
}

/**
 * Añade animación de carga a un elemento
 * @param {HTMLElement} elemento El elemento al que añadir la animación
 */
function añadirAnimacionCarga(elemento) {
    elemento.classList.add('loading');
    elemento.style.position = 'relative';
}

/**
 * Remueve animación de carga de un elemento
 * @param {HTMLElement} elemento El elemento del que remover la animación
 */
function removerAnimacionCarga(elemento) {
    elemento.classList.remove('loading');
}

/**
 * Formatea una fecha para mostrar de manera amigable
 * @param {string} fechaString Fecha en formato ISO
 * @returns {string} Fecha formateada
 */
function formatearFecha(fechaString) {
    if (!fechaString) return 'N/A';
    try {
        return new Date(fechaString).toLocaleString('es-ES', {
            day: '2-digit', 
            month: '2-digit', 
            year: 'numeric', 
            hour: '2-digit', 
            minute: '2-digit'
        });
    } catch (e) {
        console.error("Error formateando fecha:", e);
        return fechaString;
    }
}

// --- INICIALIZACIÓN PRINCIPAL ---
document.addEventListener('DOMContentLoaded', function() {
    console.log('admin_cotizaciones.js mejorado cargado y listo.');

    // Añadir estilos CSS para animaciones
    const estilosAnimacion = document.createElement('style');
    estilosAnimacion.textContent = `
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(100%); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes slideOutRight {
            from { opacity: 1; transform: translateX(0); }
            to { opacity: 0; transform: translateX(100%); }
        }
        .btn-admin-accion.processing {
            opacity: 0.7;
            pointer-events: none;
            animation: pulse 1s infinite;
        }
        .tabla-fila-actualizada {
            background: linear-gradient(135deg, #e6f3ff, #f0f8ff) !important;
            animation: highlightRow 2s ease-out;
        }
        @keyframes highlightRow {
            0% { background: #4a90e2; color: white; }
            100% { background: linear-gradient(135deg, #e6f3ff, #f0f8ff); color: inherit; }
        }
    `;
    document.head.appendChild(estilosAnimacion);

    // --- VARIABLES PRINCIPALES ---
    const modalDetalleAdmin = document.getElementById('modal-detalle-cotizacion-admin');
    const spanCerrarModalAdmin = document.getElementById('modal-cerrar-detalle-admin');
    const detalleAdminIdModalSpan = document.getElementById('detalle-cotizacion-admin-id-modal');
    const detalleAdminContenidoModal = document.getElementById('detalle-cotizacion-admin-contenido-modal');
    const tablaCotizacionesBody = document.getElementById('tabla-cotizaciones-admin-body');

    // --- MANEJO DE EVENTOS EN LA TABLA (DELEGACIÓN MEJORADA) ---
    if (tablaCotizacionesBody) {
        tablaCotizacionesBody.addEventListener('click', function(event) {
            const botonClicado = event.target.closest('.btn-admin-accion');
            if (!botonClicado) return;

            const cotizacionId = botonClicado.dataset.id;
            if (!cotizacionId) {
                console.error("ID de cotización no encontrado en el botón:", botonClicado);
                mostrarNotificacion('Error: ID de cotización no encontrado', 'error');
                return;
            }

            // Añadir efecto visual al botón clicado
            botonClicado.style.transform = 'scale(0.95)';
            setTimeout(() => {
                botonClicado.style.transform = '';
            }, 150);

            // Manejar diferentes acciones
            if (botonClicado.classList.contains('btn-admin-ver-detalle')) {
                abrirDetalleAdminModal(cotizacionId);
            } else if (botonClicado.classList.contains('btn-admin-aprobar')) {
                gestionarEstadoCotizacion(cotizacionId, 'aprobada_admin', botonClicado);
            } else if (botonClicado.classList.contains('btn-admin-rechazar')) {
                gestionarEstadoCotizacion(cotizacionId, 'rechazado', botonClicado);
            } else if (botonClicado.classList.contains('btn-admin-contactado')) {
                gestionarEstadoCotizacion(cotizacionId, 'contactado', botonClicado);
            } else if (botonClicado.classList.contains('btn-admin-editar')) {
                mostrarModalEdicion(cotizacionId);
            }
        });

        // Añadir efectos hover mejorados a las filas
        tablaCotizacionesBody.addEventListener('mouseenter', function(event) {
            const fila = event.target.closest('tr');
            if (fila && fila.dataset.cotizacionId) {
                fila.style.transform = 'scale(1.01)';
                fila.style.transition = 'all 0.2s ease';
            }
        }, true);

        tablaCotizacionesBody.addEventListener('mouseleave', function(event) {
            const fila = event.target.closest('tr');
            if (fila && fila.dataset.cotizacionId) {
                fila.style.transform = '';
            }
        }, true);
    }

    // --- FUNCIONES PRINCIPALES MEJORADAS ---
    
    /**
     * Abre el modal de detalles con animaciones mejoradas
     * @param {string} cotizacionId ID de la cotización
     */
    function abrirDetalleAdminModal(cotizacionId) {
        detalleAdminIdModalSpan.textContent = cotizacionId;
        detalleAdminContenidoModal.innerHTML = `
            <div class="loading-message">
                <div class="spinner-border text-primary me-2" role="status" aria-hidden="true"></div>
                Cargando detalles de la cotización...
            </div>
        `;
        
        // Mostrar modal con animación
        modalDetalleAdmin.style.display = 'block';
        modalDetalleAdmin.setAttribute('aria-hidden', 'false');
        
        // Añadir clase para animación
        setTimeout(() => {
            modalDetalleAdmin.querySelector('.modal-contenido').style.animation = 'slideInUp 0.4s cubic-bezier(0.25, 0.8, 0.25, 1)';
        }, 10);
        
        fetchDetalleCotizacionAdminModal(cotizacionId);
    }

    /**
     * Cierra el modal con animación
     */
    function cerrarModal() {
        const modalContenido = modalDetalleAdmin.querySelector('.modal-contenido');
        modalContenido.style.animation = 'slideOutDown 0.3s ease-in';
        
        setTimeout(() => {
            modalDetalleAdmin.style.display = 'none';
            modalDetalleAdmin.setAttribute('aria-hidden', 'true');
            modalContenido.style.animation = '';
        }, 300);
    }

    // Event listeners para cerrar modal
    if (spanCerrarModalAdmin) {
        spanCerrarModalAdmin.onclick = cerrarModal;
    }

    // Cerrar modal al hacer clic fuera
    modalDetalleAdmin.addEventListener('click', function(event) {
        if (event.target === modalDetalleAdmin) {
            cerrarModal();
        }
    });

    // Cerrar modal con tecla Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && modalDetalleAdmin.style.display === 'block') {
            cerrarModal();
        }
    });

    /**
     * Obtiene y muestra los detalles de una cotización con manejo de errores mejorado
     * @param {string} cotizacionId ID de la cotización
     */
    async function fetchDetalleCotizacionAdminModal(cotizacionId) {
        const apiUrl = `./../AJAX/cotizaciones_ajax.php?action=obtener_detalle_cotizacion_admin&id_cotizacion=${encodeURIComponent(cotizacionId)}`;
        
        try {
            const response = await fetch(apiUrl);
            
            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`Error de red: ${response.status} ${response.statusText}. Respuesta: ${errorText}`);
            }
            
            const resultado = await response.json();

            if (resultado.success && resultado.data) {
                renderizarDetalleAdminModal(resultado.data);
                mostrarNotificacion('Detalles cargados correctamente', 'success', 2000);
            } else {
                mostrarErrorDetalleAdminModal(resultado.message || "No se pudo cargar la información de la cotización.");
                mostrarNotificacion('Error al cargar detalles', 'error');
            }
        } catch (error) {
            console.error('Error en fetchDetalleCotizacionAdminModal:', error);
            mostrarErrorDetalleAdminModal(`Error de conexión: ${error.message}`);
            mostrarNotificacion('Error de conexión', 'error');
        }
    }

    /**
     * Renderiza el contenido del modal con diseño mejorado
     * @param {object} cotData Datos de la cotización
     */
    function renderizarDetalleAdminModal(cotData) {
        const fechaSolicitudFormateada = formatearFecha(cotData.cot_fecha_solicitud);

        let html = `
            <div class="detalle-grid-admin">
                <div><i class="bi bi-hash me-1"></i><strong>ID Cotización:</strong></div>
                <div><span class="badge bg-primary">${escapeHTML(cotData.cot_id)}</span></div>
                
                <div><i class="bi bi-person me-1"></i><strong>Cliente:</strong></div>
                <div>${escapeHTML(cotData.nombre_solicitante)} <small class="text-muted">(ID: ${cotData.usu_id_solicitante})</small></div>
                
                <div><i class="bi bi-envelope me-1"></i><strong>Email Cliente:</strong></div>
                <div><a href="mailto:${escapeHTML(cotData.email_solicitante)}" class="text-decoration-none">${escapeHTML(cotData.email_solicitante)}</a></div>
                
                <div><i class="bi bi-calendar me-1"></i><strong>Fecha Solicitud:</strong></div>
                <div><time datetime="${escapeHTML(cotData.cot_fecha_solicitud)}">${fechaSolicitudFormateada}</time></div>
                
                <div><i class="bi bi-flag me-1"></i><strong>Estado Actual:</strong></div>
                <div><span class="estado-tag estado-${escapeHTML((cotData.cot_estado || '').toLowerCase())}">${escapeHTML(cotData.cot_estado)}</span></div>
                
                <div><i class="bi bi-car-front me-1"></i><strong>Vehículo Solicitado:</strong></div>
                <div>${escapeHTML(cotData.cot_detalles_vehiculo_solicitado)}</div>
                
                <div><i class="bi bi-currency-euro me-1"></i><strong>Monto Estimado:</strong></div>
                <div><strong class="text-success">${parseFloat(cotData.cot_monto_estimado || 0).toFixed(2)} €</strong></div>
            </div>

            <div class="mt-4">
                <h4><i class="bi bi-chat-text me-2"></i>Mensaje del Solicitante:</h4>
                <div class="p-3 bg-light rounded border">
                    ${cotData.cot_mensaje ? nl2br(escapeHTML(cotData.cot_mensaje)) : '<em class="text-muted">Sin mensaje del solicitante.</em>'}
                </div>
            </div>
            
            <div class="mt-4">
                <h4><i class="bi bi-journal-text me-2"></i>Notas Internas (Admin):</h4>
                <textarea id="admin-notas-internas-modal" class="admin-textarea form-control" rows="4" 
                          placeholder="Añadir o editar notas internas para uso del equipo administrativo...">${escapeHTML(cotData.cot_notas_admin || '')}</textarea>
                <button id="btn-guardar-notas-admin-modal" class="btn-guardar-notas mt-2" data-id="${cotData.cot_id}">
                    <i class="bi bi-save me-2"></i>Guardar Notas
                </button>
            </div>
        `;
        
        detalleAdminContenidoModal.innerHTML = html;

        // Generar botones de acción según el estado
        generarBotonesAccionModal(cotData);
        
        // Añadir event listeners
        addEventListenersToAdminModalButtons(cotData.cot_id);
    }

    /**
     * Genera los botones de acción según el estado de la cotización
     * @param {object} cotData Datos de la cotización
     */
    function generarBotonesAccionModal(cotData) {
        const modalAdminAccionesDiv = modalDetalleAdmin.querySelector('.modal-admin-acciones');
        if (!modalAdminAccionesDiv) {
            console.error("Contenedor de acciones del modal no encontrado.");
            return;
        }

        let accionesHtml = '';
        
        if (cotData.cot_estado === 'pendiente') {
            accionesHtml += `
                <button class="btn-admin-accion btn-modal-accion me-2" data-action="aprobada_admin" data-id="${cotData.cot_id}">
                    <i class="bi bi-check-lg me-2"></i>Aprobar Cotización
                </button>
                <button class="btn-admin-accion btn-modal-accion btn-modal-rechazar" data-action="rechazado" data-id="${cotData.cot_id}">
                    <i class="bi bi-x-lg me-2"></i>Rechazar
                </button>
            `;
        } else if (cotData.cot_estado === 'aprobada_admin') {
            accionesHtml += `
                <button class="btn-admin-accion btn-modal-accion me-2" data-action="contactado" data-id="${cotData.cot_id}">
                    <i class="bi bi-telephone me-2"></i>Marcar Contactado
                </button>
                <button class="btn-admin-accion btn-modal-accion" data-action="pendiente" data-id="${cotData.cot_id}">
                    <i class="bi bi-arrow-counterclockwise me-2"></i>Revertir a Pendiente
                </button>
            `;
        } else if (cotData.cot_estado === 'contactado') {
            accionesHtml += `
                <button class="btn-admin-accion btn-modal-accion" data-action="cerrado" data-id="${cotData.cot_id}">
                    <i class="bi bi-lock me-2"></i>Marcar Cerrado
                </button>
            `;
        }
        
        modalAdminAccionesDiv.innerHTML = accionesHtml;
    }
    
    /**
     * Muestra error en el modal con diseño mejorado
     * @param {string} mensaje Mensaje de error
     */
    function mostrarErrorDetalleAdminModal(mensaje) {
        detalleAdminContenidoModal.innerHTML = `
            <div class="error-message text-center">
                <i class="bi bi-exclamation-triangle display-4 text-danger mb-3"></i>
                <h5>Error al cargar detalles</h5>
                <p>${escapeHTML(mensaje)}</p>
                <button class="btn btn-outline-primary" onclick="location.reload()">
                    <i class="bi bi-arrow-clockwise me-2"></i>Recargar página
                </button>
            </div>
        `;
        
        const modalAdminAccionesDiv = modalDetalleAdmin.querySelector('.modal-admin-acciones');
        if (modalAdminAccionesDiv) modalAdminAccionesDiv.innerHTML = '';
    }

    /**
     * Añade event listeners a los botones del modal
     * @param {string} cotizacionId ID de la cotización
     */
    function addEventListenersToAdminModalButtons(cotizacionId) {
        const btnGuardarNotasModal = document.getElementById('btn-guardar-notas-admin-modal');
        if (btnGuardarNotasModal) {
            btnGuardarNotasModal.addEventListener('click', function() {
                const notas = document.getElementById('admin-notas-internas-modal').value;
                guardarNotasInternas(this.dataset.id, notas, this);
            });
        }

        const botonesDeAccionModal = modalDetalleAdmin.querySelectorAll('.btn-modal-accion');
        botonesDeAccionModal.forEach(boton => {
            boton.addEventListener('click', function() {
                const accion = this.dataset.action;
                const idCot = this.dataset.id;
                gestionarEstadoCotizacion(idCot, accion, this);
            });
        });
    }
    
    /**
     * Gestiona el cambio de estado con confirmación mejorada y animaciones
     * @param {string} cotizacionId ID de la cotización
     * @param {string} nuevoEstado Nuevo estado
     * @param {HTMLElement} botonOpcional Botón que disparó la acción
     */
    async function gestionarEstadoCotizacion(cotizacionId, nuevoEstado, botonOpcional = null) {
        const estadoFormateado = nuevoEstado.replace(/_/g, ' ');
        
        // Confirmación mejorada con SweetAlert-style
        const confirmacion = confirm(`¿Está seguro de que desea cambiar el estado de la cotización #${cotizacionId} a "${estadoFormateado}"?\n\nEsta acción se registrará en el historial.`);
        
        if (!confirmacion) return;

        // Añadir estado de procesamiento
        if (botonOpcional) {
            botonOpcional.disabled = true;
            botonOpcional.classList.add('processing');
            const iconoOriginal = botonOpcional.innerHTML;
            botonOpcional.innerHTML = '<i class="bi bi-hourglass-split"></i> Procesando...';
            botonOpcional.dataset.iconoOriginal = iconoOriginal;
        }

        const formData = new FormData();
        formData.append('action', 'cambiar_estado_cotizacion');
        formData.append('id_cotizacion', cotizacionId);
        formData.append('nuevo_estado', nuevoEstado);

        const apiUrl = './../AJAX/cotizaciones_ajax.php';
        
        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                body: formData
            });
            
            const resultado = await response.json();

            if (resultado.success) {
                mostrarNotificacion(`Estado actualizado a "${estadoFormateado}" correctamente`, 'success');
                
                // Actualizar fila en la tabla con animación
                const filaCotizacion = tablaCotizacionesBody.querySelector(`tr[data-cotizacion-id="${cotizacionId}"]`);
                if (filaCotizacion) {
                    // Añadir clase de animación
                    filaCotizacion.classList.add('tabla-fila-actualizada');
                    
                    // Actualizar celda de estado
                    const celdaEstado = filaCotizacion.querySelector('td:nth-child(6)');
                    if (celdaEstado) {
                        const estadoFormateadoDisplay = escapeHTML(resultado.nuevo_estado.replace(/_/g,' '));
                        celdaEstado.innerHTML = `<span class="estado-tag estado-${escapeHTML(resultado.nuevo_estado.toLowerCase())}">${estadoFormateadoDisplay}</span>`;
                    }
                    
                    // Actualizar botones de acción
                    actualizarBotonesDeAccionFila(filaCotizacion, resultado.nuevo_estado);
                    
                    // Remover clase de animación después de un tiempo
                    setTimeout(() => {
                        filaCotizacion.classList.remove('tabla-fila-actualizada');
                    }, 2000);
                }

                // Recargar modal si está abierto para esta cotización
                if (modalDetalleAdmin.style.display === 'block' && detalleAdminIdModalSpan.textContent === cotizacionId) {
                    setTimeout(() => {
                        fetchDetalleCotizacionAdminModal(cotizacionId);
                    }, 500);
                }
            } else {
                mostrarNotificacion(`Error al actualizar estado: ${resultado.message || 'Error desconocido'}`, 'error');
            }
        } catch (error) {
            console.error('Error en gestionarEstadoCotizacion:', error);
            mostrarNotificacion('Error de conexión al actualizar el estado', 'error');
        } finally {
            // Restaurar botón
            if (botonOpcional) {
                botonOpcional.disabled = false;
                botonOpcional.classList.remove('processing');
                if (botonOpcional.dataset.iconoOriginal) {
                    botonOpcional.innerHTML = botonOpcional.dataset.iconoOriginal;
                }
            }
        }
    }

    /**
     * Guarda las notas internas con feedback mejorado
     * @param {string} cotizacionId ID de la cotización
     * @param {string} notas Texto de las notas
     * @param {HTMLElement} botonOpcional Botón que disparó la acción
     */
    async function guardarNotasInternas(cotizacionId, notas, botonOpcional = null) {
        if (botonOpcional) {
            botonOpcional.disabled = true;
            const iconoOriginal = botonOpcional.innerHTML;
            botonOpcional.innerHTML = '<i class="bi bi-hourglass-split"></i> Guardando...';
            botonOpcional.dataset.iconoOriginal = iconoOriginal;
        }
        
        const formData = new FormData();
        formData.append('action', 'guardar_notas_admin');
        formData.append('id_cotizacion', cotizacionId);
        formData.append('notas_internas', notas);

        const apiUrl = './../AJAX/cotizaciones_ajax.php';
        
        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                body: formData
            });
            
            const resultado = await response.json();

            if (resultado.success) {
                mostrarNotificacion('Notas internas guardadas correctamente', 'success', 3000);
                
                // Efecto visual en el textarea
                const textarea = document.getElementById('admin-notas-internas-modal');
                if (textarea) {
                    textarea.style.borderColor = '#48bb78';
                    setTimeout(() => {
                        textarea.style.borderColor = '';
                    }, 2000);
                }
            } else {
                mostrarNotificacion(`Error al guardar notas: ${resultado.message || 'Error desconocido'}`, 'error');
            }
        } catch (error) {
            console.error('Error en guardarNotasInternas:', error);
            mostrarNotificacion('Error de conexión al guardar las notas', 'error');
        } finally {
            if (botonOpcional) {
                botonOpcional.disabled = false;
                if (botonOpcional.dataset.iconoOriginal) {
                    botonOpcional.innerHTML = botonOpcional.dataset.iconoOriginal;
                }
            }
        }
    }
    
    /**
     * Actualiza los botones de acción en una fila de la tabla
     * @param {HTMLElement} filaElement La fila (TR) de la tabla
     * @param {string} nuevoEstado El nuevo estado de la cotización
     */
    function actualizarBotonesDeAccionFila(filaElement, nuevoEstado) {
        const cotId = filaElement.dataset.cotizacionId;
        const accionesTd = filaElement.querySelector('td:last-child .btn-group');
        if (!accionesTd) return;

        let botonesHtml = `
            <button class="btn-admin-accion btn-admin-ver-detalle" data-id="${cotId}" 
                    title="Ver detalles de la cotización" aria-label="Ver detalles de la cotización ${cotId}">
                <i class="bi bi-eye" aria-hidden="true"></i>
            </button>
        `;
        
        if (nuevoEstado === 'pendiente') {
            botonesHtml += `
                <button class="btn-admin-accion btn-admin-aprobar" data-id="${cotId}" 
                        title="Aprobar cotización" aria-label="Aprobar cotización ${cotId}">
                    <i class="bi bi-check-lg" aria-hidden="true"></i>
                </button>
                <button class="btn-admin-accion btn-admin-rechazar" data-id="${cotId}" 
                        title="Rechazar cotización" aria-label="Rechazar cotización ${cotId}">
                    <i class="bi bi-x-lg" aria-hidden="true"></i>
                </button>
            `;
        } else if (nuevoEstado === 'aprobada_admin') {
            botonesHtml += `
                <button class="btn-admin-accion btn-admin-contactado" data-id="${cotId}" 
                        title="Marcar como contactado" aria-label="Marcar como contactado cotización ${cotId}">
                    <i class="bi bi-telephone" aria-hidden="true"></i>
                </button>
            `;
        }
        
        botonesHtml += `
            <button class="btn-admin-accion btn-admin-editar" data-id="${cotId}" 
                    title="Editar cotización" aria-label="Editar cotización ${cotId}">
                <i class="bi bi-pencil" aria-hidden="true"></i>
            </button>
        `;
        
        accionesTd.innerHTML = botonesHtml;
    }

    /**
     * Muestra modal de edición (placeholder para funcionalidad futura)
     * @param {string} cotizacionId ID de la cotización
     */
    function mostrarModalEdicion(cotizacionId) {
        mostrarNotificacion(`Función de edición para cotización #${cotizacionId} en desarrollo`, 'info', 3000);
        console.log('Admin: Editar cotización ID:', cotizacionId);
        // TODO: Implementar modal de edición completo
    }

    // --- MEJORAS ADICIONALES ---

    // Añadir indicador de carga a los filtros
    const formFiltros = document.getElementById('form-filtros-admin');
    if (formFiltros) {
        formFiltros.addEventListener('submit', function() {
            const btnFiltrar = formFiltros.querySelector('.btn-filtrar');
            if (btnFiltrar) {
                btnFiltrar.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Filtrando...';
                btnFiltrar.disabled = true;
            }
        });
    }

    // Mejorar accesibilidad con navegación por teclado
    document.addEventListener('keydown', function(event) {
        // Navegación con flechas en la tabla
        if (event.target.closest('table')) {
            const filaActual = event.target.closest('tr');
            if (filaActual && (event.key === 'ArrowUp' || event.key === 'ArrowDown')) {
                event.preventDefault();
                const filas = Array.from(tablaCotizacionesBody.querySelectorAll('tr'));
                const indiceActual = filas.indexOf(filaActual);
                
                let nuevaFila;
                if (event.key === 'ArrowUp' && indiceActual > 0) {
                    nuevaFila = filas[indiceActual - 1];
                } else if (event.key === 'ArrowDown' && indiceActual < filas.length - 1) {
                    nuevaFila = filas[indiceActual + 1];
                }
                
                if (nuevaFila) {
                    nuevaFila.querySelector('.btn-admin-ver-detalle')?.focus();
                }
            }
        }
    });

    // Añadir tooltips mejorados
    function inicializarTooltips() {
        const elementosConTooltip = document.querySelectorAll('[title]');
        elementosConTooltip.forEach(elemento => {
            elemento.addEventListener('mouseenter', function() {
                const titulo = this.getAttribute('title');
                if (titulo) {
                    // Crear tooltip personalizado si no existe
                    let tooltip = document.getElementById('custom-tooltip');
                    if (!tooltip) {
                        tooltip = document.createElement('div');
                        tooltip.id = 'custom-tooltip';
                        tooltip.style.cssText = `
                            position: absolute;
                            background: rgba(0,0,0,0.8);
                            color: white;
                            padding: 8px 12px;
                            border-radius: 4px;
                            font-size: 12px;
                            z-index: 10000;
                            pointer-events: none;
                            opacity: 0;
                            transition: opacity 0.2s;
                        `;
                        document.body.appendChild(tooltip);
                    }
                    
                    tooltip.textContent = titulo;
                    tooltip.style.opacity = '1';
                    
                    // Posicionar tooltip
                    const rect = this.getBoundingClientRect();
                    tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
                    tooltip.style.top = rect.top - tooltip.offsetHeight - 8 + 'px';
                }
            });
            
            elemento.addEventListener('mouseleave', function() {
                const tooltip = document.getElementById('custom-tooltip');
                if (tooltip) {
                    tooltip.style.opacity = '0';
                }
            });
        });
    }

    // Inicializar tooltips
    inicializarTooltips();

    // Añadir contador de resultados dinámico
    function actualizarContadorResultados() {
        const filas = tablaCotizacionesBody.querySelectorAll('tr');
        const contador = document.querySelector('#resultados-heading .badge');
        if (contador) {
            contador.textContent = `${filas.length} resultados`;
        }
    }

    console.log('Funcionalidades mejoradas de admin_cotizaciones.js completamente inicializadas.');
    
    // Mostrar notificación de bienvenida
    setTimeout(() => {
        mostrarNotificacion('Panel de administración cargado correctamente', 'success', 3000);
    }, 1000);
});

