<?php
// Ensure session cookie parameters are robustly set
if (session_status() == PHP_SESSION_NONE) {
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params(
        $cookieParams["lifetime"],
        '/', // Path - make sure this matches the login script
        $cookieParams["domain"], // Domain - make sure this matches
        isset($_SERVER['HTTPS']), // Secure - true if HTTPS
        true // HttpOnly
    );
    session_start();
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// session_start(); // Now handled above with params
require_once __DIR__ . "/../MODELOS/vehiculos_m.php";
require_once __DIR__ . "/../MODELOS/imagenes_vehiculo_m.php";

$veh_id = null;
if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $veh_id = (int)$_GET['id'];
}

if (!$veh_id) {
    header("Location: autos_usados.php");
    exit();
}

$vehiculo_model = new Vehiculo();
$vehiculo = $vehiculo_model->getVehiculoDetalle($veh_id);

$imagenes_model = new ImagenesVehiculo_M();
$imagenes = $imagenes_model->getImagenesPorVehiculo($veh_id);

if (!$vehiculo) {
    echo "<!DOCTYPE html><html><head><title>Vehículo no Encontrado</title><link href='../Bootstrap/css/bootstrap.min.css' rel='stylesheet'></head><body class='container mt-5'><div class='alert alert-warning'><h1>Vehículo no Encontrado</h1><p>El vehículo que buscas no está disponible o no existe.</p><a href='inicio.php' class='btn btn-primary'>Volver al Inicio</a></div></body></html>";
    exit();
}

// Preparar datos para mostrar
$titulo_pagina = htmlspecialchars($vehiculo['mar_nombre'] . " " . $vehiculo['mod_nombre'] . " " . $vehiculo['veh_anio']) . " - AutoMercado Total";
$nombre_vehiculo_completo = htmlspecialchars($vehiculo['mar_nombre'] . " " . $vehiculo['mod_nombre'] . ($vehiculo['veh_subtipo_vehiculo'] ? ' ' . $vehiculo['veh_subtipo_vehiculo'] : '') . " - " . $vehiculo['veh_anio']);
$precio_formateado = number_format((float)$vehiculo['veh_precio'], 2, '.', ',');
$kilometraje_formateado = $vehiculo['veh_condicion'] == 'usado' ? number_format((int)$vehiculo['veh_kilometraje']) . " km" : "0 km (Nuevo)";
if ($vehiculo['veh_condicion'] == 'usado' && $vehiculo['veh_kilometraje'] === null) {
    $kilometraje_formateado = "N/D km";
}

$detalles_extra_array = [];
if (!empty($vehiculo['veh_detalles_extra'])) {
    $detalles_extra_array = array_map('trim', explode(',', $vehiculo['veh_detalles_extra']));
}

$imagen_principal_url = '../PUBLIC/Img/auto_placeholder.png';
$imagenes_galeria = [];
if (!empty($imagenes)) {
    foreach ($imagenes as $img) {
        if ($img['ima_es_principal']) {
            $imagen_principal_url = htmlspecialchars($img['ima_url_frontend']);
        } else {
            $imagenes_galeria[] = htmlspecialchars($img['ima_url_frontend']);
        }
    }

    
    if ($imagen_principal_url === '../PUBLIC/Img/auto_placeholder.png' && !empty($imagenes[0]['ima_url_frontend'])) {
        $imagen_principal_url = htmlspecialchars($imagenes[0]['ima_url_frontend']);
        if(isset($imagenes_galeria[0]) && $imagenes_galeria[0] === $imagen_principal_url) {
            array_shift($imagenes_galeria);
        }
    }
}

function getSpecifications($vehiculo) {
    return [
        'motor' => ['icon' => 'bi-gear-fill', 'label' => 'Motor', 'value' => $vehiculo['veh_detalles_motor'] ?: 'N/D'],
        'transmision' => ['icon' => 'bi-gear-wide-connected', 'label' => 'Transmisión', 'value' => $vehiculo['veh_tipo_transmision'] ?: 'N/D'],
        'combustible' => ['icon' => 'bi-fuel-pump-fill', 'label' => 'Combustible', 'value' => $vehiculo['veh_tipo_combustible'] ?: 'N/D'],
        'traccion' => ['icon' => 'bi-car-front-fill', 'label' => 'Tracción', 'value' => $vehiculo['veh_traccion'] ?: 'N/D']
    ];
}

