<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

header('Content-Type: application/json');

require_once __DIR__ . '/../MODELOS/usuarios_m.php'; // Ajusta la ruta si es necesario

$response = ['status' => 'error', 'message' => 'Solicitud no válida.'];

if (!isset($_SESSION['usu_id'])) {
    $response = ['status' => 'error', 'message' => 'Usuario no autenticado. Por favor, inicie sesión.'];
    echo json_encode($response);
    exit();
}

$usu_id_actual = $_SESSION['usu_id'];
$usuarios_model = new Usuario(); // Corregido: Usar el nombre de clase Usuario (singular)

if (isset($_POST['accion'])) {
    $accion = $_POST['accion'];

    switch ($accion) {
        case 'actualizar_perfil':
            // Recoger los datos del perfil. Asegúrate de que los nombres coincidan con los del formulario JS.
            $datos_perfil = [
                'nombre'      => $_POST['nombre'] ?? null,
                'apellido'    => $_POST['apellido'] ?? null,
                'cedula'      => $_POST['cedula'] ?? null,
                'telefono'    => $_POST['telefono'] ?? null,
                'direccion'   => $_POST['direccion'] ?? null,
                'fnacimiento' => $_POST['fnacimiento'] ?? null
            ];
            
            // Aquí podrías añadir una capa de validación de servidor más exhaustiva si es necesario,
            // aunque los SPs y el modelo ya tienen algunas.
            // Por ejemplo, verificar que los campos requeridos no estén vacíos.
            if (empty($datos_perfil['nombre']) || empty($datos_perfil['apellido']) || empty($datos_perfil['cedula'])) {
                $response = ['status' => 'validation_error', 'message' => 'Nombre, apellido y cédula son campos requeridos.'];
            } else {
                $response = $usuarios_model->actualizarPerfil($usu_id_actual, $datos_perfil);
            }
            break;

        case 'cambiar_password':
            $pass_actual = $_POST['pass_actual'] ?? '';
            $pass_nueva = $_POST['pass_nueva'] ?? '';
            $pass_confirmar = $_POST['pass_confirmar'] ?? '';

            if (empty($pass_actual) || empty($pass_nueva) || empty($pass_confirmar)) {
                $response = ['status' => 'validation_error', 'message' => 'Todos los campos de contraseña son requeridos.'];
            } elseif ($pass_nueva !== $pass_confirmar) {
                $response = ['status' => 'validation_error', 'message' => 'La nueva contraseña y su confirmación no coinciden.'];
            } else {
                $response = $usuarios_model->cambiarContrasena($usu_id_actual, $pass_actual, $pass_nueva);
            }
            break;

        default:
            $response = ['status' => 'error', 'message' => 'Acción desconocida o no especificada.'];
            break;
    }
} else {
    $response = ['status' => 'error', 'message' => 'No se especificó ninguna acción.'];
}

echo json_encode($response);
exit();
?>
