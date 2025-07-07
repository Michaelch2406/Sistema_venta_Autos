<?php
// admin_cotizaciones.php - Versión Mejorada

// =========== INICIO DE LA SECCIÓN ADAPTADA ===========

// TODO: Ajusta la ruta a tu archivo de configuración principal y de Conexion.
$config_global_path = __DIR__ . '/../CONFIG/global.php'; // Asumo que aquí están tus constantes DB_HOST, etc.
$conexion_class_path = __DIR__ . '/../CONFIG/Conexion.php'; // Ruta a tu clase Conexion

if (file_exists($config_global_path)) {
    require_once $config_global_path;
} else {
    die("Error crítico: Archivo global.php no encontrado.");
}

if (file_exists($conexion_class_path)) {
    require_once $conexion_class_path;
} else {
    die("Error crítico: Archivo Conexion.php no encontrado.");
}

// Iniciar sesión si no está ya iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../MODELOS/cotizaciones_m.php';

$db_conn_mysqli = null;
try {
    $conexionObj = new Conexion(); // Instanciamos tu clase Conexion
    $db_conn_mysqli = $conexionObj->conecta(); // Obtenemos el objeto mysqli
} catch (Exception $e) {
    error_log("Error al instanciar Conexion en admin_cotizaciones.php: " . $e->getMessage());
    die("Error crítico: No se pudo establecer la conexión a la base de datos.");
}

if ($db_conn_mysqli === null) {
    die("Error crítico: Conexión a la base de datos (mysqli) no disponible en admin_cotizaciones.php.");
}

// Pasamos el objeto mysqli al modelo
$cotizacionModelo = new CotizacionModelo($db_conn_mysqli);

// =========== FIN DE LA SECCIÓN ADAPTADA ===========

// Verificar si el usuario ha iniciado sesión y tiene el rol de administrador (rol_id: 3)
if (!isset($_SESSION['usu_id']) || !isset($_SESSION['rol_id'])) {
    die("Error: Sesión de administrador no iniciada. Por favor, inicie sesión. (admin_cotizaciones.php)");
}

if ($_SESSION['rol_id'] != 3) {
    die("Acceso denegado. Esta sección es solo para administradores (Rol: " . htmlspecialchars($_SESSION['rol_id']) . "). (admin_cotizaciones.php)");
}

// Nombre de usuario del admin para mostrar en la UI
$admin_nombre_display = htmlspecialchars(($_SESSION['usu_nombre'] ?? '') . ' ' . ($_SESSION['usu_apellido'] ?? 'Admin'));

$todas_las_cotizaciones = [];
$error_message_admin = null;

// Recoger valores de los filtros GET
$filtro_texto_val = trim($_GET['filtro_texto'] ?? '');
$filtro_estado_val = trim($_GET['filtro_estado'] ?? '');
$filtro_fecha_desde_val = trim($_GET['filtro_fecha_desde'] ?? '');
$filtro_fecha_hasta_val = trim($_GET['filtro_fecha_hasta'] ?? '');

try {
    $todas_las_cotizaciones = $cotizacionModelo->obtener_todas_las_cotizaciones(
        $filtro_texto_val,
        $filtro_estado_val,
        $filtro_fecha_desde_val,
        $filtro_fecha_hasta_val
    );
} catch (Exception $e) {
    error_log("Error al obtener todas las cotizaciones para admin: " . $e->getMessage());
    $error_message_admin = "Ocurrió un error al cargar las cotizaciones. Por favor, inténtalo más tarde.";
}

// Los estados para el dropdown de filtro
$estados_disponibles = ['pendiente', 'aprobada_admin', 'contactado', 'cerrado', 'rechazado'];

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Cotizaciones - <?php echo $admin_nombre_display; ?></title>
    
    <!-- Preload de fuentes para mejor rendimiento -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Estilos -->
    <link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../PUBLIC/css/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="../VISTAS/css/admin_cotizaciones.css">
    
    <!-- Loader animado -->
    <script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/trefoil.js"></script>
    
    <!-- Meta tags para SEO y redes sociales -->
    <meta name="description" content="Panel de administración para gestionar cotizaciones de vehículos">
    <meta name="robots" content="noindex, nofollow">
