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
    <script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/trefoil.js"></script>
    <style>
        #tablaUsuariosAdmin th, #tablaUsuariosAdmin td { vertical-align: middle; }
        .modal-header { background-color: #0d6efd; color: white; }
        .modal-header .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }
    </style>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">
    <div id="page-loader">
        <l-trefoil size="50" stroke="5" stroke-length="0.15" bg-opacity="0.1" speed="1.4" color="#0d6efd"></l-trefoil>
    </div>

    <header id="navbar-placeholder"></header>

    <main class="flex-grow-1 content-hidden">
        <div class="container-fluid py-5 px-lg-5">
            <div class="pt-4 mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-5 publishing-title">Gestionar Usuarios y Roles</h1>
                    <p class="lead text-muted">Administra los usuarios del sistema y sus permisos.</p>
                </div>
                <button class="btn btn-success btn-lg" id="btnNuevoUsuario">
                    <i class="bi bi-person-plus-fill me-2"></i>Crear Nuevo Usuario
                </button>
            </div>

            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Listado de Usuarios</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tablaUsuariosAdmin" class="table table-striped table-hover table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Usuario</th>
                                    <th>Nombre Completo</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Rol</th>
                                    <th>Verificado</th>
                                    <th>Registrado</th>
                                    <th>Acciones</th>
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

    <!-- Modal para Crear/Editar Usuario -->
    <div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalUsuarioLabel">Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formUsuario" class="needs-validation" novalidate>
                        <input type="hidden" id="usu_id_form" name="usu_id">
                        <input type="hidden" id="accionForm" name="accion">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="usu_usuario_form" class="form-label">Nombre de Usuario <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="usu_usuario_form" name="usu_usuario" required>
                                <div class="invalid-feedback">Ingresa un nombre de usuario.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="rol_id_form" class="form-label">Rol <span class="text-danger">*</span></label>
                                <select class="form-select" id="rol_id_form" name="rol_id" required>
                                    <option value="" selected disabled>Selecciona un rol...</option>
                                    <!-- Se poblará con JS -->
                                </select>
                                <div class="invalid-feedback">Selecciona un rol.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="usu_nombre_form" class="form-label">Nombre(s) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="usu_nombre_form" name="usu_nombre" required>
                                <div class="invalid-feedback">Ingresa el/los nombre(s).</div>
                            </div>
                            <div class="col-md-6">
                                <label for="usu_apellido_form" class="form-label">Apellido(s) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="usu_apellido_form" name="usu_apellido" required>
                                <div class="invalid-feedback">Ingresa el/los apellido(s).</div>
                            </div>
                            <div class="col-md-12">
                                <label for="usu_email_form" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="usu_email_form" name="usu_email" required>
                                <div class="invalid-feedback">Ingresa un correo válido.</div>
                            </div>
                            <div class="col-md-12">
                                <label for="usu_password_form" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="usu_password_form" name="usu_password" placeholder="Dejar en blanco para no cambiar">
                                <small class="form-text text-muted">Mínimo 8 caracteres si se establece una nueva.</small>
                                <div class="invalid-feedback">La contraseña debe tener al menos 8 caracteres.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="usu_telefono_form" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="usu_telefono_form" name="usu_telefono">
                            </div>
                            <div class="col-md-6">
                                <label for="usu_fnacimiento_form" class="form-label">Fecha de Nacimiento</label>
                                <input type="date" class="form-control" id="usu_fnacimiento_form" name="usu_fnacimiento">
                            </div>
                            <div class="col-md-12">
                                <label for="usu_direccion_form" class="form-label">Dirección</label>
                                <textarea class="form-control" id="usu_direccion_form" name="usu_direccion" rows="2"></textarea>
                            </div>
                             <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="usu_verificado_form" name="usu_verificado" value="1">
                                    <label class="form-check-label" for="usu_verificado_form">Usuario Verificado</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" form="formUsuario" id="btnGuardarUsuario">Guardar Cambios</button>
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
</body>
</html>