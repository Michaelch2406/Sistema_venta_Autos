<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - AutoMercado Total</title>

    <!-- Bootstrap CSS Local -->
    <link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Tus Estilos Personalizados -->
    <link href="../PUBLIC/css/styles.css" rel="stylesheet">

    <script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/trefoil.js"></script>
</head>

<body class="d-flex flex-column min-vh-100 login-bg">

    <div id="page-loader">
        <l-trefoil size="50" stroke="5" stroke-length="0.15" bg-opacity="0.1" speed="1.4" color="#0d6efd"></l-trefoil>
    </div>

    <!-- Barra de Navegación -->
    <header id="navbar-placeholder"></header>

    <!-- Contenido Principal - Login -->
    <main class="flex-grow-1 d-flex align-items-center justify-content-center py-5 content-hidden">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-4">
                    <div class="text-center mb-4 mt-5 pt-3">
                        <img src="../PUBLIC/Img/Auto_Mercado_Total_LOGO_BLACK_TEXT.png" alt="Logo AutoMercado" width="100">
                    </div>
                    <div class="card shadow-lg">
                        <div class="card-header text-center text-white" style="background-color: rgb(0, 50, 200);">
                            <h3 class="mb-0">Iniciar Sesión</h3>
                        </div>
                        <div class="card-body p-4">
                            <!-- CAMBIAR action cuando tengas el backend -->
                            <form id="loginForm" class="needs-validation" novalidate>
                                <!-- ID añadido, action y method eliminados -->
                                 <div class="mb-3">
                                    <label for="loginUsuario" class="form-label">Usuario o Correo Electrónico</label>
                                    <input type="text" class="form-control" id="loginUsuario" name="usu_usuario" required>
                                    <div class="invalid-feedback">Por favor, ingresa tu usuario o correo.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="loginPassword" class="form-label">Contraseña</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="loginPassword" name="usu_password" required>
                                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="loginPassword"><i class="bi bi-eye-slash"></i></button>
                                        <div class="invalid-feedback">Por favor, ingresa tu contraseña.</div>
                                    </div>
                                </div>
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="rememberMe" name="remember_me">
                                    <label class="form-check-label" for="rememberMe">Recuérdame</label>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary"
                                        style="background-color: rgb(0, 50, 200);">Ingresar</button>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="#" class="text-decoration-none small">¿Olvidaste tu contraseña?</a>
                                </div>
                            </form>
                        </div>
                        <div class="card-footer text-center py-3">
                            <small>¿No tienes cuenta? <a href="registro.php" class="text-decoration-none">Regístrate
                                    aquí</a></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Pie de Página -->
    <?php include __DIR__ . '/partials/footer.php'; ?>

    <!-- Scripts -->
    <script src="../PUBLIC/jquery-3.7.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../VISTAS/JS/global.js"></script>
    <script src="../VISTAS/JS/login.js"></script>
</body>

</html>