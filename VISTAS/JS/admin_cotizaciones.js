// admin_cotizaciones.js

// --- FUNCIONES AUXILIARES DE JAVASCRIPT ---
/**
 * Escapa caracteres HTML para prevenir XSS.
 * Es el equivalente en JS de la función htmlspecialchars de PHP.
 * @param {string | null | undefined} str La cadena a escapar.
 * @returns {string} La cadena escapada y segura para inyectar como texto.
 */
function escapeHTML(str) {
    if (str === null || str === undefined) return '';
    return String(str).replace(/[&<>"']/g, function(m) {
        switch (m) {
            case '&': return '&';
            case '<': return '<';
            case '>': return '>';
            case '"': return '"';
            default: return '"';
        }
    });
}

/**
 * Reemplaza los saltos de línea (\n) con etiquetas <br>.
 * Es el equivalente en JS de la función nl2br de PHP.
 * @param {string | null | undefined} str La cadena con saltos de línea.
 * @returns {string} La cadena con etiquetas <br>.
 */
function nl2br(str) {
    if (str === null || str === undefined) return '';
    return String(str).replace(/(\r\n|\n\r|\r|\n)/g, '<br>');
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('admin_cotizaciones.js cargado y listo.');

    // --- MANEJO DEL MODAL DE DETALLES (ADMIN) ---
    const modalDetalleAdmin = document.getElementById('modal-detalle-cotizacion-admin');
    const spanCerrarModalAdmin = document.getElementById('modal-cerrar-detalle-admin');
    const detalleAdminIdModalSpan = document.getElementById('detalle-cotizacion-admin-id-modal');
    const detalleAdminContenidoModal = document.getElementById('detalle-cotizacion-admin-contenido-modal');
    const tablaCotizacionesBody = document.getElementById('tabla-cotizaciones-admin-body');

    // Event listener para botones en la tabla (usando delegación de eventos)
    if (tablaCotizacionesBody) {
        tablaCotizacionesBody.addEventListener('click', function(event) {
            const botonClicado = event.target.closest('.btn-admin-accion');
            if (!botonClicado) return;

            const cotizacionId = botonClicado.dataset.id;
            if (!cotizacionId) {
                console.error("ID de cotización no encontrado en el botón:", botonClicado);
                return;
            }

            if (botonClicado.classList.contains('btn-admin-ver-detalle')) {
                abrirDetalleAdminModal(cotizacionId);
            } else if (botonClicado.classList.contains('btn-admin-aprobar')) {
                gestionarEstadoCotizacion(cotizacionId, 'aprobada_admin', botonClicado);
            } else if (botonClicado.classList.contains('btn-admin-rechazar')) {
                gestionarEstadoCotizacion(cotizacionId, 'rechazado', botonClicado);
            } else if (botonClicado.classList.contains('btn-admin-contactado')) {
                gestionarEstadoCotizacion(cotizacionId, 'contactado', botonClicado);
            } else if (botonClicado.classList.contains('btn-admin-editar')) {
                alert(`Función Editar para ${cotizacionId} no implementada. Redirigiría a un formulario de edición o abriría un modal de edición.`);
                console.log('Admin: Editar cotización ID:', cotizacionId);
            }
        });
    }
    
    function abrirDetalleAdminModal(cotizacionId) {
        detalleAdminIdModalSpan.textContent = cotizacionId;
        detalleAdminContenidoModal.innerHTML = '<p class="loading-message">Cargando detalles (Admin)...</p>';
        modalDetalleAdmin.style.display = 'block';
        fetchDetalleCotizacionAdminModal(cotizacionId);
    }

    if (spanCerrarModalAdmin) {
        spanCerrarModalAdmin.onclick = function() {
            modalDetalleAdmin.style.display = 'none';
        }
    }

    window.onclick = function(event) {
        if (event.target == modalDetalleAdmin) {
            modalDetalleAdmin.style.display = 'none';
        }
    }

    /**
     * Obtiene y muestra los detalles de una cotización en el modal del admin.
     * @param {string} cotizacionId ID de la cotización.
     */
    async function fetchDetalleCotizacionAdminModal(cotizacionId) {
        // CORRECCIÓN DE RUTA: Se añade ../ para subir un nivel desde /VISTAS/ a la raíz y luego entrar a /AJAX/
        const apiUrl = `../AJAX/cotizaciones_ajax.php?action=obtener_detalle_cotizacion_admin&id_cotizacion=${encodeURIComponent(cotizacionId)}`;
        
        try {
            const response = await fetch(apiUrl);
            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`Error de red: ${response.status} ${response.statusText}. Respuesta: ${errorText}`);
            }
            const resultado = await response.json();

            if (resultado.success && resultado.data) {
                renderizarDetalleAdminModal(resultado.data);
            } else {
                mostrarErrorDetalleAdminModal(resultado.message || "No se pudo cargar la información de la cotización (admin).");
            }
        } catch (error) {
            console.error('Error en fetchDetalleCotizacionAdminModal:', error);
            mostrarErrorDetalleAdminModal(`Error de conexión o respuesta inesperada (admin): ${error.message}.`);
        }
    }

    /**
     * Construye el HTML para el contenido del modal de detalle del admin.
     * @param {object} cotData Objeto con los datos de la cotización desde el backend.
     */
    function renderizarDetalleAdminModal(cotData) {
        let fechaSolicitudFormateada = 'N/A';
        if (cotData.cot_fecha_solicitud) {
            try {
                fechaSolicitudFormateada = new Date(cotData.cot_fecha_solicitud).toLocaleString('es-ES', {
                    day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'
                });
            } catch (e) { console.error("Error formateando fecha (admin):", e); }
        }

        // CORRECCIÓN JS: Se usan escapeHTML() y nl2br() en lugar de las funciones de PHP.
        let html = `
            <div class="detalle-grid-admin">
                <div><strong>ID Cotización:</strong></div><div>${cotData.cot_id}</div>
                <div><strong>Cliente:</strong></div><div>${escapeHTML(cotData.nombre_solicitante)} (ID: ${cotData.usu_id_solicitante})</div>
                <div><strong>Email Cliente:</strong></div><div><a href="mailto:${escapeHTML(cotData.email_solicitante)}">${escapeHTML(cotData.email_solicitante)}</a></div>
                <div><strong>Fecha Solicitud:</strong></div><div>${fechaSolicitudFormateada}</div>
                <div><strong>Estado Actual:</strong></div><div><span class="estado-tag estado-${escapeHTML((cotData.cot_estado || '').toLowerCase())}">${escapeHTML(cotData.cot_estado)}</span></div>
                <div><strong>Vehículo Solicitado:</strong></div><div>${escapeHTML(cotData.cot_detalles_vehiculo_solicitado)}</div>
                <div><strong>Monto Estimado:</strong></div><div>${parseFloat(cotData.cot_monto_estimado || 0).toFixed(2)} €</div>
            </div>

            <h4>Mensaje del Solicitante:</h4>
            <p class="mensaje-usuario">${cotData.cot_mensaje ? nl2br(escapeHTML(cotData.cot_mensaje)) : '<em>Sin mensaje del solicitante.</em>'}</p>
            
            <h4>Notas Internas (Admin):</h4>
            <textarea id="admin-notas-internas-modal" class="admin-textarea" rows="4" placeholder="Añadir o editar notas internas...">${escapeHTML(cotData.cot_notas_admin || '')}</textarea>
            <button id="btn-guardar-notas-admin-modal" class="btn-admin-accion btn-guardar-notas" data-id="${cotData.cot_id}"><i class="icon-save"></i> Guardar Notas</button>`;
        
        detalleAdminContenidoModal.innerHTML = html;

        const modalAdminAccionesDiv = modalDetalleAdmin.querySelector('.modal-admin-acciones');
        if (!modalAdminAccionesDiv) {
            console.error("Contenedor de acciones del modal no encontrado.");
            return;
        }
        let accionesHtml = '';
        if (cotData.cot_estado === 'pendiente') {
            accionesHtml += `<button class="btn-admin-accion btn-modal-accion" data-action="aprobada_admin" data-id="${cotData.cot_id}"><i class="icon-check"></i> Aprobar (a aprobada_admin)</button> `;
            accionesHtml += `<button class="btn-admin-accion btn-modal-accion" data-action="rechazado" data-id="${cotData.cot_id}"><i class="icon-cancel"></i> Rechazar</button> `;
        } else if (cotData.cot_estado === 'aprobada_admin') {
            accionesHtml += `<button class="btn-admin-accion btn-modal-accion" data-action="contactado" data-id="${cotData.cot_id}"><i class="icon-phone"></i> Marcar Contactado</button> `;
            accionesHtml += `<button class="btn-admin-accion btn-modal-accion" data-action="pendiente" data-id="${cotData.cot_id}"><i class="icon-undo"></i> Revertir a Pendiente</button> `;
        } else if (cotData.cot_estado === 'contactado') {
             accionesHtml += `<button class="btn-admin-accion btn-modal-accion" data-action="cerrado" data-id="${cotData.cot_id}"><i class="icon-lock"></i> Marcar Cerrado</button> `;
        }
        modalAdminAccionesDiv.innerHTML = accionesHtml;

        addEventListenersToAdminModalButtons(cotData.cot_id);
    }
    
    function mostrarErrorDetalleAdminModal(mensaje) {
        detalleAdminContenidoModal.innerHTML = `<p class="error-message">${escapeHTML(mensaje)}</p>`;
        const modalAdminAccionesDiv = modalDetalleAdmin.querySelector('.modal-admin-acciones');
        if(modalAdminAccionesDiv) modalAdminAccionesDiv.innerHTML = ''; 
    }

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
     * Gestiona el cambio de estado de una cotización.
     * @param {string} cotizacionId ID de la cotización.
     * @param {string} nuevoEstado El nuevo estado.
     * @param {HTMLElement} botonOpcional Botón que disparó la acción.
     */
    async function gestionarEstadoCotizacion(cotizacionId, nuevoEstado, botonOpcional = null) {
        if (!confirm(`¿Está seguro de que desea cambiar el estado de la cotización #${cotizacionId} a "${nuevoEstado.replace(/_/g, ' ')}"?`)) {
            return;
        }

        if (botonOpcional) {
            botonOpcional.disabled = true;
            botonOpcional.innerHTML = '<i class="icon-spinner"></i> Procesando...';
        }

        const formData = new FormData();
        formData.append('action', 'cambiar_estado_cotizacion');
        formData.append('id_cotizacion', cotizacionId);
        formData.append('nuevo_estado', nuevoEstado);

        // CORRECCIÓN DE RUTA: Se añade ../
        const apiUrl = '../AJAX/cotizaciones_ajax.php';
        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                body: formData
            });
            const resultado = await response.json();

            if (resultado.success) {
                alert(resultado.message || 'Estado de la cotización actualizado con éxito.');
                
                const filaCotizacion = tablaCotizacionesBody.querySelector(`tr[data-cotizacion-id="${cotizacionId}"]`);
                if (filaCotizacion) {
                    const celdaEstado = filaCotizacion.querySelector('td:nth-child(6)');
                    if (celdaEstado) {
                        const estadoFormateado = escapeHTML(resultado.nuevo_estado.replace(/_/g,' '));
                        celdaEstado.innerHTML = `<span class="estado-tag estado-${escapeHTML(resultado.nuevo_estado.toLowerCase())}">${estadoFormateado}</span>`;
                    }
                    actualizarBotonesDeAccionFila(filaCotizacion, resultado.nuevo_estado); 
                }

                if (modalDetalleAdmin.style.display === 'block' && detalleAdminIdModalSpan.textContent === cotizacionId) {
                    fetchDetalleCotizacionAdminModal(cotizacionId); 
                }
            } else {
                alert('Error al actualizar el estado: ' + (resultado.message || 'Error desconocido.'));
            }
        } catch (error) {
            console.error('Error en gestionarEstadoCotizacion:', error);
            alert('Ocurrió un error de conexión o respuesta inesperada al intentar actualizar el estado.');
        } finally {
            if (botonOpcional) {
                botonOpcional.disabled = false;
                // El texto del botón se restaurará automáticamente al recargar los detalles del modal
            }
        }
    }

    /**
     * Guarda las notas internas para una cotización (Admin).
     * @param {string} cotizacionId ID de la cotización.
     * @param {string} notas Texto de las notas.
     * @param {HTMLElement} botonOpcional Botón que disparó la acción.
     */
    async function guardarNotasInternas(cotizacionId, notas, botonOpcional = null) {
        if (botonOpcional) {
            botonOpcional.disabled = true;
            botonOpcional.innerHTML = '<i class="icon-spinner"></i> Guardando...';
        }
        
        const formData = new FormData();
        formData.append('action', 'guardar_notas_admin');
        formData.append('id_cotizacion', cotizacionId);
        formData.append('notas_internas', notas);

        // CORRECCIÓN DE RUTA: Se añade ../
        const apiUrl = '../AJAX/cotizaciones_ajax.php';
        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                body: formData
            });
            const resultado = await response.json();

            if (resultado.success) {
                alert(resultado.message || 'Notas internas guardadas con éxito.');
            } else {
                alert('Error al guardar notas: ' + (resultado.message || 'Error desconocido.'));
            }
        } catch (error) {
            console.error('Error en guardarNotasInternas:', error);
            alert('Ocurrió un error de conexión al intentar guardar las notas.');
        } finally {
            if (botonOpcional) {
                botonOpcional.disabled = false;
                botonOpcional.innerHTML = '<i class="icon-save"></i> Guardar Notas';
            }
        }
    }
    
    /**
     * Actualiza los botones de acción en una fila de la tabla principal
     * después de un cambio de estado.
     * @param {HTMLElement} filaElement La fila (TR) de la tabla.
     * @param {string} nuevoEstado El nuevo estado de la cotización.
     */
    function actualizarBotonesDeAccionFila(filaElement, nuevoEstado) {
        const cotId = filaElement.dataset.cotizacionId;
        const accionesTd = filaElement.querySelector('td:last-child'); 
        if (!accionesTd) return;

        let botonesHtml = `<button class="btn-admin-accion btn-admin-ver-detalle" data-id="${cotId}" title="Ver Detalle"><i class="icon-eye"></i></button> `;
        
        if (nuevoEstado === 'pendiente') {
            botonesHtml += `<button class="btn-admin-accion btn-admin-aprobar" data-id="${cotId}" title="Aprobar (a aprobada_admin)"><i class="icon-check"></i></button> `;
            botonesHtml += `<button class="btn-admin-accion btn-admin-rechazar" data-id="${cotId}" title="Rechazar"><i class="icon-cancel"></i></button> `;
        } else if (nuevoEstado === 'aprobada_admin') {
            botonesHtml += `<button class="btn-admin-accion btn-admin-contactado" data-id="${cotId}" title="Marcar como Contactado"><i class="icon-phone"></i></button> `;
        }
        
        botonesHtml += `<button class="btn-admin-accion btn-admin-editar" data-id="${cotId}" title="Editar Cotización (Notas/etc.)"><i class="icon-edit"></i></button>`;
        
        accionesTd.innerHTML = botonesHtml;
    }

    console.log('Funcionalidades de admin_cotizaciones.js completamente inicializadas.');
});