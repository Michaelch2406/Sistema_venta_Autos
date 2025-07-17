<?php
// AJAX/ventas_ajax.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ini_set('log_errors', 1);
ini_set('error_log', './../php_error.log'); 

header('Content-Type: application/json');

if (session_status() == PHP_SESSION_NONE) { session_start(); }

require_once './../CONFIG/Conexion.php';
require_once './../MODELOS/ventas_m.php';

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

$ventaModelo = new VentaModelo($db_conn_mysqli);

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
    case 'obtener_ventas_usuario':
        $usu_id_actual = verificar_sesion_y_rol([1, 2, 3]); 
        $ventas = $ventaModelo->obtener_ventas_por_usuario($usu_id_actual);
        echo json_encode(['success' => true, 'data' => $ventas]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Acción desconocida: ' . htmlspecialchars($action)]);
        break;
}
?>
