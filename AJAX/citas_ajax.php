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
        $user_id = verificar_sesion_y_rol([1, 2, 3]); 
        $cit_id = filter_input(INPUT_GET, 'id_cita', FILTER_VALIDATE_INT);
        if (!$cit_id) { echo json_encode(['success' => false, 'message' => 'ID de cita no válido (admin).']); exit; }
        
        $detalle = $citaModelo->obtener_detalle_cita($cit_id);
        if ($detalle) {
            // Verificar permisos: admin ve todo, otros solo sus vehículos
            if ($_SESSION['rol_id'] == 3 || $detalle['usu_id_gestor'] == $user_id) {
                echo json_encode(['success' => true, 'data' => $detalle]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No tiene permisos para ver esta cita.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Cita no encontrada o error al obtener detalles.']);
        }
        break;

    case 'cambiar_estado_cita':
        $user_id = verificar_sesion_y_rol([1, 2, 3]);
        $cit_id = filter_input(INPUT_POST, 'id_cita', FILTER_VALIDATE_INT);
        $nuevo_estado_raw = filter_input(INPUT_POST, 'nuevo_estado', FILTER_SANITIZE_STRING); 
        
        $estados_permitidos = ['pendiente', 'aprobada', 'rechazado'];
        if (!$cit_id || !$nuevo_estado_raw || !in_array($nuevo_estado_raw, $estados_permitidos)) {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos para cambiar estado.']); exit;
        }

        // Verificar permisos: admin puede cambiar cualquier cita, otros solo sus vehículos
        $detalle = $citaModelo->obtener_detalle_cita($cit_id);
        if (!$detalle || ($_SESSION['rol_id'] != 3 && $detalle['usu_id_gestor'] != $user_id)) {
            echo json_encode(['success' => false, 'message' => 'No tiene permisos para cambiar el estado de esta cita.']); exit;
        }

        $actualizado = $citaModelo->actualizar_estado_cita($cit_id, $nuevo_estado_raw, $user_id);
        if ($actualizado) {
            // Si la cita fue aprobada, marcar el vehículo como reservado automáticamente
            if ($nuevo_estado_raw === 'aprobada') {
                require_once './../MODELOS/vehiculos_m.php';
                $vehiculoModelo = new Vehiculo();
                $vehiculo_id = $detalle['veh_id'];
                $resultado_reserva = $vehiculoModelo->actualizarEstadoVehiculo($vehiculo_id, 'reservado', $user_id);
                
                if ($resultado_reserva['resultado'] == 1) {
                    echo json_encode(['success' => true, 'message' => "Cita #{$cit_id} aprobada exitosamente. El vehículo ha sido marcado como reservado automáticamente.", 'nuevo_estado' => $nuevo_estado_raw]);
                } else {
                    error_log("Cita aprobada pero error al reservar vehículo #{$vehiculo_id}: " . $resultado_reserva['mensaje']);
                    echo json_encode(['success' => true, 'message' => "Cita #{$cit_id} aprobada exitosamente, pero hubo un problema al reservar el vehículo. Verifique manualmente el estado del vehículo.", 'nuevo_estado' => $nuevo_estado_raw]);
                }
            } else {
                echo json_encode(['success' => true, 'message' => "Estado de la cita #{$cit_id} actualizado a '{$nuevo_estado_raw}'.", 'nuevo_estado' => $nuevo_estado_raw]);
            }
        } else {
            error_log("Error al actualizar estado de cita #{$cit_id} a '{$nuevo_estado_raw}' por usuario {$user_id}");
            echo json_encode(['success' => false, 'message' => "Error al actualizar el estado de la cita #{$cit_id}. Verifique los logs para más detalles."]);
        }
        break;

    case 'guardar_notas_admin':
        $user_id = verificar_sesion_y_rol([1, 2, 3]);
        $cit_id = filter_input(INPUT_POST, 'id_cita', FILTER_VALIDATE_INT);
        $notas = isset($_POST['notas_internas']) ? $_POST['notas_internas'] : '';
        if (!$cit_id) { echo json_encode(['success' => false, 'message' => 'ID de cita no válido para guardar notas.']); exit; }
        
        // Verificar permisos: admin puede editar cualquier cita, otros solo sus vehículos
        $detalle = $citaModelo->obtener_detalle_cita($cit_id);
        if (!$detalle || ($_SESSION['rol_id'] != 3 && $detalle['usu_id_gestor'] != $user_id)) {
            echo json_encode(['success' => false, 'message' => 'No tiene permisos para editar las notas de esta cita.']); exit;
        }
        
        $guardado = $citaModelo->guardar_notas_admin_cita($cit_id, $notas, $user_id);
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
        $user_id = verificar_sesion_y_rol([1, 2, 3]);
        $cit_id = filter_input(INPUT_POST, 'id_cita', FILTER_VALIDATE_INT);
        
        if (!$cit_id) {
            echo json_encode(['success' => false, 'message' => 'ID de cita inválido para registrar la venta.']); exit;
        }

        $cita_detalle = $citaModelo->obtener_detalle_cita($cit_id);
        if (!$cita_detalle) {
            echo json_encode(['success' => false, 'message' => 'No se encontró la cita.']); exit;
        }

        // Verificar permisos: admin puede registrar cualquier venta, otros solo sus vehículos
        if ($_SESSION['rol_id'] != 3 && $cita_detalle['usu_id_gestor'] != $user_id) {
            echo json_encode(['success' => false, 'message' => 'No tiene permisos para registrar la venta de este vehículo.']); exit;
        }

        // Verificar que el vehículo no esté ya vendido
        if ($cita_detalle['veh_estado'] === 'vendido') {
            echo json_encode(['success' => false, 'message' => 'Este vehículo ya ha sido vendido.']); exit;
        }

        // Obtener el precio del vehículo automáticamente
        $vehiculo_id = $cita_detalle['veh_id'];
        $sql_precio = "SELECT veh_precio FROM vehiculos WHERE veh_id = ?";
        $stmt_precio = $db_conn_mysqli->prepare($sql_precio);
        $stmt_precio->bind_param("i", $vehiculo_id);
        $stmt_precio->execute();
        $result_precio = $stmt_precio->get_result();
        $vehiculo_data = $result_precio->fetch_assoc();
        $stmt_precio->close();

        if (!$vehiculo_data) {
            echo json_encode(['success' => false, 'message' => 'No se pudo obtener el precio del vehículo.']); exit;
        }

        $precio_final = $vehiculo_data['veh_precio'];
        $comprador_id = $cita_detalle['usu_id_solicitante'];
        $vendedor_id = $cita_detalle['usu_id_gestor'];

        // Llamada al procedimiento almacenado de registro de venta
        $sql = "CALL sp_registrar_venta(?, ?, ?, ?, ?, ?)";
        $stmt = $db_conn_mysqli->prepare($sql);
        $notas = "Venta registrada por el usuario ID: " . $user_id . " con precio automático del vehículo";
        $stmt->bind_param("idiiis", $cit_id, $precio_final, $comprador_id, $vendedor_id, $vehiculo_id, $notas);
        $stmt->execute();
        
        // Obtener el ID de la venta insertada
        $venta_id = $db_conn_mysqli->insert_id;
        
        // Actualizar el estado del vehículo a 'vendido'
        $sql_update = "UPDATE VEHICULOS SET veh_estado = 'vendido' WHERE veh_id = ?";
        $stmt_update = $db_conn_mysqli->prepare($sql_update);
        $stmt_update->bind_param("i", $vehiculo_id);
        $stmt_update->execute();
        
        // Generar detalle de venta (reemplaza el trigger MySQL)
        require_once './../MODELOS/detalles_venta_m.php';
        $detalleVentaModelo = new DetalleVentaModelo($db_conn_mysqli);
        $detalle_generado = $detalleVentaModelo->generar_detalle_venta($venta_id);
        
        if ($detalle_generado) {
            echo json_encode(['success' => true, 'message' => 'Venta registrada exitosamente por $' . number_format($precio_final, 2) . '. El vehículo ha sido marcado como vendido y se ha generado la factura.']);
        } else {
            echo json_encode(['success' => true, 'message' => 'Venta registrada exitosamente por $' . number_format($precio_final, 2) . ', pero hubo un problema al generar la factura. Contacte al administrador.']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Acción desconocida: ' . htmlspecialchars($action)]);
        break;
}
?>