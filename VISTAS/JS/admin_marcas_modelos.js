$(document).ready(function() {
    const $marcasTableBody = $('#marcasTableBody');
    const $modelosTableContainer = $('#modelosTableContainer');
    const $modelosTableBody = $('#modelosTableBody');
    const $nombreMarcaSeleccionada = $('#nombreMarcaSeleccionada');
    const $marcaParaModelo = $('#marcaParaModelo'); // Para el título del modal de modelo

    const modalGestionMarca = new bootstrap.Modal(document.getElementById('modalGestionMarca'));
    const $formGestionMarca = $('#formGestionMarca');
    const $modalMarcaLabel = $('#modalMarcaLabel');
    const $editMarId = $('#editMarId');
    const $marNombreInput = $('#mar_nombre');
    const $marLogoUrlInput = $('#mar_logo_url');
    const $btnGuardarMarca = $('#btnGuardarMarca');
    const $marcaFormFeedback = $('#marcaFormFeedback');

    const modalGestionModelo = new bootstrap.Modal(document.getElementById('modalGestionModelo'));
    const $formGestionModelo = $('#formGestionModelo');
    const $modalModeloLabel = $('#modalModeloLabel'); // No usado directamente para título, pero sí el span
    const $editModId = $('#editModId');
    const $selectedMarIdForModeloInput = $('#selectedMarIdForModelo'); // Input hidden
    const $modNombreInput = $('#mod_nombre');
    const $btnGuardarModelo = $('#btnGuardarModelo');
    const $modeloFormFeedback = $('#modeloFormFeedback');

    let marcaActualmenteSeleccionadaId = null;
    let marcaActualmenteSeleccionadaNombre = '';

    // --- FUNCIONES DE MARCAS ---
    function cargarMarcas() {
        $marcasTableBody.html('<tr><td colspan="5" class="text-center"><div class="spinner-border spinner-border-sm" role="status"></div> Cargando marcas...</td></tr>');
        $.ajax({
            url: '../AJAX/admin_catalogos_ajax.php', type: 'GET', data: { accion: 'listarMarcas' }, dataType: 'json',
            success: function(response) {
                $marcasTableBody.empty();
                if (response.status === 'success' && response.marcas && response.marcas.length > 0) {
                    $.each(response.marcas, function(index, marca) {
                        const logoPreview = marca.mar_logo_url ? `<img src="${marca.mar_logo_url}" alt="Logo" class="logo-preview img-thumbnail">` : '<span class="text-muted small">Sin logo</span>';
                        $marcasTableBody.append(`
                            <tr data-mar_id="${marca.mar_id}" data-mar_nombre="${marca.mar_nombre}">
                                <td>${marca.mar_id}</td>
                                <td>${logoPreview}</td>
                                <td>${$('<div>').text(marca.mar_nombre).html()}</td>
                                <td class="small">${marca.mar_logo_url ? $('<div>').text(marca.mar_logo_url).html() : '-'}</td>
                                <td class="table-actions">
                                    <button class="btn btn-sm btn-info btn-ver-modelos" title="Ver/Gestionar Modelos"><i class="bi bi-car-front"></i> Modelos</button>
                                    <button class="btn btn-sm btn-warning btn-editar-marca" title="Editar Marca"><i class="bi bi-pencil-square"></i></button>
                                    <button class="btn btn-sm btn-danger btn-eliminar-marca" title="Eliminar Marca"><i class="bi bi-trash3-fill"></i></button>
                                </td>
                            </tr>
                        `);
                    });
                } else {
                    $marcasTableBody.html('<tr><td colspan="5" class="text-center">' + (response.message || 'No hay marcas para mostrar.') + '</td></tr>');
                }
            },
            error: function() { $marcasTableBody.html('<tr><td colspan="5" class="text-center text-danger">Error al cargar marcas.</td></tr>'); }
        });
    }

    $('#btnAbrirModalMarca').on('click', function() {
        $formGestionMarca[0].reset();
        $formGestionMarca.removeClass('was-validated');
        $editMarId.val('');
        $modalMarcaLabel.text('Añadir Nueva Marca');
        $marcaFormFeedback.html('');
    });

    $formGestionMarca.on('submit', function(e) {
        e.preventDefault();
        if (!this.checkValidity()) { $(this).addClass('was-validated'); return; }
        $(this).addClass('was-validated');

        const originalButtonText = $btnGuardarMarca.html();
        $btnGuardarMarca.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Guardando...');
        $marcaFormFeedback.html('');

        $.ajax({
            url: '../AJAX/admin_catalogos_ajax.php', type: 'POST', data: $(this).serialize(), dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    modalGestionMarca.hide();
                    cargarMarcas(); // Recargar la tabla de marcas
                    // Si se estaba mostrando modelos de esta marca (en caso de edición), recargar el nombre
                    if (marcaActualmenteSeleccionadaId && marcaActualmenteSeleccionadaId == response.mar_id) {
                        $nombreMarcaSeleccionada.text($marNombreInput.val());
                        $marcaParaModelo.text($marNombreInput.val());
                    }
                    showGlobalSuccess(response.message);
                } else {
                    $marcaFormFeedback.html('<div class="alert alert-danger small p-2">' + (response.message || 'Error desconocido.') + '</div>');
                }
            },
            error: function() { $marcaFormFeedback.html('<div class="alert alert-danger small p-2">Error de conexión.</div>'); },
            complete: function() { $btnGuardarMarca.prop('disabled', false).html(originalButtonText); }
        });
    });

    $marcasTableBody.on('click', '.btn-editar-marca', function() {
        const $tr = $(this).closest('tr');
        $editMarId.val($tr.data('mar_id'));
        $marNombreInput.val($tr.find('td:nth-child(3)').text()); // Nombre de la marca
        $marLogoUrlInput.val($tr.find('td:nth-child(4)').text() === '-' ? '' : $tr.find('td:nth-child(4)').text()); // URL del logo
        $modalMarcaLabel.text('Editar Marca');
        $formGestionMarca.removeClass('was-validated');
        $marcaFormFeedback.html('');
        modalGestionMarca.show();
    });

    $marcasTableBody.on('click', '.btn-eliminar-marca', function() {
        const marId = $(this).closest('tr').data('mar_id');
        const marNombre = $(this).closest('tr').data('mar_nombre');
        if (confirm(`¿Estás seguro de que quieres eliminar la marca "${marNombre}"? Esta acción no se puede deshacer y podría afectar a vehículos asociados si no se maneja la FK con ON DELETE CASCADE para modelos.`)) {
            $.ajax({
                url: '../AJAX/admin_catalogos_ajax.php', type: 'POST', data: { accion: 'eliminarMarca', mar_id: marId }, dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        cargarMarcas();
                        if (marcaActualmenteSeleccionadaId == marId) { // Si se eliminó la marca cuyos modelos se mostraban
                            $modelosTableContainer.slideUp();
                            marcaActualmenteSeleccionadaId = null;
                        }
                        showGlobalSuccess(response.message);
                    } else { showGlobalError(response.message || 'Error al eliminar.'); }
                },
                error: function() { showGlobalError('Error de conexión al eliminar.'); }
            });
        }
    });

    // --- FUNCIONES DE MODELOS ---
    $marcasTableBody.on('click', '.btn-ver-modelos', function() {
        const $tr = $(this).closest('tr');
        marcaActualmenteSeleccionadaId = $tr.data('mar_id');
        marcaActualmenteSeleccionadaNombre = $tr.data('mar_nombre');

        $marcasTableBody.find('tr').removeClass('table-active'); // Quitar resaltado de otras filas
        $tr.addClass('table-active'); // Resaltar la marca seleccionada

        $nombreMarcaSeleccionada.text(marcaActualmenteSeleccionadaNombre);
        $marcaParaModelo.text(marcaActualmenteSeleccionadaNombre); // Para el título del modal
        $selectedMarIdForModeloInput.val(marcaActualmenteSeleccionadaId); // Para el form de modelo

        $modelosTableBody.html('<tr><td colspan="3" class="text-center"><div class="spinner-border spinner-border-sm"></div> Cargando modelos...</td></tr>');
        $modelosTableContainer.slideDown();

        $.ajax({
            url: '../AJAX/admin_catalogos_ajax.php', type: 'GET', data: { accion: 'listarModelos', marca_id: marcaActualmenteSeleccionadaId }, dataType: 'json',
            success: function(response) {
                $modelosTableBody.empty();
                if (response.status === 'success' && response.modelos && response.modelos.length > 0) {
                    $.each(response.modelos, function(i, modelo) {
                        $modelosTableBody.append(`
                            <tr data-mod_id="${modelo.mod_id}" data-mod_nombre="${modelo.mod_nombre}">
                                <td>${modelo.mod_id}</td>
                                <td>${$('<div>').text(modelo.mod_nombre).html()}</td>
                                <td class="table-actions">
                                    <button class="btn btn-sm btn-outline-warning btn-editar-modelo" title="Editar Modelo"><i class="bi bi-pencil-fill"></i></button>
                                    <button class="btn btn-sm btn-outline-danger btn-eliminar-modelo" title="Eliminar Modelo"><i class="bi bi-trash-fill"></i></button>
                                </td>
                            </tr>
                        `);
                    });
                } else {
                     $modelosTableBody.html('<tr><td colspan="3" class="text-center">' + (response.message || 'No hay modelos para esta marca. Puedes añadir uno.') + '</td></tr>');
                }
            },
            error: function() { $modelosTableBody.html('<tr><td colspan="3" class="text-center text-danger">Error al cargar modelos.</td></tr>'); }
        });
    });

    $('#btnAbrirModalModelo').on('click', function() {
        if (!marcaActualmenteSeleccionadaId) {
            alert("Por favor, selecciona primero una marca de la lista para añadirle un modelo.");
            return;
        }
        $formGestionModelo[0].reset();
        $formGestionModelo.removeClass('was-validated');
        $editModId.val('');
        // $selectedMarIdForModeloInput.val(marcaActualmenteSeleccionadaId); // Ya se setea al ver modelos
        // $marcaParaModelo.text(marcaActualmenteSeleccionadaNombre); // Ya se setea al ver modelos
        $modalModeloLabel.text('Añadir Nuevo Modelo a ' + marcaActualmenteSeleccionadaNombre); // Actualizar título completo
        $modeloFormFeedback.html('');
    });

    $formGestionModelo.on('submit', function(e) {
        e.preventDefault();
        if (!this.checkValidity()) { $(this).addClass('was-validated'); return; }
        $(this).addClass('was-validated');

        const originalButtonText = $btnGuardarModelo.html();
        $btnGuardarModelo.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Guardando...');
        $modeloFormFeedback.html('');

        $.ajax({
            url: '../AJAX/admin_catalogos_ajax.php', type: 'POST', data: $(this).serialize(), dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    modalGestionModelo.hide();
                    // Recargar modelos de la marca actual
                    if (marcaActualmenteSeleccionadaId == response.mar_id_fk) { // Asegurar que se recarga para la marca correcta
                        $marcasTableBody.find(`tr[data-mar_id="${marcaActualmenteSeleccionadaId}"] .btn-ver-modelos`).click(); // Simular clic
                    }
                    showGlobalSuccess(response.message);
                } else {
                    $modeloFormFeedback.html('<div class="alert alert-danger small p-2">' + (response.message || 'Error desconocido.') + '</div>');
                }
            },
            error: function() { $modeloFormFeedback.html('<div class="alert alert-danger small p-2">Error de conexión.</div>'); },
            complete: function() { $btnGuardarModelo.prop('disabled', false).html(originalButtonText); }
        });
    });
    
    $modelosTableBody.on('click', '.btn-editar-modelo', function() {
        const $tr = $(this).closest('tr');
        $editModId.val($tr.data('mod_id'));
        $modNombreInput.val($tr.data('mod_nombre'));
        // $selectedMarIdForModeloInput.val(marcaActualmenteSeleccionadaId); // Ya debería estar seteado
        $modalModeloLabel.text('Editar Modelo de ' + marcaActualmenteSeleccionadaNombre);
        $formGestionModelo.removeClass('was-validated');
        $modeloFormFeedback.html('');
        modalGestionModelo.show();
    });

    $modelosTableBody.on('click', '.btn-eliminar-modelo', function() {
        const modId = $(this).closest('tr').data('mod_id');
        const modNombre = $(this).closest('tr').data('mod_nombre');
        if (confirm(`¿Estás seguro de que quieres eliminar el modelo "${modNombre}" de la marca "${marcaActualmenteSeleccionadaNombre}"?`)) {
            $.ajax({
                url: '../AJAX/admin_catalogos_ajax.php', type: 'POST', data: { accion: 'eliminarModelo', mod_id: modId }, dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $marcasTableBody.find(`tr[data-mar_id="${marcaActualmenteSeleccionadaId}"] .btn-ver-modelos`).click(); // Recargar
                        showGlobalSuccess(response.message);
                    } else { showGlobalError(response.message || 'Error al eliminar modelo.'); }
                },
                error: function() { showGlobalError('Error de conexión al eliminar modelo.'); }
            });
        }
    });


    // Funciones globales para feedback (puedes moverlas a global.js si las usas en más sitios)
    function showGlobalSuccess(message) {
        // Implementa tu forma de mostrar un toast o alerta global de éxito
        // Por ejemplo, usando un div fijo en la página o una librería de toasts
        alert("Éxito: " + message); // Placeholder
    }
    function showGlobalError(message) {
        alert("Error: " + message); // Placeholder
    }

    // Carga inicial de marcas
    cargarMarcas();
});