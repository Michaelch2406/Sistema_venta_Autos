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
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tus Estilos Personalizados -->
    <link href="../PUBLIC/css/styles.css" rel="stylesheet">
    <!-- Estilos específicos para contacto -->
    <link href="../VISTAS/css/contacto.css" rel="stylesheet">

    <!-- LDRS Loader Script -->
    <script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/trefoil.js"></script>
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Cursor personalizado -->
    <div class="custom-cursor"></div>
    <div class="custom-cursor-dot"></div>

    <!-- Partículas de fondo -->
    <div class="particles-container">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div id="page-loader" class="enhanced-loader">
        <div class="loader-content">
            <l-trefoil size="50" stroke="5" stroke-length="0.15" bg-opacity="0.1" speed="1.4" color="#0d6efd"></l-trefoil>
            <p class="loader-text">Cargando experiencia de contacto...</p>
        </div>
    </div>

    <!-- Placeholder para la Barra de Navegación -->
    <header id="navbar-placeholder"></header>

    <!-- Contenido Principal de la Página de Contacto -->
    <main class="flex-grow-1 content-hidden">
        <!-- Sección Hero Mejorada -->
        <section class="contact-hero-enhanced py-5 text-white position-relative overflow-hidden">
            <div class="hero-background-animation"></div>
            <div class="hero-overlay"></div>
            <div class="container text-center position-relative">
                <div class="hero-content">
                    <h1 class="display-4 fw-bold hero-title" data-aos="fade-up">
                        <span class="typing-text">Contáctanos</span>
                    </h1>
                    <p class="lead col-md-8 mx-auto hero-subtitle" data-aos="fade-up" data-aos-delay="200">
                        Estamos aquí para ayudarte. Ya sea que tengas preguntas sobre nuestros vehículos, financiamiento o simplemente quieras saber más, no dudes en ponerte en contacto.
                    </p>
                    <div class="hero-cta" data-aos="fade-up" data-aos-delay="400">
                        <a href="#contact-form" class="btn btn-primary btn-lg btn-enhanced smooth-scroll">
                            <i class="bi bi-chat-dots me-2"></i>
                            Enviar Mensaje
                        </a>
                        <a href="tel:+59322999999" class="btn btn-outline-light btn-lg btn-enhanced ms-3">
                            <i class="bi bi-telephone me-2"></i>
                            Llamar Ahora
                        </a>
                    </div>
                </div>
            </div>
            <!-- Elementos decorativos -->
            <div class="hero-decoration">
                <div class="floating-car">
                    <i class="bi bi-car-front"></i>
                </div>
            </div>
        </section>

        <section class="contact-content-enhanced py-5">
            <div class="container">
                <div class="row g-5">
                    <!-- Columna de Información de Contacto -->
                    <div class="col-lg-5 col-md-6" data-aos="slide-right">
                        <div class="contact-info-wrapper">
                            <h2 class="mb-4 fw-light section-title">
                                <span class="title-highlight">Nuestra</span> Información
                            </h2>
                            
                            <div class="contact-info-item enhanced-card" data-aos="fade-up" data-aos-delay="100">
                                <div class="info-icon">
                                    <i class="bi bi-geo-alt-fill"></i>
                                </div>
                                <div class="info-content">
                                    <h5 class="mb-1">Dirección</h5>
                                    <p class="text-muted mb-0">Av. Principal 123, Sector Automotriz<br>Quito, Ecuador</p>
                                </div>
                                <div class="info-hover-effect"></div>
                            </div>

                            <div class="contact-info-item enhanced-card" data-aos="fade-up" data-aos-delay="200">
                                <div class="info-icon">
                                    <i class="bi bi-telephone-fill"></i>
                                </div>
                                <div class="info-content">
                                    <h5 class="mb-1">Teléfono</h5>
                                    <p class="text-muted mb-0">
                                        <a href="tel:+59322999999" class="enhanced-link">(02) 299-9999</a>
                                    </p>
                                    <p class="text-muted mb-0">
                                        <a href="tel:+593991234567" class="enhanced-link">099 123 4567</a> (WhatsApp)
                                    </p>
                                </div>
                                <div class="info-hover-effect"></div>
                            </div>

                            <div class="contact-info-item enhanced-card" data-aos="fade-up" data-aos-delay="300">
                                <div class="info-icon">
                                    <i class="bi bi-envelope-fill"></i>
                                </div>
                                <div class="info-content">
                                    <h5 class="mb-1">Correo Electrónico</h5>
                                    <p class="text-muted mb-0">
                                        <a href="mailto:info@automercadototal.com" class="enhanced-link">info@automercadototal.com</a>
                                    </p>
                                    <p class="text-muted mb-0">
                                        <a href="mailto:ventas@automercadototal.com" class="enhanced-link">ventas@automercadototal.com</a>
                                    </p>
                                </div>
                                <div class="info-hover-effect"></div>
                            </div>

                            <div class="contact-info-item enhanced-card" data-aos="fade-up" data-aos-delay="400">
                                <div class="info-icon">
                                    <i class="bi bi-clock-fill"></i>
                                </div>
                                <div class="info-content">
                                    <h5 class="mb-1">Horario de Atención</h5>
                                    <p class="text-muted mb-0">Lunes a Viernes: 9:00 AM - 6:00 PM</p>
                                    <p class="text-muted mb-0">Sábados: 10:00 AM - 2:00 PM</p>
                                </div>
                                <div class="info-hover-effect"></div>
                            </div>

                            <div class="social-section" data-aos="fade-up" data-aos-delay="500">
                                <h4 class="mt-5 mb-3 fw-light">Síguenos</h4>
                                <div class="social-icons-enhanced">
                                    <a href="#" class="social-icon facebook" title="Facebook">
                                        <i class="bi bi-facebook"></i>
                                        <span class="social-tooltip">Facebook</span>
                                    </a>
                                    <a href="#" class="social-icon instagram" title="Instagram">
                                        <i class="bi bi-instagram"></i>
                                        <span class="social-tooltip">Instagram</span>
                                    </a>
                                    <a href="#" class="social-icon twitter" title="Twitter/X">
                                        <i class="bi bi-twitter-x"></i>
                                        <span class="social-tooltip">Twitter</span>
                                    </a>
                                    <a href="#" class="social-icon linkedin" title="LinkedIn">
                                        <i class="bi bi-linkedin"></i>
                                        <span class="social-tooltip">LinkedIn</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna del Formulario de Contacto -->
                    <div class="col-lg-7 col-md-6" data-aos="slide-left">
                        <div class="form-container-enhanced">
                            <div class="card enhanced-form-card border-0">
                                <div class="card-body p-4 p-md-5">
                                    <div class="form-header text-center mb-4">
                                        <h2 class="fw-light">Envíanos un Mensaje</h2>
                                        <p class="text-muted">Completa el formulario y te responderemos pronto</p>
                                        <div class="form-progress">
                                            <div class="progress-bar" id="formProgress"></div>
                                        </div>
                                    </div>

                                    <form id="contactForm" class="enhanced-form needs-validation" novalidate>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="floating-label-group">
                                                    <input type="text" class="form-control enhanced-input" id="contactName" required>
                                                    <label for="contactName" class="floating-label">Nombre Completo</label>
                                                    <div class="input-border"></div>
                                                    <div class="validation-icon">
                                                        <i class="bi bi-check-circle-fill text-success"></i>
                                                        <i class="bi bi-x-circle-fill text-danger"></i>
                                                    </div>
                                                    <div class="invalid-feedback">Por favor, ingresa tu nombre.</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="floating-label-group">
                                                    <input type="email" class="form-control enhanced-input" id="contactEmail" required>
                                                    <label for="contactEmail" class="floating-label">Correo Electrónico</label>
                                                    <div class="input-border"></div>
                                                    <div class="validation-icon">
                                                        <i class="bi bi-check-circle-fill text-success"></i>
                                                        <i class="bi bi-x-circle-fill text-danger"></i>
                                                    </div>
                                                    <div class="invalid-feedback">Por favor, ingresa un correo válido.</div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="floating-label-group">
                                                    <input type="tel" class="form-control enhanced-input" id="contactPhone">
                                                    <label for="contactPhone" class="floating-label">Teléfono <span class="text-muted">(Opcional)</span></label>
                                                    <div class="input-border"></div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="floating-label-group">
                                                    <select class="form-control enhanced-input" id="contactSubject" required>
                                                        <option value="">Selecciona un asunto</option>
                                                        <option value="informacion-vehiculos">Información sobre vehículos</option>
                                                        <option value="financiamiento">Opciones de financiamiento</option>
                                                        <option value="test-drive">Agendar test drive</option>
                                                        <option value="servicio-tecnico">Servicio técnico</option>
                                                        <option value="otros">Otros</option>
                                                    </select>
                                                    <label for="contactSubject" class="floating-label">Asunto</label>
                                                    <div class="input-border"></div>
                                                    <div class="validation-icon">
                                                        <i class="bi bi-check-circle-fill text-success"></i>
                                                        <i class="bi bi-x-circle-fill text-danger"></i>
                                                    </div>
                                                    <div class="invalid-feedback">Por favor, selecciona un asunto.</div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="floating-label-group">
                                                    <textarea class="form-control enhanced-input" id="contactMessage" rows="5" required></textarea>
                                                    <label for="contactMessage" class="floating-label">Mensaje</label>
                                                    <div class="input-border"></div>
                                                    <div class="validation-icon">
                                                        <i class="bi bi-check-circle-fill text-success"></i>
                                                        <i class="bi bi-x-circle-fill text-danger"></i>
                                                    </div>
                                                    <div class="invalid-feedback">Por favor, escribe tu mensaje.</div>
                                                    <div class="character-count">
                                                        <span id="messageCount">0</span>/500 caracteres
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-check enhanced-checkbox">
                                                    <input class="form-check-input" type="checkbox" id="privacyPolicy" required>
                                                    <label class="form-check-label" for="privacyPolicy">
                                                        Acepto la <a href="#" class="enhanced-link">política de privacidad</a> y el tratamiento de mis datos
                                                    </label>
                                                    <div class="invalid-feedback">Debes aceptar la política de privacidad.</div>
                                                </div>
                                            </div>
                                            <div class="col-12 d-grid">
                                                <button class="btn btn-primary btn-lg btn-enhanced-submit" type="submit" id="submitBtn">
                                                    <span class="btn-text">
                                                        <i class="bi bi-send me-2"></i>
                                                        Enviar Mensaje
                                                    </span>
                                                    <span class="btn-loading d-none">
                                                        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                                        Enviando...
                                                    </span>
                                                    <span class="btn-success d-none">
                                                        <i class="bi bi-check-circle me-2"></i>
                                                        ¡Enviado!
                                                    </span>
                                                    <div class="btn-ripple"></div>
                                                </button>
                                            </div>
                                        </div>
                                    </form>

                                    <!-- Mensaje de éxito -->
                                    <div class="success-message d-none" id="successMessage">
                                        <div class="success-animation">
                                            <div class="checkmark">
                                                <svg class="checkmark-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                                                    <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
                                                    <path class="checkmark-check" fill="none" d="m14.1 27.2l7.1 7.2 16.7-16.8"/>
                                                </svg>
                                            </div>
                                            <h3>¡Mensaje Enviado!</h3>
                                            <p>Gracias por contactarnos. Te responderemos pronto.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección de Mapa Mejorada -->
                <div class="row mt-5 pt-4" data-aos="fade-up">
                    <div class="col-12">
                        <div class="map-section-enhanced">
                            <h2 class="text-center mb-4 fw-light section-title">
                                <span class="title-highlight">Encuéntranos</span> Fácilmente
                            </h2>
                            <div class="map-container-enhanced">
                                <div class="map-placeholder-enhanced">
                                    <div class="map-overlay">
                                        <div class="map-info">
                                            <h4><i class="bi bi-geo-alt-fill me-2"></i>AutoMercado Total</h4>
                                            <p>Av. Principal 123, Sector Automotriz<br>Quito, Ecuador</p>
                                            <a href="#" class="btn btn-primary btn-sm">
                                                <i class="bi bi-map me-2"></i>Ver en Google Maps
                                            </a>
                                        </div>
                                    </div>
                                    <div class="map-placeholder bg-light border rounded">
                                        <div class="map-content">
                                            <i class="bi bi-geo-alt-fill text-primary"></i>
                                            <p class="text-muted">Aquí se mostraría un mapa interactivo.</p>
                                            <small class="text-muted">Integra Google Maps API para una experiencia completa</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección de Estadísticas -->
                <div class="row mt-5 pt-4" data-aos="fade-up">
                    <div class="col-12">
                        <div class="stats-section">
                            <h2 class="text-center mb-5 fw-light section-title">
                                <span class="title-highlight">Confía</span> en Nosotros
                            </h2>
                            <div class="row text-center">
                                <div class="col-md-3 col-6 mb-4">
                                    <div class="stat-item" data-aos="zoom-in" data-aos-delay="100">
                                        <div class="stat-icon">
                                            <i class="bi bi-car-front"></i>
                                        </div>
                                        <div class="stat-number" data-count="500">0</div>
                                        <div class="stat-label">Vehículos Vendidos</div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6 mb-4">
                                    <div class="stat-item" data-aos="zoom-in" data-aos-delay="200">
                                        <div class="stat-icon">
                                            <i class="bi bi-people"></i>
                                        </div>
                                        <div class="stat-number" data-count="1200">0</div>
                                        <div class="stat-label">Clientes Satisfechos</div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6 mb-4">
                                    <div class="stat-item" data-aos="zoom-in" data-aos-delay="300">
                                        <div class="stat-icon">
                                            <i class="bi bi-award"></i>
                                        </div>
                                        <div class="stat-number" data-count="15">0</div>
                                        <div class="stat-label">Años de Experiencia</div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6 mb-4">
                                    <div class="stat-item" data-aos="zoom-in" data-aos-delay="400">
                                        <div class="stat-icon">
                                            <i class="bi bi-star-fill"></i>
                                        </div>
                                        <div class="stat-number" data-count="98">0</div>
                                        <div class="stat-label">% Satisfacción</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Pie de Página -->
    <?php include __DIR__ . '/partials/footer.php'; ?>

    <!-- Botón de scroll to top -->
    <button class="scroll-to-top" id="scrollToTop">
        <i class="bi bi-arrow-up"></i>
    </button>

    <!-- Scripts -->
    <script src="../PUBLIC/jquery-3.7.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../VISTAS/JS/global.js"></script>
    <script src="../VISTAS/JS/contacto.js"></script>

</body>
</html>

