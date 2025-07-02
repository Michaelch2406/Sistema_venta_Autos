<?php
// MODELOS/cotizaciones_m.php

class CotizacionModelo {

    private $conexionObj; // Objeto de tu clase Conexion
    private $mysqli;      // Objeto mysqli

    public function __construct($db_conn_obj_mysqli) { // Recibe el objeto mysqli directamente
        if ($db_conn_obj_mysqli instanceof MySQLi) {
            $this->mysqli = $db_conn_obj_mysqli;
        } else {
            // Si se pasa el objeto Conexion en lugar del mysqli directamente
            // podrías hacer $this->conexionObj = $db_conn_obj_mysqli; $this->mysqli = $this->conexionObj->conecta();
            // Pero es más simple si los archivos de vista/AJAX ya instancian Conexion y pasan el mysqli
            error_log("CotizacionModelo: Se esperaba un objeto MySQLi.");
            throw new Exception("Error de configuración interna del modelo.");
        }
    }

    // Método auxiliar para ejecutar SPs y obtener resultados (adaptado a tu clase Conexion)
    private function _ejecutar_sp_y_obtener_todos($sql_call) {
        // Tu clase Conexion ya maneja la ejecución del SP.
        // Necesitamos una forma de pasar la conexión mysqli a un nuevo objeto Conexion
        // o, idealmente, que la clase Conexion sea inyectada o accesible globalmente.
        // Por ahora, asumiré que la conexión mysqli ya está establecida en $this->mysqli.

        // Limpiar resultados anteriores, IMPORTANTE para mysqli con SPs
        while($this->mysqli->more_results() && $this->mysqli->next_result()){
            if($res = $this->mysqli->store_result()){
                $res->free();
            }
        }

        $query_result = $this->mysqli->query($sql_call);
        if (!$query_result) {
            error_log("Error en CotizacionModelo al ejecutar SP: " . $this->mysqli->error . " (SQL: " . $sql_call . ")");
            return [];
        }
        
        $resultados = [];
        if ($query_result instanceof mysqli_result) {
            while ($fila = $query_result->fetch_assoc()) {
                $resultados[] = $fila;
            }
            $query_result->free();
        }
        // Limpiar nuevamente por si el SP tiene múltiples result sets o un output de ROW_COUNT()
        while($this->mysqli->more_results() && $this->mysqli->next_result()){
            if($res = $this->mysqli->store_result()){
                $res->free();
            }
        }
        return $resultados;
    }

    private function _ejecutar_sp_y_obtener_uno($sql_call) {
        while($this->mysqli->more_results() && $this->mysqli->next_result()){ if($res = $this->mysqli->store_result()){ $res->free(); } }
        $query_result = $this->mysqli->query($sql_call);
        if (!$query_result) {
            error_log("Error en CotizacionModelo al ejecutar SP (fetch one): " . $this->mysqli->error . " (SQL: " . $sql_call . ")");
            return false;
        }
        $resultado = false;
        if ($query_result instanceof mysqli_result) {
            $resultado = $query_result->fetch_assoc();
            $query_result->free();
        }
        while($this->mysqli->more_results() && $this->mysqli->next_result()){ if($res = $this->mysqli->store_result()){ $res->free(); } }
        return $resultado;
    }

    private function _ejecutar_sp_afecta_filas($sql_call) {
        while($this->mysqli->more_results() && $this->mysqli->next_result()){ if($res = $this->mysqli->store_result()){ $res->free(); } }
        if ($this->mysqli->query($sql_call)) {
            // Para obtener el resultado del SELECT ROW_COUNT() que añadimos a los SPs de update
            $result_row_count = $this->mysqli->query("SELECT @filas_afectadas AS filas_afectadas"); // Asumiendo que el SP setea una variable de sesión o devuelve esto
            // Si el SP devuelve directamente el resultado de ROW_COUNT() como un SELECT, entonces:
            // $result_row_count = $this->mysqli->store_result();
            // $fila_afectada = $result_row_count->fetch_assoc();
            // $result_row_count->free();
            // return ($fila_afectada && isset($fila_afectada['filas_afectadas']) && $fila_afectada['filas_afectadas'] > 0);
            // Por ahora, simplificamos: si la query no da error, asumimos que pudo o no afectar filas.
            // Los SPs sp_actualizar_estado_cotizacion y sp_guardar_notas_admin_cotizacion fueron actualizados para DEVOLVER filas_afectadas.
            $res = $this->mysqli->store_result(); // Capturar el resultset del SELECT ROW_COUNT()
            if($res){
                $row = $res->fetch_assoc();
                $res->free();
                return (isset($row['filas_afectadas']) && $row['filas_afectadas'] >= 0); // >=0 porque guardar la misma nota es éxito
            }
            return false; // No se pudo obtener el ROW_COUNT()
        } else {
            error_log("Error en CotizacionModelo al ejecutar SP (afecta filas): " . $this->mysqli->error . " (SQL: " . $sql_call . ")");
            return false;
        }
    }

    public function obtener_cotizaciones_por_usuario($usu_id) {
        $usu_id_san = (int)$usu_id;
        $sql = "CALL sp_obtener_cotizaciones_usuario({$usu_id_san})";
        return $this->_ejecutar_sp_y_obtener_todos($sql);
    }

    public function obtener_todas_las_cotizaciones($filtro_texto, $filtro_estado, $filtro_fecha_desde, $filtro_fecha_hasta) {
        $ft = $filtro_texto ? "'" . $this->mysqli->real_escape_string($filtro_texto) . "'" : "NULL";
        $fe = $filtro_estado ? "'" . $this->mysqli->real_escape_string($filtro_estado) . "'" : "NULL";
        $ffd = $filtro_fecha_desde ? "'" . $this->mysqli->real_escape_string($filtro_fecha_desde) . "'" : "NULL";
        $ffh = $filtro_fecha_hasta ? "'" . $this->mysqli->real_escape_string($filtro_fecha_hasta) . "'" : "NULL";
        
        $sql = "CALL sp_obtener_todas_las_cotizaciones({$ft}, {$fe}, {$ffd}, {$ffh})";
        return $this->_ejecutar_sp_y_obtener_todos($sql);
    }

    public function obtener_detalle_cotizacion($cot_id) {
        $cot_id_san = (int)$cot_id;
        $sql = "CALL sp_obtener_detalle_cotizacion({$cot_id_san})";
        return $this->_ejecutar_sp_y_obtener_uno($sql);
    }

    public function actualizar_estado_cotizacion($cot_id, $nuevo_estado, $actor_id) {
        $cot_id_san = (int)$cot_id;
        $ne = "'" . $this->mysqli->real_escape_string($nuevo_estado) . "'";
        $act_id = (int)$actor_id;
        $sql = "CALL sp_actualizar_estado_cotizacion({$cot_id_san}, {$ne}, {$act_id})";
        return $this->_ejecutar_sp_afecta_filas($sql);
    }

    public function guardar_notas_admin_cotizacion($cot_id, $notas, $admin_id) {
        $cot_id_san = (int)$cot_id;
        $n = "'" . $this->mysqli->real_escape_string($notas) . "'";
        $adm_id = (int)$admin_id;
        $sql = "CALL sp_guardar_notas_admin_cotizacion({$cot_id_san}, {$n}, {$adm_id})";
        return $this->_ejecutar_sp_afecta_filas($sql);
    }
}
?>