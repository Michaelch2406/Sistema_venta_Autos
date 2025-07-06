<?php
session_start();
$rol_admin_id = 3; // ID del rol Administrador
if (!isset($_SESSION['usu_id']) || !isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != $rol_admin_id) {
    echo "<!DOCTYPE html><html><head><title>Acceso Denegado</title><link href='../Bootstrap/css/bootstrap.min.css' rel='stylesheet'></head><body class='container mt-5'><div class='alert alert-danger'><h1>Acceso Denegado</h1><p>No tienes permisos.</p><a href='escritorio.php' class='btn btn-primary'>Volver</a></div></body></html>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Usuarios y Roles - Admin</title>
    <link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- DataTables CSS (local) -->
    <link rel="stylesheet" type="text/css" href="../DataTables/datatables.min.css"/>
    <link href="../PUBLIC/css/styles.css" rel="stylesheet">
    <link href="../VISTAS/CSS/admin_usuarios_roles.css" rel="stylesheet">
    <script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/trefoil.js"></script>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">
    <div id="page-loader">
        <l-trefoil size="50" stroke="5" stroke-length="0.15" bg-opacity="0.1" speed="1.4" color="#0d6efd"></l-trefoil>
    </div>

    <header id="navbar-placeholder"></header>

    <main class="flex-grow-1 content-hidden">
        <div class="container-fluid py-4 admin-usuarios-container">
            <!-- Header mejorado -->
            <div class="admin-header text-center mb-4 fade-in">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <h1 class="display-4 mb-3">
                                <i class="bi bi-people-fill me-3"></i>
                                Gestionar Usuarios y Roles
                            </h1>
                            <p class="lead mb-0">
                                Administra los usuarios del sistema y sus permisos de manera eficiente y segura
                            </p>
                        </div>
                        <div class="col-lg-4">
                            <button class="btn btn-create-user btn-lg" id="btnNuevoUsuario">
                                <i class="bi bi-person-plus-fill"></i>
                                Crear Nuevo Usuario
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estadísticas del dashboard -->
            <div class="stats-container slide-in">
                <div class="row g-4 mb-4">
                    <div class="col-lg-3 col-md-6">
                        <div class="stat-card hover-lift">
                            <div class="stat-icon users">
                                <i class="bi bi-people"></i>
                            </div>
                            <div class="stat-number" id="totalUsuarios">-</div>
                            <div class="stat-label">Total Usuarios</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="stat-card hover-lift">
                            <div class="stat-icon verified">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <div class="stat-number" id="usuariosVerificados">-</div>
                            <div class="stat-label">Verificados</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="stat-card hover-lift">
                            <div class="stat-icon roles">
                                <i class="bi bi-person-badge"></i>
                            </div>
                            <div class="stat-number" id="totalRoles">-</div>
                            <div class="stat-label">Roles Activos</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="stat-card hover-lift">
                            <div class="stat-icon recent">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <div class="stat-number" id="usuariosRecientes">-</div>
                            <div class="stat-label">Últimos 30 días</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tarjeta principal mejorada -->
            <div class="card main-card hover-lift slide-in">
                <div class="card-header card-header-custom">
                    <h5 class="mb-0">
                        <i class="bi bi-table"></i>
                        Listado Completo de Usuarios
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="tablaUsuariosAdmin" class="table table-custom" style="width:100%">
                            <thead>
                                <tr>
                                    <th>
                                        <i class="bi bi-hash me-1"></i>ID
                                    </th>
                                    <th>
                                        <i class="bi bi-person me-1"></i>Usuario
                                    </th>
                                    <th>
                                        <i class="bi bi-person-lines-fill me-1"></i>Nombre Completo
                                    </th>
                                    <th>
                                        <i class="bi bi-envelope me-1"></i>Email
                                    </th>
                                    <th>
                                        <i class="bi bi-card-text me-1"></i>Cédula
                                    </th>
                                    <th>
                                        <i class="bi bi-telephone me-1"></i>Teléfono
                                    </th>
                                    <th>
                                        <i class="bi bi-person-badge me-1"></i>Rol
                                    </th>
                                    <th>
                                        <i class="bi bi-shield-check me-1"></i>Verificado
                                    </th>
                                    <th>
                                        <i class="bi bi-calendar-plus me-1"></i>Registrado
                                    </th>
                                    <th>
                                        <i class="bi bi-gear me-1"></i>Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Filas se cargarán por DataTables -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal mejorado para Crear/Editar Usuario -->
    <div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content modal-content-custom">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title" id="modalUsuarioLabel">
                        <i class="bi bi-person-gear"></i>
                        Gestionar Usuario
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body modal-body-custom">
                    <form id="formUsuario" class="needs-validation" novalidate>
                        <input type="hidden" id="usu_id_form" name="usu_id">
                        <input type="hidden" id="accionForm" name="accion">

                        <!-- Información básica -->
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <h6 class="text-primary fw-bold mb-3">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Información Básica
                                </h6>
                            </div>
                            <div class="col-md-6">
                                <label for="usu_usuario_form" class="form-label form-label-custom">
                                    <i class="bi bi-at"></i>
                                    Nombre de Usuario 
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-custom" id="usu_usuario_form" name="usu_usuario" required placeholder="Ej: juan.perez">
                                <div class="invalid-feedback">
                                    <i class="bi bi-exclamation-circle me-1"></i>
                                    Ingresa un nombre de usuario válido.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="rol_id_form" class="form-label form-label-custom">
                                    <i class="bi bi-person-badge"></i>
                                    Rol del Sistema 
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-select form-select-custom" id="rol_id_form" name="rol_id" required>
                                    <option value="" selected disabled>Selecciona un rol...</option>
                                    <!-- Se poblará con JS -->
                                </select>
                                <div class="invalid-feedback">
                                    <i class="bi bi-exclamation-circle me-1"></i>
                                    Selecciona un rol para el usuario.
                                </div>
                            </div>
                        </div>

                        <!-- Datos personales -->
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <h6 class="text-primary fw-bold mb-3">
                                    <i class="bi bi-person-lines-fill me-2"></i>
                                    Datos Personales
                                </h6>
                            </div>
                            <div class="col-md-6">
                                <label for="usu_nombre_form" class="form-label form-label-custom">
                                    <i class="bi bi-person"></i>
                                    Nombre(s) 
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-custom" id="usu_nombre_form" name="usu_nombre" required placeholder="Nombres del usuario">
                                <div class="invalid-feedback">
                                    <i class="bi bi-exclamation-circle me-1"></i>
                                    Ingresa el/los nombre(s).
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="usu_apellido_form" class="form-label form-label-custom">
                                    <i class="bi bi-person"></i>
                                    Apellido(s) 
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-custom" id="usu_apellido_form" name="usu_apellido" required placeholder="Apellidos del usuario">
                                <div class="invalid-feedback">
                                    <i class="bi bi-exclamation-circle me-1"></i>
                                    Ingresa el/los apellido(s).
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="usu_cedula_form" class="form-label form-label-custom">
                                    <i class="bi bi-card-text"></i>
                                    Cédula de Identidad 
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-custom" id="usu_cedula_form" name="usu_cedula" required maxlength="13" pattern="\d{10}|\d{13}" placeholder="1234567890 o 1234567890001">
                                <div class="invalid-feedback">
                                    <i class="bi bi-exclamation-circle me-1"></i>
                                    Ingresa una cédula válida (10 o 13 dígitos).
                                </div>
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Formato: 10 dígitos para cédula o 13 para RUC.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="usu_fnacimiento_form" class="form-label form-label-custom">
                                    <i class="bi bi-calendar-date"></i>
                                    Fecha de Nacimiento
                                </label>
                                <input type="date" class="form-control form-control-custom" id="usu_fnacimiento_form" name="usu_fnacimiento">
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Campo opcional para completar el perfil.
                                </div>
                            </div>
                        </div>

                        <!-- Información de contacto -->
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <h6 class="text-primary fw-bold mb-3">
                                    <i class="bi bi-telephone-fill me-2"></i>
                                    Información de Contacto
                                </h6>
                            </div>
                            <div class="col-md-12">
                                <label for="usu_email_form" class="form-label form-label-custom">
                                    <i class="bi bi-envelope"></i>
                                    Correo Electrónico 
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="email" class="form-control form-control-custom" id="usu_email_form" name="usu_email" required placeholder="usuario@ejemplo.com">
                                <div class="invalid-feedback">
                                    <i class="bi bi-exclamation-circle me-1"></i>
                                    Ingresa un correo electrónico válido.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="usu_telefono_form" class="form-label form-label-custom">
                                    <i class="bi bi-telephone"></i>
                                    Teléfono
                                </label>
                                <input type="tel" class="form-control form-control-custom" id="usu_telefono_form" name="usu_telefono" placeholder="0987654321">
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Número de contacto del usuario.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="usu_password_form" class="form-label form-label-custom">
                                    <i class="bi bi-key"></i>
                                    Contraseña
                                </label>
                                <div class="password-field">
                                    <input type="password" class="form-control form-control-custom" id="usu_password_form" name="usu_password" placeholder="Dejar en blanco para no cambiar">
                                    <button type="button" class="password-toggle" onclick="togglePassword('usu_password_form')">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div class="password-strength" id="passwordStrength"></div>
                                <small class="form-text text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Mínimo 8 caracteres si se establece una nueva.
                                </small>
                                <div class="invalid-feedback">
                                    <i class="bi bi-exclamation-circle me-1"></i>
                                    La contraseña debe tener al menos 8 caracteres.
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="usu_direccion_form" class="form-label form-label-custom">
                                    <i class="bi bi-geo-alt"></i>
                                    Dirección
                                </label>
                                <textarea class="form-control form-control-custom" id="usu_direccion_form" name="usu_direccion" rows="2" placeholder="Dirección completa del usuario"></textarea>
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Dirección de residencia o trabajo del usuario.
                                </div>
                            </div>
                        </div>

                        <!-- Configuración de cuenta -->
                        <div class="row g-3">
                            <div class="col-12">
                                <h6 class="text-primary fw-bold mb-3">
                                    <i class="bi bi-gear-fill me-2"></i>
                                    Configuración de Cuenta
                                </h6>
                            </div>
                            <div class="col-12">
                                <div class="form-check form-check-custom">
                                    <input class="form-check-input form-check-input-custom" type="checkbox" role="switch" id="usu_verificado_form" name="usu_verificado" value="1">
                                    <label class="form-check-label fw-semibold" for="usu_verificado_form">
                                        <i class="bi bi-shield-check me-2 text-success"></i>
                                        Usuario Verificado y Activo
                                    </label>
                                    <div class="form-text mt-2">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Los usuarios verificados tienen acceso completo a sus funciones asignadas.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer modal-footer-custom">
                    <button type="button" class="btn btn-modal-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-modal-primary" form="formUsuario" id="btnGuardarUsuario">
                        <i class="bi bi-check-circle me-2"></i>
                        Guardar Cambios
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/partials/footer.php'; ?>

    <script src="../PUBLIC/jquery-3.7.1.min.js"></script>
    <!-- DataTables JS (local) -->
    <script type="text/javascript" src="../DataTables/datatables.min.js"></script>
    <script src="../Bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../VISTAS/JS/global.js"></script>
    <script src="../VISTAS/JS/admin_usuarios_roles.js"></script>
    
    <script>
        // Función para mostrar/ocultar contraseña
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const button = field.nextElementSibling;
            const icon = button.querySelector('i');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                field.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }

        // Indicador de fuerza de contraseña
        document.getElementById('usu_password_form').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('passwordStrength');
            
            if (password.length === 0) {
                strengthBar.className = 'password-strength';
                strengthBar.style.width = '0%';
                return;
            }
            
            let strength = 0;
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            strengthBar.className = 'password-strength';
            if (strength <= 1) {
                strengthBar.classList.add('weak');
            } else if (strength <= 2) {
                strengthBar.classList.add('medium');
            } else {
                strengthBar.classList.add('strong');
            }
        });
    </script>
</body>
</html>

