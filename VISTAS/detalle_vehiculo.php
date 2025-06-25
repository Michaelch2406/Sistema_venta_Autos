<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once __DIR__ . "/../MODELOS/vehiculos_m.php";
require_once __DIR__ . "/../MODELOS/imagenes_vehiculo_m.php";

$veh_id = null;
if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $veh_id = (int)$_GET['id'];
}

if (!$veh_id) {
    // Redirigir a una página de error o al listado si no hay ID
    header("Location: autos_usados.php"); // O a una página de "no encontrado"
    exit();
}

$vehiculo_model = new Vehiculo();
$vehiculo = $vehiculo_model->getVehiculoDetalle($veh_id);

$imagenes_model = new ImagenesVehiculo_M();
$imagenes = $imagenes_model->getImagenesPorVehiculo($veh_id);

if (!$vehiculo) {
    // Si el SP devolvió null (vehículo no encontrado o no disponible)
    echo "<!DOCTYPE html><html><head><title>Vehículo no Encontrado</title><link href='../Bootstrap/css/bootstrap.min.css' rel='stylesheet'></head><body class='container mt-5'><div class='alert alert-warning'><h1>Vehículo no Encontrado</h1><p>El vehículo que buscas no está disponible o no existe.</p><a href='inicio.php' class='btn btn-primary'>Volver al Inicio</a></div></body></html>";
    exit();
}

// Preparar datos para mostrar
$titulo_pagina = htmlspecialchars($vehiculo['mar_nombre'] . " " . $vehiculo['mod_nombre'] . " " . $vehiculo['veh_anio']) . " - AutoMercado Total";
$nombre_vehiculo_completo = htmlspecialchars($vehiculo['mar_nombre'] . " " . $vehiculo['mod_nombre'] . ($vehiculo['veh_subtipo_vehiculo'] ? ' ' . $vehiculo['veh_subtipo_vehiculo'] : '') . " - " . $vehiculo['veh_anio']);
$precio_formateado = number_format((float)$vehiculo['veh_precio'], 2, '.', ',');
$kilometraje_formateado = $vehiculo['veh_condicion'] == 'usado' ? number_format((int)$vehiculo['veh_kilometraje']) . " km" : "0 km (Nuevo)";
if ($vehiculo['veh_condicion'] == 'usado' && $vehiculo['veh_kilometraje'] === null) {
    $kilometraje_formateado = "N/D km"; // Or some other placeholder for used cars with no mileage specified
}


$detalles_extra_array = [];
if (!empty($vehiculo['veh_detalles_extra'])) {
    $detalles_extra_array = array_map('trim', explode(',', $vehiculo['veh_detalles_extra']));
}

// Separar la imagen principal de las demás
$imagen_principal_url = '../PUBLIC/Img/auto_placeholder.png'; // Default
$imagenes_galeria = [];
if (!empty($imagenes)) {
    foreach ($imagenes as $img) {
        if ($img['ima_es_principal']) {
            $imagen_principal_url = htmlspecialchars($img['ima_url_frontend']);
        } else {
            $imagenes_galeria[] = htmlspecialchars($img['ima_url_frontend']);
        }
    }
    // Si no se encontró una principal explícita, y hay imágenes, usar la primera
    if ($imagen_principal_url === '../PUBLIC/Img/auto_placeholder.png' && !empty($imagenes[0]['ima_url_frontend'])) {
        $imagen_principal_url = htmlspecialchars($imagenes[0]['ima_url_frontend']);
        // Si la primera era la principal, ya se usó. Si no, quitarla de la galería si ya es la principal.
        if(isset($imagenes_galeria[0]) && $imagenes_galeria[0] === $imagen_principal_url) {
            array_shift($imagenes_galeria);
        }
    }
}


