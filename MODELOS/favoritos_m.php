<?php
require_once __DIR__ . "/../CONFIG/Conexion.php";

class Favoritos_M
{
    private $conexion_obj;
    private $conn;

    public function __construct()
    {
        try {
            $this->conexion_obj = new Conexion();
            $this->conn = $this->conexion_obj->conecta();
        } catch (Exception $e) {
            error_log("Error de conexión en Favoritos_M constructor: " . $e->getMessage());
            $this->conn = null;
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
        while($this->conn->more_results() && $this->conn->next_result()){;}

        if (isset($out_params['resultado']) && $out_params['resultado'] == 1) {
            return ['status' => 'success', 'message' => $out_params['mensaje']];
        } elseif (isset($out_params['resultado']) && $out_params['resultado'] == 0) { // Ya era favorito
             return ['status' => 'info', 'message' => $out_params['mensaje']]; // O 'success' si prefieres
        }
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
        while($this->conn->more_results() && $this->conn->next_result()){;}
        
        if (isset($out_params['resultado']) && $out_params['resultado'] == 1) {
            return ['status' => 'success', 'message' => $out_params['mensaje']];
        } elseif (isset($out_params['resultado']) && $out_params['resultado'] == 0) { // No era favorito
             return ['status' => 'info', 'message' => $out_params['mensaje']]; // O 'success'
        }
        return ['status' => 'error', 'message' => $out_params['mensaje'] ?? 'No se pudo quitar de favoritos.'];
    }

    public function esFavorito($usu_id, $veh_id)
    {
        if (!$this->conn) return false;

        $usu_id_esc = $this->conn->real_escape_string($usu_id);
        $veh_id_esc = $this->conn->real_escape_string($veh_id);

        // Para una simple verificación, podemos usar una query directa o un SP.
        // Usaremos el SP por consistencia.
        $sql = "CALL sp_verificar_favorito($usu_id_esc, $veh_id_esc, @p_es_favorito)";
        
        if (!$this->conn->query($sql)) {
            error_log("Error al llamar a sp_verificar_favorito: " . $this->conn->error . " (SQL: $sql)");
            return false; // Asumir que no es favorito en caso de error
        }

        $res = $this->conn->query("SELECT @p_es_favorito AS es_favorito");
        if (!$res) {
            error_log("Error obteniendo resultado de sp_verificar_favorito: " . $this->conn->error);
            return false;
        }
        $out_params = $res->fetch_assoc();
        $res->free();
        while($this->conn->more_results() && $this->conn->next_result()){;}
        
        return isset($out_params['es_favorito']) ? (bool)$out_params['es_favorito'] : false;
    }

    public function getFavoritosPorUsuario($usu_id)
    {
        if (!$this->conn) return false;

        $usu_id_esc = $this->conn->real_escape_string($usu_id);
        $sql = "CALL sp_get_favoritos_por_usuario($usu_id_esc)";
        
        $resultado = $this->conexion_obj->ejecutarSP($sql);
        $favoritos = [];

        if ($resultado && $resultado instanceof mysqli_result) {
            if ($resultado->num_rows > 0) {
                while ($fila = $resultado->fetch_assoc()) {
                    // Ajustar URL de imagen para el frontend
                     if (isset($fila['imagen_principal_url']) && strpos($fila['imagen_principal_url'], 'PUBLIC/') === 0) {
                        $fila['imagen_principal_url_frontend'] = '../' . $fila['imagen_principal_url'];
                    } elseif (isset($fila['imagen_principal_url'])) {
                        $fila['imagen_principal_url_frontend'] = '../PUBLIC/' . $fila['imagen_principal_url'];
                    } else {
                        $fila['imagen_principal_url_frontend'] = '../PUBLIC/Img/auto_placeholder.png';
                    }
                    $favoritos[] = $fila;
                }
            }
            $resultado->free();
        } elseif ($resultado === false) {
            error_log("Error al ejecutar sp_get_favoritos_por_usuario para usu_id $usu_id_esc: " . $this->conn->error);
            return false;
        }
        return $favoritos;
    }
}
?>