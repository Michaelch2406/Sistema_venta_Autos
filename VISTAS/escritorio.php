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

// Definir qué puede hacer cada rol
$puede_publicar_y_gestionar_sus_vehiculos = in_array($rol_id, [1, 2, 3]); // Cliente/Vendedor, Asesor, Admin
$es_asesor = ($rol_id == 2);
$es_admin = ($rol_id == 3);

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
                <?php elseif ($es_asesor): ?>
                    <p>Desde aquí puedes asistir a los clientes, gestionar vehículos y revisar las actividades de la plataforma.</p>
                <?php elseif ($es_admin): ?>
                     <p>Este es tu centro de comando para administrar todos los aspectos del sistema.</p>
                <?php endif; ?>
            </div>

            <div class="row g-4 dashboard-cards">
                <?php if ($puede_publicar_y_gestionar_sus_vehiculos): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 dashboard-card shadow-sm">
                        <div class="card-body text-center">
                            <div class="dashboard-card-icon text-primary mb-3">
                                <i class="bi bi-plus-circle-fill" style="font-size: 3rem;"></i>
                            </div>
                            <h5 class="card-title">Nuevo Anuncio</h5>
                            <p class="card-text text-muted small">Publica tu vehículo usado en pocos pasos y llega a miles de compradores.</p>
                            <a href="publicar_vehiculo.php" class="btn btn-primary mt-auto">Publicar Vehículo</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 dashboard-card shadow-sm">
                        <div class="card-body text-center">
                            <div class="dashboard-card-icon text-success mb-3">
                                <i class="bi bi-car-front-fill" style="font-size: 3rem;"></i>
                            </div>
                            <h5 class="card-title">Mis Vehículos Publicados</h5>
                            <p class="card-text text-muted small">Gestiona tus anuncios, actualiza información y responde a interesados.</p>
                            <a href="mis_vehiculos.php" class="btn btn-success mt-auto">Ver Mis Vehículos</a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 dashboard-card shadow-sm">
                        <div class="card-body text-center">
                            <div class="dashboard-card-icon text-danger mb-3">
                                <i class="bi bi-heart-fill" style="font-size: 3rem;"></i>
                            </div>
                            <h5 class="card-title">Mis Favoritos</h5>
                            <p class="card-text text-muted small">Revisa los vehículos que has marcado como favoritos.</p>
                            <a href="mis_favoritos.php" class="btn btn-danger mt-auto">Ver Favoritos</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 dashboard-card shadow-sm">
                        <div class="card-body text-center">
                            <div class="dashboard-card-icon text-warning mb-3">
                                <i class="bi bi-person-gear" style="font-size: 3rem;"></i>
                            </div>
                            <h5 class="card-title">Mi Perfil</h5>
                            <p class="card-text text-muted small">Actualiza tu información personal y contraseña.</p>
                            <a href="configuracion_cuenta.php" class="btn btn-warning mt-auto">Editar Perfil</a>
                        </div>
                    </div>
                </div>

                <?php if ($es_asesor || $es_admin): ?>
                    <?php if ($es_asesor && !$es_admin): // Solo para Asesor, no para Admin que tiene su propio panel ?>
                         <div class="col-md-6 col-lg-4">
                            <div class="card h-100 dashboard-card shadow-sm">
                                <div class="card-body text-center">
                                    <div class="dashboard-card-icon text-info mb-3">
                                        <i class="bi bi-headset" style="font-size: 3rem;"></i>
                                    </div>
                                    <h5 class="card-title">Consultas de Clientes</h5>
                                    <p class="card-text text-muted small">Revisar y responder consultas sobre vehículos.</p>
                                    <a href="asesor_consultas.php" class="btn btn-info mt-auto">Ver Consultas</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                 <div class="col-12 mt-4">
                     <a href="logout.php" class="btn btn-outline-danger"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
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