<?php
// VISTAS/admin_citas.php
ini_set('display_errors', 0); error_reporting(E_ALL);
ini_set('log_errors', 1); ini_set('error_log', './../php_error.log'); 

if (session_status() == PHP_SESSION_NONE) { session_start(); }

require_once './../CONFIG/Conexion.php';
require_once './../MODELOS/citas_m.php';

$db_conn_mysqli = null;
try {
    $conexionObj = new Conexion();
    $db_conn_mysqli = $conexionObj->conecta();
} catch (Exception $e) { die("Error crítico: No se pudo conectar a la base de datos."); }
if ($db_conn_mysqli === null) { die("Error crítico: Conexión no disponible."); }
$citaModelo = new CitaModelo($db_conn_mysqli);

if (!isset($_SESSION['usu_id']) || !in_array($_SESSION['rol_id'], [2, 3])) {
    die("Acceso denegado. Se requiere rol de Administrador o Gestor.");
}

$admin_nombre_display = htmlspecialchars(($_SESSION['usu_nombre'] ?? '') . ' ' . ($_SESSION['usu_apellido'] ?? 'Admin'));

$filtro_texto_val = trim($_GET['filtro_texto'] ?? '');
$filtro_estado_val = trim($_GET['filtro_estado'] ?? '');
$filtro_fecha_desde_val = trim($_GET['filtro_fecha_desde'] ?? '');
$filtro_fecha_hasta_val = trim($_GET['filtro_fecha_hasta'] ?? '');

$todas_las_citas = $citaModelo->obtener_todas_las_citas(
    $filtro_texto_val, $filtro_estado_val, $filtro_fecha_desde_val, $filtro_fecha_hasta_val
);

// Nuevos estados disponibles
$estados_disponibles = ['pendiente', 'aprobada', 'rechazado'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Citas - <?php echo $admin_nombre_display; ?></title>
    <link href="./../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="./../PUBLIC/css/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="./CSS/admin_citas.css">
</head>

<body>
    <header id="navbar-placeholder"></header>

    <main role="main">
        <section id="filtros-busqueda">
            <h2><i class="bi bi-funnel me-2"></i>Filtrar Citas</h2>
            <form id="form-filtros-admin" method="GET" action="admin_citas.php">
                <div class="form-group">
                    <input type="text" id="filtro-texto" name="filtro_texto"
                        placeholder="Buscar por ID, nombre, email, vehículo..."
                        value="<?php echo htmlspecialchars($filtro_texto_val); ?>">
                </div>
                <div class="form-group">
                    <select id="filtro-estado" name="filtro_estado">
                        <option value="">Todos los estados</option>
                        <?php foreach ($estados_disponibles as $estado_opt): ?>
                        <option value="<?php echo $estado_opt; ?>"
                            <?php if ($filtro_estado_val === $estado_opt) echo 'selected'; ?>>
                            <?php echo ucfirst($estado_opt); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-filtrar"><i class="bi bi-search me-2"></i>Aplicar Filtros</button>
                    <a href="admin_citas.php" class="btn-limpiar-filtros"><i
                            class="bi bi-arrow-clockwise me-2"></i>Limpiar</a>
                </div>
            </form>
        </section>

        <section id="lista-todas-citas">
            <h2>
                <i class="bi bi-table me-2"></i>Listado General de Citas
                <span class="badge bg-primary ms-2"><?php echo count($todas_las_citas); ?> resultados</span>
            </h2>
            <div class="tabla-responsive-contenedor">
                <table>
                    <thead>
                        <tr>
                            <th>ID Cita</th>
                            <th>Solicitante</th>
                            <th>Email</th>
                            <th>Fecha Solicitud</th>
                            <th>Fecha Cita</th>
                            <th>Vehículo Solicitado</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-citas-admin-body">
                        <?php foreach ($todas_las_citas as $cita): ?>
                        <tr data-cita-id="<?php echo htmlspecialchars($cita['cit_id']); ?>">
                            <td><strong><?php echo htmlspecialchars($cita['cit_id']); ?></strong></td>
                            <td><?php echo htmlspecialchars($cita['nombre_solicitante']); ?></td>
                            <td><a
                                    href="mailto:<?php echo htmlspecialchars($cita['email_solicitante']); ?>"><?php echo htmlspecialchars($cita['email_solicitante']); ?></a>
                            </td>
                            <td><?php echo htmlspecialchars(date("d/m/Y H:i", strtotime($cita['cit_fecha_solicitud']))); ?>
                            </td>
                            <td>
                                <?php if($cita['cit_fecha_disponibilidad']): ?>
                                <strong><?php echo htmlspecialchars(date("d/m/Y", strtotime($cita['cit_fecha_disponibilidad']))); ?></strong>
                                <br><small
                                    class="text-muted"><?php echo htmlspecialchars($cita['cit_hora_disponibilidad']); ?></small>
                                <?php else: ?>
                                <span class="text-muted">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($cita['cit_detalles_vehiculo_solicitado']); ?></td>
                            <td><span
                                    class="estado-tag estado-<?php echo strtolower(htmlspecialchars($cita['cit_estado'])); ?>"><?php echo ucfirst(htmlspecialchars($cita['cit_estado'])); ?></span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn-admin-accion btn-admin-ver-detalle"
                                        data-id="<?php echo htmlspecialchars($cita['cit_id']); ?>"
                                        title="Ver detalles"><i class="bi bi-eye"></i></button>
                                    <?php if ($cita['cit_estado'] === 'pendiente'): ?>
                                    <button class="btn-admin-accion btn-admin-aprobar"
                                        data-id="<?php echo htmlspecialchars($cita['cit_id']); ?>"
                                        title="Aprobar Cita"><i class="bi bi-check-lg"></i></button>
                                    <button class="btn-admin-accion btn-admin-rechazar"
                                        data-id="<?php echo htmlspecialchars($cita['cit_id']); ?>"
                                        title="Rechazar Cita"><i class="bi bi-x-lg"></i></button>
                                    <?php elseif ($cita['cit_estado'] === 'aprobada'): ?>
                                    <button class="btn-admin-accion btn-registrar-venta"
                                        data-id="<?php echo htmlspecialchars($cita['cit_id']); ?>"
                                        title="Registrar Venta"><i class="bi bi-cash-stack"></i></button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <div id="modal-detalle-cita-admin" class="modal" style="display:none;">
            <div class="modal-contenido">
                <button class="modal-cerrar" id="modal-cerrar-detalle-admin">×</button>
                <h3><i class="bi bi-file-text me-2"></i>Detalle de la Cita #<span
                        id="detalle-cita-admin-id-modal"></span></h3>
                <div id="detalle-cita-admin-contenido-modal">
                    <p class="loading-message">Cargando...</p>
                </div>
                <div class="modal-admin-acciones"></div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . './partials/footer.php'; ?>

    <script src="./../PUBLIC/jquery-3.7.1.min.js"></script>
    <script src="./../Bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="./JS/global.js"></script>
    <script src="./JS/admin_citas.js"></script>
</body>

</html>