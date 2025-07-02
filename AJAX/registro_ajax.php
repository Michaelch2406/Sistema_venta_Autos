<?php
ini_set('display_errors', 0); 
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../php_error.log'); 

require_once __DIR__ . "/../MODELOS/usuarios_m.php";
require_once __DIR__ . "/../CONFIG/global.php";
header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Solicitud inválida.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario   = trim($_POST['usu_usuario'] ?? '');
    $nombre    = trim($_POST['usu_nombre'] ?? '');
    $apellido  = trim($_POST['usu_apellido'] ?? '');
    $email     = trim($_POST['usu_email'] ?? '');
    // === CAMPO AÑADIDO ===
    $cedula    = trim($_POST['usu_cedula'] ?? '');
    // =====================
    $password  = $_POST['usu_password'] ?? '';
    $telefono  = trim($_POST['usu_telefono'] ?? '');
    $direccion = trim($_POST['usu_direccion'] ?? '');
    $fnac      = trim($_POST['usu_fnacimiento'] ?? '');
    $accept    = isset($_POST['accept_terms']);

    if (!$usuario || !$nombre || !$apellido || !$email || !$password || !$cedula) { // Cedula añadida a la validación
        $response['message'] = 'Todos los campos marcados con * son obligatorios.';
        echo json_encode($response); exit;
    }
    if (!preg_match('/^[A-Za-z0-9_]{3,20}$/', $usuario)) {
        $response['message'] = 'Usuario inválido.';
        echo json_encode($response); exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Correo electrónico inválido.';
        echo json_encode($response); exit;
    }
    // === VALIDACIÓN AÑADIDA ===
    if (!preg_match('/^\d{10,13}$/', $cedula)) {
        $response['message'] = 'La cédula o RUC debe tener 10 o 13 dígitos numéricos.';
        echo json_encode($response); exit;
    }
    // ==========================
    if (!preg_match('/^[\p{L} ]+$/u', $nombre) || !preg_match('/^[\p{L} ]+$/u', $apellido)) {
        $response['message'] = 'Nombre y apellido deben contener solo letras y espacios.';
        echo json_encode($response); exit;
    }
    if (strlen($password) < 8) {
        $response['message'] = 'La contraseña debe tener al menos 8 caracteres.';
        echo json_encode($response); exit;
    }
    if (!$accept) {
        $response['message'] = 'Debes aceptar los términos y condiciones.';
        echo json_encode($response); exit;
    }

    $usuario_model = new Usuario();
    $rol_cliente = 1; 
    $res = $usuario_model->registrarUsuario(
        $rol_cliente, $usuario, $nombre, $apellido, $email, 
        $cedula, // Parámetro añadido en la llamada
        $password, $telefono, $direccion, $fnac
    );
    if (isset($res['resultado']) && $res['resultado'] == 1) {
        $response['status']  = 'success';
        $response['message'] = $res['mensaje'];
    } else {
        $response['message'] = $res['mensaje'] ?? 'Error al procesar el registro.';
    }
}

echo json_encode($response);
?>