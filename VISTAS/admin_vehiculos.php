<?php
session_start();
$rol_admin_id = 3; // ID del rol Administrador
if (!isset($_SESSION['usu_id']) || !isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != $rol_admin_id) {
    echo "<!DOCTYPE html><html><head><title>Acceso Denegado</title><link href='../Bootstrap/css/bootstrap.min.css' rel='stylesheet'></head><body class='container mt-5'><div class='alert alert-danger'><h1>Acceso Denegado</h1><p>No tienes permisos para acceder a esta sección.</p><a href='escritorio.php' class='btn btn-primary'>Volver al Escritorio</a></div></body></html>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Vehículos - Admin</title>
    <link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" type="text/css" href="../DataTables/datatables.min.css"/>
    <link href="../PUBLIC/css/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="../VISTAS/CSS/admin_vehiculos.css">
    <script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/trefoil.js"></script>
    
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
                    <h1 class="display-5 publishing-title">Gestionar Vehículos</h1>
                    <p class="lead text-muted">Administra, aprueba, edita o desactiva los anuncios de vehículos.</p>
                </div>
                 <a href="publicar_vehiculo.php" class="btn btn-success btn-lg">
                    <i class="bi bi-plus-circle-fill me-2"></i>Publicar Nuevo Vehículo
                </a>
            </div>

            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Listado de Vehículos</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tablaVehiculosAdmin" class="table table-striped table-hover table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Imagen</th>
                                    <th>Título (Marca Modelo Año)</th>
                                    <th>Publicador</th>
                                    <th>Precio</th>
                                    <th>Condición</th>
                                    <th>Estado Actual</th>
                                    <th>Fecha Pub.</th>
                                    <th>Ubicación</th>
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

    <!-- Modal para Confirmar Acción (Desactivar/Aprobar/Etc.) -->
    <div class="modal fade" id="modalConfirmarAccion" tabindex="-1" aria-labelledby="modalConfirmarAccionLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" id="modalConfirmarHeader">
                    <h5 class="modal-title" id="modalConfirmarAccionLabel">Confirmar Acción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="modalConfirmarMensaje">¿Estás seguro de que deseas realizar esta acción?</p>
                    <input type="hidden" id="vehiculoIdConfirmar">
                    <input type="hidden" id="accionConfirmar">
                    <input type="hidden" id="nuevoEstadoConfirmar"> 
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnEjecutarAccion">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/partials/footer.php'; ?>

    <script src="../PUBLIC/jquery-3.7.1.min.js"></script>
    <script type="text/javascript" src="../DataTables/datatables.min.js"></script>
    <script src="../Bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../VISTAS/JS/global.js"></script>
    <script src="../VISTAS/JS/admin_vehiculos.js"></script> 
</body>
</html>
