<?php
require_once __DIR__ . "/../CONFIG/Conexion.php";

class Vehiculo
{
    private $conn_obj;
    private $conn;

    public function __construct()
    {
        try {
            $this->conn_obj = new Conexion();
            $this->conn = $this->conn_obj->conecta();
        } catch (Exception $e) {
            error_log("Error de conexión en Vehiculos_M constructor: " . $e->getMessage());
            $this->conn = null; 
             // Lanzar la excepción permite que el código que llama maneje el error de conexión
            throw $e;
        }
    }

    public function insertarVehiculo($datos)
    {
        if (!$this->conn) {
             error_log("Intento de insertar vehículo sin conexión a BD válida.");
            return ['resultado' => 0, 'mensaje' => 'Error de conexión a la base de datos.', 'veh_id' => null];
        }

        $mar_id = $this->conn->real_escape_string($datos['mar_id']);
        $mod_id = $this->conn->real_escape_string($datos['mod_id']);
        $tiv_id = $this->conn->real_escape_string($datos['tiv_id']);
        $veh_subtipo_vehiculo = isset($datos['veh_subtipo_vehiculo']) && trim($datos['veh_subtipo_vehiculo']) !== '' ? "'" . $this->conn->real_escape_string(trim($datos['veh_subtipo_vehiculo'])) . "'" : "NULL";
        $usu_id_gestor = isset($datos['usu_id_gestor']) && trim($datos['usu_id_gestor']) !== '' ? "'" . $this->conn->real_escape_string(trim($datos['usu_id_gestor'])) . "'" : "NULL";
        $veh_condicion = $this->conn->real_escape_string($datos['veh_condicion']);
        $veh_anio = $this->conn->real_escape_string($datos['veh_anio']);
        $veh_precio = $this->conn->real_escape_string($datos['veh_precio']);
        $veh_vin = isset($datos['veh_vin']) && trim($datos['veh_vin']) !== '' ? "'" . $this->conn->real_escape_string(trim($datos['veh_vin'])) . "'" : "NULL";
        
        // Definición de variables para SP
        $p_veh_kilometraje = "NULL";
        $p_veh_placa_provincia_origen = "NULL";
        $p_veh_ultimo_digito_placa = "NULL";

        if ($veh_condicion == 'nuevo') {
            $p_veh_kilometraje = "0"; 
            // $p_veh_placa_provincia_origen y $p_veh_ultimo_digito_placa ya son NULL por defecto
        } else { // 'usado'
            $p_veh_kilometraje = (isset($datos['veh_kilometraje']) && trim($datos['veh_kilometraje']) !== '') ? "'" . $this->conn->real_escape_string(trim($datos['veh_kilometraje'])) . "'" : "NULL";
            $p_veh_placa_provincia_origen = (isset($datos['veh_placa_provincia_origen']) && trim($datos['veh_placa_provincia_origen']) !== '') ? "'" . $this->conn->real_escape_string(trim($datos['veh_placa_provincia_origen'])) . "'" : "NULL";
            $p_veh_ultimo_digito_placa = (isset($datos['veh_ultimo_digito_placa']) && trim($datos['veh_ultimo_digito_placa']) !== '') ? "'" . $this->conn->real_escape_string(trim($datos['veh_ultimo_digito_placa'])) . "'" : "NULL";
        }
        
        $veh_ubicacion_provincia = $this->conn->real_escape_string($datos['veh_ubicacion_provincia']);
        $veh_ubicacion_ciudad = $this->conn->real_escape_string($datos['veh_ubicacion_ciudad']);

        $veh_color_exterior = $this->conn->real_escape_string(trim($datos['veh_color_exterior']));
        $veh_color_interior = isset($datos['veh_color_interior']) && trim($datos['veh_color_interior']) !== '' ? "'" . $this->conn->real_escape_string(trim($datos['veh_color_interior'])) . "'" : "NULL";
        $veh_detalles_motor = $this->conn->real_escape_string(trim($datos['veh_detalles_motor'])); // Requerido en el form
        $veh_tipo_transmision = isset($datos['veh_tipo_transmision']) && trim($datos['veh_tipo_transmision']) !== '' ? "'" . $this->conn->real_escape_string(trim($datos['veh_tipo_transmision'])) . "'" : "NULL";
        $veh_traccion = isset($datos['veh_traccion']) && trim($datos['veh_traccion']) !== '' ? "'" . $this->conn->real_escape_string(trim($datos['veh_traccion'])) . "'" : "NULL";
        $veh_tipo_vidrios = isset($datos['veh_tipo_vidrios']) && trim($datos['veh_tipo_vidrios']) !== '' ? "'" . $this->conn->real_escape_string(trim($datos['veh_tipo_vidrios'])) . "'" : "NULL";
        $veh_tipo_combustible = isset($datos['veh_tipo_combustible']) && trim($datos['veh_tipo_combustible']) !== '' ? "'" . $this->conn->real_escape_string(trim($datos['veh_tipo_combustible'])) . "'" : "NULL";
        $veh_tipo_direccion = isset($datos['veh_tipo_direccion']) && trim($datos['veh_tipo_direccion']) !== '' ? "'" . $this->conn->real_escape_string(trim($datos['veh_tipo_direccion'])) . "'" : "NULL";
        $veh_sistema_climatizacion = isset($datos['veh_sistema_climatizacion']) && trim($datos['veh_sistema_climatizacion']) !== '' ? "'" . $this->conn->real_escape_string(trim($datos['veh_sistema_climatizacion'])) . "'" : "NULL";
        
        $veh_descripcion = $this->conn->real_escape_string(trim($datos['veh_descripcion'])); // Requerido en el form
        
        $detalles_extra_array = isset($datos['veh_detalles_extra']) && is_array($datos['veh_detalles_extra']) ? $datos['veh_detalles_extra'] : [];
        $veh_detalles_extra_str = !empty($detalles_extra_array) ? "'" . $this->conn->real_escape_string(implode(', ', $detalles_extra_array)) . "'" : "NULL";

        $veh_fecha_publicacion = $this->conn->real_escape_string($datos['veh_fecha_publicacion']);

        // Parámetros para veh_caracteristicas_seguridad y veh_caracteristicas_adicionales (opcionales)
        // Se asume que si no vienen en $datos, se pasarán como NULL al SP.
        // El SP actual en el issue los tiene como parámetros, así que debemos pasarlos.
        // Si el SP fue modificado para NO recibirlos, esta parte debe ajustarse.
        // El nuevo SP no incluye veh_caracteristicas_seguridad ni veh_caracteristicas_adicionales
        $sql = "CALL sp_insertar_vehiculo(
            $mar_id, $mod_id, $tiv_id, $veh_subtipo_vehiculo, $usu_id_gestor, '$veh_condicion', $veh_anio, $p_veh_kilometraje,
            $veh_precio, $veh_vin, '$veh_ubicacion_provincia', '$veh_ubicacion_ciudad', $p_veh_placa_provincia_origen, $p_veh_ultimo_digito_placa,
            '$veh_color_exterior', $veh_color_interior, '$veh_detalles_motor', $veh_tipo_transmision,
            $veh_traccion, $veh_tipo_vidrios, $veh_tipo_combustible, $veh_tipo_direccion, $veh_sistema_climatizacion,
            -- Ya no se pasan veh_caracteristicas_seguridad ni veh_caracteristicas_adicionales
            '$veh_descripcion', $veh_detalles_extra_str,
            '$veh_fecha_publicacion',
            @p_veh_id_insertado, @p_resultado, @p_mensaje
        )";
        
