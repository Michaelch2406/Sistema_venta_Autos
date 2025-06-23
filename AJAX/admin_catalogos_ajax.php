<?php
ini_set('display_errors', 0); // No mostrar errores PHP directamente en la respuesta JSON
error_reporting(E_ALL); // Reportar todos los errores
// Para producción, configura el logueo de errores a un archivo:
// ini_set('log_errors', 1);
// ini_set('error_log', __DIR__ . '/../php_error.log'); // Asegúrate que esta ruta sea escribible

session_start();
require_once __DIR__ . "/../MODELOS/catalogos_m.php"; // Único modelo necesario aquí

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Acción no válida o faltan datos.'];

// Verificar si el usuario es Administrador
$rol_admin_id = 3; // ID del rol Administrador (Ajusta según tu tabla Roles)
if (!isset($_SESSION['usu_id']) || !isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != $rol_admin_id) {
    $response = ['status' => 'error', 'message' => 'Acceso denegado. Permisos insuficientes.'];
    echo json_encode($response);
    exit();
}

try {
    $catalogos_model = new Catalogos();
    $accion = '';

    // Determinar la acción basada en el método de solicitud
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion'])) {
        $accion = trim($_GET['accion']);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
        $accion = trim($_POST['accion']);
    }

    if (empty($accion)) {
        $response['message'] = 'Acción no especificada.';
        echo json_encode($response);
        exit();
    }

    switch ($accion) {
        // --- ACCIONES GET ---
        case 'listarMarcas':
            $marcas = $catalogos_model->adminGetAllMarcas();
            if ($marcas !== false) {
                $response = ['status' => 'success', 'marcas' => $marcas];
            } else {
                $response['message'] = 'Error al obtener la lista de marcas.';
                error_log("admin_catalogos_ajax.php: listarMarcas - El modelo devolvió false.");
            }
            break;

        case 'listarModelos':
            if (isset($_GET['marca_id'])) {
                $marca_id = filter_var($_GET['marca_id'], FILTER_VALIDATE_INT);
                if ($marca_id) {
                    $modelos = $catalogos_model->adminGetModelosPorMarca($marca_id);
                    if ($modelos !== false) {
                        $response = ['status' => 'success', 'modelos' => $modelos];
                    } else {
                        $response['message'] = 'Error al obtener modelos para la marca seleccionada.';
                        $response['modelos'] = [];
                        error_log("admin_catalogos_ajax.php: listarModelos - marca_id $marca_id devolvió false.");
                    }
                } else {
                    $response['message'] = 'ID de marca para listar modelos no es válido.';
                }
            } else {
                $response['message'] = 'Falta el ID de marca para listar modelos.';
            }
            break;

        case 'listarTiposVehiculo':
            $tipos = $catalogos_model->adminGetAllTiposVehiculo();
            if ($tipos !== false) {
                $response = ['status' => 'success', 'tipos_vehiculo' => $tipos];
            } else {
                $response['message'] = 'Error al obtener la lista de tipos de vehículo.';
                 error_log("admin_catalogos_ajax.php: listarTiposVehiculo - El modelo devolvió false.");
            }
            break;

        // --- ACCIONES POST ---
        case 'guardarMarca':
            $mar_id = isset($_POST['mar_id']) && !empty($_POST['mar_id']) ? filter_var($_POST['mar_id'], FILTER_VALIDATE_INT) : null;
            $mar_nombre = isset($_POST['mar_nombre']) ? trim(filter_var($_POST['mar_nombre'], FILTER_SANITIZE_STRING)) : null;
            $mar_logo_url = isset($_POST['mar_logo_url']) ? trim(filter_var($_POST['mar_logo_url'], FILTER_SANITIZE_URL)) : null;
            if (empty($mar_logo_url)) $mar_logo_url = null;

            if (empty($mar_nombre)) {
                $response['message'] = 'El nombre de la marca es obligatorio.';
            } else {
                if ($mar_id) { // Actualizar
                    $resultado_sp = $catalogos_model->adminActualizarMarca($mar_id, $mar_nombre, $mar_logo_url);
                } else { // Insertar
                    $resultado_sp = $catalogos_model->adminInsertarMarca($mar_nombre, $mar_logo_url);
                }
                if (isset($resultado_sp['resultado']) && $resultado_sp['resultado'] == 1) {
                    $response = ['status' => 'success', 'message' => $resultado_sp['mensaje'], 'mar_id' => $resultado_sp['mar_id'] ?? $mar_id];
                } else {
                    $response['message'] = $resultado_sp['mensaje'] ?? 'Error al guardar la marca.';
                }
            }
            break;

        case 'eliminarMarca':
            if (isset($_POST['mar_id'])) {
                $mar_id = filter_var($_POST['mar_id'], FILTER_VALIDATE_INT);
                if ($mar_id) {
                    $resultado_sp = $catalogos_model->adminEliminarMarca($mar_id);
                    if (isset($resultado_sp['resultado']) && $resultado_sp['resultado'] == 1) {
                        $response = ['status' => 'success', 'message' => $resultado_sp['mensaje']];
                    } else {
                        $response['message'] = $resultado_sp['mensaje'] ?? 'Error al eliminar la marca.';
                    }
                } else {
                    $response['message'] = 'ID de marca para eliminar no es válido.';
                }
            } else {
                 $response['message'] = 'Falta el ID de marca para eliminar.';
            }
            break;

        case 'guardarModelo':
            $mod_id = isset($_POST['mod_id']) && !empty($_POST['mod_id']) ? filter_var($_POST['mod_id'], FILTER_VALIDATE_INT) : null;
            $mar_id_fk = isset($_POST['mar_id_fk']) ? filter_var($_POST['mar_id_fk'], FILTER_VALIDATE_INT) : null;
            $mod_nombre = isset($_POST['mod_nombre']) ? trim(filter_var($_POST['mod_nombre'], FILTER_SANITIZE_STRING)) : null;

            if (empty($mar_id_fk) || empty($mod_nombre)) {
                $response['message'] = 'La marca y el nombre del modelo son obligatorios.';
            } else {
                if ($mod_id) { // Actualizar
                    $resultado_sp = $catalogos_model->adminActualizarModelo($mod_id, $mar_id_fk, $mod_nombre);
                } else { // Insertar
                    $resultado_sp = $catalogos_model->adminInsertarModelo($mar_id_fk, $mod_nombre);
                }
                if (isset($resultado_sp['resultado']) && $resultado_sp['resultado'] == 1) {
                    $response = ['status' => 'success', 'message' => $resultado_sp['mensaje'], 'mod_id' => $resultado_sp['mod_id'] ?? $mod_id, 'mar_id_fk' => $mar_id_fk];
                } else {
                    $response['message'] = $resultado_sp['mensaje'] ?? 'Error al guardar el modelo.';
                }
            }
            break;

        case 'eliminarModelo':
            if (isset($_POST['mod_id'])) {
                $mod_id = filter_var($_POST['mod_id'], FILTER_VALIDATE_INT);
                if ($mod_id) {
                    $resultado_sp = $catalogos_model->adminEliminarModelo($mod_id);
                    if (isset($resultado_sp['resultado']) && $resultado_sp['resultado'] == 1) {
                        $response = ['status' => 'success', 'message' => $resultado_sp['mensaje']];
                    } else {
                        $response['message'] = $resultado_sp['mensaje'] ?? 'Error al eliminar el modelo.';
                    }
                } else {
                    $response['message'] = 'ID de modelo para eliminar no es válido.';
                }
            } else {
                 $response['message'] = 'Falta el ID de modelo para eliminar.';
            }
            break;

        case 'guardarTipoVehiculo':
            $tiv_id = isset($_POST['tiv_id']) && !empty($_POST['tiv_id']) ? filter_var($_POST['tiv_id'], FILTER_VALIDATE_INT) : null;
            $tiv_nombre = isset($_POST['tiv_nombre']) ? trim(filter_var($_POST['tiv_nombre'], FILTER_SANITIZE_STRING)) : null;
            $tiv_descripcion = isset($_POST['tiv_descripcion']) ? trim(filter_var($_POST['tiv_descripcion'], FILTER_SANITIZE_STRING)) : null;
            $tiv_icono_url = isset($_POST['tiv_icono_url']) ? trim(filter_var($_POST['tiv_icono_url'], FILTER_SANITIZE_URL)) : null;
            $tiv_activo = isset($_POST['tiv_activo']) && $_POST['tiv_activo'] == '1'; // Booleano

            if (empty($tiv_nombre)) {
                $response['message'] = 'El nombre del tipo de vehículo es obligatorio.';
            } else {
                if (empty($tiv_descripcion)) $tiv_descripcion = null;
                if (empty($tiv_icono_url)) $tiv_icono_url = null;

                if ($tiv_id) { // Actualizar
                    $resultado_sp = $catalogos_model->adminActualizarTipoVehiculo($tiv_id, $tiv_nombre, $tiv_descripcion, $tiv_icono_url, $tiv_activo);
                } else { // Insertar
                    $resultado_sp = $catalogos_model->adminInsertarTipoVehiculo($tiv_nombre, $tiv_descripcion, $tiv_icono_url, $tiv_activo);
                }
                if (isset($resultado_sp['resultado']) && $resultado_sp['resultado'] == 1) {
                    $response = ['status' => 'success', 'message' => $resultado_sp['mensaje'], 'tiv_id' => $resultado_sp['tiv_id'] ?? $tiv_id];
                } else {
                    $response['message'] = $resultado_sp['mensaje'] ?? 'Error al guardar el tipo de vehículo.';
                }
            }
            break;

        case 'eliminarTipoVehiculo':
            if (isset($_POST['tiv_id'])) {
                $tiv_id = filter_var($_POST['tiv_id'], FILTER_VALIDATE_INT);
                if ($tiv_id) {
                    $resultado_sp = $catalogos_model->adminEliminarTipoVehiculo($tiv_id);
                    if (isset($resultado_sp['resultado']) && $resultado_sp['resultado'] == 1) {
                        $response = ['status' => 'success', 'message' => $resultado_sp['mensaje']];
                    } else {
                        $response['message'] = $resultado_sp['mensaje'] ?? 'Error al eliminar el tipo de vehículo.';
                    }
                } else {
                    $response['message'] = 'ID de tipo de vehículo para eliminar no es válido.';
                }
            } else {
                 $response['message'] = 'Falta el ID de tipo de vehículo para eliminar.';
            }
            break;
        
        default:
            $response['message'] = 'Acción POST de catálogo no reconocida.';
            break;
    }
} catch (Exception $e) {
    error_log("Excepción en admin_catalogos_ajax.php: " . $e->getMessage() . " en " . $e->getFile() . ":" . $e->getLine());
    $response = ['status' => 'error', 'message' => 'Ocurrió un error crítico en el servidor. Por favor, intente más tarde.'];
}

echo json_encode($response);
?>