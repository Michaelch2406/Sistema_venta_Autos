$(document).ready(function() {
    // ===== VARIABLES GLOBALES =====
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
    const $pageLoader = $('#page-loader');
    const $pageTransitionOverlay = $('#page-transition-overlay');
    const $scrollToTopBtn = $('#scrollToTop');
    const $activeFiltersCount = $('.active-filters-count');
    const $filterBadge = $('.filter-badge');
    const $totalVehiculosCounter = $('#total-vehiculos-counter');
    
    let currentPage = 1;
    const itemsPorPagina = 9;
    let currentView = 'grid'; // 'grid' o 'list'
    let isLoading = false;
    let activeFiltersCount = 0;
    let totalVehiculosUsados = 0;

    // ===== FUNCIONES DE INICIALIZACIÓN =====
    function initializeEnhancements() {
        initializeLoader();
        initializeScrollEffects();
        initializeViewToggle();
        initializeRippleEffects();
        initializeParallaxEffects();
        initializeFilterEnhancements();
        initializeLazyLoading();
        initializeKeyboardNavigation();
    }

    // ===== LOADER MEJORADO Y FUNCIONAL =====
    function initializeLoader() {
        // Mostrar el loader inmediatamente
        $pageLoader.removeClass('hidden');
        
        // Simular progreso de carga realista
        let progress = 0;
        const progressInterval = setInterval(() => {
            progress += Math.random() * 10 + 5; // Incremento más realista
            if (progress >= 100) {
                progress = 100;
                clearInterval(progressInterval);
                setTimeout(hideLoader, 300);
            }
            $('.loader-progress-bar').css('width', progress + '%');
        }, 150);
    }

    function hideLoader() {
        $pageLoader.addClass('hidden');
        setTimeout(() => {
            $pageLoader.remove();
            $('main').removeClass('content-hidden');
            triggerEntranceAnimations();
        }, 500);
    }

    // ===== ANIMACIONES DE ENTRADA =====
    function triggerEntranceAnimations() {
        // Animar elementos con retraso escalonado
        $('.listado-vehiculos-header').addClass('fade-in-up');
        setTimeout(() => $('.filtros-sidebar').addClass('slide-in-left'), 200);
        setTimeout(() => $('.content-header').addClass('slide-in-right'), 400);
        setTimeout(() => animateVehicleCounter(), 600);
    }

    // ===== CONTADOR DINÁMICO DE VEHÍCULOS =====
    function animateVehicleCounter() {
        if (totalVehiculosUsados > 0) {
            const target = totalVehiculosUsados;
            const duration = 2000;
            const increment = target / (duration / 16);
            let current = 0;

            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                $totalVehiculosCounter.text(Math.floor(current));
            }, 16);
        }
    }

    function updateVehicleCounter(total) {
        totalVehiculosUsados = total;
        $totalVehiculosCounter.attr('data-count', total);
        animateVehicleCounter();
    }

    // ===== EFECTOS DE SCROLL =====
    function initializeScrollEffects() {
        let ticking = false;

        function updateScrollEffects() {
            const scrollTop = $(window).scrollTop();
            
            // Mostrar/ocultar botón scroll to top
            if (scrollTop > 300) {
                $scrollToTopBtn.addClass('visible');
            } else {
                $scrollToTopBtn.removeClass('visible');
            }

            // Efecto parallax en header
            const headerOffset = scrollTop * 0.5;
            $('.header-glow').css('transform', `translateY(${headerOffset}px)`);

            // Animaciones on scroll
            $('.animate-on-scroll').each(function() {
                const $element = $(this);
                const elementTop = $element.offset().top;
                const elementBottom = elementTop + $element.outerHeight();
                const viewportTop = scrollTop;
                const viewportBottom = viewportTop + $(window).height();

                if (elementBottom > viewportTop && elementTop < viewportBottom) {
                    $element.addClass('animate-in');
                }
            });

            ticking = false;
        }

        $(window).on('scroll', function() {
            if (!ticking) {
                requestAnimationFrame(updateScrollEffects);
                ticking = true;
            }
        });

        // Scroll to top functionality
        $scrollToTopBtn.on('click', function() {
            $('html, body').animate({ scrollTop: 0 }, 800, 'easeInOutCubic');
        });
    }

    // ===== EFECTOS RIPPLE =====
    function initializeRippleEffects() {
        $('.btn-enhanced').on('click', function(e) {
            const $button = $(this);
            const $ripple = $button.find('.btn-ripple');
            
            if ($ripple.length === 0) return;

            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            $ripple.css({
                width: size,
                height: size,
                left: x,
                top: y
            }).addClass('animate');

            setTimeout(() => $ripple.removeClass('animate'), 600);
        });
    }

    // ===== TOGGLE DE VISTA MEJORADO =====
    function initializeViewToggle() {
        $('.view-toggle .btn').on('click', function() {
            const $this = $(this);
            const view = $this.data('view');
            
            if (view === currentView) return;

            $('.view-toggle .btn').removeClass('active');
            $this.addClass('active');
            
            currentView = view;
            
            // Aplicar transición suave
            $listaVehiculosContainer.addClass('transitioning');
            
            setTimeout(() => {
                if (view === 'list') {
                    $listaVehiculosContainer.addClass('list-view');
                    // Reorganizar tarjetas para vista de lista
                    $('.card-vehiculo').each(function() {
                        const $card = $(this);
                        const $cardBody = $card.find('.card-body');
                        const $img = $card.find('.card-vehiculo-img-top');
                        
                        if (!$cardBody.find('.row').length) {
                            const $row = $('<div class="row g-0"></div>');
                            const $colImg = $('<div class="col-md-4"></div>');
                            const $colContent = $('<div class="col-md-8"></div>');
                            
                            $colImg.append($img.clone());
                            $colContent.append($cardBody.html());
                            $row.append($colImg).append($colContent);
                            
                            $cardBody.html($row);
                            $img.hide();
                        }
                    });
                } else {
                    $listaVehiculosContainer.removeClass('list-view');
                    // Restaurar vista de cuadrícula
                    $('.card-vehiculo').each(function() {
                        const $card = $(this);
                        const $cardBody = $card.find('.card-body');
                        const $row = $cardBody.find('.row');
                        
                        if ($row.length) {
                            const originalContent = $row.find('.col-md-8').html();
                            $cardBody.html(originalContent);
                            $card.find('.card-vehiculo-img-top').show();
                        }
                    });
                }
                
                $listaVehiculosContainer.removeClass('transitioning');
            }, 150);
        });
    }

    // ===== MEJORAS DE FILTROS =====
    function initializeFilterEnhancements() {
        // Contador de filtros activos
        $filtrosForm.on('change', 'select, input', updateActiveFiltersCount);
        
        // Efectos hover en grupos de filtros
        $('.filter-group').hover(
            function() { $(this).addClass('hover-lift'); },
            function() { $(this).removeClass('hover-lift'); }
        );

        // Animación en focus de inputs
        $('.enhanced-select, .enhanced-input').on('focus', function() {
            $(this).parent().addClass('focused');
        }).on('blur', function() {
            $(this).parent().removeClass('focused');
        });
    }

    function updateActiveFiltersCount() {
        let count = 0;
        
        $filtrosForm.find('select, input').each(function() {
            if ($(this).val() && $(this).val() !== '') {
                count++;
            }
        });

        activeFiltersCount = count;
        $activeFiltersCount.text(count);
        $filterBadge.text(count);

        if (count > 0) {
            $activeFiltersCount.show().addClass('bounce-in');
            $filterBadge.show().addClass('bounce-in');
        } else {
            $activeFiltersCount.hide();
            $filterBadge.hide();
        }
    }

    // ===== EFECTOS PARALLAX =====
    function initializeParallaxEffects() {
        $(window).on('scroll', function() {
            const scrolled = $(this).scrollTop();
            const parallax = scrolled * 0.3;
            
            $('.header-particles .particle').each(function(index) {
                const speed = (index + 1) * 0.1;
                $(this).css('transform', `translateY(${parallax * speed}px)`);
            });
        });
    }

    // ===== LAZY LOADING =====
    function initializeLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    }

    // ===== NAVEGACIÓN POR TECLADO =====
    function initializeKeyboardNavigation() {
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                $('.offcanvas.show').offcanvas('hide');
            }
            
            if (e.key === 'Enter' && $(e.target).is('.enhanced-input')) {
                e.preventDefault();
                $filtrosForm.submit();
            }
        });
    }

    // ===== FUNCIONES ORIGINALES MEJORADAS =====
    function poblarSelect($selectElement, data, valueField, textField, defaultOptionText, placeholderValue = "") {
        $selectElement.empty().append($('<option>', { value: placeholderValue, text: defaultOptionText }));
        $.each(data, function(i, item) {
            $selectElement.append($('<option>', { value: item[valueField], text: item[textField] }));
        });
        $selectElement.addClass('fade-in-up');
    }

    function poblarSelectSimple($selectElement, dataArray, defaultOptionText, placeholderValue = "") {
        $selectElement.empty().append($('<option>', { value: placeholderValue, text: defaultOptionText }));
        $.each(dataArray, function(i, item) {
            $selectElement.append($('<option>', { value: item, text: item }));
        });
        $selectElement.addClass('fade-in-up');
    }

    function cargarFiltrosIniciales() {
        $filtrosForm.addClass('loading');
        
        $.ajax({
            url: '../AJAX/vehiculos_ajax.php', 
            type: 'GET', 
            data: { accion: 'getCatalogos' }, 
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    poblarSelect($filtroMarca, response.marcas, 'mar_id', 'mar_nombre', 'Todas las marcas');
                    poblarSelect($filtroTipo, response.tipos_vehiculo, 'tiv_id', 'tiv_nombre', 'Todos los tipos');
                    poblarSelectSimple($filtroProvincia, response.provincias, 'Todas las provincias');
                    
                    // Poblar años
                    const currentYear = new Date().getFullYear();
                    for (let year = currentYear + 1; year >= 1980; year--) {
                        $filtroAnioMin.append($('<option>', { value: year, text: year }));
                        $filtroAnioMax.append($('<option>', { value: year, text: year }));
                    }
                    $filtroAnioMin.val('');
                    $filtroAnioMax.val('');
                    
                    setTimeout(() => {
                        $filtrosForm.removeClass('loading').addClass('loaded');
                    }, 300);
                } else { 
                    console.error('Error al cargar catálogos para filtros:', response.message);
                    showNotification('Error al cargar filtros', 'error');
                }
            },
            error: function() { 
                console.error('Error de conexión al cargar catálogos para filtros.');
                showNotification('Error de conexión al cargar filtros', 'error');
                $filtrosForm.removeClass('loading');
            }
        });
    }

    // ===== MANEJO DE CAMBIO DE MARCA MEJORADO =====
    $filtroMarca.on('change', function() {
        var marcaId = $(this).val();
        $filtroModelo.empty().append('<option value="">Cualquier modelo</option>').prop('disabled', true);
        $filtroModelo.addClass('loading');
        
        if (marcaId) {
            $.ajax({
                url: '../AJAX/vehiculos_ajax.php', 
                type: 'GET', 
                data: { accion: 'getModelos', marca_id: marcaId }, 
                dataType: 'json',
                success: function(response) {
                    $filtroModelo.removeClass('loading');
                    if (response.status === 'success' && response.modelos && response.modelos.length > 0) {
                        poblarSelect($filtroModelo, response.modelos, 'mod_id', 'mod_nombre', 'Cualquier modelo');
                        $filtroModelo.prop('disabled', false).addClass('bounce-in');
                    } else { 
                        $filtroModelo.prop('disabled', true);
                    }
                },
                error: function() { 
                    $filtroModelo.prop('disabled', true).removeClass('loading');
                    showNotification('Error al cargar modelos', 'error');
                }
            });
        } else {
            $filtroModelo.removeClass('loading');
        }
    });

    // ===== RENDERIZADO DE VEHÍCULOS MEJORADO =====
    function renderVehiculos(vehiculos) {
        $listaVehiculosContainer.empty();
        
        if (vehiculos && vehiculos.length > 0) {
            $.each(vehiculos, function(index, v) {
                let imagenUrl = v.imagen_principal_url ? v.imagen_principal_url : '../PUBLIC/Img/auto_placeholder.png';
                if (imagenUrl.startsWith('PUBLIC/')) { 
                    imagenUrl = '../' + imagenUrl; 
                }

                const precioFormateado = v.veh_precio ? parseFloat(v.veh_precio).toLocaleString('es-EC', { style: 'currency', currency: 'USD' }) : 'Consultar';
                const kmFormateado = v.veh_kilometraje ? parseInt(v.veh_kilometraje).toLocaleString('es-EC') : '0';
                
                // Determinar badge de estado
                let statusBadge = '';
                if (v.veh_estado === 'nuevo') {
                    statusBadge = '<span class="badge bg-success vehicle-status-badge">Nuevo</span>';
                } else if (v.veh_destacado) {
                    statusBadge = '<span class="badge bg-warning vehicle-status-badge">Destacado</span>';
                }
                
                const cardHtml = `
                    <div class="col-sm-6 col-md-6 col-lg-4 mb-4">
                        <div class="card card-vehiculo h-100 shadow-sm animate-on-scroll" style="animation-delay: ${index * 0.1}s">
                            ${statusBadge}
                            <a href="detalle_vehiculo.php?id=${v.veh_id}" class="text-decoration-none">
                                <img src="${imagenUrl}" class="card-img-top card-vehiculo-img-top" alt="${v.mar_nombre} ${v.mod_nombre}" loading="lazy">
                            </a>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">
                                    <a href="detalle_vehiculo.php?id=${v.veh_id}" class="text-dark text-decoration-none">
                                        ${v.mar_nombre} ${v.mod_nombre}
                                    </a>
                                </h5>
                                <p class="precio mb-2">${precioFormateado}</p>
                                <div class="caracteristicas-list mt-1">
                                    <p class="caracteristica-item mb-1">
                                        <i class="bi bi-calendar-event me-2"></i>
                                        <span>Año: ${v.veh_anio}</span>
                                    </p>
                                    <p class="caracteristica-item mb-1">
                                        <i class="bi bi-speedometer2 me-2"></i>
                                        <span>Recorrido: ${kmFormateado} km</span>
                                    </p>
                                    <p class="caracteristica-item mb-1">
                                        <i class="bi bi-geo-alt me-2"></i>
                                        <span>${v.veh_ubicacion_ciudad || 'N/D'}, ${v.veh_ubicacion_provincia || 'N/D'}</span>
                                    </p>
                                </div>
                                <a href="detalle_vehiculo.php?id=${v.veh_id}" class="btn btn-ver-detalles mt-auto w-100">
                                    <i class="bi bi-search me-2"></i>Ver Detalles
                                    <span class="btn-ripple"></span>
                                </a>
                            </div>
                        </div>
                    </div>`;
                $listaVehiculosContainer.append(cardHtml);
            });
            
            // Activar animaciones de scroll
            setTimeout(() => {
                $('.animate-on-scroll').each(function(index) {
                    setTimeout(() => {
                        $(this).addClass('animate-in');
                    }, index * 100);
                });
            }, 100);
        }
    }

    // ===== RENDERIZADO DE PAGINACIÓN MEJORADO =====
    function renderPaginacion(paginaActual, totalPaginas) {
        $paginacionContainer.empty();
        if (totalPaginas <= 1) return;

        let ul = $('<ul class="pagination justify-content-center"></ul>');
        
        // Botón Anterior
        ul.append(`<li class="page-item ${paginaActual === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${paginaActual - 1}">
                <i class="bi bi-chevron-left me-1"></i>Anterior
            </a>
        </li>`);

        // Números de página
        let inicio = Math.max(1, paginaActual - 2);
        let fin = Math.min(totalPaginas, paginaActual + 2);

        if (inicio > 1) {
            ul.append('<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>');
            if (inicio > 2) ul.append('<li class="page-item disabled"><span class="page-link">...</span></li>');
        }

        for (let i = inicio; i <= fin; i++) {
            ul.append(`<li class="page-item ${i === paginaActual ? 'active' : ''}">
                <a class="page-link" href="#" data-page="${i}">${i}</a>
            </li>`);
        }

        if (fin < totalPaginas) {
            if (fin < totalPaginas - 1) ul.append('<li class="page-item disabled"><span class="page-link">...</span></li>');
            ul.append(`<li class="page-item"><a class="page-link" href="#" data-page="${totalPaginas}">${totalPaginas}</a></li>`);
        }

        // Botón Siguiente
        ul.append(`<li class="page-item ${paginaActual === totalPaginas ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${paginaActual + 1}">
                Siguiente<i class="bi bi-chevron-right ms-1"></i>
            </a>
        </li>`);
        
        $paginacionContainer.append(ul).addClass('fade-in-up');
    }

    // ===== CARGA DE VEHÍCULOS MEJORADA =====
    function cargarVehiculos(page = 1, filtrosData = {}) {
        if (isLoading) return;
        
        isLoading = true;
        $loadingVehiculos.show().addClass('pulse');
        $noVehiculosMessage.hide();
        $listaVehiculosContainer.html('');
        $paginacionContainer.empty();

        // Mostrar overlay de transición
        $pageTransitionOverlay.addClass('active');

        const dataToSend = {
            accion: 'getVehiculosListado',
            condicion: 'usado',
            pagina: page,
            items_por_pagina: itemsPorPagina,
            ...filtrosData
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
                setTimeout(() => {
                    $loadingVehiculos.hide().removeClass('pulse');
                    $pageTransitionOverlay.removeClass('active');
                    isLoading = false;
                    
                    if (response.status === 'success') {
                        if (response.vehiculos && response.vehiculos.length > 0) {
                            renderVehiculos(response.vehiculos);
                            renderPaginacion(response.pagina_actual, response.total_paginas);
                            
                            const offset = (response.pagina_actual - 1) * response.items_por_pagina;
                            $conteoResultados.html(`
                                <i class="bi bi-car-front me-2"></i>
                                Mostrando ${offset + 1} - ${offset + response.vehiculos.length} de ${response.total_vehiculos} vehículos usados.
                            `).addClass('bounce-in');
                            
                            // Actualizar contador dinámico en header
                            updateVehicleCounter(response.total_vehiculos);
                            
                            showNotification(`Se encontraron ${response.total_vehiculos} vehículos`, 'success');
                        } else {
                            $noVehiculosMessage.show().addClass('bounce-in');
                            $conteoResultados.html('<i class="bi bi-car-front me-2"></i>Mostrando 0 de 0 vehículos usados.');
                            updateVehicleCounter(0);
                        }
                    } else {
                        $listaVehiculosContainer.html(`
                            <div class="col-12 alert alert-danger glass-effect">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Error: ${response.message || 'No se pudieron cargar los vehículos.'}
                            </div>
                        `);
                        $conteoResultados.html('<i class="bi bi-exclamation-triangle me-2"></i>Error al cargar.');
                        showNotification('Error al cargar vehículos', 'error');
                    }
                }, 300);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                setTimeout(() => {
                    $loadingVehiculos.hide().removeClass('pulse');
                    $pageTransitionOverlay.removeClass('active');
                    isLoading = false;
                    
                    $listaVehiculosContainer.html(`
                        <div class="col-12 alert alert-danger glass-effect">
                            <i class="bi bi-wifi-off me-2"></i>
                            Error de conexión. Intenta de nuevo.
                        </div>
                    `);
                    console.error("AJAX Error en cargarVehiculos:", jqXHR.responseText, textStatus, errorThrown);
                    $conteoResultados.html('<i class="bi bi-wifi-off me-2"></i>Error de conexión.');
                    showNotification('Error de conexión', 'error');
                }, 300);
            }
        });
    }

    // ===== SISTEMA DE NOTIFICACIONES =====
    function showNotification(message, type = 'info') {
        const notificationHtml = `
            <div class="notification notification-${type} glass-effect">
                <i class="bi bi-${getNotificationIcon(type)} me-2"></i>
                ${message}
            </div>
        `;
        
        const $notification = $(notificationHtml);
        $('body').append($notification);
        
        setTimeout(() => $notification.addClass('show'), 100);
        setTimeout(() => {
            $notification.removeClass('show');
            setTimeout(() => $notification.remove(), 300);
        }, 3000);
    }

    function getNotificationIcon(type) {
        switch(type) {
            case 'success': return 'check-circle';
            case 'error': return 'exclamation-circle';
            case 'warning': return 'exclamation-triangle';
            default: return 'info-circle';
        }
    }

    // ===== HELPER FUNCTIONS =====
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

    // ===== EVENT HANDLERS =====
    
    // Carga inicial
    cargarVehiculos(currentPage);
    cargarFiltrosIniciales();

    // Envío de filtros
    $filtrosForm.on('submit', function(e) {
        e.preventDefault();
        currentPage = 1;
        const filtrosData = $(this).serializeObject();
        cargarVehiculos(currentPage, filtrosData);
        
        // Cerrar offcanvas en móvil
        $('#filtrosOffcanvas').offcanvas('hide');
    });

    // Limpiar filtros
    $('#resetFiltrosBtn, #clearFiltersBtn').on('click', function() {
        $filtrosForm[0].reset();
        $filtroModelo.empty().append('<option value="">Selecciona marca primero</option>').prop('disabled', true);
        currentPage = 1;
        cargarVehiculos(currentPage);
        updateActiveFiltersCount();
        showNotification('Filtros limpiados', 'info');
    });

    // Ver todos los vehículos
    $('#verTodosLink, #expandSearchBtn').on('click', function(e) {
        e.preventDefault();
        $('#resetFiltrosBtn').click();
    });

    // Paginación
    $paginacionContainer.on('click', 'a.page-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (page && page !== currentPage && !isLoading) {
            currentPage = parseInt(page);
            cargarVehiculos(currentPage, $filtrosForm.serializeObject());
            
            // Scroll suave al inicio de la lista
            $('html, body').animate({
                scrollTop: $listaVehiculosContainer.offset().top - 100
            }, 500);
        }
    });

    // Clonar filtros para móvil
    function cloneFiltrosToMobile() {
        const $filtrosClone = $filtrosForm.clone();
        $filtrosClone.attr('id', 'filtrosFormMobile');
        $('#filtrosMobileBody').html($filtrosClone);
        
        // Sincronizar valores
        $filtrosClone.find('select, input').each(function() {
            const name = $(this).attr('name');
            const value = $filtrosForm.find(`[name="${name}"]`).val();
            $(this).val(value);
        });
    }

    // Mostrar offcanvas
    $('#filtrosOffcanvas').on('show.bs.offcanvas', function() {
        cloneFiltrosToMobile();
    });

    // ===== INICIALIZACIÓN =====
    initializeEnhancements();
});

