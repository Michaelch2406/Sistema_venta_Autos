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
    <div id="page-loader">
        <l-trefoil size="50" stroke="5" stroke-length="0.15" bg-opacity="0.1" speed="1.4" color="#0d6efd"></l-trefoil>
    </div>

    <header id="navbar-placeholder"></header>

    <main class="flex-grow-1 content-hidden">
        <div class="container-fluid py-4"> <!-- container-fluid para el header ancho -->
            <div class="listado-vehiculos-header text-center">
                <div class="header-particles"> <!-- Si decides mantener las partículas -->
                    <div class="particle"></div> <div class="particle"></div> <div class="particle"></div>
                    <div class="particle"></div> <div class="particle"></div>
                </div>
                <h1 class="display-4 fw-bold">Vehículos Usados</h1>
                <p class="lead col-lg-7 mx-auto">Encuentra el auto usado perfecto para ti entre nuestra amplia selección.</p>
            </div>
            
            <div class="container"> <!-- container normal para el contenido principal -->
                <div class="row">
                    <div class="col-lg-3 mb-4 d-none d-lg-block" id="filtrosSidebarContainer">
                        <div class="filtros-sidebar sticky-top" style="top: 80px;">
                            <h5><i class="bi bi-filter-circle-fill me-2"></i>Filtrar Vehículos</h5>
                            <form id="filtrosForm">
                                <div class="mb-3"><label for="filtro_mar_id" class="form-label"><i class="bi bi-tag me-1"></i>Marca</label><select class="form-select" id="filtro_mar_id" name="mar_id"><option value="">Todas</option></select></div>
                                <div class="mb-3"><label for="filtro_mod_id" class="form-label"><i class="bi bi-gear me-1"></i>Modelo</label><select class="form-select" id="filtro_mod_id" name="mod_id" disabled><option value="">Selecciona marca</option></select></div>
                                <div class="mb-3"><label for="filtro_tiv_id" class="form-label"><i class="bi bi-list-ul me-1"></i>Tipo</label><select class="form-select" id="filtro_tiv_id" name="tiv_id"><option value="">Todos</option></select></div>
                                <div class="mb-3"><label for="filtro_precio_min" class="form-label"><i class="bi bi-currency-dollar me-1"></i>Precio Mín.</label><input type="number" class="form-control" id="filtro_precio_min" name="precio_min" placeholder="Ej: 5000"></div>
                                <div class="mb-3"><label for="filtro_precio_max" class="form-label"><i class="bi bi-currency-dollar me-1"></i>Precio Máx.</label><input type="number" class="form-control" id="filtro_precio_max" name="precio_max" placeholder="Ej: 20000"></div>
                                <div class="mb-3"><label for="filtro_anio_min" class="form-label"><i class="bi bi-calendar3-week me-1"></i>Año Desde</label><select class="form-select" id="filtro_anio_min" name="anio_min"><option value="">Cualquiera</option></select></div>
                                <div class="mb-3"><label for="filtro_anio_max" class="form-label"><i class="bi bi-calendar3-event me-1"></i>Año Hasta</label><select class="form-select" id="filtro_anio_max" name="anio_max"><option value="">Cualquiera</option></select></div>
                                <div class="mb-3"><label for="filtro_provincia" class="form-label"><i class="bi bi-geo-alt me-1"></i>Provincia</label><select class="form-select" id="filtro_provincia" name="provincia"><option value="">Todas</option></select></div>
                                <div class="d-grid"><button type="submit" class="btn btn-primary"><i class="bi bi-funnel-fill me-2"></i>Aplicar Filtros</button>
                                <button type="reset" class="btn btn-outline-secondary mt-2" id="resetFiltrosBtn"><i class="bi bi-arrow-clockwise me-2"></i>Limpiar</button></div>
                            </form>
                        </div>
                    </div>

                    <div class="col-lg-9">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div id="conteoResultados" class="text-muted"><i class="bi bi-car-front me-2"></i>Cargando...</div>
                            <button class="btn btn-outline-primary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#filtrosOffcanvas" aria-controls="filtrosOffcanvas"><i class="bi bi-funnel me-2"></i>Filtros</button>
                        </div>
                        <div id="listaVehiculosUsados" class="row g-4">
                            <div class="col-12 text-center" id="loadingVehiculosListado"><div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"><span class="visually-hidden">Cargando...</span></div><p class="mt-3 text-muted">Cargando vehículos...</p></div>
                        </div>
                        <div id="noVehiculosListadoMessage" class="col-12 text-center mt-5 py-5" style="display: none;"><i class="bi bi-search display-1 text-muted mb-3"></i><h4 class="mt-3">No se encontraron vehículos usados</h4><p class="text-muted">Intenta ajustar tus filtros o <a href="#" id="verTodosLink" class="text-decoration-none">ver todos los vehículos</a>.</p></div>
                        <nav id="paginacionVehiculosUsados" aria-label="Paginación de vehículos" class="mt-5 d-flex justify-content-center"></nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Offcanvas para filtros móviles -->
        <div class="offcanvas offcanvas-start" tabindex="-1" id="filtrosOffcanvas" aria-labelledby="filtrosOffcanvasLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="filtrosOffcanvasLabel"><i class="bi bi-filter-circle-fill me-2"></i>Filtrar Vehículos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body" id="filtrosMobileBody">
                <!-- El formulario de filtros se clonará aquí por JS -->
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/partials/footer.php'; ?>

    <script src="../PUBLIC/jquery-3.7.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../VISTAS/JS/global.js"></script>
    <script src="../VISTAS/JS/autos_usados.js"></script>
</body>
</html>