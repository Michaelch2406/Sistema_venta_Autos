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
        
        // Usar consulta directa en lugar del stored procedure problemático
        $sql = "
            SELECT 
                v.vnt_id,
                v.vnt_fecha_venta,
                v.vnt_precio_final,
                v.comprador_id,
                v.vendedor_id,
                CONCAT(m.mar_nombre, ' ', mo.mod_nombre) AS vehiculo_nombre,
                CONCAT(comp.usu_nombre, ' ', comp.usu_apellido) AS comprador_nombre,
                CONCAT(vend.usu_nombre, ' ', vend.usu_apellido) AS vendedor_nombre,
                COALESCE(dv.dv_codigo_factura, 'N/A') AS dv_codigo_factura
            FROM ventas v
            JOIN vehiculos veh ON v.vehiculo_id = veh.veh_id
            JOIN marcas m ON veh.mar_id = m.mar_id
            JOIN modelos mo ON veh.mod_id = mo.mod_id
            JOIN usuarios comp ON v.comprador_id = comp.usu_id
            JOIN usuarios vend ON v.vendedor_id = vend.usu_id
            LEFT JOIN detalles_venta dv ON v.vnt_id = dv.vnt_id
            WHERE v.vnt_id = {$vnt_id_san}
        ";
        
        $result = $this->mysqli->query($sql);
        if (!$result) {
            error_log("Error en DetalleVentaModelo al obtener detalle de venta: " . $this->mysqli->error . " (SQL: " . $sql . ")");
            return false;
        }

        $detalle_venta = $result->fetch_assoc();
        $result->free();
        
        return $detalle_venta;
    }

    /**
     * Genera el código de factura y crea el detalle de venta
     * Esta función reemplaza el trigger MySQL
     */
    public function generar_detalle_venta($vnt_id) {
        $vnt_id_san = (int)$vnt_id;
        
        // Iniciar transacción
        $this->mysqli->begin_transaction();
        
        try {
            // Verificar si ya existe un detalle de venta para esta venta
            $check_sql = "SELECT dv_id FROM detalles_venta WHERE vnt_id = {$vnt_id_san}";
            $check_result = $this->mysqli->query($check_sql);
            
            if ($check_result && $check_result->num_rows > 0) {
                // Ya existe un detalle de venta, no hacer nada
                $check_result->free();
                $this->mysqli->commit();
                return true;
            }
            
            if ($check_result) {
                $check_result->free();
            }
            
            // Obtener el siguiente número secuencial
            $contador_sql = "SELECT IFNULL(MAX(dv_id), 0) + 1 as contador FROM detalles_venta";
            $contador_result = $this->mysqli->query($contador_sql);
            
            if (!$contador_result) {
                throw new Exception("Error al obtener contador: " . $this->mysqli->error);
            }
            
            $contador_row = $contador_result->fetch_assoc();
            $contador = $contador_row['contador'];
            $contador_result->free();
            
            // Generar código con formato AT-YYYYMM-000001
            $fecha_actual = date('Ym');
            $codigo_factura = "AT-{$fecha_actual}-" . str_pad($contador, 6, '0', STR_PAD_LEFT);
            
            // Insertar el detalle de venta con el código generado
            $insert_sql = "INSERT INTO detalles_venta (vnt_id, dv_codigo_factura, dv_fecha_creacion) 
                          VALUES ({$vnt_id_san}, '{$codigo_factura}', NOW())";
            
            if (!$this->mysqli->query($insert_sql)) {
                throw new Exception("Error al insertar detalle de venta: " . $this->mysqli->error);
            }
            
            // Confirmar transacción
            $this->mysqli->commit();
            
            error_log("Detalle de venta generado exitosamente para vnt_id: {$vnt_id_san}, código: {$codigo_factura}");
            return true;
            
        } catch (Exception $e) {
            // Revertir transacción en caso de error
            $this->mysqli->rollback();
            error_log("Error al generar detalle de venta: " . $e->getMessage());
            return false;
        }
    }
}
?>
