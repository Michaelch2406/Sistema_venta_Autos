$(document).ready(function() {
    const $marcasTableBody = $('#marcasTableBody');
    const $modelosTableContainer = $('#modelosTableContainer');
    const $modelosTableBody = $('#modelosTableBody');
    const $nombreMarcaSeleccionada = $('#nombreMarcaSeleccionada');
    const $marcaParaModelo = $('#marcaParaModelo');
    const $searchInput = $('#searchInput');
    const $searchModelosInput = $('#searchModelosInput');
    const $refreshBtn = $('#refreshBtn');

    const modalGestionMarca = new bootstrap.Modal(document.getElementById('modalGestionMarca'));
    const $formGestionMarca = $('#formGestionMarca');
    const $modalMarcaLabel = $('#modalMarcaLabel');
    const $editMarId = $('#editMarId');
    const $marNombreInput = $('#mar_nombre');
    const $marLogoUrlInput = $('#mar_logo_url');
    const $btnGuardarMarca = $('#btnGuardarMarca');
    const $marcaFormFeedback = $('#marcaFormFeedback');
    const $logoPreviewContainer = $('#logoPreviewContainer');
    const $logoPreview = $('#logoPreview');

    const modalGestionModelo = new bootstrap.Modal(document.getElementById('modalGestionModelo'));
    const $formGestionModelo = $('#formGestionModelo');
    const $modalModeloLabel = $('#modalModeloLabel');
    const $editModId = $('#editModId');
    const $selectedMarIdForModeloInput = $('#selectedMarIdForModelo');
    const $modNombreInput = $('#mod_nombre');
    const $btnGuardarModelo = $('#btnGuardarModelo');
    const $modeloFormFeedback = $('#modeloFormFeedback');

    let marcaActualmenteSeleccionadaId = null;
    let marcaActualmenteSeleccionadaNombre = '';
    let marcasData = [];
    let modelosData = [];
    let filtroActual = 'all';

    // Función para mostrar toast de notificación
    function showToast(message, type = 'info') {
        const $toast = $('#notificationToast');
        if ($toast.length === 0) {
            // Si no existe el toast, usar alert como fallback
            alert((type === 'error' ? 'Error: ' : type === 'success' ? 'Éxito: ' : '') + message);
            return;
        }
        
        const $toastBody = $('#toastBody');
        const $toastIcon = $toast.find('.toast-header i');
        
        $toastIcon.removeClass().addClass('me-2');
        switch(type) {
            case 'success':
                $toastIcon.addClass('bi bi-check-circle-fill text-success');
                break;
            case 'error':
                $toastIcon.addClass('bi bi-exclamation-triangle-fill text-danger');
                break;
            case 'warning':
                $toastIcon.addClass('bi bi-exclamation-circle-fill text-warning');
                break;
            default:
                $toastIcon.addClass('bi bi-info-circle-fill text-primary');
        }
        
        $toastBody.text(message);
        const toast = new bootstrap.Toast($toast[0]);
        toast.show();
    }

    // Función para actualizar estadísticas
    function actualizarEstadisticas() {
        const totalMarcas = marcasData.length;
        const marcasConLogo = marcasData.filter(marca => marca.mar_logo_url && marca.mar_logo_url.trim() !== '' && marca.mar_logo_url !== '-').length;
        const totalModelos = modelosData.length;
        
        $('#totalMarcas').text(totalMarcas);
        $('#marcasConLogo').text(marcasConLogo);
        $('#totalModelos').text(totalModelos);
        $('#marcaSeleccionada').text(marcaActualmenteSeleccionadaNombre || 'Ninguna');
        
        // Animar los números
        $('#statsContainer .card-title').each(function() {
            $(this).addClass('fade-in');
        });
    }

    // Función para filtrar marcas
    function filtrarMarcas() {
        let marcasFiltradas = marcasData;
        const termino = $searchInput.val().toLowerCase();

        // Aplicar filtro por tipo
        if (filtroActual === 'withLogo') {
            marcasFiltradas = marcasFiltradas.filter(marca => 
                marca.mar_logo_url && marca.mar_logo_url.trim() !== '' && marca.mar_logo_url !== '-'
            );
        } else if (filtroActual === 'withoutLogo') {
            marcasFiltradas = marcasFiltradas.filter(marca => 
                !marca.mar_logo_url || marca.mar_logo_url.trim() === '' || marca.mar_logo_url === '-'
            );
        }

        // Aplicar búsqueda por texto
        if (termino.trim() !== '') {
            marcasFiltradas = marcasFiltradas.filter(marca => 
                marca.mar_nombre.toLowerCase().includes(termino)
            );
        }

        mostrarMarcas(marcasFiltradas);
    }

    // Función para mostrar marcas en la tabla
    function mostrarMarcas(marcas) {
        $marcasTableBody.empty();
        
        if (marcas.length === 0) {
            $marcasTableBody.html('<tr><td colspan="5" class="text-center text-muted">No se encontraron marcas.</td></tr>');
            return;
        }

        marcas.forEach((marca, index) => {
            const logoPreview = (marca.mar_logo_url && marca.mar_logo_url.trim() !== '' && marca.mar_logo_url !== '-') ? 
                `<img src="${marca.mar_logo_url}" alt="Logo" class="logo-preview img-thumbnail" onerror="this.style.display='none'">` : 
                '<span class="text-muted small"><i class="bi bi-image-alt"></i> Sin logo</span>';
            
            const row = `
                <tr data-mar_id="${marca.mar_id}" data-mar_nombre="${marca.mar_nombre}" style="animation-delay: ${index * 0.1}s" class="fade-in">
                    <td><span class="badge bg-primary">${marca.mar_id}</span></td>
                    <td>${logoPreview}</td>
                    <td><strong>${$('<div>').text(marca.mar_nombre).html()}</strong></td>
                    <td class="small text-muted">${(marca.mar_logo_url && marca.mar_logo_url.trim() !== '' && marca.mar_logo_url !== '-') ? $('<div>').text(marca.mar_logo_url).html() : '-'}</td>
                    <td class="table-actions">
                        <button class="btn btn-sm btn-info btn-ver-modelos" title="Ver/Gestionar Modelos">
                            <i class="bi bi-car-front"></i> Modelos
                        </button>
                        <button class="btn btn-sm btn-warning btn-editar-marca" title="Editar Marca">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-eliminar-marca" title="Eliminar Marca">
                            <i class="bi bi-trash3-fill"></i>
                        </button>
                    </td>
                </tr>
            `;
            $marcasTableBody.append(row);
        });
    }

    // Función para filtrar modelos
    function filtrarModelos() {
        const termino = $searchModelosInput.val().toLowerCase();
        let modelosFiltrados = modelosData;

        if (termino.trim() !== '') {
            modelosFiltrados = modelosData.filter(modelo => 
                modelo.mod_nombre.toLowerCase().includes(termino)
            );
        }

        mostrarModelos(modelosFiltrados);
    }

    // Función para mostrar modelos en la tabla
    function mostrarModelos(modelos) {
        $modelosTableBody.empty();
        
        if (modelos.length === 0) {
            $modelosTableBody.html('<tr><td colspan="3" class="text-center text-muted">No se encontraron modelos para esta marca.</td></tr>');
            return;
        }

        modelos.forEach((modelo, index) => {
            const row = `
                <tr data-mod_id="${modelo.mod_id}" data-mod_nombre="${modelo.mod_nombre}" style="animation-delay: ${index * 0.1}s" class="fade-in">
                    <td><span class="badge bg-success">${modelo.mod_id}</span></td>
                    <td><strong>${$('<div>').text(modelo.mod_nombre).html()}</strong></td>
                    <td class="table-actions">
                        <button class="btn btn-sm btn-outline-warning btn-editar-modelo" title="Editar Modelo">
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger btn-eliminar-modelo" title="Eliminar Modelo">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </td>
                </tr>
            `;
            $modelosTableBody.append(row);
        });
    }

    // --- FUNCIONES DE MARCAS ---
    function cargarMarcas() {
        $marcasTableBody.html('<tr><td colspan="5" class="text-center"><div class="spinner-border spinner-border-sm" role="status"></div> Cargando marcas...</td></tr>');
        
        // Animación del botón de refresh
        if ($refreshBtn.length > 0) {
            $refreshBtn.find('i').addClass('fa-spin');
        }
        
        $.ajax({
            url: '../AJAX/admin_catalogos_ajax.php', 
            type: 'GET', 
            data: { accion: 'listarMarcas' }, 
            dataType: 'json',
            success: function(response) {
                if ($refreshBtn.length > 0) {
                    $refreshBtn.find('i').removeClass('fa-spin');
                }
                
                console.log('Respuesta del servidor:', response); // Debug
                
                if (response.status === 'success' && response.marcas && response.marcas.length > 0) {
                    marcasData = response.marcas;
                    filtrarMarcas();
                    actualizarEstadisticas();
                    showToast('Marcas cargadas correctamente', 'success');
                } else {
                    marcasData = [];
                    $marcasTableBody.html('<tr><td colspan="5" class="text-center">' + (response.message || 'No hay marcas para mostrar.') + '</td></tr>');
                    actualizarEstadisticas();
                    if (response.message) {
                        showToast(response.message, 'warning');
                    }
                }
            },
            error: function(jqXHR, textStatus, errorThrown) { 
                if ($refreshBtn.length > 0) {
                    $refreshBtn.find('i').removeClass('fa-spin');
                }
                console.error('Error AJAX:', jqXHR.responseText, textStatus, errorThrown); // Debug
                $marcasTableBody.html('<tr><td colspan="5" class="text-center text-danger">Error al cargar marcas.</td></tr>');
                showToast('Error al cargar marcas: ' + textStatus, 'error');
            }
        });
    }

    // Event listeners para filtros de marcas
    $('#filterAll').on('click', function() {
        $('.btn[id^="filter"]').removeClass('active');
        $(this).addClass('active');
        filtroActual = 'all';
        filtrarMarcas();
    });

    $('#filterWithLogo').on('click', function() {
        $('.btn[id^="filter"]').removeClass('active');
        $(this).addClass('active');
        filtroActual = 'withLogo';
        filtrarMarcas();
    });

    $('#filterWithoutLogo').on('click', function() {
        $('.btn[id^="filter"]').removeClass('active');
        $(this).addClass('active');
        filtroActual = 'withoutLogo';
        filtrarMarcas();
    });

    // Event listener para búsqueda de marcas
    $searchInput.on('input', function() {
        filtrarMarcas();
    });

    // Event listener para búsqueda de modelos
    $searchModelosInput.on('input', function() {
        filtrarModelos();
    });

    // Event listener para refresh
    $refreshBtn.on('click', function() {
        cargarMarcas();
        if (marcaActualmenteSeleccionadaId) {
            cargarModelos(marcaActualmenteSeleccionadaId);
        }
    });

    // Vista previa del logo
    $marLogoUrlInput.on('input', function() {
        const url = $(this).val().trim();
        if (url && isValidUrl(url)) {
            $logoPreview.attr('src', url);
            $logoPreviewContainer.show();
        } else {
            $logoPreviewContainer.hide();
        }
    });

    function isValidUrl(string) {
        try {
            new URL(string);
            return true;
        } catch (_) {
            return false;
        }
    }

    $('#btnAbrirModalMarca').on('click', function() {
        $formGestionMarca[0].reset();
        $formGestionMarca.removeClass('was-validated');
        $editMarId.val('');
        $modalMarcaLabel.text('Añadir Nueva Marca');
        $('#marLogoUrlContainer').show(); // Mostrar siempre el campo de logo
        $logoPreviewContainer.hide();
        $marLogoUrlInput.val('');
        $marcaFormFeedback.html('');
    });

    $formGestionMarca.on('submit', function(e) {
        e.preventDefault();
        if (!this.checkValidity()) { 
            $(this).addClass('was-validated'); 
            return; 
        }
        $(this).addClass('was-validated');

        const originalButtonText = $btnGuardarMarca.html();
        $btnGuardarMarca.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Guardando...');
        $marcaFormFeedback.html('');

        $.ajax({
            url: '../AJAX/admin_catalogos_ajax.php', 
            type: 'POST', 
            data: $(this).serialize(), 
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    modalGestionMarca.hide();
                    cargarMarcas();
                    if (marcaActualmenteSeleccionadaId && marcaActualmenteSeleccionadaId == response.mar_id) {
                        $nombreMarcaSeleccionada.text($marNombreInput.val());
                        $marcaParaModelo.text($marNombreInput.val());
                        marcaActualmenteSeleccionadaNombre = $marNombreInput.val();
                    }
                    showGlobalSuccess(response.message);
                } else {
                    $marcaFormFeedback.html('<div class="alert alert-danger small p-2">' + (response.message || 'Error desconocido.') + '</div>');
                }
            },
            error: function() { 
                $marcaFormFeedback.html('<div class="alert alert-danger small p-2">Error de conexión.</div>'); 
                showGlobalError('Error de conexión');
            },
            complete: function() { 
                $btnGuardarMarca.prop('disabled', false).html(originalButtonText); 
            }
        });
    });

    $marcasTableBody.on('click', '.btn-editar-marca', function() {
        const $tr = $(this).closest('tr');
        $editMarId.val($tr.data('mar_id'));
        $marNombreInput.val($tr.find('td:nth-child(3)').text().trim());
        const logoUrl = $tr.find('td:nth-child(4)').text() === '-' ? '' : $tr.find('td:nth-child(4)').text();
        $marLogoUrlInput.val(logoUrl);
        
        if (logoUrl) {
            $logoPreview.attr('src', logoUrl);
            $logoPreviewContainer.show();
        } else {
            $logoPreviewContainer.hide();
        }
        
        $('#marLogoUrlContainer').show();
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
                url: '../AJAX/admin_catalogos_ajax.php', 
                type: 'POST', 
                data: { accion: 'eliminarMarca', mar_id: marId }, 
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        cargarMarcas();
                        if (marcaActualmenteSeleccionadaId == marId) {
                            $modelosTableContainer.slideUp();
                            marcaActualmenteSeleccionadaId = null;
                            marcaActualmenteSeleccionadaNombre = '';
                            modelosData = [];
                            actualizarEstadisticas();
                        }
                        showGlobalSuccess(response.message);
                    } else { 
                        showGlobalError(response.message || 'Error al eliminar.'); 
                    }
                },
                error: function() { 
                    showGlobalError('Error de conexión al eliminar.'); 
                }
            });
        }
    });

    // --- FUNCIONES DE MODELOS ---
    function cargarModelos(marcaId) {
        $modelosTableBody.html('<tr><td colspan="3" class="text-center"><div class="spinner-border spinner-border-sm"></div> Cargando modelos...</td></tr>');

        $.ajax({
            url: '../AJAX/admin_catalogos_ajax.php', 
            type: 'GET', 
            data: { accion: 'listarModelos', marca_id: marcaId }, 
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' && response.modelos && response.modelos.length > 0) {
                    modelosData = response.modelos;
                    filtrarModelos();
                    actualizarEstadisticas();
                } else {
                    modelosData = [];
                    $modelosTableBody.html('<tr><td colspan="3" class="text-center">' + (response.message || 'No hay modelos para esta marca. Puedes añadir uno.') + '</td></tr>');
                    actualizarEstadisticas();
                }
            },
            error: function() { 
                $modelosTableBody.html('<tr><td colspan="3" class="text-center text-danger">Error al cargar modelos.</td></tr>'); 
                showGlobalError('Error al cargar modelos');
            }
        });
    }

    $marcasTableBody.on('click', '.btn-ver-modelos', function() {
        const $tr = $(this).closest('tr');
        marcaActualmenteSeleccionadaId = $tr.data('mar_id');
        marcaActualmenteSeleccionadaNombre = $tr.data('mar_nombre');

        $marcasTableBody.find('tr').removeClass('table-active');
        $tr.addClass('table-active');

        $nombreMarcaSeleccionada.text(marcaActualmenteSeleccionadaNombre);
        $marcaParaModelo.text(marcaActualmenteSeleccionadaNombre);
        $selectedMarIdForModeloInput.val(marcaActualmenteSeleccionadaId);

        $modelosTableContainer.slideDown();
        cargarModelos(marcaActualmenteSeleccionadaId);
        actualizarEstadisticas();
    });

    $('#btnAbrirModalModelo').on('click', function() {
        if (!marcaActualmenteSeleccionadaId) {
            alert("Por favor, selecciona primero una marca de la lista para añadirle un modelo.");
            return;
        }
        $formGestionModelo[0].reset();
        $formGestionModelo.removeClass('was-validated');
        $editModId.val('');
        $modalModeloLabel.text('Añadir Nuevo Modelo a ' + marcaActualmenteSeleccionadaNombre);
        $modeloFormFeedback.html('');
    });

    $formGestionModelo.on('submit', function(e) {
        e.preventDefault();
        if (!this.checkValidity()) { 
            $(this).addClass('was-validated'); 
            return; 
        }
        $(this).addClass('was-validated');

        const originalButtonText = $btnGuardarModelo.html();
        $btnGuardarModelo.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Guardando...');
        $modeloFormFeedback.html('');

        $.ajax({
            url: '../AJAX/admin_catalogos_ajax.php', 
            type: 'POST', 
            data: $(this).serialize(), 
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    modalGestionModelo.hide();
                    if (marcaActualmenteSeleccionadaId == response.mar_id_fk) {
                        cargarModelos(marcaActualmenteSeleccionadaId);
                    }
                    showGlobalSuccess(response.message);
                } else {
                    $modeloFormFeedback.html('<div class="alert alert-danger small p-2">' + (response.message || 'Error desconocido.') + '</div>');
                }
            },
            error: function() { 
                $modeloFormFeedback.html('<div class="alert alert-danger small p-2">Error de conexión.</div>'); 
                showGlobalError('Error de conexión');
            },
            complete: function() { 
                $btnGuardarModelo.prop('disabled', false).html(originalButtonText); 
            }
        });
    });
    
    $modelosTableBody.on('click', '.btn-editar-modelo', function() {
        const $tr = $(this).closest('tr');
        $editModId.val($tr.data('mod_id'));
        $modNombreInput.val($tr.data('mod_nombre'));
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
                url: '../AJAX/admin_catalogos_ajax.php', 
                type: 'POST', 
                data: { accion: 'eliminarModelo', mod_id: modId }, 
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        cargarModelos(marcaActualmenteSeleccionadaId);
                        showGlobalSuccess(response.message);
                    } else { 
                        showGlobalError(response.message || 'Error al eliminar modelo.'); 
                    }
                },
                error: function() { 
                    showGlobalError('Error de conexión al eliminar modelo.'); 
                }
            });
        }
    });

    // Funciones globales para feedback (compatibilidad con código original)
    function showGlobalSuccess(message) {
        showToast(message, 'success');
    }
    
    function showGlobalError(message) {
        showToast(message, 'error');
    }

    // Hacer las funciones globales disponibles
    window.showGlobalSuccess = showGlobalSuccess;
    window.showGlobalError = showGlobalError;

    // Efectos hover mejorados para las tablas
    $marcasTableBody.on('mouseenter', 'tr', function() {
        $(this).addClass('shadow-sm');
    }).on('mouseleave', 'tr', function() {
        $(this).removeClass('shadow-sm');
    });

    $modelosTableBody.on('mouseenter', 'tr', function() {
        $(this).addClass('shadow-sm');
    }).on('mouseleave', 'tr', function() {
        $(this).removeClass('shadow-sm');
    });

    // Carga inicial de marcas
    cargarMarcas();
});

