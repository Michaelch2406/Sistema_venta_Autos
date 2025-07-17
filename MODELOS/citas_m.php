<?php
// MODELOS/citas_m.php
ini_set('display_errors', 0); 
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', './../php_error.log'); 

class CitaModelo {

    private $mysqli;

    public function __construct($db_conn_obj_mysqli) {
        if ($db_conn_obj_mysqli instanceof MySQLi) {
            $this->mysqli = $db_conn_obj_mysqli;
        } else {
            error_log("CitaModelo: Se esperaba un objeto MySQLi.");
            throw new Exception("Error de configuración interna del modelo.");
        }
    }

    // Método NUEVO para consultar horas ocupadas
    public function obtener_horas_ocupadas($veh_id, $fecha) {
        $veh_id_san = (int)$veh_id;
        $fecha_san = "'" . $this->mysqli->real_escape_string($fecha) . "'";
        $sql = "CALL sp_obtener_disponibilidad_cita({$veh_id_san}, {$fecha_san})";
        
        // Usamos el método que devuelve todos los resultados
        $resultados_raw = $this->_ejecutar_sp_y_obtener_todos($sql);

        // Devolvemos solo un array simple de horas
        $horas_ocupadas = [];
        foreach($resultados_raw as $row) {
            $horas_ocupadas[] = $row['cit_hora_disponibilidad'];
        }
        return $horas_ocupadas;
    }

    private function _ejecutar_sp_y_obtener_todos($sql_call) {
        while($this->mysqli->more_results() && $this->mysqli->next_result()){ if($res = $this->mysqli->store_result()){ $res->free(); } }
        $query_result = $this->mysqli->query($sql_call);
        if (!$query_result) {
            error_log("Error en CitaModelo al ejecutar SP: " . $this->mysqli->error . " (SQL: " . $sql_call . ")");
            return [];
        }
        $resultados = [];
        if ($query_result instanceof mysqli_result) { while ($fila = $query_result->fetch_assoc()) { $resultados[] = $fila; } $query_result->free(); }
        while($this->mysqli->more_results() && $this->mysqli->next_result()){ if($res = $this->mysqli->store_result()){ $res->free(); } }
        return $resultados;
    }

    private function _ejecutar_sp_y_obtener_uno($sql_call) {
        while($this->mysqli->more_results() && $this->mysqli->next_result()){ if($res = $this->mysqli->store_result()){ $res->free(); } }
        $query_result = $this->mysqli->query($sql_call);
        if (!$query_result) {
            error_log("Error en CitaModelo al ejecutar SP (fetch one): " . $this->mysqli->error . " (SQL: " . $sql_call . ")");
            return false;
        }
        $resultado = false;
        if ($query_result instanceof mysqli_result) { $resultado = $query_result->fetch_assoc(); $query_result->free(); }
        while($this->mysqli->more_results() && $this->mysqli->next_result()){ if($res = $this->mysqli->store_result()){ $res->free(); } }
        return $resultado;
    }

    private function _ejecutar_sp_afecta_filas($sql_call) {
        // Limpiar resultados previos
        while($this->mysqli->more_results() && $this->mysqli->next_result()){ 
            if($res = $this->mysqli->store_result()){ $res->free(); } 
        }
        
        if ($this->mysqli->query($sql_call)) {
            $res = $this->mysqli->store_result();
            if($res){ 
                $row = $res->fetch_assoc(); 
                $res->free(); 
                
                // Limpiar resultados posteriores
                while($this->mysqli->more_results() && $this->mysqli->next_result()){ 
                    if($res = $this->mysqli->store_result()){ $res->free(); } 
                }
                
                $filas_afectadas = isset($row['filas_afectadas']) ? (int)$row['filas_afectadas'] : 0;
                error_log("SP ejecutado. Filas afectadas: " . $filas_afectadas . " (SQL: " . $sql_call . ")");
                return $filas_afectadas > 0;
            }
            return false;
        } else {
            error_log("Error en CitaModelo al ejecutar SP (afecta filas): " . $this->mysqli->error . " (SQL: " . $sql_call . ")");
            return false;
        }
    }

    public function obtener_citas_por_usuario($usu_id) {
        $usu_id_san = (int)$usu_id;
        $sql = "CALL sp_obtener_citas_usuario({$usu_id_san})";
        return $this->_ejecutar_sp_y_obtener_todos($sql);
    }

    public function obtener_todas_las_citas($filtro_texto, $filtro_estado, $filtro_fecha_desde, $filtro_fecha_hasta) {
        $ft = $filtro_texto ? "'" . $this->mysqli->real_escape_string($filtro_texto) . "'" : "NULL";
        $fe = $filtro_estado ? "'" . $this->mysqli->real_escape_string($filtro_estado) . "'" : "NULL";
        $ffd = $filtro_fecha_desde ? "'" . $this->mysqli->real_escape_string($filtro_fecha_desde) . "'" : "NULL";
        $ffh = $filtro_fecha_hasta ? "'" . $this->mysqli->real_escape_string($filtro_fecha_hasta) . "'" : "NULL";
        $sql = "CALL sp_obtener_todas_las_citas({$ft}, {$fe}, {$ffd}, {$ffh})";
        return $this->_ejecutar_sp_y_obtener_todos($sql);
    }

    public function obtener_citas_por_vehiculos_propios($usu_id, $filtro_texto, $filtro_estado, $filtro_fecha_desde, $filtro_fecha_hasta) {
        $usu_id_san = (int)$usu_id;
        $ft = $filtro_texto ? "'" . $this->mysqli->real_escape_string($filtro_texto) . "'" : "NULL";
        $fe = $filtro_estado ? "'" . $this->mysqli->real_escape_string($filtro_estado) . "'" : "NULL";
        $ffd = $filtro_fecha_desde ? "'" . $this->mysqli->real_escape_string($filtro_fecha_desde) . "'" : "NULL";
        $ffh = $filtro_fecha_hasta ? "'" . $this->mysqli->real_escape_string($filtro_fecha_hasta) . "'" : "NULL";
        $sql = "CALL sp_obtener_citas_vehiculos_propios({$usu_id_san}, {$ft}, {$fe}, {$ffd}, {$ffh})";
        return $this->_ejecutar_sp_y_obtener_todos($sql);
    }

    public function obtener_detalle_cita($cit_id) {
        $cit_id_san = (int)$cit_id;
        $sql = "CALL sp_obtener_detalle_cita({$cit_id_san})";
        return $this->_ejecutar_sp_y_obtener_uno($sql);
    }

    public function actualizar_estado_cita($cit_id, $nuevo_estado, $actor_id) {
        $cit_id_san = (int)$cit_id;
        $ne = "'" . $this->mysqli->real_escape_string($nuevo_estado) . "'";
        $act_id = (int)$actor_id;
        $sql = "CALL sp_actualizar_estado_cita({$cit_id_san}, {$ne}, {$act_id})";
        return $this->_ejecutar_sp_afecta_filas($sql);
    }

    public function guardar_notas_admin_cita($cit_id, $notas, $admin_id) {
        $cit_id_san = (int)$cit_id;
        $n = "'" . $this->mysqli->real_escape_string($notas) . "'";
        $adm_id = (int)$admin_id;
        $sql = "CALL sp_guardar_notas_admin_cita({$cit_id_san}, {$n}, {$adm_id})";
        return $this->_ejecutar_sp_afecta_filas($sql);
    }
}
?>