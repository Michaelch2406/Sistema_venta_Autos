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

    function cargarTiposVehiculo() {
        $tiposTableBody.html('<tr><td colspan="6" class="text-center"><div class="spinner-border spinner-border-sm" role="status"></div> Cargando...</td></tr>');
        $.ajax({
            url: '../AJAX/admin_catalogos_ajax.php',
            type: 'GET',
            data: { accion: 'listarTiposVehiculo' },
            dataType: 'json',
            success: function(response) {
                $tiposTableBody.empty();
                if (response.status === 'success' && response.tipos_vehiculo && response.tipos_vehiculo.length > 0) {
                    $.each(response.tipos_vehiculo, function(index, tipo) {
                        const iconoPreview = tipo.tiv_icono_url ? `<img src="${tipo.tiv_icono_url}" alt="Icono" class="icono-preview">` : '';
                        const descripcionCorta = tipo.tiv_descripcion ? `<span class="descripcion-corta" title="${$('<div>').text(tipo.tiv_descripcion).html()}">${$('<div>').text(tipo.tiv_descripcion).html()}</span>` : '-';
                        const estadoBadge = tipo.tiv_activo == 1 ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>';
                        
                        $tiposTableBody.append(`
                            <tr data-tiv_id="${tipo.tiv_id}">
                                <td>${tipo.tiv_id}</td>
                                <td>${iconoPreview}</td>
                                <td>${$('<div>').text(tipo.tiv_nombre).html()}</td>
                                <td>${descripcionCorta}</td>
                                <td>${estadoBadge}</td>
                                <td class="table-actions">
                                    <button class="btn btn-sm btn-warning btn-editar-tipo" title="Editar Tipo"><i class="bi bi-pencil-square"></i></button>
                                    <button class="btn btn-sm btn-danger btn-eliminar-tipo" title="Eliminar Tipo"><i class="bi bi-trash3-fill"></i></button>
                                </td>
                            </tr>
                        `);
                    });
                    // Inicializar tooltips para descripciones largas si es necesario
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"], .descripcion-corta[title]'));
                    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });
                } else {
                    $tiposTableBody.html('<tr><td colspan="6" class="text-center">' + (response.message || 'No hay tipos de vehículo para mostrar.') + '</td></tr>');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $tiposTableBody.html('<tr><td colspan="6" class="text-center text-danger">Error al cargar tipos de vehículo. Revisa la consola.</td></tr>');
                console.error("Error AJAX listarTiposVehiculo:", jqXHR.responseText, textStatus, errorThrown);
            }
        });
    }

    $('#btnAbrirModalTipoVehiculo').on('click', function() {
        $formGestionTipo[0].reset();
        $formGestionTipo.removeClass('was-validated');
        $editTivId.val(''); // Limpiar ID para modo inserción
        $tivActivoCheckbox.prop('checked', true); // Por defecto activo
        $modalTipoLabel.text('Añadir Nuevo Tipo de Vehículo');
        $tipoFormFeedback.html('');
    });

    $formGestionTipo.on('submit', function(e) {
        e.preventDefault();
        if (!this.checkValidity()) {
            $(this).addClass('was-validated');
            return;
        }
        $(this).addClass('was-validated');

        const originalButtonText = $btnGuardarTipo.html();
        $btnGuardarTipo.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Guardando...');
        $tipoFormFeedback.html('');

        // Crear objeto FormData para enviar también el estado del checkbox
        var formData = new FormData(this);
        // El checkbox no se envía si no está marcado, así que nos aseguramos de que 'tiv_activo' esté presente
        formData.set('tiv_activo', $tivActivoCheckbox.is(':checked') ? '1' : '0');


        $.ajax({
            url: '../AJAX/admin_catalogos_ajax.php',
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
                } else {
                    $tipoFormFeedback.html('<div class="alert alert-danger small p-2">' + (response.message || 'Error desconocido.') + '</div>');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $tipoFormFeedback.html('<div class="alert alert-danger small p-2">Error de conexión o del servidor.</div>');
                 console.error("Error AJAX guardarTipoVehiculo:", jqXHR.responseText, textStatus, errorThrown);
            },
            complete: function() {
                $btnGuardarTipo.prop('disabled', false).html(originalButtonText);
            }
        });
    });

    $tiposTableBody.on('click', '.btn-editar-tipo', function() {
        const $tr = $(this).closest('tr');
        const tipoId = $tr.data('tiv_id');
        
        // Para obtener los datos completos, podrías hacer otra llamada AJAX
        // o si los datos en la tabla son suficientes (y no muy largos):
        const nombre = $tr.find('td:nth-child(3)').text();
        const descripcionFull = $tr.find('td:nth-child(4) .descripcion-corta').attr('title') || $tr.find('td:nth-child(4)').text(); // Obtener descripción completa del title
        const iconoUrl = $tr.find('td:nth-child(2) img').attr('src') || '';
        const esActivo = $tr.find('td:nth-child(5) .bg-success').length > 0;

        $editTivId.val(tipoId);
        $tivNombreInput.val(nombre);
        $tivDescripcionInput.val(descripcionFull === '-' ? '' : descripcionFull);
        $tivIconoUrlInput.val(iconoUrl);
        $tivActivoCheckbox.prop('checked', esActivo);

        $modalTipoLabel.text('Editar Tipo de Vehículo');
        $formGestionTipo.removeClass('was-validated');
        $tipoFormFeedback.html('');
        modalGestionTipo.show();
    });

    $tiposTableBody.on('click', '.btn-eliminar-tipo', function() {
        const $tr = $(this).closest('tr');
        const tivId = $tr.data('tiv_id');
        const tivNombre = $tr.find('td:nth-child(3)').text();

        if (confirm(`¿Estás seguro de que quieres eliminar el tipo de vehículo "${tivNombre}"? Esta acción no se puede deshacer y podría fallar si hay vehículos asociados a este tipo.`)) {
            $.ajax({
                url: '../AJAX/admin_catalogos_ajax.php',
                type: 'POST',
                data: { accion: 'eliminarTipoVehiculo', tiv_id: tivId },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        cargarTiposVehiculo();
                        showGlobalSuccess(response.message);
                    } else {
                        showGlobalError(response.message || 'Error al eliminar.');
                    }
                },
                error: function() {
                    showGlobalError('Error de conexión al intentar eliminar.');
                }
            });
        }
    });

    // Placeholder para feedback global (puedes mover a global.js)
    function showGlobalSuccess(message) {
        // Reemplaza con tu sistema de notificaciones (ej: Toastr, SweetAlert)
        $('#formSubmissionMessageGlobal').remove(); // Limpiar mensajes anteriores
        $('body').append(`<div id="formSubmissionMessageGlobal" class="alert alert-success position-fixed bottom-0 end-0 m-3 p-2" role="alert" style="z-index: 1056;">${message}</div>`);
        setTimeout(function() { $('#formSubmissionMessageGlobal').fadeOut(500, function() { $(this).remove(); }); }, 4000);
    }
    function showGlobalError(message) {
         $('#formSubmissionMessageGlobal').remove();
        $('body').append(`<div id="formSubmissionMessageGlobal" class="alert alert-danger position-fixed bottom-0 end-0 m-3 p-2" role="alert" style="z-index: 1056;">${message}</div>`);
        setTimeout(function() { $('#formSubmissionMessageGlobal').fadeOut(500, function() { $(this).remove(); }); }, 5000);
    }

    // Carga inicial
    cargarTiposVehiculo();
});