        if (!$this->conn->query($sql)) {
            error_log("Error al llamar a sp_insertar_vehiculo: " . $this->conn->error . ". SQL ejecutado: " . preg_replace('/\s+/', ' ', $sql));
            return ['resultado' => 0, 'mensaje' => 'Error técnico al publicar el vehículo. (SP Call Error)', 'veh_id' => null];
        }

        $res = $this->conn->query("SELECT @p_veh_id_insertado AS veh_id, @p_resultado AS resultado, @p_mensaje AS mensaje");
        if (!$res) {
             error_log("Error al obtener resultados de sp_insertar_vehiculo: " . $this->conn->error);
            return ['resultado' => 0, 'mensaje' => 'Error técnico al obtener resultados del SP.', 'veh_id' => null];
        }
        $out_params = $res->fetch_assoc();
        $res->free();
        
        // Es importante limpiar resultados múltiples ANTES de la siguiente llamada si usas la misma conexión
        while($this->conn->more_results() && $this->conn->next_result()){
            if($rs = $this->conn->store_result()){ $rs->free(); }
        }

        return $out_params;
    }

    public function getVehiculosPorGestor($usu_id_gestor)
    {
        if (!$this->conn) return false;
        $usu_id_gestor_esc = $this->conn->real_escape_string($usu_id_gestor);
        $sql = "CALL sp_get_vehiculos_por_gestor($usu_id_gestor_esc)";
        $resultado = $this->conn_obj->ejecutarSP($sql); 
        $vehiculos = [];
        if ($resultado && $resultado instanceof mysqli_result) {
            if ($resultado->num_rows > 0) {
                while ($fila = $resultado->fetch_assoc()) {
                    $vehiculos[] = $fila;
                }
            }
            $resultado->free();
        } elseif ($resultado === false) {
            error_log("Error al ejecutar sp_get_vehiculos_por_gestor: " . ($this->conn->error ?? 'Error desconocido') . " (SQL: " . $sql . ")");
            return false; 
        }
        return $vehiculos;
    }

    public function actualizarEstadoVehiculo($veh_id, $nuevo_estado, $usu_id_gestor_actual)
    {
        if (!$this->conn) return ['resultado' => 0, 'mensaje' => 'Error de conexión a la base de datos.'];
        $veh_id_esc = $this->conn->real_escape_string($veh_id);
        $nuevo_estado_esc = $this->conn->real_escape_string($nuevo_estado);
        $usu_id_gestor_actual_esc = $this->conn->real_escape_string($usu_id_gestor_actual);
        $sql = "CALL sp_actualizar_estado_vehiculo(
            $veh_id_esc, '$nuevo_estado_esc', $usu_id_gestor_actual_esc,
            @p_resultado, @p_mensaje
        )";
        if (!$this->conn->query($sql)) {
            error_log("Error al llamar a sp_actualizar_estado_vehiculo: " . $this->conn->error . " (SQL: $sql)");
            return ['resultado' => 0, 'mensaje' => 'Error técnico al actualizar estado (llamada SP).'];
        }
        $res = $this->conn->query("SELECT @p_resultado AS resultado, @p_mensaje AS mensaje");
        if (!$res) {
            error_log("Error al obtener resultados de sp_actualizar_estado_vehiculo: " . $this->conn->error);
            return ['resultado' => 0, 'mensaje' => 'Error técnico al obtener resultados del SP de actualización de estado.'];
        }
        $out_params = $res->fetch_assoc();
        $res->free();
        while($this->conn->more_results() && $this->conn->next_result()){
             if($rs = $this->conn->store_result()){ $rs->free(); }
        }
        return $out_params;
    }
    public function getVehiculosListado($filtros)
    {
        if (!$this->conn) return ['vehiculos' => [], 'total' => 0, 'error' => 'No hay conexión a BD'];

        // Valores por defecto para filtros y paginación
        $condicion = isset($filtros['condicion']) ? $this->conn->real_escape_string($filtros['condicion']) : 'todos';
        $mar_id = isset($filtros['mar_id']) && filter_var($filtros['mar_id'], FILTER_VALIDATE_INT) ? (int)$filtros['mar_id'] : 'NULL';
        $mod_id = isset($filtros['mod_id']) && filter_var($filtros['mod_id'], FILTER_VALIDATE_INT) ? (int)$filtros['mod_id'] : 'NULL';
        $tiv_id = isset($filtros['tiv_id']) && filter_var($filtros['tiv_id'], FILTER_VALIDATE_INT) ? (int)$filtros['tiv_id'] : 'NULL';
        
        $precio_min = isset($filtros['precio_min']) && is_numeric($filtros['precio_min']) ? "'" . $this->conn->real_escape_string($filtros['precio_min']) . "'" : 'NULL';
        $precio_max = isset($filtros['precio_max']) && is_numeric($filtros['precio_max']) ? "'" . $this->conn->real_escape_string($filtros['precio_max']) . "'" : 'NULL';
        $anio_min = isset($filtros['anio_min']) && filter_var($filtros['anio_min'], FILTER_VALIDATE_INT) ? (int)$filtros['anio_min'] : 'NULL';
        $anio_max = isset($filtros['anio_max']) && filter_var($filtros['anio_max'], FILTER_VALIDATE_INT) ? (int)$filtros['anio_max'] : 'NULL';
        $provincia = isset($filtros['provincia']) && !empty($filtros['provincia']) ? "'" . $this->conn->real_escape_string($filtros['provincia']) . "'" : 'NULL';
        
        $items_por_pagina = isset($filtros['items_por_pagina']) && filter_var($filtros['items_por_pagina'], FILTER_VALIDATE_INT) ? (int)$filtros['items_por_pagina'] : 12; // Default 12 items
        $pagina_actual = isset($filtros['pagina']) && filter_var($filtros['pagina'], FILTER_VALIDATE_INT) ? (int)$filtros['pagina'] : 1;
        $offset = ($pagina_actual - 1) * $items_por_pagina;

        $sql = "CALL sp_get_vehiculos_listado(
            '$condicion', $mar_id, $mod_id, $tiv_id,
            $precio_min, $precio_max, $anio_min, $anio_max, $provincia,
            $items_por_pagina, $offset,
            @p_total_vehiculos
        )";
        
        $resultado_sp = $this->conn_obj->ejecutarSP($sql);
        $vehiculos = [];

        if ($resultado_sp && $resultado_sp instanceof mysqli_result) {
            if ($resultado_sp->num_rows > 0) {
                while ($fila = $resultado_sp->fetch_assoc()) {
                    $vehiculos[] = $fila;
                }
            }
            $resultado_sp->free();
            // Es crucial limpiar para poder obtener el parámetro OUT
            while($this->conn->more_results() && $this->conn->next_result()){;}
            
            // Obtener el total de vehículos
            $res_total = $this->conn->query("SELECT @p_total_vehiculos AS total");
            if ($res_total) {
                $total_vehiculos = (int)$res_total->fetch_assoc()['total'];
                $res_total->free();
            } else {
                $total_vehiculos = 0;
                 error_log("Error obteniendo @p_total_vehiculos: " . $this->conn->error);
            }
            return ['vehiculos' => $vehiculos, 'total' => $total_vehiculos];

        } else {
            error_log("Error al ejecutar sp_get_vehiculos_listado: " . $this->conn->error . " (SQL: " . $sql . ")");
            return ['vehiculos' => [], 'total' => 0, 'error' => 'Error en la consulta de vehículos.'];
        }
    }
    public function getVehiculoDetalle($veh_id)
    {
        if (!$this->conn) return false;

        $veh_id_esc = $this->conn->real_escape_string($veh_id);
        $sql = "CALL sp_get_vehiculo_detalle($veh_id_esc)";
        
        $resultado = $this->conexion_obj->ejecutarSP($sql);
        $vehiculo_detalle = null;

        if ($resultado && $resultado instanceof mysqli_result) {
            if ($resultado->num_rows > 0) {
                $vehiculo_detalle = $resultado->fetch_assoc();
            }
            $resultado->free();
            // Limpiar para asegurar que la conexión esté lista para la siguiente query (de imágenes)
            while($this->conn->more_results() && $this->conn->next_result()){;}
        } elseif ($resultado === false) {
            error_log("Error al ejecutar sp_get_vehiculo_detalle para veh_id $veh_id_esc: " . $this->conn->error . " (SQL: $sql)");
            return false;
        }
        return $vehiculo_detalle; // Puede ser null si no se encontró el vehículo
    }

}

?>