/**
 * mis_ventas.js
 * JavaScript para mejorar la funcionalidad de la página de ventas
 */

$(document).ready(function() {
    // Inicializar la página
    initializePage();
    
    // Configurar eventos
    setupEventListeners();
    
    // Cargar datos adicionales si es necesario
    loadAdditionalData();
});

/**
 * Inicializa la página con configuraciones básicas
 */
function initializePage() {
    // Agregar clases CSS para mejor presentación
    $('#lista-ventas table').addClass('ventas-table');
    
    // Configurar tooltips para los botones
    $('[title]').tooltip();
    
    // Agregar animaciones de entrada
    $('#lista-ventas').addClass('fade-in');
    
    // Configurar la tabla como responsiva
    makeTableResponsive();
    
    console.log('Página de ventas inicializada correctamente');
}

/**
 * Configura los event listeners para la interactividad
 */
function setupEventListeners() {
    // Evento para filtrar ventas
    setupSearchFilter();
    
    // Evento para ordenar columnas
    setupColumnSorting();
    
    // Evento para actualizar datos
    setupRefreshButton();
    
    // Eventos para botones de acción
    setupActionButtons();
}

/**
 * Configura el filtro de búsqueda
 */
function setupSearchFilter() {
    // Crear input de búsqueda si no existe
    if ($('#search-ventas').length === 0) {
        const searchHTML = `
            <div class="search-container mb-3">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" id="search-ventas" class="form-control" 
                           placeholder="Buscar por ID, vehículo, comprador...">
                    <button class="btn btn-outline-secondary" type="button" id="clear-search">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
        `;
        $('#lista-ventas h2').after(searchHTML);
    }
    
    // Evento de búsqueda en tiempo real
    $('#search-ventas').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        filterVentas(searchTerm);
    });
    
    // Limpiar búsqueda
    $('#clear-search').on('click', function() {
        $('#search-ventas').val('');
        filterVentas('');
    });
}

/**
 * Filtra las ventas según el término de búsqueda
 */
function filterVentas(searchTerm) {
    $('#tabla-ventas-body tr').each(function() {
        const row = $(this);
        const text = row.text().toLowerCase();
        
        if (text.includes(searchTerm) || searchTerm === '') {
            row.show().addClass('fade-in');
        } else {
            row.hide().removeClass('fade-in');
        }
    });
    
    // Mostrar mensaje si no hay resultados
    const visibleRows = $('#tabla-ventas-body tr:visible').length;
    if (visibleRows === 0 && searchTerm !== '') {
        showNoResultsMessage();
    } else {
        hideNoResultsMessage();
    }
}

/**
 * Configura el ordenamiento de columnas
 */
function setupColumnSorting() {
    $('#lista-ventas table thead th').each(function(index) {
        if (index < 6) { // Excluir la columna de acciones
            $(this).addClass('sortable').append(' <i class="bi bi-arrow-down-up sort-icon"></i>');
        }
    });
    
    $('.sortable').on('click', function() {
        const columnIndex = $(this).index();
        const isAscending = !$(this).hasClass('sort-asc');
        
        // Remover clases de ordenamiento previas
        $('.sortable').removeClass('sort-asc sort-desc');
        
        // Agregar clase de ordenamiento actual
        $(this).addClass(isAscending ? 'sort-asc' : 'sort-desc');
        
        // Ordenar filas
        sortTableByColumn(columnIndex, isAscending);
    });
}

/**
 * Ordena la tabla por columna
 */
function sortTableByColumn(columnIndex, ascending) {
    const tbody = $('#tabla-ventas-body');
    const rows = tbody.find('tr').toArray();
    
    rows.sort(function(a, b) {
        const aText = $(a).find('td').eq(columnIndex).text().trim();
        const bText = $(b).find('td').eq(columnIndex).text().trim();
        
        // Detectar si es número o fecha
        let aVal = aText;
        let bVal = bText;
        
        if (columnIndex === 0 || columnIndex === 3) { // ID o Precio
            aVal = parseFloat(aText.replace(/[^\d.-]/g, '')) || 0;
            bVal = parseFloat(bText.replace(/[^\d.-]/g, '')) || 0;
        } else if (columnIndex === 1) { // Fecha
            aVal = new Date(aText.split(' ')[0].split('/').reverse().join('-'));
            bVal = new Date(bText.split(' ')[0].split('/').reverse().join('-'));
        }
        
        if (aVal < bVal) return ascending ? -1 : 1;
        if (aVal > bVal) return ascending ? 1 : -1;
        return 0;
    });
    
    // Reordenar filas en el DOM
    tbody.empty().append(rows);
    
    // Agregar animación
    $(rows).addClass('fade-in');
}

/**
 * Configura el botón de actualizar
 */