$especificaciones = getSpecifications($vehiculo);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo_pagina; ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    
    <link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
    <link href="../PUBLIC/css/styles.css" rel="stylesheet">
    <link href="../VISTAS/css/detalle_vehiculo.css" rel="stylesheet">
    
    <meta name="description" content="<?php echo htmlspecialchars($nombre_vehiculo_completo . ' - ' . $precio_formateado . ' USD. ' . ($vehiculo['veh_descripcion'] ? substr($vehiculo['veh_descripcion'], 0, 150) . '...' : 'Vehículo en venta en AutoMercado Total.')); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($vehiculo['mar_nombre'] . ', ' . $vehiculo['mod_nombre'] . ', ' . $vehiculo['veh_anio'] . ', ' . $vehiculo['veh_condicion'] . ', auto, venta'); ?>">
    
    <meta property="og:title" content="<?php echo htmlspecialchars($nombre_vehiculo_completo); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($precio_formateado . ' USD - ' . ($vehiculo['veh_descripcion'] ? substr($vehiculo['veh_descripcion'], 0, 150) . '...' : 'Vehículo en venta')); ?>">
    <meta property="og:image" content="<?php echo $imagen_principal_url; ?>">
    <meta property="og:type" content="product">
    
    <script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/trefoil.js"></script>
