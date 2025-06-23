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