// JS/mis_citas.js
document.addEventListener('DOMContentLoaded', function() {
    console.log('mis_citas.js cargado');

    const modalDetalleUsuario = document.getElementById('modal-detalle-cita');
    const spanCerrarModalUsuario = document.getElementById('modal-cerrar-detalle');
    const tablaCitasBody = document.querySelector('#lista-citas tbody');
    
    // Escapa caracteres especiales para HTML
    function escapeHTML(str) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        };
        return String(str || '').replace(/[&<>"']/g, m => map[m]);
    }

    // Convierte saltos de línea en <br>
    function nl2br(str) {
        return String(str || '').replace(/(\r\n|\n\r|\r|\n)/g, '<br>');
    }

    // Formatea la fecha en español
    function formatearFecha(fechaString) {
        if (!fechaString) return 'No definida';
        try {
            return new Date(fechaString).toLocaleString('es-ES', {
                day: '2-digit',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch (e) {
            return fechaString;
        }
    }
    
    // Delegación de eventos en la tabla de citas
    if (tablaCitasBody) {
        tablaCitasBody.addEventListener('click', function(event) {
            const botonVerDetalle = event.target.closest('.btn-ver-detalle');
            if (!botonVerDetalle) return;
            const citaId = botonVerDetalle.dataset.id;
            if (!citaId) return;
            abrirDetalleUsuarioModal(citaId);
        });
    }

    // Abre el modal y carga datos
    function abrirDetalleUsuarioModal(citaId) {
        document.getElementById('detalle-cita-id-modal').textContent = citaId;
        document.getElementById('detalle-cita-contenido-modal').innerHTML =
            '<p class="loading-message">Cargando detalles...</p>';
        modalDetalleUsuario.style.display = 'block';
        fetchDetalleCitaUsuario(citaId);
    }
    
    // Cierra modal al hacer click en la X o fuera
    if (spanCerrarModalUsuario) {
        spanCerrarModalUsuario.onclick = () => {
            modalDetalleUsuario.style.display = 'none';
        };
    }
    window.onclick = (event) => {
        if (event.target === modalDetalleUsuario) {
            modalDetalleUsuario.style.display = 'none';
        }
    };

    // Petición AJAX para obtener detalle de cita
    async function fetchDetalleCitaUsuario(citaId) {
        try {
            const response = await fetch(`./../AJAX/citas_ajax.php?action=obtener_detalle_cita_usuario&id_cita=${citaId}`);
            const resultado = await response.json();
            if (resultado.success) {
                renderizarDetalleUsuarioModal(resultado.data);
            } else {
                document.getElementById('detalle-cita-contenido-modal').innerHTML =
                    `<p class="error">${escapeHTML(resultado.message)}</p>`;
            }
        } catch (error) {
            console.error('Error fetching cita usuario:', error);
            document.getElementById('detalle-cita-contenido-modal').innerHTML =
                '<p class="error">Error de conexión.</p>';
        }
    }
    
    // Renderiza el contenido en el modal
    function renderizarDetalleUsuarioModal(data) {
        const contenido = `
            <div class="detalle-grid-usuario">
                <div><strong>ID Cita:</strong></div>
                <div>${data.cit_id}</div>

                <div><strong>Fecha Solicitud:</strong></div>
                <div>${formatearFecha(data.cit_fecha_solicitud)}</div>

                <div><strong>Estado:</strong></div>
                <div>
                    <span class="estado-tag estado-${escapeHTML(data.cit_estado.toLowerCase())}">
                        ${escapeHTML(data.cit_estado)}
                    </span>
                </div>

                <div><strong>Fecha Estimada Cita:</strong></div>
                <div>${formatearFecha(data.cit_fecha_estimada)}</div>

                <div><strong>Vehículo Solicitado:</strong></div>
                <div>${escapeHTML(data.cit_detalles_vehiculo_solicitado)}</div>
            </div>

            <div class="mt-3">
                <h4>Tu Mensaje Enviado:</h4>
                <div class="p-2 mensaje-usuario">
                    ${data.cit_mensaje ? nl2br(escapeHTML(data.cit_mensaje)) : '<em>No enviaste un mensaje adicional.</em>'}
                </div>
            </div>

            <div class="mt-3">
                <h4>Notas del Gestor:</h4>
                <div class="p-2 notas-admin">
                    ${data.cit_notas_admin ? nl2br(escapeHTML(data.cit_notas_admin)) : '<em>Aún no hay notas del gestor.</em>'}
                </div>
            </div>
        `;
        document.getElementById('detalle-cita-contenido-modal').innerHTML = contenido;
    }
});
