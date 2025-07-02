<?php
// admin_cotizaciones.php

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
    // header("Location: /ruta/a/login.php"); exit;
    die("Error: Sesión de administrador no iniciada. Por favor, inicie sesión. (admin_cotizaciones.php)");
}

if ($_SESSION['rol_id'] != 3) {
    // header("Location: /ruta/a/acceso_denegado.php"); exit;
    die("Acceso denegado. Esta sección es solo para administradores (Rol: " . htmlspecialchars($_SESSION['rol_id']) . "). (admin_cotizaciones.php)");
}

// Nombre de usuario del admin para mostrar en la UI, usando los nombres de columna de tu tabla usuarios
$admin_nombre_display = htmlspecialchars(($_SESSION['usu_nombre'] ?? '') . ' ' . ($_SESSION['usu_apellido'] ?? 'Admin'));

$todas_las_cotizaciones = [];
$error_message_admin = null;

// Recoger valores de los filtros GET
$filtro_texto_val = trim($_GET['filtro_texto'] ?? ''); // 'filtro_texto' en lugar de 'filtro_usuario' para más generalidad
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
    <link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../PUBLIC/css/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="../VISTAS/css/admin_cotizaciones.css">
    <script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/trefoil.js"></script>
    <!-- <link rel="stylesheet" href="ruta/a/tu/font-awesome.css"> o similar para iconos -->
