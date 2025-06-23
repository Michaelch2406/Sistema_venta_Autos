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

    function poblarSelect($selectElement, data, valueField, textField, defaultOptionText, defaultSelectedValue = '') {
        $selectElement.empty().append($('<option>', { value: '', text: defaultOptionText, disabled: true, selected: (defaultSelectedValue === '') }));
        $.each(data, function(i, item) {
            var $option = $('<option>', { value: item[valueField], text: item[textField] });
            if (item[valueField] == defaultSelectedValue) { // Usar == para comparación flexible si los tipos no coinciden exactamente
                $option.prop('selected', true);
            }
            $selectElement.append($option);
        });
    }
    
    function poblarSelectSimple($selectElement, dataArray, defaultOptionText, defaultSelectedValue = '') {
        $selectElement.empty().append($('<option>', { value: '', text: defaultOptionText, disabled: true, selected: (defaultSelectedValue === '') }));
        $.each(dataArray, function(i, item) {
            var $option = $('<option>', { value: item, text: item });
            if (item == defaultSelectedValue) {
                $option.prop('selected', true);
            }
            $selectElement.append($option);
        });
    }

    function cargarCatalogosIniciales() {
        $.ajax({
            url: '../AJAX/vehiculos_ajax.php',type: 'GET', data: { accion: 'getCatalogos' }, dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    poblarSelect($('#mar_id'), response.marcas, 'mar_id', 'mar_nombre', 'Selecciona marca...');
                    poblarSelect($('#tiv_id'), response.tipos_vehiculo, 'tiv_id', 'tiv_nombre', 'Selecciona tipo...');
                    poblarSelectSimple($('#veh_ubicacion_provincia'), response.provincias, 'Selecciona provincia de ubicación...');
                    poblarSelectSimple($('#veh_placa_provincia_origen'), response.provincias, 'Selecciona provincia de placa...');
                } else { 
                    $('#formSubmissionMessage').html('<div class="alert alert-danger">Error al cargar catálogos: ' + (response.message || 'Respuesta no exitosa.') + '</div>').show();
                    console.error('Error en respuesta de getCatalogos:', response);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) { 
                $('#formSubmissionMessage').html('<div class="alert alert-danger">Error de conexión al cargar datos iniciales. Por favor, revisa la consola.</div>').show();
                console.error('AJAX error en getCatalogos:', textStatus, errorThrown, jqXHR.responseText);
            }
        });
    }
    cargarCatalogosIniciales();

    $('#mar_id').on('change', function() {
        var marcaId = $(this).val(); var $selectModelos = $('#mod_id');
        $selectModelos.empty().append('<option value="" selected disabled>Cargando modelos...</option>').prop('disabled', true);
        if (marcaId) {
            $.ajax({
                url: '../AJAX/vehiculos_ajax.php', type: 'GET', data: { accion: 'getModelos', marca_id: marcaId }, dataType: 'json',
                success: function(response) {
                    $selectModelos.empty();
                    if (response.status === 'success' && response.modelos && response.modelos.length > 0) {
                        poblarSelect($selectModelos, response.modelos, 'mod_id', 'mod_nombre', 'Selecciona modelo...');
                        $selectModelos.prop('disabled', false);
                    } else { 
                        $selectModelos.append('<option value="" selected disabled>No hay modelos disponibles</option>').prop('disabled', true);
                        if(response.message) console.warn('Advertencia en getModelos:', response.message);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) { 
                    $selectModelos.empty().append('<option value="" selected disabled>Error de conexión</option>'); 
                    console.error('AJAX error en getModelos:', textStatus, errorThrown, jqXHR.responseText);
                }
            });
        } else { $selectModelos.empty().append('<option value="" selected disabled>Selecciona una marca primero...</option>').prop('disabled', true); }
    });

    $('#veh_ubicacion_provincia').on('change', function() {
        var provincia = $(this).val(); var $selectCiudades = $('#veh_ubicacion_ciudad');
        $selectCiudades.empty().append('<option value="" selected disabled>Cargando ciudades...</option>').prop('disabled', true);
        if (provincia && provinciasCiudades[provincia]) {
            poblarSelectSimple($selectCiudades, provinciasCiudades[provincia], 'Selecciona una ciudad...');
            $selectCiudades.prop('disabled', false);
        } else if (provincia) { // Provincia seleccionada pero no encontrada en el objeto (debería ser raro si se carga desde el mismo fuente)
            $selectCiudades.empty().append('<option value="" selected disabled>No hay ciudades para esta provincia</option>').prop('disabled', true);
        }
         else { $selectCiudades.empty().append('<option value="" selected disabled>Selecciona provincia primero...</option>').prop('disabled', true); }
    });

    var $kilometrajeContainer = $('#kilometraje_div_container');
    var $kilometrajeInput = $('#veh_kilometraje');
    var $kilometrajeLabel = $('#label_kilometraje');
    var $camposPlacaGroup = $('#campos_placa_group');
    var $placaProvinciaInput = $('#veh_placa_provincia_origen');
    var $ultimoDigitoInput = $('#veh_ultimo_digito_placa');

    function actualizarCamposUsado(esUsado) {
        if (esUsado) {
            $kilometrajeLabel.html('Recorrido (km) <span class="text-danger">*</span>');
            $kilometrajeInput.prop('required', true);
            $placaProvinciaInput.prop('required', true);
            $ultimoDigitoInput.prop('required', true);
            $kilometrajeContainer.slideDown();
            $camposPlacaGroup.slideDown();
        } else {
            $kilometrajeLabel.html('Recorrido (km)');
            $kilometrajeInput.prop('required', false).val('').removeClass('is-invalid is-valid');
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
            // Para vehículos nuevos, mostrar solo año actual +1 y +2
            // y seleccionar el más alto por defecto.
            var anioMasNuevo = currentYear + 1;
            $selectAnio.append($('<option>', { value: '', text: 'Selecciona año...', disabled: true }));
            for (var year = anioMasNuevo; year >= currentYear + 1; year--) {
                 var $option = $('<option>', { value: year, text: year });
                 if (year === anioMasNuevo) {
                    // $option.prop('selected', true); // Comentado para que el usuario seleccione explícitamente
                 }
                 $selectAnio.append($option);
            }
             // Seleccionar el más nuevo por defecto si así se desea, o dejar que el usuario elija.
             // Para asegurar que el placeholder "Selecciona año..." no quede seleccionado si hay opciones:
            if ($selectAnio.find('option[value="' + anioMasNuevo + '"]').length > 0) {
                 $selectAnio.val(anioMasNuevo); // Seleccionar el año más nuevo
            } else if ($selectAnio.find('option[value="' + (currentYear + 1) + '"]').length > 0) {
                $selectAnio.val(currentYear + 1); // Si no, el siguiente
            } else {
                $selectAnio.val(''); // Si no hay opciones, dejar el placeholder
            }


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

    $('#veh_imagenes').on('change', function(event) {
        var $previewContainer = $('#imagePreviewContainer'); $previewContainer.empty();
        if (this.files) {
            var files = Array.from(this.files);
            if (files.length > 10) { alert("Máximo 10 imágenes."); $(this).val(''); $previewContainer.html('<small class="text-muted align-self-center mx-auto">Previsualización...</small>'); return; }
            if (files.length === 0 && $(this).prop('required')) { $(this).addClass('is-invalid'); } 
            else { $(this).removeClass('is-invalid'); }
            if (files.length === 0) { $previewContainer.html('<small class="text-muted align-self-center mx-auto">Previsualización...</small>'); }
            files.forEach(function(file) {
                if (file.type.startsWith('image/')) {
                    var reader = new FileReader();
                    reader.onload = function(e) { $('<img>').attr('src', e.target.result).addClass('img-thumbnail m-1').css({'height': '100px', 'width': 'auto', 'object-fit': 'cover'}).appendTo($previewContainer); }
                    reader.readAsDataURL(file);
                }
            });
        } else { $previewContainer.html('<small class="text-muted align-self-center mx-auto">Previsualización...</small>');}
    });

    $('#publicarVehiculoForm').on('submit', function(event) {
        var form = this; var $formMessage = $('#formSubmissionMessage'); $formMessage.html('').hide();
        var $imagenesInput = $('#veh_imagenes');
        if ($imagenesInput.prop('required') && ($imagenesInput[0].files.length === 0)) {
            event.preventDefault(); event.stopPropagation(); $imagenesInput.addClass('is-invalid');
            $imagenesInput[0].setCustomValidity("Debes subir al menos una imagen.");
            $(form).addClass('was-validated'); 
            $formMessage.html('<div class="alert alert-warning">Sube al menos una imagen.</div>').show();
            return;
        } else { $imagenesInput.removeClass('is-invalid'); $imagenesInput[0].setCustomValidity(""); }

        if (!form.checkValidity()) {
            event.preventDefault(); event.stopPropagation(); $(form).addClass('was-validated');
            $formMessage.html('<div class="alert alert-warning">Corrige los errores resaltados.</div>').show();
            return;
        }
        $(form).addClass('was-validated'); event.preventDefault();
        var formData = new FormData(this); var $submitButton = $(this).find('button[type="submit"]');
        var originalButtonText = $submitButton.html();
        $submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Publicando...');
        $formMessage.html('<div class="alert alert-info">Enviando datos...</div>').show();
        $.ajax({
            url: '../AJAX/vehiculos_ajax.php', type: 'POST', data: formData, dataType: 'json', contentType: false, processData: false,
            success: function(response) {
                if (response.status === 'success') {
                    $formMessage.html('<div class="alert alert-success">' + $('<div/>').text(response.message).html() + (response.veh_id ? ' ID: ' + $('<div/>').text(response.veh_id).html() : '') + '</div>');
                    $('#publicarVehiculoForm')[0].reset(); $('#imagePreviewContainer').html('<small class="text-muted align-self-center mx-auto">Previsualización...</small>');
                    $(form).removeClass('was-validated'); $('#veh_condicion').trigger('change');
                } else { $formMessage.html('<div class="alert alert-danger">Error: ' + $('<div/>').text(response.message || 'Respuesta no exitosa del servidor.').html() + '</div>'); }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $formMessage.html('<div class="alert alert-danger">Error de conexión o del servidor (' + textStatus + '). Revisa la consola.</div>');
                console.error("AJAX Error publicando vehículo:", jqXHR.responseText, textStatus, errorThrown);
            },
            complete: function() { $submitButton.prop('disabled', false).html(originalButtonText); }
        });
    });

    $('#publicarVehiculoForm').on('reset', function() {
        $(this).removeClass('was-validated');
        $('#imagePreviewContainer').html('<small class="text-muted align-self-center mx-auto">Previsualización...</small>');
        $('#formSubmissionMessage').html('').hide();
        actualizarCamposUsado(false);
        $('#veh_condicion').val('');
        $('#mod_id').empty().append('<option value="" selected disabled>Selecciona marca...</option>').prop('disabled', true);
        $('#veh_ubicacion_ciudad').empty().append('<option value="" selected disabled>Selecciona provincia...</option>').prop('disabled', true);
    });
});