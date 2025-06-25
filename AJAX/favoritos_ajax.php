<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

header('Content-Type: application/json'); // Asegurar que la respuesta sea JSON

require_once __DIR__ . '/../MODELOS/favoritos_m.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usu_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Usuario no autenticado. Por favor, inicie sesión.']);
    exit();
}

$usu_id = $_SESSION['usu_id'];
$response = ['status' => 'error', 'message' => 'Acción no válida o faltan parámetros.']; // Respuesta por defecto

// Priorizar POST para todas las acciones
if (isset($_POST['accion']) && isset($_POST['veh_id'])) {
    $accion = $_POST['accion']; // Cambiado de 'agregarFavorito' a 'agregar', etc.
    $veh_id = filter_var($_POST['veh_id'], FILTER_VALIDATE_INT);

    if ($veh_id === false) {
        $response = ['status' => 'error', 'message' => 'ID de vehículo no válido.'];
    } else {
        try {
            $favoritos_model = new Favoritos_M();

            switch ($accion) {
                case 'agregar':
                    $resultado_sp = $favoritos_model->agregarFavorito($usu_id, $veh_id);
                    $response = $resultado_sp;
                    // Asegurar que 'esFavorito' esté presente en la respuesta
                    $response['esFavorito'] = $favoritos_model->verificarFavorito($usu_id, $veh_id);
                    break;

                case 'quitar':
                    $resultado_sp = $favoritos_model->quitarFavorito($usu_id, $veh_id);
                    $response = $resultado_sp;
                    $response['esFavorito'] = $favoritos_model->verificarFavorito($usu_id, $veh_id);
                    break;

                case 'verificar': // Acción 'verificar' también por POST para consistencia
                    $es_favorito = $favoritos_model->verificarFavorito($usu_id, $veh_id);
                    $response = [
                        'status' => 'success',
                        'esFavorito' => $es_favorito,
                        'message' => $es_favorito ? 'El vehículo es favorito.' : 'El vehículo no es favorito.'
                    ];
                    break;

                default:
                    $response = ['status' => 'error', 'message' => 'Acción desconocida: ' . htmlspecialchars($accion)];
                    break;
            }
        } catch (Exception $e) {
            error_log("Excepción en favoritos_ajax.php (POST): " . $e->getMessage());
            $response = ['status' => 'error', 'message' => 'Error procesando la solicitud: ' . $e->getMessage()];
        }
    }
}
// Fallback a GET solo para 'verificar' si no se proporcionaron datos POST
else if (isset($_GET['accion']) && $_GET['accion'] === 'verificar' && isset($_GET['veh_id']) && !isset($_POST['accion'])) {
    $veh_id = filter_var($_GET['veh_id'], FILTER_VALIDATE_INT);
    if ($veh_id === false) {
        $response = ['status' => 'error', 'message' => 'ID de vehículo no válido (GET).'];
    } else {
        try {
            $favoritos_model = new Favoritos_M();
            // El método en el modelo se llama verificarFavorito, no esFavorito
            $es_favorito = $favoritos_model->verificarFavorito($usu_id, $veh_id);
            $response = [
                'status' => 'success',
                'esFavorito' => $es_favorito, // (bool) ya es manejado por el modelo
                'message' => $es_favorito ? 'El vehículo es favorito.' : 'El vehículo no es favorito.'
            ];
        } catch (Exception $e) {
            error_log("Excepción en favoritos_ajax.php (GET verificar): " . $e->getMessage());
            $response = ['status' => 'error', 'message' => 'Error verificando estado: ' . $e->getMessage()];
        }
    }
} else {
     $response = ['status' => 'error', 'message' => 'Parámetros incompletos. Se requiere acción y veh_id (preferiblemente vía POST).'];
}

echo json_encode($response);
exit();
?>