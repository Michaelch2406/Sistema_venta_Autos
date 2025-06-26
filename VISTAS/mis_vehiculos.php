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
    <link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../PUBLIC/css/styles.css" rel="stylesheet">
    <script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/trefoil.js"></script>
    <style>
        .vehiculo-card {
            transition: box-shadow .3s;
            border: 1px solid #e0e0e0;
        }
        .vehiculo-card:hover {
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
        }
        .vehiculo-card img {
            height: 200px;
            object-fit: cover;
            border-bottom: 1px solid #f0f0f0;
        }
        .estado-disponible { color: green; font-weight: bold; }
        .estado-reservado { color: orange; font-weight: bold; }
        .estado-vendido { color: red; font-weight: bold; }
        .estado-desactivado { color: grey; font-weight: bold; }
        .actions-dropdown .dropdown-item { font-size: 0.9rem; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">
    <div id="page-loader">
        <l-trefoil size="50" stroke="5" stroke-length="0.15" bg-opacity="0.1" speed="1.4" color="#0d6efd"></l-trefoil>
    </div>

    <header id="navbar-placeholder"></header>

    <main class="flex-grow-1 content-hidden">
        <div class="container py-5">
            <div class="pt-4 mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-5 publishing-title">Mis Vehículos Publicados</h1>
                    <p class="lead text-muted">Gestiona tus anuncios, actualiza información y más.</p>
                </div>
                <a href="publicar_vehiculo.php" class="btn btn-primary btn-lg">
                    <i class="bi bi-plus-circle-fill me-2"></i>Publicar Nuevo Vehículo
                </a>
            </div>

            <div id="listaVehiculosContainer" class="row g-4">
                <!-- Los vehículos se cargarán aquí por AJAX -->
                <div class="col-12 text-center" id="loadingVehiculos">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Cargando vehículos...</span>
                    </div>
                    <p class="mt-2">Cargando tus vehículos...</p>
                </div>
            </div>
            <div id="noVehiculosMessage" class="col-12 text-center mt-5" style="display: none;">
                <i class="bi bi-car-front-fill display-1 text-muted"></i>
                <h3 class="mt-3">Aún no has publicado vehículos.</h3>
                <p class="text-muted">¡Empieza ahora y llega a miles de compradores!</p>
                <a href="publicar_vehiculo.php" class="btn btn-success btn-lg mt-2">Publicar mi Primer Vehículo</a>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/partials/footer.php'; ?>

    <script src="../PUBLIC/jquery-3.7.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../VISTAS/JS/global.js"></script>
    <script src="../VISTAS/JS/mis_vehiculos.js"></script>
</body>
</html>