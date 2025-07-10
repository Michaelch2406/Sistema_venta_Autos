<?php
session_start();
// Verificar si el usuario está logueado y tiene permiso (Cliente/Vendedor, Vendedor o Administrador)
// Rol Cliente/Vendedor es 1, Vendedor 2 (si aplica), Administrador 3
if (!isset($_SESSION['usu_id']) || !in_array($_SESSION['rol_id'], [1, 2, 3])) {
    echo "<!DOCTYPE html><html><head><title>Acceso Denegado</title><link href='../Bootstrap/css/bootstrap.min.css' rel='stylesheet'></head><body class='container mt-5'><div class='alert alert-danger'><h1>Acceso Denegado</h1><p>No tienes permisos para acceder a esta página.</p><a href='escritorio.php' class='btn btn-primary'>Volver al Escritorio</a></div></body></html>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Vehículos Publicados - AutoMercado Total</title>
    <!-- Bootstrap CSS Local -->
    <link href="./../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Tus Estilos Personalizados -->
    <link href="./../PUBLIC/css/styles.css" rel="stylesheet">
    <!-- CSS Específico para esta página -->
    <link href="./CSS/mis_vehiculos.css" rel="stylesheet">
    <script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/trefoil.js"></script>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">
    <div id="page-loader">
        <l-trefoil size="50" stroke="5" stroke-length="0.15" bg-opacity="0.1" speed="1.4" color="#0d6efd"></l-trefoil>
    </div>

    <header id="navbar-placeholder"></header>

    <main class="flex-grow-1 content-hidden">
        <div class="container py-5">
            <!-- Header Section Mejorado -->
            <div class="header-section">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                    <div>
                        <h1 class="display-5 publishing-title mb-2">Mis Vehículos Publicados</h1>
                        <p class="lead text-muted mb-0">Gestiona tus anuncios, actualiza información y controla el estado de tus publicaciones.</p>
                    </div>
                    <div class="d-flex flex-column flex-sm-row gap-2">
                        <a href="publicar_vehiculo.php" class="btn btn-primary btn-lg">
                            <i class="bi bi-plus-circle-fill me-2"></i>Publicar Nuevo Vehículo
                        </a>
                        <button class="btn btn-outline-secondary btn-lg" id="refreshBtn" title="Actualizar lista">
                            <i class="bi bi-arrow-clockwise me-2"></i>Actualizar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Estadísticas Rápidas -->
            <div class="row g-3 mb-4" id="statsContainer">
                <div class="col-6 col-md-3">
                    <div class="stats-card">
                        <span class="stats-number" id="totalVehiculos">-</span>
                        <span class="stats-label">Total</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stats-card">
                        <span class="stats-number text-success" id="disponiblesCount">-</span>
                        <span class="stats-label">Disponibles</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stats-card">
                        <span class="stats-number text-warning" id="reservadosCount">-</span>
                        <span class="stats-label">Reservados</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stats-card">
                        <span class="stats-number text-danger" id="vendidosCount">-</span>
                        <span class="stats-label">Vendidos</span>
                    </div>
                </div>
            </div>

            <!-- Filtros y Búsqueda -->
            <div class="filters-section">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <div class="search-container">
                            <i class="bi bi-search search-icon"></i>
                            <input type="text" class="form-control search-input" id="searchInput" placeholder="Buscar por marca, modelo o año...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex flex-wrap justify-content-md-end">
                            <button class="filter-btn btn btn-outline-primary active" data-filter="todos">
                                <i class="bi bi-grid-3x3-gap me-1"></i>Todos
                            </button>
                            <button class="filter-btn btn btn-outline-success" data-filter="disponible">
                                <i class="bi bi-check-circle me-1"></i>Disponibles
                            </button>
                            <button class="filter-btn btn btn-outline-warning" data-filter="reservado">
                                <i class="bi bi-clock me-1"></i>Reservados
                            </button>
                            <button class="filter-btn btn btn-outline-danger" data-filter="vendido">
                                <i class="bi bi-currency-dollar me-1"></i>Vendidos
                            </button>
                            <button class="filter-btn btn btn-outline-secondary" data-filter="desactivado">
                                <i class="bi bi-pause-circle me-1"></i>Desactivados
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenedor de Vehículos -->
            <div id="listaVehiculosContainer" class="row g-4">
                <!-- Los vehículos se cargarán aquí por AJAX -->
                <div class="col-12 text-center" id="loadingVehiculos">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Cargando vehículos...</span>
                    </div>
                    <p class="loading-text">Cargando tus vehículos...</p>
                </div>
            </div>

            <!-- Mensaje cuando no hay vehículos -->
            <div id="noVehiculosMessage" class="text-center mt-5" style="display: none;">
                <i class="bi bi-car-front-fill display-1 text-muted mb-3"></i>
                <h3 class="mb-3">Aún no has publicado vehículos</h3>
                <p class="text-muted mb-4">¡Empieza ahora y llega a miles de compradores potenciales!</p>
                <a href="publicar_vehiculo.php" class="btn btn-success btn-lg">
                    <i class="bi bi-plus-circle-fill me-2"></i>Publicar mi Primer Vehículo
                </a>
            </div>

            <!-- Mensaje cuando no hay resultados de búsqueda -->
            <div id="noResultsMessage" class="text-center mt-5" style="display: none;">
                <i class="bi bi-search display-1 text-muted mb-3"></i>
                <h3 class="mb-3">No se encontraron vehículos</h3>
                <p class="text-muted mb-4">Intenta ajustar los filtros o términos de búsqueda.</p>
                <button class="btn btn-outline-primary" id="clearFiltersBtn">
                    <i class="bi bi-x-circle me-2"></i>Limpiar Filtros
                </button>
            </div>
        </div>
    </main>

    <?php include __DIR__ . './partials/footer.php'; ?>

    <!-- Modal de Confirmación -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Confirmar Acción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="confirmModalBody">
                    <!-- Contenido dinámico -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="confirmActionBtn">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast para notificaciones -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="notificationToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="bi bi-info-circle-fill text-primary me-2"></i>
                <strong class="me-auto">Notificación</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toastBody">
                <!-- Mensaje dinámico -->
            </div>
        </div>
    </div>

    <script src="./../PUBLIC/jquery-3.7.1.min.js"></script>
    <script src="./../Bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="./JS/global.js"></script>
    <script src="./JS/mis_vehiculos.js"></script>
</body>
</html>

