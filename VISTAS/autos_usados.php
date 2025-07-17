<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehículos Usados - AutoMercado Total</title>
    <link href="./../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="./../PUBLIC/css/styles.css" rel="stylesheet"> <!-- Estilos Globales -->
    <link href="./CSS/autos_usados.css" rel="stylesheet"> <!-- NUEVO: Estilos Adaptados con Paleta de Colores -->
    <script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/trefoil.js"></script>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">
    <!-- Loader minimalista con colores adaptados -->
    <div id="page-loader" class="page-loader">
        <div class="loader-content">
            <div class="loader-spinner">
                <l-trefoil size="40" stroke="4" stroke-length="0.15" bg-opacity="0.1" speed="1.2" color="#D32727"></l-trefoil>
            </div>
            <div class="loader-text">Cargando vehículos...</div>
            <div class="loader-progress">
                <div class="loader-progress-bar"></div>
            </div>
        </div>
    </div>

    <!-- Overlay de transición minimalista -->
    <div id="page-transition-overlay" class="page-transition-overlay"></div>

    <header id="navbar-placeholder"></header>

    <main class="flex-grow-1 content-hidden">
        <div class="container-fluid py-4">
            <!-- Header minimalista con contador dinámico -->
            <div class="listado-vehiculos-header text-center">
                <div class="header-content">
                    <h1 class="display-4 fw-bold">
                        <span>Vehículos</span> 
                        <span class="text-highlight">Usados</span>
                    </h1>
                    <p class="lead col-lg-7 mx-auto">Encuentra el auto usado perfecto para ti entre nuestra amplia selección de vehículos certificados.</p>
                    <div class="header-stats">
                        <div class="stat-item">
                            <i class="bi bi-car-front-fill"></i>
                            <span class="stat-number" id="total-vehiculos-counter" data-count="0">0</span>
                            <span class="stat-label">Vehículos Disponibles</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Sidebar de filtros minimalista -->
                <div class="col-lg-3 mb-4">
                    <div class="filtros-sidebar">
                        <div class="sidebar-header">
                            <h5><i class="bi bi-funnel me-2"></i>Filtros de Búsqueda</h5>
                            <span class="active-filters-count" style="display: none;">0</span>
                        </div>
                        
                        <form id="filtrosForm" class="needs-validation" novalidate>
                            <!-- Filtro por Marca -->
                            <div class="filter-group">
                                <label class="filter-group-title" for="filtro_mar_id">Marca</label>
                                <select class="form-select enhanced-select" id="filtro_mar_id" name="marca_id">
                                    <option value="">Selecciona una marca</option>
                                </select>
                            </div>

                            <!-- Filtro por Modelo -->
                            <div class="filter-group">
                                <label class="filter-group-title" for="filtro_mod_id">Modelo</label>
                                <select class="form-select enhanced-select" id="filtro_mod_id" name="modelo_id" disabled>
                                    <option value="">Selecciona marca primero</option>
                                </select>
                            </div>

                            <!-- Filtro por Tipo de Vehículo -->
                            <div class="filter-group">
                                <label class="filter-group-title" for="filtro_tiv_id">Tipo de Vehículo</label>
                                <select class="form-select enhanced-select" id="filtro_tiv_id" name="tipo_vehiculo_id">
                                    <option value="">Todos los tipos</option>
                                </select>
                            </div>

                            <!-- Filtro por Provincia -->
                            <div class="filter-group">
                                <label class="filter-group-title" for="filtro_provincia">Provincia</label>
                                <select class="form-select enhanced-select" id="filtro_provincia" name="provincia">
                                    <option value="">Todas las provincias</option>
                                </select>
                            </div>

                            <!-- Filtro por Rango de Años -->
                            <div class="filter-group">
                                <label class="filter-group-title">Año del Vehículo</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <select class="form-select enhanced-select" id="filtro_anio_min" name="anio_min">
                                            <option value="">Desde</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <select class="form-select enhanced-select" id="filtro_anio_max" name="anio_max">
                                            <option value="">Hasta</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Filtro por Rango de Precios -->
                            <div class="filter-group">
                                <label class="filter-group-title">Rango de Precio (USD)</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <input type="number" class="form-control enhanced-input" id="filtro_precio_min" name="precio_min" placeholder="Mínimo" min="0">
                                    </div>
                                    <div class="col-6">
                                        <input type="number" class="form-control enhanced-input" id="filtro_precio_max" name="precio_max" placeholder="Máximo" min="0">
                                    </div>
                                </div>
                            </div>

                            <!-- Filtro por Kilometraje -->
                            <div class="filter-group">
                                <label class="filter-group-title">Kilometraje Máximo</label>
                                <input type="number" class="form-control enhanced-input" id="filtro_kilometraje_max" name="kilometraje_max" placeholder="Ej: 100000" min="0">
                            </div>

                            <!-- Botones de acción -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-enhanced btn-primary">
                                    <i class="bi bi-search me-2"></i>Buscar Vehículos
                                </button>
                                <button type="button" class="btn btn-enhanced btn-outline-secondary" id="resetFiltrosBtn">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Limpiar Filtros
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Contenido principal -->
                <div class="col-lg-9">
                    <!-- Header de contenido -->
                    <div class="content-header d-flex justify-content-between align-items-center flex-wrap">
                        <div class="results-counter" id="conteoResultados">
                            <i class="bi bi-car-front me-2"></i>Cargando vehículos...
                        </div>
                        
                        <div class="header-controls">
                            <!-- Toggle de vista -->
                            <div class="view-toggle">
                                <button type="button" class="btn active" data-view="grid" title="Vista en cuadrícula">
                                    <i class="bi bi-grid-3x3-gap"></i>
                                </button>
                                <button type="button" class="btn" data-view="list" title="Vista en lista">
                                    <i class="bi bi-list"></i>
                                </button>
                            </div>
                            
                            <!-- Botón de filtros para móvil -->
                            <button class="btn btn-enhanced btn-outline-secondary d-lg-none position-relative" type="button" data-bs-toggle="offcanvas" data-bs-target="#filtrosOffcanvas">
                                <i class="bi bi-funnel me-2"></i>Filtros
                                <span class="filter-badge" style="display: none;">0</span>
                            </button>
                        </div>
                    </div>

                    <!-- Loading skeleton -->
                    <div id="loadingVehiculosListado" class="loading-container" style="display: none;">
                        <div class="loading-skeleton">
                            <div class="skeleton-card"></div>
                            <div class="skeleton-card"></div>
                            <div class="skeleton-card"></div>
                            <div class="skeleton-card"></div>
                            <div class="skeleton-card"></div>
                            <div class="skeleton-card"></div>
                        </div>
                    </div>

                    <!-- Lista de vehículos -->
                    <div class="vehicles-grid">
                        <div class="row" id="listaVehiculosUsados">
                            <!-- Los vehículos se cargarán aquí dinámicamente -->
                        </div>
                    </div>

                    <!-- Mensaje cuando no hay vehículos -->
                    <div id="noVehiculosListadoMessage" class="no-results-container" style="display: none;">
                        <div class="no-results-icon mb-3">
                            <i class="bi bi-car-front display-1"></i>
                        </div>
                        <h4 class="mb-3">No se encontraron vehículos</h4>
                        <p class="text-muted mb-4">No hay vehículos que coincidan con los filtros seleccionados. Intenta ajustar tus criterios de búsqueda.</p>
                        <div class="suggested-actions">
                            <button class="btn btn-enhanced btn-primary" id="clearFiltersBtn">
                                <i class="bi bi-arrow-clockwise me-2"></i>Limpiar Filtros
                            </button>
                            <a href="#" class="btn btn-enhanced btn-outline-secondary" id="expandSearchBtn">
                                <i class="bi bi-search me-2"></i>Ver Todos
                            </a>
                        </div>
                    </div>

                    <!-- Paginación -->
                    <div class="mt-4">
                        <nav aria-label="Paginación de vehículos" id="paginacionVehiculosUsados">
                            <!-- La paginación se generará dinámicamente -->
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Offcanvas para filtros en móvil -->
    <div class="offcanvas offcanvas-start enhanced-offcanvas" tabindex="-1" id="filtrosOffcanvas">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">
                <i class="bi bi-funnel me-2"></i>Filtros de Búsqueda
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body" id="filtrosMobileBody">
            <!-- Los filtros se clonarán aquí para móvil -->
        </div>
    </div>

    <!-- Botón scroll to top -->
    <button id="scrollToTop" class="scroll-to-top" title="Volver arriba">
        <i class="bi bi-arrow-up"></i>
    </button>

    <footer id="footer-placeholder"></footer>

    <!-- Scripts -->
    <script src="./../PUBLIC/jquery-3.7.1.min.js"></script>
    <script src="./../Bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="./JS/global.js"></script>
    <script src="./JS/autos_usados.js"></script> <!-- NUEVO: JavaScript Adaptado -->
</body>
</html>

