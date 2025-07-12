$(document).ready(function() {
    const $tiposTableBody = $('#tiposVehiculoTableBody');
    const modalGestionTipo = new bootstrap.Modal(document.getElementById('modalGestionTipoVehiculo'));
    const $formGestionTipo = $('#formGestionTipoVehiculo');
    const $modalTipoLabel = $('#modalTipoVehiculoLabel');
    const $editTivId = $('#editTivId');
    const $tivNombreInput = $('#tiv_nombre');
    const $tivDescripcionInput = $('#tiv_descripcion');
    const $tivIconoUrlInput = $('#tiv_icono_url');
    const $tivActivoCheckbox = $('#tiv_activo');
    const $btnGuardarTipo = $('#btnGuardarTipoVehiculo');
    const $tipoFormFeedback = $('#tipoVehiculoFormFeedback');

    // Contadores para estadísticas
    let totalTipos = 0;
    let tiposActivos = 0;
    let tiposInactivos = 0;

    function actualizarEstadisticas() {
        $('#totalTipos').text(totalTipos);
        $('#tiposActivos').text(tiposActivos);
        $('#tiposInactivos').text(tiposInactivos);
        
        // Animación de conteo
        $('#totalTipos, #tiposActivos, #tiposInactivos').each(function() {
            $(this).addClass('pulse');
            setTimeout(() => {
                $(this).removeClass('pulse');
            }, 600);
        });
    }

    function cargarTiposVehiculo() {
        $tiposTableBody.html(`
            <tr>
                <td colspan="6" class="text-center py-5">
                    <div class="d-flex flex-column align-items-center">
                        <div class="loading-spinner mb-3"></div>
                        <span class="text-muted">Cargando tipos de vehículo...</span>
                    </div>
                </td>
            </tr>
        `);
        
        $.ajax({
            url: './../AJAX/admin_catalogos_ajax.php',
            type: 'GET',
            data: { accion: 'listarTiposVehiculo' },
            dataType: 'json',
            success: function(response) {
                $tiposTableBody.empty();
                
                if (response.status === 'success' && response.tipos_vehiculo && response.tipos_vehiculo.length > 0) {
                    // Resetear contadores
                    totalTipos = response.tipos_vehiculo.length;
                    tiposActivos = 0;
                    tiposInactivos = 0;
                    
                    $.each(response.tipos_vehiculo, function(index, tipo) {
                        // Contar estados
                        if (tipo.tiv_activo == 1) {
                            tiposActivos++;
                        } else {
                            tiposInactivos++;
                        }
                        
                        const iconoPreview = tipo.tiv_icono_url ? 
                            `<img src="${tipo.tiv_icono_url}" alt="Icono ${tipo.tiv_nombre}" class="icono-preview" title="Icono de ${tipo.tiv_nombre}">` : 
                            `<div class="icono-preview d-flex align-items-center justify-content-center bg-light border rounded-circle" title="Sin icono">
                                <i class="bi bi-image text-muted"></i>
                            </div>`;
                        
                        const descripcionCorta = tipo.tiv_descripcion ? 
                            `<span class="descripcion-corta" title="${$('<div>').text(tipo.tiv_descripcion).html()}">${$('<div>').text(tipo.tiv_descripcion).html()}</span>` : 
                            '<span class="text-muted fst-italic">Sin descripción</span>';
                        
                        const estadoBadge = tipo.tiv_activo == 1 ? 
                            '<span class="badge badge-custom badge-activo"><i class="bi bi-check-circle me-1"></i>Activo</span>' : 
                            '<span class="badge badge-custom badge-inactivo"><i class="bi bi-pause-circle me-1"></i>Inactivo</span>';
                        
                        const fila = $(`
                            <tr data-tiv_id="${tipo.tiv_id}" class="fade-in" style="animation-delay: ${index * 0.1}s">
                                <td class="fw-bold text-primary">#${tipo.tiv_id}</td>
                                <td class="text-center">${iconoPreview}</td>
                                <td>
                                    <div class="fw-semibold">${$('<div>').text(tipo.tiv_nombre).html()}</div>
                                </td>
                                <td>${descripcionCorta}</td>
                                <td class="text-center">${estadoBadge}</td>
                                <td class="table-actions">
                                    <button class="btn btn-action btn-editar btn-editar-tipo" title="Editar ${tipo.tiv_nombre}" data-bs-toggle="tooltip">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-action btn-eliminar btn-eliminar-tipo" title="Eliminar ${tipo.tiv_nombre}" data-bs-toggle="tooltip">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </td>
                            </tr>
                        `);
                        
                        $tiposTableBody.append(fila);
                    });
                    
                    // Inicializar tooltips
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"], .descripcion-corta[title], .icono-preview[title]'));
                    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });
                    
                } else {
                    totalTipos = 0;
                    tiposActivos = 0;
                    tiposInactivos = 0;
                    
                    $tiposTableBody.html(`
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                                    <h5 class="text-muted">No hay tipos de vehículo</h5>
                                    <p class="text-muted mb-3">${response.message || 'No se encontraron tipos de vehículo para mostrar.'}</p>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalGestionTipoVehiculo">
                                        <i class="bi bi-plus-circle me-2"></i>Crear el primer tipo
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `);
                }
                
                // Actualizar estadísticas
                actualizarEstadisticas();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                totalTipos = 0;
                tiposActivos = 0;
                tiposInactivos = 0;
                actualizarEstadisticas();
                
                $tiposTableBody.html(`
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center">
                                <i class="bi bi-exclamation-triangle display-1 text-danger mb-3"></i>
                                <h5 class="text-danger">Error al cargar datos</h5>
                                <p class="text-muted mb-3">No se pudieron cargar los tipos de vehículo. Revisa la conexión.</p>
                                <button class="btn btn-outline-primary" onclick="location.reload()">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Reintentar
                                </button>
                            </div>
                        </td>
                    </tr>
                `);
                console.error("Error AJAX listarTiposVehiculo:", jqXHR.responseText, textStatus, errorThrown);
            }
        });
    }

    // Evento para abrir modal de nuevo tipo
    $('#btnAbrirModalTipoVehiculo').on('click', function() {
        $formGestionTipo[0].reset();
        $formGestionTipo.removeClass('was-validated');
        $editTivId.val(''); // Limpiar ID para modo inserción
        $tivActivoCheckbox.prop('checked', true); // Por defecto activo
        $modalTipoLabel.text('Añadir Nuevo Tipo de Vehículo');
        $tipoFormFeedback.html('');
        
        // Enfocar el primer campo
        setTimeout(() => {
            $tivNombreInput.focus();
        }, 500);
    });

    // Validación en tiempo real
    $tivNombreInput.on('input', function() {
        const valor = $(this).val().trim();
        if (valor.length > 0) {
            $(this).removeClass('is-invalid').addClass('is-valid');
        } else {
            $(this).removeClass('is-valid').addClass('is-invalid');
        }
    });

    $tivIconoUrlInput.on('input', function() {
        const valor = $(this).val().trim();
        if (valor === '' || isValidURL(valor)) {
            $(this).removeClass('is-invalid').addClass('is-valid');
        } else {
            $(this).removeClass('is-valid').addClass('is-invalid');
        }
    });

    function isValidURL(string) {
        try {
            new URL(string);
            return true;
        } catch (_) {
            return false;
        }
    }

    // Envío del formulario
    $formGestionTipo.on('submit', function(e) {
        e.preventDefault();
        if (!this.checkValidity()) {
            $(this).addClass('was-validated');
            return;
        }
        $(this).addClass('was-validated');

        const originalButtonText = $btnGuardarTipo.html();
        $btnGuardarTipo.prop('disabled', true).html('<span class="loading-spinner me-2"></span>Guardando...');
        $tipoFormFeedback.html('');

        // Crear objeto FormData para enviar también el estado del checkbox
        var formData = new FormData(this);
        // El checkbox no se envía si no está marcado, así que nos aseguramos de que 'tiv_activo' esté presente
        formData.set('tiv_activo', $tivActivoCheckbox.is(':checked') ? '1' : '0');

        $.ajax({
            url: './../AJAX/admin_catalogos_ajax.php',
            type: 'POST',
            data: formData,
            processData: false, // Necesario para FormData
            contentType: false, // Necesario para FormData
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    modalGestionTipo.hide();
                    cargarTiposVehiculo();
                    showGlobalSuccess(response.message || "Operación exitosa.");
                    
                    // Limpiar formulario
                    $formGestionTipo[0].reset();
                    $formGestionTipo.removeClass('was-validated');
                } else {
                    $tipoFormFeedback.html(`
                        <div class="alert alert-danger-custom">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            ${response.message || 'Error desconocido.'}
                        </div>
                    `);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $tipoFormFeedback.html(`
                    <div class="alert alert-danger-custom">
                        <i class="bi bi-wifi-off me-2"></i>
                        Error de conexión o del servidor. Inténtalo nuevamente.
                    </div>
                `);
                console.error("Error AJAX guardarTipoVehiculo:", jqXHR.responseText, textStatus, errorThrown);
            },
            complete: function() {
                $btnGuardarTipo.prop('disabled', false).html(originalButtonText);
            }
        });
    });

    // Editar tipo
    $tiposTableBody.on('click', '.btn-editar-tipo', function() {
        const $tr = $(this).closest('tr');
        const tipoId = $tr.data('tiv_id');
        
        // Agregar efecto visual
        $tr.addClass('table-row-highlight');
        setTimeout(() => {
            $tr.removeClass('table-row-highlight');
        }, 2000);
        
        // Para obtener los datos completos, podrías hacer otra llamada AJAX
        // o si los datos en la tabla son suficientes (y no muy largos):
        const nombre = $tr.find('td:nth-child(3) .fw-semibold').text();
        const descripcionFull = $tr.find('td:nth-child(4) .descripcion-corta').attr('title') || 
                               ($tr.find('td:nth-child(4)').text().includes('Sin descripción') ? '' : $tr.find('td:nth-child(4)').text());
        const iconoUrl = $tr.find('td:nth-child(2) img').attr('src') || '';
        const esActivo = $tr.find('td:nth-child(5) .badge-activo').length > 0;

        $editTivId.val(tipoId);
        $tivNombreInput.val(nombre);
        $tivDescripcionInput.val(descripcionFull);
        $tivIconoUrlInput.val(iconoUrl);
        $tivActivoCheckbox.prop('checked', esActivo);

        $modalTipoLabel.text('Editar Tipo de Vehículo');
        $formGestionTipo.removeClass('was-validated');
        $tipoFormFeedback.html('');
        modalGestionTipo.show();
        
        // Enfocar el primer campo
        setTimeout(() => {
            $tivNombreInput.focus().select();
        }, 500);
    });

    // Eliminar tipo con confirmación mejorada
    $tiposTableBody.on('click', '.btn-eliminar-tipo', function() {
        const $tr = $(this).closest('tr');
        const tivId = $tr.data('tiv_id');
        const tivNombre = $tr.find('td:nth-child(3) .fw-semibold').text();

        // Crear modal de confirmación personalizado
        const confirmModal = $(`
            <div class="modal fade" id="confirmDeleteModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content modal-content-custom">
                        <div class="modal-header modal-header-custom bg-danger">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="bi bi-exclamation-triangle-fill text-white fs-3"></i>
                                </div>
                                <div>
                                    <h5 class="modal-title text-white mb-0">Confirmar Eliminación</h5>
                                    <small class="text-white opacity-75">Esta acción no se puede deshacer</small>
                                </div>
                            </div>
                        </div>
                        <div class="modal-body p-4">
                            <div class="text-center mb-4">
                                <i class="bi bi-trash3 display-1 text-danger mb-3"></i>
                                <h5>¿Eliminar "${tivNombre}"?</h5>
                                <p class="text-muted">
                                    Esta acción eliminará permanentemente el tipo de vehículo y podría fallar 
                                    si hay vehículos asociados a este tipo.
                                </p>
                            </div>
                            <div class="alert alert-warning">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Importante:</strong> Verifica que no existan vehículos registrados con este tipo antes de eliminarlo.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-modal-secondary" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle me-2"></i>Cancelar
                            </button>
                            <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                                <i class="bi bi-trash3 me-2"></i>Eliminar Definitivamente
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `);

        $('body').append(confirmModal);
        const confirmModalInstance = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
        confirmModalInstance.show();

        // Manejar confirmación
        $('#confirmDeleteBtn').on('click', function() {
            const $btn = $(this);
            const originalText = $btn.html();
            $btn.prop('disabled', true).html('<span class="loading-spinner me-2"></span>Eliminando...');

            $.ajax({
                url: './../AJAX/admin_catalogos_ajax.php',
                type: 'POST',
                data: { accion: 'eliminarTipoVehiculo', tiv_id: tivId },
                dataType: 'json',
                success: function(response) {
                    confirmModalInstance.hide();
                    if (response.status === 'success') {
                        // Animar eliminación de fila
                        $tr.addClass('table-danger');
                        setTimeout(() => {
                            cargarTiposVehiculo();
                        }, 300);
                        showGlobalSuccess(response.message);
                    } else {
                        showGlobalError(response.message || 'Error al eliminar.');
                    }
                },
                error: function() {
                    confirmModalInstance.hide();
                    showGlobalError('Error de conexión al intentar eliminar.');
                },
                complete: function() {
                    $btn.prop('disabled', false).html(originalText);
                }
            });
        });

        // Limpiar modal al cerrarse
        $('#confirmDeleteModal').on('hidden.bs.modal', function() {
            $(this).remove();
        });
    });

    // Funciones de notificación mejoradas
    function showGlobalSuccess(message) {
        $('#formSubmissionMessageGlobal').remove();
        const notification = $(`
            <div id="formSubmissionMessageGlobal" class="alert alert-success position-fixed bottom-0 end-0 m-3 p-3 slide-in" role="alert" style="z-index: 1056; max-width: 400px;">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill me-3 fs-4"></i>
                    <div>
                        <div class="fw-bold">¡Éxito!</div>
                        <div>${message}</div>
                    </div>
                </div>
            </div>
        `);
        $('body').append(notification);
        setTimeout(function() { 
            notification.fadeOut(500, function() { 
                $(this).remove(); 
            }); 
        }, 4000);
    }

    function showGlobalError(message) {
        $('#formSubmissionMessageGlobal').remove();
        const notification = $(`
            <div id="formSubmissionMessageGlobal" class="alert alert-danger position-fixed bottom-0 end-0 m-3 p-3 slide-in" role="alert" style="z-index: 1056; max-width: 400px;">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
                    <div>
                        <div class="fw-bold">Error</div>
                        <div>${message}</div>
                    </div>
                </div>
            </div>
        `);
        $('body').append(notification);
        setTimeout(function() { 
            notification.fadeOut(500, function() { 
                $(this).remove(); 
            }); 
        }, 5000);
    }

    // Carga inicial
    cargarTiposVehiculo();

    // Actualizar cada 30 segundos (opcional)
    setInterval(function() {
        if (!modalGestionTipo._isShown) {
            cargarTiposVehiculo();
        }
    }, 30000);
});

