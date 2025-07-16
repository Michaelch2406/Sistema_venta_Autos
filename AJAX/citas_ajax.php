<?php
// AJAX/citas_ajax.php
ini_set('display_errors', 1); // Cambiar a 0 en producción
ini_set('display_startup_errors', 1); // Cambiar a 0 en producción
error_reporting(E_ALL);

// Configuración del archivo de log. El path es relativo a este script (citas_ajax.php)
ini_set('log_errors', 1);
// Suponiendo que la estructura es: /RaizDelProyecto/AJAX/citas_ajax.php
// La siguiente línea guardará el log en /RaizDelProyecto/php_error.log
ini_set('error_log', __DIR__ . '/../php_error.log'); 

header('Content-Type: application/json');

if (session_status() == PHP_SESSION_NONE) { session_start(); }

require_once './../CONFIG/Conexion.php';
require_once './../MODELOS/citas_m.php';

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

$citaModelo = new CitaModelo($db_conn_mysqli);

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
    case 'obtener_detalle_cita_usuario':
        $usu_id_actual = verificar_sesion_y_rol([1, 2]); 
        $cit_id = filter_input(INPUT_GET, 'id_cita', FILTER_VALIDATE_INT);
        if (!$cit_id) { echo json_encode(['success' => false, 'message' => 'ID de cita no válido.']); exit; }
        
        $detalle = $citaModelo->obtener_detalle_cita($cit_id);
        
        if ($detalle && $detalle['usu_id_solicitante'] == $usu_id_actual) {
            echo json_encode(['success' => true, 'data' => $detalle]);
        } else {
            error_log("Intento de acceso no autorizado a cita {$cit_id} por usuario {$usu_id_actual}.");
            echo json_encode(['success' => false, 'message' => 'No tiene permiso para ver esta cita.', 'error_code' => 'FORBIDDEN_CITA_DETAIL']);
        }
        break;

    case 'obtener_detalle_cita_admin':
        $admin_id = verificar_sesion_y_rol([3, 2]); 
        $cit_id = filter_input(INPUT_GET, 'id_cita', FILTER_VALIDATE_INT);
        if (!$cit_id) { echo json_encode(['success' => false, 'message' => 'ID de cita no válido (admin).']); exit; }
        
        $detalle = $citaModelo->obtener_detalle_cita($cit_id);
        if ($detalle) {
            echo json_encode(['success' => true, 'data' => $detalle]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Cita no encontrada o error al obtener detalles.']);
        }
        break;

    case 'cambiar_estado_cita':
        $admin_id = verificar_sesion_y_rol([3, 2]);
        $cit_id = filter_input(INPUT_POST, 'id_cita', FILTER_VALIDATE_INT);
        $nuevo_estado_raw = filter_input(INPUT_POST, 'nuevo_estado', FILTER_SANITIZE_STRING); 
        
        $estados_permitidos = ['pendiente', 'aprobada', 'rechazado'];
        if (!$cit_id || !$nuevo_estado_raw || !in_array($nuevo_estado_raw, $estados_permitidos)) {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos para cambiar estado.']); exit;
        }

        $actualizado = $citaModelo->actualizar_estado_cita($cit_id, $nuevo_estado_raw, $admin_id);
        if ($actualizado) {
            echo json_encode(['success' => true, 'message' => "Estado de la cita #{$cit_id} actualizado a '{$nuevo_estado_raw}'.", 'nuevo_estado' => $nuevo_estado_raw]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado de la cita.']);
        }
        break;

    case 'guardar_notas_admin':
        $admin_id = verificar_sesion_y_rol([3, 2]);
        $cit_id = filter_input(INPUT_POST, 'id_cita', FILTER_VALIDATE_INT);
        $notas = isset($_POST['notas_internas']) ? $_POST['notas_internas'] : '';
        if (!$cit_id) { echo json_encode(['success' => false, 'message' => 'ID de cita no válido para guardar notas.']); exit; }
        
        $guardado = $citaModelo->guardar_notas_admin_cita($cit_id, $notas, $admin_id);
        if ($guardado) {
            echo json_encode(['success' => true, 'message' => "Notas administrativas para la cita #{$cit_id} guardadas."]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al guardar las notas administrativas.']);
        }
        break;

    case 'obtener_disponibilidad':
    // No requiere sesión para ver la disponibilidad
    $veh_id = filter_input(INPUT_GET, 'veh_id', FILTER_VALIDATE_INT);
    $fecha = filter_input(INPUT_GET, 'fecha', FILTER_SANITIZE_STRING); // YYYY-MM-DD
    if (!$veh_id || !$fecha) {
        echo json_encode(['success' => false, 'message' => 'Faltan datos para consultar disponibilidad.']);
        exit;
    }

    // Simulación extra de días no disponibles
    $dias_no_laborables = []; // Ej: ['2025-12-25', '2026-01-01']
    if (in_array($fecha, $dias_no_laborables)) {
        // CORRECCIÓN: cerrar array y función sin paréntesis extra
        echo json_encode(['success' => true, 'horas_ocupadas' => ['todas']]);
        exit;
    }
    
    $horas_ocupadas = $citaModelo->obtener_horas_ocupadas($veh_id, $fecha);
    echo json_encode(['success' => true, 'horas_ocupadas' => $horas_ocupadas]);
    break;

case 'insertar_cita':
    $usu_id_actual = verificar_sesion_y_rol([1, 2]); // Solo usuarios logueados pueden pedir cita
    
    // Recoger y validar datos
    $veh_id     = filter_input(INPUT_POST, 'veh_id', FILTER_VALIDATE_INT);
    $mensaje    = filter_input(INPUT_POST, 'mensaje', FILTER_SANITIZE_STRING);
    $fecha_disp = filter_input(INPUT_POST, 'fecha_disponibilidad', FILTER_SANITIZE_STRING);
    $hora_disp  = filter_input(INPUT_POST, 'hora_disponibilidad', FILTER_SANITIZE_STRING);
    
    if (!$veh_id || !$fecha_disp || !$hora_disp) {
        echo json_encode(['success' => false, 'message' => 'Por favor, seleccione una fecha y hora para su cita.']);
        exit;
    }
    
    // Llamada al procedimiento almacenado de inserción
    $sql = "CALL sp_insertar_cita(?, ?, ?, ?, ?, @p_resultado, @p_mensaje_respuesta)";
    $stmt = $db_conn_mysqli->prepare($sql);
    $stmt->bind_param("issss", $usu_id_actual, $veh_id, $mensaje, $fecha_disp, $hora_disp);
    $stmt->execute();
    
    // Obtener resultados de los parámetros OUT
    $select = $db_conn_mysqli->query("SELECT @p_resultado AS resultado, @p_mensaje_respuesta AS mensaje");
    $result = $select->fetch_assoc();
    
    if ($result['resultado'] == 1) {
        echo json_encode(['success' => true, 'message' => $result['mensaje']]);
    } else {
        echo json_encode(['success' => false, 'message' => $result['mensaje']]);
    }
    break;
    
    case 'registrar_venta':
        $admin_id = verificar_sesion_y_rol([3, 2]);
        $cit_id = filter_input(INPUT_POST, 'id_cita', FILTER_VALIDATE_INT);
        $precio_final = filter_input(INPUT_POST, 'precio_final', FILTER_VALIDATE_FLOAT);

        if (!$cit_id || !$precio_final) {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos para registrar la venta.']); exit;
        }

        $cita_detalle = $citaModelo->obtener_detalle_cita($cit_id);
        if (!$cita_detalle) {
            echo json_encode(['success' => false, 'message' => 'No se encontró la cita.']); exit;
        }

        $comprador_id = $cita_detalle['usu_id_solicitante'];
        $vehiculo_id = $cita_detalle['veh_id'];

        // Obtener el ID del vendedor (propietario del vehículo)
        // Esta parte puede necesitar un ajuste dependiendo de cómo se almacena el propietario del vehículo.
        // Por ahora, asumiré que el administrador que registra la venta es el vendedor.
        $vendedor_id = $admin_id;

        // Llamada al procedimiento almacenado de registro de venta
        $sql = "CALL sp_registrar_venta(?, ?, ?, ?, ?, ?)";
        $stmt = $db_conn_mysqli->prepare($sql);
        $notas = "Venta registrada por el administrador ID: " . $admin_id;
        $stmt->bind_param("idiiis", $cit_id, $precio_final, $comprador_id, $vendedor_id, $vehiculo_id, $notas);
        $stmt->execute();
        
        echo json_encode(['success' => true, 'message' => 'Venta registrada exitosamente.']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Acción desconocida: ' . htmlspecialchars($action)]);
        break;
}
?>