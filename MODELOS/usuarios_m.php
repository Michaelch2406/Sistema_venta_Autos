<?php
require_once __DIR__ . "/../CONFIG/Conexion.php";

class Usuario
{
    private $conn_obj; // Objeto Conexion
    private $conn;     // Conexión mysqli

    public function __construct()
    {
        $this->conn_obj = new Conexion();
        $this->conn = $this->conn_obj->conecta();
        if ($this->conn->connect_error) {
            error_log("Error de conexión al modelo Usuario: " . $this->conn->connect_error);
            throw new Exception("Error de conexión al modelo Usuario: " . $this->conn->connect_error);
        }
    }

    // --- Métodos existentes (registrarUsuario, loginUsuario) ---
    public function registrarUsuario(
        $rol_id, $usuario, $nombre, $apellido, $email,
        $password_plana, $telefono, $direccion, $fnacimiento
    ) {
        $password_hash = password_hash($password_plana, PASSWORD_DEFAULT);
        $rol_id_esc = $this->conn->real_escape_string($rol_id);
        $usuario_esc = $this->conn->real_escape_string($usuario);
        $nombre_esc = $this->conn->real_escape_string($nombre);
        $apellido_esc = $this->conn->real_escape_string($apellido);
        $email_esc = $this->conn->real_escape_string($email);
        $telefono_esc = $telefono ? "'" . $this->conn->real_escape_string($telefono) . "'" : "NULL";
        $direccion_esc = $direccion ? "'" . $this->conn->real_escape_string($direccion) . "'" : "NULL";
        $fnacimiento_esc = $fnacimiento ? "'" . $this->conn->real_escape_string($fnacimiento) . "'" : "NULL";

        $sql = "CALL sp_registrar_usuario(
            $rol_id_esc, '$usuario_esc', '$nombre_esc', '$apellido_esc', '$email_esc',
            '$password_hash', $telefono_esc, $direccion_esc, $fnacimiento_esc,
            @p_resultado, @p_mensaje
        )";
        if (!$this->conn->query($sql)) {
            error_log("Error al llamar a sp_registrar_usuario: " . $this->conn->error);
            return ['resultado' => -2, 'mensaje' => 'Error técnico al registrar.'];
        }
        $res = $this->conn->query("SELECT @p_resultado AS resultado, @p_mensaje AS mensaje");
        $out_params = $res->fetch_assoc();
        $res->free();
        while($this->conn->more_results() && $this->conn->next_result()){;}
        return $out_params;
    }

    public function loginUsuario($usuario_o_email)
    {
        $usuario_o_email_esc = $this->conn->real_escape_string($usuario_o_email);
        $sql = "CALL sp_login_usuario(
            '$usuario_o_email_esc',
            @p_usu_id, @p_rol_id, @p_usu_nombre, @p_usu_apellido,
            @p_usu_email_db, @p_usu_password_hash, @p_usu_verificado,
            @p_resultado, @p_mensaje
        )";
        if (!$this->conn->query($sql)) {
            error_log("Error al llamar a sp_login_usuario: " . $this->conn->error);
            return ['resultado' => 0, 'mensaje' => 'Error técnico al login.'];
        }
        $res = $this->conn->query("SELECT @p_usu_id AS usu_id, @p_rol_id AS rol_id,
            @p_usu_nombre AS usu_nombre, @p_usu_apellido AS usu_apellido,
            @p_usu_email_db AS usu_email, @p_usu_password_hash AS usu_password,
            @p_usu_verificado AS usu_verificado,
            @p_resultado AS resultado, @p_mensaje AS mensaje");
        $out_params = $res->fetch_assoc();
        $res->free();
        while($this->conn->more_results() && $this->conn->next_result()){;}
        return $out_params;
    }

    // --- NUEVOS MÉTODOS PARA EL PANEL DE ADMIN ---
    public function getTodosUsuariosAdmin()
    {
        $sql = "CALL sp_get_todos_usuarios_admin()";
        $resultado = $this->conn_obj->ejecutarSP($sql);
        $usuarios = [];
        if ($resultado && $resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $usuarios[] = $fila;
            }
            $resultado->free();
        } elseif ($resultado === false) {
             error_log("Error al ejecutar sp_get_todos_usuarios_admin: " . $this->conn->error);
             return false;
        }
        while($this->conn->more_results() && $this->conn->next_result()){;}
        return $usuarios;
    }

    public function getRolesParaSelect()
    {
        $sql = "CALL sp_get_roles_para_select()";
        $resultado = $this->conn_obj->ejecutarSP($sql);
        $roles = [];
        if ($resultado && $resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $roles[] = $fila;
            }
            $resultado->free();
        } elseif ($resultado === false) {
            error_log("Error al ejecutar sp_get_roles_para_select: " . $this->conn->error);
            return false;
        }
        while($this->conn->more_results() && $this->conn->next_result()){;}
        return $roles;
    }

    public function getUsuarioPorIdAdmin($usu_id)
    {
        $usu_id_esc = $this->conn->real_escape_string($usu_id);
        $sql = "CALL sp_get_usuario_por_id_admin($usu_id_esc)";
        $resultado = $this->conn_obj->ejecutarSP($sql);
        $usuario = null;
        if ($resultado && $resultado->num_rows > 0) {
            $usuario = $resultado->fetch_assoc();
            $resultado->free();
        } elseif ($resultado === false) {
            error_log("Error al ejecutar sp_get_usuario_por_id_admin: " . $this->conn->error);
            return false;
        }
        while($this->conn->more_results() && $this->conn->next_result()){;}
        return $usuario;
    }

    public function crearUsuarioAdmin(
        $rol_id, $usuario, $nombre, $apellido, $email,
        $password_plana, $telefono, $direccion, $fnacimiento, $verificado
    ) {
        $password_hash = password_hash($password_plana, PASSWORD_DEFAULT);
        $rol_id_esc = $this->conn->real_escape_string($rol_id);
        $usuario_esc = $this->conn->real_escape_string($usuario);
        $nombre_esc = $this->conn->real_escape_string($nombre);
        $apellido_esc = $this->conn->real_escape_string($apellido);
        $email_esc = $this->conn->real_escape_string($email);
        $telefono_esc = $telefono ? "'" . $this->conn->real_escape_string($telefono) . "'" : "NULL";
        $direccion_esc = $direccion ? "'" . $this->conn->real_escape_string($direccion) . "'" : "NULL";
        $fnacimiento_esc = $fnacimiento ? "'" . $this->conn->real_escape_string($fnacimiento) . "'" : "NULL";
        $verificado_esc = (bool)$verificado ? 'TRUE' : 'FALSE';

        $sql = "CALL sp_crear_usuario_admin(
            $rol_id_esc, '$usuario_esc', '$nombre_esc', '$apellido_esc', '$email_esc',
            '$password_hash', $telefono_esc, $direccion_esc, $fnacimiento_esc, $verificado_esc,
            @p_usu_id_creado, @p_resultado, @p_mensaje
        )";
        if (!$this->conn->query($sql)) {
            error_log("Error al llamar a sp_crear_usuario_admin: " . $this->conn->error);
            return ['resultado' => -2, 'mensaje' => 'Error técnico al crear usuario (admin).'];
        }
        $res = $this->conn->query("SELECT @p_usu_id_creado as usu_id, @p_resultado AS resultado, @p_mensaje AS mensaje");
        $out_params = $res->fetch_assoc();
        $res->free();
        while($this->conn->more_results() && $this->conn->next_result()){;}
        return $out_params;
    }

    public function actualizarUsuarioAdmin(
        $usu_id, $rol_id, $usuario, $nombre, $apellido, $email,
        $password_plana, // Puede ser vacía si no se cambia
        $telefono, $direccion, $fnacimiento, $verificado
    ) {
        $password_hash_sql = "NULL"; // Por defecto no se cambia la contraseña
        if (!empty($password_plana)) {
            $password_hash = password_hash($password_plana, PASSWORD_DEFAULT);
            $password_hash_sql = "'" . $this->conn->real_escape_string($password_hash) . "'";
        }

        $usu_id_esc = $this->conn->real_escape_string($usu_id);
        $rol_id_esc = $this->conn->real_escape_string($rol_id);
        $usuario_esc = $this->conn->real_escape_string($usuario);
        $nombre_esc = $this->conn->real_escape_string($nombre);
        $apellido_esc = $this->conn->real_escape_string($apellido);
        $email_esc = $this->conn->real_escape_string($email);
        $telefono_esc = $telefono ? "'" . $this->conn->real_escape_string($telefono) . "'" : "NULL";
        $direccion_esc = $direccion ? "'" . $this->conn->real_escape_string($direccion) . "'" : "NULL";
        $fnacimiento_esc = $fnacimiento ? "'" . $this->conn->real_escape_string($fnacimiento) . "'" : "NULL";
        $verificado_esc = (bool)$verificado ? 'TRUE' : 'FALSE';

        $sql = "CALL sp_actualizar_usuario_admin(
            $usu_id_esc, $rol_id_esc, '$usuario_esc', '$nombre_esc', '$apellido_esc', '$email_esc',
            $password_hash_sql, 
            $telefono_esc, $direccion_esc, $fnacimiento_esc, $verificado_esc,
            @p_resultado, @p_mensaje
        )";
        if (!$this->conn->query($sql)) {
            error_log("Error al llamar a sp_actualizar_usuario_admin: " . $this->conn->error);
            return ['resultado' => -2, 'mensaje' => 'Error técnico al actualizar usuario (admin).'];
        }
        $res = $this->conn->query("SELECT @p_resultado AS resultado, @p_mensaje AS mensaje");
        $out_params = $res->fetch_assoc();
        $res->free();
        while($this->conn->more_results() && $this->conn->next_result()){;}
        return $out_params;
    }


    // --- Métodos para Configuración de Cuenta del Usuario ---

    public function getUsuarioParaConfiguracion($usu_id)
    {
        if (!$this->conn) {
            error_log("Error de conexión en getUsuarioParaConfiguracion: Conexión nula.");
            return null;
        }

        $usu_id_esc = $this->conn->real_escape_string($usu_id);
        $sql = "CALL sp_get_usuario_configuracion($usu_id_esc)";
        
        $resultado = $this->conn_obj->ejecutarSP($sql); 
        $usuario_data = null;

        if ($resultado && $resultado instanceof mysqli_result) {
            if ($resultado->num_rows > 0) {
                $usuario_data = $resultado->fetch_assoc();
            }
            $resultado->free();
            while($this->conn->more_results() && $this->conn->next_result()){
                if($rs = $this->conn->store_result()){ $rs->free(); }
            }
        } elseif ($resultado === false) {
            error_log("Error al ejecutar sp_get_usuario_configuracion para usu_id $usu_id_esc: " . ($this->conn->error ?? 'Error desconocido en la ejecución del SP. SQL: '.$sql));
        } else {
            error_log("sp_get_usuario_configuracion para usu_id $usu_id_esc no devolvió un resultado válido. SQL: ".$sql);
        }
        return $usuario_data;
    }

    public function actualizarPerfil($usu_id, $datos_perfil)
    {
        if (!$this->conn) return ['status' => 'error', 'message' => 'Error de conexión a la base de datos.'];

        $nombre = isset($datos_perfil['nombre']) ? trim($datos_perfil['nombre']) : '';
        $apellido = isset($datos_perfil['apellido']) ? trim($datos_perfil['apellido']) : '';
        $cedula = isset($datos_perfil['cedula']) ? trim($datos_perfil['cedula']) : '';
        $telefono = (isset($datos_perfil['telefono']) && trim($datos_perfil['telefono']) !== '') ? "'" . $this->conn->real_escape_string(trim($datos_perfil['telefono'])) . "'" : "NULL";
        $direccion = (isset($datos_perfil['direccion']) && trim($datos_perfil['direccion']) !== '') ? "'" . $this->conn->real_escape_string(trim($datos_perfil['direccion'])) . "'" : "NULL";
        $fnacimiento = (isset($datos_perfil['fnacimiento']) && !empty($datos_perfil['fnacimiento'])) ? "'" . $this->conn->real_escape_string($datos_perfil['fnacimiento']) . "'" : "NULL";
        
        $usu_id_esc = $this->conn->real_escape_string($usu_id);
        $nombre_esc = $this->conn->real_escape_string($nombre);
        $apellido_esc = $this->conn->real_escape_string($apellido);
        $cedula_esc = $this->conn->real_escape_string($cedula);

        $sql = "CALL sp_actualizar_perfil_usuario(
            $usu_id_esc, 
            '$nombre_esc', 
            '$apellido_esc', 
            '$cedula_esc', 
            $telefono, 
            $direccion, 
            $fnacimiento,
            @p_resultado, 
            @p_mensaje
        )";

        if (!$this->conn->query($sql)) {
            error_log("Error al llamar a sp_actualizar_perfil_usuario: " . $this->conn->error . " (SQL: $sql)");
            return ['status' => 'error', 'message' => 'Error técnico al actualizar el perfil (llamada SP).'];
        }

        $res_sp = $this->conn->query("SELECT @p_resultado AS resultado, @p_mensaje AS mensaje");
        if (!$res_sp) {
            error_log("Error obteniendo resultados de sp_actualizar_perfil_usuario: " . $this->conn->error);
            return ['status' => 'error', 'message' => 'Error técnico al obtener respuesta del SP de perfil.'];
        }
        $out_params = $res_sp->fetch_assoc();
        $res_sp->free();
        while($this->conn->more_results() && $this->conn->next_result()){
            if($rs = $this->conn->store_result()){ $rs->free(); }
        }

        if (isset($out_params['resultado'])) {
            if ($out_params['resultado'] == 1) {
                return ['status' => 'success', 'message' => $out_params['mensaje']];
            } elseif ($out_params['resultado'] == 0) {
                return ['status' => 'validation_error', 'message' => $out_params['mensaje']];
            } elseif ($out_params['resultado'] == 2) {
                return ['status' => 'duplicate_cedula', 'message' => $out_params['mensaje']];
            }
        }
        return ['status' => 'error', 'message' => $out_params['mensaje'] ?? 'No se pudo actualizar el perfil. Verifique los datos.'];
    }

    public function cambiarContrasena($usu_id, $pass_actual_plain, $pass_nueva_plain)
    {
        if (!$this->conn) return ['status' => 'error', 'message' => 'Error de conexión a la base de datos.'];

        $usu_id_esc = $this->conn->real_escape_string($usu_id);

        $sql_get_hash = "SELECT usu_password FROM Usuarios WHERE usu_id = $usu_id_esc";
        $res_hash = $this->conn->query($sql_get_hash);

        if (!$res_hash || $res_hash->num_rows === 0) {
            if($res_hash) $res_hash->free();
            return ['status' => 'error', 'message' => 'Usuario no encontrado o error al obtener datos de autenticación.'];
        }
        $usuario_actual = $res_hash->fetch_assoc();
        $res_hash->free();
        $hash_guardado = $usuario_actual['usu_password'];

        if (!password_verify($pass_actual_plain, $hash_guardado)) {
            return ['status' => 'auth_error', 'message' => 'La contraseña actual ingresada es incorrecta.'];
        }

        if (strlen(trim($pass_nueva_plain)) < 8) {
            return ['status' => 'validation_error', 'message' => 'La nueva contraseña debe tener al menos 8 caracteres.'];
        }

        $nuevo_hash = password_hash($pass_nueva_plain, PASSWORD_DEFAULT);
        if ($nuevo_hash === false) {
            error_log("Error al hashear la nueva contraseña para usu_id $usu_id_esc.");
            return ['status' => 'error', 'message' => 'Error interno del servidor al procesar la nueva contraseña.'];
        }

        $sql_sp_change = "CALL sp_cambiar_contrasena(
            $usu_id_esc,
            '$nuevo_hash',
            @p_resultado,
            @p_mensaje
        )";

        if (!$this->conn->query($sql_sp_change)) {
            error_log("SQL para sp_cambiar_contrasena: " . $sql_sp_change);
            error_log("Valores para sp_cambiar_contrasena: usu_id_esc='{$usu_id_esc}', nuevo_hash='{$nuevo_hash}'");
            error_log("Error al llamar a sp_cambiar_contrasena: " . $this->conn->error . " (SQL: $sql_sp_change)");
            return ['status' => 'error', 'message' => 'Error técnico al cambiar la contraseña (llamada SP).'];
        }

        $res_sp_change = $this->conn->query("SELECT @p_resultado AS resultado, @p_mensaje AS mensaje");
        if (!$res_sp_change) {
            error_log("Error obteniendo resultados de sp_cambiar_contrasena: " . $this->conn->error);
            return ['status' => 'error', 'message' => 'Error técnico al obtener respuesta del SP de contraseña.'];
        }
        $out_params_change = $res_sp_change->fetch_assoc();
        $res_sp_change->free();
        while($this->conn->more_results() && $this->conn->next_result()){
            if($rs = $this->conn->store_result()){ $rs->free(); }
        }

        if (isset($out_params_change['resultado'])) {
            if ($out_params_change['resultado'] == 1) {
                return ['status' => 'success', 'message' => $out_params_change['mensaje']];
            } elseif ($out_params_change['resultado'] == 2) {
                 return ['status' => 'validation_error', 'message' => $out_params_change['mensaje']];
            }
        }
        
        return ['status' => 'error', 'message' => $out_params_change['mensaje'] ?? 'No se pudo cambiar la contraseña.'];
    }

    public function __destruct()
    {
        // La conexión es manejada por el objeto Conexion, que se cierra cuando ya no se usa.
        // No es estrictamente necesario cerrar aquí si el objeto Conexion lo hace en su propio __destruct
        // o si los scripts son cortos.
        // if ($this->conn) {
        //     $this->conn->close();
        // }
    }
}
?>