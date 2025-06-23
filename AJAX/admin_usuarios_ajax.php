<?php
session_start();
require_once __DIR__ . "/../MODELOS/usuarios_m.php";

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Acción no válida o faltan datos.'];

// Verificar si es Administrador
$rol_admin_id = 3; // ¡Verifica este ID!
if (!isset($_SESSION['usu_id']) || !isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != $rol_admin_id) {
    $response = ['status' => 'error', 'message' => 'Acceso denegado. Permisos insuficientes.'];
    echo json_encode($response);
    exit();
}

$usuario_model = new Usuario();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion'])) {
    $accion = $_GET['accion'];

    if ($accion === 'listarUsuarios') {
        $usuarios = $usuario_model->getTodosUsuariosAdmin();
        if ($usuarios !== false) {
            $response = ['status' => 'success', 'data' => $usuarios];
        } else {
            $response['message'] = 'Error al obtener la lista de usuarios.';
        }
    } elseif ($accion === 'getRoles') {
        $roles = $usuario_model->getRolesParaSelect();
        if ($roles !== false) {
            $response = ['status' => 'success', 'data' => $roles];
        } else {
            $response['message'] = 'Error al obtener la lista de roles.';
        }
    } elseif ($accion === 'getUsuario' && isset($_GET['usu_id'])) {
        $usu_id = filter_var($_GET['usu_id'], FILTER_VALIDATE_INT);
        if ($usu_id) {
            $usuario = $usuario_model->getUsuarioPorIdAdmin($usu_id);
            if ($usuario) {
                $response = ['status' => 'success', 'data' => $usuario];
            } else {
                $response['message'] = 'Usuario no encontrado o error al obtener datos.';
            }
        } else {
            $response['message'] = 'ID de usuario inválido.';
        }
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $accion = $_POST['accion'];

    if ($accion === 'crearUsuario') {
        // Validaciones básicas
        if (empty($_POST['usu_usuario']) || empty($_POST['usu_nombre']) || empty($_POST['usu_apellido']) || empty($_POST['usu_email']) || empty($_POST['rol_id']) || empty($_POST['usu_password'])) {
            $response['message'] = 'Faltan campos obligatorios para crear el usuario.';
        } elseif (!filter_var($_POST['usu_email'], FILTER_VALIDATE_EMAIL)) {
            $response['message'] = 'Formato de correo electrónico inválido.';
        } elseif (strlen($_POST['usu_password']) < 8) {
            $response['message'] = 'La contraseña debe tener al menos 8 caracteres.';
        } else {
            $resultado_sp = $usuario_model->crearUsuarioAdmin(
                $_POST['rol_id'],
                trim($_POST['usu_usuario']),
                trim($_POST['usu_nombre']),
                trim($_POST['usu_apellido']),
                trim($_POST['usu_email']),
                $_POST['usu_password'], // Se hashea en el modelo
                isset($_POST['usu_telefono']) ? trim($_POST['usu_telefono']) : null,
                isset($_POST['usu_direccion']) ? trim($_POST['usu_direccion']) : null,
                isset($_POST['usu_fnacimiento']) && !empty($_POST['usu_fnacimiento']) ? $_POST['usu_fnacimiento'] : null,
                isset($_POST['usu_verificado']) ? (bool)$_POST['usu_verificado'] : false
            );
            if ($resultado_sp['resultado'] == 1) {
                $response = ['status' => 'success', 'message' => $resultado_sp['mensaje'], 'usu_id' => $resultado_sp['usu_id']];
            } else {
                $response['message'] = $resultado_sp['mensaje'];
            }
        }
    } elseif ($accion === 'actualizarUsuario') {
         if (empty($_POST['usu_id']) || empty($_POST['usu_usuario']) || empty($_POST['usu_nombre']) || empty($_POST['usu_apellido']) || empty($_POST['usu_email']) || empty($_POST['rol_id'])) {
            $response['message'] = 'Faltan campos obligatorios para actualizar el usuario.';
        } elseif (!filter_var($_POST['usu_email'], FILTER_VALIDATE_EMAIL)) {
            $response['message'] = 'Formato de correo electrónico inválido.';
        } elseif (!empty($_POST['usu_password']) && strlen($_POST['usu_password']) < 8) { // Validar contraseña solo si se provee una nueva
            $response['message'] = 'La nueva contraseña debe tener al menos 8 caracteres.';
        } else {
            $resultado_sp = $usuario_model->actualizarUsuarioAdmin(
                $_POST['usu_id'],
                $_POST['rol_id'],
                trim($_POST['usu_usuario']),
                trim($_POST['usu_nombre']),
                trim($_POST['usu_apellido']),
                trim($_POST['usu_email']),
                !empty($_POST['usu_password']) ? $_POST['usu_password'] : null, // Pasar null si no se cambia contraseña
                isset($_POST['usu_telefono']) ? trim($_POST['usu_telefono']) : null,
                isset($_POST['usu_direccion']) ? trim($_POST['usu_direccion']) : null,
                isset($_POST['usu_fnacimiento']) && !empty($_POST['usu_fnacimiento']) ? $_POST['usu_fnacimiento'] : null,
                isset($_POST['usu_verificado']) ? (bool)$_POST['usu_verificado'] : false
            );
             if ($resultado_sp['resultado'] == 1) {
                $response = ['status' => 'success', 'message' => $resultado_sp['mensaje']];
            } else {
                $response['message'] = $resultado_sp['mensaje'];
            }
        }
    }
}

echo json_encode($response);
?>