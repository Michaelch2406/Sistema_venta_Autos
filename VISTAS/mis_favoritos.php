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
    <link href="CSS/mis_favoritos.css" rel="stylesheet">
    <script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/trefoil.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
    <div id="page-loader">
        <l-trefoil size="50" stroke="5" stroke-length="0.15" bg-opacity="0.1" speed="1.4" color="#6366f1"></l-trefoil>
    </div>

    <header id="navbar-placeholder"></header>

    <main class="flex-grow-1 content-hidden">
        <!-- Hero Section -->
        <div class="hero-section">
            <div class="hero-overlay"></div>
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="hero-content">
                            <h1 class="hero-title">
                                <i class="bi bi-heart-fill text-danger me-3"></i>
                                <?php echo htmlspecialchars($page_title); ?>
                            </h1>
                            <p class="hero-subtitle">
                                Gestiona tu colección de vehículos favoritos
                            </p>
                            <div class="hero-stats">
                                <div class="stat-item">
                                    <span class="stat-number"><?php echo count($lista_favoritos ?: []); ?></span>
                                    <span class="stat-label">Favoritos</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-end">
                        <a href="autos_usados.php" class="btn btn-outline-light btn-lg">
                            <i class="bi bi-arrow-left me-2"></i>Volver al Catálogo
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Section -->
        <div class="content-section">
            <div class="container">
                <?php if ($lista_favoritos === false): ?>
                    <div class="error-state">
                        <div class="error-icon">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        </div>
                        <h3>Error al cargar favoritos</h3>
                        <p>No pudimos cargar tus vehículos favoritos. Por favor, intenta más tarde.</p>
                        <button class="btn btn-primary" onclick="location.reload()">
                            <i class="bi bi-arrow-clockwise me-2"></i>Intentar nuevamente
                        </button>
                    </div>
                <?php elseif (empty($lista_favoritos)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="bi bi-heart"></i>
                        </div>
                        <h3>Aún no tienes favoritos</h3>
                        <p>Explora nuestro catálogo y guarda los vehículos que más te gusten</p>
                        <div class="empty-actions">
                            <a href="autos_usados.php" class="btn btn-primary btn-lg">
                                <i class="bi bi-search me-2"></i>Explorar Vehículos
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Filter Bar -->
                    <div class="filter-bar">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="results-info">
                                    <span class="results-count"><?php echo count($lista_favoritos); ?></span>
                                    <span class="results-text">vehículos en favoritos</span>
                                </div>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <div class="view-controls">
                                    <button class="btn btn-sm btn-outline-secondary active" data-view="grid">
                                        <i class="bi bi-grid-3x3-gap"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" data-view="list">
                                        <i class="bi bi-list"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vehicles Grid -->
                    <div class="vehicles-grid" id="lista-favoritos-container">
                        <?php foreach ($lista_favoritos as $index => $vehiculo): ?>
                            <div class="vehicle-card favorito-card-item" 
                                 data-veh-id="<?php echo htmlspecialchars($vehiculo['veh_id']); ?>"
                                 style="animation-delay: <?php echo $index * 0.1; ?>s">
                                
                                <div class="card-header">
                                    <div class="favorite-badge">
                                        <i class="bi bi-heart-fill"></i>
                                    </div>
                                    <div class="price-tag">
                                        $<?php echo htmlspecialchars(number_format((float)$vehiculo['veh_precio'], 0, '.', ',')); ?>
                                    </div>
                                </div>

                                <div class="card-image">
                                    <a href="detalle_vehiculo.php?id=<?php echo htmlspecialchars($vehiculo['veh_id']); ?>">
                                        <img src="<?php echo htmlspecialchars($vehiculo['imagen_principal_url_frontend'] ?? '../PUBLIC/Img/auto_placeholder.png'); ?>" 
                                             alt="<?php echo htmlspecialchars($vehiculo['mar_nombre'] . ' ' . $vehiculo['mod_nombre']); ?>">
                                    </a>
                                    <div class="image-overlay">
                                        <button class="btn btn-light btn-sm btn-view-details" 
                                                onclick="location.href='detalle_vehiculo.php?id=<?php echo htmlspecialchars($vehiculo['veh_id']); ?>'">
                                            <i class="bi bi-eye me-1"></i>Ver detalles
                                        </button>
                                    </div>
                                </div>

                                <div class="card-content">
                                    <div class="vehicle-title">
                                        <h5><?php echo htmlspecialchars($vehiculo['mar_nombre'] . ' ' . $vehiculo['mod_nombre']); ?></h5>
                                    </div>

                                    <div class="vehicle-specs">
                                        <div class="spec-item">
                                            <i class="bi bi-calendar-event"></i>
                                            <span><?php echo htmlspecialchars($vehiculo['veh_anio']); ?></span>
                                        </div>
                                        <div class="spec-item">
                                            <i class="bi bi-speedometer2"></i>
                                            <span><?php echo htmlspecialchars(ucfirst($vehiculo['veh_condicion'])); ?></span>
                                        </div>
                                        <div class="spec-item">
                                            <i class="bi bi-geo-alt"></i>
                                            <span><?php echo htmlspecialchars($vehiculo['veh_ubicacion_ciudad']); ?></span>
                                        </div>
                                        <div class="spec-item">
                                            <i class="bi bi-clock"></i>
                                            <span>Agregado <?php echo htmlspecialchars(date("d/m/Y", strtotime($vehiculo['fav_fecha_agregado']))); ?></span>
                                        </div>
                                    </div>

                                    <div class="card-actions">
                                        <a href="detalle_vehiculo.php?id=<?php echo htmlspecialchars($vehiculo['veh_id']); ?>" 
                                           class="btn btn-primary">
                                            <i class="bi bi-search me-2"></i>Ver Detalles
                                        </a>
                                        <button class="btn btn-outline-danger btn-quitar-favorito-lista" 
                                                data-veh-id="<?php echo htmlspecialchars($vehiculo['veh_id']); ?>">
                                            <i class="bi bi-heart-fill me-2"></i>Quitar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/partials/footer.php'; ?>

    <!-- Toast Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="favoriteToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="bi bi-heart-fill text-danger me-2"></i>
                <strong class="me-auto">Favoritos</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body"></div>
        </div>
    </div>

    <script src="../PUBLIC/jquery-3.7.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../VISTAS/JS/global.js"></script>
    <script src="../VISTAS/JS/mis_favoritos.js"></script>
</body>
</html>