<?php
session_start();
require_once __DIR__ . "/../MODELOS/usuarios_m.php";

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Solicitud inválida.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_login = isset($_POST['usu_usuario']) ? trim($_POST['usu_usuario']) : null;
    $password_login = isset($_POST['usu_password']) ? $_POST['usu_password'] : null;

    if (empty($usuario_login) || empty($password_login)) {
        $response['message'] = 'Usuario y contraseña son requeridos.';
    } else {
        $usuario_model = new Usuario();
        $datos_usuario_sp = $usuario_model->loginUsuario($usuario_login);

        if (isset($datos_usuario_sp['resultado'])) {
            if ($datos_usuario_sp['resultado'] == 1) { // Usuario encontrado por SP
                if (password_verify($password_login, $datos_usuario_sp['usu_password'])) {
                    // Contraseña correcta
                    $_SESSION['usu_id'] = $datos_usuario_sp['usu_id'];
                    $_SESSION['usu_nombre_completo'] = $datos_usuario_sp['usu_nombre'] . " " . $datos_usuario_sp['usu_apellido'];
                    $_SESSION['usu_email'] = $datos_usuario_sp['usu_email'];
                    $_SESSION['rol_id'] = (int)$datos_usuario_sp['rol_id']; // Convertir a entero para comparación estricta

                    $response['status'] = 'success';
                    $response['message'] = 'Inicio de sesión exitoso.';

                    // --- REDIRECCIÓN BASADA EN ROL ---
                    $rol_admin_id = 3; // Asegúrate que este sea el ID correcto del rol 'Administrador'

                    if ($_SESSION['rol_id'] === $rol_admin_id) {
                        $response['redirect_url'] = 'admin_panel.php'; // Redirigir Administradores
                    } else {
                        $response['redirect_url'] = 'escritorio.php'; // Redirigir otros usuarios (Cliente/Vendedor, Asesor)
                    }
                    // --- FIN REDIRECCIÓN ---

                } else {
                    $response['message'] = 'Contraseña incorrecta.';
                }
            } else {
                $response['message'] = $datos_usuario_sp['mensaje']; 
            }
        } else {
            $response['message'] = 'Error al intentar iniciar sesión.';
        }
    }
}
echo json_encode($response);
?>