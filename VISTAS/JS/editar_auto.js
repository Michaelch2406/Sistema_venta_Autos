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
        { id: '#mar_id', url: '../AJAX/vehiculos_ajax.php?accion=getCatalogos', tipo: 'marcas', },
        { id: '#tiv_id', url: '../AJAX/vehiculos_ajax.php?accion=getCatalogos', tipo: 'tipos_vehiculo' },
        { id: '#veh_ubicacion_provincia', url: '../AJAX/vehiculos_ajax.php?accion=getCatalogos', tipo: 'provincias' },
        { id: '#veh_placa_provincia_origen', url: '../AJAX/vehiculos_ajax.php?accion=getCatalogos', tipo: 'provincias', optional: true }
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
        // Simplemente lo mostramos.
        // Asumimos que su CSS ya lo posiciona en el centro como un overlay.
        loader.show(); 
    } else {
        // Lo ocultamos.
        loader.hide();
    }
}

    async function cargarDatosVehiculoParaEdicion(vehId) {
        mostrarOverlayCarga(true);
        try {
            const response = await $.ajax({
                url: `../AJAX/vehiculos_ajax.php?accion=getDetallesVehiculoParaEdicion&veh_id=${vehId}`,
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
        
        // Una vez cargada la marca, cargar modelos y seleccionar el modelo del vehículo
        if (vehiculoData.mar_id) {
            await cargarOpcionesSelect('#mod_id', `../AJAX/vehiculos_ajax.php?accion=getModelos&marca_id=${vehiculoData.mar_id}`, 'modelos', vehiculoData.mod_id);
            $('#mod_id').prop('disabled', false);
        }

        // Una vez cargada la provincia de ubicación, cargar ciudades y seleccionar
        if (vehiculoData.veh_ubicacion_provincia) {
            await cargarOpcionesSelect('#veh_ubicacion_ciudad', `../AJAX/vehiculos_ajax.php?accion=getCiudades&provincia=${encodeURIComponent(vehiculoData.veh_ubicacion_provincia)}`, 'ciudades', vehiculoData.veh_ubicacion_ciudad);
            $('#veh_ubicacion_ciudad').prop('disabled', false);
        }
        
        // Poblar campos de texto y otros selects
        $('#veh_subtipo_vehiculo').val(vehiculoData.veh_subtipo_vehiculo);
        $('#veh_condicion').val(vehiculoData.veh_condicion).trigger('change'); // Trigger change para lógica de km/placa
        $('#veh_anio').val(vehiculoData.veh_anio);
        $('#veh_kilometraje').val(vehiculoData.veh_kilometraje);
        $('#veh_placa').val(vehiculoData.veh_placa);
        // Si veh_placa_provincia_origen ya fue cargado por selectsConfig, solo se re-selecciona si es necesario.
        // Si no, se puede cargar aquí específicamente si no estaba en selectsConfig.
        $('#veh_ultimo_digito_placa').val(vehiculoData.veh_ultimo_digito_placa);
        $('#veh_precio').val(parseFloat(vehiculoData.veh_precio).toFixed(2));
        $('#veh_vin').val(vehiculoData.veh_vin);
        
        // Formatear fecha para input type="date" (YYYY-MM-DD)
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

        // Poblar checkboxes de detalles_extra
        if (vehiculoData.veh_detalles_extra) {
            const extras = vehiculoData.veh_detalles_extra.split(',').map(extra => extra.trim());
            extras.forEach(extra => {
                $(`input[name="veh_detalles_extra[]"][value="${extra}"]`).prop('checked', true);
            });
        }
        // Lógica de visibilidad para campos de usado (similar a publicar_vehiculo.js)
        toggleCamposVehiculoUsado(vehiculoData.veh_condicion === 'usado');
    }

    // --- LÓGICA DE CARGA DE SELECTS (reutilizada de publicar_vehiculo.js, adaptada para async/await) ---
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
                    else { value = item; text = item; } // Provincias y Ciudades
                    select.append($('<option>', { value: value, text: text }));
                });
                if (valorSeleccionado) {
                    select.val(valorSeleccionado);
                }
                 // Habilitar el select si antes estaba deshabilitado (ej. modelos, ciudades)
                if (select.prop('disabled') && datos.length > 0) {
                    select.prop('disabled', false);
                } else if (datos.length === 0 && (selectId === '#mod_id' || selectId === '#veh_ubicacion_ciudad')) {
                     select.prop('disabled', true); // Mantener deshabilitado si no hay opciones
                }

            } else { throw new Error(response.message || `Error cargando ${tipoDato}`); }
        } catch (error) {
            console.error(`Error en cargarOpcionesSelect para ${selectId}:`, error);
            $(selectId).empty().append($('<option>', { value: '', text: `Error al cargar ${tipoDato}` })).prop('disabled', true);
        }
    }
    
    // Event listeners para selects dependientes (marcas -> modelos, provincias -> ciudades)
    $('#mar_id').change(function () {
        const marcaId = $(this).val();
        if (marcaId) {
            cargarOpcionesSelect('#mod_id', `../AJAX/vehiculos_ajax.php?accion=getModelos&marca_id=${marcaId}`, 'modelos');
            $('#mod_id').prop('disabled', false);
        } else {
            $('#mod_id').empty().append($('<option>', { value: '', text: 'Selecciona marca...' })).prop('disabled', true);
        }
    });

    $('#veh_ubicacion_provincia').change(function () {
        const provincia = $(this).val();
        if (provincia) {
            cargarOpcionesSelect('#veh_ubicacion_ciudad', `../AJAX/vehiculos_ajax.php?accion=getCiudades&provincia=${encodeURIComponent(provincia)}`, 'ciudades');
            $('#veh_ubicacion_ciudad').prop('disabled', false);
        } else {
            $('#veh_ubicacion_ciudad').empty().append($('<option>', { value: '', text: 'Selecciona provincia...' })).prop('disabled', true);
        }
    });
    
    // Lógica de visibilidad para campos de vehículo usado (similar a publicar_vehiculo.js)
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
            // Hacer que los campos de placa sean requeridos es opcional, depende de la lógica de negocio.
            // Por ahora, los mantenemos opcionales pero visibles.
            // placaInput.prop('required', true);
            // placaProvinciaSelect.prop('required', true);
            // ultimoDigitoSelect.prop('required', true);
        } else { // Nuevo
            kilometrajeDiv.show(); // Mantener visible pero no requerido y con valor 0
            kmInput.prop('required', false).val('0').attr('placeholder', '0 (Nuevo)');
            $('#label_kilometraje').html('Recorrido (km)');
            
            camposPlacaGroup.hide();
            placaInput.prop('required', false);
            placaProvinciaSelect.prop('required', false);
            ultimoDigitoSelect.prop('required', false);
        }
    }

    // --- MANEJO DE IMÁGENES ---
    function renderizarImagenesActuales() {
        currentImagesPreviewContainer.empty();
        if (imagenesActuales.length === 0) {
            currentImagesPreviewContainer.html('<small class="text-muted align-self-center mx-auto">Este vehículo no tiene imágenes actualmente.</small>');
            return;
        }

        imagenesActuales.forEach(img => {
            if (imagenesAEliminar.includes(img.ima_id)) return; // No renderizar si está marcada para eliminar

            // La URL de la imagen ya debería venir correcta del backend (ej: ../PUBLIC/uploads/...)
            // Si viene como PUBLIC/uploads/... , el ../ se añade aquí
            let imgUrlCorrected = img.ima_url;
            if (imgUrlCorrected && !imgUrlCorrected.startsWith('../')) {
                 imgUrlCorrected = '../' + imgUrlCorrected;
            }


            const wrapper = $(`
                <div class="img-preview-wrapper" data-id="${img.ima_id}">
                    <img src="${imgUrlCorrected}" alt="Imagen actual">
                    <button type="button" class="btn btn-danger btn-sm btn-remove-img" title="Eliminar esta imagen"><i class="bi bi-trash"></i></button>
                </div>
            `);
            
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
            imagenesAEliminar.push(String(imageId)); // Guardar como string para consistencia con hidden input
            $(this).closest('.img-preview-wrapper').hide(); // Ocultar en lugar de remover para posible "deshacer"
            // Si la imagen eliminada era la principal, resetear
            if (imagenPrincipalActualId == imageId) {
                imagenPrincipalActualId = null; 
                // Aquí se podría intentar marcar otra como principal automáticamente, o requerir al usuario.
            }
            $('#imagenes_a_eliminar_form').val(imagenesAEliminar.join(','));
            // Re-renderizar para actualizar botones de "principal" si es necesario
            // O simplemente ocultar el wrapper y manejar la lógica de principal al enviar.
             if ($('#currentImagesPreviewContainer .img-preview-wrapper:visible').length === 0) {
                currentImagesPreviewContainer.html('<small class="text-muted align-self-center mx-auto">Todas las imágenes han sido marcadas para eliminar. Añade nuevas imágenes.</small>');
            }
        }
    });

    currentImagesPreviewContainer.on('click', '.btn-set-principal', function () {
        const imageId = $(this).closest('.img-preview-wrapper').data('id');
        imagenPrincipalActualId = imageId;
        nuevaImagenPrincipalNombreTemporal = null; // Si se establece una actual como principal, anular nueva candidata
        
        // Actualizar UI
        $('#currentImagesPreviewContainer .img-preview-wrapper').removeClass('border-success border-2');
        $('#currentImagesPreviewContainer .badge-principal').remove();
        $('#currentImagesPreviewContainer .btn-set-principal').show();

        const wrapperSeleccionado = $(this).closest('.img-preview-wrapper');
        wrapperSeleccionado.addClass('border-success border-2');
        wrapperSeleccionado.find('.btn-set-principal').hide();
        wrapperSeleccionado.append('<span class="badge bg-success badge-principal">Principal</span>');
        
        // También desmarcar cualquier nueva imagen que se haya marcado como principal
        $('#newImagePreviewContainer .img-preview-wrapper').removeClass('border-primary border-2');
        $('#newImagePreviewContainer .badge-principal-nueva').remove();
        $('#newImagePreviewContainer .btn-set-nueva-principal').show();
    });

    // Manejo de nuevas imágenes (similar a publicar_vehiculo.js)
    $('#veh_imagenes_nuevas').on('change', function (event) {
        imagePreviewContainer.empty().html('<small class="text-muted align-self-center mx-auto">Previsualización de nuevas imágenes aparecerá aquí...</small>');
        uploadedFiles = Array.from(event.target.files);
        let hasProvisionalPrincipal = false; // Para marcar solo la primera nueva como principal provisionalmente

        if (uploadedFiles.length > 0) {
            imagePreviewContainer.empty(); // Limpiar mensaje "Previsualización..."
        }

        uploadedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const wrapper = $(`
                    <div class="img-preview-wrapper" data-filename="${file.name}">
                        <img src="${e.target.result}" alt="${file.name}">
                        <button type="button" class="btn btn-danger btn-sm btn-remove-new-img" title="Eliminar esta nueva imagen"><i class="bi bi-x-lg"></i></button>
                    </div>`);
                
                // Si no hay una imagen principal actual Y esta es la primera nueva imagen, marcarla provisionalmente.
                if (!imagenPrincipalActualId && !nuevaImagenPrincipalNombreTemporal && index === 0) {
                    wrapper.addClass('border-primary border-2'); // Estilo para nueva principal
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
        
        // Si la imagen eliminada era la principal provisional, resetear
        if (nuevaImagenPrincipalNombreTemporal === fileNameToRemove) {
            nuevaImagenPrincipalNombreTemporal = null;
            // Si quedan otras imágenes nuevas, marcar la primera de ellas como principal provisional
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
        // Actualizar el input file (recreándolo o limpiándolo) para reflejar los archivos eliminados
        const dt = new DataTransfer();
        uploadedFiles.forEach(file => dt.items.add(file));
        $('#veh_imagenes_nuevas')[0].files = dt.files;
    });

    imagePreviewContainer.on('click', '.btn-set-nueva-principal', function () {
        const fileName = $(this).closest('.img-preview-wrapper').data('filename');
        nuevaImagenPrincipalNombreTemporal = fileName;
        imagenPrincipalActualId = null; // Anular la principal actual si se elige una nueva

        // Actualizar UI para nuevas imágenes
        $('#newImagePreviewContainer .img-preview-wrapper').removeClass('border-primary border-2');
        $('#newImagePreviewContainer .badge-principal-nueva').remove();
        $('#newImagePreviewContainer .btn-set-nueva-principal').show();
        
        const wrapperSeleccionado = $(this).closest('.img-preview-wrapper');
        wrapperSeleccionado.addClass('border-primary border-2');
        wrapperSeleccionado.find('.btn-set-nueva-principal').hide();
        wrapperSeleccionado.append('<span class="badge bg-info badge-principal-nueva">Principal (Nueva)</span>');

        // Desmarcar cualquier imagen actual que fuera principal
        $('#currentImagesPreviewContainer .img-preview-wrapper').removeClass('border-success border-2');
        $('#currentImagesPreviewContainer .badge-principal').remove();
        $('#currentImagesPreviewContainer .btn-set-principal').show(); // Mostrar botón para marcar como principal en todas las actuales
    });


    // --- ENVÍO DEL FORMULARIO ---
    publicarVehiculoForm.on('submit', function (event) {
        event.preventDefault();
        event.stopPropagation();

        if (!this.checkValidity()) {
            $(this).addClass('was-validated');
            mostrarAlerta('warning', 'Por favor, corrige los errores en el formulario.');
            return;
        }
        $(this).addClass('was-validated');

        // Validar que haya al menos una imagen (ya sea actual no eliminada o nueva)
        const totalImagenesVisibles = $('#currentImagesPreviewContainer .img-preview-wrapper:visible').length + uploadedFiles.length;
        if (totalImagenesVisibles === 0) {
             mostrarAlerta('error', 'Debes tener al menos una imagen para el vehículo (ya sea existente o nueva).');
             return;
        }
        // Validar que haya una imagen principal seleccionada
        if (!imagenPrincipalActualId && !nuevaImagenPrincipalNombreTemporal) {
            mostrarAlerta('error', 'Debes seleccionar una imagen como principal.');
            return;
        }


        const formData = new FormData(this);
        formData.delete('veh_imagenes_nuevas[]'); // Eliminar el array original de nuevas imágenes
        uploadedFiles.forEach(file => { // Añadir las nuevas imágenes filtradas
            formData.append('veh_imagenes_nuevas[]', file);
        });
        
        // Añadir IDs de imágenes a eliminar y la nueva principal
        formData.set('imagenes_a_eliminar', imagenesAEliminar.join(','));
        formData.set('imagen_principal_actual_id', imagenPrincipalActualId || ''); // ID de la imagen existente marcada como principal
        formData.set('nueva_imagen_principal_nombre_temporal', nuevaImagenPrincipalNombreTemporal || ''); // Nombre de archivo de la nueva imagen marcada como principal

        mostrarOverlayCarga(true);

        $.ajax({
            url: '../AJAX/vehiculos_ajax.php', // Acción 'actualizarVehiculo' está en el FormData
            type: 'POST',
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.status === 'success') {
                    mostrarAlerta('success', response.message || 'Vehículo actualizado con éxito. Redirigiendo...', () => {
                        window.location.href = 'admin_vehiculos.php';
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


    // Función de alerta (asumimos que está en global.js o se define aquí)
    // function mostrarAlerta(tipo, mensaje, callback) { ... }
});
