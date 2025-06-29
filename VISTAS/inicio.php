<?php
require_once __DIR__ . '/../MODELOS/vehiculos_m.php';

$vehiculo_model = new Vehiculo();
$vehiculos_nuevos_destacados = $vehiculo_model->getVehiculosDestacados('nuevo', 3);
$vehiculos_usados_destacados = $vehiculo_model->getVehiculosDestacados('usado', 3);

/**
 * Función auxiliar para renderizar una tarjeta de vehículo.
 * Esto evita repetir código HTML.
 */
function render_vehicle_card($vehiculo) {
    $precio_formateado = number_format((float)$vehiculo['veh_precio'], 0, ',', '.');
    $kilometraje_texto = ($vehiculo['veh_condicion'] == 'usado')
        ? '<i class="bi bi-speedometer2 me-1"></i>' . number_format((int)$vehiculo['veh_kilometraje']) . ' Kms'
        : '<i class="bi bi-star-fill me-1"></i>0 Kms';

    $etiqueta = '';
    if ($vehiculo['veh_condicion'] == 'nuevo') {
        $etiqueta = '<span class="badge bg-info px-3 py-2 rounded-pill">Nuevo</span>';
    } else {
        // Puedes añadir más lógica aquí si tienes etiquetas como 'Destacado' o 'Premium'
        $etiqueta = '<span class="badge bg-success px-3 py-2 rounded-pill">Seminuevo</span>';
    }

    echo '
    <div class="col animate-on-scroll">
        <div class="card h-100 car-card shadow-sm">
            <div class="position-relative overflow-hidden">
                <a href="detalle_vehiculo.php?id=' . htmlspecialchars($vehiculo['veh_id']) . '">
                    <img src="' . htmlspecialchars($vehiculo['imagen_principal_url']) . '" alt="' . htmlspecialchars($vehiculo['mar_nombre'] . ' ' . $vehiculo['mod_nombre']) . '" class="card-img-top">
                </a>
                <div class="position-absolute top-0 end-0 m-3">
                    ' . $etiqueta . '
                </div>
            </div>
            <div class="card-body d-flex flex-column">
                <h5 class="card-title"><a href="detalle_vehiculo.php?id=' . htmlspecialchars($vehiculo['veh_id']) . '" class="text-decoration-none">' . htmlspecialchars($vehiculo['mar_nombre'] . ' ' . $vehiculo['mod_nombre']) . '</a></h5>
                <p class="card-text text-muted small">
                    ' . $kilometraje_texto . ' · 
                    <i class="bi bi-geo-alt me-1"></i>' . htmlspecialchars($vehiculo['veh_ubicacion_ciudad']) . '
                </p>
                <p class="card-text fw-bold fs-4 mt-auto price-highlight">$' . $precio_formateado . '</p>
            </div>
            <div class="card-footer text-center bg-transparent border-top-0">
               <a href="detalle_vehiculo.php?id=' . htmlspecialchars($vehiculo['veh_id']) . '" class="btn btn-primary">
                   <i class="bi bi-eye me-2"></i>Ver Detalles
               </a>
            </div>
        </div>
    </div>';
}
?>
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
    <link href="../VISTAS/css/inicio.css" rel="stylesheet">
    
    <script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/trefoil.js"></script>
