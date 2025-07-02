<?php
// mis_cotizaciones.php
ini_set('display_errors', 0); 
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../php_error.log'); 

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
    error_log("Error al instanciar Conexion en mis_cotizaciones.php: " . $e->getMessage());
    die("Error crítico: No se pudo establecer la conexión a la base de datos.");
}

if ($db_conn_mysqli === null) {
    die("Error crítico: Conexión a la base de datos (mysqli) no disponible en mis_cotizaciones.php.");
}

// Pasamos el objeto mysqli al modelo
$cotizacionModelo = new CotizacionModelo($db_conn_mysqli);

// =========== FIN DE LA SECCIÓN ADAPTADA ===========


// Verificar si el usuario ha iniciado sesión y tiene el rol adecuado (1 o 2)
if (!isset($_SESSION['usu_id']) || !isset($_SESSION['rol_id'])) {
    // Redirigir al login si no hay sesión. Ajusta la ruta a tu archivo de login.
    // header("Location: /ruta/a/login.php");
    // exit();
    die("Error: Sesión no iniciada o rol no definido. Por favor, inicie sesión. (mis_cotizaciones.php)");
}

if (!in_array($_SESSION['rol_id'], [1, 2])) {
    // Redirigir si el rol no es 1 o 2.
    // header("Location: /ruta/a/acceso_denegado.php");
    // exit();
    die("Acceso denegado. No tienes los permisos necesarios para ver esta página (Rol: " . htmlspecialchars($_SESSION['rol_id']) . "). (mis_cotizaciones.php)");
}

$usu_id_actual = $_SESSION['usu_id'];
// Asumimos que 'usu_nombre' y 'usu_apellido' están en la sesión después del login.
// Si no, necesitarías obtenerlos de la BD aquí usando $usu_id_actual.
$nombre_usuario_display = htmlspecialchars(($_SESSION['usu_nombre'] ?? '') . ' ' . ($_SESSION['usu_apellido'] ?? 'Usuario'));


$cotizaciones_usuario = [];
$error_message = null;

try {
    $cotizaciones_usuario = $cotizacionModelo->obtener_cotizaciones_por_usuario($usu_id_actual);
    // El SP ya debería devolver cot_monto_estimado (veh_precio)
} catch (Exception $e) { // Captura excepciones generales del modelo si las lanza
    error_log("Error al obtener cotizaciones para usuario ID $usu_id_actual: " . $e->getMessage());
    $error_message = "Ocurrió un error al cargar tus cotizaciones. Por favor, inténtalo más tarde.";
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Cotizaciones - <?php echo $nombre_usuario_display; ?></title>
    <link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../PUBLIC/css/styles.css" rel="stylesheet">
    <link href="../VISTAS/css/mis_cotizaciones.css" rel="stylesheet">
    <script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/trefoil.js"></script>

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
        <section id="lista-cotizaciones">
            <h2>Historial de Cotizaciones de <?php echo $nombre_usuario_display; ?></h2>

            <?php if ($error_message): ?>
                <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>

            <?php if (empty($cotizaciones_usuario) && !$error_message): ?>
                <p>Aún no tienes cotizaciones registradas.</p>
                <?php /* <a href="/solicitar_cotizacion.php" class="btn-accion">Solicitar Nueva Cotización</a> */ ?>
            <?php elseif (!empty($cotizaciones_usuario)): ?>
                <div class="tabla-responsive-contenedor">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fecha Solicitud</th>
                                <th>Vehículo Solicitado</th>
                                <th>Estado</th>
                                <th>Monto Estimado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cotizaciones_usuario as $cotizacion): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($cotizacion['cot_id']); ?></td>
                                    <td><?php echo htmlspecialchars(date("d/m/Y H:i", strtotime($cotizacion['cot_fecha_solicitud']))); ?></td>
                                    <td><?php echo htmlspecialchars($cotizacion['vehiculo_nombre_display']); // Usar el alias del SP ?></td>
                                    <td><span class="estado-tag estado-<?php echo strtolower(htmlspecialchars($cotizacion['cot_estado'])); ?>"><?php echo htmlspecialchars($cotizacion['cot_estado']); ?></span></td>
                                    <td class="text-right"><?php echo htmlspecialchars(number_format($cotizacion['cot_monto_estimado'], 2, ',', '.')); ?> €</td>
                                    <td>
                                        <button class="btn-accion btn-ver-detalle" data-id="<?php echo htmlspecialchars($cotizacion['cot_id']); ?>" title="Ver Detalle">
                                            <i class="icon-eye"></i> Ver Detalle <?php // Reemplaza con tu clase de icono real ?>
                                        </button>
                                        <?php /* // Lógica de botones adicionales según estado:
                                        if ($cotizacion['cot_estado'] === 'aprobada_admin') {
                                            // Opción para que el usuario confirme/acepte formalmente
                                        } elseif ($cotizacion['cot_estado'] === 'pendiente') {
                                            // Opción para cancelar solicitud si aplica
                                        }
                                        */ ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>

        <!-- Modal o sección para mostrar el detalle de la cotización -->
        <div id="modal-detalle-cotizacion" class="modal" style="display:none;">
            <div class="modal-contenido">
                <span class="modal-cerrar" id="modal-cerrar-detalle">×</span>
                <h3>Detalle de la Cotización #<span id="detalle-cotizacion-id-modal"></span></h3>
                <div id="detalle-cotizacion-contenido-modal">
                    <p class="loading-message">Cargando detalles...</p>
                </div>
                 <div class="modal-acciones">
                    <!-- Botones como Aceptar/Rechazar se añadirían aquí si el usuario puede realizar esas acciones -->
                    <button class="btn-accion btn-imprimir-cot" data-id="">Imprimir</button>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/partials/footer.php'; ?>

    <script src="../PUBLIC/jquery-3.7.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../VISTAS/JS/global.js"></script>
    <script src="../VISTAS/js/mis_cotizaciones.js"></script>
</body>
</html>