?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo_pagina; ?></title>
    <link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Lightbox CSS (opcional, para galería de imágenes) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
    <link href="../PUBLIC/css/styles.css" rel="stylesheet">
    <script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/trefoil.js"></script>
    <style>
        .detalle-vehiculo-header {
            margin-bottom: 1.5rem;
        }

        .precio-destacado {
            font-size: 2.5rem;
            font-weight: bold;
            color: #0d6efd;
            margin-bottom: 1rem;
        }

        .galeria-principal img {
            width: 100%;
            max-height: 500px;
            object-fit: cover;
            border-radius: .5rem;
            border: 1px solid #dee2e6;
        }

        .galeria-miniaturas img {
            height: 80px;
            width: 100%;
            object-fit: cover;
            cursor: pointer;
            border-radius: .25rem;
            border: 1px solid #dee2e6;
            opacity: 0.7;
            transition: opacity 0.2s ease-in-out;
        }

        .galeria-miniaturas img:hover,
        .galeria-miniaturas img.active-thumb {
            opacity: 1;
            border-color: #0d6efd;
        }

        .detalle-seccion {
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #eee;
        }

        .detalle-seccion:last-child {
            border-bottom: none;
        }

        .detalle-seccion h3 {
            font-size: 1.5rem;
            color: #343a40;
            margin-bottom: 1rem;
            font-weight: 500;
        }

        .detalle-lista {
            list-style: none;
            padding-left: 0;
        }

        .detalle-lista li {
            padding: 0.5rem 0;
            border-bottom: 1px dashed #f0f0f0;
            display: flex;
            justify-content: space-between;
        }

        .detalle-lista li:last-child {
            border-bottom: none;
        }

        .detalle-lista li strong {
            color: #495057;
        }

        .contact-card {
            background-color: #f8f9fa;
        }

        .contact-card .btn-whatsapp {
            background-color: #25D366;
            color: white;
        }

        .contact-card .btn-whatsapp:hover {
            background-color: #1DAE50;
        }

        .detalles-extra-list i {
            color: #198754;
        }

        /* Verde para check */
    </style>
</head>

