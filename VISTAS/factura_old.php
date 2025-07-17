<?php
// VISTAS/factura.php
ini_set('display_errors', 0); 
error_reporting(E_ALL);
ini_set('log_errors', 1); 
ini_set('error_log', __DIR__ . './../php_error.log'); 

if (session_status() == PHP_SESSION_NONE) { 
    session_start(); 
}

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usu_id'])) {
    header("Location: login.php");
    exit;
}

// Obtener el ID de la venta
$vnt_id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : null;

if (!$vnt_id) {
    die("ID de venta no válido.");
}

require_once __DIR__ . './../CONFIG/Conexion.php';
require_once __DIR__ . './../MODELOS/facturas_m.php';

$db_conn_mysqli = null;
try {
    $conexionObj = new Conexion();
    $db_conn_mysqli = $conexionObj->conecta();
} catch (Exception $e) { die("Error crítico: No se pudo conectar a la base de datos."); }
if ($db_conn_mysqli === null) { die("Error crítico: Conexión no disponible."); }
$detalleVentaModelo = new DetalleVentaModelo($db_conn_mysqli);

if (!isset($_SESSION['usu_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("ID de venta no especificado.");
}

$venta_id = $_GET['id'];
$detalle_venta = $detalleVentaModelo->obtener_detalle_venta($venta_id);

if (!$detalle_venta) {
    die("No se encontró el detalle de la venta.");
}

// Verificar que el usuario actual es el comprador o el vendedor
if ($_SESSION['usu_id'] != $detalle_venta['comprador_id'] && $_SESSION['usu_id'] != $detalle_venta['vendedor_id']) {
    die("No tiene permiso para ver esta factura.");
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura <?php echo htmlspecialchars($detalle_venta['dv_codigo_factura']); ?></title>
    <link href="./../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="./../PUBLIC/css/styles.css" rel="stylesheet">
    <style>
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, .15);
            font-size: 16px;
            line-height: 24px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                <img src="./../PUBLIC/Img/Auto_Mercado_Total_LOGO4_SIN_FONDO.png" style="width:100%; max-width:300px;">
                            </td>
                            <td>
                                Factura #: <?php echo htmlspecialchars($detalle_venta['dv_codigo_factura']); ?><br>
                                Creada: <?php echo htmlspecialchars(date("d/m/Y", strtotime($detalle_venta['vnt_fecha_venta']))); ?><br>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                Vendedor: <?php echo htmlspecialchars($detalle_venta['vendedor_nombre']); ?><br>
                            </td>
                            <td>
                                Comprador: <?php echo htmlspecialchars($detalle_venta['comprador_nombre']); ?><br>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="heading">
                <td>
                    Descripción
                </td>
                <td>
                    Precio
                </td>
            </tr>
            <tr class="item">
                <td>
                    <?php echo htmlspecialchars($detalle_venta['vehiculo_nombre']); ?>
                </td>
                <td>
                    <?php echo htmlspecialchars($detalle_venta['vnt_precio_final']); ?>
                </td>
            </tr>
            <tr class="total">
                <td></td>
                <td>
                   Total: <?php echo htmlspecialchars($detalle_venta['vnt_precio_final']); ?>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
