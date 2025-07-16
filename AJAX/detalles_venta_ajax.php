<?php
// AJAX/detalles_venta_ajax.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../php_error.log'); 

header('Content-Type: application/json');

if (session_status() == PHP_SESSION_NONE) { session_start(); }

require_once './../CONFIG/Conexion.php';
require_once './../MODELOS/detalles_venta_m.php';

$db_conn_mysqli = null;
try {
    $conexionObj = new Conexion();
    $db_conn_mysqli = $conexionObj->conecta();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión.']); exit;
}

if ($db_conn_mysqli === null) {
    echo json_encode(['success' => false, 'message' => 'Conexión no disponible.']); exit;
}

$detalleVentaModelo = new DetalleVentaModelo($db_conn_mysqli);

$action = $_POST['action'] ?? $_GET['action'] ?? null;

if (!$action) {
    echo json_encode(['success' => false, 'message' => 'Acción no especificada.']); exit;
}

function verificar_sesion_y_rol($roles_permitidos = []) {
    if (!isset($_SESSION['usu_id']) || !isset($_SESSION['rol_id'])) {
        echo json_encode(['success' => false, 'message' => 'Acceso denegado: No ha iniciado sesión.', 'error_code' => 'AUTH_REQUIRED']); exit;
    }
    if (!empty($roles_permitidos) && !in_array($_SESSION['rol_id'], $roles_permitidos)) {
        echo json_encode(['success' => false, 'message' => 'Acceso denegado: Permisos insuficientes.', 'error_code' => 'FORBIDDEN']); exit;
    }
    return $_SESSION['usu_id']; 
}

switch ($action) {
    case 'obtener_detalle_venta':
        $usu_id_actual = verificar_sesion_y_rol([1, 2, 3]); 
        $vnt_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$vnt_id) { echo json_encode(['success' => false, 'message' => 'ID de venta no válido.']); exit; }
        
        $detalle_venta = $detalleVentaModelo->obtener_detalle_venta($vnt_id);
        
        if ($detalle_venta) {
            // Verificar que el usuario actual es el comprador o el vendedor
            if ($usu_id_actual == $detalle_venta['comprador_id'] || $usu_id_actual == $detalle_venta['vendedor_id']) {
                echo json_encode(['success' => true, 'data' => $detalle_venta]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No tiene permiso para ver esta factura.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontró el detalle de la venta.']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Acción desconocida: ' . htmlspecialchars($action)]);
        break;
}
?>
