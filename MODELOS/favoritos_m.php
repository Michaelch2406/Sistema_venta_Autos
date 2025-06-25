<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../CONFIG/Conexion.php";

class Favoritos_M
{
    private $conn_obj; // Para el objeto Conexion
    private $conn;     // Para el objeto mysqli

    public function __construct()
    {
        try {
            $this->conn_obj = new Conexion(); // Instancia de nuestra clase Conexion
            $this->conn = $this->conn_obj->conecta(); // Obtenemos el objeto mysqli
        } catch (Exception $e) {
            error_log("Error de conexión en Favoritos_M constructor: " . $e->getMessage());
            $this->conn = null;
            // Considerar si relanzar la excepción o manejarla aquí de otra forma.
            // Por ahora, si conn es null, los métodos devolverán error.
        }
    }

    public function agregarFavorito($usu_id, $veh_id)
    {
        if (!$this->conn) return ['status' => 'error', 'message' => 'Error de conexión BD.'];
        
        $usu_id_esc = $this->conn->real_escape_string($usu_id);
        $veh_id_esc = $this->conn->real_escape_string($veh_id);

        $sql = "CALL sp_agregar_favorito($usu_id_esc, $veh_id_esc, @p_resultado, @p_mensaje)";
        
        if (!$this->conn->query($sql)) {
            error_log("Error al llamar a sp_agregar_favorito: " . $this->conn->error . " (SQL: $sql)");
            return ['status' => 'error', 'message' => 'Error técnico al agregar favorito (SP Call).'];
        }

        $res = $this->conn->query("SELECT @p_resultado AS resultado, @p_mensaje AS mensaje");
        if (!$res) {
            error_log("Error obteniendo resultado de sp_agregar_favorito: " . $this->conn->error);
            return ['status' => 'error', 'message' => 'Error obteniendo resultado del SP (agregar).'];
        }
        $out_params = $res->fetch_assoc();
        $res->free();
        while($this->conn->more_results() && $this->conn->next_result()){
            if($rs = $this->conn->store_result()){ $rs->free(); } // Liberar explícitamente
        }

        if (isset($out_params['resultado'])) {
            if ($out_params['resultado'] == 1) { // Éxito
                return ['status' => 'success', 'message' => $out_params['mensaje']];
            } elseif ($out_params['resultado'] == 0) { // Ya era favorito
                 return ['status' => 'info', 'message' => $out_params['mensaje']];
            }
        }
        // Error o resultado no esperado del SP
        return ['status' => 'error', 'message' => $out_params['mensaje'] ?? 'No se pudo agregar a favoritos.'];
    }

    public function quitarFavorito($usu_id, $veh_id)
    {
        if (!$this->conn) return ['status' => 'error', 'message' => 'Error de conexión BD.'];

        $usu_id_esc = $this->conn->real_escape_string($usu_id);
        $veh_id_esc = $this->conn->real_escape_string($veh_id);

        $sql = "CALL sp_quitar_favorito($usu_id_esc, $veh_id_esc, @p_resultado, @p_mensaje)";

        if (!$this->conn->query($sql)) {
            error_log("Error al llamar a sp_quitar_favorito: " . $this->conn->error . " (SQL: $sql)");
            return ['status' => 'error', 'message' => 'Error técnico al quitar favorito (SP Call).'];
        }
        
        $res = $this->conn->query("SELECT @p_resultado AS resultado, @p_mensaje AS mensaje");
        if (!$res) {
            error_log("Error obteniendo resultado de sp_quitar_favorito: " . $this->conn->error);
            return ['status' => 'error', 'message' => 'Error obteniendo resultado del SP (quitar).'];
        }
        $out_params = $res->fetch_assoc();
        $res->free();
        while($this->conn->more_results() && $this->conn->next_result()){
            if($rs = $this->conn->store_result()){ $rs->free(); } // Liberar explícitamente
        }
        
        if (isset($out_params['resultado'])) {
            if ($out_params['resultado'] == 1) { // Éxito
                return ['status' => 'success', 'message' => $out_params['mensaje']];
            } elseif ($out_params['resultado'] == 0) { // No era favorito
                 return ['status' => 'info', 'message' => $out_params['mensaje']];
            }
        }
        // Error o resultado no esperado del SP
        return ['status' => 'error', 'message' => $out_params['mensaje'] ?? 'No se pudo quitar de favoritos.'];
    }

