<?php
session_start();
if (!isset($_SESSION['usu_id'])) { header("Location: login.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Cotizaciones - AutoMercado Total</title>
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
    <main class="container py-5">
        <h1 class="mt-5">Mis Cotizaciones</h1>
        <p class="lead">Esta sección está en construcción. Aquí podrás ver y gestionar todos los prospectos y solicitudes de información sobre tus vehículos.</p>
        <a href="escritorio.php" class="btn btn-primary">Volver al Tablero</a>
    </main>

    <?php include __DIR__ . '/partials/footer.php'; ?>

    <script src="../PUBLIC/jquery-3.7.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../VISTAS/JS/global.js"></script> <!-- Para cargar el navbar -->
</body>
</html>