<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoMercado Total - Inicio</title>

    <!-- Bootstrap CSS Local -->
    <link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Tus Estilos Personalizados -->
    <link href="../PUBLIC/css/styles.css" rel="stylesheet">
    
    <script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/trefoil.js"></script>
    
    <style>
        /* Animaciones y efectos mejorados */
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

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.8);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
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

        /* Hero Section Mejorada */
        .hero-section {
            position: relative;
            overflow: hidden;
        }

        .hero-video-element {
            object-fit: cover;
            filter: brightness(0.7);
            transition: filter 0.3s ease;
        }

        .hero-content {
            animation: fadeInUp 1.5s ease-out;
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .hero-content h1 {
            background: linear-gradient(135deg, #fff 0%, #ffd700 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: pulse 3s ease-in-out infinite;
        }

        .hero-content p {
            animation: slideInRight 1.5s ease-out 0.3s both;
        }

        .hero-content .btn {
            animation: slideInLeft 1.5s ease-out 0.6s both;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .hero-content .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }

        .hero-content .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s ease;
        }

        .hero-content .btn:hover::before {
            left: 100%;
        }

        /* Tarjetas de autos mejoradas */
        .car-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            border-radius: 15px;
            overflow: hidden;
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            animation: scaleIn 0.8s ease-out;
        }

        .car-card:hover {
            transform: translateY(-15px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .car-card img {
            transition: transform 0.4s ease;
            height: 250px;
            object-fit: cover;
        }

        .car-card:hover img {
            transform: scale(1.1);
        }

        .car-card .card-body {
            position: relative;
        }

        .car-card .card-title {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
        }

        .car-card .btn {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .car-card .btn:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(0, 123, 255, 0.4);
        }

        .car-card .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s ease;
        }

        .car-card .btn:hover::before {
            left: 100%;
        }

        /* Precio con efecto brillante */
        .price-highlight {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
        }

        .price-highlight::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.8), transparent);
            animation: shimmer 3s ease-in-out infinite;
        }

        /* Sección de características mejorada */
        .features-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            position: relative;
            overflow: hidden;
        }

        .features-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="%23007bff" opacity="0.05"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.5;
        }

        .feature-icon {
            animation: float 3s ease-in-out infinite;
            transition: all 0.3s ease;
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.2);
            color: #ffd700 !important;
        }

        .feature-card {
            transition: all 0.3s ease;
            border-radius: 15px;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        /* Animaciones de entrada */
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease-out;
        }

        .animate-on-scroll.animate-in {
            opacity: 1;
            transform: translateY(0);
        }

        /* Título de sección mejorado */
        .section-title {
            position: relative;
            display: inline-block;
            padding-bottom: 15px;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background: linear-gradient(135deg, #007bff 0%, #ffd700 100%);
            border-radius: 2px;
        }

        /* Efectos de carga escalonada para las tarjetas */
        .car-card:nth-child(1) { animation-delay: 0.1s; }
        .car-card:nth-child(2) { animation-delay: 0.2s; }
        .car-card:nth-child(3) { animation-delay: 0.3s; }

        /* Botones del carrusel mejorados */
        .carousel-control-prev,
        .carousel-control-next {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            margin: auto;
            transition: all 0.3s ease;
        }

        .carousel-control-prev:hover,
        .carousel-control-next:hover {
            background: rgba(0, 123, 255, 0.8);
            transform: scale(1.1);
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .hero-content {
                padding: 1rem;
                margin: 0 1rem;
            }
            
            .hero-content h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    
    <div id="page-loader">
        <!-- LDRS Trefoil Loader -->
        <l-trefoil
            size="50"
            stroke="5"
            stroke-length="0.15"
            bg-opacity="0.1"
            speed="1.4"
            color="#0d6efd" 
        ></l-trefoil>
    </div>

    <!-- Barra de Navegación -->
    <header id="navbar-placeholder"></header>

    <!-- Cuerpo Principal -->
    <main class="content-hidden">
        <!-- Sección Hero con Carrusel de Videos -->
        <section class="hero-section text-white text-center d-flex align-items-center justify-content-center">
            <div id="heroVideoCarousel" class="carousel slide" data-bs-ride="carousel" style="width: 100%; height: 100vh;">
                <div class="carousel-inner" style="width: 100%; height: 100%;">
                    <div class="carousel-item active" data-bs-interval="10000">
                        <video class="d-block w-100 h-100 hero-video-element" autoplay muted loop playsinline>
                            <source src="../PUBLIC/Video/The BUGATTI W16 MISTRAL conquers the Mont Ventoux.mp4" type="video/mp4">
                        </video>
                    </div>
                    <div class="carousel-item" data-bs-interval="10000">
                        <video class="d-block w-100 h-100 hero-video-element" autoplay muted loop playsinline>
                            <source src="../PUBLIC/Video/KOENIGSEGG Gemera Configurator Teaser.mp4" type="video/mp4">
                            Tu navegador no soporta videos HTML5.
                        </video>
                    </div>
                    <div class="carousel-item" data-bs-interval="10000">
                        <video class="d-block w-100 h-100 hero-video-element" autoplay muted loop playsinline>
                            <source src="../PUBLIC/Video/Next-Gen Ford Ranger Raptor _ Ford España.mp4" type="video/mp4">
                            Tu navegador no soporta videos HTML5.
                        </video>
                    </div>
                    <div class="carousel-item" data-bs-interval="10000">
                        <video class="d-block w-100 h-100 hero-video-element" autoplay muted loop playsinline>
                            <source src="../PUBLIC/Video/The BUGATTI TOURBILLON_ an automotive icon ‘Pour l’éternité’.mp4" type="video/mp4">
                            Tu navegador no soporta videos HTML5.
                        </video>
                    </div>
                    <div class="carousel-item" data-bs-interval="10000">
                        <video class="d-block w-100 h-100 hero-video-element" autoplay muted loop playsinline>
                            <source src="../PUBLIC/Video/The Porsche 917 that started a legacy.mp4" type="video/mp4">
                            Tu navegador no soporta videos HTML5.
                        </video>
                    </div>
                    <div class="carousel-item" data-bs-interval="10000">
                        <video class="d-block w-100 h-100 hero-video-element" autoplay muted loop playsinline>
                            <source src="../PUBLIC/Video/The new Kia Sportage _ Unveiling Film.mp4" type="video/mp4">
                            Tu navegador no soporta videos HTML5.
                        </video>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#heroVideoCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#heroVideoCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Siguiente</span>
                </button>
            </div>
            <!-- Contenido superpuesto mejorado -->
            <div class="hero-content position-absolute top-50 start-50 translate-middle text-center text-white" style="z-index: 2;">
                <h1 class="display-4 fw-bold mb-4">Encuentra el Auto de Tus Sueños</h1>
                <p class="lead mb-4">Explora nuestro inventario de vehículos nuevos y usados con la mejor calidad y garantía.</p>
                <div class="d-flex flex-column flex-md-row gap-3 justify-content-center">
                    <a href="#" class="btn btn-lg px-4 py-2" style="background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%); color: black; border: none; border-radius: 25px; font-weight: 600;">
                        <i class="bi bi-car-front me-2"></i>Ver Autos Nuevos
                    </a>
                    <a href="autos_usados.php" class="btn btn-lg px-4 py-2" style="background: linear-gradient(135deg, #6c757d 0%, #495057 100%); color: white; border: none; border-radius: 25px; font-weight: 600;">
                        <i class="bi bi-speedometer2 me-2"></i>Ver Autos Usados
                    </a>
                </div>
            </div>
        </section>

        <!-- Sección de Autos Destacados -->
        <section class="container my-5">
            <h2 class="text-center mb-5 section-title animate-on-scroll">Vehículos Destacados</h2>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <!-- Tarjeta 1 - BMW X1 -->
                <div class="col animate-on-scroll">
                    <div class="card h-100 car-card shadow-sm">
                        <div class="position-relative overflow-hidden">
                            <img src="https://images.patiotuerca.com/thumbs/w/1024x768/amz_ptf_ecuador/2892023/1777027/o_o/1777027_1740278343736_730.jpg" alt="BMW X1 2021 blanco" class="card-img-top">
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-success px-3 py-2 rounded-pill">Destacado</span>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">BMW X1 2021</h5>
                            <p class="card-text text-muted small">
                                <i class="bi bi-speedometer2 me-1"></i>30,000 Kms · 
                                <i class="bi bi-geo-alt me-1"></i>Quito
                            </p>
                            <p class="card-text fw-bold fs-4 mt-auto price-highlight">$39,990</p>
                        </div>
                        <div class="card-footer text-center bg-transparent border-top-0">
                           <a href="#" class="btn btn-primary">
                               <i class="bi bi-eye me-2"></i>Ver Detalles
                           </a>
                        </div>
                    </div>
                </div>
                <!-- Tarjeta 2 - Maserati Levante -->
                <div class="col animate-on-scroll">
                    <div class="card h-100 car-card shadow-sm">
                        <div class="position-relative overflow-hidden">
                            <img src="https://images.patiotuerca.com/thumbs/w/1024x768/amz_ptf_ecuador/2025422/1893272/o_o/pt_1893272_7610706.jpg" alt="Maserati Levante GTS 2019" class="card-img-top">
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Premium</span>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">Maserati Levante GTS 2019</h5>
                            <p class="card-text text-muted small">
                                <i class="bi bi-speedometer2 me-1"></i>13,000 Kms · 
                                <i class="bi bi-geo-alt me-1"></i>Quito
                            </p>
                            <p class="card-text fw-bold fs-4 mt-auto price-highlight">$140,000</p>
                        </div>
                        <div class="card-footer text-center bg-transparent border-top-0">
                            <a href="#" class="btn btn-primary">
                                <i class="bi bi-eye me-2"></i>Ver Detalles
                            </a>
                        </div>
                    </div>
                </div>
                <!-- Tarjeta 3 - Audi Q5 Quattro -->
                <div class="col animate-on-scroll">
                    <div class="card h-100 car-card shadow-sm">
                        <div class="position-relative overflow-hidden">
                            <img src="https://images.patiotuerca.com/thumbs/w/1024x768/amz_ptf_ecuador/2082023/1767640/o_o/1767640_1692540850529_189.jpeg" alt="Audi Q5 Quattro 2022 blanco" class="card-img-top">
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-info px-3 py-2 rounded-pill">Nuevo</span>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">Audi Q5 Quattro 2022</h5>
                            <p class="card-text text-muted small">
                                <i class="bi bi-speedometer2 me-1"></i>9,500 Kms · 
                                <i class="bi bi-geo-alt me-1"></i>Quito
                            </p>
                            <p class="card-text fw-bold fs-4 mt-auto price-highlight">$55,000</p>
                        </div>
                        <div class="card-footer text-center bg-transparent border-top-0">
                            <a href="#" class="btn btn-primary">
                                <i class="bi bi-eye me-2"></i>Ver Detalles
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sección de características mejorada -->
        <section class="features-section py-5">
            <div class="container text-center">
                <h2 class="mb-5 section-title animate-on-scroll">¿Por Qué Elegir AutoMercado Total?</h2>
                <div class="row">
                    <div class="col-md-4 mb-4 animate-on-scroll">
                        <div class="feature-card">
                            <i class="bi bi-check-circle-fill fs-1 text-primary mb-3 feature-icon"></i>
                            <h4 class="mb-3">Calidad Garantizada</h4>
                            <p class="text-muted">Todos nuestros vehículos pasan por rigurosas inspecciones técnicas y certificaciones de calidad antes de llegar a tu garaje.</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4 animate-on-scroll">
                        <div class="feature-card">
                            <i class="bi bi-wallet2 fs-1 text-primary mb-3 feature-icon"></i>
                            <h4 class="mb-3">Precios Competitivos</h4>
                            <p class="text-muted">Ofrecemos las mejores ofertas del mercado con planes de financiamiento flexibles adaptados a tu presupuesto.</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4 animate-on-scroll">
                        <div class="feature-card">
                            <i class="bi bi-people-fill fs-1 text-primary mb-3 feature-icon"></i>
                            <h4 class="mb-3">Atención Personalizada</h4>
                            <p class="text-muted">Nuestro equipo de expertos te acompaña en cada paso del proceso para encontrar el vehículo perfecto para ti.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Pie de Página -->
    <?php include __DIR__ . '/partials/footer.php'; ?>

    <!-- Scripts: jQuery, Bootstrap JS, Tu JS Personalizado -->
    <script src="../PUBLIC/jquery-3.7.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../VISTAS/JS/global.js"></script>
    <script src="../VISTAS/JS/inicio.js"></script>

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

        // Mejorar la experiencia del carrusel
        document.addEventListener('DOMContentLoaded', function() {
            const carousel = document.querySelector('#heroVideoCarousel');
            const videos = carousel.querySelectorAll('video');
            
            // Pausar videos no activos
            carousel.addEventListener('slide.bs.carousel', function(e) {
                videos.forEach(video => video.pause());
            });
            
            carousel.addEventListener('slid.bs.carousel', function(e) {
                const activeVideo = e.target.querySelector('.carousel-item.active video');
                if (activeVideo) {
                    activeVideo.play();
                }
            });
        });

        // Efecto parallax sutil en el hero
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const hero = document.querySelector('.hero-section');
            const heroContent = document.querySelector('.hero-content');
            
            if (hero && scrolled < hero.offsetHeight) {
                heroContent.style.transform = `translate(-50%, calc(-50% + ${scrolled * 0.5}px))`;
            }
        });
    </script>

</body>
</html>