<body class="d-flex flex-column min-vh-100 bg-light">
    <div id="page-loader">
        <l-trefoil size="50" stroke="5" stroke-length="0.15" bg-opacity="0.1" speed="1.4" color="#0d6efd"></l-trefoil>
    </div>

    <header id="navbar-placeholder"></header>

    <main class="flex-grow-1 content-hidden">
        <div class="container py-5">
            <div class="pt-3">
                <!-- Espacio para el navbar fijo -->
                <nav aria-label="breadcrumb mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="inicio.php">Inicio</a></li>
                        <li class="breadcrumb-item"><a
                                href="autos_<?php echo htmlspecialchars($vehiculo['veh_condicion']); ?>.php">Autos
                                <?php echo htmlspecialchars(ucfirst($vehiculo['veh_condicion'])); ?>s</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?php echo htmlspecialchars($vehiculo['mar_nombre'] . " " . $vehiculo['mod_nombre']); ?>
                        </li>
                    </ol>
                </nav>

                <div class="row g-5">
                    <!-- Columna de Galería de Imágenes -->
                    <div class="col-lg-7">
                        <div class="galeria-principal mb-3 text-center">
                            <a href="<?php echo $imagen_principal_url; ?>" data-lightbox="galeria-vehiculo"
                                data-title="<?php echo $nombre_vehiculo_completo; ?>">
                                <img src="<?php echo $imagen_principal_url; ?>"
                                    alt="Imagen principal de <?php echo $nombre_vehiculo_completo; ?>" class="img-fluid"
                                    id="imagenPrincipalVehiculo">
                            </a>
                        </div>
                        <?php if (count($imagenes_galeria) > 0): ?>
                        <div class="galeria-miniaturas d-flex flex-wrap justify-content-start gap-2">
                            <?php if ($imagen_principal_url !== '../PUBLIC/Img/auto_placeholder.png' && !in_array($imagen_principal_url, $imagenes_galeria)): // Mostrar la principal si no está ya en galería ?>
                            <a href="<?php echo $imagen_principal_url; ?>" data-lightbox="galeria-vehiculo"
                                data-title="<?php echo $nombre_vehiculo_completo; ?>">
                                <img src="<?php echo $imagen_principal_url; ?>" alt="Miniatura principal"
                                    class="active-thumb" data-fullimage="<?php echo $imagen_principal_url; ?>">
                            </a>
                            <?php endif; ?>
                            <?php foreach ($imagenes_galeria as $url_miniatura): ?>
                            <a href="<?php echo $url_miniatura; ?>" data-lightbox="galeria-vehiculo"
                                data-title="<?php echo $nombre_vehiculo_completo; ?>">
                                <img src="<?php echo $url_miniatura; ?>"
                                    alt="Miniatura de <?php echo $nombre_vehiculo_completo; ?>"
                                    data-fullimage="<?php echo $url_miniatura; ?>">
                            </a>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Columna de Detalles y Precio -->
                    <div class="col-lg-5">
                        <div class="detalle-vehiculo-header">
                            <h1 class="display-6 fw-bold mb-1"><?php echo $nombre_vehiculo_completo; ?></h1>
                            <p class="text-muted mb-2">
                                <?php echo htmlspecialchars($vehiculo['tiv_nombre']) . ($vehiculo['veh_subtipo_vehiculo'] ? ' / ' . htmlspecialchars($vehiculo['veh_subtipo_vehiculo']) : ''); ?>
                            </p>
                        </div>

                        <p class="precio-destacado">$<?php echo $precio_formateado; ?> USD</p>

                        <ul class="detalle-lista mb-4">
                            <li><strong>Condición:</strong>
                                <span><?php echo htmlspecialchars(ucfirst($vehiculo['veh_condicion'])); ?></span></li>
                            <li><strong>Año:</strong>
                                <span><?php echo htmlspecialchars($vehiculo['veh_anio']); ?></span></li>
                            <?php if ($vehiculo['veh_condicion'] == 'usado'): ?>
                            <li><strong>Recorrido:</strong> <span><?php echo $kilometraje_formateado; ?></span></li>
                            <?php if ($vehiculo['veh_placa_provincia_origen']): ?>
                            <li><strong>Placa:</strong>
                                <span><?php echo htmlspecialchars($vehiculo['veh_placa_provincia_origen']) . " - termina en " . htmlspecialchars($vehiculo['veh_ultimo_digito_placa']); ?></span>
                            </li>
                            <?php endif; ?>
                            <?php endif; ?>
                            <li><strong>Ubicación:</strong>
                                <span><?php echo htmlspecialchars($vehiculo['veh_ubicacion_ciudad'] . ", " . $vehiculo['veh_ubicacion_provincia']); ?></span>
                            </li>
                            <li><strong>Publicado:</strong>
                                <span><?php echo date("d/m/Y", strtotime($vehiculo['veh_fecha_publicacion'])); ?></span>
                            </li>
                        </ul>

                        <?php if ($vehiculo['gestor_telefono'] || $vehiculo['gestor_usuario']): ?>
                        <div class="card contact-card shadow-sm">
                            <div class="card-body text-center">
                                <h5 class="card-title mb-3">Contactar al Vendedor</h5>
                                <?php if ($vehiculo['gestor_nombre_completo']): ?>
                                <p class="mb-2"><i
                                        class="bi bi-person-check-fill me-2"></i><?php echo htmlspecialchars($vehiculo['gestor_nombre_completo']); ?>
                                </p>
                                <?php endif; ?>
                                <?php if ($vehiculo['gestor_telefono']): ?>
                                <a href="https://wa.me/593<?php echo substr(preg_replace('/[^0-9]/', '', $vehiculo['gestor_telefono']), -9); ?>?text=Hola, me interesa el <?php echo urlencode($nombre_vehiculo_completo); ?> (ID: <?php echo $veh_id; ?>) que vi en AutoMercado Total."
                                    target="_blank" class="btn btn-lg btn-whatsapp w-100 mb-2">
                                    <i class="bi bi-whatsapp me-2"></i>Contactar por WhatsApp
                                </a>
                                <p class="small text-muted">Tel:
                                    <?php echo htmlspecialchars($vehiculo['gestor_telefono']); ?></p>
                                <?php endif; ?>
                                <!-- Podrías añadir un botón para un formulario de contacto directo aquí -->
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <hr class="my-5">

                <div class="row">
                    <div class="col-lg-8">
                        <section class="detalle-seccion">
                            <h3><i class="bi bi-card-text me-2"></i>Descripción</h3>
                            <p><?php echo nl2br(htmlspecialchars($vehiculo['veh_descripcion'] ?: 'No se proporcionó una descripción detallada.')); ?>
                            </p>
                        </section>

                        <section class="detalle-seccion">
                            <h3><i class="bi bi-tools me-2"></i>Especificaciones Técnicas</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="detalle-lista">
                                        <li><strong>Color Exterior:</strong>
                                            <span><?php echo htmlspecialchars($vehiculo['veh_color_exterior'] ?: 'N/D'); ?></span>
                                        </li>
                                        <li><strong>Color Interior:</strong>
                                            <span><?php echo htmlspecialchars($vehiculo['veh_color_interior'] ?: 'N/D'); ?></span>
                                        </li>
                                        <li><strong>Motor:</strong>
                                            <span><?php echo htmlspecialchars($vehiculo['veh_detalles_motor'] ?: 'N/D'); ?></span>
                                        </li>
                                        <li><strong>Transmisión:</strong>
                                            <span><?php echo htmlspecialchars($vehiculo['veh_tipo_transmision'] ?: 'N/D'); ?></span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="detalle-lista">
                                        <li><strong>Tracción:</strong>
                                            <span><?php echo htmlspecialchars($vehiculo['veh_traccion'] ?: 'N/D'); ?></span>
                                        </li>
                                        <li><strong>Combustible:</strong>
                                            <span><?php echo htmlspecialchars($vehiculo['veh_tipo_combustible'] ?: 'N/D'); ?></span>
                                        </li>
                                        <li><strong>Dirección:</strong>
                                            <span><?php echo htmlspecialchars($vehiculo['veh_tipo_direccion'] ?: 'N/D'); ?></span>
                                        </li>
                                        <li><strong>Vidrios:</strong>
                                            <span><?php echo htmlspecialchars($vehiculo['veh_tipo_vidrios'] ?: 'N/D'); ?></span>
                                        </li>
                                        <li><strong>Climatización:</strong>
                                            <span><?php echo htmlspecialchars($vehiculo['veh_sistema_climatizacion'] ?: 'N/D'); ?></span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </section>

                        <?php if (!empty($detalles_extra_array)): ?>
                        <section class="detalle-seccion">
                            <h3><i class="bi bi-check-circle-fill me-2"></i>Detalles Adicionales</h3>
                            <ul class="list-unstyled row">
                                <?php foreach ($detalles_extra_array as $detalle_extra): ?>
                                <li class="col-md-6 py-1"><i
                                        class="bi bi-check-lg me-2"></i><?php echo htmlspecialchars($detalle_extra); ?>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </section>
                        <?php endif; ?>

                        <?php if ($vehiculo['veh_vin']): ?>
                        <section class="detalle-seccion">
                            <h3><i class="bi bi-fingerprint me-2"></i>Identificación</h3>
                            <ul class="detalle-lista">
                                <li><strong>VIN:</strong>
                                    <span><?php echo htmlspecialchars($vehiculo['veh_vin']); ?></span></li>
                            </ul>
                        </section>
                        <?php endif; ?>
                    </div>
                    <div class="col-lg-4">
                        <!-- Espacio para publicidad, vehículos relacionados, etc. -->
                        <div class="sticky-top" style="top: 80px;">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title">¿Interesado?</h5>
                                    <p class="card-text small text-muted">Contacta directamente al vendedor o solicita
                                        más información.</p>
                                    <a href="#contactFormVehiculo" class="btn btn-outline-primary w-100 mb-2"><i
                                            class="bi bi-envelope-fill me-2"></i>Enviar Mensaje al Vendedor</a>
                                    <?php if (isset($_SESSION['usu_id'])): ?>
                                    <button class="btn btn-outline-danger w-100 btn-agregar-favoritos"
                                        data-veh-id="<?php echo $veh_id; ?>">
                                        <i class="bi bi-heart me-2"></i><span id="favText">Agregar a Favoritos</span>
                                    </button>
                                    <?php else: ?>
                                    <a href="login.php?redirect=detalle_vehiculo.php?id=<?php echo $veh_id; ?>"
                                        class="btn btn-outline-danger w-100"><i class="bi bi-heart me-2"></i>Agregar a
                                        Favoritos (Requiere Login)</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="card shadow-sm mt-4">
                                <div class="card-body">
                                    <h5 class="card-title">Comparte</h5>
                                    <p class="card-text small text-muted">Si te gustó este vehículo, ¡compártelo!</p>
                                    <!-- Implementar botones de compartir -->
                                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); ?>"
                                        target="_blank" class="btn btn-sm btn-outline-primary me-1"><i
                                            class="bi bi-facebook"></i></a>
                                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); ?>&text=Mira este <?php echo urlencode($nombre_vehiculo_completo); ?>"
                                        target="_blank" class="btn btn-sm btn-outline-info me-1"><i
                                            class="bi bi-twitter-x"></i></a>
                                    <a href="https://api.whatsapp.com/send?text=Mira este <?php echo urlencode($nombre_vehiculo_completo); ?>: <?php echo urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); ?>"
                                        target="_blank" class="btn btn-sm btn-outline-success"><i
                                            class="bi bi-whatsapp"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/partials/footer.php'; ?>

    <script src="../PUBLIC/jquery-3.7.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Lightbox JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script src="../VISTAS/JS/global.js"></script>
    <script src="../VISTAS/JS/detalle_vehiculo.js"></script>
</body>

</html>