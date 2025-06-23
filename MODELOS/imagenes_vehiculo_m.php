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
}
?>