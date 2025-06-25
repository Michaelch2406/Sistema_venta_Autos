<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['usu_id'])) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../MODELOS/usuarios_m.php';
$usuarios_model = new Usuario(); // Corregido: Usar el nombre de clase Usuario (singular)
$usuario_actual = $usuarios_model->getUsuarioParaConfiguracion($_SESSION['usu_id']);

if (!$usuario_actual) {
    // Manejar el caso en que no se puedan cargar los datos del usuario
    // Esto podría ser una redirección, un mensaje de error, etc.
    $_SESSION['error_message'] = "Error al cargar los datos de tu cuenta. Por favor, intenta más tarde.";
    // header("Location: escritorio.php"); // O alguna otra página de dashboard
    // exit();
    // Por ahora, mostraremos un error en la misma página para depuración,
    // pero en producción sería mejor redirigir o tener una página de error más amigable.
}

$page_title = "Configuración de la Cuenta";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - AutoMercado Total</title>
    <link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../PUBLIC/css/styles.css" rel="stylesheet">
    <link href="CSS/configuracion_cuenta.css" rel="stylesheet">
    <script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/trefoil.js"></script>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">
    <div id="page-loader">
        <l-trefoil size="50" stroke="5" stroke-length="0.15" bg-opacity="0.1" speed="1.4" color="#0d6efd"></l-trefoil>
    </div>

    <header id="navbar-placeholder"></header>

    <main class="flex-grow-1 content-hidden py-5">
        <div class="container">
            <h1 class="display-5 fw-bold mb-4"><?php echo htmlspecialchars($page_title); ?></h1>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($_SESSION['error_message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($_SESSION['success_message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <!-- Mensaje general para respuestas AJAX -->
            <div id="mensajeGeneral" class="alert d-none" role="alert"></div>

            <?php if ($usuario_actual): ?>
            <div class="row g-5">
                <!-- Formulario de Datos Personales -->
                <div class="col-lg-7">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>Información Personal</h4>
                        </div>
                        <div class="card-body p-4">
                            <form id="formPerfil" novalidate>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="usu_nombre" class="form-label">Nombre(s): <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="usu_nombre" name="nombre" value="<?php echo htmlspecialchars($usuario_actual['usu_nombre'] ?? ''); ?>" required>
                                        <div class="invalid-feedback">Por favor, ingresa tu nombre.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="usu_apellido" class="form-label">Apellido(s): <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="usu_apellido" name="apellido" value="<?php echo htmlspecialchars($usuario_actual['usu_apellido'] ?? ''); ?>" required>
                                        <div class="invalid-feedback">Por favor, ingresa tus apellidos.</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="usu_cedula" class="form-label">Cédula: <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="usu_cedula" name="cedula" value="<?php echo htmlspecialchars($usuario_actual['usu_cedula'] ?? ''); ?>" required maxlength="13">
                                        <div class="invalid-feedback" id="feedbackCedula">Por favor, ingresa tu cédula.</div>
                                    </div>
                                     <div class="col-md-6">
                                        <label for="usu_fnacimiento" class="form-label">Fecha de Nacimiento:</label>
                                        <input type="date" class="form-control" id="usu_fnacimiento" name="fnacimiento" value="<?php echo htmlspecialchars($usuario_actual['usu_fnacimiento'] ?? ''); ?>">
                                        <div class="invalid-feedback">Ingresa una fecha válida.</div>
                                    </div>

                                    <div class="col-12">
                                        <label for="usu_email" class="form-label">Correo Electrónico:</label>
                                        <input type="email" class="form-control" id="usu_email" name="email" value="<?php echo htmlspecialchars($usuario_actual['usu_email'] ?? ''); ?>" readonly disabled>
                                        <small class="form-text text-muted">El correo electrónico no se puede modificar desde aquí.</small>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="usu_telefono" class="form-label">Teléfono:</label>
                                        <input type="tel" class="form-control" id="usu_telefono" name="telefono" value="<?php echo htmlspecialchars($usuario_actual['usu_telefono'] ?? ''); ?>" maxlength="15">
                                        <div class="invalid-feedback">Ingresa un número de teléfono válido.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="usu_usuario" class="form-label">Nombre de Usuario:</label>
                                        <input type="text" class="form-control" id="usu_usuario" name="usuario" value="<?php echo htmlspecialchars($usuario_actual['usu_usuario'] ?? ''); ?>" readonly disabled>
                                        <small class="form-text text-muted">El nombre de usuario no se puede modificar.</small>
                                    </div>

                                    <div class="col-12">
                                        <label for="usu_direccion" class="form-label">Dirección:</label>
                                        <textarea class="form-control" id="usu_direccion" name="direccion" rows="3"><?php echo htmlspecialchars($usuario_actual['usu_direccion'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                                <hr class="my-4">
                                <button class="btn btn-primary btn-lg w-100" type="submit" id="btnGuardarPerfil">
                                    <i class="bi bi-save me-2"></i>Guardar Cambios de Perfil
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Formulario de Cambio de Contraseña -->
                <div class="col-lg-5">
                    <div class="card shadow-sm">
                        <div class="card-header bg-warning text-dark">
                             <h4 class="mb-0"><i class="bi bi-key-fill me-2"></i>Cambiar Contraseña</h4>
                        </div>
                        <div class="card-body p-4">
                            <form id="formCambiarPassword" novalidate>
                                <div class="mb-3">
                                    <label for="pass_actual" class="form-label">Contraseña Actual: <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="pass_actual" name="pass_actual" required>
                                    <div class="invalid-feedback">Ingresa tu contraseña actual.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="pass_nueva" class="form-label">Nueva Contraseña: <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="pass_nueva" name="pass_nueva" required minlength="8">
                                    <div class="invalid-feedback" id="feedbackNuevaPass">La nueva contraseña debe tener al menos 8 caracteres.</div>
                                    <small class="form-text text-muted">Mínimo 8 caracteres. Se recomienda incluir mayúsculas, minúsculas, números y símbolos.</small>
                                </div>
                                <div class="mb-3">
                                    <label for="pass_confirmar" class="form-label">Confirmar Nueva Contraseña: <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="pass_confirmar" name="pass_confirmar" required>
                                    <div class="invalid-feedback">Confirma tu nueva contraseña. Deben coincidir.</div>
                                </div>
                                <hr class="my-4">
                                <button class="btn btn-warning btn-lg w-100" type="submit" id="btnCambiarPassword">
                                    <i class="bi bi-shield-lock me-2"></i>Actualizar Contraseña
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php else: ?>
                <div class="alert alert-warning text-center" role="alert">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                    No se pudieron cargar los datos de tu cuenta en este momento. Si el problema persiste, contacta a soporte.
                </div>
            <?php endif; ?>

        </div>
    </main>

    <?php include __DIR__ . '/partials/footer.php'; ?>

    <script src="../PUBLIC/jquery-3.7.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../VISTAS/JS/global.js"></script>
    <script src="../VISTAS/JS/configuracion_cuenta.js"></script>
</body>
</html>
