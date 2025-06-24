$(document).ready(function() {
    const $listaVehiculosContainer = $('#listaVehiculosUsados');
    const $loadingVehiculos = $('#loadingVehiculosListado');
    const $noVehiculosMessage = $('#noVehiculosListadoMessage');
    const $paginacionContainer = $('#paginacionVehiculosUsados');
    const $conteoResultados = $('#conteoResultados');
    
    const $filtrosFormDesktop = $('#filtrosForm');
    const $filtrosMobileBody = $('#filtrosMobileBody');
    let $filtrosFormMobile; 

    const $filtroMarcaDesktop = $('#filtro_mar_id');
    const $filtroModeloDesktop = $('#filtro_mod_id');
    const $filtroTipoDesktop = $('#filtro_tiv_id');
    const $filtroProvinciaDesktop = $('#filtro_provincia');
    const $filtroAnioMinDesktop = $('#filtro_anio_min');
    const $filtroAnioMaxDesktop = $('#filtro_anio_max');

    let currentPage = 1;
    const itemsPorPagina = 9;

    function poblarSelect($selectElement, data, valueField, textField, defaultOptionText, placeholderValue = "") {
        $selectElement.empty().append($('<option>', { value: placeholderValue, text: defaultOptionText }));
        if (data && data.length > 0) {
            $.each(data, function(i, item) {
                $selectElement.append($('<option>', { value: item[valueField], text: item[textField] }));
            });
        }
    }
    function poblarSelectSimple($selectElement, dataArray, defaultOptionText, placeholderValue = "") {
         $selectElement.empty().append($('<option>', { value: placeholderValue, text: defaultOptionText }));
        if (dataArray && dataArray.length > 0) {
            $.each(dataArray, function(i, item) {
                $selectElement.append($('<option>', { value: item, text: item }));
            });
        }
    }

    function poblarAnios($selectAnioMin, $selectAnioMax) {
        const currentYear = new Date().getFullYear();
        $selectAnioMin.append($('<option>', { value: '', text: 'Cualquiera' }));
        $selectAnioMax.append($('<option>', { value: '', text: 'Cualquiera' }));
        for (let year = currentYear + 1; year >= 1980; year--) {
            $selectAnioMin.append($('<option>', { value: year, text: year }));
            $selectAnioMax.append($('<option>', { value: year, text: year }));
        }
        $selectAnioMin.val('');
        $selectAnioMax.val('');
    }

    function cargarFiltrosIniciales() {
        $.ajax({
            url: '../AJAX/vehiculos_ajax.php', type: 'GET', data: { accion: 'getCatalogos' }, dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    poblarSelect($filtroMarcaDesktop, response.marcas, 'mar_id', 'mar_nombre', 'Todas las marcas');
                    poblarSelect($filtroTipoDesktop, response.tipos_vehiculo, 'tiv_id', 'tiv_nombre', 'Todos los tipos');
                    poblarSelectSimple($filtroProvinciaDesktop, response.provincias, 'Todas las provincias');
                    poblarAnios($filtroAnioMinDesktop, $filtroAnioMaxDesktop);
                    clonarFiltrosParaMovil();
                } else { console.error('Error al cargar catálogos para filtros:', response.message); }
            },
            error: function(jqXHR, textStatus, errorThrown) { 
                console.error('Error de conexión al cargar catálogos para filtros:', textStatus, errorThrown, jqXHR.responseText); 
            }
        });
    }
    
    function clonarFiltrosParaMovil() {
        if ($filtrosFormDesktop.length && $filtrosMobileBody.length) {
            $filtrosMobileBody.empty().append($filtrosFormDesktop.clone().attr('id', 'filtrosFormMobile'));
            $filtrosFormMobile = $('#filtrosFormMobile');

            $filtrosFormMobile.find('#filtro_mar_id').on('change', function() {
                var marcaId = $(this).val();
                var $selectModeloClon = $filtrosFormMobile.find('#filtro_mod_id');
                $selectModeloClon.empty().append('<option value="">Cualquier modelo</option>').prop('disabled', true);
                if (marcaId) {
                    $.ajax({
                        url: '../AJAX/vehiculos_ajax.php', type: 'GET', data: { accion: 'getModelos', marca_id: marcaId }, dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success' && response.modelos && response.modelos.length > 0) {
                                poblarSelect($selectModeloClon, response.modelos, 'mod_id', 'mod_nombre', 'Cualquier modelo');
                                $selectModeloClon.prop('disabled', false);
                            } else { $selectModeloClon.prop('disabled', true); }
                        },
                        error: function() { $selectModeloClon.prop('disabled', true); }
                    });
                }
            });

            $filtrosFormMobile.on('submit', function(e) {
                e.preventDefault(); currentPage = 1;
                const filtrosData = $(this).serializeObject();
                cargarVehiculos(currentPage, filtrosData);
                var offcanvasElement = document.getElementById('filtrosOffcanvas');
                if (offcanvasElement) {
                    var offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvasElement) || new bootstrap.Offcanvas(offcanvasElement);
                    offcanvasInstance.hide();
                }
            });
            $filtrosFormMobile.find('button[type="reset"]').on('click', function() { // Asumiendo type="reset"
                setTimeout(function() { // Dejar que el reset nativo ocurra primero
                    $filtrosFormMobile.find('#filtro_mod_id').empty().append('<option value="">Selecciona marca</option>').prop('disabled', true);
                    currentPage = 1; cargarVehiculos(currentPage);
                     var offcanvasElement = document.getElementById('filtrosOffcanvas');
                    if (offcanvasElement) {
                        var offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvasElement) || new bootstrap.Offcanvas(offcanvasElement);
                        offcanvasInstance.hide();
                    }
                }, 0);
            });
        }
    }
    cargarFiltrosIniciales();

    $filtroMarcaDesktop.on('change', function() {
        var marcaId = $(this).val();
        $filtroModeloDesktop.empty().append('<option value="">Cualquier modelo</option>').prop('disabled', true);
        if (marcaId) {
            $.ajax({
                url: '../AJAX/vehiculos_ajax.php', type: 'GET', data: { accion: 'getModelos', marca_id: marcaId }, dataType: 'json',
                success: function(response) {
                    if (response.status === 'success' && response.modelos && response.modelos.length > 0) {
                        poblarSelect($filtroModeloDesktop, response.modelos, 'mod_id', 'mod_nombre', 'Cualquier modelo');
                        $filtroModeloDesktop.prop('disabled', false);
                    } else { $filtroModeloDesktop.prop('disabled', true); }
                },
                error: function() { $filtroModeloDesktop.prop('disabled', true); }
            });
        }
    });

    function renderVehiculos(vehiculos) {
        $listaVehiculosContainer.empty();
        if (vehiculos && vehiculos.length > 0) {
            $.each(vehiculos, function(index, v) {
                let imagenUrl = v.imagen_principal_url ? v.imagen_principal_url : '../PUBLIC/Img/auto_placeholder.png';
                if (imagenUrl && imagenUrl.startsWith('PUBLIC/')) { imagenUrl = '../' + imagenUrl; }
                const precioFormateado = v.veh_precio ? parseFloat(v.veh_precio).toLocaleString('es-EC', { style: 'currency', currency: 'USD' }) : 'Consultar';
                const kmFormateado = v.veh_kilometraje ? parseInt(v.veh_kilometraje).toLocaleString('es-EC') + ' km' : 'N/D';
                
                const cardHtml = `
                    <div class="col-sm-6 col-lg-4 mb-4 animate-on-scroll">
                        <div class="card card-vehiculo h-100">
                            <a href="detalle_vehiculo.php?id=${v.veh_id}" class="text-decoration-none d-block">
                                <img src="${$('<div>').text(imagenUrl).html()}" class="card-img-top card-vehiculo-img-top" alt="${$('<div>').text(v.mar_nombre + ' ' + v.mod_nombre).html()}">
                            </a>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title mb-1"><a href="detalle_vehiculo.php?id=${v.veh_id}" class="text-decoration-none">${$('<div>').text(v.mar_nombre + ' ' + v.mod_nombre).html()}</a></h5>
                                <p class="text-muted small mb-2">${$('<div>').text(v.veh_anio).html()} - ${$('<div>').text(v.tiv_nombre).html()}</p>
                                <p class="precio mb-2">${precioFormateado}</p>
                                <div class="caracteristicas-list mt-1 small">
                                    ${v.veh_condicion === 'usado' && v.veh_kilometraje !== null ? `<p class="caracteristica-item mb-1"><i class="bi bi-speedometer2"></i> ${kmFormateado}</p>` : '<p class="caracteristica-item mb-1"><i class="bi bi-stars text-warning"></i> Nuevo (0km)</p>'}
                                    <p class="caracteristica-item mb-0"><i class="bi bi-geo-alt"></i> ${$('<div>').text(v.veh_ubicacion_ciudad || 'N/D').html()}, ${$('<div>').text(v.veh_ubicacion_provincia || 'N/D').html()}</p>
                                </div>
                                <a href="detalle_vehiculo.php?id=${v.veh_id}" class="btn btn-primary mt-auto w-100 btn-ver-detalles"><i class="bi bi-search me-2"></i>Ver Detalles</a>
                            </div>
                        </div>
                    </div>`;
                $listaVehiculosContainer.append(cardHtml);
            });
            simpleAnimateOnScroll();
        }
    }

    function renderPaginacion(paginaActual, totalPaginas) {
        $paginacionContainer.empty(); if (totalPaginas <= 1) return;
        let ul = $('<ul class="pagination"></ul>');
        ul.append(`<li class="page-item ${paginaActual === 1 ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${paginaActual - 1}">Anterior</a></li>`);
        let inicio = Math.max(1, paginaActual - 2); let fin = Math.min(totalPaginas, paginaActual + 2);
        if (inicio > 1) { ul.append('<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>'); if (inicio > 2) ul.append('<li class="page-item disabled"><span class="page-link">...</span></li>'); }
        for (let i = inicio; i <= fin; i++) { ul.append(`<li class="page-item ${i === paginaActual ? 'active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`); }
        if (fin < totalPaginas) { if (fin < totalPaginas - 1) ul.append('<li class="page-item disabled"><span class="page-link">...</span></li>'); ul.append(`<li class="page-item"><a class="page-link" href="#" data-page="${totalPaginas}">${totalPaginas}</a></li>`); }
        ul.append(`<li class="page-item ${paginaActual === totalPaginas ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${paginaActual + 1}">Siguiente</a></li>`);
        $paginacionContainer.append(ul);
    }

    function cargarVehiculos(page = 1, filtrosData = {}) {
        $loadingVehiculos.show(); $noVehiculosMessage.hide();
        $listaVehiculosContainer.addClass('opacity-50'); 
        const dataToSend = { accion: 'getVehiculosListado', condicion: 'usado', pagina: page, items_por_pagina: itemsPorPagina, ...filtrosData };
        for (const key in dataToSend) { if (dataToSend[key] === '' || dataToSend[key] === null) { delete dataToSend[key]; } }

        $.ajax({
            url: '../AJAX/vehiculos_ajax.php', type: 'GET', data: dataToSend, dataType: 'json',
            success: function(response) {
                $loadingVehiculos.hide(); $listaVehiculosContainer.removeClass('opacity-50');
                if (response.status === 'success') {
                    if (response.vehiculos && response.vehiculos.length > 0) {
                        renderVehiculos(response.vehiculos); renderPaginacion(response.pagina_actual, response.total_paginas);
                        const offset = (response.pagina_actual - 1) * response.items_por_pagina;
                        $conteoResultados.html(`<i class="bi bi-car-front me-2"></i>Mostrando <b>${offset + 1} - ${offset + response.vehiculos.length}</b> de <b>${response.total_vehiculos}</b> vehículos`);
                    } else { $noVehiculosMessage.show(); $conteoResultados.html('<i class="bi bi-car-front me-2"></i>Mostrando 0 de 0 vehículos'); $paginacionContainer.empty(); }
                } else { $listaVehiculosContainer.html('<div class="col-12 alert alert-danger">Error: ' + (response.message || 'No se pudieron cargar.') + '</div>'); $conteoResultados.text('Error.'); }
            },
            error: function(jqXHR, textStatus, errorThrown) { 
                $loadingVehiculos.hide(); $listaVehiculosContainer.removeClass('opacity-50').html('<div class="col-12 alert alert-danger">Error de conexión. Intenta de nuevo.</div>'); 
                console.error("AJAX Error en cargarVehiculos:", jqXHR.responseText, textStatus, errorThrown);
                $conteoResultados.text('Error de conexión.');
            }
        });
    }
    cargarVehiculos(currentPage);

    $filtrosFormDesktop.on('submit', function(e) {
        e.preventDefault(); currentPage = 1; 
        const filtrosData = $(this).serializeObject();
        cargarVehiculos(currentPage, filtrosData);
    });
    
    $('body').on('submit', '#filtrosFormMobile', function(e) {
        e.preventDefault(); currentPage = 1;
        const filtrosData = $(this).serializeObject();
        cargarVehiculos(currentPage, filtrosData);
        var offcanvasElement = document.getElementById('filtrosOffcanvas');
        if(offcanvasElement) {
            var offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvasElement) || new bootstrap.Offcanvas(offcanvasElement);
            offcanvasInstance.hide();
        }
    });

    $.fn.serializeObject = function() {
        var o = {}; var a = this.serializeArray();
        $.each(a, function() {
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) { o[this.name] = [o[this.name]]; }
                o[this.name].push(this.value || '');
            } else { o[this.name] = this.value || ''; }
        });
        return o;
    };
    
    function resetAllFilters() {
        currentPage = 1;
        if ($filtrosFormDesktop.length) $filtrosFormDesktop[0].reset();
        if ($filtrosFormMobile && $filtrosFormMobile.length) $filtrosFormMobile[0].reset();
        
        $('#filtro_mod_id, #filtrosFormMobile #filtro_mod_id').empty().append('<option value="">Selecciona marca</option>').prop('disabled', true);
        cargarVehiculos(currentPage);
    }

    $('#resetFiltrosBtn').on('click', function() { resetAllFilters(); });
    $('body').on('click', '#filtrosFormMobile button[type="reset"]', function() { 
        resetAllFilters(); 
        var offcanvasElement = document.getElementById('filtrosOffcanvas');
        if(offcanvasElement) {
            var offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvasElement) || new bootstrap.Offcanvas(offcanvasElement);
            offcanvasInstance.hide();
        }
    });
    
    $('#verTodosLink').on('click', function(e) { e.preventDefault(); resetAllFilters(); });

    $paginacionContainer.on('click', 'a.page-link', function(e) {
        e.preventDefault(); const page = $(this).data('page');
        if (page && page !== currentPage) { 
            currentPage = parseInt(page); 
            const currentFilters = $filtrosFormDesktop.length ? $filtrosFormDesktop.serializeObject() : ($filtrosFormMobile ? $filtrosFormMobile.serializeObject() : {});
            cargarVehiculos(currentPage, currentFilters); 
            $('html, body').animate({ scrollTop: $listaVehiculosContainer.offset().top - 100 }, 500);
        }
    });

    function simpleAnimateOnScroll() {
        $('.animate-on-scroll:not(.animate-in)').each(function() {
            var elementTop = $(this).offset().top;
            var windowBottom = $(window).scrollTop() + $(window).height();
            if (elementTop < windowBottom - 50) { $(this).addClass('animate-in'); }
        });
    }
    $(window).on('scroll load', function() {
        // Ejecutar animación de scroll después de un pequeño retraso 
        // para dar tiempo a que los elementos se rendericen si son cargados por AJAX.
        setTimeout(simpleAnimateOnScroll, 200);
    });
});