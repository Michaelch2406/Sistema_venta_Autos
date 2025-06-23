<?php
session_start(); 
/*
if (isset($_SESSION['usu_id'])) {
    header('Location: escritorio.php');
    exit();
}
*/
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
	
	<script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/trefoil.js"></script>
	
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
        <!-- Sección Hero con Video -->
        <section class="hero-section text-white text-center d-flex align-items-center justify-content-center">
            <div class="position-relative overflow-hidden" style="height: 100vh;">
           <video autoplay muted loop  class="w-100 h-100">
              <source src="../PUBLIC/Video/The BUGATTI W16 MISTRAL conquers the Mont Ventoux.mp4" type="video/mp4">
          </video>
                <div class="hero-content position-absolute top-50 start-50 translate-middle text-center text-white" style="z-index: 2;">
                    <h1 class="display-4 fw-bold">Encuentra el Auto de Tus Sueños</h1>
                    <p class="lead">Explora nuestro inventario de vehículos nuevos y usados.</p>
                    <a href="#" class="btn btn-lg me-2" style="background: yellow; color: black;">Ver Autos Nuevos</a>
                    <a href="#" class="btn btn-secondary btn-lg">Ver Autos Usados</a>
                </div>
            </div>
        </section>

        <!-- Sección de Autos Destacados -->
        <section class="container my-5">
            <h2 class="text-center mb-4">Vehículos Destacados</h2>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <!-- Tarjeta 1 - BMW X1 -->
                <div class="col">
                    <div class="card h-100 car-card shadow-sm">
                        <img src="https://images.patiotuerca.com/thumbs/w/1024x768/amz_ptf_ecuador/2892023/1777027/o_o/1777027_1740278343736_730.jpg" alt="BMW X1 2021 blanco" class="card-img-top">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">BMW X1 2021</h5>
                            <p class="card-text text-muted small">30000 Kms · Quito</p>
                            <p class="card-text fw-bold fs-5 mt-auto">$39,990</p>
                        </div>
                        <div class="card-footer text-center bg-transparent border-top-0">
                           <a href="#" class="btn btn-outline-primary">Ver Detalles</a>
                        </div>
                    </div>
                </div>
                <!-- Tarjeta 2 - Maserati Levante -->
                <div class="col">
                    <div class="card h-100 car-card shadow-sm">
                        <img src="https://images.patiotuerca.com/thumbs/w/1024x768/amz_ptf_ecuador/2025422/1893272/o_o/pt_1893272_7610706.jpg" alt="Maserati Levante GTS 2019" class="card-img-top">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">Maserati Levante GTS 2019</h5>
                            <p class="card-text text-muted small">13000 Kms · Quito</p>
                            <p class="card-text fw-bold fs-5 mt-auto">$140,000</p>
                        </div>
                        <div class="card-footer text-center bg-transparent border-top-0">
                            <a href="#" class="btn btn-outline-primary">Ver Detalles</a>
                        </div>
                    </div>
                </div>
                <!-- Tarjeta 3 - Audi Q5 Quattro -->
                <div class="col">
                    <div class="card h-100 car-card shadow-sm">
                        <img src="https://images.patiotuerca.com/thumbs/w/1024x768/amz_ptf_ecuador/2082023/1767640/o_o/1767640_1692540850529_189.jpeg" alt="Audi Q5 Quattro 2022 blanco" class="card-img-top" style="height: 270px; object-fit: cover;"> <!-- Ajuste inline por height de img -->
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">Audi Q5 Quattro 2022</h5>
                            <p class="card-text text-muted small">9500 Kms · Quito</p>
                            <p class="card-text fw-bold fs-5 mt-auto">$55,000</p>
                        </div>
                        <div class="card-footer text-center bg-transparent border-top-0">
                            <a href="#" class="btn btn-outline-primary">Ver Detalles</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Otra sección (ej. Por qué elegirnos) -->
        <section class="bg-light py-5">
            <div class="container text-center">
                <h2 class="mb-4">¿Por Qué Elegir AutoMercado Total?</h2>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <i class="bi bi-check-circle-fill fs-1 text-primary mb-2"></i>
                        <h4>Calidad Garantizada</h4>
                        <p>Vehículos inspeccionados y certificados.</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <i class="bi bi-wallet2 fs-1 text-primary mb-2"></i>
                        <h4>Precios Competitivos</h4>
                        <p>Las mejores ofertas del mercado.</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <i class="bi bi-people-fill fs-1 text-primary mb-2"></i>
                        <h4>Atención Personalizada</h4>
                        <p>Te ayudamos a encontrar tu auto ideal.</p>
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
    <script src="../VISTAS/JS/global.js"></script> <!-- Script global primero -->
    <script src="../VISTAS/JS/login.js"></script>   <!-- Script específico de la página después -->

</body>
</html>