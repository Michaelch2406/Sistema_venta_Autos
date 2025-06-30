<?php
session_start();
if (!isset($_SESSION['usu_id'])) {
    header("Location: login.php");
    exit();
}

// Redirección para administradores
if (isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 3) {
    header("Location: admin_panel.php");
    exit();
}

$rol_id = $_SESSION['rol_id'] ?? 0;
$nombre_usuario = htmlspecialchars($_SESSION['usu_nombre_completo'] ?? 'Usuario');

// Definir permisos basados en roles
$es_vendedor_o_asesor = in_array($rol_id, [1, 2]);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Tablero - AutoMercado Total</title>
    <link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../PUBLIC/css/styles.css" rel="stylesheet">
    <!-- CSS Específico para esta página -->
    <link href="../VISTAS/css/escritorio.css" rel="stylesheet">
    <script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/trefoil.js"></script>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">
    <div id="page-loader">
        <l-trefoil size="50" stroke="5" stroke-length="0.15" bg-opacity="0.1" speed="1.4" color="#0d6efd"></l-trefoil>
    </div>

    <header id="navbar-placeholder"></header>

    <main class="flex-grow-1 content-hidden">
        <div class="container py-5">
            <div class="dashboard-header pt-4 mb-5">
                <h1 class="display-5">¡Hola, <?php echo $nombre_usuario; ?>!</h1>
                <p class="lead text-muted">Bienvenido(a) a tu tablero de control de AutoMercado Total.</p>
                <?php if ($rol_id == 1): ?>
                    <p>Aquí puedes gestionar tus vehículos en venta, ver tus favoritos y configurar tu cuenta.</p>
                <?php elseif ($rol_id == 2): ?>
                    <p>Desde aquí puedes asistir a los clientes, gestionar vehículos y revisar las actividades de la plataforma.</p>
                <?php endif; ?>
            </div>

            <div class="row g-4 dashboard-cards">
                
                <!-- Tarjetas para Vendedores y Asesores -->
                <?php if ($es_vendedor_o_asesor): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 dashboard-card shadow-sm">
                            <div class="card-body text-center d-flex flex-column">
                                <i class="bi bi-plus-circle-fill text-primary dashboard-card-icon"></i>
                                <h5 class="card-title">Nuevo Anuncio</h5>
                                <p class="card-text text-muted small">Publica tu vehículo usado en pocos pasos y llega a miles de compradores.</p>
                                <a href="publicar_vehiculo.php" class="btn btn-primary mt-auto">Publicar Vehículo</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 dashboard-card shadow-sm">
                            <div class="card-body text-center d-flex flex-column">
                                <i class="bi bi-car-front-fill text-success dashboard-card-icon"></i>
                                <h5 class="card-title">Mis Vehículos Publicados</h5>
                                <p class="card-text text-muted small">Gestiona tus anuncios, actualiza información y responde a interesados.</p>
                                <a href="mis_vehiculos.php" class="btn btn-success mt-auto">Ver Mis Vehículos</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 dashboard-card shadow-sm">
                            <div class="card-body text-center d-flex flex-column">
                                <i class="bi bi-chat-quote-fill text-info dashboard-card-icon"></i>
                                <h5 class="card-title">Mis Cotizaciones</h5>
                                <p class="card-text text-muted small">Revisa las solicitudes de información de clientes potenciales.</p>
                                <a href="mis_cotizaciones.php" class="btn btn-info mt-auto">Gestionar Cotizaciones</a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- === TARJETA AÑADIDA === -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 dashboard-card shadow-sm">
                            <div class="card-body text-center d-flex flex-column">
                                <i class="bi bi-patch-check-fill text-secondary dashboard-card-icon"></i>
                                <h5 class="card-title">Historial de Ventas</h5>
                                <p class="card-text text-muted small">Consulta el registro de los vehículos que has vendido exitosamente.</p>
                                <a href="mis_ventas.php" class="btn btn-secondary mt-auto">Ver Mis Ventas</a>
                            </div>
                        </div>
                    </div>
                    <!-- ===================== -->
                <?php endif; ?>

                <!-- Tarjetas para todos los usuarios -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 dashboard-card shadow-sm">
                        <div class="card-body text-center d-flex flex-column">
                            <i class="bi bi-heart-fill text-danger dashboard-card-icon"></i>
                            <h5 class="card-title">Mis Favoritos</h5>
                            <p class="card-text text-muted small">Revisa los vehículos que has marcado como favoritos.</p>
                            <a href="mis_favoritos.php" class="btn btn-danger mt-auto">Ver Favoritos</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 dashboard-card shadow-sm">
                        <div class="card-body text-center d-flex flex-column">
                            <i class="bi bi-person-gear text-warning dashboard-card-icon"></i>
                            <h5 class="card-title">Mi Perfil</h5>
                            <p class="card-text text-muted small">Actualiza tu información personal y contraseña.</p>
                            <a href="configuracion_cuenta.php" class="btn btn-warning mt-auto">Editar Perfil</a>
                        </div>
                    </div>
                </div>

                 <div class="col-12 mt-4">
                     <a href="logout.php" class="btn btn-outline-danger"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</a>
                 </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/partials/footer.php'; ?>

    <script src="../PUBLIC/jquery-3.7.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../VISTAS/JS/global.js"></script>
    <script src="../VISTAS/JS/escritorio.js"></script>
</body>
</html>