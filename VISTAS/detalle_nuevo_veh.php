<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle Vehículo Nuevo y Cotización - AutoMercado Total</title>
    <link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Roboto:wght@300;400&display=swap" rel="stylesheet">
    <link href="../PUBLIC/css/styles.css" rel="stylesheet"> <!-- Estilos Globales -->
    <link href="./CSS/autos_nuevos.css" rel="stylesheet"> <!-- Estilos base de autos nuevos -->
    <link href="./CSS/detalle_nuevo_veh.css" rel="stylesheet"> <!-- Estilos Específicos para esta página -->
    <script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/trefoil.js"></script>
</head>
<body class="luxury-theme d-flex flex-column min-vh-100">
    <div id="page-loader">
        <l-trefoil size="60" stroke="5" stroke-length="0.15" bg-opacity="0.1" speed="1.4" color="var(--luxury-gold)"></l-trefoil>
    </div>

    <header id="navbar-placeholder"></header>

    <main class="flex-grow-1 content-hidden">
        <div class="container mt-5 pt-5">
            <div id="detalleVehiculoLoader" class="text-center py-5">
                <l-trefoil size="80" stroke="6" stroke-length="0.18" bg-opacity="0.1" speed="1.2" color="var(--luxury-gold)"></l-trefoil>
                <p class="mt-3 lead text-light">Cargando detalles del vehículo...</p>
            </div>

            <div id="detalleVehiculoContent" style="display: none;">
                <!-- Fila Principal: Imagen a la Izquierda, Detalles y Cotización a la Derecha -->
                <div class="row g-5">
                    <!-- Columna Izquierda: Galería de Imágenes -->
                    <div class="col-lg-7">
                        <div id="imagenPrincipalContainer" class="mb-3 position-relative">
                            <img src="" id="imagenPrincipalVehiculo" class="img-fluid rounded shadow-lg" alt="Imagen Principal del Vehículo" style="max-height: 500px; width: 100%; object-fit: cover;">
                            <button id="btnPrevImagen" class="btn btn-dark btn-sm position-absolute top-50 start-0 translate-middle-y ms-2 opacity-75"><i class="bi bi-chevron-left"></i></button>
                            <button id="btnNextImagen" class="btn btn-dark btn-sm position-absolute top-50 end-0 translate-middle-y me-2 opacity-75"><i class="bi bi-chevron-right"></i></button>
                        </div>
                        <div id="galeriaThumbnails" class="d-flex flex-wrap gap-2 justify-content-center">
                            <!-- Thumbnails se cargarán aquí por JS -->
                        </div>
                    </div>

                    <!-- Columna Derecha: Información del Vehículo y Formulario de Cotización -->
                    <div class="col-lg-5">
                        <div class="sticky-top" style="top: 100px;"> <!-- Para que el formulario se mantenga visible al hacer scroll -->
                            <h1 id="nombreVehiculo" class="h2 text-gold mb-3"></h1>
                            <p id="precioVehiculo" class="h4 text-light mb-4"></p>
                            
                            <div class="accordion" id="accordionDetallesCotizacion">
                                <!-- Sección Detalles del Vehículo -->
                                <div class="accordion-item luxury-accordion-item">
                                    <h2 class="accordion-header" id="headingDetalles">
                                        <button class="accordion-button luxury-accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDetalles" aria-expanded="true" aria-controls="collapseDetalles">
                                            <i class="bi bi-car-front-fill me-2"></i> Especificaciones del Vehículo
                                        </button>
                                    </h2>
                                    <div id="collapseDetalles" class="accordion-collapse collapse show" aria-labelledby="headingDetalles" data-bs-parent="#accordionDetallesCotizacion">
                                        <div class="accordion-body">
                                            <ul class="list-unstyled" id="listaDetallesVehiculo">
                                                <!-- Detalles se cargarán aquí por JS -->
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sección Formulario de Cotización -->
                                <div class="accordion-item luxury-accordion-item">
                                    <h2 class="accordion-header" id="headingCotizacion">
                                        <button class="accordion-button luxury-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCotizacion" aria-expanded="false" aria-controls="collapseCotizacion">
                                            <i class="bi bi-calculator-fill me-2"></i> Solicitar Cotización Personalizada
                                        </button>
                                    </h2>
                                    <div id="collapseCotizacion" class="accordion-collapse collapse" aria-labelledby="headingCotizacion" data-bs-parent="#accordionDetallesCotizacion">
                                        <div class="accordion-body">
                                            <form id="formCotizacionNuevo">
                                                <input type="hidden" id="cot_veh_id" name="cot_veh_id">
                                                
                                                <p class="small text-muted-luxury mb-3">Complete los siguientes campos para obtener una cotización detallada.</p>

                                                <div class="mb-3">
                                                    <label for="cot_version" class="form-label">Versión/Paquete:</label>
                                                    <select class="form-select form-select-sm" id="cot_version" name="cot_version">
                                                        <option value="base">Modelo Base</option>
                                                        <!-- Opciones se pueden cargar dinámicamente si hay varias versiones -->
                                                        <option value="luxury">Luxury</option>
                                                        <option value="sport">Sport</option>
                                                        <option value="full_equipo">Full Equipo</option>
                                                    </select>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="cot_transmision" class="form-label">Transmisión:</label>
                                                        <select class="form-select form-select-sm" id="cot_transmision" name="cot_transmision">
                                                            <option value="automatica">Automática</option>
                                                            <option value="manual">Manual</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="cot_motorizacion" class="form-label">Motorización:</label>
                                                        <input type="text" class="form-control form-control-sm" id="cot_motorizacion" name="cot_motorizacion" placeholder="Ej: 2.0L Turbo">
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="cot_color" class="form-label">Color Exterior Preferido:</label>
                                                    <input type="text" class="form-control form-control-sm" id="cot_color" name="cot_color" placeholder="Ej: Rojo Metálico">
                                                </div>

                                                <div class="mb-3">
                                                    <label for="cot_accesorios" class="form-label">Accesorios Adicionales (opcional):</label>
                                                    <textarea class="form-control form-control-sm" id="cot_accesorios" name="cot_accesorios" rows="2" placeholder="Ej: Techo solar, aros deportivos"></textarea>
                                                </div>
                                                
                                                <hr class="my-4 border-secondary">
                                                <p class="text-gold">Información Personal y Financiera</p>
                                                <p class="small text-muted-luxury">Esta información es necesaria para evaluar opciones de financiamiento. Será tratada con confidencialidad.</p>

                                                <div class="mb-3">
                                                    <label for="cot_cedula" class="form-label">Cédula de Identidad:</label>
                                                    <input type="text" class="form-control form-control-sm" id="cot_cedula" name="cot_cedula" required>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="cot_comprobantes_ingresos" class="form-label">Fuente de Ingresos:</label>
                                                    <select class="form-select form-select-sm" id="cot_comprobantes_ingresos" name="cot_comprobantes_ingresos">
                                                        <option value="roles_pago">Roles de Pago (Dependiente)</option>
                                                        <option value="declaraciones_impuestos">Declaraciones de Impuestos (Independiente)</option>
                                                        <option value="otro">Otro</option>
                                                    </select>
                                                    <small class="form-text text-muted-luxury">Un asesor se contactará para solicitar los documentos específicos.</small>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="cot_mensaje_adicional" class="form-label">Mensaje Adicional (opcional):</label>
                                                    <textarea class="form-control form-control-sm" id="cot_mensaje_adicional" name="cot_mensaje_adicional" rows="3" placeholder="¿Alguna pregunta o comentario adicional?"></textarea>
                                                </div>

                                                <div class="d-grid">
                                                    <button type="submit" class="btn btn-primary btn-lg luxury-btn-primary"><i class="bi bi-send-fill me-2"></i> Enviar Solicitud de Cotización</button>
                                                </div>
                                                <div id="cotizacionSpinner" class="text-center mt-3" style="display: none;">
                                                    <div class="spinner-border text-gold" role="status">
                                                        <span class="visually-hidden">Enviando...</span>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sección de Resumen de Cotización (se mostrará después del envío o si ya existe una) -->
                                <div class="accordion-item luxury-accordion-item mt-3" id="resumenCotizacionSection" style="display:none;">
                                    <h2 class="accordion-header" id="headingResumenCotizacion">
                                        <button class="accordion-button luxury-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseResumenCotizacion" aria-expanded="false" aria-controls="collapseResumenCotizacion">
                                            <i class="bi bi-file-earmark-text-fill me-2"></i> Resumen de su Cotización
                                        </button>
                                    </h2>
                                    <div id="collapseResumenCotizacion" class="accordion-collapse collapse" aria-labelledby="headingResumenCotizacion" data-bs-parent="#accordionDetallesCotizacion">
                                        <div class="accordion-body" id="resumenCotizacionBody">
                                            <!-- Contenido del resumen de la cotización se cargará aquí -->
                                            <p class="text-light"><strong>Precio del Vehículo (Modelo Base):</strong> <span id="res_precio_base"></span></p>
                                            <p class="text-light"><strong>IVA (15%):</strong> <span id="res_iva"></span></p>
                                            <p class="text-light fw-bold">Precio Total al Contado Estimado: <span id="res_total_contado"></span></p>
                                            <hr class="border-secondary">
                                            <p class="text-gold">Ejemplo de Financiamiento (Sujeto a aprobación):</p>
                                            <p class="text-light"><strong>Cuota Inicial Sugerida (e.g., 30%):</strong> <span id="res_cuota_inicial"></span></p>
                                            <p class="text-light"><strong>Plazo Estimado:</strong> 60 meses</p>
                                            <p class="text-light"><strong>Cuota Mensual Estimada:</strong> <span id="res_cuota_mensual"></span></p>
                                            <small class="text-muted-luxury">Esta es una estimación básica. Un asesor le presentará opciones detalladas de financiamiento, costos de matrícula, seguros y accesorios.</small>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- Fin Accordion -->
                        </div>
                    </div>
                </div>
                
                <!-- Sección Descripción Detallada del Vehículo (debajo de la fila principal) -->
                <div class="row mt-5">
                    <div class="col-12">
                        <h3 class="text-gold border-bottom border-secondary pb-2 mb-3">Descripción Completa</h3>
                        <div id="descripcionVehiculo" class="text-light-emphasis">
                            <!-- Descripción se cargará aquí por JS -->
                        </div>
                    </div>
                </div>

            </div> <!-- Fin #detalleVehiculoContent -->
             <div id="errorVehiculo" class="text-center py-5" style="display: none;">
                <i class="bi bi-emoji-frown display-1 text-muted-luxury mb-3"></i>
                <h4 class="mt-3 text-light">Vehículo no encontrado</h4>
                <p class="text-muted-luxury">No pudimos encontrar los detalles para el vehículo solicitado. <a href="autos_nuevos.php" class="text-gold">Volver al catálogo</a>.</p>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/partials/footer.php'; ?>

    <script src="../PUBLIC/jquery-3.7.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../VISTAS/JS/global.js"></script>
    <script src="../VISTAS/JS/detalle_nuevo_veh.js"></script> 
</body>
</html>
