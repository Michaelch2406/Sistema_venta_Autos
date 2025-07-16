<?php
// MODELOS/ventas_m.php
ini_set('display_errors', 0); 
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', './../php_error.log'); 

class VentaModelo {

    private $mysqli;

    public function __construct($db_conn_obj_mysqli) {
        if ($db_conn_obj_mysqli instanceof MySQLi) {
            $this->mysqli = $db_conn_obj_mysqli;
        } else {
            error_log("VentaModelo: Se esperaba un objeto MySQLi.");
            throw new Exception("Error de configuración interna del modelo.");
        }
    }

    public function obtener_ventas_por_usuario($usu_id) {
        $usu_id_san = (int)$usu_id;
        $sql = "
            SELECT 
                v.vnt_id,
                v.vnt_fecha_venta,
                v.vnt_precio_final,
                CONCAT(veh.veh_marca, ' ', veh.veh_modelo) AS vehiculo_nombre,
                CONCAT(comp.usu_nombre, ' ', comp.usu_apellido) AS comprador_nombre,
                CONCAT(vend.usu_nombre, ' ', vend.usu_apellido) AS vendedor_nombre
            FROM ventas v
            JOIN vehiculos veh ON v.vehiculo_id = veh.veh_id
            JOIN usuarios comp ON v.comprador_id = comp.usu_id
            JOIN usuarios vend ON v.vendedor_id = vend.usu_id
            WHERE v.comprador_id = {$usu_id_san} OR v.vendedor_id = {$usu_id_san}
            ORDER BY v.vnt_fecha_venta DESC
        ";
        
        $result = $this->mysqli->query($sql);
        if (!$result) {
            error_log("Error en VentaModelo al obtener ventas: " . $this->mysqli->error);
            return [];
        }

        $ventas = [];
        while ($fila = $result->fetch_assoc()) {
            $ventas[] = $fila;
        }
        $result->free();
        return $ventas;
    }
}
?>