function setupRefreshButton() {
    // Agregar botón de actualizar si no existe
    if ($('#refresh-ventas').length === 0) {
        const refreshHTML = `
            <button id="refresh-ventas" class="btn btn-outline-primary btn-sm ms-2" title="Actualizar datos">
                <i class="bi bi-arrow-clockwise"></i> Actualizar
            </button>
        `;
        $('#lista-ventas h2').append(refreshHTML);
    }
    
    $('#refresh-ventas').on('click', function() {
        refreshVentasData();
    });
}

/**
         * Función para ver detalles de una venta específica
         */
        function verDetallesVenta(ventaId) {
            const modal = new bootstrap.Modal(document.getElementById('modalDetallesVenta'));
            const contenido = document.getElementById('contenidoDetallesVenta');
            
            // Mostrar loading
            contenido.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando detalles de la venta...</p>
                </div>
            `;
            
            // Mostrar modal
            modal.show();
            
            // Cargar detalles via AJAX
            $.ajax({
                url: 'ajax/obtener_detalle_venta.php',
                method: 'GET',
                data: { id: ventaId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        mostrarDetallesVenta(response.venta);
                    } else {
                        mostrarErrorDetalles(response.message);
                    }
                },
                error: function() {
                    mostrarErrorDetalles('Error de conexión al cargar los detalles.');
                }
            });
        }
        
        /**
         * Muestra los detalles de la venta en el modal
         */
        function mostrarDetallesVenta(venta) {
            const contenido = document.getElementById('contenidoDetallesVenta');
            const fecha = new Date(venta.vnt_fecha_venta).toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            
            contenido.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">Información de la Venta</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>ID Venta:</strong></td>
                                <td>${venta.vnt_id}</td>
                            </tr>
                            <tr>
                                <td><strong>Fecha:</strong></td>
                                <td>${fecha}</td>
                            </tr>
                            <tr>
                                <td><strong>Precio Final:</strong></td>
                                <td class="text-success"><strong>$${parseFloat(venta.vnt_precio_final).toLocaleString('es-ES', {minimumFractionDigits: 2})}</strong></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">Información del Vehículo</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Vehículo:</strong></td>
                                <td>${venta.vehiculo_nombre}</td>
                            </tr>
                            <tr>
                                <td><strong>Comprador:</strong></td>
                                <td>${venta.comprador_nombre}</td>
                            </tr>
                            <tr>
                                <td><strong>Vendedor:</strong></td>
                                <td>${venta.vendedor_nombre}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="factura.php?id=${venta.vnt_id}" class="btn btn-primary">
                        <i class="bi bi-receipt"></i> Ver Factura Completa
                    </a>
                </div>
            `;
        }
        
        /**
         * Muestra error en el modal de detalles
         */
        function mostrarErrorDetalles(mensaje) {
            const contenido = document.getElementById('contenidoDetallesVenta');
            contenido.innerHTML = `
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Error:</strong> ${mensaje}
                </div>
            `;
        }
        
        // Configuración adicional cuando el documento esté listo
        $(document).ready(function() {
            // Configurar tooltips de Bootstrap
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Agregar clase de animación al contenedor principal
            $('.container').addClass('slide-in');
            
            // Log para debugging
            console.log('Página de ventas cargada correctamente');
            console.log('Total de ventas mostradas:', $('#tabla-ventas-body tr').length);
        });

/**
 * Actualiza los datos de ventas via AJAX
 */
function refreshVentasData() {
    const button = $('#refresh-ventas');
    const originalHTML = button.html();
    
    // Mostrar loading
    button.html('<i class="bi bi-arrow-clockwise spin"></i> Actualizando...').prop('disabled', true);
    
    $.ajax({
        url: 'ajax/obtener_ventas.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                updateVentasTable(response.ventas);
                showNotification('Datos actualizados correctamente', 'success');
            } else {
                showNotification('Error al actualizar datos: ' + response.message, 'error');
            }
        },
        error: function() {
            showNotification('Error de conexión al actualizar datos', 'error');
        },
        complete: function() {
            // Restaurar botón
            button.html(originalHTML).prop('disabled', false);
        }
    });
}

/**
 * Actualiza la tabla con nuevos datos
 */
function updateVentasTable(ventas) {
    const tbody = $('#tabla-ventas-body');
    tbody.empty();
    
    if (ventas && ventas.length > 0) {
        ventas.forEach(function(venta) {
            const row = createVentaRow(venta);
            tbody.append(row);
        });
    } else {
        tbody.append('<tr><td colspan="7" class="text-center">No tienes ventas registradas.</td></tr>');
    }
    
    // Reaplicar eventos
    setupActionButtons();
}

/**
 * Crea una fila de venta
 */
function createVentaRow(venta) {
    const fecha = new Date(venta.vnt_fecha_venta).toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    return `
        <tr class="fade-in">
            <td><strong>${venta.vnt_id}</strong></td>
            <td>${fecha}</td>
            <td>${venta.vehiculo_nombre}</td>
            <td>$${parseFloat(venta.vnt_precio_final).toLocaleString('es-ES', {minimumFractionDigits: 2})}</td>
            <td>${venta.comprador_nombre}</td>
            <td>${venta.vendedor_nombre}</td>
            <td>
                <a href="factura.php?id=${venta.vnt_id}" class="btn btn-primary btn-sm" title="Ver Factura">
                    <i class="bi bi-receipt"></i>
                </a>
            </td>
        </tr>
    `;
}

