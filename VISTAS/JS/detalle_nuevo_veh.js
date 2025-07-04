$(document).ready(function() {
    const urlParams = new URLSearchParams(window.location.search);
    const vehId = urlParams.get('id');

    const pageLoader = $('#page-loader');
    const detalleVehiculoLoader = $('#detalleVehiculoLoader');
    const detalleVehiculoContent = $('#detalleVehiculoContent');
    const errorVehiculo = $('#errorVehiculo');

    // Elementos de la galería
    const imagenPrincipal = $('#imagenPrincipalVehiculo');
    const galeriaThumbnails = $('#galeriaThumbnails');
    const btnPrevImagen = $('#btnPrevImagen');
    const btnNextImagen = $('#btnNextImagen');
    let imagenesVehiculo = [];
    let imagenActualIdx = 0;

    // Elementos de información del vehículo
    const nombreVehiculoEl = $('#nombreVehiculo');
    const precioVehiculoEl = $('#precioVehiculo');
    const listaDetallesVehiculoEl = $('#listaDetallesVehiculo');
    const descripcionVehiculoEl = $('#descripcionVehiculo');

    // Formulario de cotización
    const formCotizacion = $('#formCotizacionNuevo');
    const cotVehIdInput = $('#cot_veh_id');
    const cotizacionSpinner = $('#cotizacionSpinner');
    const resumenCotizacionSection = $('#resumenCotizacionSection');
    const resumenCotizacionBody = $('#resumenCotizacionBody');


    if (!vehId) {
        mostrarErrorCarga("ID de vehículo no proporcionado.");
        return;
    }

    cotVehIdInput.val(vehId); // Asignar el ID del vehículo al campo oculto del formulario

    cargarDetallesVehiculo();

    function mostrarErrorCarga(mensaje) {
        pageLoader.hide();
        detalleVehiculoLoader.hide();
        detalleVehiculoContent.hide();
        errorVehiculo.find('p').text(mensaje || "No pudimos encontrar los detalles para el vehículo solicitado.");
        errorVehiculo.fadeIn();
    }

    function cargarDetallesVehiculo() {
        $.ajax({
            url: '../AJAX/vehiculos_ajax.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'obtener_detalle_vehiculo_nuevo', // Acción específica para autos nuevos
                veh_id: vehId
            },
            beforeSend: function() {
                detalleVehiculoLoader.show();
                detalleVehiculoContent.hide();
                errorVehiculo.hide();
            },
            success: function(response) {
                if (response.success && response.vehiculo) {
                    mostrarDetalles(response.vehiculo, response.imagenes);
                    imagenesVehiculo = response.imagenes || [];
                    if (imagenesVehiculo.length > 0) {
                        actualizarGaleria();
                    } else {
                        imagenPrincipal.attr('src', '../PUBLIC/Img/Auto_Mercado_Total_LOGO4_SIN_FONDO.png'); // Placeholder
                        btnPrevImagen.hide();
                        btnNextImagen.hide();
                    }
                    detalleVehiculoContent.fadeIn();
                } else {
                    mostrarErrorCarga(response.message || "No se pudo cargar la información del vehículo.");
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Error AJAX:", textStatus, errorThrown, jqXHR.responseText);
                mostrarErrorCarga("Error al conectar con el servidor. Por favor, intente más tarde.");
            },
            complete: function() {
                detalleVehiculoLoader.hide();
                // pageLoader se oculta en global.js después de que todo carga
            }
        });
    }

    function mostrarDetalles(vehiculo, imagenes) {
        nombreVehiculoEl.text(`${vehiculo.mar_nombre} ${vehiculo.mod_nombre} ${vehiculo.veh_anio}`);
        precioVehiculoEl.text(`Desde ${parseFloat(vehiculo.veh_precio).toLocaleString('es-ES', { style: 'currency', currency: 'USD' })}`);

        // Llenar lista de detalles básicos
        listaDetallesVehiculoEl.empty(); // Limpiar por si acaso
        listaDetallesVehiculoEl.append(`<li><strong>Marca:</strong> ${vehiculo.mar_nombre}</li>`);
        listaDetallesVehiculoEl.append(`<li><strong>Modelo:</strong> ${vehiculo.mod_nombre}</li>`);
        listaDetallesVehiculoEl.append(`<li><strong>Año:</strong> ${vehiculo.veh_anio}</li>`);
        listaDetallesVehiculoEl.append(`<li><strong>Condición:</strong> <span class="badge bg-success text-uppercase">${vehiculo.veh_condicion}</span></li>`);
        if (vehiculo.tiv_nombre) {
            listaDetallesVehiculoEl.append(`<li><strong>Tipo:</strong> ${vehiculo.tiv_nombre}</li>`);
        }
        if (vehiculo.veh_color_exterior) {
             listaDetallesVehiculoEl.append(`<li><strong>Color Exterior:</strong> ${vehiculo.veh_color_exterior}</li>`);
        }
        if (vehiculo.veh_tipo_transmision) {
             listaDetallesVehiculoEl.append(`<li><strong>Transmisión Base:</strong> ${vehiculo.veh_tipo_transmision}</li>`);
        }
         if (vehiculo.veh_tipo_combustible) {
             listaDetallesVehiculoEl.append(`<li><strong>Combustible Base:</strong> ${vehiculo.veh_tipo_combustible}</li>`);
        }
        if (vehiculo.veh_detalles_motor) {
             listaDetallesVehiculoEl.append(`<li><strong>Motor Base:</strong> ${vehiculo.veh_detalles_motor}</li>`);
        }
        // Puedes añadir más detalles aquí según la estructura de 'vehiculo'

        descripcionVehiculoEl.html(vehiculo.veh_descripcion || '<p>No hay descripción detallada disponible para este vehículo.</p>');
        
        // Llenar campos del formulario con datos base si es pertinente
        $('#cot_motorizacion').attr('placeholder', `Ej: ${vehiculo.veh_detalles_motor || '2.0L Turbo'}`);
        if (vehiculo.veh_color_exterior) {
            $('#cot_color').attr('placeholder', `Ej: ${vehiculo.veh_color_exterior}`);
        }
        if (vehiculo.veh_tipo_transmision) {
            // Seleccionar la transmisión base si coincide
            $('#cot_transmision option').each(function() {
                if ($(this).text().toLowerCase() === vehiculo.veh_tipo_transmision.toLowerCase()) {
                    $(this).prop('selected', true);
                }
            });
        }
    }

    // --- Lógica de la Galería ---
    function actualizarGaleria() {
        if (imagenesVehiculo.length === 0) {
            imagenPrincipal.attr('src', '../PUBLIC/Img/Auto_Mercado_Total_LOGO4_SIN_FONDO.png').attr('alt', 'Imagen no disponible');
            galeriaThumbnails.html('<p class="text-muted-luxury small">No hay imágenes adicionales.</p>');
            btnPrevImagen.hide();
            btnNextImagen.hide();
            return;
        }

        const imgPath = `../PUBLIC/uploads/vehiculos/${vehId}/`;
        imagenPrincipal.attr('src', imgPath + imagenesVehiculo[imagenActualIdx].ima_url).attr('alt', `Imagen ${imagenActualIdx + 1} de ${nombreVehiculoEl.text()}`);
        
        galeriaThumbnails.empty();
        imagenesVehiculo.forEach((img, index) => {
            galeriaThumbnails.append(
                `<img src="${imgPath + img.ima_url}" 
                      alt="Thumbnail ${index + 1}" 
                      class="img-thumbnail ${index === imagenActualIdx ? 'active-thumbnail' : ''}" 
                      data-index="${index}">`
            );
        });

        btnPrevImagen.toggle(imagenesVehiculo.length > 1);
        btnNextImagen.toggle(imagenesVehiculo.length > 1);
    }

    galeriaThumbnails.on('click', 'img', function() {
        imagenActualIdx = $(this).data('index');
        actualizarGaleria();
    });

    btnPrevImagen.on('click', function() {
        imagenActualIdx = (imagenActualIdx - 1 + imagenesVehiculo.length) % imagenesVehiculo.length;
        actualizarGaleria();
    });

    btnNextImagen.on('click', function() {
        imagenActualIdx = (imagenActualIdx + 1) % imagenesVehiculo.length;
        actualizarGaleria();
    });


    // --- Lógica del Formulario de Cotización ---
    formCotizacion.on('submit', function(e) {
        e.preventDefault();
        if (!$(this)[0].checkValidity()) {
            $(this)[0].reportValidity();
            return;
        }

        const formData = $(this).serializeArray();
        let detallesCotizacion = {};
        let mensajeAdicional = "";
        let cedula = "";

        formData.forEach(item => {
            if (item.name === "cot_mensaje_adicional") {
                mensajeAdicional = item.value;
            } else if (item.name === "cot_cedula") {
                cedula = item.value;
            } else if (item.name !== "cot_veh_id") {
                detallesCotizacion[item.name.replace('cot_', '')] = item.value;
            }
        });
        
        // Crear el objeto para cot_detalles_vehiculo_solicitado
        const cot_detalles_vehiculo_solicitado = JSON.stringify({
            version: detallesCotizacion.version,
            transmision: detallesCotizacion.transmision,
            motorizacion: detallesCotizacion.motorizacion,
            color: detallesCotizacion.color,
            accesorios: detallesCotizacion.accesorios,
            // Se podría añadir la fuente de ingresos aquí si se quiere en el JSON principal
            // fuente_ingresos: detallesCotizacion.comprobantes_ingresos 
        });

        // El campo cot_mensaje podría usarse para la cédula y la fuente de ingresos,
        // o información más general. Aquí combinamos cédula y el mensaje del usuario.
        const cot_mensaje = `Cédula: ${cedula}. Fuente de Ingresos: ${detallesCotizacion.comprobantes_ingresos}. Mensaje Adicional: ${mensajeAdicional}`;

        const dataToSend = {
            action: 'registrar_cotizacion_nuevo', // Nueva acción para cotización de nuevos
            veh_id: vehId,
            // El ID de usuario se tomará de la sesión en el backend
            // usu_id_solicitante: (obtenido de la sesión PHP),
            cot_detalles_vehiculo_solicitado: cot_detalles_vehiculo_solicitado,
            cot_mensaje: cot_mensaje 
        };
        
        $.ajax({
            url: '../AJAX/cotizaciones_ajax.php',
            type: 'POST',
            dataType: 'json',
            data: dataToSend,
            beforeSend: function() {
                cotizacionSpinner.show();
                formCotizacion.find('button[type="submit"]').prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Solicitud Enviada',
                        text: response.message || 'Su solicitud de cotización ha sido enviada. Un asesor se pondrá en contacto con usted pronto.',
                        confirmButtonColor: 'var(--luxury-gold)'
                    });
                    formCotizacion[0].reset();
                    // Mostrar un resumen básico si se desea
                    mostrarResumenEstimado(response.vehiculo_base_precio); 
                    // Podrías ocultar el formulario y mostrar un mensaje de agradecimiento más permanente
                     $('#collapseCotizacion').collapse('hide');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al Enviar',
                        text: response.message || 'No se pudo enviar su solicitud. Por favor, intente de nuevo.',
                        confirmButtonColor: 'var(--luxury-gold)'
                    });
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Error AJAX cotización:", textStatus, errorThrown, jqXHR.responseText);
                 Swal.fire({
                    icon: 'error',
                    title: 'Error de Conexión',
                    text: 'Hubo un problema al conectar con el servidor. Por favor, intente más tarde.',
                    confirmButtonColor: 'var(--luxury-gold)'
                });
            },
            complete: function() {
                cotizacionSpinner.hide();
                formCotizacion.find('button[type="submit"]').prop('disabled', false);
            }
        });
    });

    function mostrarResumenEstimado(precioBase) {
        if (!precioBase && !$('#precioVehiculo').text().includes('Desde')) return; // Si no hay precio base, no mostrar resumen

        let precioVehiculoNumerico;
        if (precioBase) {
            precioVehiculoNumerico = parseFloat(precioBase);
        } else {
            const precioTexto = $('#precioVehiculo').text().replace('Desde ', '').replace('USD', '').replace(/\./g, '').replace(',', '.').trim();
            precioVehiculoNumerico = parseFloat(precioTexto);
        }


        if (isNaN(precioVehiculoNumerico)) return;

        const ivaPorcentaje = 0.15; // Asumiendo 15% IVA
        const ivaCalculado = precioVehiculoNumerico * ivaPorcentaje;
        const totalContado = precioVehiculoNumerico + ivaCalculado;

        const cuotaInicialPorcentaje = 0.30; // Ejemplo 30%
        const cuotaInicialCalculada = totalContado * cuotaInicialPorcentaje;
        
        // Estimación simple de cuota mensual (esto es muy básico y no real)
        const montoAFinanciar = totalContado - cuotaInicialCalculada;
        // Tasa de interés anual de ejemplo (ej. 16%) y plazo en meses (ej. 60)
        // Para un cálculo real se necesita una fórmula de amortización financiera.
        // Esto es solo una placeholder muy simplificado:
        const tasaInteresMensualEjemplo = (0.16 / 12); 
        const numCuotasEjemplo = 60;
        // Formula simplificada (no financieramente precisa para cuotas fijas con interés compuesto):
        // let cuotaMensualEstimada = montoAFinanciar / numCuotasEjemplo; 
        // cuotaMensualEstimada = cuotaMensualEstimada * (1 + tasaInteresMensualEjemplo * (numCuotasEjemplo/24) ); // Ajuste muy burdo

        // Usando la fórmula correcta para cuota fija (anualidad)
        // C = P * [i(1+i)^n] / [(1+i)^n - 1]
        // donde P = monto a financiar, i = tasa mensual, n = numero de cuotas
        let cuotaMensualEstimada;
        if (tasaInteresMensualEjemplo > 0) {
             cuotaMensualEstimada = montoAFinanciar * (tasaInteresMensualEjemplo * Math.pow(1 + tasaInteresMensualEjemplo, numCuotasEjemplo)) / (Math.pow(1 + tasaInteresMensualEjemplo, numCuotasEjemplo) - 1);
        } else { // Si no hay interés (o es 0)
            cuotaMensualEstimada = montoAFinanciar / numCuotasEjemplo;
        }


        $('#res_precio_base').text(precioVehiculoNumerico.toLocaleString('es-ES', { style: 'currency', currency: 'USD' }));
        $('#res_iva').text(ivaCalculado.toLocaleString('es-ES', { style: 'currency', currency: 'USD' }));
        $('#res_total_contado').text(totalContado.toLocaleString('es-ES', { style: 'currency', currency: 'USD' }));
        $('#res_cuota_inicial').text(cuotaInicialCalculada.toLocaleString('es-ES', { style: 'currency', currency: 'USD' }));
        $('#res_cuota_mensual').text(cuotaMensualEstimada.toLocaleString('es-ES', { style: 'currency', currency: 'USD' }));

        resumenCotizacionSection.slideDown();
        // Abrir el acordeón del resumen
        const collapseElement = document.getElementById('collapseResumenCotizacion');
        if (collapseElement) {
             const bsCollapse = new bootstrap.Collapse(collapseElement, {
                 show: true
             });
        }
    }

    // Ocultar loader principal de la página cuando todo esté listo
    // Esto se maneja en global.js, pero nos aseguramos que el contenido específico esté visible
    $(window).on('load', function() {
        // Adicionalmente, si todo cargó bien, page-loader se oculta en global.js
        // Si hubo error, errorVehiculo ya está visible y pageLoader oculto.
    });

});
