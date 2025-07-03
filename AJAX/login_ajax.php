<?php
// It's good practice to set cookie params before session_start()
// if not done globally in php.ini or a central bootstrap file.
// Assuming the application is at the root of the domain.
// And session cookies should be valid for the entire domain (not just subdomains).
if (session_status() == PHP_SESSION_NONE) {
    // Set cookie parameters:
    // lifetime: 0 means until browser closes
    // path: '/' means entire domain
    // domain: current domain (empty string usually works, or explicitly set if needed for subdomains)
    // secure: true if site is HTTPS, false if HTTP (important for cookie delivery)
    // httponly: true to prevent JS access to cookie
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params(
        $cookieParams["lifetime"],
        '/', // Path
        $cookieParams["domain"], // Domain - adjust if using subdomains
        isset($_SERVER['HTTPS']), // Secure - true if HTTPS
        true // HttpOnly
    );
    session_start();
}

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
                    
                    // Regenerate session ID for security and to prevent fixation
                    session_regenerate_id(true); 

                    $_SESSION['usu_id'] = $datos_usuario_sp['usu_id'];
                    $_SESSION['usu_nombre_completo'] = $datos_usuario_sp['usu_nombre'] . " " . $datos_usuario_sp['usu_apellido'];
                    $_SESSION['usu_email'] = $datos_usuario_sp['usu_email'];
                    $_SESSION['rol_id'] = (int)$datos_usuario_sp['rol_id'];

                    $response['status'] = 'success';
                    $response['message'] = 'Inicio de sesión exitoso.';

                    $rol_admin_id = 3; 

                    if ($_SESSION['rol_id'] === $rol_admin_id) {
                        $response['redirect_url'] = 'admin_panel.php';
                    } else {
                        $response['redirect_url'] = 'escritorio.php';
                    }

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