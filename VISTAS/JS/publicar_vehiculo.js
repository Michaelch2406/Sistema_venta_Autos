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

    // ===== ALGORITMO DE GENERACIÓN DE DESCRIPCIÓN AUTOMÁTICA =====
    
    // Función principal para generar descripción automática
    function generarDescripcionVehiculo() {
        // Obtener todos los valores del formulario
        const datos = obtenerDatosFormulario();
        
        // Validar que tenemos los datos mínimos necesarios
        if (!validarDatosMinimos(datos)) {
            return null;
        }
        
        // Generar la descripción usando plantillas inteligentes
        const descripcion = construirDescripcion(datos);
        
        return descripcion;
    }

    function obtenerDatosFormulario() {
        return {
            // Información principal
            marca: $('#mar_id option:selected').text() || '',
            modelo: $('#mod_id option:selected').text() || '',
            tipoVehiculo: $('#tiv_id option:selected').text() || '',
            subtipo: $('#veh_subtipo_vehiculo').val() || '',
            condicion: $('#veh_condicion').val() || '',
            anio: $('#veh_anio').val() || '',
            kilometraje: $('#veh_kilometraje').val() || '',
            precio: $('#veh_precio').val() || '',
            
            // Ubicación y apariencia
            provincia: $('#veh_ubicacion_provincia').val() || '',
            ciudad: $('#veh_ubicacion_ciudad').val() || '',
            colorExterior: $('#veh_color_exterior').val() || '',
            colorInterior: $('#veh_color_interior').val() || '',
            
            // Especificaciones técnicas
            detallesMotor: $('#veh_detalles_motor').val() || '',
            transmision: $('#veh_tipo_transmision').val() || '',
            traccion: $('#veh_traccion').val() || '',
            combustible: $('#veh_tipo_combustible').val() || '',
            direccion: $('#veh_tipo_direccion').val() || '',
            vidrios: $('#veh_tipo_vidrios').val() || '',
            climatizacion: $('#veh_sistema_climatizacion').val() || '',
            
            // Extras seleccionados
            extras: obtenerExtrasSeleccionados(),
            
            // Datos de placa (si aplica)
            placa: $('#veh_placa').val() || '',
            placaProvincia: $('#veh_placa_provincia_origen').val() || '',
            ultimoDigito: $('#veh_ultimo_digito_placa').val() || ''
        };
    }

    function obtenerExtrasSeleccionados() {
        const extras = [];
        $('input[name="veh_detalles_extra[]"]:checked').each(function() {
            extras.push($(this).val());
        });
        return extras;
    }

    function validarDatosMinimos(datos) {
        // Verificar que tenemos al menos marca, modelo, año y precio
        return datos.marca && datos.marca !== 'Selecciona marca...' &&
               datos.modelo && datos.modelo !== 'Selecciona modelo...' &&
               datos.anio && 
               datos.precio;
    }

    function construirDescripcion(datos) {
        let descripcion = '';
        
        // 1. Introducción atractiva
        descripcion += generarIntroduccion(datos);
        
        // 2. Características principales
        descripcion += generarCaracteristicasPrincipales(datos);
        
        // 3. Especificaciones técnicas
        descripcion += generarEspecificacionesTecnicas(datos);
        
        // 4. Extras y beneficios
        descripcion += generarExtrasYBeneficios(datos);
        
        // 5. Llamada a la acción
        descripcion += generarLlamadaAccion(datos);
        
        return descripcion.trim();
    }

    function generarIntroduccion(datos) {
        const frases = [
            `¡Descubre este increíble ${datos.marca} ${datos.modelo} ${datos.anio}!`,
            `Te presentamos este espectacular ${datos.marca} ${datos.modelo} ${datos.anio}.`,
            `¡Oportunidad única! ${datos.marca} ${datos.modelo} ${datos.anio} en excelente estado.`,
            `No te pierdas este magnífico ${datos.marca} ${datos.modelo} ${datos.anio}.`
        ];
        
        let intro = frases[Math.floor(Math.random() * frases.length)];
        
        // Agregar información sobre la condición
        if (datos.condicion === 'nuevo') {
            intro += ' Este vehículo completamente nuevo te ofrece la última tecnología y garantía de fábrica.';
        } else if (datos.condicion === 'usado' && datos.kilometraje) {
            const km = parseInt(datos.kilometraje);
            if (km < 20000) {
                intro += ' Con muy poco recorrido, prácticamente como nuevo.';
            } else if (km < 50000) {
                intro += ' Con un recorrido moderado y excelente mantenimiento.';
            } else if (km < 100000) {
                intro += ' Con un historial de uso responsable y cuidadoso.';
            } else {
                intro += ' Un vehículo con experiencia que aún tiene mucho que ofrecer.';
            }
        }
        
        return intro + '\n\n';
    }

    function generarCaracteristicasPrincipales(datos) {
        let caracteristicas = '🚗 **Características Destacadas:**\n';
        
        // Tipo de vehículo y subtipo
        if (datos.tipoVehiculo) {
            caracteristicas += `• Tipo: ${datos.tipoVehiculo}`;
            if (datos.subtipo) {
                caracteristicas += ` (${datos.subtipo})`;
            }
            caracteristicas += '\n';
        }
        
        // Año y kilometraje
        if (datos.anio) {
            caracteristicas += `• Año de fabricación: ${datos.anio}\n`;
        }
        
        if (datos.kilometraje && datos.condicion === 'usado') {
            const km = parseInt(datos.kilometraje);
            caracteristicas += `• Recorrido: ${km.toLocaleString()} km\n`;
        }
        
        // Colores
        if (datos.colorExterior) {
            caracteristicas += `• Color exterior: ${datos.colorExterior}`;
            if (datos.colorInterior) {
                caracteristicas += ` / Interior: ${datos.colorInterior}`;
            }
            caracteristicas += '\n';
        }
        
        // Ubicación
        if (datos.ciudad && datos.provincia) {
            caracteristicas += `• Ubicación: ${datos.ciudad}, ${datos.provincia}\n`;
        }
        
        return caracteristicas + '\n';
    }

    function generarEspecificacionesTecnicas(datos) {
        let especificaciones = '⚙️ **Especificaciones Técnicas:**\n';
        let tieneEspecificaciones = false;
        
        // Motor
        if (datos.detallesMotor) {
            especificaciones += `• Motor: ${datos.detallesMotor}\n`;
            tieneEspecificaciones = true;
        }
        
        // Transmisión
        if (datos.transmision) {
            especificaciones += `• Transmisión: ${datos.transmision}\n`;
            tieneEspecificaciones = true;
        }
        
        // Tracción
        if (datos.traccion) {
            especificaciones += `• Tracción: ${datos.traccion}\n`;
            tieneEspecificaciones = true;
        }
        
        // Combustible
        if (datos.combustible) {
            especificaciones += `• Combustible: ${datos.combustible}\n`;
            tieneEspecificaciones = true;
        }
        
        // Dirección
        if (datos.direccion) {
            especificaciones += `• Dirección: ${datos.direccion}\n`;
            tieneEspecificaciones = true;
        }
        
        // Vidrios
        if (datos.vidrios) {
            especificaciones += `• Vidrios: ${datos.vidrios}\n`;
            tieneEspecificaciones = true;
        }
        
        // Climatización
        if (datos.climatizacion && datos.climatizacion !== 'Ninguno') {
            especificaciones += `• Climatización: ${datos.climatizacion}\n`;
            tieneEspecificaciones = true;
        }
        
        return tieneEspecificaciones ? especificaciones + '\n' : '';
    }

    function generarExtrasYBeneficios(datos) {
        if (datos.extras.length === 0) {
            return '';
        }
        
        let extras = '✨ **Extras y Beneficios:**\n';
        
        datos.extras.forEach(extra => {
            extras += `• ${extra}\n`;
        });
        
        return extras + '\n';
    }

    function generarLlamadaAccion(datos) {
        const llamadas = [
            '📞 ¡No dejes pasar esta oportunidad! Contáctanos ahora para más información y agenda tu cita para verlo.',
            '🤝 ¡Este vehículo no durará mucho en el mercado! Llámanos hoy mismo para más detalles.',
            '💬 ¿Interesado? Escríbenos o llámanos para coordinar una visita y prueba de manejo.',
            '⭐ ¡Tu próximo vehículo te está esperando! Contáctanos para más información y financiamiento.'
        ];
        
        let llamada = llamadas[Math.floor(Math.random() * llamadas.length)];
        
        // Agregar información sobre precio si es negociable
        if (datos.extras.includes('Negociable')) {
            llamada += ' ¡Precio negociable!';
        }
        
        // Agregar información sobre parte de pago
        if (datos.extras.includes('Acepto Vehiculo Como Parte de Pago')) {
            llamada += ' Aceptamos tu vehículo como parte de pago.';
        }
        
        return llamada;
    }

    // Event handler para el botón de generar descripción
    $('#btnGenerarDescripcion').on('click', function() {
        const $boton = $(this);
        const $textarea = $('#veh_descripcion');
        
        // Cambiar estado del botón a cargando
        const textoOriginal = $boton.html();
        $boton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Generando...');
        
        // Simular un pequeño delay para mejor UX
        setTimeout(function() {
            try {
                // Generar la descripción
                const descripcionGenerada = generarDescripcionVehiculo();
                
                if (descripcionGenerada) {
                    // Si ya hay contenido, preguntar si quiere reemplazarlo
                    const contenidoActual = $textarea.val().trim();
                    if (contenidoActual && contenidoActual.length > 0) {
                        if (confirm('Ya hay contenido en la descripción. ¿Deseas reemplazarlo con la descripción generada automáticamente?')) {
                            $textarea.val(descripcionGenerada);
                            // Remover clases de validación si las hay
                            $textarea.removeClass('is-invalid is-valid');
                            // Mostrar mensaje de éxito
                            mostrarMensajeExito('Descripción generada exitosamente');
                        }
                    } else {
                        $textarea.val(descripcionGenerada);
                        $textarea.removeClass('is-invalid is-valid');
                        mostrarMensajeExito('Descripción generada exitosamente');
                    }
                } else {
                    // Mostrar mensaje de error si no se pudo generar
                    mostrarMensajeError('Para generar la descripción automática, completa al menos: Marca, Modelo, Año y Precio.');
                }
            } catch (error) {
                console.error('Error al generar descripción:', error);
                mostrarMensajeError('Ocurrió un error al generar la descripción. Inténtalo de nuevo.');
            }
            
            // Restaurar estado del botón
            $boton.prop('disabled', false).html(textoOriginal);
        }, 800); // Delay de 800ms para simular procesamiento
    });

    // Funciones auxiliares para mostrar mensajes
    function mostrarMensajeExito(mensaje) {
        const $alert = $(`
            <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        // Insertar después del textarea
        $('#veh_descripcion').after($alert);
        
        // Auto-remover después de 3 segundos
        setTimeout(function() {
            $alert.fadeOut(function() {
                $(this).remove();
            });
        }, 3000);
    }

    function mostrarMensajeError(mensaje) {
        const $alert = $(`
            <div class="alert alert-warning alert-dismissible fade show mt-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        // Insertar después del textarea
        $('#veh_descripcion').after($alert);
        
        // Auto-remover después de 5 segundos
        setTimeout(function() {
            $alert.fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    }

    // ===== FIN DEL ALGORITMO DE GENERACIÓN DE DESCRIPCIÓN =====



    // ===== DATOS DE PLACAS DE ECUADOR Y LÓGICA DE AUTO-LLENADO =====

    const placasEcuadorMap = {
        'A': 'Azuay',
        'B': 'Bolívar',
        'C': 'Carchi',
        'E': 'Esmeraldas',
        'G': 'Guayas',
        'H': 'Chimborazo',
        'I': 'Imbabura',
        'J': 'Santo Domingo de los Tsáchilas',
        'K': 'Sucumbíos',
        'L': 'Loja',
        'M': 'Manabí',
        'N': 'Napo',
        'O': 'El Oro',
        'P': 'Pichincha',
        'Q': 'Orellana',
        'R': 'Los Ríos',
        'S': 'Pastaza',
        'T': 'Tungurahua',
        'U': 'Cañar',
        'V': 'Morona Santiago',
        'W': 'Galápagos',
        'X': 'Cotopaxi',
        'Y': 'Santa Elena',
        'Z': 'Zamora Chinchipe'
    };

    function obtenerProvinciaPorPlaca(placa) {
        if (typeof placa !== 'string' || placa.length === 0) {
            return null;
        }
        const primeraLetra = placa.toUpperCase()[0];
        return placasEcuadorMap[primeraLetra] || null;
    }

    function obtenerUltimoDigitoPlaca(placa) {
        if (typeof placa !== 'string' || placa.length < 1) {
            return null;
        }
        const digitos = placa.match(/\d+/g);
        if (digitos && digitos.length > 0) {
            const ultimoDigito = digitos[digitos.length - 1].slice(-1);
            return ultimoDigito;
        }
        return null;
    }

    function validarFormatoPlacaEcuador(placa) {
        // Formato: ABC-1234 o ABC-123
        const regex = /^[A-Z]{3}-\d{3,4}$/;
        return regex.test(placa.toUpperCase());
    }

    function mostrarIndicadoresAutoLlenado(mostrar) {
        const $badgeProvincia = $('#badge_auto_provincia');
        const $badgeDigito = $('#badge_auto_digito');
        const $helpProvincia = $('#help_provincia_auto');
        const $helpDigito = $('#help_digito_auto');

        if (mostrar) {
            $badgeProvincia.fadeIn(300);
            $badgeDigito.fadeIn(300);
            $helpProvincia.fadeIn(300);
            $helpDigito.fadeIn(300);
        } else {
            $badgeProvincia.fadeOut(300);
            $badgeDigito.fadeOut(300);
            $helpProvincia.fadeOut(300);
            $helpDigito.fadeOut(300);
        }
    }

    function aplicarEfectoAutoLlenado($elemento) {
        $elemento.addClass('auto-filled');
        setTimeout(() => {
            $elemento.removeClass('auto-filled');
        }, 2000);
    }

    // Event listener mejorado para el campo de placa
    $('#veh_placa').on('input blur', function() {
        const placaInput = $(this).val().trim().toUpperCase();
        const condicion = $('#veh_condicion').val();

        // Solo aplicar la lógica si la condición es 'usado'
        if (condicion === 'usado') {
            const $provinciaSelect = $('#veh_placa_provincia_origen');
            const $ultimoDigitoSelect = $('#veh_ultimo_digito_placa');
            const $placaField = $(this);

            if (placaInput.length >= 1) {
                // Agregar clase visual para indicar que está activa la detección
                $placaField.addClass('auto-detection-active');

                // Detectar provincia
                const provinciaDetectada = obtenerProvinciaPorPlaca(placaInput);
                if (provinciaDetectada) {
                    $provinciaSelect.val(provinciaDetectada);
                    aplicarEfectoAutoLlenado($provinciaSelect);
                    
                    // Remover clases de validación si las hay
                    $provinciaSelect.removeClass('is-invalid');
                } else {
                    $provinciaSelect.val('');
                }

                // Detectar último dígito
                const ultimoDigitoDetectado = obtenerUltimoDigitoPlaca(placaInput);
                if (ultimoDigitoDetectado !== null) {
                    $ultimoDigitoSelect.val(ultimoDigitoDetectado);
                    aplicarEfectoAutoLlenado($ultimoDigitoSelect);
                    
                    // Remover clases de validación si las hay
                    $ultimoDigitoSelect.removeClass('is-invalid');
                } else {
                    $ultimoDigitoSelect.val('');
                }

                // Mostrar indicadores visuales
                mostrarIndicadoresAutoLlenado(true);

            } else {
                // Limpiar campos si la placa está vacía
                $provinciaSelect.val('');
                $ultimoDigitoSelect.val('');
                $placaField.removeClass('auto-detection-active');
                mostrarIndicadoresAutoLlenado(false);
            }
        } else {
            // Si no es usado, ocultar indicadores
            mostrarIndicadoresAutoLlenado(false);
            $(this).removeClass('auto-detection-active');
        }
    });

    // Formateo automático de placa mientras se escribe
    $('#veh_placa').on('input', function() {
        let valor = $(this).val().toUpperCase().replace(/[^A-Z0-9]/g, '');
        
        // Agregar guión automáticamente después de 3 letras
        if (valor.length > 3) {
            valor = valor.substring(0, 3) + '-' + valor.substring(3, 7);
        }
        
        $(this).val(valor);
    });

    // Mejorar el event listener para cambio de condición
    $('#veh_condicion').on('change', function() {
        const $placaField = $('#veh_placa');
        const $provinciaSelect = $('#veh_placa_provincia_origen');
        const $ultimoDigitoSelect = $('#veh_ultimo_digito_placa');

        if ($(this).val() === 'nuevo') {
            // Limpiar campos y ocultar indicadores
            $provinciaSelect.val('');
            $ultimoDigitoSelect.val('');
            $placaField.removeClass('auto-detection-active');
            mostrarIndicadoresAutoLlenado(false);
        } else if ($(this).val() === 'usado') {
            // Si ya hay una placa ingresada, procesarla
            const placaActual = $placaField.val().trim();
            if (placaActual) {
                $placaField.trigger('input');
            }
        }
    });

    // ===== FIN DE DATOS DE PLACAS DE ECUADOR Y LÓGICA DE AUTO-LLENADO =====


