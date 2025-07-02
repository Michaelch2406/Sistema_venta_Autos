// mis_cotizaciones.js

// --- FUNCIONES AUXILIARES DE JAVASCRIPT ---
function escapeHTML(str) {
    if (str === null || str === undefined) return '';
    return String(str).replace(/[&<>"']/g, function(m) {
        switch (m) {
            case '&': return '&';
            case '<': return '<';
            case '>': return '>';
            case '"': return '"';
            default: return '"';     // '
        }
    });
}

function nl2br(str) {
    if (str === null || str === undefined) return '';
    return String(str).replace(/(\r\n|\n\r|\r|\n)/g, '<br>');
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('mis_cotizaciones.js cargado y listo.');

    // --- MANEJO DEL MODAL DE DETALLES (USUARIO) ---
    const modalDetalleUsuario = document.getElementById('modal-detalle-cotizacion');
    const spanCerrarModalUsuario = document.getElementById('modal-cerrar-detalle');
    const detalleUsuarioIdModalSpan = document.getElementById('detalle-cotizacion-id-modal');
    const detalleUsuarioContenidoModal = document.getElementById('detalle-cotizacion-contenido-modal');
    const tablaCotizacionesBody = document.querySelector('#lista-cotizaciones tbody');

    // Event listener para botones "Ver Detalle"
    if (tablaCotizacionesBody) {
        tablaCotizacionesBody.addEventListener('click', function(event) {
            const botonVerDetalle = event.target.closest('.btn-ver-detalle');
            if (botonVerDetalle) {
                const cotizacionId = botonVerDetalle.dataset.id;
                abrirDetalleUsuarioModal(cotizacionId);
            }
        });
    }

    function abrirDetalleUsuarioModal(cotizacionId) {
        if (!modalDetalleUsuario) return;
        detalleUsuarioIdModalSpan.textContent = cotizacionId;
        detalleUsuarioContenidoModal.innerHTML = '<p class="loading-message">Cargando detalles...</p>';
        modalDetalleUsuario.style.display = 'block';
        fetchDetalleCotizacionUsuario(cotizacionId);
    }

    if (spanCerrarModalUsuario) {
        spanCerrarModalUsuario.onclick = function() {
            modalDetalleUsuario.style.display = 'none';
        }
    }

    window.onclick = function(event) {
        if (event.target == modalDetalleUsuario) {
            modalDetalleUsuario.style.display = 'none';
        }
    }

    async function fetchDetalleCotizacionUsuario(cotizacionId) {
        // --- RUTA CORREGIDA ---
        const apiUrl = `../AJAX/cotizaciones_ajax.php?action=obtener_detalle_cotizacion_usuario&id_cotizacion=${encodeURIComponent(cotizacionId)}`;
        
        try {
            const response = await fetch(apiUrl);
            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`Error de red: ${response.status} ${response.statusText}. Respuesta: ${errorText}`);
            }
            const resultado = await response.json();

            if (resultado.success && resultado.data) {
                renderizarDetalleUsuarioModal(resultado.data);
            } else {
                mostrarErrorDetalleUsuarioModal(resultado.message || "No se pudo cargar la información.");
            }
        } catch (error) {
            console.error('Error en fetchDetalleCotizacionUsuario:', error);
            // El error "htmlspecialchars is not defined" que viste venía de aquí, al intentar renderizar el error.
            // Ahora usamos escapeHTML para mostrar el mensaje de error de forma segura.
            mostrarErrorDetalleUsuarioModal(`Error de conexión o respuesta inesperada: ${escapeHTML(error.message)}. Inténtelo más tarde.`);
        }
    }

    function renderizarDetalleUsuarioModal(cotData) {
        let fechaSolicitudFormateada = 'N/A';
        if (cotData.cot_fecha_solicitud) {
            try {
                fechaSolicitudFormateada = new Date(cotData.cot_fecha_solicitud).toLocaleString('es-ES', {
                    day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'
                });
            } catch (e) { console.error("Error formateando fecha (usuario):", e); }
        }

        // Usamos escapeHTML() y nl2br() para mostrar los datos de forma segura
        let html = `
            <div class="detalle-grid-usuario">
                <div><strong>ID Cotización:</strong></div><div>${cotData.cot_id}</div>
                <div><strong>Fecha Solicitud:</strong></div><div>${fechaSolicitudFormateada}</div>
                <div><strong>Estado:</strong></div><div><span class="estado-tag estado-${escapeHTML((cotData.cot_estado || '').toLowerCase())}">${escapeHTML(cotData.cot_estado.replace('_', ' '))}</span></div>
                <div><strong>Vehículo Solicitado:</strong></div><div>${escapeHTML(cotData.cot_detalles_vehiculo_solicitado)}</div>
                <div><strong>Monto Estimado:</strong></div><div><strong>${parseFloat(cotData.cot_monto_estimado || 0).toFixed(2)} €</strong></div>
            </div>
            <h4>Tu Mensaje Enviado:</h4>
            <p class="mensaje-usuario">${cotData.cot_mensaje ? nl2br(escapeHTML(cotData.cot_mensaje)) : '<em>No enviaste un mensaje adicional.</em>'}</p>
            
            <h4>Notas del Administrador:</h4>
            <p class="notas-admin">${cotData.cot_notas_admin ? nl2br(escapeHTML(cotData.cot_notas_admin)) : '<em>Aún no hay notas del administrador.</em>'}</p>`;
        
        detalleUsuarioContenidoModal.innerHTML = html;
        
        // Actualizar botón de imprimir si existe
        const btnImprimir = modalDetalleUsuario.querySelector('.btn-imprimir-cot');
        if(btnImprimir) {
            btnImprimir.dataset.id = cotData.cot_id;
        }
    }
    
    function mostrarErrorDetalleUsuarioModal(mensaje) {
        detalleUsuarioContenidoModal.innerHTML = `<p class="error-message">${escapeHTML(mensaje)}</p>`;
    }
});