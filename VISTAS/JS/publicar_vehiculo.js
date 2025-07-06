$(document).ready(function() {
    const provinciasCiudades = {
        "Azuay": ["Cuenca", "Gualaceo", "Paute", "Sígsig", "Chordeleg", "Santa Isabel", "Girón", "Nabón", "Camilo Ponce Enríquez"],
        "Bolívar": ["Guaranda", "San Miguel", "Chimbo", "Caluma", "Echeandía", "Las Naves"],
        "Cañar": ["Azogues", "La Troncal", "Biblián", "Cañar", "El Tambo", "Suscal"],
        "Carchi": ["Tulcán", "San Gabriel", "El Ángel", "Mira", "Bolívar (Carchi)", "Montúfar"],
        "Chimborazo": ["Riobamba", "Guano", "Alausí", "Chambo", "Colta", "Cumandá", "Pallatanga"],
        "Cotopaxi": ["Latacunga", "La Maná", "Pujilí", "Salcedo", "Saquisilí", "Sigchos"],
        "El Oro": ["Machala", "Pasaje", "Santa Rosa", "Huaquillas", "Arenillas", "Piñas", "El Guabo"],
        "Esmeraldas": ["Esmeraldas", "Atacames", "Quinindé (Rosa Zárate)", "San Lorenzo", "Muisne"],
        "Galápagos": ["Puerto Baquerizo Moreno", "Puerto Ayora", "Puerto Villamil"],
        "Guayas": ["Guayaquil", "Durán", "Daule", "Samborondón", "Milagro", "General Villamil (Playas)", "El Triunfo", "Naranjal", "Balzar", "Yaguachi", "Velasco Ibarra", "Pedro Carbo", "Naranjito", "Lomas de Sargentillo"],
        "Imbabura": ["Ibarra", "Otavalo", "Atuntaqui", "Cotacachi", "Pimampiro", "Urcuquí"],
        "Loja": ["Loja", "Catamayo", "Macará", "Cariamanga", "Saraguro", "Gonzanamá"],
        "Los Ríos": ["Babahoyo", "Quevedo", "Buena Fe", "Ventanas", "Vinces", "Valencia", "Montalvo"],
        "Manabí": ["Portoviejo", "Manta", "Chone", "Jipijapa", "Montecristi", "El Carmen", "Bahía de Caráquez", "Calceta", "Pedernales", "Jaramijó"],
        "Morona Santiago": ["Macas", "Sucúa", "Gualaquiza", "Limón Indanza", "Palora"],
        "Napo": ["Tena", "Archidona", "El Chaco", "Baeza"],
        "Orellana": ["Francisco de Orellana (El Coca)", "La Joya de los Sachas", "Loreto"],
        "Pastaza": ["Puyo", "Mera", "Santa Clara", "Arajuno"],
        "Pichincha": ["Quito", "Sangolquí (Rumiñahui)", "Cayambe", "Machachi", "Tabacundo"],
        "Santa Elena": ["Santa Elena", "La Libertad", "Salinas"],
        "Santo Domingo de los Tsáchilas": ["Santo Domingo", "La Concordia"],
        "Sucumbíos": ["Nueva Loja (Lago Agrio)", "Shushufindi", "Cascales", "Cuyabeno"],
        "Tungurahua": ["Ambato", "Baños de Agua Santa", "Pelileo", "Patate", "Quero"],
        "Zamora Chinchipe": ["Zamora", "Yantzaza", "El Pangui", "Centinela del Cóndor"]
    };

    function poblarSelect($selectElement, data, valueField, textField, defaultOptionText) {
        $selectElement.empty().append($('<option>', { value: '', text: defaultOptionText, disabled: true, selected: true }));
        $.each(data, (i, item) => $selectElement.append($('<option>', { value: item[valueField], text: item[textField] })));
    }

    function poblarSelectSimple($selectElement, dataArray, defaultOptionText) {
        $selectElement.empty().append($('<option>', { value: '', text: defaultOptionText, disabled: true, selected: true }));
        $.each(dataArray, (i, item) => $selectElement.append($('<option>', { value: item, text: item })));
    }

    function cargarCatalogosIniciales() {
        $.ajax({
            url: '../AJAX/vehiculos_ajax.php', data: { accion: 'getCatalogos' }, dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    poblarSelect($('#mar_id'), response.marcas, 'mar_id', 'mar_nombre', 'Selecciona marca...');
                    poblarSelect($('#tiv_id'), response.tipos_vehiculo, 'tiv_id', 'tiv_nombre', 'Selecciona tipo...');
                    poblarSelectSimple($('#veh_ubicacion_provincia'), response.provincias, 'Selecciona provincia...');
                    poblarSelectSimple($('#veh_placa_provincia_origen'), response.provincias, 'Selecciona provincia...');
                }
            }
        });
    }
    cargarCatalogosIniciales();

    $('#mar_id').on('change', function() {
        const marcaId = $(this).val();
        const $selectModelos = $('#mod_id');
        $selectModelos.empty().append('<option value="" selected disabled>Cargando...</option>').prop('disabled', true);
        if (marcaId) {
            $.ajax({
                url: '../AJAX/vehiculos_ajax.php', data: { accion: 'getModelos', marca_id: marcaId }, dataType: 'json',
                success: function(response) {
                    if (response.status === 'success' && response.modelos.length > 0) {
                        poblarSelect($selectModelos, response.modelos, 'mod_id', 'mod_nombre', 'Selecciona modelo...');
                        $selectModelos.prop('disabled', false);
                    } else {
                        $selectModelos.empty().append('<option value="" selected disabled>No hay modelos</option>');
                    }
                }
            });
        }
    });

    $('#veh_ubicacion_provincia').on('change', function() {
        var provincia = $(this).val(); var $selectCiudades = $('#veh_ubicacion_ciudad');
        $selectCiudades.empty().append('<option value="" selected disabled>Cargando ciudades...</option>').prop('disabled', true);
        if (provincia && provinciasCiudades[provincia]) {
            poblarSelectSimple($selectCiudades, provinciasCiudades[provincia], 'Selecciona una ciudad...');
            $selectCiudades.prop('disabled', false);
        } else {
            $selectCiudades.empty().append('<option value="" selected disabled>Selecciona provincia...</option>').prop('disabled', true);
        }
    });

    var $kilometrajeContainer = $('#kilometraje_div_container');
    var $kilometrajeInput = $('#veh_kilometraje');
    var $kilometrajeLabel = $('#label_kilometraje');
    var $camposPlacaGroup = $('#campos_placa_group');
    var $placaProvinciaInput = $('#veh_placa_provincia_origen');
    var $ultimoDigitoInput = $('#veh_ultimo_digito_placa');
    var $placaInput = $('#veh_placa');

    function actualizarCamposUsado(esUsado) {
        if (esUsado) {
            $kilometrajeLabel.html('Recorrido (km) <span class="text-danger">*</span>');
            $kilometrajeInput.prop('required', true);
            $placaInput.prop('required', true);
            $placaProvinciaInput.prop('required', true);
            $ultimoDigitoInput.prop('required', true);
            $kilometrajeContainer.slideDown();
            $camposPlacaGroup.slideDown();
        } else {
            $kilometrajeLabel.html('Recorrido (km)');
            $kilometrajeInput.prop('required', false).val('').removeClass('is-invalid is-valid');
            $placaInput.prop('required', false).val('').removeClass('is-invalid is-valid');
            $placaProvinciaInput.prop('required', false).val('').removeClass('is-invalid is-valid');
            $ultimoDigitoInput.prop('required', false).val('').removeClass('is-invalid is-valid');
            $kilometrajeContainer.slideUp();
            $camposPlacaGroup.slideUp();
        }
    }
    actualizarCamposUsado($('#veh_condicion').val() === 'usado'); 

    // --- INICIO: Lógica para Año de Fabricación Dinámico ---
    var $selectAnio = $('#veh_anio');
    var opcionesAnioOriginales = $selectAnio.html(); // Guardar opciones originales

    function actualizarOpcionesAnio(esNuevo) {
        var currentYear = new Date().getFullYear();
        $selectAnio.empty(); // Limpiar opciones actuales

        if (esNuevo) {
            // Para vehículos nuevos, mostrar solo año actual +1 y actual +2.
            // Seleccionar el más alto (actual + 2) por defecto.
            var anioOpcion1 = currentYear + 1;
            var anioOpcion2 = currentYear;

            $selectAnio.append($('<option>', { value: '', text: 'Selecciona año...', disabled: true }));
            $selectAnio.append($('<option>', { value: anioOpcion2, text: anioOpcion2 }));
            $selectAnio.append($('<option>', { value: anioOpcion1, text: anioOpcion1 }));
            
            $selectAnio.val(anioOpcion2); // Seleccionar por defecto el año más alto (actual + 2)

        } else {
            // Para vehículos usados o sin condición, restaurar opciones originales
            $selectAnio.html(opcionesAnioOriginales);
            $selectAnio.val(''); // Resetear selección a placeholder
        }
        // Disparar un evento de cambio si es necesario para validaciones u otras lógicas
        // $selectAnio.trigger('change'); 
    }

    // Llamada inicial por si el formulario se carga con una condición ya seleccionada (ej. al editar)
    // Esto requiere que el valor de veh_condicion esté disponible al cargar.
    // Si es un formulario nuevo, 'veh_condicion' estará vacío, así que no hará nada especial.
    if ($('#veh_condicion').val()) {
        actualizarOpcionesAnio($('#veh_condicion').val() === 'nuevo');
    }


    $('#veh_condicion').on('change', function() {
        var esUsado = $(this).val() === 'usado';
        var esNuevo = $(this).val() === 'nuevo';
        actualizarCamposUsado(esUsado);
        actualizarOpcionesAnio(esNuevo);
    });
    // --- FIN: Lógica para Año de Fabricación Dinámico ---

    // --- NUEVA LÓGICA DE PREVISUALIZACIÓN DE IMÁGENES ---
    const MAX_IMAGES = 10;
    const imagePreviewContainer = $('#imagePreviewContainerPublicar');
    const vehImagenesInput = $('#veh_imagenes');
    const imagenPrincipalField = $('#imagen_principal_nombre_temporal_form_publicar');
    const defaultPreviewText = '<small class="text-muted align-self-center mx-auto default-text">La previsualización de imágenes aparecerá aquí...</small>';

    let uploadedFiles = [];
    let principalImageTempName = null;

    vehImagenesInput.on('change', function(event) {
        const nuevosArchivos = Array.from(event.target.files);
        nuevosArchivos.forEach(file => {
            if (uploadedFiles.length < MAX_IMAGES && !uploadedFiles.some(f => f.name === file.name && f.size === file.size)) {
                if (file.type.startsWith('image/')) {
                    uploadedFiles.push(file);
                }
            }
        });
        if (uploadedFiles.length > MAX_IMAGES) {
            alert(`Máximo ${MAX_IMAGES} imágenes permitidas.`);
            uploadedFiles = uploadedFiles.slice(0, MAX_IMAGES);
        }
        $(this).val('');
        if (!principalImageTempName && uploadedFiles.length > 0) {
            principalImageTempName = uploadedFiles[0].name;
        }
        renderizarPreviews();
        actualizarInputFileEnDOM();
    });

    function renderizarPreviews() {
        imagePreviewContainer.empty();
        if (uploadedFiles.length === 0) {
            imagePreviewContainer.html(defaultPreviewText);
            principalImageTempName = null;
            updateHiddenPrincipalField();
            actualizarEstadoInputFileRequired();
            return;
        }
        uploadedFiles.forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const isPrincipal = principalImageTempName === file.name;
                const wrapper = $(`
                    <div class="img-preview-wrapper ${isPrincipal ? 'is-principal' : ''}" data-filename="${file.name}">
                        <div class="image-container">
                             <img src="${e.target.result}" alt="${file.name}">
                             <button type="button" class="btn btn-danger btn-sm btn-remove-new-img" title="Eliminar"><i class="bi bi-x-lg"></i></button>
                             ${isPrincipal ? '<span class="badge bg-primary badge-principal-nueva">Principal</span>' : ''}
                        </div>
                    </div>`);
                if (!isPrincipal) {
                    wrapper.append(`<button type="button" class="btn btn-outline-primary btn-sm btn-set-nueva-principal" title="Hacer Principal">Principal</button>`);
                }
                imagePreviewContainer.append(wrapper);
            }
            reader.readAsDataURL(file);
        });
        updateHiddenPrincipalField();
        actualizarEstadoInputFileRequired();
    }

    imagePreviewContainer.on('click', '.btn-remove-new-img', function(e) {
        e.stopPropagation();
        const fileNameToRemove = $(this).closest('.img-preview-wrapper').data('filename');
        uploadedFiles = uploadedFiles.filter(file => file.name !== fileNameToRemove);
        if (principalImageTempName === fileNameToRemove) {
            principalImageTempName = (uploadedFiles.length > 0) ? uploadedFiles[0].name : null;
        }
        renderizarPreviews();
        actualizarInputFileEnDOM();
    });

    imagePreviewContainer.on('click', '.btn-set-nueva-principal', function() {
        principalImageTempName = $(this).closest('.img-preview-wrapper').data('filename');
        renderizarPreviews();
    });

    function updateHiddenPrincipalField() {
        imagenPrincipalField.val(principalImageTempName || '');
    }

    function actualizarInputFileEnDOM() {
        const dataTransfer = new DataTransfer();
        uploadedFiles.forEach(file => dataTransfer.items.add(file));
        vehImagenesInput[0].files = dataTransfer.files;
    }

    function actualizarEstadoInputFileRequired() {
        vehImagenesInput.prop('required', uploadedFiles.length === 0);
        if (uploadedFiles.length > 0 && vehImagenesInput.hasClass('is-invalid')) {
            vehImagenesInput.removeClass('is-invalid')[0].setCustomValidity("");
        }
    }
    // --- FIN NUEVA LÓGICA DE PREVISUALIZACIÓN ---

    // Modificar el submit handler para usar archivosParaSubir
    $('#publicarVehiculoForm').on('submit', function(event) {
        event.preventDefault();
        var form = this;
        var $formMessage = $('#formSubmissionMessage').html('').hide();

        actualizarInputFileEnDOM(); // Sincronizar antes de validar

        if (uploadedFiles.length === 0) {
            $(form).addClass('was-validated');
            $formMessage.html('<div class="alert alert-warning">Debes subir al menos una imagen.</div>').show();
            vehImagenesInput.addClass('is-invalid')[0].setCustomValidity("Debes subir al menos una imagen.");
            vehImagenesInput[0].reportValidity();
            return;
        }
        if (!principalImageTempName) {
            $formMessage.html('<div class="alert alert-warning">Debes designar una imagen como principal.</div>').show();
            return;
        }
        if (!form.checkValidity()) {
            event.stopPropagation();
            $(form).addClass('was-validated');
            $formMessage.html('<div class="alert alert-warning">Corrige los errores resaltados.</div>').show();
            return;
        }
        $(form).addClass('was-validated');
        var formData = new FormData(form);
        var $submitButton = $(this).find('button[type="submit"]');
        var originalButtonText = $submitButton.html();
        $submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Publicando...');
        
        $.ajax({
            url: '../AJAX/vehiculos_ajax.php', type: 'POST', data: formData, dataType: 'json', contentType: false, processData: false,
            success: function(response) {
                if (response.status === 'success') {
                    $formMessage.html(`<div class="alert alert-success">${response.message}</div>`).show();
                    form.reset();
                } else {
                    $formMessage.html(`<div class="alert alert-danger">Error: ${response.message}</div>`).show();
                }
            },
            error: function() {
                $formMessage.html('<div class="alert alert-danger">Error de conexión.</div>').show();
            },
            complete: function() {
                $submitButton.prop('disabled', false).html(originalButtonText);
            }
        });
    });

     $('#publicarVehiculoForm').on('reset', function() {
        $(this).removeClass('was-validated');
        uploadedFiles = [];
        principalImageTempName = null;
        renderizarPreviews();
        actualizarInputFileEnDOM();
        $('#formSubmissionMessage').html('').hide();
        $('#mod_id').empty().append('<option value="" selected disabled>Selecciona marca...</option>').prop('disabled', true);
        $('#veh_ubicacion_ciudad').empty().append('<option value="" selected disabled>Selecciona provincia...</option>').prop('disabled', true);
    });

    renderizarPreviews();
    actualizarEstadoInputFile(); 
    
    if (!$('#veh_condicion').val()) {
        $selectAnio.html(opcionesAnioOriginalesHTML);
        $selectAnio.val('');
    }
});