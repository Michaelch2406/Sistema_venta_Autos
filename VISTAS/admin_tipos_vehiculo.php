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
    <script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/trefoil.js"></script>
    <style>
        .table-actions button { margin-right: 5px; }
        .icono-preview { max-height: 30px; max-width: 30px; object-fit: contain; margin-right: 8px; }
        .descripcion-corta {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: help; /* Para indicar que se puede hacer hover para ver más */
        }
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
                    <h1 class="display-5 publishing-title">Gestión de Tipos de Vehículo</h1>
                    <p class="lead text-muted">Administra las categorías principales de vehículos (ej: Sedán, SUV, Camioneta).</p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="bi bi-list-check me-2"></i>Listado de Tipos de Vehículo</h4>
                    <button class="btn btn-primary" id="btnAbrirModalTipoVehiculo" data-bs-toggle="modal" data-bs-target="#modalGestionTipoVehiculo">
                        <i class="bi bi-plus-circle-fill me-2"></i>Añadir Nuevo Tipo
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="tiposVehiculoTable">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Icono</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Activo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tiposVehiculoTableBody">
                                <tr><td colspan="6" class="text-center">Cargando tipos de vehículo...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal para Gestión de Tipos de Vehículo (Añadir/Editar) -->
    <div class="modal fade" id="modalGestionTipoVehiculo" tabindex="-1" aria-labelledby="modalTipoVehiculoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTipoVehiculoLabel">Gestionar Tipo de Vehículo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formGestionTipoVehiculo" class="needs-validation" novalidate>
                        <input type="hidden" name="accion" value="guardarTipoVehiculo">
                        <input type="hidden" id="editTivId" name="tiv_id">
                        <div class="mb-3">
                            <label for="tiv_nombre" class="form-label">Nombre del Tipo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="tiv_nombre" name="tiv_nombre" required>
                            <div class="invalid-feedback">El nombre es obligatorio.</div>
                        </div>
                        <div class="mb-3">
                            <label for="tiv_descripcion" class="form-label">Descripción <span class="text-muted">(Opcional)</span></label>
                            <textarea class="form-control" id="tiv_descripcion" name="tiv_descripcion" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="tiv_icono_url" class="form-label">URL del Icono <span class="text-muted">(Opcional)</span></label>
                            <input type="url" class="form-control" id="tiv_icono_url" name="tiv_icono_url" placeholder="https://ejemplo.com/icono.svg">
                            <div class="invalid-feedback">Ingresa una URL válida si proporcionas una.</div>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="tiv_activo" name="tiv_activo" value="1" checked>
                            <label class="form-check-label" for="tiv_activo">
                                Activo (visible para selección)
                            </label>
                        </div>
                        <div id="tipoVehiculoFormFeedback" class="mt-2"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" form="formGestionTipoVehiculo" id="btnGuardarTipoVehiculo">Guardar Tipo</button>
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