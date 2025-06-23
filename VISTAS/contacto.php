<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto - AutoMercado Total</title>

    <!-- Bootstrap CSS Local -->
    <link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Tus Estilos Personalizados -->
    <link href="../PUBLIC/css/styles.css" rel="stylesheet">

    <!-- LDRS Loader Script (si decides usarlo en todas las páginas) -->
    <script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/trefoil.js"></script>
</head>
<body class="d-flex flex-column min-vh-100">

    <div id="page-loader">
        <l-trefoil size="50" stroke="5" stroke-length="0.15" bg-opacity="0.1" speed="1.4" color="#0d6efd"></l-trefoil>
    </div>

    <!-- Placeholder para la Barra de Navegación -->
    <header id="navbar-placeholder"></header>

    <!-- Contenido Principal de la Página de Contacto -->
    <main class="flex-grow-1 content-hidden">
        <section class="contact-hero py-5 text-white" style="background-color: #2c3e50;"> <!-- Un azul oscuro elegante -->
            <div class="container text-center">
                <h1 class="display-4 fw-bold">Contáctanos</h1>
                <p class="lead col-md-8 mx-auto">
                    Estamos aquí para ayudarte. Ya sea que tengas preguntas sobre nuestros vehículos, financiamiento o simplemente quieras saber más, no dudes en ponerte en contacto.
                </p>
            </div>
        </section>

        <section class="contact-content py-5">
            <div class="container">
                <div class="row g-5">
                    <!-- Columna de Información de Contacto -->
                    <div class="col-lg-5 col-md-6">
                        <h2 class="mb-4 fw-light">Nuestra Información</h2>
                        <div class="contact-info-item d-flex align-items-start mb-4">
                            <i class="bi bi-geo-alt-fill fs-2 text-primary me-3"></i>
                            <div>
                                <h5 class="mb-1">Dirección</h5>
                                <p class="text-muted mb-0">Av. Principal 123, Sector Automotriz<br>Quito, Ecuador</p>
                            </div>
                        </div>
                        <div class="contact-info-item d-flex align-items-start mb-4">
                            <i class="bi bi-telephone-fill fs-2 text-primary me-3"></i>
                            <div>
                                <h5 class="mb-1">Teléfono</h5>
                                <p class="text-muted mb-0"><a href="tel:+59322999999" class="text-decoration-none text-muted">(02) 299-9999</a></p>
                                <p class="text-muted mb-0"><a href="tel:+593991234567" class="text-decoration-none text-muted">099 123 4567</a> (WhatsApp)</p>
                            </div>
                        </div>
                        <div class="contact-info-item d-flex align-items-start mb-4">
                            <i class="bi bi-envelope-fill fs-2 text-primary me-3"></i>
                            <div>
                                <h5 class="mb-1">Correo Electrónico</h5>
                                <p class="text-muted mb-0"><a href="mailto:info@automercadototal.com" class="text-decoration-none text-muted">info@automercadototal.com</a></p>
                                <p class="text-muted mb-0"><a href="mailto:ventas@automercadototal.com" class="text-decoration-none text-muted">ventas@automercadototal.com</a></p>
                            </div>
                        </div>
                        <div class="contact-info-item d-flex align-items-start mb-4">
                            <i class="bi bi-clock-fill fs-2 text-primary me-3"></i>
                            <div>
                                <h5 class="mb-1">Horario de Atención</h5>
                                <p class="text-muted mb-0">Lunes a Viernes: 9:00 AM - 6:00 PM</p>
                                <p class="text-muted mb-0">Sábados: 10:00 AM - 2:00 PM</p>
                            </div>
                        </div>
                        <h4 class="mt-5 mb-3 fw-light">Síguenos</h4>
                        <div class="social-icons">
                            <a href="#" class="text-primary fs-3 me-3" title="Facebook"><i class="bi bi-facebook"></i></a>
                            <a href="#" class="text-primary fs-3 me-3" title="Instagram"><i class="bi bi-instagram"></i></a>
                            <a href="#" class="text-primary fs-3" title="Twitter/X"><i class="bi bi-twitter-x"></i></a>
                        </div>
                    </div>

                    <!-- Columna del Formulario de Contacto -->
                    <div class="col-lg-7 col-md-6">
                        <div class="card shadow-lg border-0">
                            <div class="card-body p-4 p-md-5">
                                <h2 class="mb-4 fw-light text-center">Envíanos un Mensaje</h2>
                                <form id="contactForm" class="row g-3 needs-validation" novalidate>
                                    <div class="col-md-6">
                                        <label for="contactName" class="form-label">Nombre Completo</label>
                                        <input type="text" class="form-control form-control-lg" id="contactName" required>
                                        <div class="invalid-feedback">Por favor, ingresa tu nombre.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="contactEmail" class="form-label">Correo Electrónico</label>
                                        <input type="email" class="form-control form-control-lg" id="contactEmail" required>
                                        <div class="invalid-feedback">Por favor, ingresa un correo válido.</div>
                                    </div>
                                    <div class="col-12">
                                        <label for="contactPhone" class="form-label">Teléfono <span class="text-muted">(Opcional)</span></label>
                                        <input type="tel" class="form-control form-control-lg" id="contactPhone">
                                    </div>
                                    <div class="col-12">
                                        <label for="contactSubject" class="form-label">Asunto</label>
                                        <input type="text" class="form-control form-control-lg" id="contactSubject" required>
                                        <div class="invalid-feedback">Por favor, ingresa un asunto.</div>
                                    </div>
                                    <div class="col-12">
                                        <label for="contactMessage" class="form-label">Mensaje</label>
                                        <textarea class="form-control form-control-lg" id="contactMessage" rows="5" required></textarea>
                                        <div class="invalid-feedback">Por favor, escribe tu mensaje.</div>
                                    </div>
                                    <div class="col-12 d-grid">
                                        <button class="btn btn-primary btn-lg" type="submit">Enviar Mensaje</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección Opcional: Mapa (Placeholder) -->
                <div class="row mt-5 pt-4">
                    <div class="col-12">
                        <h2 class="text-center mb-4 fw-light">Encuéntranos</h2>
                        <div class="map-placeholder bg-light border rounded" style="height: 400px; display: flex; align-items: center; justify-content: center;">
                            <!-- Para un mapa real, deberías integrar Google Maps API o similar aquí -->
                            <p class="text-muted">Aquí se mostraría un mapa interactivo.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Pie de Página -->
    <?php include __DIR__ . '/partials/footer.php'; ?>

    <!-- Scripts -->
    <script src="../PUBLIC/jquery-3.7.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../VISTAS/JS/global.js"></script>
    <script src="../VISTAS/JS/contacto.js"></script> <!-- JS específico para la página de contacto -->

</body>
</html>