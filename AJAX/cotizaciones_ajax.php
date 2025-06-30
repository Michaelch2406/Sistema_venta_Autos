<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../php_error.log');
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../MODELOS/cotizaciones_m.php';

$response = ['status' => 'error', 'message' => 'Petición no válida.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'enviarCotizacion') {
    if (!isset($_SESSION['usu_id'])) {
        $response['message'] = 'Debes iniciar sesión para enviar una solicitud.';
        echo json_encode($response);
        exit();
    }

    if (empty($_POST['veh_id']) || !filter_var($_POST['veh_id'], FILTER_VALIDATE_INT)) {
        $response['message'] = 'ID de vehículo no válido.';
        echo json_encode($response);
        exit();
    }

    $datos_cotizacion = [
        'usu_id_solicitante' => $_SESSION['usu_id'],
        'veh_id' => (int)$_POST['veh_id'],
        'mensaje' => isset($_POST['mensaje']) && trim($_POST['mensaje']) !== '' ? trim($_POST['mensaje']) : 'El usuario no dejó un mensaje adicional.'
    ];

    try {
        $cotizacion_model = new Cotizaciones_M();
        $resultado_sp = $cotizacion_model->insertarCotizacion($datos_cotizacion);

        if (isset($resultado_sp['resultado']) && $resultado_sp['resultado'] == 1) {
            $response['status'] = 'success';
            $response['message'] = $resultado_sp['mensaje'];
        } else {
            $response['message'] = $resultado_sp['mensaje'] ?? 'No se pudo procesar tu solicitud.';
        }

    } catch (Exception $e) {
        error_log("Excepción en cotizaciones_ajax.php: " . $e->getMessage());
        $response['message'] = 'Ocurrió un error crítico en el servidor.';
    }
}

echo json_encode($response);
?>