<?php
// AJAX/cotizaciones_ajax.php

ini_set('display_errors', 0); 
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../php_error.log'); 

// =========== INICIO DE LA SECCIÓN ADAPTADA ===========

header('Content-Type: application/json');

$config_global_path = __DIR__ . '/../CONFIG/global.php';
$conexion_class_path = __DIR__ . '/../CONFIG/Conexion.php';

if (file_exists($config_global_path)) {
    require_once $config_global_path;
} else {
    echo json_encode(['success' => false, 'message' => 'Error crítico: global.php no encontrado.']);
    exit;
}
if (file_exists($conexion_class_path)) {
    require_once $conexion_class_path;
} else {
    echo json_encode(['success' => false, 'message' => 'Error crítico: Conexion.php no encontrado.']);
    exit;
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../MODELOS/cotizaciones_m.php';

$db_conn_mysqli = null;
try {
    $conexionObj = new Conexion();
    $db_conn_mysqli = $conexionObj->conecta();
} catch (Exception $e) {
    error_log("Error al instanciar Conexion en AJAX/cotizaciones_ajax.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error crítico: No se pudo establecer la conexión a la BD (AJAX).']);
    exit;
}

if ($db_conn_mysqli === null) {
    echo json_encode(['success' => false, 'message' => 'Error crítico: Conexión mysqli no disponible (AJAX).']);
    exit;
}

// Pasamos el objeto mysqli al modelo
$cotizacionModelo = new CotizacionModelo($db_conn_mysqli);

// =========== FIN DE LA SECCIÓN ADAPTADA ===========


// --- Determinar la Acción Solicitada ---
$action = $_POST['action'] ?? $_GET['action'] ?? null; // Priorizar POST para acciones que modifican datos

if (!$action) {
    echo json_encode(['success' => false, 'message' => 'Acción no especificada.']);
    exit;
}

// --- Manejo de Sesiones y Roles (Funciones Auxiliares) ---
function verificar_sesion_y_rol($roles_permitidos = []) {
    // La sesión ya debería estar iniciada por el bloque de código superior.
    // if (session_status() == PHP_SESSION_NONE) { session_start(); }

    if (!isset($_SESSION['usu_id']) || !isset($_SESSION['rol_id'])) {
        echo json_encode(['success' => false, 'message' => 'Acceso denegado: No ha iniciado sesión.', 'error_code' => 'AUTH_REQUIRED']);
        exit;
    }
    if (!empty($roles_permitidos) && !in_array($_SESSION['rol_id'], $roles_permitidos)) {
        echo json_encode(['success' => false, 'message' => 'Acceso denegado: No tiene los permisos necesarios para esta acción (Rol: '.$_SESSION['rol_id'].').', 'error_code' => 'FORBIDDEN']);
        exit;
    }
    return $_SESSION['usu_id']; 
}

// --- Switch para Manejar las Acciones ---
switch ($action) {
    case 'obtener_detalle_cotizacion_usuario':
        $usu_id_actual = verificar_sesion_y_rol([1, 2]); 
        $cot_id = filter_input(INPUT_GET, 'id_cotizacion', FILTER_VALIDATE_INT);
        
        if (!$cot_id) {
            echo json_encode(['success' => false, 'message' => 'ID de cotización no válido.']);
            exit;
        }
        
        $detalle = $cotizacionModelo->obtener_detalle_cotizacion($cot_id);
        
        if ($detalle) {
            // Importante: Verificar que el usuario solicitante sea el dueño de la cotización
            if (isset($detalle['usu_id_solicitante']) && $detalle['usu_id_solicitante'] == $usu_id_actual) {
                echo json_encode(['success' => true, 'data' => $detalle]);
            } else {
                // No enviar mensaje de error detallado al cliente por seguridad, loguear internamente si es necesario.
                error_log("Intento de acceso no autorizado a cotización {$cot_id} por usuario {$usu_id_actual}. Dueño: " . ($detalle['usu_id_solicitante'] ?? 'desconocido'));
                echo json_encode(['success' => false, 'message' => 'No tiene permiso para ver esta cotización.', 'error_code' => 'FORBIDDEN_COT_DETAIL']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Cotización no encontrada o error al obtener detalles.']);
        }
        break;

    case 'obtener_detalle_cotizacion_admin':
        $admin_id = verificar_sesion_y_rol([3]); 
        $cot_id = filter_input(INPUT_GET, 'id_cotizacion', FILTER_VALIDATE_INT);

        if (!$cot_id) {
            echo json_encode(['success' => false, 'message' => 'ID de cotización no válido (admin).']);
            exit;
        }
        $detalle = $cotizacionModelo->obtener_detalle_cotizacion($cot_id);
        if ($detalle) {
            echo json_encode(['success' => true, 'data' => $detalle]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Cotización no encontrada o error al obtener detalles (admin).']);
        }
        break;

    case 'cambiar_estado_cotizacion':
        $admin_id = verificar_sesion_y_rol([3]);
        $cot_id = filter_input(INPUT_POST, 'id_cotizacion', FILTER_VALIDATE_INT);
        // Sanitizar el string, aunque el SP usa ENUM que es más seguro.
        $nuevo_estado_raw = filter_input(INPUT_POST, 'nuevo_estado', FILTER_SANITIZE_STRING); 
        
        if (!$cot_id) {
            echo json_encode(['success' => false, 'message' => 'ID de cotización no proporcionado o no válido.']);
            exit;
        }
        
        // Validar contra los estados permitidos por el ENUM de la BD
        // ENUM('pendiente','aprobada_admin','contactado','cerrado','rechazado')
        $estados_permitidos = ['pendiente', 'aprobada_admin', 'contactado', 'cerrado', 'rechazado'];
        if (!$nuevo_estado_raw || !in_array($nuevo_estado_raw, $estados_permitidos)) {
            echo json_encode(['success' => false, 'message' => 'Nuevo estado no válido o no proporcionado. Estado recibido: ' . htmlspecialchars($nuevo_estado_raw) ]);
            exit;
        }

        $actualizado = $cotizacionModelo->actualizar_estado_cotizacion($cot_id, $nuevo_estado_raw, $admin_id);
        if ($actualizado) {
            echo json_encode(['success' => true, 'message' => "Estado de la cotización #{$cot_id} actualizado a '{$nuevo_estado_raw}'.", 'nuevo_estado' => $nuevo_estado_raw]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado de la cotización. Verifique que la cotización exista y que el estado sea diferente al actual.']);
        }
        break;

    case 'guardar_notas_admin':
        $admin_id = verificar_sesion_y_rol([3]);
        $cot_id = filter_input(INPUT_POST, 'id_cotizacion', FILTER_VALIDATE_INT);
        $notas = isset($_POST['notas_internas']) ? $_POST['notas_internas'] : ''; // Permitir string vacío
        // Sanitizar si es necesario, aunque TEXT en BD y PDO suelen manejar bien muchos caracteres.
        // $notas_saneadas = filter_var($notas, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); // Ejemplo de sanitización

        if (!$cot_id) {
            echo json_encode(['success' => false, 'message' => 'ID de cotización no válido para guardar notas.']);
            exit;
        }
        
        $guardado = $cotizacionModelo->guardar_notas_admin_cotizacion($cot_id, $notas, $admin_id);
        if ($guardado) { // El modelo ahora devuelve true/false basado en el éxito de la query
            echo json_encode(['success' => true, 'message' => "Notas administrativas para la cotización #{$cot_id} guardadas."]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al guardar las notas administrativas. Verifique que la cotización exista.']);
        }
        break;
    
    default:
        echo json_encode(['success' => false, 'message' => 'Acción desconocida o no implementada: ' . htmlspecialchars($action)]);
        break;
}

?>