/**
 * Configura los botones de acción
 */
function setupActionButtons() {
    // Mejorar botones de factura
    $('.btn[title="Ver Factura"]').on('click', function(e) {
        const button = $(this);
        button.addClass('loading');
        
        // Agregar indicador de carga
        setTimeout(function() {
            button.removeClass('loading');
        }, 1000);
    });
}

/**
 * Hace la tabla responsiva
 */
function makeTableResponsive() {
    const table = $('#lista-ventas table');
    
    // Agregar scroll horizontal en móviles
    if ($(window).width() < 768) {
        table.wrap('<div class="table-scroll"></div>');
    }
    
    // Redimensionar en cambio de ventana
    $(window).on('resize', function() {
        if ($(window).width() < 768) {
            if (!table.parent().hasClass('table-scroll')) {
                table.wrap('<div class="table-scroll"></div>');
            }
        } else {
            if (table.parent().hasClass('table-scroll')) {
                table.unwrap();
            }
        }
    });
}

/**
 * Carga datos adicionales si es necesario
 */
function loadAdditionalData() {
    // Cargar estadísticas básicas
    loadVentasStats();
}

/**
 * Carga estadísticas de ventas
 */
function loadVentasStats() {
    const totalVentas = $('#tabla-ventas-body tr').length;
    const totalIngresos = calculateTotalIngresos();
    
    // Agregar panel de estadísticas si no existe
    if ($('#ventas-stats').length === 0) {
        const statsHTML = `
            <div id="ventas-stats" class="stats-panel mb-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="stat-card">
                            <i class="bi bi-graph-up stat-icon"></i>
                            <div class="stat-content">
                                <h4>${totalVentas}</h4>
                                <p>Total de Ventas</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="stat-card">
                            <i class="bi bi-currency-dollar stat-icon"></i>
                            <div class="stat-content">
                                <h4>$${totalIngresos.toLocaleString('es-ES', {minimumFractionDigits: 2})}</h4>
                                <p>Ingresos Totales</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        $('#lista-ventas h2').after(statsHTML);
    }
}

/**
 * Calcula el total de ingresos
 */
function calculateTotalIngresos() {
    let total = 0;
    $('#tabla-ventas-body tr').each(function() {
        const precioText = $(this).find('td').eq(3).text();
        const precio = parseFloat(precioText.replace(/[^\d.-]/g, '')) || 0;
        total += precio;
    });
    return total;
}

/**
 * Muestra mensaje de no resultados
 */
function showNoResultsMessage() {
    if ($('#no-results-message').length === 0) {
        const message = `
            <tr id="no-results-message">
                <td colspan="7" class="text-center text-muted">
                    <i class="bi bi-search"></i>
                    <p>No se encontraron ventas que coincidan con tu búsqueda.</p>
                </td>
            </tr>
        `;
        $('#tabla-ventas-body').append(message);
    }
}

/**
 * Oculta mensaje de no resultados
 */
function hideNoResultsMessage() {
    $('#no-results-message').remove();
}

/**
 * Muestra notificaciones
 */
function showNotification(message, type = 'info') {
    const alertClass = type === 'success' ? 'alert-success' : 
                      type === 'error' ? 'alert-danger' : 'alert-info';
    
    const notification = `
        <div class="alert ${alertClass} alert-dismissible fade show notification" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Agregar al inicio del contenedor principal
    $('.container').prepend(notification);
    
    // Auto-remover después de 5 segundos
    setTimeout(function() {
        $('.notification').fadeOut(function() {
            $(this).remove();
        });
    }, 5000);
}

/**
 * Funciones de utilidad
 */
const VentasUtils = {
    formatCurrency: function(amount) {
        return new Intl.NumberFormat('es-ES', {
            style: 'currency',
            currency: 'EUR'
        }).format(amount);
    },
    
    formatDate: function(dateString) {
        return new Date(dateString).toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    },
    
    exportToCSV: function() {
        const rows = [];
        const headers = ['ID Venta', 'Fecha', 'Vehículo', 'Precio', 'Comprador', 'Vendedor'];
        rows.push(headers.join(','));
        
        $('#tabla-ventas-body tr:visible').each(function() {
            const row = [];
            $(this).find('td').slice(0, 6).each(function() {
                row.push('"' + $(this).text().trim() + '"');
            });
            rows.push(row.join(','));
        });
        
        const csvContent = rows.join('\n');
        const blob = new Blob([csvContent], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'mis_ventas.csv';
        a.click();
        window.URL.revokeObjectURL(url);
    }
};

// Exponer funciones globalmente si es necesario
window.VentasUtils = VentasUtils;

