<?php
require_once __DIR__ . "/../CONFIG/Conexion.php";

class Cotizaciones_M {
    private $conn_obj;
    private $conn;

    public function __construct() {
        try {
            $this->conn_obj = new Conexion();
            $this->conn = $this->conn_obj->conecta();
        } catch (Exception $e) {
            error_log("Error de conexión en Cotizaciones_M constructor: " . $e->getMessage());
            $this->conn = null;
            throw $e;
        }
    }
    
    // --- FUNCIÓN PARA OBTENER COTIZACIONES (CORREGIDA) ---
    public function getCotizacionesPorVendedor($usu_id_vendedor) {
        if (!$this->conn) return [];
        
        try {
            // Preparamos la llamada al SP
            $stmt = $this->conn->prepare("CALL sp_get_cotizaciones_por_vendedor(?)");
            if ($stmt === false) {
                throw new Exception("Error al preparar sp_get_cotizaciones_por_vendedor: " . $this->conn->error);
            }

            $stmt->bind_param("i", $usu_id_vendedor);
            $stmt->execute();

            // Obtenemos el resultado
            $resultado = $stmt->get_result();
            if ($resultado === false) {
                throw new Exception("Error al obtener el resultado de sp_get_cotizaciones_por_vendedor: " . $stmt->error);
            }
            
            $cotizaciones = $resultado->fetch_all(MYSQLI_ASSOC);
            
            // Cerramos el statement y limpiamos la conexión, imitando tu clase Conexion.php
            $stmt->close();
            while($this->conn->more_results() && $this->conn->next_result()) {;}

            return $cotizaciones;
            
        } catch (Exception $e) {
            error_log("Excepción en getCotizacionesPorVendedor: " . $e->getMessage());
            // En caso de excepción, intenta cerrar el statement si existe
            if (isset($stmt) && $stmt instanceof mysqli_stmt) {
                $stmt->close();
            }
            return [];
        }
    }

    // --- FUNCIÓN PARA INSERTAR COTIZACIÓN (CORREGIDA) ---
    public function insertarCotizacion($datos) {
        if (!$this->conn) return ['resultado' => 0, 'mensaje' => 'Error de conexión.'];
        
        try {
            $stmt = $this->conn->prepare("CALL sp_insertar_cotizacion(?, ?, ?, @p_resultado, @p_mensaje_respuesta)");
            if ($stmt === false) {
                throw new Exception("Error al preparar sp_insertar_cotizacion: " . $this->conn->error);
            }

            $stmt->bind_param("iis", $datos['usu_id_solicitante'], $datos['veh_id'], $datos['mensaje']);
            $stmt->execute();
            $stmt->close();

            // Ahora usamos tu método para obtener los resultados, ya que no tiene parámetros
            $res_sp = $this->conn_obj->ejecutarSP("SELECT @p_resultado AS resultado, @p_mensaje_respuesta AS mensaje");
            if (!$res_sp) {
                 throw new Exception("Error al obtener resultados del SP de inserción.");
            }
            $out_params = $res_sp->fetch_assoc();
            $res_sp->free();

            return $out_params;

        } catch (Exception $e) {
            error_log("Excepción en insertarCotizacion: " . $e->getMessage());
            return ['resultado' => 0, 'mensaje' => 'Error técnico al procesar la solicitud.'];
        }
    }

    // --- FUNCIÓN PARA ACTUALIZAR ESTADO (CORREGIDA) ---
    public function updateEstadoCotizacion($cot_id, $nuevo_estado, $usu_id_vendedor) {
        if (!$this->conn) return ['resultado' => 0, 'mensaje' => 'Error de conexión.'];
        
        try {
            $stmt = $this->conn->prepare("CALL sp_update_cotizacion_estado(?, ?, ?, @p_resultado, @p_mensaje)");
            if ($stmt === false) {
                throw new Exception("Error al preparar sp_update_cotizacion_estado: " . $this->conn->error);
            }

            $stmt->bind_param("isi", $cot_id, $nuevo_estado, $usu_id_vendedor);
            $stmt->execute();
            $stmt->close();
            
            // Usamos tu método de nuevo
            $res_sp = $this->conn_obj->ejecutarSP("SELECT @p_resultado AS resultado, @p_mensaje AS mensaje");
            if (!$res_sp) {
                throw new Exception("Error al obtener resultados del SP de actualización.");
            }
            $out_params = $res_sp->fetch_assoc();
            $res_sp->free();
            
            return $out_params;

        } catch (Exception $e) {
            error_log("Excepción en updateEstadoCotizacion: " . $e->getMessage());
            return ['resultado' => 0, 'mensaje' => 'Error técnico al actualizar el estado.'];
        }
    }
}
?>