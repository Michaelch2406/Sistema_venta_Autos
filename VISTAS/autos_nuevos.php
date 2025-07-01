<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Colección Exclusiva: Vehículos Nuevos - AutoMercado Total</title>
    <link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Roboto:wght@300;400&display=swap" rel="stylesheet">
    <link href="../PUBLIC/css/styles.css" rel="stylesheet"> <!-- Estilos Globales -->
    <link href="../VISTAS/CSS/autos_nuevos.css" rel="stylesheet"> <!-- Estilos Específicos NUEVOS -->
    <script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/trefoil.js"></script>
</head>
<body class="luxury-theme d-flex flex-column min-vh-100">
    <div id="page-loader">
        <l-trefoil size="60" stroke="5" stroke-length="0.15" bg-opacity="0.1" speed="1.4" color="var(--luxury-gold)"></l-trefoil>
    </div>

    <header id="navbar-placeholder"></header>

    <main class="flex-grow-1 content-hidden">
        <section class="hero-section-nuevos text-center">
            <div class="container">
                <h1 class="hero-title">Descubra la Distinción</h1>
                <p class="hero-subtitle">Una selección curada de vehículos nuevos, donde la ingeniería y el lujo se encuentran.</p>
            </div>
        </section>

        <section class="filtros-controles-nuevos py-3 py-md-4">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center">
                    <button class="btn btn-outline-light btn-sm d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#filtrosOffcanvasNuevos" aria-controls="filtrosOffcanvasNuevos">
                        <i class="bi bi-filter-left me-2"></i>Filtrar Colección
                    </button>
                    <div id="conteoResultadosNuevos" class="text-light small d-none d-lg-block">Cargando vehículos...</div>
                     <button class="btn btn-outline-light btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#filtrosDesktopCollapse" aria-expanded="false" aria-controls="filtrosDesktopCollapse">
                        <i class="bi bi-sliders me-2"></i>Mostrar Filtros
                    </button>
                </div>
                <div class="collapse" id="filtrosDesktopCollapse">
                    <div class="filtros-sidebar-luxury mt-3">
                        <form id="filtrosFormNuevos" class="row g-3 align-items-center">
                            <div class="col-md-3"><select class="form-select form-select-sm" id="filtro_mar_id_nuevo" name="mar_id"><option value="">Marca (Todas)</option></select></div>
                            <div class="col-md-3"><select class="form-select form-select-sm" id="filtro_mod_id_nuevo" name="mod_id" disabled><option value="">Modelo</option></select></div>
                            <div class="col-md-2"><select class="form-select form-select-sm" id="filtro_tiv_id_nuevo" name="tiv_id"><option value="">Tipo (Todos)</option></select></div>
                            <div class="col-md-2"><input type="number" class="form-control form-control-sm" id="filtro_precio_max_nuevo" name="precio_max" placeholder="Precio Máx (USD)"></div>
                            <div class="col-md-2 d-flex">
                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1 me-2"><i class="bi bi-search"></i></button>
                                <button type="reset" class="btn btn-outline-secondary btn-sm flex-grow-1" id="resetFiltrosBtnNuevos"><i class="bi bi-arrow-clockwise"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
        
        <div class="container py-5">
            <div id="listaVehiculosNuevos" class="row g-xl-5 g-lg-4 g-md-3 g-2">
                <!-- Indicador de carga principal -->
                <div class="col-12 text-center" id="loadingVehiculosListadoNuevos">
                    <l-trefoil size="80" stroke="6" stroke-length="0.18" bg-opacity="0.1" speed="1.2" color="var(--luxury-gold)"></l-trefoil>
                    <p class="mt-3 lead text-light">Cargando Colección...</p>
                </div>
            </div>
            <div id="noVehiculosListadoMessageNuevos" class="col-12 text-center mt-5 py-5" style="display: none;">
                <i class="bi bi-diamond-exclamation display-1 text-muted-luxury mb-3"></i>
                <h4 class="mt-3 text-light">No se encontraron vehículos que coincidan con su búsqueda.</h4>
                <p class="text-muted-luxury">Le invitamos a <a href="#" id="verTodosLinkNuevos" class="text-gold">explorar toda nuestra colección</a> o ajustar sus filtros.</p>
            </div>
            <nav id="paginacionVehiculosNuevos" aria-label="Paginación de vehículos nuevos" class="mt-5 d-flex justify-content-center"></nav>
        </div>
        

        <!-- Offcanvas para filtros móviles -->
        <div class="offcanvas offcanvas-start luxury-offcanvas" tabindex="-1" id="filtrosOffcanvasNuevos" aria-labelledby="filtrosOffcanvasLabelNuevos">
            <div class="offcanvas-header border-bottom border-secondary">
                <h5 class="offcanvas-title text-light" id="filtrosOffcanvasLabelNuevos"><i class="bi bi-filter-diamond-fill me-2 text-gold"></i>Filtrar Colección</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body" id="filtrosMobileBodyNuevos">
                <!-- El formulario de filtros se clonará aquí por JS -->
                 <!-- Se espera que JS clone el form Desktop aquí, adaptado para mobile -->
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/partials/footer.php'; ?>

    <script src="../PUBLIC/jquery-3.7.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../VISTAS/JS/global.js"></script>
    <script src="../VISTAS/JS/autos_nuevos.js"></script> <!-- JS Específico para Autos Nuevos -->
    
</body>
</html>