</head>
<body class="vehicle-detail-page">
    <div id="page-loader" class="page-loader">
        <div class="loader-content">
            <l-trefoil size="50" stroke="5" stroke-length="0.15" bg-opacity="0.1" speed="1.4" color="#dc2626"></l-trefoil>
            <p class="loader-text">Cargando vehículo...</p>
        </div>
    </div>

    <header id="navbar-placeholder"></header>

    <main class="main-content content-hidden">
        <section class="breadcrumb-section">
            <div class="container">
                <nav aria-label="breadcrumb" class="custom-breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="inicio.php"><i class="bi bi-house-fill"></i> Inicio</a></li>
                        <li class="breadcrumb-item"><a href="autos_<?php echo htmlspecialchars($vehiculo['veh_condicion']); ?>s.php">Autos <?php echo htmlspecialchars(ucfirst($vehiculo['veh_condicion'])); ?>s</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($vehiculo['mar_nombre'] . " " . $vehiculo['mod_nombre']); ?></li>
                    </ol>
                </nav>
            </div>
        </section>

        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-8">
                    <div class="vehicle-gallery fade-in-left">
                        <div class="gallery-main">
                            <div class="main-image-container">
                                <a href="<?php echo $imagen_principal_url; ?>" data-lightbox="galeria-vehiculo" data-title="<?php echo $nombre_vehiculo_completo; ?>" class="main-image-link">
                                    <img src="<?php echo $imagen_principal_url; ?>" alt="<?php echo $nombre_vehiculo_completo; ?>" class="main-image" id="imagenPrincipalVehiculo">
                                    <div class="image-overlay"><i class="bi bi-zoom-in"></i><span>Ver en tamaño completo</span></div>
                                </a>
                                <div class="image-counter"><i class="bi bi-images"></i><span><?php echo count($imagenes) > 0 ? count($imagenes) : 1; ?> fotos</span></div>
                            </div>
                        </div>
                        <?php if (count($imagenes_galeria) > 0): ?>
                        <div class="gallery-thumbnails">
                            <div class="thumbnails-container">
                                <?php
                                $all_images_for_thumb = array_unique(array_merge([$imagen_principal_url], $imagenes_galeria));
                                foreach ($all_images_for_thumb as $index => $url_miniatura): ?>
                                <div class="thumbnail-item <?php echo ($url_miniatura === $imagen_principal_url) ? 'active' : ''; ?>">
                                    <a href="<?php echo $url_miniatura; ?>" data-lightbox="galeria-vehiculo" data-title="<?php echo $nombre_vehiculo_completo; ?>">
                                        <img src="<?php echo $url_miniatura; ?>" alt="Vista <?php echo $index + 1; ?>" class="thumbnail-image" data-fullimage="<?php echo $url_miniatura; ?>">
                                    </a>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <section class="specifications-section mt-5">
                        <div class="section-header reveal-on-scroll">
                            <h2 class="section-title"><i class="bi bi-gear-fill"></i> Especificaciones Técnicas</h2>
                        </div>
                        <div class="specs-grid reveal-on-scroll">
                            <?php foreach ($especificaciones as $key => $spec): ?>
                            <div class="spec-card" data-spec="<?php echo $key; ?>">
                                <div class="spec-icon"><i class="bi <?php echo $spec['icon']; ?>"></i></div>
                                <div class="spec-content">
                                    <h3 class="spec-label"><?php echo $spec['label']; ?></h3>
                                    <p class="spec-value"><?php echo htmlspecialchars($spec['value']); ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <div class="detail-block reveal-on-scroll mt-5">
                        <h3 class="detail-title"><i class="bi bi-card-text"></i> Descripción</h3>
                        <div class="detail-content">
                            <p><?php echo nl2br(htmlspecialchars($vehiculo['veh_descripcion'] ?: 'No se proporcionó una descripción detallada para este vehículo.')); ?></p>
                        </div>
                    </div>

                    <div class="detail-block reveal-on-scroll">
                        <h3 class="detail-title"><i class="bi bi-tools"></i> Detalles Técnicos</h3>
                        <div class="detail-content">
                            <div class="row">
                                <div class="col-md-6"><div class="detail-list">
                                    <div class="detail-item"><span class="detail-label">Color Exterior:</span> <span class="detail-value"><?php echo htmlspecialchars($vehiculo['veh_color_exterior'] ?: 'N/D'); ?></span></div>
                                    <div class="detail-item"><span class="detail-label">Color Interior:</span> <span class="detail-value"><?php echo htmlspecialchars($vehiculo['veh_color_interior'] ?: 'N/D'); ?></span></div>
                                    <div class="detail-item"><span class="detail-label">Dirección:</span> <span class="detail-value"><?php echo htmlspecialchars($vehiculo['veh_tipo_direccion'] ?: 'N/D'); ?></span></div>
                                </div></div>
                                <div class="col-md-6"><div class="detail-list">
                                    <div class="detail-item"><span class="detail-label">Vidrios:</span> <span class="detail-value"><?php echo htmlspecialchars($vehiculo['veh_tipo_vidrios'] ?: 'N/D'); ?></span></div>
                                    <div class="detail-item"><span class="detail-label">Climatización:</span> <span class="detail-value"><?php echo htmlspecialchars($vehiculo['veh_sistema_climatizacion'] ?: 'N/D'); ?></span></div>
                                    <?php if ($vehiculo['veh_condicion'] == 'usado' && !empty($vehiculo['veh_placa_provincia_origen']) && !empty($vehiculo['veh_ultimo_digito_placa'])): ?>
                                    <div class="detail-item"><span class="detail-label">Matrícula:</span> <span class="detail-value"><?php echo htmlspecialchars($vehiculo['veh_placa_provincia_origen']); ?> - termina en <?php echo htmlspecialchars($vehiculo['veh_ultimo_digito_placa']); ?></span></div>
                                    <?php endif; ?>
                                </div></div>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($detalles_extra_array)): ?>
                    <div class="detail-block reveal-on-scroll">
                        <h3 class="detail-title"><i class="bi bi-check-circle-fill"></i> Características Adicionales</h3>
                        <div class="detail-content">
                            <div class="features-grid">
                                <?php foreach ($detalles_extra_array as $detalle_extra): ?>
                                <div class="feature-item"><i class="bi bi-check-lg"></i> <span><?php echo htmlspecialchars($detalle_extra); ?></span></div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="col-lg-4">
                    <div class="sidebar-sticky">
                        <div class="vehicle-info fade-in-right">
                            <div class="vehicle-header stagger-1">
                                <div class="condition-badge <?php echo $vehiculo['veh_condicion']; ?>"><?php echo htmlspecialchars(ucfirst($vehiculo['veh_condicion'])); ?></div>
                                <h1 class="vehicle-title"><?php echo $nombre_vehiculo_completo; ?></h1>
                                <p class="vehicle-subtitle"><?php echo htmlspecialchars($vehiculo['tiv_nombre']) . ($vehiculo['veh_subtipo_vehiculo'] ? ' / ' . htmlspecialchars($vehiculo['veh_subtipo_vehiculo']) : ''); ?></p>
                            </div>
                            <div class="price-section stagger-2">
                                <div class="price-main"><span class="currency">$</span><span class="amount"><?php echo $precio_formateado; ?></span><span class="currency-code">USD</span></div>
                                <div class="price-subtitle">Precio de venta</div>
                            </div>
                            <div class="quick-info stagger-3">
                                <div class="info-grid">
                                    <div class="info-item"><i class="bi bi-calendar-check"></i><div class="info-content"><span class="info-label">Año</span><span class="info-value"><?php echo htmlspecialchars($vehiculo['veh_anio']); ?></span></div></div>
                                    <?php if ($vehiculo['veh_condicion'] == 'usado'): ?>
                                    <div class="info-item"><i class="bi bi-speedometer2"></i><div class="info-content"><span class="info-label">Recorrido</span><span class="info-value"><?php echo $kilometraje_formateado; ?></span></div></div>
                                    <?php endif; ?>
                                    <div class="info-item"><i class="bi bi-geo-alt-fill"></i><div class="info-content"><span class="info-label">Ubicación</span><span class="info-value"><?php echo htmlspecialchars($vehiculo['veh_ubicacion_ciudad'] . ", " . $vehiculo['veh_ubicacion_provincia']); ?></span></div></div>
                                    <div class="info-item"><i class="bi bi-clock-fill"></i><div class="info-content"><span class="info-label">Publicado</span><span class="info-value"><?php echo date("d/m/Y", strtotime($vehiculo['veh_fecha_publicacion'])); ?></span></div></div>
                                </div>
                            </div>
                            
                            <!-- CAMBIO: La sección de contacto AHORA contiene los botones de acción principales -->
                            <div class="contact-section stagger-4">
                                <?php if (isset($_SESSION['usu_id']) && isset($vehiculo['usu_id_gestor']) && $_SESSION['usu_id'] == $vehiculo['usu_id_gestor']): ?>
                                    <div class="owner-notice"><i class="bi bi-info-circle-fill"></i><span>Este es uno de tus vehículos publicados</span></div>
                                    <a href="mis_vehiculos.php" class="btn btn-primary btn-lg w-100"><i class="bi bi-car-front-fill"></i> Gestionar Mis Vehículos</a>
                                <?php else: ?>
                                    <?php if (!empty($vehiculo['gestor_telefono'])): ?>
                                        <a href="https://wa.me/593<?php echo substr(preg_replace('/[^0-9]/', '', $vehiculo['gestor_telefono']), -9); ?>?text=Hola, me interesa el <?php echo urlencode($nombre_vehiculo_completo); ?> (ID: <?php echo $veh_id; ?>) que vi en AutoMercado Total." target="_blank" class="btn btn-whatsapp btn-lg w-100 mb-3"><i class="bi bi-whatsapp"></i> Contactar por WhatsApp</a>
                                    <?php endif; ?>
                                    <?php if (isset($_SESSION['usu_id'])): ?>
                                        <button class="btn btn-secondary btn-lg w-100" data-bs-toggle="modal" data-bs-target="#modalContactoVendedor"><i class="bi bi-chat-dots-fill"></i> Solicitar Información</button>
                                    <?php else: ?>
                                        <p class="text-muted small mt-2 mb-2 text-center">Inicia sesión para más acciones:</p>
                                        <a href="login.php?redirect=detalle_vehiculo.php?id=<?php echo $veh_id; ?>" class="btn btn-outline-primary btn-lg w-100"><i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión para Contactar</a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>

                            <!-- CAMBIO: El 'action-card' ahora está DENTRO del 'vehicle-info' para agruparlo correctamente -->
                            <div class="action-card reveal-on-scroll mt-4">
                                <h4 class="card-title">Acciones Adicionales</h4>
                                <p class="card-subtitle">Guarda o comparte este vehículo</p>
                                <div class="action-buttons">
                                    <?php if (isset($_SESSION['usu_id'])): ?>
                                    <button class="btn btn-outline-danger w-100 btn-favoritos" data-veh-id="<?php echo $veh_id; ?>">
                                        <i class="bi bi-heart"></i> <span id="favText">Agregar a Favoritos</span>
                                    </button>
                                    <?php else: ?>
                                    <a href="login.php?redirect=detalle_vehiculo.php?id=<?php echo $veh_id; ?>" class="btn btn-outline-danger w-100"><i class="bi bi-heart"></i> Agregar a Favoritos</a>
                                    <?php endif; ?>
                                    <button class="btn btn-outline-secondary w-100 btn-share" data-share-title="<?php echo urlencode($nombre_vehiculo_completo); ?>" data-share-url="<?php echo urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); ?>">
                                        <i class="bi bi-share"></i> Compartir Vehículo
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php if (isset($_SESSION['usu_id'])): ?>
    <div class="modal fade" id="modalContactoVendedor" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Contactar al Vendedor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formContactoVendedor">
                    <div class="modal-body">
                        <p>Se enviará una notificación al vendedor con tus datos de contacto. Puedes añadir un mensaje opcional.</p>
                        <input type="hidden" name="veh_id" value="<?php echo $veh_id; ?>">
                        <input type="hidden" name="accion" value="enviarCotizacion">
                        <div class="mb-3">
                            <label for="cot_mensaje" class="form-label">Mensaje Adicional (Opcional):</label>
                            <textarea class="form-control" id="cot_mensaje" name="mensaje" rows="4" placeholder="Ej: Hola, ¿aún está disponible? Me gustaría más información."></textarea>
                        </div>
                        <div id="contactFormMessage" class="mt-3"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="btnEnviarCotizacion"><i class="bi bi-send-fill me-2"></i>Enviar Solicitud</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php include __DIR__ . '/partials/footer.php'; ?>
    
    <script src="../PUBLIC/jquery-3.7.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script src="../VISTAS/JS/global.js"></script>
    <script src="../VISTAS/js/detalle_vehiculo.js"></script>
</body>
</html>