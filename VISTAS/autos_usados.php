<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehículos Usados - AutoMercado Total</title>
    <link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../PUBLIC/css/styles.css" rel="stylesheet"> <!-- Estilos Globales -->
    <link href="../VISTAS/css/autos_usados.css" rel="stylesheet"> <!-- NUEVO: Estilos Específicos -->
    <script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/trefoil.js"></script>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">
    <!-- Loader mejorado y funcional -->
    <div id="page-loader" class="page-loader">
        <div class="loader-content">
            <div class="loader-spinner">
                <l-trefoil size="50" stroke="5" stroke-length="0.15" bg-opacity="0.1" speed="1.4" color="#ff6b35"></l-trefoil>
            </div>
            <div class="loader-text">Cargando vehículos...</div>
            <div class="loader-progress">
                <div class="loader-progress-bar"></div>
            </div>
        </div>
    </div>

    <!-- Overlay de transición -->
    <div id="page-transition-overlay" class="page-transition-overlay"></div>

    <header id="navbar-placeholder"></header>

    <main class="flex-grow-1 content-hidden">
        <div class="container-fluid py-4">
            <!-- Header mejorado con solo contador dinámico -->
            <div class="listado-vehiculos-header text-center">
                <div class="header-particles">
                    <div class="particle"></div> <div class="particle"></div> <div class="particle"></div>
                    <div class="particle"></div> <div class="particle"></div> <div class="particle"></div>
                    <div class="particle"></div> <div class="particle"></div>
                </div>
                <div class="header-glow"></div>
                <div class="header-content">
                    <h1 class="display-4 fw-bold">
                        <span class="text-gradient">Vehículos</span> 
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
            
            <div class="container">
                <div class="row">
                    <!-- Sidebar de filtros mejorado y adaptativo -->
                    <div class="col-lg-3 mb-4 d-none d-lg-block" id="filtrosSidebarContainer">
                        <div class="filtros-sidebar">
                            <div class="sidebar-header">
                                <h5><i class="bi bi-filter-circle-fill me-2"></i>Filtrar Vehículos</h5>
                                <div class="filter-indicator">
                                    <span class="active-filters-count">0</span>
                                </div>
                            </div>
                            <form id="filtrosForm">
                                <div class="filter-group">
                                    <div class="mb-3">
                                        <label for="filtro_mar_id" class="form-label">
                                            <i class="bi bi-tag me-1"></i>Marca
                                        </label>
                                        <select class="form-select enhanced-select" id="filtro_mar_id" name="mar_id">
                                            <option value="">Todas</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="filtro_mod_id" class="form-label">
                                            <i class="bi bi-gear me-1"></i>Modelo
                                        </label>
                                        <select class="form-select enhanced-select" id="filtro_mod_id" name="mod_id" disabled>
                                            <option value="">Selecciona marca</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="filtro_tiv_id" class="form-label">
                                            <i class="bi bi-list-ul me-1"></i>Tipo
                                        </label>
                                        <select class="form-select enhanced-select" id="filtro_tiv_id" name="tiv_id">
                                            <option value="">Todos</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="filter-group">
                                    <div class="filter-group-title">
                                        <i class="bi bi-currency-dollar me-2"></i>Rango de Precio
                                    </div>
                                    <div class="price-range-container">
                                        <div class="mb-3">
                                            <label for="filtro_precio_min" class="form-label">Precio Mínimo</label>
                                            <input type="number" class="form-control enhanced-input" id="filtro_precio_min" name="precio_min" placeholder="Ej: 5000">
                                        </div>
                                        <div class="mb-3">
                                            <label for="filtro_precio_max" class="form-label">Precio Máximo</label>
                                            <input type="number" class="form-control enhanced-input" id="filtro_precio_max" name="precio_max" placeholder="Ej: 20000">
                                        </div>
                                    </div>
                                </div>

                                <div class="filter-group">
                                    <div class="filter-group-title">
                                        <i class="bi bi-calendar3 me-2"></i>Año del Vehículo
                                    </div>
                                    <div class="year-range-container">
                                        <div class="mb-3">
                                            <label for="filtro_anio_min" class="form-label">Desde</label>
                                            <select class="form-select enhanced-select" id="filtro_anio_min" name="anio_min">
                                                <option value="">Cualquiera</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="filtro_anio_max" class="form-label">Hasta</label>
                                            <select class="form-select enhanced-select" id="filtro_anio_max" name="anio_max">
                                                <option value="">Cualquiera</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="filter-group">
                                    <div class="mb-3">
                                        <label for="filtro_provincia" class="form-label">
                                            <i class="bi bi-geo-alt me-1"></i>Provincia
                                        </label>
                                        <select class="form-select enhanced-select" id="filtro_provincia" name="provincia">
                                            <option value="">Todas</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="filter-actions">
                                    <button type="submit" class="btn btn-primary btn-enhanced w-100 mb-2">
                                        <i class="bi bi-funnel-fill me-2"></i>Aplicar Filtros
                                        <span class="btn-ripple"></span>
                                    </button>
                                    <button type="reset" class="btn btn-outline-secondary btn-enhanced w-100" id="resetFiltrosBtn">
                                        <i class="bi bi-arrow-clockwise me-2"></i>Limpiar Filtros
                                        <span class="btn-ripple"></span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Contenido principal mejorado -->
                    <div class="col-lg-9">                        
                        <div class="content-header d-flex justify-content-between align-items-center mb-4">
                            <div id="conteoResultados" class="results-counter">
                                <i class="bi bi-car-front me-2"></i>Cargando...
                            </div>
                            <div class="header-controls">
                                <div class="view-toggle d-none d-md-flex me-3">
                                    <button class="btn btn-sm btn-outline-primary active" data-view="grid" title="Vista en cuadrícula">
                                        <i class="bi bi-grid-3x3-gap"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary" data-view="list" title="Vista en lista">
                                        <i class="bi bi-list"></i>
                                    </button>
                                </div>
                                <button class="btn btn-outline-primary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#filtrosOffcanvas" aria-controls="filtrosOffcanvas">
                                    <i class="bi bi-funnel me-2"></i>Filtros
                                    <span class="filter-badge">0</span>
                                </button>
                            </div>
                        </div>

                        <!-- Contenedor de vehículos con animaciones mejoradas -->
                        <div id="listaVehiculosUsados" class="vehicles-grid row g-4">
                            <div class="col-12 text-center" id="loadingVehiculosListado">
                                <div class="loading-container">
                                    <div class="loading-spinner">
                                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                    </div>
                                    <div class="loading-skeleton">
                                        <div class="skeleton-card"></div>
                                        <div class="skeleton-card"></div>
                                        <div class="skeleton-card"></div>
                                    </div>
                                    <p class="mt-3 text-muted">Cargando vehículos...</p>
                                </div>
                            </div>
                        </div>

                        <!-- Mensaje de no resultados mejorado -->
                        <div id="noVehiculosListadoMessage" class="col-12 text-center mt-5 py-5" style="display: none;">
                            <div class="no-results-container">
                                <div class="no-results-icon">
                                    <i class="bi bi-search display-1 text-muted mb-3"></i>
                                </div>
                                <h4 class="mt-3">No se encontraron vehículos</h4>
                                <p class="text-muted">Intenta ajustar tus filtros o <a href="#" id="verTodosLink" class="text-decoration-none">ver todos los vehículos</a>.</p>
                                <div class="suggested-actions mt-4">
                                    <button class="btn btn-outline-primary me-2" id="clearFiltersBtn">
                                        <i class="bi bi-funnel-fill me-1"></i>Limpiar Filtros
                                    </button>
                                    <button class="btn btn-outline-secondary" id="expandSearchBtn">
                                        <i class="bi bi-search me-1"></i>Ampliar Búsqueda
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Paginación mejorada -->
                        <nav id="paginacionVehiculosUsados" aria-label="Paginación de vehículos" class="mt-5 d-flex justify-content-center">
                            <!-- La paginación se genera dinámicamente -->
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Offcanvas para filtros móviles mejorado -->
        <div class="offcanvas offcanvas-start enhanced-offcanvas" tabindex="-1" id="filtrosOffcanvas" aria-labelledby="filtrosOffcanvasLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="filtrosOffcanvasLabel">
                    <i class="bi bi-filter-circle-fill me-2"></i>Filtrar Vehículos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body" id="filtrosMobileBody">
                <!-- El formulario de filtros se clonará aquí por JS -->
            </div>
        </div>

        <!-- Botón de scroll to top -->
        <button id="scrollToTop" class="scroll-to-top" title="Volver arriba">
            <i class="bi bi-arrow-up"></i>
        </button>
    </main>

    <?php include __DIR__ . '/partials/footer.php'; ?>

    <script src="../PUBLIC/jquery-3.7.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../VISTAS/JS/global.js"></script>
    <script src="../VISTAS/JS/autos_usados.js"></script>
</body>
</html>

