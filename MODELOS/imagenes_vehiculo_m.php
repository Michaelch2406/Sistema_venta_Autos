<?php
require_once __DIR__ . "/../CONFIG/Conexion.php";

class ImagenesVehiculo_M
{
    private $conn_obj;
    private $conn;

    public function __construct()
    {
        try {
            $this->conexion_obj = new Conexion();
            $this->conn = $this->conexion_obj->conecta();
        } catch (Exception $e) {
            error_log("CRITICAL: Error de conexión en constructor ImagenesVehiculo_M: " . $e->getMessage());
            $this->conn = null; 
            // Es importante que las funciones que usan $this->conn verifiquen si es null
        }
    }

    // Devuelve un array con 'resultado' y 'mensaje'
    // 'resultado' = 1 si se insertó correctamente, 0 si hubo error
    // 'mensaje' = mensaje de éxito o error
    public function insertarImagen($veh_id, $ima_url, $es_principal = false)
    {
        if (!$this->conn) {
            error_log("insertarImagen: No hay conexión a la BD.");
            return ['resultado' => 0, 'mensaje' => 'Error de conexión interna al guardar imagen.'];
        }

        $veh_id_esc = $this->conn->real_escape_string($veh_id);
        $ima_url_esc = $this->conn->real_escape_string($ima_url);
        $es_principal_esc = $es_principal ? 'TRUE' : 'FALSE';

        $sql = "CALL sp_insertar_imagen_vehiculo(
            $veh_id_esc, '$ima_url_esc', $es_principal_esc,
            @p_ima_id_insertado, @p_resultado, @p_mensaje
        )";

        // Limpiar resultados anteriores si los hay
        while($this->conn->more_results() && $this->conn->next_result()){ if($res = $this->conn->store_result()){ $res->free(); } }

        if (!$this->conn->query($sql)) {
            error_log("Error al llamar a sp_insertar_imagen_vehiculo: " . $this->conn->error . " (SQL: $sql)");
            return ['resultado' => 0, 'mensaje' => 'Error técnico al guardar la imagen (llamada SP).'];
        }

        $res_out = $this->conn->query("SELECT @p_ima_id_insertado AS ima_id, @p_resultado AS resultado, @p_mensaje AS mensaje");
        if (!$res_out) {
            error_log("Error al obtener parámetros OUT de sp_insertar_imagen_vehiculo: " . $this->conn->error);
            return ['resultado' => 0, 'mensaje' => 'Error técnico al obtener resultados del SP de imagen.'];
        }
        $out_params = $res_out->fetch_assoc();
        $res_out->free();
        
        // Asegurarse de que todos los resultados estén limpios
        while($this->conn->more_results() && $this->conn->next_result()){ if($res = $this->conn->store_result()){ $res->free(); } }

        return $out_params;
    }
    public function getImagenesPorVehiculo($veh_id)
    {
        if (!$this->conn) return false;

        $veh_id_esc = $this->conn->real_escape_string($veh_id);
        // No necesitamos un SP para una consulta tan simple, pero se podría crear por consistencia
        $sql = "SELECT ima_id, ima_url, ima_es_principal 
                FROM ImagenesVehiculo 
                WHERE veh_id = $veh_id_esc 
                ORDER BY ima_es_principal DESC, ima_id ASC";
        
        $resultado = $this->conn->query($sql); // Usar query directamente para SELECTs simples
        $imagenes = [];

        if ($resultado && $resultado instanceof mysqli_result) {
            if ($resultado->num_rows > 0) {
                while ($fila = $resultado->fetch_assoc()) {
                    // Ajustar la URL si es necesario para el frontend
                    if (strpos($fila['ima_url'], 'PUBLIC/') === 0) { // Si ya contiene PUBLIC/
                        $fila['ima_url_frontend'] = '../' . $fila['ima_url'];
                    } else { // Si es una ruta como uploads/vehiculos/...
                        $fila['ima_url_frontend'] = '../PUBLIC/' . $fila['ima_url'];
                    }
                    $imagenes[] = $fila;
                }
            }
            $resultado->free();
        } elseif ($resultado === false) {
            error_log("Error al obtener imágenes para veh_id $veh_id_esc: " . $this->conn->error . " (SQL: $sql)");
            return false;
        }
        // No es necesario limpiar resultados múltiples aquí para un SELECT simple
        return $imagenes;
    }

    // Nuevo método que utiliza el Stored Procedure sp_get_imagenes_por_vehiculo
    public function getImagenesPorVehiculoId($veh_id) {
        if (!$this->conn) {
            error_log("getImagenesPorVehiculoId: No hay conexión a la BD.");
            return []; // Devuelve array vacío en caso de error de conexión
        }

        $veh_id_esc = $this->conn->real_escape_string($veh_id);
        $sql = "CALL sp_get_imagenes_por_vehiculo($veh_id_esc)";

        // Limpiar resultados anteriores si los hay
        while($this->conn->more_results() && $this->conn->next_result()){ if($res = $this->conn->store_result()){ $res->free(); } }
        
        $resultado = $this->conn->query($sql);
        $imagenes = [];

        if ($resultado && $resultado instanceof mysqli_result) {
            while ($fila = $resultado->fetch_assoc()) {
                // La URL ya debería estar correcta desde la BD (ej. PUBLIC/uploads/vehiculos/1/imagen.jpg)
                // El JS se encarga de añadir '../' si es necesario para la ruta relativa desde VISTAS/JS/
                $imagenes[] = $fila;
            }
            $resultado->free();
            // Limpiar nuevamente por si el SP tiene múltiples result sets o un output
            while($this->conn->more_results() && $this->conn->next_result()){ if($res = $this->conn->store_result()){ $res->free(); } }
        } elseif ($resultado === false) {
            error_log("Error al ejecutar sp_get_imagenes_por_vehiculo para veh_id $veh_id_esc: " . $this->conn->error . " (SQL: $sql)");
            // Devuelve array vacío en caso de error de consulta
        }
        return $imagenes;
    }
}
?>