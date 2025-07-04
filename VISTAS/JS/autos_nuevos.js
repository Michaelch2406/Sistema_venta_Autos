/* vehiculos_nuevos_fixed.js */

$(document).ready(function() {
    const $listaVehiculosContainer = $('#listaVehiculosNuevos');
    const $loadingVehiculos = $('#loadingVehiculosListadoNuevos');
    const $noVehiculosMessage = $('#noVehiculosListadoMessageNuevos');
    const $paginacionContainer = $('#paginacionVehiculosNuevos');
    const $conteoResultados = $('#conteoResultadosNuevos');

    const $filtrosFormPrincipal = $('#filtrosFormNuevos');
    const $filtroMarcaPrincipal = $('#filtro_mar_id_nuevo');
    const $filtroModeloPrincipal = $('#filtro_mod_id_nuevo');
    const $filtroTipoPrincipal = $('#filtro_tiv_id_nuevo');
    const $filtroPrecioMaxPrincipal = $('#filtro_precio_max_nuevo');

    let currentPage = 1;
    const itemsPorPagina = 6;

    function poblarSelect($select, data, valField, txtField, defaultText, placeholder = "") {
        $select.empty()
               .append($('<option>', { value: placeholder, text: defaultText }));
        data.forEach(item => {
            $select.append($('<option>', {
                value: item[valField],
                text: item[txtField]
            }));
        });
    }

    function cargarFiltrosIniciales() {
        $.ajax({
            url: '../AJAX/vehiculos_ajax.php',
            type: 'GET',
            data: { accion: 'getCatalogos', tipo_vehiculo: 'nuevo' },
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    poblarSelect($filtroMarcaPrincipal, res.marcas, 'mar_id', 'mar_nombre', 'Marca (Todas)');
                    poblarSelect($filtroTipoPrincipal, res.tipos_vehiculo, 'tiv_id', 'tiv_nombre', 'Tipo (Todos)');

                    // Offcanvas filters
                    if ($('#filtrosMobileBodyNuevos').length) {
                        const $fm = $('#filtrosFormNuevosMobile');
                        const $mMarca = $fm.find('#filtro_mar_id_nuevo');
                        const $mTipo  = $fm.find('#filtro_tiv_id_nuevo');
                        if ($mMarca.length) poblarSelect($mMarca, res.marcas, 'mar_id', 'mar_nombre', 'Marca (Todas)');
                        if ($mTipo.length)  poblarSelect($mTipo,  res.tipos_vehiculo, 'tiv_id', 'tiv_nombre', 'Tipo (Todos)');
                    }
                } else console.error('Error catálogos:', res.message);
            },
            error: function() { console.error('Error conexión catálogos.'); }
        });
    }

    function manejarCambioMarca() {
        const marcaId = $(this).val();
        const $form = $(this).closest('form');
        const $modSelect = $form.find('select[name="mod_id"]');

        $modSelect.empty()
                  .append('<option value="">Modelo</option>')
                  .prop('disabled', true);
        if (!marcaId) return;

        $.ajax({
            url: '../AJAX/vehiculos_ajax.php',
            type: 'GET',
            data: { accion: 'getModelos', marca_id: marcaId, tipo_vehiculo: 'nuevo' },
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success' && res.modelos.length) {
                    poblarSelect($modSelect, res.modelos, 'mod_id', 'mod_nombre', 'Modelo (Todos)');
                    $modSelect.prop('disabled', false);
                } else {
                    $modSelect.empty().append('<option value="">Sin modelos</option>');
                }
            },
            error: function() {
                $modSelect.empty().append('<option value="">Error modelos</option>');
            }
        });
    }

    $filtrosFormPrincipal.on('change', 'select[name="mar_id"]', manejarCambioMarca);
    $('#filtrosMobileBodyNuevos').on('change', '#filtrosFormNuevosMobile select[name="mar_id"]', manejarCambioMarca);

    function renderVehiculos(vehiculos) {
        $listaVehiculosContainer.empty();
        vehiculos.forEach(v => {
            let img = v.imagen_principal_url || '../PUBLIC/Img/auto_placeholder_luxury.jpg';
            if (img.startsWith('PUBLIC/')) img = '../' + img;

            const price = v.veh_precio
                ? parseFloat(v.veh_precio).toLocaleString('es-EC', { style: 'currency', currency: 'USD', minimumFractionDigits: 0 })
                : 'Precio a Consultar';
            
            // El enlace ahora apunta a detalle_nuevo_veh.php
            const detalleUrl = `detalle_nuevo_veh.php?id=${v.veh_id}`;

            const html = `
                <div class="col-lg-6 col-md-6 mb-4 mb-xl-5">
                    <div class="card-vehiculo-luxury">
                        <a href="${detalleUrl}" class="vehiculo-imagen-container">
                            <img src="${img}" class="vehiculo-imagen" alt="${v.mar_nombre} ${v.mod_nombre}">
                        </a>
                        <div class="vehiculo-info">
                            <h3 class="vehiculo-marca-modelo">${v.mar_nombre} ${v.mod_nombre}</h3>
                            <p class="vehiculo-precio">${price}</p>
                            <p class="vehiculo-anio">Año: ${v.veh_anio}</p>
                            <p class="vehiculo-condicion"><i class="bi bi-gem me-2"></i>Nuevo (0km)</p>
                            <a href="${detalleUrl}" class="btn btn-ver-detalles-luxury mt-3">
                                <i class="bi bi-arrow-right-circle me-2"></i>Ver Detalles
                            </a>
                        </div>
                    </div>
                </div>`;
            $listaVehiculosContainer.append(html);
        });
    }

    function renderPaginacion(actual, total) {
        $paginacionContainer.empty();
        if (total <= 1) return $paginacionContainer.hide();

        const ul = $('<ul class="pagination justify-content-center"></ul>');
        ul.append(`<li class="page-item ${actual===1?'disabled':''}"><a class="page-link" href="#" data-page="${actual-1}"><i class="bi bi-chevron-left"></i></a></li>`);

        const start = Math.max(1, actual-1);
        const end   = Math.min(total, actual+1);

        if (start>1) {
            ul.append('<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>');
            if (start>2) ul.append('<li class="page-item disabled"><span class="page-link">...</span></li>');
        }
        for (let i=start;i<=end;i++) {
            ul.append(`<li class="page-item ${i===actual?'active':''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`);
        }
        if (end<total) {
            if (end<total-1) ul.append('<li class="page-item disabled"><span class="page-link">...</span></li>');
            ul.append(`<li class="page-item"><a class="page-link" href="#" data-page="${total}">${total}</a></li>`);
        }
        ul.append(`<li class="page-item ${actual===total?'disabled':''}"><a class="page-link" href="#" data-page="${actual+1}"><i class="bi bi-chevron-right"></i></a></li>`);

        $paginacionContainer.append(ul).show();
    }

    function cargarVehiculos(page=1, filtros={}) {
        $loadingVehiculos.show();
        $noVehiculosMessage.hide();
        $listaVehiculosContainer.empty();
        $paginacionContainer.hide().empty();

        let data = { accion: 'getVehiculosListado', condicion: 'nuevo', pagina: page, items_por_pagina: itemsPorPagina, ...filtros };
        Object.keys(data).forEach(k => { if (data[k]===""||data[k]==null) delete data[k]; });

        $.ajax({
            url: '../AJAX/vehiculos_ajax.php',
            type: 'GET', data, dataType: 'json',
            success: res => {
                $loadingVehiculos.hide();
                if (res.status==='success' && res.vehiculos.length) {
                    renderVehiculos(res.vehiculos);
                    renderPaginacion(res.pagina_actual, res.total_paginas);
                    $conteoResultados.html(`<i class="bi bi-car-front-fill me-2"></i>${res.total_vehiculos} Vehículos en la Colección`);
                } else {
                    $noVehiculosMessage.show();
                    $conteoResultados.text('No se encontraron vehículos');
                }
            },
            error: (jq, st, err) => {
                $loadingVehiculos.hide();
                $listaVehiculosContainer.html('<div class="col-12 alert alert-danger text-center">Error de conexión.</div>');
                $conteoResultados.text('Error de conexión');
                console.error('AJAX Error:', err);
            }
        });
    }

    // Clonar filtros para offcanvas
    const $orig = $('#filtrosFormNuevos');
    if ($orig.length && $('#filtrosMobileBodyNuevos').length) {
        const $clone = $orig.clone().attr('id','filtrosFormNuevosMobile');
        $('#filtrosMobileBodyNuevos').append($clone);
    }

    cargarFiltrosIniciales();
    cargarVehiculos(currentPage);

    // Eventos de submit y reset
    $filtrosFormPrincipal.on('submit', e => {
        e.preventDefault();
        currentPage=1;
        cargarVehiculos(1, $filtrosFormPrincipal.serializeObject());
        $('#filtrosDesktopCollapse').collapse('hide');
    });

    $('#filtrosMobileBodyNuevos').on('submit','#filtrosFormNuevosMobile', e=>{
        e.preventDefault();
        currentPage=1;
        cargarVehiculos(1, $(e.currentTarget).serializeObject());
        bootstrap.Offcanvas.getInstance(document.getElementById('filtrosOffcanvasNuevos')).hide();
    });

    $.fn.serializeObject = function() {
        const o = {};
        this.serializeArray().forEach(({name,value}) => {
            if (value) {
                if (o[name]) {
                    if (!Array.isArray(o[name])) o[name]=[o[name]];
                    o[name].push(value);
                } else o[name]=value;
            }
        });
        return o;
    };

    $filtrosFormPrincipal.on('click', '#resetFiltrosBtnNuevos', function() {
        $filtrosFormPrincipal[0].reset();
        $filtroModeloPrincipal.empty().append('<option value="">Modelo</option>').prop('disabled', true);
        currentPage=1;
        cargarVehiculos(1);
    });
    
    $('#filtrosMobileBodyNuevos').on('click', '#resetFiltrosBtnNuevos', function() {
        const $f = $(this).closest('form');
        $f[0].reset();
        $f.find('select[name="mod_id"]').empty().append('<option value="">Modelo</option>').prop('disabled', true);
        currentPage=1;
        cargarVehiculos(1);
    });

    $('#verTodosLinkNuevos').on('click', function(e) {
        e.preventDefault();
        $filtrosFormPrincipal.add('#filtrosFormNuevosMobile').each((i,form)=>form.reset());
        $filtroModeloPrincipal.empty().append('<option value="">Modelo</option>').prop('disabled', true);
        currentPage=1;
        cargarVehiculos(1);
    });

    $paginacionContainer.on('click', 'a.page-link', function(e) {
        e.preventDefault();
        const page = parseInt($(this).data('page'));
        if (page && page!==currentPage) {
            currentPage = page;
            cargarVehiculos(page, $filtrosFormPrincipal.serializeObject());
            $('html, body').animate({scrollTop: $listaVehiculosContainer.offset().top - 100}, 500);
        }
    });
});
