$(document).ready(function() {
    const $listaVehiculosContainer = $('#listaVehiculosUsados');
    const $loadingVehiculos = $('#loadingVehiculosListado');
    const $noVehiculosMessage = $('#noVehiculosListadoMessage');
    const $paginacionContainer = $('#paginacionVehiculosUsados');
    const $conteoResultados = $('#conteoResultados');
    const $filtrosForm = $('#filtrosForm');
    const $filtroMarca = $('#filtro_mar_id');
    const $filtroModelo = $('#filtro_mod_id');
    const $filtroTipo = $('#filtro_tiv_id');
    const $filtroProvincia = $('#filtro_provincia');
    const $filtroAnioMin = $('#filtro_anio_min');
    const $filtroAnioMax = $('#filtro_anio_max');

    let currentPage = 1;
    const itemsPorPagina = 9; // Debe coincidir con el default en AJAX PHP

    function poblarSelect($selectElement, data, valueField, textField, defaultOptionText, placeholderValue = "") {
        $selectElement.empty().append($('<option>', { value: placeholderValue, text: defaultOptionText }));
        $.each(data, function(i, item) {
            $selectElement.append($('<option>', { value: item[valueField], text: item[textField] }));
        });
    }
    function poblarSelectSimple($selectElement, dataArray, defaultOptionText, placeholderValue = "") {
         $selectElement.empty().append($('<option>', { value: placeholderValue, text: defaultOptionText }));
        $.each(dataArray, function(i, item) {
            $selectElement.append($('<option>', { value: item, text: item }));
        });
    }

    function cargarFiltrosIniciales() {
        $.ajax({
            url: '../AJAX/vehiculos_ajax.php', type: 'GET', data: { accion: 'getCatalogos' }, dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    poblarSelect($filtroMarca, response.marcas, 'mar_id', 'mar_nombre', 'Todas las marcas');
                    poblarSelect($filtroTipo, response.tipos_vehiculo, 'tiv_id', 'tiv_nombre', 'Todos los tipos');
                    poblarSelectSimple($filtroProvincia, response.provincias, 'Todas las provincias');
                    
                    // Poblar años (ej: desde 1980 hasta actual+1)
                    const currentYear = new Date().getFullYear();
                    for (let year = currentYear + 1; year >= 1980; year--) {
                        $filtroAnioMin.append($('<option>', { value: year, text: year }));
                        $filtroAnioMax.append($('<option>', { value: year, text: year }));
                    }
                    $filtroAnioMin.val(''); // Resetear a 'Cualquiera'
                    $filtroAnioMax.val('');
                } else { console.error('Error al cargar catálogos para filtros:', response.message); }
            },
            error: function() { console.error('Error de conexión al cargar catálogos para filtros.'); }
        });
    }
    cargarFiltrosIniciales();

    $filtroMarca.on('change', function() {
        var marcaId = $(this).val();
        $filtroModelo.empty().append('<option value="">Cualquier modelo</option>').prop('disabled', true);
        if (marcaId) {
            $.ajax({
                url: '../AJAX/vehiculos_ajax.php', type: 'GET', data: { accion: 'getModelos', marca_id: marcaId }, dataType: 'json',
                success: function(response) {
                    if (response.status === 'success' && response.modelos && response.modelos.length > 0) {
                        poblarSelect($filtroModelo, response.modelos, 'mod_id', 'mod_nombre', 'Cualquier modelo');
                        $filtroModelo.prop('disabled', false);
                    } else { $filtroModelo.prop('disabled', true); }
                },
                error: function() { $filtroModelo.prop('disabled', true); }
            });
        }
    });


    function renderVehiculos(vehiculos) {
        $listaVehiculosContainer.empty();
        if (vehiculos && vehiculos.length > 0) {
            $.each(vehiculos, function(index, v) {
                let imagenUrl = v.imagen_principal_url ? v.imagen_principal_url : '../PUBLIC/Img/auto_placeholder.png';
                if (imagenUrl.startsWith('PUBLIC/')) { imagenUrl = '../' + imagenUrl; }

                const precioFormateado = v.veh_precio ? parseFloat(v.veh_precio).toLocaleString('es-EC', { style: 'currency', currency: 'USD' }) : 'Consultar';
                const kmFormateado = v.veh_kilometraje ? parseInt(v.veh_kilometraje).toLocaleString('es-EC') + ' km' : 'N/D';
                
                const cardHtml = `
                    <div class="col-sm-6 col-md-6 col-lg-4 mb-4">
                        <div class="card card-vehiculo h-100 shadow-sm">
                            <a href="detalle_vehiculo.php?id=${v.veh_id}" class="text-decoration-none">
                                <img src="${imagenUrl}" class="card-img-top card-vehiculo-img-top" alt="${v.mar_nombre} ${v.mod_nombre}">
                            </a>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><a href="detalle_vehiculo.php?id=${v.veh_id}" class="text-dark text-decoration-none">${v.mar_nombre} ${v.mod_nombre}</a></h5>
                                <p class="precio mb-2">${precioFormateado}</p>
                                <div class="caracteristicas-list mt-1">
                                    <p class="caracteristica-item mb-1"><i class="bi bi-calendar-event"></i> Año: ${v.veh_anio}</p>
                                    ${v.veh_condicion === 'usado' ? `<p class="caracteristica-item mb-1"><i class="bi bi-speedometer2"></i> ${kmFormateado}</p>` : ''}
                                    <p class="caracteristica-item mb-1"><i class="bi bi-geo-alt"></i> ${v.veh_ubicacion_ciudad || 'N/D'}, ${v.veh_ubicacion_provincia || 'N/D'}</p>
                                </div>
                                <a href="detalle_vehiculo.php?id=${v.veh_id}" class="btn btn-primary mt-auto w-100 view-details-btn"><i class="bi bi-search me-2"></i>Ver Detalles</a>
                            </div>
                        </div>
                    </div>`;
                $listaVehiculosContainer.append(cardHtml);
            });
        }
    }

    function renderPaginacion(paginaActual, totalPaginas) {
        $paginacionContainer.empty();
        if (totalPaginas <= 1) return;

        let ul = $('<ul class="pagination"></ul>');
        
        // Botón Anterior
        ul.append(`<li class="page-item ${paginaActual === 1 ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${paginaActual - 1}">Anterior</a></li>`);

        // Números de página (lógica simplificada, se puede mejorar para muchos números)
        let inicio = Math.max(1, paginaActual - 2);
        let fin = Math.min(totalPaginas, paginaActual + 2);

        if (inicio > 1) {
            ul.append('<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>');
            if (inicio > 2) ul.append('<li class="page-item disabled"><span class="page-link">...</span></li>');
        }

        for (let i = inicio; i <= fin; i++) {
            ul.append(`<li class="page-item ${i === paginaActual ? 'active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`);
        }

        if (fin < totalPaginas) {
            if (fin < totalPaginas - 1) ul.append('<li class="page-item disabled"><span class="page-link">...</span></li>');
            ul.append(`<li class="page-item"><a class="page-link" href="#" data-page="${totalPaginas}">${totalPaginas}</a></li>`);
        }

        // Botón Siguiente
        ul.append(`<li class="page-item ${paginaActual === totalPaginas ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${paginaActual + 1}">Siguiente</a></li>`);
        
        $paginacionContainer.append(ul);
    }

    function cargarVehiculos(page = 1, filtrosData = {}) {
        $loadingVehiculos.show();
        $noVehiculosMessage.hide();
        $listaVehiculosContainer.html(''); // Limpiar resultados anteriores
        $paginacionContainer.empty();

        const dataToSend = {
            accion: 'getVehiculosListado',
            condicion: 'usado', // Siempre 'usado' para esta página
            pagina: page,
            items_por_pagina: itemsPorPagina,
            ...filtrosData // Fusionar con filtros del formulario
        };
        // Limpiar filtros vacíos
        for (const key in dataToSend) {
            if (dataToSend[key] === '' || dataToSend[key] === null) {
                delete dataToSend[key];
            }
        }

        $.ajax({
            url: '../AJAX/vehiculos_ajax.php',
            type: 'GET',
            data: dataToSend,
            dataType: 'json',
            success: function(response) {
                $loadingVehiculos.hide();
                if (response.status === 'success') {
                    if (response.vehiculos && response.vehiculos.length > 0) {
                        renderVehiculos(response.vehiculos);
                        renderPaginacion(response.pagina_actual, response.total_paginas);
                        const offset = (response.pagina_actual - 1) * response.items_por_pagina;
                        $conteoResultados.text(`Mostrando ${offset + 1} - ${offset + response.vehiculos.length} de ${response.total_vehiculos} vehículos usados.`);
                    } else {
                        $noVehiculosMessage.show();
                        $conteoResultados.text('Mostrando 0 de 0 vehículos usados.');
                    }
                } else {
                    $listaVehiculosContainer.html('<div class="col-12 alert alert-danger">Error: ' + (response.message || 'No se pudieron cargar los vehículos.') + '</div>');
                    $conteoResultados.text('Error al cargar.');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $loadingVehiculos.hide();
                $listaVehiculosContainer.html('<div class="col-12 alert alert-danger">Error de conexión. Intenta de nuevo.</div>');
                 console.error("AJAX Error en cargarVehiculos:", jqXHR.responseText, textStatus, errorThrown);
                 $conteoResultados.text('Error de conexión.');
            }
        });
    }

    // Carga inicial
    cargarVehiculos(currentPage);

    // Manejar envío de filtros
    $filtrosForm.on('submit', function(e) {
        e.preventDefault();
        currentPage = 1; // Resetear a la primera página con nuevos filtros
        const filtrosData = $(this).serializeObject(); // Necesitas una función helper o cambiar a serializeArray
        cargarVehiculos(currentPage, filtrosData);
    });

    // Helper para convertir form data a objeto (si no usas serializeArray y procesas)
    $.fn.serializeObject = function() {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function() {
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };
    
    // Limpiar filtros
    $('#resetFiltrosBtn').on('click', function() {
        $filtrosForm[0].reset();
        $filtroModelo.empty().append('<option value="">Selecciona marca primero</option>').prop('disabled', true); // Resetear modelo
        currentPage = 1;
        cargarVehiculos(currentPage);
    });
    
    $('#verTodosLink').on('click', function(e) { // Para el link en mensaje de no resultados
        e.preventDefault();
        $('#resetFiltrosBtn').click();
    });

    // Manejar clics en paginación
    $paginacionContainer.on('click', 'a.page-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (page && page !== currentPage) {
            currentPage = parseInt(page);
            cargarVehiculos(currentPage, $filtrosForm.serializeObject());
        }
    });

});