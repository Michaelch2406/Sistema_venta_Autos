<?php
session_start();
require_once __DIR__ . "/../MODELOS/favoritos_m.php"; 

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Acción no válida.'];

if (!isset($_SESSION['usu_id'])) {
    $response['message'] = 'Debes iniciar sesión para gestionar favoritos.';
    echo json_encode($response);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'], $_POST['veh_id'])) {
    $accion = $_POST['accion'];
    $veh_id = filter_var($_POST['veh_id'], FILTER_VALIDATE_INT);
    $usu_id = $_SESSION['usu_id'];

    if (!$veh_id) {
        $response['message'] = 'ID de vehículo inválido.';
        echo json_encode($response);
        exit();
    }

    try {
        $favoritos_model = new Favoritos_M();

        if ($accion === 'agregarFavorito') {
            $resultado = $favoritos_model->agregarFavorito($usu_id, $veh_id);
            $response = $resultado; // El modelo ya devuelve 'status' y 'message'
        } elseif ($accion === 'quitarFavorito') {
            $resultado = $favoritos_model->quitarFavorito($usu_id, $veh_id);
            $response = $resultado;
        } else {
            $response['message'] = 'Acción de favoritos desconocida.';
        }
    } catch (Exception $e) {
        error_log("Excepción en favoritos_ajax.php (POST): " . $e->getMessage());
        $response['message'] = 'Error procesando la solicitud de favoritos: ' . $e->getMessage();
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'verificarEstado' && isset($_GET['veh_id'])) {
    $veh_id = filter_var($_GET['veh_id'], FILTER_VALIDATE_INT);
    $usu_id = $_SESSION['usu_id'];
    if ($veh_id && $usu_id) {
        try {
            $favoritos_model = new Favoritos_M();
            $esFavorito = $favoritos_model->esFavorito($usu_id, $veh_id);
            $response = ['status' => 'success', 'esFavorito' => (bool)$esFavorito];
        } catch (Exception $e) {
            error_log("Excepción en favoritos_ajax.php (GET verificarEstado): " . $e->getMessage());
            $response['message'] = 'Error verificando estado de favorito: ' . $e->getMessage();
        }
    } else {
        $response['message'] = 'Faltan datos para verificar estado de favorito.';
    }
} else {
    $response['message'] = 'Petición no válida para favoritos.';
}

echo json_encode($response);
?>