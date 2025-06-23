<?php
require_once __DIR__ . "/../CONFIG/Conexion.php";

class Catalogos
{
    private $conexion_obj;
    private $conn;

    public function __construct()
    {
        try {
            $this->conexion_obj = new Conexion();
            $this->conn = $this->conexion_obj->conecta();
        } catch (Exception $e) {
            error_log("Error de conexión en Catalogos_M constructor: " . $e->getMessage());
            $this->conn = null; 
        }
    }

    public function getMarcas()
    {
        if (!$this->conn) return false;
        $sql = "CALL sp_get_marcas_activas()";
        $resultado = $this->conexion_obj->ejecutarSP($sql);
        $marcas = [];
        if ($resultado && $resultado instanceof mysqli_result) {
            if ($resultado->num_rows > 0) { while ($fila = $resultado->fetch_assoc()) { $marcas[] = $fila; } }
            $resultado->free();
        } elseif ($resultado === false) { return false; }
        return $marcas;
    }

    public function getModelosPorMarca($marca_id)
    {
        if (!$this->conn) return false;
        $marca_id_esc = $this->conn->real_escape_string($marca_id);
        $sql = "CALL sp_get_modelos_por_marca($marca_id_esc)";
        $resultado = $this->conexion_obj->ejecutarSP($sql);
        $modelos = [];
        if ($resultado && $resultado instanceof mysqli_result) {
            if ($resultado->num_rows > 0) { while ($fila = $resultado->fetch_assoc()) { $modelos[] = $fila; } }
            $resultado->free();
        } elseif ($resultado === false) { return false; }
        return $modelos;
    }

    public function getTiposVehiculo()
    {
        if (!$this->conn) return false;
        $sql = "CALL sp_get_tipos_vehiculo_activos()";
        $resultado = $this->conexion_obj->ejecutarSP($sql);
        $tipos = [];
        if ($resultado && $resultado instanceof mysqli_result) {
            if ($resultado->num_rows > 0) { while ($fila = $resultado->fetch_assoc()) { $tipos[] = $fila; } }
            $resultado->free();
        } elseif ($resultado === false) { return false; }
        return $tipos;
    }

    public function adminGetAllMarcas()
    {
        if (!$this->conn) return false;
        $sql = "CALL sp_admin_get_all_marcas()";
        $resultado = $this->conexion_obj->ejecutarSP($sql);
        $marcas = [];
        if ($resultado && $resultado instanceof mysqli_result) {
            if ($resultado->num_rows > 0) { while ($fila = $resultado->fetch_assoc()) { $marcas[] = $fila; } }
            $resultado->free();
        } elseif ($resultado === false) { return false; }
        return $marcas;
    }

    public function adminInsertarMarca($nombre, $logo_url = null)
    {
        if (!$this->conn) return ['resultado' => 0, 'mensaje' => 'Error de conexión.'];
        $nombre_esc = $this->conn->real_escape_string($nombre);
        $logo_url_esc = $logo_url ? "'" . $this->conn->real_escape_string($logo_url) . "'" : "NULL";
        
        $sql = "CALL sp_admin_insertar_marca('$nombre_esc', $logo_url_esc, @p_mar_id, @p_resultado, @p_mensaje)";
        if (!$this->conn->query($sql)) {
            error_log("Error al llamar a sp_admin_insertar_marca: " . $this->conn->error);
            return ['resultado' => 0, 'mensaje' => 'Error técnico al insertar marca (SP Call).'];
        }
        $res = $this->conn->query("SELECT @p_mar_id AS mar_id, @p_resultado AS resultado, @p_mensaje AS mensaje");
        if (!$res) return ['resultado' => 0, 'mensaje' => 'Error obteniendo resultado de SP.'];
        $out = $res->fetch_assoc(); $res->free();
        while($this->conn->more_results() && $this->conn->next_result()){;}
        return $out;
    }

    public function adminActualizarMarca($id, $nombre, $logo_url = null)
    {
        if (!$this->conn) return ['resultado' => 0, 'mensaje' => 'Error de conexión.'];
        $id_esc = $this->conn->real_escape_string($id);
        $nombre_esc = $this->conn->real_escape_string($nombre);
        $logo_url_esc = $logo_url ? "'" . $this->conn->real_escape_string($logo_url) . "'" : "NULL";

        $sql = "CALL sp_admin_actualizar_marca($id_esc, '$nombre_esc', $logo_url_esc, @p_resultado, @p_mensaje)";
        if (!$this->conn->query($sql)) {
            error_log("Error al llamar a sp_admin_actualizar_marca: " . $this->conn->error . " (SQL: " . $sql . ")");
            return ['resultado' => 0, 'mensaje' => 'Error técnico al actualizar marca (SP Call).'];
        }
        $res = $this->conn->query("SELECT @p_resultado AS resultado, @p_mensaje AS mensaje");
        if (!$res) {
             error_log("Error al obtener parámetros OUT de sp_admin_actualizar_marca: " . $this->conn->error);
            return ['resultado' => 0, 'mensaje' => 'Error obteniendo resultado de SP para actualizar marca.'];
        }
        $out = $res->fetch_assoc(); $res->free();
        while($this->conn->more_results() && $this->conn->next_result()){;}
        return $out;
    }