</head>
<body>
    <!-- Loader mejorado -->
    <div id="page-loader" class="page-loader">
        <div class="loader-content">
            <l-trefoil size="50" stroke="5" stroke-length="0.15" bg-opacity="0.1" speed="1.4" color="#4a90e2"></l-trefoil>
            <p class="loader-text">Cargando panel de administración...</p>
        </div>
    </div>

    <!-- Header mejorado -->
    <header id="navbar-placeholder" role="banner">
        <h1>Panel de Administración</h1>
        <nav role="navigation" aria-label="Navegación principal">
            <span>Bienvenido, <?php echo $admin_nombre_display; ?></span>
        </nav>
    </header>

    <!-- Contenido principal -->
    <main role="main">
        <!-- Sección de filtros mejorada -->
        <section id="filtros-busqueda" aria-labelledby="filtros-heading">
            <h2 id="filtros-heading">
                <i class="bi bi-funnel me-2" aria-hidden="true"></i>
                Filtrar Cotizaciones
            </h2>
            
            <form id="form-filtros-admin" method="GET" action="admin_cotizaciones.php" role="search">
                <div class="form-group">
                    <label for="filtro-texto">
                        <i class="bi bi-search me-1" aria-hidden="true"></i>
                        Búsqueda General
                    </label>
                    <input 
                        type="text" 
                        id="filtro-texto" 
                        name="filtro_texto" 
                        placeholder="ID, nombre, email, detalles del vehículo..." 
                        value="<?php echo htmlspecialchars($filtro_texto_val); ?>"
                        aria-describedby="filtro-texto-help"
                    >
                    <small id="filtro-texto-help" class="form-text text-muted">
                        Busca por ID, cliente, email o detalles del vehículo
                    </small>
                </div>
                
                <div class="form-group">
                    <label for="filtro-estado">
                        <i class="bi bi-flag me-1" aria-hidden="true"></i>
                        Estado
                    </label>
                    <select id="filtro-estado" name="filtro_estado" aria-describedby="filtro-estado-help">
                        <option value="" <?php if ($filtro_estado_val === '') echo 'selected'; ?>>
                            Todos los estados
                        </option>
                        <?php foreach ($estados_disponibles as $estado_opt): ?>
                            <option value="<?php echo $estado_opt; ?>" <?php if ($filtro_estado_val === $estado_opt) echo 'selected'; ?>>
                                <?php echo ucfirst(str_replace('_', ' ', $estado_opt)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small id="filtro-estado-help" class="form-text text-muted">
                        Filtra por estado de la cotización
                    </small>
                </div>
                
                <div class="form-group">
                    <label for="filtro-fecha-desde">
                        <i class="bi bi-calendar-event me-1" aria-hidden="true"></i>
                        Fecha Desde
                    </label>
                    <input 
                        type="date" 
                        id="filtro-fecha-desde" 
                        name="filtro_fecha_desde" 
                        value="<?php echo htmlspecialchars($filtro_fecha_desde_val); ?>"
                        aria-describedby="filtro-fecha-desde-help"
                    >
                    <small id="filtro-fecha-desde-help" class="form-text text-muted">
                        Fecha de inicio del rango
                    </small>
                </div>
                
                <div class="form-group">
                    <label for="filtro-fecha-hasta">
                        <i class="bi bi-calendar-check me-1" aria-hidden="true"></i>
                        Fecha Hasta
                    </label>
                    <input 
                        type="date" 
                        id="filtro-fecha-hasta" 
                        name="filtro_fecha_hasta" 
                        value="<?php echo htmlspecialchars($filtro_fecha_hasta_val); ?>"
                        aria-describedby="filtro-fecha-hasta-help"
                    >
                    <small id="filtro-fecha-hasta-help" class="form-text text-muted">
                        Fecha de fin del rango
                    </small>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-filtrar">
                        <i class="bi bi-search me-2" aria-hidden="true"></i>
                        Aplicar Filtros
                    </button>
                    <a href="admin_cotizaciones.php" class="btn-limpiar-filtros">
                        <i class="bi bi-arrow-clockwise me-2" aria-hidden="true"></i>
                        Limpiar Filtros
                    </a>
                </div>
            </form>
        </section>

        <!-- Sección de resultados mejorada -->
        <section id="lista-todas-cotizaciones" aria-labelledby="resultados-heading">
            <h2 id="resultados-heading">
                <i class="bi bi-table me-2" aria-hidden="true"></i>
                Listado General de Cotizaciones
                <?php if (!empty($todas_las_cotizaciones)): ?>
                    <span class="badge bg-primary ms-2"><?php echo count($todas_las_cotizaciones); ?> resultados</span>
                <?php endif; ?>
            </h2>
            
            <?php if ($error_message_admin): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle me-2" aria-hidden="true"></i>
                    <?php echo htmlspecialchars($error_message_admin); ?>
                </div>
            <?php endif; ?>

            <?php if (empty($todas_las_cotizaciones) && !$error_message_admin): ?>
                <div class="alert alert-info" role="alert">
                    <i class="bi bi-info-circle me-2" aria-hidden="true"></i>
                    No hay cotizaciones que coincidan con los filtros aplicados, o no hay cotizaciones registradas en el sistema.
                </div>
            <?php elseif (!empty($todas_las_cotizaciones)): ?>
                <div class="tabla-responsive-contenedor">
                    <table role="table" aria-label="Tabla de cotizaciones">
                        <thead>
                            <tr>
                                <th scope="col">
                                    <i class="bi bi-hash me-1" aria-hidden="true"></i>
                                    ID Cot.
                                </th>
                                <th scope="col">
                                    <i class="bi bi-person me-1" aria-hidden="true"></i>
                                    Solicitante
                                </th>
                                <th scope="col">
                                    <i class="bi bi-envelope me-1" aria-hidden="true"></i>
                                    Email
                                </th>
                                <th scope="col">
                                    <i class="bi bi-calendar me-1" aria-hidden="true"></i>
                                    Fecha Solicitud
                                </th>
                                <th scope="col">
                                    <i class="bi bi-car-front me-1" aria-hidden="true"></i>
                                    Vehículo Solicitado
                                </th>
                                <th scope="col">
                                    <i class="bi bi-flag me-1" aria-hidden="true"></i>
                                    Estado
                                </th>
                                <th scope="col">
                                    <i class="bi bi-currency-euro me-1" aria-hidden="true"></i>
                                    Monto Est. (€)
                                </th>
                                <th scope="col">
                                    <i class="bi bi-gear me-1" aria-hidden="true"></i>
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody id="tabla-cotizaciones-admin-body">
                            <?php foreach ($todas_las_cotizaciones as $cotizacion): ?>
                                <tr data-cotizacion-id="<?php echo htmlspecialchars($cotizacion['cot_id']); ?>" 
                                    aria-label="Cotización <?php echo htmlspecialchars($cotizacion['cot_id']); ?>">
                                    <td>
                                        <strong><?php echo htmlspecialchars($cotizacion['cot_id']); ?></strong>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($cotizacion['nombre_solicitante'] ?? $cotizacion['usu_id_solicitante']); ?>
                                    </td>
                                    <td>
                                        <a href="mailto:<?php echo htmlspecialchars($cotizacion['email_solicitante'] ?? ''); ?>" 
                                           class="text-decoration-none">
                                            <?php echo htmlspecialchars($cotizacion['email_solicitante'] ?? 'N/A'); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <time datetime="<?php echo htmlspecialchars($cotizacion['cot_fecha_solicitud']); ?>">
                                            <?php echo htmlspecialchars(date("d/m/Y H:i", strtotime($cotizacion['cot_fecha_solicitud']))); ?>
                                        </time>
                                    </td>
                                    <td>
                                        <span class="text-truncate" style="max-width: 200px;" 
                                              title="<?php echo htmlspecialchars($cotizacion['cot_detalles_vehiculo_solicitado']); ?>">
                                            <?php echo htmlspecialchars($cotizacion['cot_detalles_vehiculo_solicitado']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="estado-tag estado-<?php echo strtolower(htmlspecialchars($cotizacion['cot_estado'])); ?>"
                                              aria-label="Estado: <?php echo htmlspecialchars(str_replace('_', ' ', $cotizacion['cot_estado'])); ?>">
                                            <?php echo htmlspecialchars(str_replace('_', ' ', $cotizacion['cot_estado'])); ?>
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <strong><?php echo htmlspecialchars(number_format($cotizacion['cot_monto_estimado'], 2, ',', '.')); ?></strong>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="Acciones para cotización <?php echo htmlspecialchars($cotizacion['cot_id']); ?>">
                                            <button class="btn-admin-accion btn-admin-ver-detalle" 
                                                    data-id="<?php echo htmlspecialchars($cotizacion['cot_id']); ?>" 
                                                    title="Ver detalles de la cotización"
                                                    aria-label="Ver detalles de la cotización <?php echo htmlspecialchars($cotizacion['cot_id']); ?>">
                                                <i class="bi bi-eye" aria-hidden="true"></i>
                                            </button>
                                            
                                            <?php if ($cotizacion['cot_estado'] === 'pendiente'): ?>
                                                <button class="btn-admin-accion btn-admin-aprobar" 
                                                        data-id="<?php echo htmlspecialchars($cotizacion['cot_id']); ?>" 
                                                        title="Aprobar cotización"
                                                        aria-label="Aprobar cotización <?php echo htmlspecialchars($cotizacion['cot_id']); ?>">
                                                    <i class="bi bi-check-lg" aria-hidden="true"></i>
                                                </button>
                                                <button class="btn-admin-accion btn-admin-rechazar" 
                                                        data-id="<?php echo htmlspecialchars($cotizacion['cot_id']); ?>" 
                                                        title="Rechazar cotización"
                                                        aria-label="Rechazar cotización <?php echo htmlspecialchars($cotizacion['cot_id']); ?>">
                                                    <i class="bi bi-x-lg" aria-hidden="true"></i>
                                                </button>
                                            <?php elseif ($cotizacion['cot_estado'] === 'aprobada_admin'): ?>
                                                <button class="btn-admin-accion btn-admin-contactado" 
                                                        data-id="<?php echo htmlspecialchars($cotizacion['cot_id']); ?>" 
                                                        title="Marcar como contactado"
                                                        aria-label="Marcar como contactado cotización <?php echo htmlspecialchars($cotizacion['cot_id']); ?>">
                                                    <i class="bi bi-telephone" aria-hidden="true"></i>
                                                </button>
                                            <?php endif; ?>
                                            
                                            <button class="btn-admin-accion btn-admin-editar" 
                                                    data-id="<?php echo htmlspecialchars($cotizacion['cot_id']); ?>" 
                                                    title="Editar cotización"
                                                    aria-label="Editar cotización <?php echo htmlspecialchars($cotizacion['cot_id']); ?>">
                                                <i class="bi bi-pencil" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>

        <!-- Modal mejorado para detalles de cotización -->
        <div id="modal-detalle-cotizacion-admin" class="modal" style="display:none;" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
            <div class="modal-contenido" role="document">
                <button class="modal-cerrar" id="modal-cerrar-detalle-admin" aria-label="Cerrar modal">
                    <i class="bi bi-x-lg" aria-hidden="true"></i>
                </button>
                
                <h3 id="modal-title">
                    <i class="bi bi-file-text me-2" aria-hidden="true"></i>
                    Detalle de la Cotización #<span id="detalle-cotizacion-admin-id-modal"></span>
                </h3>
                
                <div id="detalle-cotizacion-admin-contenido-modal" aria-live="polite">
                    <div class="loading-message">
                        <i class="bi bi-hourglass-split me-2" aria-hidden="true"></i>
                        Cargando detalles de la cotización...
                    </div>
                </div>
                
                <div class="modal-admin-acciones" role="group" aria-label="Acciones de administración">
                    <!-- Los botones de acción se cargarán dinámicamente por JavaScript -->
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include __DIR__ . '/partials/footer.php'; ?>

    <!-- Scripts -->
    <script src="../PUBLIC/jquery-3.7.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../VISTAS/JS/global.js"></script>
    <script src="../VISTAS/JS/admin_cotizaciones.js"></script>
    
    <!-- Script para ocultar el loader cuando la página esté lista -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ocultar el loader con una animación suave
            setTimeout(function() {
                const loader = document.getElementById('page-loader');
                if (loader) {
                    loader.style.opacity = '0';
                    loader.style.transition = 'opacity 0.5s ease-out';
                    setTimeout(function() {
                        loader.style.display = 'none';
                    }, 500);
                }
            }, 800);
        });
    </script>
</body>
</html>

