$(document).ready(function () {
    const vehIdParaEditar = $('#veh_id_form').val();
    const publicarVehiculoForm = $('#editarVehiculoForm'); // Reutilizamos ID, pero es el form de edición
    const imagePreviewContainer = $('#newImagePreviewContainer'); // Para nuevas imágenes
    const currentImagesPreviewContainer = $('#currentImagesPreviewContainer');
    let uploadedFiles = []; // Para nuevas imágenes
    let imagenesActuales = []; // Para llevar control de las imágenes existentes
    let imagenesAEliminar = []; // IDs de imágenes existentes a eliminar
    let imagenPrincipalActualId = null; // ID de la imagen principal actual
    let nuevaImagenPrincipalNombreTemporal = null; // Nombre temporal de la nueva imagen principal (si se elige una nueva)

    // Selects y sus URLs para carga inicial (similar a publicar_vehiculo.js)
    const selectsConfig = [
        { id: '#mar_id', url: './../AJAX/vehiculos_ajax.php?accion=getCatalogos', tipo: 'marcas', },
        { id: '#tiv_id', url: './../AJAX/vehiculos_ajax.php?accion=getCatalogos', tipo: 'tipos_vehiculo' },
        { id: '#veh_ubicacion_provincia', url: './../AJAX/vehiculos_ajax.php?accion=getCatalogos', tipo: 'provincias' },
        { id: '#veh_placa_provincia_origen', url: './../AJAX/vehiculos_ajax.php?accion=getCatalogos', tipo: 'provincias', optional: true }
    ];

    // --- INICIALIZACIÓN Y CARGA DE DATOS DEL VEHÍCULO ---
    if (vehIdParaEditar) {
        $('#tituloVehiculoID').text(`ID: ${vehIdParaEditar}`);
        cargarDatosVehiculoParaEdicion(vehIdParaEditar);
    } else {
        mostrarAlerta('error', 'No se proporcionó un ID de vehículo para editar.', () => {
            window.location.href = 'admin_vehiculos.php';
        });
    }

    function mostrarOverlayCarga(mostrar) {
    const loader = $('#page-loader'); // Apuntamos al loader que ya existe

    if (mostrar) {
        loader.show(); 
    } else {
        loader.hide();
    }
}

    async function cargarDatosVehiculoParaEdicion(vehId) {
        mostrarOverlayCarga(true);
        try {
            const response = await $.ajax({
                url: `./../AJAX/vehiculos_ajax.php?accion=getDetallesVehiculoParaEdicion&veh_id=${vehId}`,
                type: 'GET',
                dataType: 'json'
            });

            if (response.status === 'success' && response.data) {
                await poblarFormulario(response.data.vehiculo, response.data.imagenes);
                imagenesActuales = response.data.imagenes || [];
                renderizarImagenesActuales();
            } else {
                throw new Error(response.message || 'No se pudieron cargar los datos del vehículo.');
            }
        } catch (error) {
            console.error("Error cargando datos del vehículo:", error);
            mostrarAlerta('error', `Error al cargar datos: ${error.message}. Redirigiendo...`, () => {
                window.location.href = 'admin_vehiculos.php';
            });
        } finally {
            mostrarOverlayCarga(false);
        }
    }

    async function poblarFormulario(vehiculoData, imagenesData) {
        // Poblar selects básicos primero (marcas, tipos, provincias)
        for (const config of selectsConfig) {
            await cargarOpcionesSelect(config.id, config.url, config.tipo, vehiculoData[$(config.id).attr('name')]);
        }
        
        if (vehiculoData.mar_id) {
            await cargarOpcionesSelect('#mod_id', `./../AJAX/vehiculos_ajax.php?accion=getModelos&marca_id=${vehiculoData.mar_id}`, 'modelos', vehiculoData.mod_id);
            $('#mod_id').prop('disabled', false);
        }

        if (vehiculoData.veh_ubicacion_provincia) {
            await cargarOpcionesSelect('#veh_ubicacion_ciudad', `./../AJAX/vehiculos_ajax.php?accion=getCiudades&provincia=${encodeURIComponent(vehiculoData.veh_ubicacion_provincia)}`, 'ciudades', vehiculoData.veh_ubicacion_ciudad);
            $('#veh_ubicacion_ciudad').prop('disabled', false);
        }
        
        $('#veh_subtipo_vehiculo').val(vehiculoData.veh_subtipo_vehiculo);
        $('#veh_condicion').val(vehiculoData.veh_condicion).trigger('change');
        $('#veh_anio').val(vehiculoData.veh_anio);
        $('#veh_kilometraje').val(vehiculoData.veh_kilometraje);
        $('#veh_placa').val(vehiculoData.veh_placa).trigger('input'); // Trigger input para que la lógica de placas se ejecute al cargar
        $('#veh_ultimo_digito_placa').val(vehiculoData.veh_ultimo_digito_placa);
        $('#veh_precio').val(parseFloat(vehiculoData.veh_precio).toFixed(2));
        $('#veh_vin').val(vehiculoData.veh_vin);
        
        if (vehiculoData.veh_fecha_publicacion) {
            $('#veh_fecha_publicacion').val(vehiculoData.veh_fecha_publicacion.split(' ')[0]);
        }
        
        $('#veh_color_exterior').val(vehiculoData.veh_color_exterior);
        $('#veh_color_interior').val(vehiculoData.veh_color_interior);
        $('#veh_detalles_motor').val(vehiculoData.veh_detalles_motor);
        $('#veh_tipo_transmision').val(vehiculoData.veh_tipo_transmision);
        $('#veh_traccion').val(vehiculoData.veh_traccion);
        $('#veh_tipo_combustible').val(vehiculoData.veh_tipo_combustible);
        $('#veh_tipo_direccion').val(vehiculoData.veh_tipo_direccion);
        $('#veh_tipo_vidrios').val(vehiculoData.veh_tipo_vidrios);
        $('#veh_sistema_climatizacion').val(vehiculoData.veh_sistema_climatizacion);
        $('#veh_descripcion').val(vehiculoData.veh_descripcion);

        if (vehiculoData.veh_detalles_extra) {
            const extras = vehiculoData.veh_detalles_extra.split(',').map(extra => extra.trim());
            extras.forEach(extra => {
                $(`input[name="veh_detalles_extra[]"][value="${extra}"]`).prop('checked', true);
            });
        }
        toggleCamposVehiculoUsado(vehiculoData.veh_condicion === 'usado');
    }

    async function cargarOpcionesSelect(selectId, url, tipoDato, valorSeleccionado = null) {
        try {
            const response = await $.ajax({ url: url, type: 'GET', dataType: 'json' });
            if (response.status === 'success') {
                const select = $(selectId);
                select.empty().append($('<option>', { value: '', text: `Selecciona...` }));
                let datos = [];
                if (tipoDato === 'marcas') datos = response.marcas;
                else if (tipoDato === 'modelos') datos = response.modelos;
                else if (tipoDato === 'tipos_vehiculo') datos = response.tipos_vehiculo;
                else if (tipoDato === 'provincias') datos = response.provincias;
                else if (tipoDato === 'ciudades') datos = response.ciudades;

                $.each(datos, function (i, item) {
                    let value, text;
                    if (tipoDato === 'marcas') { value = item.mar_id; text = item.mar_nombre; }
                    else if (tipoDato === 'modelos') { value = item.mod_id; text = item.mod_nombre; }
                    else if (tipoDato === 'tipos_vehiculo') { value = item.tiv_id; text = item.tiv_nombre; }
                    else { value = item; text = item; }
                    select.append($('<option>', { value: value, text: text }));
                });
                if (valorSeleccionado) {
                    select.val(valorSeleccionado);
                }
                if (select.prop('disabled') && datos.length > 0) {
                    select.prop('disabled', false);
                } else if (datos.length === 0 && (selectId === '#mod_id' || selectId === '#veh_ubicacion_ciudad')) {
                     select.prop('disabled', true);
                }

            } else { throw new Error(response.message || `Error cargando ${tipoDato}`); }
        } catch (error) {
            console.error(`Error en cargarOpcionesSelect para ${selectId}:`, error);
            $(selectId).empty().append($('<option>', { value: '', text: `Error al cargar ${tipoDato}` })).prop('disabled', true);
        }
    }
    
    $('#mar_id').change(function () {
        const marcaId = $(this).val();
        if (marcaId) {
            cargarOpcionesSelect('#mod_id', `./../AJAX/vehiculos_ajax.php?accion=getModelos&marca_id=${marcaId}`, 'modelos');
            $('#mod_id').prop('disabled', false);
        } else {
            $('#mod_id').empty().append($('<option>', { value: '', text: 'Selecciona marca...' })).prop('disabled', true);
        }
    });

    $('#veh_ubicacion_provincia').change(function () {
        const provincia = $(this).val();
        if (provincia) {
            cargarOpcionesSelect('#veh_ubicacion_ciudad', `./../AJAX/vehiculos_ajax.php?accion=getCiudades&provincia=${encodeURIComponent(provincia)}`, 'ciudades');
            $('#veh_ubicacion_ciudad').prop('disabled', false);
        } else {
            $('#veh_ubicacion_ciudad').empty().append($('<option>', { value: '', text: 'Selecciona provincia...' })).prop('disabled', true);
        }
    });
    
    $('#veh_condicion').change(function () {
        toggleCamposVehiculoUsado($(this).val() === 'usado');
    });

    function toggleCamposVehiculoUsado(esUsado) {
        const camposPlacaGroup = $('#campos_placa_group');
        const kilometrajeDiv = $('#kilometraje_div_container');
        const kmInput = $('#veh_kilometraje');
        const placaInput = $('#veh_placa');
        const placaProvinciaSelect = $('#veh_placa_provincia_origen');
        const ultimoDigitoSelect = $('#veh_ultimo_digito_placa');

        if (esUsado) {
            kilometrajeDiv.show();
            kmInput.prop('required', true).attr('placeholder', 'Ej: 25000');
            $('#label_kilometraje').html('Recorrido (km) <span class="text-danger">*</span>');
            camposPlacaGroup.show();
        } else {
            kilometrajeDiv.show();
            kmInput.prop('required', false).val('0').attr('placeholder', '0 (Nuevo)');
            $('#label_kilometraje').html('Recorrido (km)');
            camposPlacaGroup.hide();
            placaInput.prop('required', false);
            placaProvinciaSelect.prop('required', false);
            ultimoDigitoSelect.prop('required', false);
        }
    }

    // --- MANEJO DE IMÁGENES --- (Sin cambios, ya funciona)
    function renderizarImagenesActuales() {
        currentImagesPreviewContainer.empty();
        if (imagenesActuales.length === 0) {
            currentImagesPreviewContainer.html('<small class="text-muted align-self-center mx-auto">Este vehículo no tiene imágenes actualmente.</small>');
            return;
        }

        imagenesActuales.forEach(img => {
            if (imagenesAEliminar.includes(img.ima_id)) return;
            let imgUrlCorrected = img.ima_url;
            if (imgUrlCorrected && !imgUrlCorrected.startsWith('../')) {
                 imgUrlCorrected = '../' + imgUrlCorrected;
            }
            const wrapper = $(`<div class="img-preview-wrapper" data-id="${img.ima_id}"><img src="${imgUrlCorrected}" alt="Imagen actual"><button type="button" class="btn btn-danger btn-sm btn-remove-img" title="Eliminar esta imagen"><i class="bi bi-trash"></i></button></div>`);
            if (img.ima_es_principal) {
                imagenPrincipalActualId = img.ima_id;
                wrapper.append('<span class="badge bg-success badge-principal">Principal</span>');
                wrapper.addClass('border-success border-2');
            } else {
                wrapper.append(`<button type="button" class="btn btn-outline-success btn-sm btn-set-principal mt-1" title="Marcar como principal">Principal</button>`);
            }
            currentImagesPreviewContainer.append(wrapper);
        });
         if (currentImagesPreviewContainer.is(':empty')) {
             currentImagesPreviewContainer.html('<small class="text-muted align-self-center mx-auto">Todas las imágenes han sido marcadas para eliminar. Añade nuevas imágenes.</small>');
         }
    }
    currentImagesPreviewContainer.on('click', '.btn-remove-img', function () {
        const imageId = $(this).closest('.img-preview-wrapper').data('id');
        if (imageId) {
            imagenesAEliminar.push(String(imageId));
            $(this).closest('.img-preview-wrapper').hide();
            if (imagenPrincipalActualId == imageId) {
                imagenPrincipalActualId = null; 
            }
            $('#imagenes_a_eliminar_form').val(imagenesAEliminar.join(','));
             if ($('#currentImagesPreviewContainer .img-preview-wrapper:visible').length === 0) {
                currentImagesPreviewContainer.html('<small class="text-muted align-self-center mx-auto">Todas las imágenes han sido marcadas para eliminar. Añade nuevas imágenes.</small>');
            }
        }
    });
    currentImagesPreviewContainer.on('click', '.btn-set-principal', function () {
        const imageId = $(this).closest('.img-preview-wrapper').data('id');
        imagenPrincipalActualId = imageId;
        nuevaImagenPrincipalNombreTemporal = null;
        $('#currentImagesPreviewContainer .img-preview-wrapper').removeClass('border-success border-2');
        $('#currentImagesPreviewContainer .badge-principal').remove();
        $('#currentImagesPreviewContainer .btn-set-principal').show();
        const wrapperSeleccionado = $(this).closest('.img-preview-wrapper');
        wrapperSeleccionado.addClass('border-success border-2');
        wrapperSeleccionado.find('.btn-set-principal').hide();
        wrapperSeleccionado.append('<span class="badge bg-success badge-principal">Principal</span>');
        $('#newImagePreviewContainer .img-preview-wrapper').removeClass('border-primary border-2');
        $('#newImagePreviewContainer .badge-principal-nueva').remove();
        $('#newImagePreviewContainer .btn-set-nueva-principal').show();
    });
    $('#veh_imagenes_nuevas').on('change', function (event) {
        imagePreviewContainer.empty().html('<small class="text-muted align-self-center mx-auto">Previsualización de nuevas imágenes aparecerá aquí...</small>');
        uploadedFiles = Array.from(event.target.files);
        if (uploadedFiles.length > 0) {
            imagePreviewContainer.empty();
        }
        uploadedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const wrapper = $(`<div class="img-preview-wrapper" data-filename="${file.name}"><img src="${e.target.result}" alt="${file.name}"><button type="button" class="btn btn-danger btn-sm btn-remove-new-img" title="Eliminar esta nueva imagen"><i class="bi bi-x-lg"></i></button></div>`);
                if (!imagenPrincipalActualId && !nuevaImagenPrincipalNombreTemporal && index === 0) {
                    wrapper.addClass('border-primary border-2');
                    wrapper.append('<span class="badge bg-info badge-principal-nueva">Principal (Nueva)</span>');
                    nuevaImagenPrincipalNombreTemporal = file.name;
                } else {
                     wrapper.append(`<button type="button" class="btn btn-outline-info btn-sm btn-set-nueva-principal mt-1" title="Marcar como principal">Principal</button>`);
                }
                imagePreviewContainer.append(wrapper);
            }
            reader.readAsDataURL(file);
        });
    });
    imagePreviewContainer.on('click', '.btn-remove-new-img', function () {
        const fileNameToRemove = $(this).closest('.img-preview-wrapper').data('filename');
        uploadedFiles = uploadedFiles.filter(file => file.name !== fileNameToRemove);
        $(this).closest('.img-preview-wrapper').remove();
        if (nuevaImagenPrincipalNombreTemporal === fileNameToRemove) {
            nuevaImagenPrincipalNombreTemporal = null;
            if (uploadedFiles.length > 0) {
                const firstNewImageWrapper = $('#newImagePreviewContainer .img-preview-wrapper').first();
                if (firstNewImageWrapper.length) {
                    firstNewImageWrapper.addClass('border-primary border-2');
                    firstNewImageWrapper.append('<span class="badge bg-info badge-principal-nueva">Principal (Nueva)</span>');
                    firstNewImageWrapper.find('.btn-set-nueva-principal').hide();
                    nuevaImagenPrincipalNombreTemporal = uploadedFiles[0].name;
                }
            }
        }
        if (imagePreviewContainer.is(':empty') && uploadedFiles.length === 0) {
            imagePreviewContainer.html('<small class="text-muted align-self-center mx-auto">Previsualización de nuevas imágenes aparecerá aquí...</small>');
        }
        const dt = new DataTransfer();
        uploadedFiles.forEach(file => dt.items.add(file));
        $('#veh_imagenes_nuevas')[0].files = dt.files;
    });
    imagePreviewContainer.on('click', '.btn-set-nueva-principal', function () {
        const fileName = $(this).closest('.img-preview-wrapper').data('filename');
        nuevaImagenPrincipalNombreTemporal = fileName;
        imagenPrincipalActualId = null;
        $('#newImagePreviewContainer .img-preview-wrapper').removeClass('border-primary border-2');
        $('#newImagePreviewContainer .badge-principal-nueva').remove();
        $('#newImagePreviewContainer .btn-set-nueva-principal').show();
        const wrapperSeleccionado = $(this).closest('.img-preview-wrapper');
        wrapperSeleccionado.addClass('border-primary border-2');
        wrapperSeleccionado.find('.btn-set-nueva-principal').hide();
        wrapperSeleccionado.append('<span class="badge bg-info badge-principal-nueva">Principal (Nueva)</span>');
        $('#currentImagesPreviewContainer .img-preview-wrapper').removeClass('border-success border-2');
        $('#currentImagesPreviewContainer .badge-principal').remove();
        $('#currentImagesPreviewContainer .btn-set-principal').show();
    });

    // --- ENVÍO DEL FORMULARIO --- (Sin cambios, ya funciona)
    publicarVehiculoForm.on('submit', function (event) {
        event.preventDefault();
        event.stopPropagation();
        if (!this.checkValidity()) {
            $(this).addClass('was-validated');
            mostrarAlerta('warning', 'Por favor, corrige los errores en el formulario.');
            return;
        }
        $(this).addClass('was-validated');
        const totalImagenesVisibles = $('#currentImagesPreviewContainer .img-preview-wrapper:visible').length + uploadedFiles.length;
        if (totalImagenesVisibles === 0) {
             mostrarAlerta('error', 'Debes tener al menos una imagen para el vehículo (ya sea existente o nueva).');
             return;
        }
        if (!imagenPrincipalActualId && !nuevaImagenPrincipalNombreTemporal) {
            mostrarAlerta('error', 'Debes seleccionar una imagen como principal.');
            return;
        }
        const formData = new FormData(this);
        formData.delete('veh_imagenes_nuevas[]');
        uploadedFiles.forEach(file => { formData.append('veh_imagenes_nuevas[]', file); });
        formData.set('imagenes_a_eliminar', imagenesAEliminar.join(','));
        formData.set('imagen_principal_actual_id', imagenPrincipalActualId || '');
        formData.set('nueva_imagen_principal_nombre_temporal', nuevaImagenPrincipalNombreTemporal || '');
        mostrarOverlayCarga(true);
        $.ajax({
            url: './../AJAX/vehiculos_ajax.php', type: 'POST', data: formData, dataType: 'json', contentType: false, processData: false,
            success: function (response) {
                if (response.status === 'success') {
                    mostrarAlerta('success', response.message || 'Vehículo actualizado con éxito. Redirigiendo...', () => {
                        window.location.href = REDIRECT_URL_ON_SUCCESS;
                    });
                } else {
                    mostrarAlerta('error', response.message || 'Error al actualizar el vehículo.');
                }
            },
            error: function (xhr, status, error) {
                console.error("Error en AJAX:", xhr, status, error);
                mostrarAlerta('error', 'Error de conexión o del servidor: ' + error);
            },
            complete: function () {
                mostrarOverlayCarga(false);
                publicarVehiculoForm.removeClass('was-validated');
            }
        });
    });


    // ===== INICIO: ALGORITMO DE GENERACIÓN DE DESCRIPCIÓN (COPIADO DE PUBLICAR_VEHICULO.JS) =====
    function generarDescripcionVehiculo() {
        const datos = {
            marca: $('#mar_id option:selected').text() || '',
            modelo: $('#mod_id option:selected').text() || '',
            tipoVehiculo: $('#tiv_id option:selected').text() || '',
            subtipo: $('#veh_subtipo_vehiculo').val() || '',
            condicion: $('#veh_condicion').val() || '',
            anio: $('#veh_anio').val() || '',
            kilometraje: $('#veh_kilometraje').val() || '',
            precio: $('#veh_precio').val() || '',
            provincia: $('#veh_ubicacion_provincia').val() || '',
            ciudad: $('#veh_ubicacion_ciudad').val() || '',
            colorExterior: $('#veh_color_exterior').val() || '',
            colorInterior: $('#veh_color_interior').val() || '',
            detallesMotor: $('#veh_detalles_motor').val() || '',
            transmision: $('#veh_tipo_transmision').val() || '',
            traccion: $('#veh_traccion').val() || '',
            combustible: $('#veh_tipo_combustible').val() || '',
            direccion: $('#veh_tipo_direccion').val() || '',
            vidrios: $('#veh_tipo_vidrios').val() || '',
            climatizacion: $('#veh_sistema_climatizacion').val() || '',
            extras: $('input[name="veh_detalles_extra[]"]:checked').map(function() { return $(this).val(); }).get(),
            placa: $('#veh_placa').val() || '',
            placaProvincia: $('#veh_placa_provincia_origen').val() || '',
            ultimoDigito: $('#veh_ultimo_digito_placa').val() || ''
        };
        if (!(datos.marca && datos.marca !== 'Selecciona marca...' && datos.modelo && datos.modelo !== 'Selecciona modelo...' && datos.anio && datos.precio)) {
            return null;
        }
        let descripcion = '';
        const frasesIntro = [ `¡Descubre este increíble ${datos.marca} ${datos.modelo} ${datos.anio}!`, `Te presentamos este espectacular ${datos.marca} ${datos.modelo} ${datos.anio}.`, `¡Oportunidad única! ${datos.marca} ${datos.modelo} ${datos.anio} en excelente estado.`, `No te pierdas este magnífico ${datos.marca} ${datos.modelo} ${datos.anio}.` ];
        let intro = frasesIntro[Math.floor(Math.random() * frasesIntro.length)];
        if (datos.condicion === 'nuevo') { intro += ' Este vehículo completamente nuevo te ofrece la última tecnología y garantía de fábrica.';
        } else if (datos.condicion === 'usado' && datos.kilometraje) {
            const km = parseInt(datos.kilometraje);
            if (km < 20000) intro += ' Con muy poco recorrido, prácticamente como nuevo.';
            else if (km < 50000) intro += ' Con un recorrido moderado y excelente mantenimiento.';
            else if (km < 100000) intro += ' Con un historial de uso responsable y cuidadoso.';
            else intro += ' Un vehículo con experiencia que aún tiene mucho que ofrecer.';
        }
        descripcion += intro + '\n\n';
        let caracteristicas = 'Características Destacadas:\n';
        if (datos.tipoVehiculo) { caracteristicas += `• Tipo: ${datos.tipoVehiculo}${datos.subtipo ? ` (${datos.subtipo})` : ''}\n`; }
        if (datos.anio) { caracteristicas += `• Año de fabricación: ${datos.anio}\n`; }
        if (datos.kilometraje && datos.condicion === 'usado') { caracteristicas += `• Recorrido: ${parseInt(datos.kilometraje).toLocaleString()} km\n`; }
        if (datos.colorExterior) { caracteristicas += `• Color exterior: ${datos.colorExterior}${datos.colorInterior ? ` / Interior: ${datos.colorInterior}` : ''}\n`; }
        if (datos.ciudad && datos.provincia) { caracteristicas += `• Ubicación: ${datos.ciudad}, ${datos.provincia}\n`; }
        descripcion += caracteristicas + '\n';
        let especificaciones = 'Especificaciones Técnicas:\n';
        let tieneEspecificaciones = false;
        if (datos.detallesMotor) { especificaciones += `• Motor: ${datos.detallesMotor}\n`; tieneEspecificaciones = true; }
        if (datos.transmision) { especificaciones += `• Transmisión: ${datos.transmision}\n`; tieneEspecificaciones = true; }
        if (datos.traccion) { especificaciones += `• Tracción: ${datos.traccion}\n`; tieneEspecificaciones = true; }
        if (datos.combustible) { especificaciones += `• Combustible: ${datos.combustible}\n`; tieneEspecificaciones = true; }
        if (datos.direccion) { especificaciones += `• Dirección: ${datos.direccion}\n`; tieneEspecificaciones = true; }
        if (datos.vidrios) { especificaciones += `• Vidrios: ${datos.vidrios}\n`; tieneEspecificaciones = true; }
        if (datos.climatizacion && datos.climatizacion !== 'Ninguno') { especificaciones += `• Climatización: ${datos.climatizacion}\n`; tieneEspecificaciones = true; }
        if(tieneEspecificaciones) descripcion += especificaciones + '\n';
        if (datos.extras.length > 0) {
            let extrasTxt = 'Extras y Beneficios:\n';
            datos.extras.forEach(extra => { extrasTxt += `• ${extra}\n`; });
            descripcion += extrasTxt + '\n';
        }
        const llamadas = [ '¡No dejes pasar esta oportunidad! Contáctanos ahora para más información y agenda tu cita para verlo.', '¡Este vehículo no durará mucho en el mercado! Llámanos hoy mismo para más detalles.', '¿Interesado? Escríbenos o llámanos para coordinar una visita y prueba de manejo.', '¡Tu próximo vehículo te está esperando! Contáctanos para más información y financiamiento.' ];
        let llamada = llamadas[Math.floor(Math.random() * llamadas.length)];
        if (datos.extras.includes('Negociable')) llamada += ' ¡Precio negociable!';
        if (datos.extras.includes('Acepto Vehiculo Como Parte de Pago')) llamada += ' Aceptamos tu vehículo como parte de pago.';
        descripcion += llamada;
        return descripcion.trim();
    }
    $('#btnGenerarDescripcion').on('click', function() { // Suponiendo que tienes un botón con este ID en editar_auto.php
        const $boton = $(this);
        const $textarea = $('#veh_descripcion');
        const textoOriginal = $boton.html();
        $boton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Generando...');
        setTimeout(function() {
            try {
                const descripcionGenerada = generarDescripcionVehiculo();
                if (descripcionGenerada) {
                    const contenidoActual = $textarea.val().trim();
                    if (contenidoActual && !confirm('Ya hay contenido en la descripción. ¿Deseas reemplazarlo?')) {
                        // No hacer nada si el usuario cancela
                    } else {
                        $textarea.val(descripcionGenerada).removeClass('is-invalid is-valid');
                    }
                } else {
                    alert('Para generar la descripción automática, completa al menos: Marca, Modelo, Año y Precio.');
                }
            } catch (error) {
                console.error('Error al generar descripción:', error);
                alert('Ocurrió un error al generar la descripción.');
            }
            $boton.prop('disabled', false).html(textoOriginal);
        }, 500);
    });
    // ===== FIN: ALGORITMO DE GENERACIÓN DE DESCRIPCIÓN =====

    // ===== INICIO: LÓGICA DE PLACAS ECUATORIANAS (COPIADO DE PUBLICAR_VEHICULO.JS) =====
    const placasEcuadorMap = {
        'A': 'Azuay', 'B': 'Bolívar', 'C': 'Carchi', 'E': 'Esmeraldas', 'G': 'Guayas',
        'H': 'Chimborazo', 'I': 'Imbabura', 'J': 'Santo Domingo de los Tsáchilas',
        'K': 'Sucumbíos', 'L': 'Loja', 'M': 'Manabí', 'N': 'Napo', 'O': 'El Oro',
        'P': 'Pichincha', 'Q': 'Orellana', 'R': 'Los Ríos', 'S': 'Pastaza', 'T': 'Tungurahua',
        'U': 'Cañar', 'V': 'Morona Santiago', 'W': 'Galápagos', 'X': 'Cotopaxi',
        'Y': 'Santa Elena', 'Z': 'Zamora Chinchipe'
    };
    function obtenerProvinciaPorPlaca(placa) {
        if (typeof placa !== 'string' || placa.length === 0) return null;
        return placasEcuadorMap[placa.toUpperCase()[0]] || null;
    }
    function obtenerUltimoDigitoPlaca(placa) {
        if (typeof placa !== 'string' || placa.length < 1) return null;
        const digitos = placa.match(/\d/g);
        return digitos ? digitos[digitos.length - 1] : null;
    }
    function aplicarEfectoAutoLlenado($elemento) {
        $elemento.addClass('auto-filled');
        setTimeout(() => { $elemento.removeClass('auto-filled'); }, 1500);
    }
    $('#veh_placa').on('input', function() {
        let valor = $(this).val().toUpperCase().replace(/[^A-Z0-9]/g, '');
        if (valor.length > 3) { valor = valor.substring(0, 3) + '-' + valor.substring(3, 7); }
        $(this).val(valor);

        if ($('#veh_condicion').val() === 'usado') {
            const placaInput = $(this).val();
            const $provinciaSelect = $('#veh_placa_provincia_origen');
            const $ultimoDigitoSelect = $('#veh_ultimo_digito_placa');
            
            const provinciaDetectada = obtenerProvinciaPorPlaca(placaInput);
            if (provinciaDetectada) {
                if ($provinciaSelect.val() !== provinciaDetectada) {
                    $provinciaSelect.val(provinciaDetectada);
                    aplicarEfectoAutoLlenado($provinciaSelect);
                }
            } else {
                $provinciaSelect.val('');
            }
            
            const ultimoDigitoDetectado = obtenerUltimoDigitoPlaca(placaInput);
            if (ultimoDigitoDetectado !== null) {
                if ($ultimoDigitoSelect.val() !== ultimoDigitoDetectado) {
                    $ultimoDigitoSelect.val(ultimoDigitoDetectado);
                    aplicarEfectoAutoLlenado($ultimoDigitoSelect);
                }
            } else {
                $ultimoDigitoSelect.val('');
            }
        }
    });
    // ===== FIN: LÓGICA DE PLACAS ECUATORIANAS =====
});