</head>
<body>
    <div id="page-loader" class="page-loader">
        <div class="loader-content">
            <l-trefoil size="50" stroke="5" stroke-length="0.15" bg-opacity="0.1" speed="1.4" color="#dc2626"></l-trefoil>
            <p class="loader-text">Cargando cotizaciones...</p>
        </div>
    </div>

    <header id="navbar-placeholder"></header>

    <main>
        <section id="filtros-busqueda">
            <h2>Filtrar Cotizaciones</h2>
            <form id="form-filtros-admin" method="GET" action="admin_cotizaciones.php">
                <div>
                    <label for="filtro-texto">Buscar (ID, Cliente, Email, Vehículo):</label>
                    <input type="text" id="filtro-texto" name="filtro_texto" placeholder="ID, nombre, email, detalles vehículo..." value="<?php echo htmlspecialchars($filtro_texto_val); ?>">
                </div>
                <div>
                    <label for="filtro-estado">Estado:</label>
                    <select id="filtro-estado" name="filtro_estado">
                        <option value="" <?php if ($filtro_estado_val === '') echo 'selected'; ?>>Todos</option>
                        <?php foreach ($estados_disponibles as $estado_opt): ?>
                            <option value="<?php echo $estado_opt; ?>" <?php if ($filtro_estado_val === $estado_opt) echo 'selected'; ?>>
                                <?php echo ucfirst(str_replace('_', ' ', $estado_opt)); // Formatear para display ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="filtro-fecha-desde">Desde:</label>
                    <input type="date" id="filtro-fecha-desde" name="filtro_fecha_desde" value="<?php echo htmlspecialchars($filtro_fecha_desde_val); ?>">
                </div>
                <div>
                    <label for="filtro-fecha-hasta">Hasta:</label>
                    <input type="date" id="filtro-fecha-hasta" name="filtro_fecha_hasta" value="<?php echo htmlspecialchars($filtro_fecha_hasta_val); ?>">
                </div>
                <button type="submit" class="btn-filtrar">Filtrar</button>
                <a href="admin_cotizaciones.php" class="btn-limpiar-filtros">Limpiar Filtros</a>
            </form>
        </section>

        <section id="lista-todas-cotizaciones">
            <h2>Listado General de Cotizaciones</h2>
            <?php if ($error_message_admin): ?>
                <p class="error"><?php echo htmlspecialchars($error_message_admin); ?></p>
            <?php endif; ?>

            <?php if (empty($todas_las_cotizaciones) && !$error_message_admin): ?>
                <p>No hay cotizaciones que coincidan con los filtros aplicados, o no hay cotizaciones registradas en el sistema.</p>
            <?php elseif (!empty($todas_las_cotizaciones)): ?>
                <div class="tabla-responsive-contenedor">
                    <table>
                        <thead>
                            <tr>
                                <th>ID Cot.</th>
                                <th>Solicitante</th>
                                <th>Email</th>
                                <th>Fecha Solicitud</th>
                                <th>Vehículo Solicitado</th>
                                <th>Estado</th>
                                <th>Monto Est. (€)</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-cotizaciones-admin-body">
                            <?php foreach ($todas_las_cotizaciones as $cotizacion): ?>
                                <tr data-cotizacion-id="<?php echo htmlspecialchars($cotizacion['cot_id']); ?>">
                                    <td><?php echo htmlspecialchars($cotizacion['cot_id']); ?></td>
                                    <td><?php echo htmlspecialchars($cotizacion['nombre_solicitante'] ?? $cotizacion['usu_id_solicitante']); ?></td>
                                    <td><?php echo htmlspecialchars($cotizacion['email_solicitante'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars(date("d/m/Y H:i", strtotime($cotizacion['cot_fecha_solicitud']))); ?></td>
                                    <td><?php echo htmlspecialchars($cotizacion['cot_detalles_vehiculo_solicitado']); // Nombre del vehículo ?></td>
                                    <td><span class="estado-tag estado-<?php echo strtolower(htmlspecialchars($cotizacion['cot_estado'])); ?>"><?php echo htmlspecialchars(str_replace('_', ' ', $cotizacion['cot_estado'])); ?></span></td>
                                    <td class="text-right"><?php echo htmlspecialchars(number_format($cotizacion['cot_monto_estimado'], 2, ',', '.')); ?></td>
                                    <td>
                                        <button class="btn-admin-accion btn-admin-ver-detalle" data-id="<?php echo htmlspecialchars($cotizacion['cot_id']); ?>" title="Ver Detalle"><i class="icon-eye"></i></button>
                                        <?php if ($cotizacion['cot_estado'] === 'pendiente'): ?>
                                            <button class="btn-admin-accion btn-admin-aprobar" data-id="<?php echo htmlspecialchars($cotizacion['cot_id']); ?>" title="Aprobar (a aprobada_admin)"><i class="icon-check"></i></button>
                                            <button class="btn-admin-accion btn-admin-rechazar" data-id="<?php echo htmlspecialchars($cotizacion['cot_id']); ?>" title="Rechazar"><i class="icon-cancel"></i></button>
                                        <?php elseif ($cotizacion['cot_estado'] === 'aprobada_admin'): ?>
                                             <button class="btn-admin-accion btn-admin-contactado" data-id="<?php echo htmlspecialchars($cotizacion['cot_id']); ?>" title="Marcar como Contactado"><i class="icon-phone"></i></button> <?php // Ejemplo, necesitarás un icono icon-phone ?>
                                        <?php endif; ?>
                                        <button class="btn-admin-accion btn-admin-editar" data-id="<?php echo htmlspecialchars($cotizacion['cot_id']); ?>" title="Editar Cotización (Notas/etc.)"><i class="icon-edit"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>

        <!-- Modal para Detalles de Cotización (Admin View) -->
        <div id="modal-detalle-cotizacion-admin" class="modal" style="display:none;">
            <div class="modal-contenido">
                <span class="modal-cerrar" id="modal-cerrar-detalle-admin">×</span>
                <h3>Detalle de la Cotización (Admin) #<span id="detalle-cotizacion-admin-id-modal"></span></h3>
                <div id="detalle-cotizacion-admin-contenido-modal">
                    <p class="loading-message">Cargando detalles...</p>
                </div>
                 <div class="modal-admin-acciones">
                    <!-- Botones de acción del modal se cargarán por JS basado en el estado de la cotización -->
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/partials/footer.php'; ?>

    <script src="../PUBLIC/jquery-3.7.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../VISTAS/JS/global.js"></script>
    <script src="../VISTAS/JS/admin_cotizaciones.js"></script>
</body>
</html>