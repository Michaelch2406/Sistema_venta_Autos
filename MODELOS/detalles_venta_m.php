<?php
// MODELOS/detalles_venta_m.php
ini_set('display_errors', 0); 
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', './../php_error.log'); 

class DetalleVentaModelo {

    private $mysqli;

    public function __construct($db_conn_obj_mysqli) {
        if ($db_conn_obj_mysqli instanceof MySQLi) {
            $this->mysqli = $db_conn_obj_mysqli;
        } else {
            error_log("DetalleVentaModelo: Se esperaba un objeto MySQLi.");
            throw new Exception("Error de configuración interna del modelo.");
        }
    }

    public function obtener_detalle_venta($vnt_id) {
        $vnt_id_san = (int)$vnt_id;
        $sql = "CALL sp_obtener_detalle_venta({$vnt_id_san})";
        
        $result = $this->mysqli->query($sql);
        if (!$result) {
            error_log("Error en DetalleVentaModelo al obtener detalle de venta: " . $this->mysqli->error);
            return false;
        }

        $detalle_venta = $result->fetch_assoc();
        $result->free();
        
        // Limpiar cualquier resultado múltiple
        while($this->mysqli->more_results() && $this->mysqli->next_result()) {
            if($res = $this->mysqli->store_result()) {
                $res->free();
            }
        }
        
        return $detalle_venta;
    }
}
?>
