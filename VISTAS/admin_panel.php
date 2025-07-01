<?php
session_start();

if (!isset($_SESSION['usu_id'])) {
    header("Location: login.php");
    exit();
}

// ID del rol Administrador (ajusta según tu BD, ej: 3)
$rol_admin_id = 3; 
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != $rol_admin_id) {
    echo "<!DOCTYPE html><html><head><title>Acceso Denegado</title><link href='../Bootstrap/css/bootstrap.min.css' rel='stylesheet'></head><body class='container mt-5'><div class='alert alert-danger'><h1>Acceso Denegado</h1><p>No tienes los permisos necesarios para acceder a esta sección.</p><p><a href='escritorio.php' class='btn btn-primary'>Volver al Escritorio</a></p></div></body></html>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - AutoMercado Total</title>
    <link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../PUBLIC/css/styles.css" rel="stylesheet">
    <!-- Archivo CSS específico para el panel de admin -->
    <link href="../VISTAS/css/admin_panel.css" rel="stylesheet">
    <script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/trefoil.js"></script>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">
    <div id="page-loader">
        <l-trefoil size="50" stroke="5" stroke-length="0.15" bg-opacity="0.1" speed="1.4" color="#0d6efd"></l-trefoil>
    </div>

    <header id="navbar-placeholder"></header>

    <main class="flex-grow-1 content-hidden">
        <div class="admin-panel-header text-center">
            <div class="container">
                <h1 class="display-4">Panel de Administración</h1>
                <p class="lead">Gestión y configuración integral de AutoMercado Total.</p>
            </div>
        </div>

        <div class="container pb-5">
            <!-- Sección de Gestión de Contenido y Catálogos -->
            <div>
                <h2 class="dashboard-section-title"><i class="bi bi-boxes me-2"></i>Gestión de Catálogo</h2>
                <div class="row g-4 dashboard-cards">
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 dashboard-card shadow-sm">
                            <div class="card-body text-center d-flex flex-column">
                                <i class="bi bi-car-front-fill text-success dashboard-card-icon"></i>
                                <h5 class="card-title">Todos los Vehículos</h5>
                                <p class="card-text text-muted small">Administrar, aprobar, editar o desactivar todos los anuncios de vehículos.</p>
                                <a href="admin_vehiculos.php" class="btn btn-success mt-auto">Gestionar Vehículos</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 dashboard-card shadow-sm">
                            <div class="card-body text-center d-flex flex-column">
                                <i class="bi bi-tags-fill text-info dashboard-card-icon"></i>
                                <h5 class="card-title">Marcas y Modelos</h5>
                                <p class="card-text text-muted small">Añadir, editar o eliminar marcas y sus respectivos modelos.</p>
                                <a href="admin_marcas_modelos.php" class="btn btn-info mt-auto">Ir a Marcas/Modelos</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 dashboard-card shadow-sm">
                            <div class="card-body text-center d-flex flex-column">
                                <i class="bi bi-list-check text-warning dashboard-card-icon"></i>
                                <h5 class="card-title">Tipos de Vehículo</h5>
                                <p class="card-text text-muted small">Administrar las categorías principales de vehículos (SUV, Sedán, etc.).</p>
                                <a href="admin_tipos_vehiculo.php" class="btn btn-warning mt-auto">Gestionar Tipos</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección de Gestión de Usuarios -->
            <div>
                <h2 class="dashboard-section-title"><i class="bi bi-people-fill me-2"></i>Gestión de Usuarios</h2>
                <div class="row g-4 dashboard-cards">
                     <div class="col-md-6 col-lg-4">
                        <div class="card h-100 dashboard-card shadow-sm">
                            <div class="card-body text-center d-flex flex-column">
                                <i class="bi bi-person-lines-fill text-primary dashboard-card-icon"></i>
                                <h5 class="card-title">Usuarios y Roles</h5>
                                <p class="card-text text-muted small">Ver, editar, verificar usuarios y gestionar sus roles (Cliente, Asesor, Admin).</p>
                                <a href="admin_usuarios_roles.php" class="btn btn-primary mt-auto">Gestionar Usuarios</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 dashboard-card shadow-sm">
                            <div class="card-body text-center d-flex flex-column">
                                <i class="bi bi-plus-circle-dotted text-primary-emphasis dashboard-card-icon"></i>
                                <h5 class="card-title">Publicar Vehículo (Admin)</h5>
                                <p class="card-text text-muted small">Publicar un vehículo directamente como administrador.</p>
                                <a href="publicar_vehiculo.php" class="btn btn-outline-primary mt-auto">Publicar Ahora</a>
                            </div>
                        </div>
                    </div>
                     <div class="col-md-6 col-lg-4">
                        <div class="card h-100 dashboard-card shadow-sm">
                            <div class="card-body text-center d-flex flex-column">
                                <i class="bi bi-journal-check text-success-emphasis dashboard-card-icon"></i>
                                <h5 class="card-title">Mis Publicaciones (Admin)</h5>
                                <p class="card-text text-muted small">Ver y gestionar los vehículos publicados por esta cuenta de administrador.</p>
                                <a href="mis_vehiculos.php" class="btn btn-outline-success mt-auto">Ver Mis Publicaciones</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección de Operaciones y Configuración -->
            <div>
                 <h2 class="dashboard-section-title"><i class="bi bi-sliders me-2"></i>Operaciones y Sistema</h2>
                <div class="row g-4 dashboard-cards">
                    
                    <!-- === TARJETA AÑADIDA AQUÍ === -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 dashboard-card shadow-sm">
                            <div class="card-body text-center d-flex flex-column">
                                <i class="bi bi-chat-quote-fill text-cyan dashboard-card-icon"></i>
                                <h5 class="card-title">Gestión de Cotizaciones</h5>
                                <p class="card-text text-muted small">Revisar y gestionar todas las solicitudes de información de los usuarios.</p>
                                <a href="admin_cotizaciones.php" class="btn btn-cyan mt-auto">Gestionar Cotizaciones</a>
                            </div>
                        </div>
                    </div>
                    <!-- ========================== -->

                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 dashboard-card shadow-sm">
                            <div class="card-body text-center d-flex flex-column">
                                <i class="bi bi-cash-coin text-danger dashboard-card-icon"></i>
                                <h5 class="card-title">Ventas y Pagos</h5>
                                <p class="card-text text-muted small">Monitorear el historial de ventas, transacciones y estados de pago.</p>
                                <a href="admin_ventas.php" class="btn btn-danger mt-auto">Revisar Ventas</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 dashboard-card shadow-sm">
                            <div class="card-body text-center d-flex flex-column">
                                <i class="bi bi-gear-wide-connected text-secondary dashboard-card-icon"></i>
                                <h5 class="card-title">Configuración General</h5>
                                <p class="card-text text-muted small">Ajustes globales del sistema, parámetros y configuraciones avanzadas.</p>
                                <a href="admin_configuracion_general.php" class="btn btn-secondary mt-auto">Ir a Configuración</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-12 text-center">
                     <a href="../CONTROLADORES/logout.php" class="btn btn-lg btn-outline-danger"><i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión</a>
                 </div>
            </div>

        </div>
    </main>

    <?php include __DIR__ . '/partials/footer.php'; ?>

    <script src="../PUBLIC/jquery-3.7.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../VISTAS/JS/global.js"></script>
    <!-- Archivo JS específico para el panel de admin -->
    <script src="../VISTAS/JS/admin_panel.js"></script>
</body>
</html>