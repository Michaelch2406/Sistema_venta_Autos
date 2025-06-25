<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usu_id'])) {
    // Guardar la página actual para redirigir después del login
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../MODELOS/favoritos_m.php';
$favoritos_model = new Favoritos_M();
$lista_favoritos = $favoritos_model->getFavoritosPorUsuario($_SESSION['usu_id']);

$page_title = "Mis Vehículos Favoritos";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - AutoMercado Total</title>
    <link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../PUBLIC/css/styles.css" rel="stylesheet"> <!-- Estilos Globales -->
    <link href="CSS/mis_favoritos.css" rel="stylesheet"> <!-- Estilos específicos para esta página -->
    <script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/trefoil.js"></script>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">
    <div id="page-loader">
        <l-trefoil size="50" stroke="5" stroke-length="0.15" bg-opacity="0.1" speed="1.4" color="#0d6efd"></l-trefoil>
    </div>

    <header id="navbar-placeholder"></header>

    <main class="flex-grow-1 content-hidden py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="display-5 fw-bold"><?php echo htmlspecialchars($page_title); ?></h1>
                <a href="autos_usados.php" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-2"></i>Volver al Catálogo
                </a>
            </div>

            <?php if ($lista_favoritos === false): ?>
                <div class="alert alert-danger text-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Error al cargar tus favoritos. Por favor, intenta más tarde.
                </div>
            <?php elseif (empty($lista_favoritos)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-emoji-frown display-1 text-muted mb-3"></i>
                    <h4 class="mt-3">Aún no tienes vehículos favoritos.</h4>
                    <p class="text-muted">Explora nuestro catálogo y guarda los que más te gusten.</p>
                    <a href="autos_usados.php" class="btn btn-primary mt-3">
                        <i class="bi bi-search me-2"></i>Explorar Vehículos
                    </a>
                </div>
            <?php else: ?>
                <div class="row g-4" id="lista-favoritos-container">
                    <?php foreach ($lista_favoritos as $vehiculo): ?>
                        <div class="col-md-6 col-lg-4 favorito-card-item" data-veh-id="<?php echo htmlspecialchars($vehiculo['veh_id']); ?>">
                            <div class="card card-vehiculo h-100 shadow-sm">
                                <a href="detalle_vehiculo.php?id=<?php echo htmlspecialchars($vehiculo['veh_id']); ?>" class="text-decoration-none">
                                    <img src="<?php echo htmlspecialchars($vehiculo['imagen_principal_url_frontend'] ?? '../PUBLIC/Img/auto_placeholder.png'); ?>"
                                         class="card-img-top card-vehiculo-img-top"
                                         alt="<?php echo htmlspecialchars($vehiculo['mar_nombre'] . ' ' . $vehiculo['mod_nombre']); ?>">
                                </a>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">
                                        <a href="detalle_vehiculo.php?id=<?php echo htmlspecialchars($vehiculo['veh_id']); ?>" class="text-dark text-decoration-none">
                                            <?php echo htmlspecialchars($vehiculo['mar_nombre'] . ' ' . $vehiculo['mod_nombre']); ?>
                                        </a>
                                    </h5>
                                    <p class="precio mb-2">
                                        <?php echo htmlspecialchars(number_format((float)$vehiculo['veh_precio'], 2, '.', ',')) . ' USD'; ?>
                                    </p>
                                    <div class="caracteristicas-list mt-1 small text-muted">
                                        <p class="caracteristica-item mb-1">
                                            <i class="bi bi-calendar-event me-1"></i> Año: <?php echo htmlspecialchars($vehiculo['veh_anio']); ?>
                                        </p>
                                        <p class="caracteristica-item mb-1">
                                            <i class="bi bi-speedometer2 me-1"></i> Condición: <?php echo htmlspecialchars(ucfirst($vehiculo['veh_condicion'])); ?>
                                        </p>
                                        <p class="caracteristica-item mb-1">
                                            <i class="bi bi-geo-alt me-1"></i> <?php echo htmlspecialchars($vehiculo['veh_ubicacion_ciudad'] . ', ' . $vehiculo['veh_ubicacion_provincia']); ?>
                                        </p>
                                        <p class="caracteristica-item mb-1">
                                            <i class="bi bi-bookmark-heart me-1"></i> Agregado: <?php echo htmlspecialchars(date("d/m/Y", strtotime($vehiculo['fav_fecha_agregado']))); ?>
                                        </p>
                                    </div>
                                    <div class="mt-auto pt-3">
                                        <a href="detalle_vehiculo.php?id=<?php echo htmlspecialchars($vehiculo['veh_id']); ?>" class="btn btn-primary w-100 mb-2">
                                            <i class="bi bi-search me-2"></i>Ver Detalles
                                        </a>
                                        <button class="btn btn-outline-danger w-100 btn-quitar-favorito-lista" data-veh-id="<?php echo htmlspecialchars($vehiculo['veh_id']); ?>">
                                            <i class="bi bi-trash3 me-2"></i>Quitar de Favoritos
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include __DIR__ . '/partials/footer.php'; ?>

    <script src="../PUBLIC/jquery-3.7.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../VISTAS/JS/global.js"></script>
    <script src="../VISTAS/JS/mis_favoritos.js"></script> <!-- JS específico para esta página -->
</body>
</html>
