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
        /* Animaciones y efectos avanzados */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes shimmer {
            0% {
                background-position: -200% 0;
            }
            100% {
                background-position: 200% 0;
            }
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.02);
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-5px);
            }
        }

        /* Header mejorado */
        .listado-vehiculos-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4rem 1.5rem;
            border-radius: 20px;
            margin-bottom: 3rem;
            position: relative;
            overflow: hidden;
            animation: fadeInUp 1s ease-out;
        }

        .listado-vehiculos-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="2" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="1.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .listado-vehiculos-header h1 {
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: pulse 3s ease-in-out infinite;
            position: relative;
            z-index: 1;
        }

        .listado-vehiculos-header p {
            animation: slideInRight 1s ease-out 0.3s both;
            position: relative;
            z-index: 1;
        }

        /* Sidebar de filtros mejorada */
        .filtros-sidebar {
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            animation: slideInLeft 0.8s ease-out;
        }

        .filtros-sidebar h5 {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            border-bottom: 2px solid #007bff;
            padding-bottom: 0.75rem;
            margin-bottom: 1.5rem;
            position: relative;
        }

        .filtros-sidebar h5::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 50px;
            height: 2px;
            background: linear-gradient(135deg, #007bff 0%, #28a745 100%);
            border-radius: 2px;
        }

        .filtros-sidebar .form-control,
        .filtros-sidebar .form-select {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.75rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
        }

        .filtros-sidebar .form-control:focus,
        .filtros-sidebar .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
            transform: translateY(-2px);
        }

        .filtros-sidebar .btn {
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .filtros-sidebar .btn-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border: none;
        }

        .filtros-sidebar .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 123, 255, 0.4);
        }

        .filtros-sidebar .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s ease;
        }

        .filtros-sidebar .btn:hover::before {
            left: 100%;
        }

        /* Tarjetas de vehículos mejoradas */
        .card-vehiculo {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            border-radius: 15px;
            overflow: hidden;
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            animation: scaleIn 0.6s ease-out;
            position: relative;
        }

        .card-vehiculo::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(0, 123, 255, 0.1) 0%, rgba(40, 167, 69, 0.1) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1;
        }

        .card-vehiculo:hover::before {
            opacity: 1;
        }

        .card-vehiculo:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .card-vehiculo-img-top {
            height: 250px;
            object-fit: cover;
            transition: transform 0.4s ease;
            position: relative;
            z-index: 2;
        }

        .card-vehiculo:hover .card-vehiculo-img-top {
            transform: scale(1.05);
        }

        .card-vehiculo .card-body {
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        .card-vehiculo .card-title {
            font-weight: 700;
            background: linear-gradient(135deg, #333 0%, #007bff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            min-height: 48px;
            transition: all 0.3s ease;
        }

        .card-vehiculo:hover .card-title {
            transform: translateX(5px);
        }

        .card-vehiculo .precio {
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
        }

        .card-vehiculo .precio::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.8), transparent);
            animation: shimmer 3s ease-in-out infinite;
        }

        .card-vehiculo .caracteristica-item {
            font-size: 0.9rem;
            color: #6c757d;
            transition: all 0.3s ease;
            padding: 0.25rem 0;
        }

        .card-vehiculo .caracteristica-item:hover {
            color: #007bff;
            transform: translateX(3px);
        }

        .card-vehiculo .caracteristica-item i {
            margin-right: 8px;
            transition: transform 0.3s ease;
        }

        .card-vehiculo .caracteristica-item:hover i {
            transform: scale(1.2);
        }

        /* Badge de estado */
        .vehicle-status-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 3;
            animation: float 3s ease-in-out infinite;
        }

        /* Contador de resultados mejorado */
        #conteoResultados {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            border: 1px solid rgba(0, 123, 255, 0.2);
            animation: slideInLeft 0.6s ease-out;
        }

        /* Loading state mejorado */
        #loadingVehiculosListado {
            animation: pulse 2s ease-in-out infinite;
        }

        /* Mensaje de no resultados mejorado */
        #noVehiculosListadoMessage {
            background: linear-gradient(145deg, #f8f9fa 0%, #ffffff 100%);
            border-radius: 15px;
            padding: 3rem;
            border: 2px dashed #dee2e6;
        }

        #noVehiculosListadoMessage i {
            animation: float 2s ease-in-out infinite;
        }

        /* Paginación mejorada */
        .pagination .page-link {
            border: none;
            border-radius: 10px;
            margin: 0 2px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .pagination .page-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
            transform: translateY(-2px);
        }

        /* Animaciones de entrada escalonadas */
        .card-vehiculo:nth-child(1) { animation-delay: 0.1s; }
        .card-vehiculo:nth-child(2) { animation-delay: 0.2s; }
        .card-vehiculo:nth-child(3) { animation-delay: 0.3s; }
        .card-vehiculo:nth-child(4) { animation-delay: 0.4s; }
        .card-vehiculo:nth-child(5) { animation-delay: 0.5s; }
        .card-vehiculo:nth-child(6) { animation-delay: 0.6s; }

        /* Animaciones de scroll */
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease-out;
        }

        .animate-on-scroll.animate-in {
            opacity: 1;
            transform: translateY(0);
        }

        /* Botón de ver detalles mejorado */
        .btn-ver-detalles {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border: none;
            border-radius: 25px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-ver-detalles:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 123, 255, 0.4);
        }

        .btn-ver-detalles::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s ease;
        }

        .btn-ver-detalles:hover::before {
            left: 100%;
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .listado-vehiculos-header {
                padding: 2rem 1rem;
                margin-bottom: 2rem;
            }
            
            .listado-vehiculos-header h1 {
                font-size: 2.5rem;
            }
            
            .filtros-sidebar {
                padding: 1.5rem;
            }
        }

        /* Efectos de partículas en el header */
        .header-particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: floatParticle 6s ease-in-out infinite;
        }

        .particle:nth-child(1) { left: 10%; top: 20%; width: 4px; height: 4px; animation-delay: 0s; }
        .particle:nth-child(2) { left: 80%; top: 10%; width: 6px; height: 6px; animation-delay: 1s; }
        .particle:nth-child(3) { left: 20%; top: 80%; width: 3px; height: 3px; animation-delay: 2s; }
        .particle:nth-child(4) { left: 90%; top: 70%; width: 5px; height: 5px; animation-delay: 3s; }
        .particle:nth-child(5) { left: 50%; top: 30%; width: 4px; height: 4px; animation-delay: 4s; }

        @keyframes floatParticle {
            0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.3; }
            50% { transform: translateY(-20px) rotate(180deg); opacity: 1; }
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
                <div class="header-particles">
                    <div class="particle"></div>
                    <div class="particle"></div>
                    <div class="particle"></div>
                    <div class="particle"></div>
                    <div class="particle"></div>
                </div>
                <h1 class="display-4 fw-bold">Vehículos Usados</h1>
                <p class="lead col-lg-7 mx-auto">Encuentra el auto usado perfecto para ti entre nuestra amplia selección de vehículos verificados y de calidad garantizada.</p>
            </div>
            
            <div class="container">
                <div class="row">
                    <!-- Columna de Filtros -->
                    <div class="col-lg-3 mb-4 d-none d-lg-block" id="filtrosSidebar">
                        <div class="filtros-sidebar sticky-top" style="top: 80px;">
                            <h5><i class="bi bi-filter-circle-fill me-2"></i>Filtrar Vehículos</h5>
                            <form id="filtrosForm">
                                <div class="mb-3">
                                    <label for="filtro_mar_id" class="form-label">
                                        <i class="bi bi-car-front me-1"></i>Marca
                                    </label>
                                    <select class="form-select" id="filtro_mar_id" name="mar_id">
                                        <option value="">Todas las marcas</option>
                                        <!-- Se poblará con JS -->
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="filtro_mod_id" class="form-label">
                                        <i class="bi bi-gear me-1"></i>Modelo
                                    </label>
                                    <select class="form-select" id="filtro_mod_id" name="mod_id" disabled>
                                        <option value="">Selecciona marca primero</option>
                                        <!-- Se poblará con JS -->
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="filtro_tiv_id" class="form-label">
                                        <i class="bi bi-truck me-1"></i>Tipo de Vehículo
                                    </label>
                                    <select class="form-select" id="filtro_tiv_id" name="tiv_id">
                                        <option value="">Todos los tipos</option>
                                        <!-- Se poblará con JS -->
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="filtro_precio_min" class="form-label">
                                        <i class="bi bi-currency-dollar me-1"></i>Precio Mínimo
                                    </label>
                                    <input type="number" class="form-control" id="filtro_precio_min" name="precio_min" placeholder="Ej: 5000">
                                </div>
                                <div class="mb-3">
                                    <label for="filtro_precio_max" class="form-label">
                                        <i class="bi bi-currency-dollar me-1"></i>Precio Máximo
                                    </label>
                                    <input type="number" class="form-control" id="filtro_precio_max" name="precio_max" placeholder="Ej: 20000">
                                </div>
                                <div class="mb-3">
                                    <label for="filtro_anio_min" class="form-label">
                                        <i class="bi bi-calendar-check me-1"></i>Año Desde
                                    </label>
                                    <select class="form-select" id="filtro_anio_min" name="anio_min">
                                        <option value="">Cualquiera</option>
                                        <!-- Se poblará con JS (años) -->
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="filtro_anio_max" class="form-label">
                                        <i class="bi bi-calendar-check me-1"></i>Año Hasta
                                    </label>
                                    <select class="form-select" id="filtro_anio_max" name="anio_max">
                                        <option value="">Cualquiera</option>
                                        <!-- Se poblará con JS (años) -->
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="filtro_provincia" class="form-label">
                                        <i class="bi bi-geo-alt me-1"></i>Provincia
                                    </label>
                                    <select class="form-select" id="filtro_provincia" name="provincia">
                                        <option value="">Todas las provincias</option>
                                        <!-- Se poblará con JS -->
                                    </select>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-funnel-fill me-2"></i>Aplicar Filtros
                                    </button>
                                    <button type="reset" class="btn btn-outline-secondary mt-2" id="resetFiltrosBtn">
                                        <i class="bi bi-arrow-clockwise me-2"></i>Limpiar Filtros
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Columna de Listado de Vehículos -->
                    <div class="col-lg-9">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div id="conteoResultados" class="text-muted">
                                <i class="bi bi-car-front me-2"></i>Mostrando 0 de 0 vehículos
                            </div>
                            <div>
                                <!-- Botón de filtros móvil -->
                                <button class="btn btn-outline-primary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#filtrosMobile">
                                    <i class="bi bi-funnel me-2"></i>Filtros
                                </button>
                            </div>
                        </div>

                        <div id="listaVehiculosUsados" class="row g-4">
                            <div class="col-12 text-center" id="loadingVehiculosListado">
                                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                <p class="mt-3 text-muted">Cargando vehículos usados...</p>
                            </div>
                        </div>
                        
                        <div id="noVehiculosListadoMessage" class="col-12 text-center mt-5 py-5" style="display: none;">
                            <i class="bi bi-search display-1 text-muted mb-3"></i>
                            <h4 class="mt-3">No se encontraron vehículos usados</h4>
                            <p class="text-muted">Intenta ajustar tus criterios de búsqueda o <a href="#" id="verTodosLink" class="text-decoration-none">ver todos los vehículos usados</a>.</p>
                        </div>
                        
                        <nav id="paginacionVehiculosUsados" aria-label="Paginación de vehículos" class="mt-5 d-flex justify-content-center"></nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Offcanvas para filtros móviles -->
        <div class="offcanvas offcanvas-start" tabindex="-1" id="filtrosMobile">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title">
                    <i class="bi bi-filter-circle-fill me-2"></i>Filtrar Vehículos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body">
                <!-- Aquí se copiará el contenido del formulario de filtros -->
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/partials/footer.php'; ?>

    <script src="../PUBLIC/jquery-3.7.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../VISTAS/JS/global.js"></script>
    <script src="../VISTAS/JS/autos_usados.js"></script>

    <script>
        // Animaciones al hacer scroll
        function animateOnScroll() {
            const elements = document.querySelectorAll('.animate-on-scroll');
            
            elements.forEach(element => {
                const elementTop = element.getBoundingClientRect().top;
                const elementVisible = 150;
                
                if (elementTop < window.innerHeight - elementVisible) {
                    element.classList.add('animate-in');
                }
            });
        }

        // Ejecutar animaciones cuando se carga la página y al hacer scroll
        window.addEventListener('scroll', animateOnScroll);
        window.addEventListener('load', animateOnScroll);

        // Animación de entrada para las tarjetas de vehículos
        function animateVehicleCards() {
            const cards = document.querySelectorAll('.card-vehiculo');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
                card.classList.add('animate-on-scroll');
            });
        }

        // Copiar filtros al offcanvas móvil
        document.addEventListener('DOMContentLoaded', function() {
            const filtrosDesktop = document.querySelector('#filtrosForm');
            const filtrosMobile = document.querySelector('#filtrosMobile .offcanvas-body');
            
            if (filtrosDesktop && filtrosMobile) {
                filtrosMobile.innerHTML = filtrosDesktop.outerHTML;
                // Cambiar IDs para evitar duplicados
                const mobileForm = filtrosMobile.querySelector('form');
                if (mobileForm) {
                    mobileForm.id = 'filtrosFormMobile';
                }
            }
        });

        // Efecto de hover mejorado para las tarjetas
        document.addEventListener('DOMContentLoaded', function() {
            // Observador para animar elementos cuando entran en viewport
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in');
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });

            // Observar elementos con animación
            const animatedElements = document.querySelectorAll('.animate-on-scroll');
            animatedElements.forEach(el => observer.observe(el));
        });

        // Función para crear tarjetas de vehículos con animaciones
        function createVehicleCard(vehicle, index) {
            return `
                <div class="col-md-6 col-xl-4 animate-on-scroll" style="animation-delay: ${index * 0.1}s">
                    <div class="card h-100 card-vehiculo">
                        <div class="position-relative">
                            <img src="${vehicle.image}" alt="${vehicle.title}" class="card-img-top card-vehiculo-img-top">
                            <div class="vehicle-status-badge">
                                <span class="badge bg-success px-3 py-2 rounded-pill">Disponible</span>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">${vehicle.title}</h5>
                            <div class="caracteristicas mb-3">
                                <div class="caracteristica-item">
                                    <i class="bi bi-speedometer2 text-primary"></i>
                                    ${
                                    vehicle.motor
                                    }
                                    </div>
                                    \end{code}
                                </div>

                            </div>
                            <div class="card-footer text-center bg-transparent border-top-0">
                                <a href="#" class="btn btn-primary">
                                    <i class="bi bi-eye me-2"></i>Ver Detalles
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            `;
        }
    </script>