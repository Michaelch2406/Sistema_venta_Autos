<?php
// registro_ajax.php
require_once __DIR__ . "/../MODELOS/usuarios_m.php";
require_once __DIR__ . "/../CONFIG/global.php";
header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Solicitud inválida.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger y sanitizar
    $usuario   = trim($_POST['usu_usuario'] ?? '');
    $nombre    = trim($_POST['usu_nombre'] ?? '');
    $apellido  = trim($_POST['usu_apellido'] ?? '');
    $email     = trim($_POST['usu_email'] ?? '');
    $password  = $_POST['usu_password'] ?? '';
    $telefono  = trim($_POST['usu_telefono'] ?? '');
    $direccion = trim($_POST['usu_direccion'] ?? '');
    $fnac      = trim($_POST['usu_fnacimiento'] ?? '');
    $accept    = isset($_POST['accept_terms']);

    // 1) Campos obligatorios
    if (!$usuario || !$nombre || !$apellido || !$email || !$password) {
        $response['message'] = 'Todos los campos marcados con * son obligatorios.';
        echo json_encode($response); exit;
    }
    // 2) Usuario alfanumérico
    if (!preg_match('/^[A-Za-z0-9_]{3,20}$/', $usuario)) {
        $response['message'] = 'Usuario inválido.';
        echo json_encode($response); exit;
    }
    // 3) Email
    $email_regex = '/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match($email_regex, $email)) {
        $response['message'] = 'Correo electrónico inválido.';
        echo json_encode($response); exit;
    }
    // 4) Nombre y apellido letras
    if (!preg_match('/^[\p{L} ]+$/u', $nombre) || !preg_match('/^[\p{L} ]+$/u', $apellido)) {
        $response['message'] = 'Nombre y apellido deben contener solo letras y espacios.';
        echo json_encode($response); exit;
    }
    // 5) Contraseña
    if (strlen($password) < 8) {
        $response['message'] = 'La contraseña debe tener al menos 8 caracteres.';
        echo json_encode($response); exit;
    }
    // 6) Términos
    if (!$accept) {
        $response['message'] = 'Debes aceptar los términos y condiciones.';
        echo json_encode($response); exit;
    }
    // 7) Teléfono opcional
    if ($telefono !== '' && !preg_match('/^\+?[0-9]{7,15}$/', $telefono)) {
        $response['message'] = 'Teléfono inválido. Entre 7 y 15 dígitos, opcional +.';
        echo json_encode($response); exit;
    }
    // 8) Dirección opcional
    if ($direccion !== '' && strlen($direccion) < 5) {
        $response['message'] = 'La dirección es muy corta.';
        echo json_encode($response); exit;
    }
    // 9) Fecha de nacimiento opcional (0–99 años)
    if ($fnac !== '') {
        $hoy = new DateTime('today');
        $lim = (clone $hoy)->modify('-99 years');
        $dob = DateTime::createFromFormat('Y-m-d', $fnac);
        if (!$dob) {
            $response['message'] = 'Formato de fecha inválido.';
            echo json_encode($response); exit;
        }
        if ($dob < $lim || $dob > $hoy) {
            $response['message'] = 'Debes tener entre 0 y 99 años.';
            echo json_encode($response); exit;
        }
    }

    // Todo validado: insertar
    $usuario_model = new Usuario();
    $rol_cliente = 1;
    $res = $usuario_model->registrarUsuario(
        $rol_cliente, $usuario, $nombre, $apellido,
        $email, $password, $telefono, $direccion, $fnac
    );
    if (isset($res['resultado']) && $res['resultado'] == 1) {
        $response['status']  = 'success';
        $response['message'] = $res['mensaje'];
    } else {
        $response['message'] = $res['mensaje'] ?? 'Error al procesar el registro.';
    }
}

echo json_encode($response);