    public function verificarFavorito($usu_id, $veh_id) // Cambiado de esFavorito para mayor claridad con el SP
    {
        if (!$this->conn) return false; // En caso de error de BD, asumimos que no es favorito

        $usu_id_esc = $this->conn->real_escape_string($usu_id);
        $veh_id_esc = $this->conn->real_escape_string($veh_id);

        $sql = "CALL sp_verificar_favorito($usu_id_esc, $veh_id_esc, @p_es_favorito)";
        
        if (!$this->conn->query($sql)) {
            error_log("Error al llamar a sp_verificar_favorito: " . $this->conn->error . " (SQL: $sql)");
            return false; 
        }

        $res = $this->conn->query("SELECT @p_es_favorito AS es_favorito");
        if (!$res) {
            error_log("Error obteniendo resultado de sp_verificar_favorito: " . $this->conn->error);
            return false;
        }
        $out_params = $res->fetch_assoc();
        $res->free();
        while($this->conn->more_results() && $this->conn->next_result()){
            if($rs = $this->conn->store_result()){ $rs->free(); } // Liberar explícitamente
        }
        
        // El SP devuelve 1 (TRUE) o 0 (FALSE) para p_es_favorito
        return isset($out_params['es_favorito']) ? (bool)$out_params['es_favorito'] : false;
    }

    public function getFavoritosPorUsuario($usu_id)
    {
        if (!$this->conn) return []; // Devolver array vacío en caso de error de BD

        $usu_id_esc = $this->conn->real_escape_string($usu_id);
        $sql = "CALL sp_get_favoritos_por_usuario($usu_id_esc)";
        
        // Usar $this->conn_obj ya que ejecutarSP es un método de la clase Conexion
        $resultado = $this->conn_obj->ejecutarSP($sql); 
        $favoritos = [];

        if ($resultado && $resultado instanceof mysqli_result) {
            if ($resultado->num_rows > 0) {
                while ($fila = $resultado->fetch_assoc()) {
                    // Ajustar URL de imagen para el frontend
                     if (isset($fila['imagen_principal_url']) && !empty($fila['imagen_principal_url'])) {
                        if (strpos($fila['imagen_principal_url'], 'PUBLIC/') === 0) {
                           $fila['imagen_principal_url_frontend'] = '../' . $fila['imagen_principal_url'];
                        } else {
                           // Si la URL ya es relativa o absoluta y no contiene 'PUBLIC/' al inicio,
                           // o si es solo el nombre del archivo, prefijamos con la ruta esperada.
                           // Esto podría necesitar ajuste según cómo se guarden las URLs en la BD.
                           $fila['imagen_principal_url_frontend'] = '../PUBLIC/uploads/vehiculos/' . $fila['veh_id'] . '/' . $fila['imagen_principal_url'];
                           // Asumiendo una estructura como PUBLIC/uploads/vehiculos/ID_VEHICULO/nombre_imagen.jpg
                           // Si la URL ya es completa desde la raíz de PUBLIC/, ej: "uploads/vehiculos/1/imagen.jpg"
                           // entonces sería: '../PUBLIC/' . $fila['imagen_principal_url']
                           // La lógica actual del SP parece devolver solo el nombre o una ruta parcial.
                           // La línea original era:
                           // $fila['imagen_principal_url_frontend'] = '../PUBLIC/' . $fila['imagen_principal_url'];
                           // Esta línea se cambia para ser más específica si es solo el nombre del archivo.
                           // Si el SP devuelve una URL ya completa desde la raíz del sitio, se debe ajustar.
                           // Por ahora, para ser más robusto, si no empieza con PUBLIC/, asumimos que es nombre de archivo dentro de su carpeta de vehículo.
                           // Si el SP devuelve 'PUBLIC/uploads/vehiculos/1/img.jpg', la primera condición es suficiente.
                           // Si el SP devuelve solo 'img.jpg', la segunda condición debe construir la ruta.
                           // Se necesita claridad sobre qué devuelve exactamente el SP en `imagen_principal_url`.
                           // Por ahora, volviendo a una lógica más simple basada en tu código original:
                            $fila['imagen_principal_url_frontend'] = '../' . $fila['imagen_principal_url'];
                        }
                    } else {
                        $fila['imagen_principal_url_frontend'] = '../PUBLIC/Img/auto_placeholder.png';
                    }
                    $favoritos[] = $fila;
                }
            }
            $resultado->free();
            // Asegurar que todos los resultados se limpian antes de otra posible query si este método se expandiera.
            while($this->conn->more_results() && $this->conn->next_result()){
                if($rs = $this->conn->store_result()){ $rs->free(); }
            }
        } elseif ($resultado === false) {
            error_log("Error al ejecutar sp_get_favoritos_por_usuario para usu_id $usu_id_esc: " . ($this->conn->error ?? 'Error desconocido') . " (SQL: $sql)");
            return []; // Devolver array vacío en caso de error
        }
        return $favoritos;
    }
}
?>