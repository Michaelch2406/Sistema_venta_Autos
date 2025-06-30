<?php
require_once __DIR__ . "/../CONFIG/Conexion.php";

class Cotizaciones_M {
    private $conn_obj;
    private $conn;

    public function __construct() {
        $this->conn_obj = new Conexion();
        $this->conn = $this->conn_obj->conecta();
    }

    public function insertarCotizacion($datos) {
        if (!$this->conn) {
            error_log("Intento de insertar cotización sin conexión a BD.");
            return ['resultado' => 0, 'mensaje' => 'Error de conexión.'];
        }

        try {
            $stmt = $this->conn->prepare("CALL sp_insertar_cotizacion(?, ?, ?, @p_resultado, @p_mensaje_respuesta)");
            if ($stmt === false) {
                throw new Exception("Error al preparar el SP: " . $this->conn->error);
            }

            $stmt->bind_param(
                "iis",
                $datos['usu_id_solicitante'],
                $datos['veh_id'],
                $datos['mensaje']
            );

            $stmt->execute();
            $stmt->close();

            // Es importante limpiar los resultados antes de la siguiente consulta
            while($this->conn->more_results() && $this->conn->next_result()) {;}

            // Obtener los parámetros de salida del SP
            $res_sp = $this->conn->query("SELECT @p_resultado AS resultado, @p_mensaje_respuesta AS mensaje");
            if (!$res_sp) {
                 throw new Exception("Error al obtener resultados del SP: " . $this->conn->error);
            }
            $out_params = $res_sp->fetch_assoc();
            $res_sp->free();

            return $out_params;

        } catch (Exception $e) {
            error_log("Error en insertarCotizacion: " . $e->getMessage());
            return ['resultado' => 0, 'mensaje' => 'Error técnico al procesar la solicitud.'];
        }
    }
}
?>