<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehículos Usados - AutoMercado Total</title>
    <link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../PUBLIC/css/styles.css" rel="stylesheet">
    <script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/trefoil.js"></script>
    <style>
        .listado-vehiculos-header {
            background: linear-gradient(to right, #0052D4, #65C7F7, #9CECFB); /* Un gradiente azul */
            color: white;
            padding: 3rem 1.5rem;
            border-radius: .5rem;
            margin-bottom: 2.5rem;
        }
        .card-vehiculo {
            transition: all 0.3s ease-in-out;
            border: 1px solid #e9ecef;
        }
        .card-vehiculo:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.12)!important;
        }
        .card-vehiculo-img-top {
            height: 220px;
            object-fit: cover;
            border-bottom: 1px solid #f0f0f0;
        }
        .card-vehiculo .card-title {
            font-weight: 600;
            color: #333;
            min-height: 48px; /* Para alinear títulos de 2 líneas */
        }
        .card-vehiculo .precio {
            font-size: 1.4rem;
            font-weight: bold;
            color: #0d6efd; /* Azul primario */
        }
        .card-vehiculo .caracteristica-item {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .card-vehiculo .caracteristica-item i {
            margin-right: 6px;
        }
        .filtros-sidebar {
            background-color: #fff;
            padding: 1.5rem;
            border-radius: .375rem;
            box-shadow: 0 .125rem .25rem rgba(0,0,0,.075);
        }
        .filtros-sidebar h5 {
            border-bottom: 1px solid #eee;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">
    <div id="page-loader">
        <l-trefoil size="50" stroke="5" stroke-length="0.15" bg-opacity="0.1" speed="1.4" color="#0d6efd"></l-trefoil>
    </div>

    <header id="navbar-placeholder"></header>

    <main class="flex-grow-1 content-hidden">
        <div class="container-fluid py-4">
            <div class="listado-vehiculos-header text-center">
                <h1 class="display-4 fw-bold">Vehículos Usados</h1>
                <p class="lead col-lg-7 mx-auto">Encuentra el auto usado perfecto para ti entre nuestra amplia selección de vehículos verificados.</p>
            </div>
            
            <div class="container">
                <div class="row">
                    <!-- Columna de Filtros (Opcional, se puede implementar después) -->
                    <div class="col-lg-3 mb-4 d-none d-lg-block" id="filtrosSidebar">
                        <div class="filtros-sidebar sticky-top" style="top: 80px;"> <!-- Ajustar top según altura de navbar -->
                            <h5><i class="bi bi-filter-circle-fill me-2"></i>Filtrar Vehículos</h5>
                            <form id="filtrosForm">
                                <div class="mb-3">
                                    <label for="filtro_mar_id" class="form-label">Marca</label>
                                    <select class="form-select" id="filtro_mar_id" name="mar_id">
                                        <option value="">Todas las marcas</option>
                                        <!-- Se poblará con JS -->
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="filtro_mod_id" class="form-label">Modelo</label>
                                    <select class="form-select" id="filtro_mod_id" name="mod_id" disabled>
                                        <option value="">Selecciona marca primero</option>
                                        <!-- Se poblará con JS -->
                                    </select>
                                </div>
                                 <div class="mb-3">
                                    <label for="filtro_tiv_id" class="form-label">Tipo de Vehículo</label>
                                    <select class="form-select" id="filtro_tiv_id" name="tiv_id">
                                        <option value="">Todos los tipos</option>
                                        <!-- Se poblará con JS -->
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="filtro_precio_min" class="form-label">Precio Mínimo</label>
                                    <input type="number" class="form-control" id="filtro_precio_min" name="precio_min" placeholder="Ej: 5000">
                                </div>
                                <div class="mb-3">
                                    <label for="filtro_precio_max" class="form-label">Precio Máximo</label>
                                    <input type="number" class="form-control" id="filtro_precio_max" name="precio_max" placeholder="Ej: 20000">
                                </div>
                                <div class="mb-3">
                                    <label for="filtro_anio_min" class="form-label">Año Desde</label>
                                    <select class="form-select" id="filtro_anio_min" name="anio_min">
                                        <option value="">Cualquiera</option>
                                        <!-- Se poblará con JS (años) -->
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="filtro_anio_max" class="form-label">Año Hasta</label>
                                    <select class="form-select" id="filtro_anio_max" name="anio_max">
                                        <option value="">Cualquiera</option>
                                         <!-- Se poblará con JS (años) -->
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="filtro_provincia" class="form-label">Provincia</label>
                                    <select class="form-select" id="filtro_provincia" name="provincia">
                                        <option value="">Todas las provincias</option>
                                         <!-- Se poblará con JS -->
                                    </select>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary"><i class="bi bi-funnel-fill me-2"></i>Aplicar Filtros</button>
                                    <button type="reset" class="btn btn-outline-secondary mt-2" id="resetFiltrosBtn">Limpiar Filtros</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Columna de Listado de Vehículos -->
                    <div class="col-lg-9">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div id="conteoResultados" class="text-muted">Mostrando 0 de 0 vehículos</div>
                            <div>
                                <!-- Opciones de ordenamiento (implementación futura) -->
                                <!-- <select class="form-select form-select-sm" id="ordenarPor">
                                    <option selected>Ordenar por: Relevancia</option>
                                    <option value="precio_asc">Precio: Menor a Mayor</option>
                                    <option value="precio_desc">Precio: Mayor a Menor</option>
                                    <option value="fecha_desc">Más Recientes</option>
                                </select> -->
                            </div>
                        </div>

                        <div id="listaVehiculosUsados" class="row g-4">
                            <div class="col-12 text-center" id="loadingVehiculosListado">
                                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                <p class="mt-2">Cargando vehículos usados...</p>
                            </div>
                        </div>
                        <div id="noVehiculosListadoMessage" class="col-12 text-center mt-5 py-5" style="display: none;">
                            <i class="bi bi-search display-1 text-muted"></i>
                            <h4 class="mt-3">No se encontraron vehículos usados con los filtros actuales.</h4>
                            <p class="text-muted">Intenta ajustar tus criterios de búsqueda o <a href="#" id="verTodosLink">ver todos los vehículos usados</a>.</p>
                        </div>
                        <nav id="paginacionVehiculosUsados" aria-label="Paginación de vehículos" class="mt-5 d-flex justify-content-center"></nav>
                    </div>
                </div>
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