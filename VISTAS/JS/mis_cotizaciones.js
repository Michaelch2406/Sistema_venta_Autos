$(document).ready(function() {
    const container = $('#cotizacionesContainer');
    const loadingIndicator = $('#loadingIndicator');
    const noCotizacionesMessage = $('#noCotizacionesMessage');

    function getStatusBadge(estado) {
        const estadoLower = estado.toLowerCase();
        return `<span class="badge badge-${estadoLower}">${estado.charAt(0).toUpperCase() + estado.slice(1)}</span>`;
    }

    function renderCotizacionCard(cotizacion) {
        const fecha = new Date(cotizacion.cot_fecha_solicitud).toLocaleDateString('es-ES', {
            year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'
        });

        return `
            <div class="col-lg-6">
                <div class="card cotizacion-card border-${cotizacion.cot_estado.toLowerCase()}">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-card-text me-2"></i>
                            <strong>Vehículo:</strong> ${cotizacion.cot_detalles_vehiculo_solicitado}
                        </div>
                        <span class="text-muted small">${fecha}</span>
                    </div>
                    <div class="card-body">
                        <div class="solicitante-info mb-3">
                            <h5 class="card-title mb-3">Información del Solicitante</h5>
                            <p><i class="bi bi-person-fill me-2"></i>${cotizacion.usu_nombre} ${cotizacion.usu_apellido}</p>
                            <p><i class="bi bi-envelope-fill me-2"></i><a href="mailto:${cotizacion.usu_email}">${cotizacion.usu_email}</a></p>
                            <p><i class="bi bi-telephone-fill me-2"></i><a href="tel:${cotizacion.usu_telefono}">${cotizacion.usu_telefono}</a></p>
                        </div>
                        <p class="mb-2"><strong>Mensaje:</strong></p>
                        <div class="mensaje-solicitante">
                            ${cotizacion.cot_mensaje}
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Estado:</strong> ${getStatusBadge(cotizacion.cot_estado)}
                        </div>
                        <div class="d-flex align-items-center">
                            <label for="status-select-${cotizacion.cot_id}" class="form-label me-2 small mb-0">Cambiar a:</label>
                            <select class="form-select form-select-sm status-select" data-cot-id="${cotizacion.cot_id}">
                                <option value="pendiente" ${cotizacion.cot_estado === 'pendiente' ? 'selected' : ''}>Pendiente</option>
                                <option value="contactado" ${cotizacion.cot_estado === 'contactado' ? 'selected' : ''}>Contactado</option>
                                <option value="cerrado" ${cotizacion.cot_estado === 'cerrado' ? 'selected' : ''}>Cerrado</option>
                                <option value="rechazado" ${cotizacion.cot_estado === 'rechazado' ? 'selected' : ''}>Rechazado</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>`;
    }

    function cargarCotizaciones() {
        loadingIndicator.show();
        noCotizacionesMessage.hide();
        container.empty().append(loadingIndicator); // Mover el spinner dentro del contenedor

        $.ajax({
            url: '../AJAX/cotizaciones_ajax.php',
            type: 'GET',
            data: { accion: 'getMisCotizaciones' },
            dataType: 'json',
            success: function(response) {
                loadingIndicator.hide();
                if (response.status === 'success' && response.data.length > 0) {
                    response.data.forEach(cotizacion => {
                        container.append(renderCotizacionCard(cotizacion));
                    });
                } else {
                    noCotizacionesMessage.show();
                }
            },
            error: function() {
                loadingIndicator.hide();
                container.html('<div class="alert alert-danger">Error de conexión al cargar las cotizaciones.</div>');
            }
        });
    }

    // Cargar cotizaciones al iniciar la página
    cargarCotizaciones();

    // Manejar cambio de estado con delegación de eventos
    container.on('change', '.status-select', function() {
        const select = $(this);
        const cotId = select.data('cot-id');
        const nuevoEstado = select.val();
        
        select.prop('disabled', true);

        $.ajax({
            url: '../AJAX/cotizaciones_ajax.php',
            type: 'POST',
            data: {
                accion: 'actualizarEstado',
                cot_id: cotId,
                nuevo_estado: nuevoEstado
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Refrescar la lista para mostrar el cambio visualmente
                    cargarCotizaciones();
                } else {
                    alert('Error: ' + response.message);
                    select.prop('disabled', false);
                }
            },
            error: function() {
                alert('Error de conexión al actualizar el estado.');
                select.prop('disabled', false);
            }
        });
    });
});