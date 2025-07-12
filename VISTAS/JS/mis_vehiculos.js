$(document).ready(function() {
    const $listaVehiculosContainer = $('#listaVehiculosContainer');
    const $loadingVehiculos = $('#loadingVehiculos');
    const $noVehiculosMessage = $('#noVehiculosMessage');
    const $noResultsMessage = $('#noResultsMessage');
    const $searchInput = $('#searchInput');
    const $filterBtns = $('.filter-btn');
    const $refreshBtn = $('#refreshBtn');
    const $clearFiltersBtn = $('#clearFiltersBtn');
    
    let vehiculosData = [];
    let filtroActual = 'todos';
    let busquedaActual = '';

    // Función para mostrar toast de notificación
    function showToast(message, type = 'info') {
        const $toast = $('#notificationToast');
        const $toastBody = $('#toastBody');
        const $toastIcon = $toast.find('.toast-header i');
        
        // Cambiar icono y color según el tipo
        $toastIcon.removeClass().addClass('me-2');
        switch(type) {
            case 'success':
                $toastIcon.addClass('bi bi-check-circle-fill text-success');
                break;
            case 'error':
                $toastIcon.addClass('bi bi-exclamation-triangle-fill text-danger');
                break;
            case 'warning':
                $toastIcon.addClass('bi bi-exclamation-circle-fill text-warning');
                break;
            default:
                $toastIcon.addClass('bi bi-info-circle-fill text-primary');
        }
        
        $toastBody.text(message);
        const toast = new bootstrap.Toast($toast[0]);
        toast.show();
    }

    // Función para actualizar estadísticas
    function actualizarEstadisticas(vehiculos) {
        const stats = {
            total: vehiculos.length,
            disponible: vehiculos.filter(v => v.veh_estado === 'disponible').length,
            reservado: vehiculos.filter(v => v.veh_estado === 'reservado').length,
            vendido: vehiculos.filter(v => v.veh_estado === 'vendido').length,
            desactivado: vehiculos.filter(v => v.veh_estado === 'desactivado').length
        };

        $('#totalVehiculos').text(stats.total);
        $('#disponiblesCount').text(stats.disponible);
        $('#reservadosCount').text(stats.reservado);
        $('#vendidosCount').text(stats.vendido);
        
        // Animar los números
        $('#statsContainer .stats-number').each(function() {
            $(this).addClass('fade-in');
        });
    }

    // Función para filtrar y buscar vehículos
    function filtrarVehiculos() {
        let vehiculosFiltrados = vehiculosData;

        // Aplicar filtro por estado
        if (filtroActual !== 'todos') {
            vehiculosFiltrados = vehiculosFiltrados.filter(vehiculo => 
                vehiculo.veh_estado === filtroActual
            );
        }

        // Aplicar búsqueda por texto
        if (busquedaActual.trim() !== '') {
            const termino = busquedaActual.toLowerCase();
            vehiculosFiltrados = vehiculosFiltrados.filter(vehiculo => 
                vehiculo.mar_nombre.toLowerCase().includes(termino) ||
                vehiculo.mod_nombre.toLowerCase().includes(termino) ||
                vehiculo.veh_anio.toString().includes(termino) ||
                vehiculo.veh_condicion.toLowerCase().includes(termino)
            );
        }

        mostrarVehiculos(vehiculosFiltrados);
    }

    // Función para mostrar vehículos en el DOM
    function mostrarVehiculos(vehiculos) {
        $listaVehiculosContainer.find('.vehiculo-item-col').remove();
        $noVehiculosMessage.hide();
        $noResultsMessage.hide();

        if (vehiculos.length === 0) {
            if (vehiculosData.length === 0) {
                $noVehiculosMessage.show();
            } else {
                $noResultsMessage.show();
            }
            return;
        }

        vehiculos.forEach((vehiculo, index) => {
            let imagenUrl = vehiculo.imagen_principal_url ? vehiculo.imagen_principal_url : '../PUBLIC/Img/auto_placeholder.png';
            
            if (imagenUrl.startsWith('PUBLIC/')) {
                imagenUrl = '../' + imagenUrl;
            }

            let estadoClass = 'estado-' + vehiculo.veh_estado.toLowerCase();
            let estadoTexto = vehiculo.veh_estado.charAt(0).toUpperCase() + vehiculo.veh_estado.slice(1);

            const vehiculoCardHtml = `
                <div class="col-md-6 col-lg-4 vehiculo-item-col" style="animation-delay: ${index * 0.1}s">
                    <div class="card h-100 vehiculo-card shadow-sm" data-vehiculo-id="${vehiculo.veh_id}">
                        <div class="position-relative">
                            <img src="${imagenUrl}" class="card-img-top" alt="${vehiculo.mar_nombre} ${vehiculo.mod_nombre}" loading="lazy">
                            <div class="position-absolute top-0 end-0 m-2">
                                <span class="badge bg-primary">${vehiculo.total_imagenes} fotos</span>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">
                                ${vehiculo.mar_nombre} ${vehiculo.mod_nombre} 
                                <small class="text-muted">(${vehiculo.veh_anio})</small>
                            </h5>
                            <div class="mb-3">
                                <p class="card-text mb-1">
                                    <span class="fw-bold">Condición:</span> 
                                    <span class="badge bg-light text-dark">${vehiculo.veh_condicion.charAt(0).toUpperCase() + vehiculo.veh_condicion.slice(1)}</span>
                                </p>
                                <p class="card-text mb-1">
                                    <span class="fw-bold">Precio:</span> 
                                    <span class="text-success fw-bold fs-5">$${parseFloat(vehiculo.veh_precio).toLocaleString('es-EC', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>
                                </p>
                                <p class="card-text mb-1">
                                    <span class="fw-bold">Publicado:</span> 
                                    ${new Date(vehiculo.veh_fecha_publicacion + 'T00:00:00').toLocaleDateString('es-EC')}
                                </p>
                                <p class="card-text mb-2">
                                    <span class="fw-bold">Estado:</span> 
                                    <span class="${estadoClass}">${estadoTexto}</span>
                                </p>
                            </div>
                            
                            <div class="mt-auto">
                                <div class="d-flex gap-2 mb-2">
                                    <a href="editar_auto.php?id=${vehiculo.veh_id}" class="btn btn-outline-primary flex-fill">
                                        <i class="bi bi-pencil-square me-1"></i>Editar
                                    </a>
                                    <a href="detalle_vehiculo.php?id=${vehiculo.veh_id}" target="_blank" class="btn btn-outline-info flex-fill">
                                        <i class="bi bi-eye me-1"></i>Ver
                                    </a>
                                </div>
                                <div class="dropup actions-dropdown w-100"> 
                                    <button class="btn btn-outline-secondary dropdown-toggle w-100" type="button" id="dropdownMenuButton-${vehiculo.veh_id}" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-gear me-1"></i>Más Acciones
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end w-100" aria-labelledby="dropdownMenuButton-${vehiculo.veh_id}">
                                        ${vehiculo.veh_estado === 'disponible' ? `
                                            <li><a class="dropdown-item cambiar-estado-btn" href="#" data-id="${vehiculo.veh_id}" data-estado-actual="${vehiculo.veh_estado}" data-nuevo-estado="reservado">
                                                <i class="bi bi-calendar-check me-2"></i>Marcar como Reservado
                                            </a></li>
                                            <li><a class="dropdown-item cambiar-estado-btn" href="#" data-id="${vehiculo.veh_id}" data-estado-actual="${vehiculo.veh_estado}" data-nuevo-estado="vendido">
                                                <i class="bi bi-currency-dollar me-2"></i>Marcar como Vendido
                                            </a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item cambiar-estado-btn text-warning" href="#" data-id="${vehiculo.veh_id}" data-estado-actual="${vehiculo.veh_estado}" data-nuevo-estado="desactivado">
                                                <i class="bi bi-pause-circle me-2"></i>Desactivar Anuncio
                                            </a></li>
                                        ` : ''}
                                        ${vehiculo.veh_estado === 'reservado' ? `
                                            <li><a class="dropdown-item cambiar-estado-btn text-success" href="#" data-id="${vehiculo.veh_id}" data-estado-actual="${vehiculo.veh_estado}" data-nuevo-estado="disponible">
                                                <i class="bi bi-arrow-clockwise me-2"></i>Marcar como Disponible
                                            </a></li>
                                            <li><a class="dropdown-item cambiar-estado-btn" href="#" data-id="${vehiculo.veh_id}" data-estado-actual="${vehiculo.veh_estado}" data-nuevo-estado="vendido">
                                                <i class="bi bi-currency-dollar me-2"></i>Marcar como Vendido
                                            </a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item cambiar-estado-btn text-warning" href="#" data-id="${vehiculo.veh_id}" data-estado-actual="${vehiculo.veh_estado}" data-nuevo-estado="desactivado">
                                                <i class="bi bi-pause-circle me-2"></i>Desactivar Anuncio
                                            </a></li>
                                        ` : ''}
                                        ${vehiculo.veh_estado === 'vendido' ? `
                                            <li><span class="dropdown-item-text text-muted fst-italic">
                                                <i class="bi bi-check-circle me-2"></i>Vehículo vendido
                                            </span></li>
                                        ` : ''}
                                        ${vehiculo.veh_estado === 'desactivado' ? `
                                            <li><a class="dropdown-item cambiar-estado-btn text-success" href="#" data-id="${vehiculo.veh_id}" data-estado-actual="${vehiculo.veh_estado}" data-nuevo-estado="disponible">
                                                <i class="bi bi-play-circle me-2"></i>Reactivar Anuncio
                                            </a></li>
                                        ` : ''}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $listaVehiculosContainer.append(vehiculoCardHtml);
        });

        // Agregar clase de animación a los elementos recién agregados
        setTimeout(() => {
            $('.vehiculo-item-col').addClass('fade-in');
        }, 100);
    }

    // Función principal para cargar vehículos
    function cargarMisVehiculos() {
        $loadingVehiculos.show();
        $noVehiculosMessage.hide();
        $noResultsMessage.hide();
        $listaVehiculosContainer.find('.vehiculo-item-col').remove();

        // Animación del botón de refresh
        $refreshBtn.find('i').addClass('fa-spin');

        $.ajax({
            url: './../AJAX/vehiculos_ajax.php',
            type: 'GET',
            data: { accion: 'getMisVehiculos' },
            dataType: 'json',
            success: function(response) {
                $loadingVehiculos.hide();
                $refreshBtn.find('i').removeClass('fa-spin');
                
                if (response.status === 'success' && response.vehiculos && response.vehiculos.length > 0) {
                    vehiculosData = response.vehiculos;
                    actualizarEstadisticas(vehiculosData);
                    filtrarVehiculos();
                    showToast('Vehículos cargados correctamente', 'success');
                } else if (response.status === 'success' && response.vehiculos && response.vehiculos.length === 0) {
                    vehiculosData = [];
                    actualizarEstadisticas(vehiculosData);
                    $noVehiculosMessage.show();
                } else {
                    $listaVehiculosContainer.append('<div class="col-12"><div class="alert alert-danger">Error al cargar tus vehículos: ' + response.message + '</div></div>');
                    showToast('Error al cargar vehículos: ' + response.message, 'error');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $loadingVehiculos.hide();
                $refreshBtn.find('i').removeClass('fa-spin');
                $listaVehiculosContainer.append('<div class="col-12"><div class="alert alert-danger">Error de conexión al cargar tus vehículos. Por favor, intenta de nuevo.</div></div>');
                console.error("AJAX Error en getMisVehiculos:", jqXHR.responseText, textStatus, errorThrown);
                showToast('Error de conexión. Intenta de nuevo.', 'error');
            }
        });
    }

    // Event listeners para filtros
    $filterBtns.on('click', function() {
        $filterBtns.removeClass('active');
        $(this).addClass('active');
        filtroActual = $(this).data('filter');
        filtrarVehiculos();
    });

    // Event listener para búsqueda
    $searchInput.on('input', function() {
        busquedaActual = $(this).val();
        filtrarVehiculos();
    });

    // Event listener para limpiar filtros
    $clearFiltersBtn.on('click', function() {
        $searchInput.val('');
        busquedaActual = '';
        filtroActual = 'todos';
        $filterBtns.removeClass('active');
        $filterBtns.first().addClass('active');
        filtrarVehiculos();
    });

    // Event listener para refresh
    $refreshBtn.on('click', function() {
        cargarMisVehiculos();
    });

    // Manejar clic en botones de "Cambiar Estado" con modal de confirmación
    $listaVehiculosContainer.on('click', '.cambiar-estado-btn', function(e) {
        e.preventDefault();
        const vehiculoId = $(this).data('id');
        const nuevoEstado = $(this).data('nuevo-estado');
        const estadoActual = $(this).data('estado-actual');

        if (nuevoEstado === estadoActual) {
            showToast("El vehículo ya se encuentra en este estado.", 'warning');
            return;
        }

        // Configurar modal de confirmación
        const estadoTexto = nuevoEstado.charAt(0).toUpperCase() + nuevoEstado.slice(1);
        $('#confirmModalLabel').text('Confirmar Cambio de Estado');
        $('#confirmModalBody').html(`
            <p>¿Estás seguro de que quieres cambiar el estado de este vehículo a <strong>"${estadoTexto}"</strong>?</p>
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                Esta acción actualizará el estado del vehículo en tu inventario.
            </div>
        `);

        const $confirmBtn = $('#confirmActionBtn');
        $confirmBtn.off('click').on('click', function() {
            const modal = bootstrap.Modal.getInstance($('#confirmModal')[0]);
            modal.hide();

            // Mostrar loading en el botón
            $confirmBtn.html('<span class="spinner-border spinner-border-sm me-2"></span>Procesando...');
            $confirmBtn.prop('disabled', true);

            $.ajax({
                url: './../AJAX/vehiculos_ajax.php',
                type: 'POST',
                data: {
                    accion: 'cambiarEstadoVehiculo',
                    veh_id: vehiculoId,
                    nuevo_estado: nuevoEstado,
                },
                dataType: 'json',
                success: function(response) {
                    $confirmBtn.html('Confirmar');
                    $confirmBtn.prop('disabled', false);
                    
                    if (response.status === 'success') {
                        showToast(response.message || 'Estado del vehículo actualizado correctamente.', 'success');
                        cargarMisVehiculos(); // Recargar la lista
                    } else {
                        showToast('Error al actualizar estado: ' + (response.message || 'Error desconocido.'), 'error');
                    }
                },
                error: function() {
                    $confirmBtn.html('Confirmar');
                    $confirmBtn.prop('disabled', false);
                    showToast('Error de conexión al intentar cambiar el estado.', 'error');
                }
            });
        });

        // Mostrar modal
        const modal = new bootstrap.Modal($('#confirmModal')[0]);
        modal.show();
    });

    // Efecto hover mejorado para las tarjetas
    $listaVehiculosContainer.on('mouseenter', '.vehiculo-card', function() {
        $(this).addClass('shadow-lg');
    }).on('mouseleave', '.vehiculo-card', function() {
        $(this).removeClass('shadow-lg');
    });

    // Lazy loading para imágenes
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

        $listaVehiculosContainer.on('DOMNodeInserted', 'img[data-src]', function() {
            imageObserver.observe(this);
        });
    }

    // Cargar vehículos al iniciar la página
    cargarMisVehiculos();

    // Auto-refresh cada 5 minutos (opcional)
    setInterval(function() {
        if (document.visibilityState === 'visible') {
            cargarMisVehiculos();
        }
    }, 300000); // 5 minutos
});