</head>
<body>
    
    <div id="page-loader">
        <l-trefoil size="50" stroke="5" stroke-length="0.15" bg-opacity="0.1" speed="1.4" color="#0d6efd"></l-trefoil>
    </div>

    <!-- Barra de Navegación -->
    <header id="navbar-placeholder"></header>

    <!-- Cuerpo Principal -->
    <main class="content-hidden">
        <!-- Sección Hero con Carrusel de Videos -->
        <section class="hero-section text-white text-center d-flex align-items-center justify-content-center">
            <div id="heroVideoCarousel" class="carousel slide" data-bs-ride="false" style="width: 100%; height: 100vh;">
                <div class="carousel-inner" style="width: 100%; height: 100%;">
                    
                    <!-- CAMBIOS: Se eliminó data-bs-interval y el atributo 'loop' del video -->
                    <div class="carousel-item active">
                        <video class="d-block w-100 h-100 hero-video-element" autoplay muted playsinline>
                            <source src="../PUBLIC/Video/The BUGATTI W16 MISTRAL conquers the Mont Ventoux.mp4" type="video/mp4">
                        </video>
                    </div>
                    <div class="carousel-item">
                        <video class="d-block w-100 h-100 hero-video-element" autoplay muted playsinline>
                            <source src="../PUBLIC/Video/KOENIGSEGG Gemera Configurator Teaser.mp4" type="video/mp4">
                        </video>
                    </div>
                    <div class="carousel-item">
                        <video class="d-block w-100 h-100 hero-video-element" autoplay muted playsinline>
                            <source src="../PUBLIC/Video/Next-Gen Ford Ranger Raptor _ Ford España.mp4" type="video/mp4">
                        </video>
                    </div>
                    <div class="carousel-item">
                        <video class="d-block w-100 h-100 hero-video-element" autoplay muted playsinline>
                            <source src="../PUBLIC/Video/The BUGATTI TOURBILLON_ an automotive icon ‘Pour l’éternité’.mp4" type="video/mp4">
                        </video>
                    </div>
                    <div class="carousel-item">
                        <video class="d-block w-100 h-100 hero-video-element" autoplay muted playsinline>
                            <source src="../PUBLIC/Video/The Porsche 917 that started a legacy.mp4" type="video/mp4">
                        </video>
                    </div>
                    <div class="carousel-item">
                        <video class="d-block w-100 h-100 hero-video-element" autoplay muted playsinline>
                            <source src="../PUBLIC/Video/The new Kia Sportage _ Unveiling Film.mp4" type="video/mp4">
                        </video>
                    </div>
                    <div class="carousel-item">
                        <video class="d-block w-100 h-100 hero-video-element" autoplay muted playsinline>
                            <source src="../PUBLIC/Video/Esto Es Nissan KICKS I Detona Tu Instinto.mp4" type="video/mp4">
                        </video>
                    </div>
                    <div class="carousel-item">
                        <video class="d-block w-100 h-100 hero-video-element" autoplay muted playsinline>
                            <source src="../PUBLIC/Video/Chevy Silverado No le dice No a Nada Comercial Chevrolet.mp4" type="video/mp4">
                        </video>
                    </div>
                    <div class="carousel-item">
                        <video class="d-block w-100 h-100 hero-video-element" autoplay muted playsinline>
                            <source src="../PUBLIC/Video/In its Element The Pagani Huayra R.mp4" type="video/mp4">
                        </video>
                    </div>
                    <div class="carousel-item">
                        <video class="d-block w-100 h-100 hero-video-element" autoplay muted playsinline>
                            <source src="../PUBLIC/Video/ALFA ROMEO TONALE PLUG-IN HYBRID Q4_.mp4" type="video/mp4">
                        </video>
                    </div>
                    <div class="carousel-item">
                        <video class="d-block w-100 h-100 hero-video-element" autoplay muted playsinline>
                            <source src="../PUBLIC/Video/ford_mustang.mp4" type="video/mp4">
                        </video>
                    </div>
                    <div class="carousel-item">
                        <video class="d-block w-100 h-100 hero-video-element" autoplay muted playsinline>
                            <source src="../PUBLIC/Video/Pagani Utopia - An emotion taking shape..mp4" type="video/mp4">
                        </video>
                    </div>
                    <div class="carousel-item">
                        <video class="d-block w-100 h-100 hero-video-element" autoplay muted playsinline>
                            <source src="../PUBLIC/Video/Apollo IE Story.mp4" type="video/mp4">
                        </video>
                    </div>
                    <div class="carousel-item">
                        <video class="d-block w-100 h-100 hero-video-element" autoplay muted playsinline>
                            <source src="../PUBLIC/Video/Maserati GranTurismo MC Rosso Magma 4K.mp4" type="video/mp4">
                        </video>
                    </div>
                    <div class="carousel-item">
                        <video class="d-block w-100 h-100 hero-video-element" autoplay muted playsinline>
                            <source src="../PUBLIC/Video/Maserati, Presents, Tales of GranTurismo & GranCabrio, 2020.mp4" type="video/mp4">
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
        <?php if (!empty($vehiculos_nuevos_destacados)): ?>
        <section class="container my-5">
            <h2 class="text-center mb-5 section-title animate-on-scroll">Novedades 0km</h2>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach ($vehiculos_nuevos_destacados as $vehiculo): ?>
                    <?php render_vehicle_card($vehiculo); ?>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- Sección de Seminuevos Destacados -->
        <?php if (!empty($vehiculos_usados_destacados)): ?>
        <section class="container my-5">
            <h2 class="text-center mb-5 section-title animate-on-scroll">Seminuevos Destacados</h2>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach ($vehiculos_usados_destacados as $vehiculo): ?>
                    <?php render_vehicle_card($vehiculo); ?>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

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
    
    <!-- El script en línea ha sido eliminado y su lógica movida a inicio.js -->

</body>
</html>