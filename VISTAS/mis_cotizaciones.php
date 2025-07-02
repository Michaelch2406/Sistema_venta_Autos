<?php
session_start();
if (!isset($_SESSION['usu_id'])) {
    header("Location: login.php");
    exit();
}
$nombre_usuario = htmlspecialchars($_SESSION['usu_nombre_completo'] ?? 'Usuario');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Cotizaciones - AutoMercado Total</title>
    <link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../PUBLIC/css/styles.css" rel="stylesheet">
    <link href="../VISTAS/css/mis_cotizaciones.css" rel="stylesheet">
    <script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/trefoil.js"></script>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">
    <div id="page-loader">
        <l-trefoil size="50" stroke="5" stroke-length="0.15" bg-opacity="0.1" speed="1.4" color="#0d6efd"></l-trefoil>
    </div>

    <header id="navbar-placeholder"></header>

    <main class="flex-grow-1 content-hidden">
        <div class="container py-5">
            <div class="page-header pt-4 mb-4">
                <h1 class="display-5">Mis Cotizaciones</h1>
                <p class="lead text-muted">Aquí puedes ver todas las solicitudes de información que has recibido por tus vehículos publicados.</p>
            </div>

            <div id="cotizacionesContainer" class="row g-4">
                <!-- Indicador de Carga -->
                <div id="loadingIndicator" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-3">Cargando tus cotizaciones...</p>
                </div>
                <!-- Mensaje de No Cotizaciones -->
                <div id="noCotizacionesMessage" class="text-center py-5" style="display: none;">
                    <i class="bi bi-journal-x display-1 text-muted"></i>
                    <h4 class="mt-3">Aún no tienes cotizaciones</h4>
                    <p class="text-muted">Cuando un comprador solicite información sobre uno de tus vehículos, aparecerá aquí.</p>
                </div>
                <!-- Las tarjetas de cotización se insertarán aquí por JS -->
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/partials/footer.php'; ?>

    <script src="../PUBLIC/jquery-3.7.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../VISTAS/JS/global.js"></script>
    <script src="../VISTAS/JS/mis_cotizaciones.js"></script>
</body>
</html>