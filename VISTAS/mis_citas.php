<?php
// VISTAS/mis_citas.php
ini_set('display_errors', 0); error_reporting(E_ALL);
ini_set('log_errors', 1); ini_set('error_log', __DIR__ . './../php_error.log'); 

if (session_status() == PHP_SESSION_NONE) { session_start(); }

require_once __DIR__ . './../CONFIG/Conexion.php';
require_once __DIR__ . './../MODELOS/citas_m.php';

$db_conn_mysqli = null;
try {
    $conexionObj = new Conexion();
    $db_conn_mysqli = $conexionObj->conecta();
} catch (Exception $e) { die("Error crítico: No se pudo conectar a la base de datos."); }
if ($db_conn_mysqli === null) { die("Error crítico: Conexión no disponible."); }

$citaModelo = new CitaModelo($db_conn_mysqli);

if (!isset($_SESSION['usu_id']) || !in_array($_SESSION['rol_id'], [1, 2])) {
    die("Acceso denegado. Por favor, inicie sesión.");
}

$usu_id_actual = $_SESSION['usu_id'];
$nombre_usuario_display = htmlspecialchars(($_SESSION['usu_nombre'] ?? '') . ' ' . ($_SESSION['usu_apellido'] ?? 'Usuario'));

$citas_usuario = $citaModelo->obtener_citas_por_usuario($usu_id_actual);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Citas - <?php echo $nombre_usuario_display; ?></title>
    <link href="./../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="./../PUBLIC/css/styles.css" rel="stylesheet">
    <link href="./CSS/mis_citas.css" rel="stylesheet">
</head>

<body>
    <header id="navbar-placeholder"></header>
    <main>
        <section id="lista-citas">
            <h2>Historial de Citas de <?php echo $nombre_usuario_display; ?></h2>
            <?php if (empty($citas_usuario)): ?>
            <p>Aún no tienes citas registradas.</p>
            <?php else: ?>
            <div class="tabla-responsive-contenedor">
                <table>
                    <thead>
                        <tr>
                            <th>ID Cita</th>
                            <th>Fecha Solicitud</th>
                            <th>Fecha Programada</th>
                            <th>Vehículo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($citas_usuario as $cita): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($cita['cit_id']); ?></td>
                            <td><?php echo htmlspecialchars(date("d/m/Y H:i", strtotime($cita['cit_fecha_solicitud']))); ?>
                            </td>
                            <td>
                                <?php if($cita['cit_fecha_disponibilidad']): ?>
                                <strong><?php echo htmlspecialchars(date("d/m/Y", strtotime($cita['cit_fecha_disponibilidad']))); ?></strong>
                                a las <span
                                    class="badge bg-primary"><?php echo htmlspecialchars($cita['cit_hora_disponibilidad']); ?></span>
                                <?php else: ?>
                                <span class="text-muted">Sin programar</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($cita['vehiculo_nombre_display']); ?></td>
                            <td><span
                                    class="estado-tag estado-<?php echo strtolower(htmlspecialchars($cita['cit_estado'])); ?>"><?php echo ucfirst(htmlspecialchars($cita['cit_estado'])); ?></span>
                            </td>
                            <td>
                                <button class="btn-accion btn-ver-detalle"
                                    data-id="<?php echo htmlspecialchars($cita['cit_id']); ?>">
                                    <i class="icon-eye"></i> Ver Detalle
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </section>

        <div id="modal-detalle-cita" class="modal" style="display:none;">
            <div class="modal-contenido">
                <span class="modal-cerrar" id="modal-cerrar-detalle">×</span>
                <h3>Detalle de la Cita #<span id="detalle-cita-id-modal"></span></h3>
                <div id="detalle-cita-contenido-modal">
                    <p class="loading-message">Cargando...</p>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . './partials/footer.php'; ?>
    <script src="./../PUBLIC/jquery-3.7.1.min.js"></script>
    <script src="./../Bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="./JS/global.js"></script>
    <script src="./JS/mis_citas.js"></script>
</body>

</html>