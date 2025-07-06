<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
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
    <title>Gestión de Marcas y Modelos - Admin</title>
    <link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../PUBLIC/css/styles.css" rel="stylesheet">
    <link href="../VISTAS/CSS/admin_marcas_modelos.css" rel="stylesheet">
    <script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/trefoil.js"></script>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">
    <div id="page-loader">
        <l-trefoil size="50" stroke="5" stroke-length="0.15" bg-opacity="0.1" speed="1.4" color="#0d6efd"></l-trefoil>
    </div>

    <header id="navbar-placeholder"></header>

    <main class="flex-grow-1 content-hidden">
        <div class="container py-5">
            <!-- Header Section Mejorado -->
            <div class="header-section">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                    <div>
                        <h1 class="display-5 publishing-title mb-2">Gestión de Marcas y Modelos</h1>
                        <p class="lead text-muted mb-0">Administra las marcas de vehículos y sus respectivos modelos de manera eficiente.</p>
                    </div>
                    <div class="d-flex flex-column flex-sm-row gap-2">
                        <button class="btn btn-outline-secondary btn-lg" id="refreshBtn" title="Actualizar datos">
                            <i class="bi bi-arrow-clockwise me-2"></i>Actualizar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Estadísticas Rápidas -->
            <div class="row g-3 mb-4" id="statsContainer">
                <div class="col-6 col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="bi bi-tags-fill display-4 text-primary mb-2"></i>
                            <h3 class="card-title" id="totalMarcas">-</h3>
                            <p class="card-text text-muted">Total Marcas</p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="bi bi-car-front-fill display-4 text-success mb-2"></i>
                            <h3 class="card-title" id="totalModelos">-</h3>
                            <p class="card-text text-muted">Total Modelos</p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="bi bi-image display-4 text-info mb-2"></i>
                            <h3 class="card-title" id="marcasConLogo">-</h3>
                            <p class="card-text text-muted">Con Logo</p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="bi bi-star-fill display-4 text-warning mb-2"></i>
                            <h3 class="card-title" id="marcaSeleccionada">-</h3>
                            <p class="card-text text-muted">Seleccionada</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros y Búsqueda -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="position-relative">
                                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                                <input type="text" class="form-control ps-5" id="searchInput" placeholder="Buscar marcas por nombre...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex flex-wrap justify-content-md-end gap-2">
                                <button class="btn btn-outline-primary active" id="filterAll">
                                    <i class="bi bi-grid-3x3-gap me-1"></i>Todas
                                </button>
                                <button class="btn btn-outline-success" id="filterWithLogo">
                                    <i class="bi bi-image me-1"></i>Con Logo
                                </button>
                                <button class="btn btn-outline-secondary" id="filterWithoutLogo">
                                    <i class="bi bi-image-alt me-1"></i>Sin Logo
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección de Marcas -->
            <div class="card shadow-sm mb-5">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="bi bi-tags-fill me-2"></i>Marcas de Vehículos</h4>
                    <button class="btn btn-primary" id="btnAbrirModalMarca" data-bs-toggle="modal" data-bs-target="#modalGestionMarca">
                        <i class="bi bi-plus-circle-fill me-2"></i>Añadir Nueva Marca
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="marcasTable">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Logo</th>
                                    <th>Nombre</th>
                                    <th>URL Logo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="marcasTableBody">
                                <!-- Las marcas se cargarán aquí por JS -->
                                <tr><td colspan="5" class="text-center">Cargando marcas...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sección de Modelos (se muestra al seleccionar una marca) -->
            <div id="modelosTableContainer" class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                     <h4 class="mb-0">
                         <i class="bi bi-car-front me-2"></i>Modelos de: 
                         <span id="nombreMarcaSeleccionada" class="fw-bold text-light"></span>
                     </h4>
                    <button class="btn btn-success" id="btnAbrirModalModelo" data-bs-toggle="modal" data-bs-target="#modalGestionModelo">
                        <i class="bi bi-plus-circle-fill me-2"></i>Añadir Nuevo Modelo
                    </button>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="position-relative">
                            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            <input type="text" class="form-control ps-5" id="searchModelosInput" placeholder="Buscar modelos...">
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="modelosTable">
                            <thead class="table-light">
                                <tr>
                                    <th>ID Modelo</th>
                                    <th>Nombre del Modelo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="modelosTableBody">
                                <!-- Los modelos se cargarán aquí por JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal para Gestión de Marcas (Añadir/Editar) -->
    <div class="modal fade" id="modalGestionMarca" tabindex="-1" aria-labelledby="modalMarcaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalMarcaLabel">Gestionar Marca</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formGestionMarca" class="needs-validation" novalidate>
                        <input type="hidden" name="accion" value="guardarMarca">
                        <input type="hidden" id="editMarId" name="mar_id">
                        <div class="mb-3">
                            <label for="mar_nombre" class="form-label">
                                <i class="bi bi-tag me-1"></i>Nombre de la Marca 
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="mar_nombre" name="mar_nombre" required>
                            <div class="invalid-feedback">El nombre de la marca es obligatorio.</div>
                        </div>
                        <div class="mb-3" id="marLogoUrlContainer">
                            <label for="mar_logo_url" class="form-label">
                                <i class="bi bi-image me-1"></i>URL del Logo 
                                <span class="text-muted">(Opcional)</span>
                            </label>
                            <input type="url" class="form-control" id="mar_logo_url" name="mar_logo_url" placeholder="https://ejemplo.com/logo.png">
                            <div class="invalid-feedback">Por favor, ingresa una URL válida.</div>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Recomendamos imágenes en formato PNG o SVG con fondo transparente.
                            </div>
                        </div>
                        <div id="logoPreviewContainer" class="mb-3" style="display: none;">
                            <label class="form-label">Vista Previa del Logo:</label>
                            <div class="border rounded p-3 text-center bg-light">
                                <img id="logoPreview" src="" alt="Vista previa" class="logo-preview">
                            </div>
                        </div>
                        <div id="marcaFormFeedback" class="mt-2"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" form="formGestionMarca" id="btnGuardarMarca">
                        <i class="bi bi-check-circle me-1"></i>Guardar Marca
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Gestión de Modelos (Añadir/Editar) -->
    <div class="modal fade" id="modalGestionModelo" tabindex="-1" aria-labelledby="modalModeloLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalModeloLabel">
                        Gestionar Modelo para <span id="marcaParaModelo" class="fw-bold"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formGestionModelo" class="needs-validation" novalidate>
                        <input type="hidden" name="accion" value="guardarModelo">
                        <input type="hidden" id="editModId" name="mod_id">
                        <input type="hidden" id="selectedMarIdForModelo" name="mar_id_fk">
                        
                        <div class="mb-3">
                            <label for="mod_nombre" class="form-label">
                                <i class="bi bi-car-front me-1"></i>Nombre del Modelo 
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="mod_nombre" name="mod_nombre" required>
                            <div class="invalid-feedback">El nombre del modelo es obligatorio.</div>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Ejemplo: Corolla, Civic, Focus, etc.
                            </div>
                        </div>
                        <div id="modeloFormFeedback" class="mt-2"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-success" form="formGestionModelo" id="btnGuardarModelo">
                        <i class="bi bi-check-circle me-1"></i>Guardar Modelo
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Confirmar Acción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="confirmModalBody">
                    <!-- Contenido dinámico -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmActionBtn">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast para notificaciones -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="notificationToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="bi bi-info-circle-fill text-primary me-2"></i>
                <strong class="me-auto">Notificación</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toastBody">
                <!-- Mensaje dinámico -->
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/partials/footer.php'; ?>

    <script src="../PUBLIC/jquery-3.7.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../VISTAS/JS/global.js"></script>
    <script src="../VISTAS/JS/admin_marcas_modelos.js"></script>
</body>
</html>

