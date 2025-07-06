<?php
session_start();
$rol_admin_id = 3; // ID del rol Administrador (Ajusta según tu BD)
if (!isset($_SESSION['usu_id']) || !isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != $rol_admin_id) {
    echo "<!DOCTYPE html><html><head><title>Acceso Denegado</title><link href='../Bootstrap/css/bootstrap.min.css' rel='stylesheet'></head><body class='container mt-5'><div class='alert alert-danger'><h1>Acceso Denegado</h1><p>No tienes permisos para acceder a esta página.</p><a href='escritorio.php' class='btn btn-primary'>Volver al Escritorio</a></div></body></html>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Tipos de Vehículo - Admin</title>
    <link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../PUBLIC/css/styles.css" rel="stylesheet">
    <link href="../VISTAS/CSS/admin_tipos_vehiculo.css" rel="stylesheet">
    <script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/trefoil.js"></script>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">
    <div id="page-loader">
        <l-trefoil size="50" stroke="5" stroke-length="0.15" bg-opacity="0.1" speed="1.4" color="#0d6efd"></l-trefoil>
    </div>

    <header id="navbar-placeholder"></header>

    <main class="flex-grow-1 content-hidden">
        <div class="container py-4 admin-tipos-container">
            <!-- Header mejorado -->
            <div class="admin-header text-center mb-4 fade-in">
                <div class="container">
                    <h1 class="display-4 mb-3">
                        <i class="bi bi-car-front-fill me-3"></i>
                        Gestión de Tipos de Vehículo
                    </h1>
                    <p class="lead mb-0">
                        Administra las categorías principales de vehículos de manera eficiente y moderna
                    </p>
                    <div class="mt-3">
                        <span class="badge badge-custom badge-activo me-2">
                            <i class="bi bi-speedometer2 me-1"></i>
                            Panel Administrativo
                        </span>
                        <span class="badge badge-custom badge-inactivo">
                            <i class="bi bi-shield-check me-1"></i>
                            Acceso Seguro
                        </span>
                    </div>
                </div>
            </div>

            <!-- Tarjeta principal mejorada -->
            <div class="card main-card hover-lift slide-in">
                <div class="card-header card-header-custom d-flex justify-content-between align-items-center flex-wrap">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="bi bi-list-check text-white fs-4"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="mb-1">Listado de Tipos de Vehículo</h4>
                            <small class="text-muted">Gestiona todas las categorías disponibles</small>
                        </div>
                    </div>
                    <button class="btn btn-add-new" id="btnAbrirModalTipoVehiculo" data-bs-toggle="modal" data-bs-target="#modalGestionTipoVehiculo">
                        <i class="bi bi-plus-circle-fill"></i>
                        Añadir Nuevo Tipo
                    </button>
                </div>
                
                <div class="card-body p-0">
                    <!-- Estadísticas rápidas -->
                    <div class="row g-0 border-bottom">
                        <div class="col-md-4 p-3 text-center border-end">
                            <div class="d-flex align-items-center justify-content-center">
                                <div class="me-2">
                                    <i class="bi bi-collection text-primary fs-3"></i>
                                </div>
                                <div>
                                    <div class="fw-bold fs-5" id="totalTipos">-</div>
                                    <small class="text-muted">Total Tipos</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 p-3 text-center border-end">
                            <div class="d-flex align-items-center justify-content-center">
                                <div class="me-2">
                                    <i class="bi bi-check-circle text-success fs-3"></i>
                                </div>
                                <div>
                                    <div class="fw-bold fs-5" id="tiposActivos">-</div>
                                    <small class="text-muted">Activos</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 p-3 text-center">
                            <div class="d-flex align-items-center justify-content-center">
                                <div class="me-2">
                                    <i class="bi bi-pause-circle text-warning fs-3"></i>
                                </div>
                                <div>
                                    <div class="fw-bold fs-5" id="tiposInactivos">-</div>
                                    <small class="text-muted">Inactivos</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla mejorada -->
                    <div class="table-responsive">
                        <table class="table table-custom mb-0" id="tiposVehiculoTable">
                            <thead>
                                <tr>
                                    <th width="80">
                                        <i class="bi bi-hash me-1"></i>ID
                                    </th>
                                    <th width="100">
                                        <i class="bi bi-image me-1"></i>Icono
                                    </th>
                                    <th>
                                        <i class="bi bi-tag me-1"></i>Nombre
                                    </th>
                                    <th>
                                        <i class="bi bi-text-paragraph me-1"></i>Descripción
                                    </th>
                                    <th width="120">
                                        <i class="bi bi-toggle-on me-1"></i>Estado
                                    </th>
                                    <th width="150">
                                        <i class="bi bi-gear me-1"></i>Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="tiposVehiculoTableBody">
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="loading-spinner mb-3"></div>
                                            <span class="text-muted">Cargando tipos de vehículo...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal mejorado para Gestión de Tipos de Vehículo -->
    <div class="modal fade" id="modalGestionTipoVehiculo" tabindex="-1" aria-labelledby="modalTipoVehiculoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content modal-content-custom">
                <div class="modal-header modal-header-custom">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-car-front text-white fs-5"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="modal-title mb-0" id="modalTipoVehiculoLabel">Gestionar Tipo de Vehículo</h5>
                            <small class="opacity-75">Complete la información requerida</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body modal-body-custom">
                    <form id="formGestionTipoVehiculo" class="needs-validation" novalidate>
                        <input type="hidden" name="accion" value="guardarTipoVehiculo">
                        <input type="hidden" id="editTivId" name="tiv_id">
                        
                        <!-- Nombre del tipo -->
                        <div class="mb-4">
                            <label for="tiv_nombre" class="form-label form-label-custom">
                                <i class="bi bi-tag-fill me-2 text-primary"></i>
                                Nombre del Tipo 
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-custom" id="tiv_nombre" name="tiv_nombre" required placeholder="Ej: Sedán, SUV, Camioneta...">
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i>
                                El nombre es obligatorio.
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="mb-4">
                            <label for="tiv_descripcion" class="form-label form-label-custom">
                                <i class="bi bi-text-paragraph me-2 text-primary"></i>
                                Descripción 
                                <span class="text-muted">(Opcional)</span>
                            </label>
                            <textarea class="form-control form-control-custom" id="tiv_descripcion" name="tiv_descripcion" rows="3" placeholder="Describe las características principales de este tipo de vehículo..."></textarea>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Proporciona detalles que ayuden a identificar este tipo de vehículo.
                            </div>
                        </div>

                        <!-- URL del icono -->
                        <div class="mb-4">
                            <label for="tiv_icono_url" class="form-label form-label-custom">
                                <i class="bi bi-image me-2 text-primary"></i>
                                URL del Icono 
                                <span class="text-muted">(Opcional)</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-link-45deg"></i>
                                </span>
                                <input type="url" class="form-control form-control-custom" id="tiv_icono_url" name="tiv_icono_url" placeholder="https://ejemplo.com/icono.svg">
                            </div>
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i>
                                Ingresa una URL válida si proporcionas una.
                            </div>
                            <div class="form-text">
                                <i class="bi bi-lightbulb me-1"></i>
                                Recomendamos usar iconos en formato SVG para mejor calidad.
                            </div>
                        </div>

                        <!-- Estado activo -->
                        <div class="form-check form-check-custom mb-4">
                            <input class="form-check-input form-check-input-custom" type="checkbox" id="tiv_activo" name="tiv_activo" value="1" checked>
                            <label class="form-check-label fw-semibold" for="tiv_activo">
                                <i class="bi bi-toggle-on me-2 text-success"></i>
                                Tipo activo y visible para selección
                            </label>
                            <div class="form-text mt-2">
                                <i class="bi bi-info-circle me-1"></i>
                                Los tipos inactivos no aparecerán en los formularios de registro de vehículos.
                            </div>
                        </div>

                        <!-- Área de feedback -->
                        <div id="tipoVehiculoFormFeedback" class="mt-3"></div>
                    </form>
                </div>
                
                <div class="modal-footer modal-footer-custom">
                    <button type="button" class="btn btn-modal-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-modal-primary" form="formGestionTipoVehiculo" id="btnGuardarTipoVehiculo">
                        <i class="bi bi-check-circle me-2"></i>
                        Guardar Tipo
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/partials/footer.php'; ?>

    <script src="../PUBLIC/jquery-3.7.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../VISTAS/JS/global.js"></script>
    <script src="../VISTAS/JS/admin_tipos_vehiculo.js"></script>
</body>
</html>

