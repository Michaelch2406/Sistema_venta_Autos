<?php
// MODELOS/facturas_m.php
ini_set('display_errors', 0); 
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', './../php_error.log'); 

class FacturaModelo {

    private $mysqli;

    public function __construct($db_conn_obj_mysqli) {
        if ($db_conn_obj_mysqli instanceof MySQLi) {
            $this->mysqli = $db_conn_obj_mysqli;
        } else {
            error_log("FacturaModelo: Se esperaba un objeto MySQLi.");
            throw new Exception("Error de configuración interna del modelo.");
        }
    }

    /**
     * Obtiene los datos completos de una factura por ID de venta
     */
    public function obtener_datos_factura($vnt_id) {
        $vnt_id_san = (int)$vnt_id;
        $sql = "CALL sp_obtener_datos_factura({$vnt_id_san})";
        
        // Limpiar resultados previos
        while($this->mysqli->more_results() && $this->mysqli->next_result()){ 
            if($res = $this->mysqli->store_result()){ $res->free(); } 
        }
        
        $query_result = $this->mysqli->query($sql);
        if (!$query_result) {
            error_log("Error en FacturaModelo al obtener datos de factura: " . $this->mysqli->error . " (SQL: " . $sql . ")");
            return false;
        }
        
        $resultado = false;
        if ($query_result instanceof mysqli_result) { 
            $resultado = $query_result->fetch_assoc(); 
            $query_result->free(); 
        }
        
        // Limpiar resultados posteriores
        while($this->mysqli->more_results() && $this->mysqli->next_result()){ 
            if($res = $this->mysqli->store_result()){ $res->free(); } 
        }
        
        return $resultado;
    }

    /**
     * Verifica si el usuario tiene acceso a una factura específica
     */
    public function verificar_acceso_factura($vnt_id, $usu_id, $rol_id) {
        $vnt_id_san = (int)$vnt_id;
        $usu_id_san = (int)$usu_id;
        
        // Los administradores pueden ver cualquier factura
        if ($rol_id == 3) {
            return true;
        }
        
        // Los usuarios solo pueden ver sus propias facturas (como comprador o vendedor)
        $sql = "SELECT COUNT(*) as tiene_acceso FROM ventas 
                WHERE vnt_id = {$vnt_id_san} 
                AND (comprador_id = {$usu_id_san} OR vendedor_id = {$usu_id_san})";
        
        $result = $this->mysqli->query($sql);
        if (!$result) {
            error_log("Error al verificar acceso a factura: " . $this->mysqli->error);
            return false;
        }
        
        $row = $result->fetch_assoc();
        $result->free();
        
        return $row['tiene_acceso'] > 0;
    }
}
?>