    public function adminEliminarMarca($id)
    {
        if (!$this->conn) return ['resultado' => 0, 'mensaje' => 'Error de conexión.'];
        $id_esc = $this->conn->real_escape_string($id);
        $sql = "CALL sp_admin_eliminar_marca($id_esc, @p_resultado, @p_mensaje)";
        if (!$this->conn->query($sql)) {
            error_log("Error al llamar a sp_admin_eliminar_marca: " . $this->conn->error);
            return ['resultado' => 0, 'mensaje' => 'Error técnico al eliminar marca (SP Call).'];
        }
        $res = $this->conn->query("SELECT @p_resultado AS resultado, @p_mensaje AS mensaje");
        if (!$res) return ['resultado' => 0, 'mensaje' => 'Error obteniendo resultado de SP.'];
        $out = $res->fetch_assoc(); $res->free();
        while($this->conn->more_results() && $this->conn->next_result()){;}
        return $out;
    }

    public function adminGetModelosPorMarca($marca_id)
    {
        if (!$this->conn) return false;
        $marca_id_esc = $this->conn->real_escape_string($marca_id);
        $sql = "CALL sp_admin_get_modelos_por_marca($marca_id_esc)";
        $resultado = $this->conexion_obj->ejecutarSP($sql);
        $modelos = [];
        if ($resultado && $resultado instanceof mysqli_result) {
            if ($resultado->num_rows > 0) { while ($fila = $resultado->fetch_assoc()) { $modelos[] = $fila; } }
            $resultado->free();
        } elseif ($resultado === false) { return false; }
        return $modelos;
    }

    public function adminInsertarModelo($marca_id, $nombre_modelo)
    {
        if (!$this->conn) return ['resultado' => 0, 'mensaje' => 'Error de conexión.'];
        $marca_id_esc = $this->conn->real_escape_string($marca_id);
        $nombre_modelo_esc = $this->conn->real_escape_string($nombre_modelo);
        
        $sql = "CALL sp_admin_insertar_modelo($marca_id_esc, '$nombre_modelo_esc', @p_mod_id, @p_resultado, @p_mensaje)";
        if (!$this->conn->query($sql)) {
            error_log("Error al llamar a sp_admin_insertar_modelo: " . $this->conn->error);
            return ['resultado' => 0, 'mensaje' => 'Error técnico al insertar modelo (SP Call).'];
        }
        $res = $this->conn->query("SELECT @p_mod_id AS mod_id, @p_resultado AS resultado, @p_mensaje AS mensaje");
        if (!$res) return ['resultado' => 0, 'mensaje' => 'Error obteniendo resultado de SP.'];
        $out = $res->fetch_assoc(); $res->free();
        while($this->conn->more_results() && $this->conn->next_result()){;}
        return $out;
    }

    public function adminActualizarModelo($modelo_id, $marca_id, $nombre_modelo)
    {
        if (!$this->conn) return ['resultado' => 0, 'mensaje' => 'Error de conexión.'];
        $modelo_id_esc = $this->conn->real_escape_string($modelo_id);
        $marca_id_esc = $this->conn->real_escape_string($marca_id);
        $nombre_modelo_esc = $this->conn->real_escape_string($nombre_modelo);

        $sql = "CALL sp_admin_actualizar_modelo($modelo_id_esc, $marca_id_esc, '$nombre_modelo_esc', @p_resultado, @p_mensaje)";
        if (!$this->conn->query($sql)) {
            error_log("Error al llamar a sp_admin_actualizar_modelo: " . $this->conn->error);
            return ['resultado' => 0, 'mensaje' => 'Error técnico al actualizar modelo (SP Call).'];
        }
        $res = $this->conn->query("SELECT @p_resultado AS resultado, @p_mensaje AS mensaje");
        if (!$res) return ['resultado' => 0, 'mensaje' => 'Error obteniendo resultado de SP.'];
        $out = $res->fetch_assoc(); $res->free();
        while($this->conn->more_results() && $this->conn->next_result()){;}
        return $out;
    }

    public function adminEliminarModelo($modelo_id)
    {
        if (!$this->conn) return ['resultado' => 0, 'mensaje' => 'Error de conexión.'];
        $modelo_id_esc = $this->conn->real_escape_string($modelo_id);
        $sql = "CALL sp_admin_eliminar_modelo($modelo_id_esc, @p_resultado, @p_mensaje)";
        if (!$this->conn->query($sql)) {
            error_log("Error al llamar a sp_admin_eliminar_modelo: " . $this->conn->error);
            return ['resultado' => 0, 'mensaje' => 'Error técnico al eliminar modelo (SP Call).'];
        }
        $res = $this->conn->query("SELECT @p_resultado AS resultado, @p_mensaje AS mensaje");
        if (!$res) return ['resultado' => 0, 'mensaje' => 'Error obteniendo resultado de SP.'];
        $out = $res->fetch_assoc(); $res->free();
        while($this->conn->more_results() && $this->conn->next_result()){;}
        return $out;
    }

    // --- NUEVOS MÉTODOS PARA ADMINISTRACIÓN DE TIPOS DE VEHÍCULO ---
    public function adminGetAllTiposVehiculo()
    {
        if (!$this->conn) return false;
        $sql = "CALL sp_admin_get_all_tipos_vehiculo()";
        $resultado = $this->conexion_obj->ejecutarSP($sql);
        $tipos = [];
        if ($resultado && $resultado instanceof mysqli_result) {
            if ($resultado->num_rows > 0) { while ($fila = $resultado->fetch_assoc()) { $tipos[] = $fila; } }
            $resultado->free();
        } elseif ($resultado === false) { return false; }
        return $tipos;
    }

    public function adminInsertarTipoVehiculo($nombre, $descripcion = null, $icono_url = null, $activo = true)
    {
        if (!$this->conn) return ['resultado' => 0, 'mensaje' => 'Error de conexión.'];
        $nombre_esc = $this->conn->real_escape_string($nombre);
        $descripcion_esc = $descripcion ? "'" . $this->conn->real_escape_string($descripcion) . "'" : "NULL";
        $icono_url_esc = $icono_url ? "'" . $this->conn->real_escape_string($icono_url) . "'" : "NULL";
        $activo_esc = (bool)$activo ? 'TRUE' : 'FALSE';
        
        $sql = "CALL sp_admin_insertar_tipo_vehiculo('$nombre_esc', $descripcion_esc, $icono_url_esc, $activo_esc, @p_tiv_id, @p_resultado, @p_mensaje)";
        if (!$this->conn->query($sql)) {
            error_log("Error al llamar a sp_admin_insertar_tipo_vehiculo: " . $this->conn->error . " SQL: " . $sql);
            return ['resultado' => 0, 'mensaje' => 'Error técnico al insertar tipo de vehículo (SP Call).'];
        }
        $res = $this->conn->query("SELECT @p_tiv_id AS tiv_id, @p_resultado AS resultado, @p_mensaje AS mensaje");
        if (!$res) return ['resultado' => 0, 'mensaje' => 'Error obteniendo resultado de SP.'];
        $out = $res->fetch_assoc(); $res->free();
        while($this->conn->more_results() && $this->conn->next_result()){;}
        return $out;
    }

    public function adminActualizarTipoVehiculo($id, $nombre, $descripcion = null, $icono_url = null, $activo = true)
    {
        if (!$this->conn) return ['resultado' => 0, 'mensaje' => 'Error de conexión.'];
        $id_esc = $this->conn->real_escape_string($id);
        $nombre_esc = $this->conn->real_escape_string($nombre);
        $descripcion_esc = $descripcion ? "'" . $this->conn->real_escape_string($descripcion) . "'" : "NULL";
        $icono_url_esc = $icono_url ? "'" . $this->conn->real_escape_string($icono_url) . "'" : "NULL";
        $activo_esc = (bool)$activo ? 'TRUE' : 'FALSE';

        $sql = "CALL sp_admin_actualizar_tipo_vehiculo($id_esc, '$nombre_esc', $descripcion_esc, $icono_url_esc, $activo_esc, @p_resultado, @p_mensaje)";
        if (!$this->conn->query($sql)) {
            error_log("Error al llamar a sp_admin_actualizar_tipo_vehiculo: " . $this->conn->error . " SQL: " . $sql);
            return ['resultado' => 0, 'mensaje' => 'Error técnico al actualizar tipo de vehículo (SP Call).'];
        }
        $res = $this->conn->query("SELECT @p_resultado AS resultado, @p_mensaje AS mensaje");
        if (!$res) return ['resultado' => 0, 'mensaje' => 'Error obteniendo resultado de SP.'];
        $out = $res->fetch_assoc(); $res->free();
        while($this->conn->more_results() && $this->conn->next_result()){;}
        return $out;
    }

    public function adminEliminarTipoVehiculo($id)
    {
        if (!$this->conn) return ['resultado' => 0, 'mensaje' => 'Error de conexión.'];
        $id_esc = $this->conn->real_escape_string($id);
        $sql = "CALL sp_admin_eliminar_tipo_vehiculo($id_esc, @p_resultado, @p_mensaje)";
        if (!$this->conn->query($sql)) {
            error_log("Error al llamar a sp_admin_eliminar_tipo_vehiculo: " . $this->conn->error);
            return ['resultado' => 0, 'mensaje' => 'Error técnico al eliminar tipo de vehículo (SP Call).'];
        }
        $res = $this->conn->query("SELECT @p_resultado AS resultado, @p_mensaje AS mensaje");
        if (!$res) return ['resultado' => 0, 'mensaje' => 'Error obteniendo resultado de SP.'];
        $out = $res->fetch_assoc(); $res->free();
        while($this->conn->more_results() && $this->conn->next_result()){;}
        return $out;
    }
}
?>