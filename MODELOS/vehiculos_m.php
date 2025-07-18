<?php
require_once "./../CONFIG/Conexion.php";

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
        $usu_id_gestor = isset($datos['usu_id_gestor']) && filter_var(trim($datos['usu_id_gestor']), FILTER_VALIDATE_INT) 
        ? (int)$datos['usu_id_gestor'] 
        : "NULL";
        $veh_condicion = $this->conn->real_escape_string($datos['veh_condicion']);
        $veh_anio = $this->conn->real_escape_string($datos['veh_anio']);
        $veh_precio = $this->conn->real_escape_string($datos['veh_precio']);
        $veh_vin = isset($datos['veh_vin']) && trim($datos['veh_vin']) !== '' ? "'" . $this->conn->real_escape_string(trim($datos['veh_vin'])) . "'" : "NULL";
        $veh_placa = isset($datos['veh_placa']) && trim($datos['veh_placa']) !== '' ? "'" . $this->conn->real_escape_string(strtoupper(trim($datos['veh_placa']))) . "'" : "NULL";
        
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
            $veh_precio, $veh_vin, $veh_placa, '$veh_ubicacion_provincia', '$veh_ubicacion_ciudad', $p_veh_placa_provincia_origen,
            $p_veh_ultimo_digito_placa,
            '$veh_color_exterior', $veh_color_interior, '$veh_detalles_motor', $veh_tipo_transmision,
            $veh_traccion, $veh_tipo_vidrios, $veh_tipo_combustible, $veh_tipo_direccion, $veh_sistema_climatizacion,
            '$veh_descripcion',
            $veh_detalles_extra_str,
            '$veh_fecha_publicacion',
            @p_veh_id_insertado, @p_resultado, @p_mensaje
        )";
        
        if (!$this->conn->query($sql)) {
    // Volcamos el error y el SQL en dos líneas para poder copiarlo bien
    error_log("[SP ERROR] MySQL Error: " . $this->conn->errno . " - " . $this->conn->error);
    error_log("[SP ERROR] SQL ejecutado: " . preg_replace('/\s+/', ' ', $sql));
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

    /**
     * Obtiene una lista de vehículos destacados llamando a un Stored Procedure.
     *
     * @param string $condicion 'nuevo' o 'usado'.
     * @param int $limite El número máximo de vehículos a devolver.
     * @return array Un array con los datos de los vehículos o un array vacío.
     */
    public function getVehiculosDestacados($condicion, $limite = 3)
    {
        if (!$this->conn) {
            return [];
        }

        // --- Preparar y ejecutar la llamada al SP ---
        $sql = "CALL sp_get_vehiculos_destacados(?, ?)";

        try {
            $stmt = $this->conn->prepare($sql);
            if ($stmt === false) {
                // Mensaje de error más específico
                error_log("Error al preparar la llamada a sp_get_vehiculos_destacados: " . $this->conn->error);
                return [];
            }
            
            // Los parámetros se enlazan de la misma manera
            $stmt->bind_param("si", $condicion, $limite);
            $stmt->execute();
            $resultado = $stmt->get_result();
            
            $vehiculos = [];
            while ($fila = $resultado->fetch_assoc()) {
                // Preparamos la URL de la imagen para el frontend
                if (!empty($fila['imagen_principal_url']) && strpos($fila['imagen_principal_url'], 'PUBLIC/') === 0) {
                    $fila['imagen_principal_url'] = '../' . $fila['imagen_principal_url'];
                } else {
                    $fila['imagen_principal_url'] = '../PUBLIC/Img/auto_placeholder.png';
                }
                $vehiculos[] = $fila;
            }
            
            $stmt->close();
            return $vehiculos;

        } catch (Exception $e) {
            // Mensaje de error más específico
            error_log("Excepción al llamar a sp_get_vehiculos_destacados: " . $e->getMessage());
            return [];
        }
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
        $kilometraje_max = isset($filtros['kilometraje_max']) && filter_var($filtros['kilometraje_max'], FILTER_VALIDATE_INT) ? (int)$filtros['kilometraje_max'] : 'NULL';
        $provincia = isset($filtros['provincia']) && !empty($filtros['provincia']) ? "'" . $this->conn->real_escape_string($filtros['provincia']) . "'" : 'NULL';
        
        $items_por_pagina = isset($filtros['items_por_pagina']) && filter_var($filtros['items_por_pagina'], FILTER_VALIDATE_INT) ? (int)$filtros['items_por_pagina'] : 12; // Default 12 items
        $pagina_actual = isset($filtros['pagina']) && filter_var($filtros['pagina'], FILTER_VALIDATE_INT) ? (int)$filtros['pagina'] : 1;
        $offset = ($pagina_actual - 1) * $items_por_pagina;

        // Intentar con la versión actual del SP (13 parámetros)
        $sql = "CALL sp_get_vehiculos_listado(
            '$condicion', $mar_id, $mod_id, $tiv_id,
            $precio_min, $precio_max, $anio_min, $anio_max, $kilometraje_max, $provincia,
            $items_por_pagina, $offset,
            @p_total_vehiculos
        )";
        
        $resultado_sp = $this->conn_obj->ejecutarSP($sql);
        
        // Si falla el SP, usar consulta directa como fallback
        if (!$resultado_sp) {
            error_log("SP falló, usando consulta directa como fallback");
            return $this->getVehiculosListadoDirecto($filtros);
        }
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

    /**
     * Método fallback que usa consulta directa cuando el SP falla
     */
    private function getVehiculosListadoDirecto($filtros) {
        if (!$this->conn) return ['vehiculos' => [], 'total' => 0, 'error' => 'No hay conexión a BD'];

        // Reconstruir filtros
        $condicion = isset($filtros['condicion']) ? $filtros['condicion'] : 'todos';
        $mar_id = isset($filtros['mar_id']) ? (int)$filtros['mar_id'] : null;
        $mod_id = isset($filtros['mod_id']) ? (int)$filtros['mod_id'] : null;
        $tiv_id = isset($filtros['tiv_id']) ? (int)$filtros['tiv_id'] : null;
        $precio_min = isset($filtros['precio_min']) ? (float)$filtros['precio_min'] : null;
        $precio_max = isset($filtros['precio_max']) ? (float)$filtros['precio_max'] : null;
        $anio_min = isset($filtros['anio_min']) ? (int)$filtros['anio_min'] : null;
        $anio_max = isset($filtros['anio_max']) ? (int)$filtros['anio_max'] : null;
        $kilometraje_max = isset($filtros['kilometraje_max']) ? (int)$filtros['kilometraje_max'] : null;
        $provincia = isset($filtros['provincia']) ? $filtros['provincia'] : null;
        $items_por_pagina = isset($filtros['items_por_pagina']) ? (int)$filtros['items_por_pagina'] : 12;
        $pagina_actual = isset($filtros['pagina']) ? (int)$filtros['pagina'] : 1;
        $offset = ($pagina_actual - 1) * $items_por_pagina;

        // Construir consulta WHERE
        $whereConditions = [];
        $params = [];
        $types = "";

        if ($condicion !== 'todos') {
            $whereConditions[] = "v.veh_condicion = ?";
            $params[] = $condicion;
            $types .= "s";
        }

        if ($mar_id) {
            $whereConditions[] = "v.mar_id = ?";
            $params[] = $mar_id;
            $types .= "i";
        }

        if ($mod_id) {
            $whereConditions[] = "v.mod_id = ?";
            $params[] = $mod_id;
            $types .= "i";
        }

        if ($tiv_id) {
            $whereConditions[] = "v.tiv_id = ?";
            $params[] = $tiv_id;
            $types .= "i";
        }

        if ($precio_min !== null) {
            $whereConditions[] = "v.veh_precio >= ?";
            $params[] = $precio_min;
            $types .= "d";
        }

        if ($precio_max !== null) {
            $whereConditions[] = "v.veh_precio <= ?";
            $params[] = $precio_max;
            $types .= "d";
        }

        if ($anio_min !== null) {
            $whereConditions[] = "v.veh_anio >= ?";
            $params[] = $anio_min;
            $types .= "i";
        }

        if ($anio_max !== null) {
            $whereConditions[] = "v.veh_anio <= ?";
            $params[] = $anio_max;
            $types .= "i";
        }

        if ($kilometraje_max !== null) {
            $whereConditions[] = "v.veh_kilometraje <= ?";
            $params[] = $kilometraje_max;
            $types .= "i";
        }

        if ($provincia) {
            $whereConditions[] = "v.veh_ubicacion_provincia = ?";
            $params[] = $provincia;
            $types .= "s";
        }

        // Agregar filtro de estado para excluir vehículos vendidos
        $whereConditions[] = "v.veh_estado != 'vendido'";

        $whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";

        // Consulta principal
        $sql = "SELECT v.veh_id, v.veh_anio, v.veh_kilometraje, v.veh_precio, 
                       v.veh_ubicacion_ciudad, v.veh_ubicacion_provincia,
                       m.mar_nombre, mo.mod_nombre, tv.tiv_nombre,
                       (SELECT ima_url FROM imagenesvehiculo iv 
                        WHERE iv.veh_id = v.veh_id AND iv.ima_es_principal = TRUE 
                        LIMIT 1) AS imagen_principal_url
                FROM vehiculos v
                JOIN marcas m ON v.mar_id = m.mar_id
                JOIN modelos mo ON v.mod_id = mo.mod_id
                JOIN tiposvehiculo tv ON v.tiv_id = tv.tiv_id
                $whereClause
                ORDER BY v.veh_id DESC
                LIMIT ? OFFSET ?";

        $params[] = $items_por_pagina;
        $params[] = $offset;
        $types .= "ii";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Error preparando consulta directa: " . $this->conn->error);
            return ['vehiculos' => [], 'total' => 0, 'error' => 'Error en la consulta de vehículos.'];
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $vehiculos = [];
        while ($row = $result->fetch_assoc()) {
            $vehiculos[] = $row;
        }

        $stmt->close();

        // Consulta para contar total
        $countSql = "SELECT COUNT(*) as total
                     FROM vehiculos v
                     JOIN marcas m ON v.mar_id = m.mar_id
                     JOIN modelos mo ON v.mod_id = mo.mod_id
                     JOIN tiposvehiculo tv ON v.tiv_id = tv.tiv_id
                     $whereClause";

        $countStmt = $this->conn->prepare($countSql);
        if (!$countStmt) {
            error_log("Error preparando consulta de conteo: " . $this->conn->error);
            return ['vehiculos' => $vehiculos, 'total' => count($vehiculos)];
        }

        // Remover los últimos 2 parámetros (LIMIT y OFFSET) para el conteo
        $countParams = array_slice($params, 0, -2);
        $countTypes = substr($types, 0, -2);

        if (!empty($countParams)) {
            $countStmt->bind_param($countTypes, ...$countParams);
        }

        $countStmt->execute();
        $countResult = $countStmt->get_result();
        $total = $countResult->fetch_assoc()['total'];
        $countStmt->close();

        return ['vehiculos' => $vehiculos, 'total' => (int)$total];
    }
    public function getVehiculoDetalle($veh_id)
    {
        if (!$this->conn) return false;

        $veh_id_esc = $this->conn->real_escape_string($veh_id);
        $sql = "CALL sp_get_vehiculo_detalle($veh_id_esc)";
        
        // Corregido: $this->conexion_obj a $this->conn_obj
        $resultado = $this->conn_obj->ejecutarSP($sql);
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

    public function getTodosLosVehiculosAdmin()
    {
        if (!$this->conn) {
            error_log("getTodosLosVehiculosAdmin: No hay conexión a BD.");
            return false;
        }

        // Este SP debe ser creado en la base de datos.
        // Debe devolver todos los vehículos con la información necesaria para la tabla de administración.
        // Ej: veh_id, imagen_principal_url, mar_nombre, mod_nombre, veh_anio, 
        // usu_nombre_completo (del publicador), veh_precio, veh_condicion, 
        // veh_estado_actual, veh_fecha_publicacion, veh_ubicacion_ciudad, veh_ubicacion_provincia.
        $sql = "CALL sp_get_todos_vehiculos_admin()"; 
        
        $resultado = $this->conn_obj->ejecutarSP($sql);
        $vehiculos_admin = [];

        if ($resultado && $resultado instanceof mysqli_result) {
            if ($resultado->num_rows > 0) {
                while ($fila = $resultado->fetch_assoc()) {
                    // Ajustar la URL de la imagen si es necesario, similar a otros métodos.
                    if (!empty($fila['imagen_principal_url']) && strpos($fila['imagen_principal_url'], 'PUBLIC/') === 0) {
                        // La URL ya está bien para el backend, el JS la prefija con '../' si es necesario
                        // $fila['imagen_principal_url'] = '../' . $fila['imagen_principal_url']; 
                    } else if (empty($fila['imagen_principal_url'])) {
                        // Si no hay imagen, se podría establecer una placeholder aquí, pero es mejor que el SP la devuelva o el JS la maneje.
                        // $fila['imagen_principal_url'] = 'PUBLIC/Img/auto_placeholder.png';
                    }
                    $vehiculos_admin[] = $fila;
                }
            }
            $resultado->free();
            // Limpiar resultados si el SP devuelve más de uno (no debería ser el caso aquí)
            while($this->conn->more_results() && $this->conn->next_result()){
                if($rs = $this->conn->store_result()){ $rs->free(); }
            }
        } elseif ($resultado === false) {
            error_log("Error al ejecutar sp_get_todos_vehiculos_admin: " . ($this->conn->error ?? 'Error desconocido') . " (SQL: " . $sql . ")");
            return false; 
        }
        // Devuelve un array vacío si no hay vehículos, lo cual es un resultado válido.
        return $vehiculos_admin;
    }

    public function eliminarVehiculo($veh_id, $usu_id_admin_que_elimina)
    {
        if (!$this->conn) {
            return ['resultado' => 0, 'mensaje' => 'Error de conexión a la base de datos.'];
        }

        $veh_id_esc = $this->conn->real_escape_string($veh_id);
        $usu_id_admin_esc = $this->conn->real_escape_string($usu_id_admin_que_elimina);

        // Se debe crear un SP `sp_eliminar_vehiculo_admin` que maneje la lógica de eliminación.
        // Esto podría incluir:
        // 1. Verificar si el vehículo existe.
        // 2. Eliminar registros relacionados (imágenes, cotizaciones asociadas si la política es eliminarlas).
        // 3. Eliminar el vehículo de la tabla principal.
        // 4. Registrar la acción de eliminación (opcional, en una tabla de auditoría).
        // El SP debe devolver un resultado y un mensaje.
        $sql = "CALL sp_eliminar_vehiculo_admin(
            $veh_id_esc, 
            $usu_id_admin_esc, 
            @p_resultado, 
            @p_mensaje
        )";

        if (!$this->conn->query($sql)) {
            error_log("Error al llamar a sp_eliminar_vehiculo_admin: " . $this->conn->error . " (SQL: $sql)");
            return ['resultado' => 0, 'mensaje' => 'Error técnico al eliminar vehículo (llamada SP).'];
        }

        $res = $this->conn->query("SELECT @p_resultado AS resultado, @p_mensaje AS mensaje");
        if (!$res) {
            error_log("Error al obtener resultados de sp_eliminar_vehiculo_admin: " . $this->conn->error);
            return ['resultado' => 0, 'mensaje' => 'Error técnico al obtener resultados del SP de eliminación.'];
        }
        $out_params = $res->fetch_assoc();
        $res->free();
        
        while($this->conn->more_results() && $this->conn->next_result()){
            if($rs = $this->conn->store_result()){ $rs->free(); }
        }
        return $out_params;
    }

    public function getDetallesVehiculoParaEdicionDB($veh_id)
    {
        if (!$this->conn) {
            error_log("getDetallesVehiculoParaEdicionDB: No hay conexión a BD.");
            return ['error' => 'Error de conexión interna.'];
        }
        $veh_id_esc = $this->conn->real_escape_string($veh_id);

        // 1. Obtener los detalles básicos del vehículo usando el SP existente o uno modificado.
        // Asumimos que sp_get_vehiculo_detalle ya devuelve la mayoría de los campos necesarios.
        // Si sp_get_vehiculo_detalle no es suficiente, se necesitaría un SP específico para edición
        // que devuelva TODOS los campos editables del formulario.
        $sql_vehiculo = "CALL sp_get_vehiculo_detalle($veh_id_esc)"; // O sp_get_vehiculo_detalles_edicion si se crea
        
        $resultado_vehiculo = $this->conn_obj->ejecutarSP($sql_vehiculo);
        $detalles_vehiculo = null;

        if ($resultado_vehiculo && $resultado_vehiculo instanceof mysqli_result) {
            if ($resultado_vehiculo->num_rows > 0) {
                $detalles_vehiculo = $resultado_vehiculo->fetch_assoc();
            }
            $resultado_vehiculo->free();
            while($this->conn->more_results() && $this->conn->next_result()){
                if($rs = $this->conn->store_result()){ $rs->free(); }
            }
        } elseif ($resultado_vehiculo === false) {
            error_log("Error al ejecutar SP para detalles de vehículo (edición) para veh_id $veh_id_esc: " . ($this->conn->error ?? 'Error desconocido') . " (SQL: $sql_vehiculo)");
            return ['error' => 'Error al obtener datos del vehículo.'];
        }

        if (!$detalles_vehiculo) {
            return ['error' => 'Vehículo no encontrado.'];
        }

        // 2. Obtener las imágenes del vehículo.
        // Es buena práctica tener un modelo separado para imágenes, como ImagenesVehiculo_M.
        // Si no existe, se puede incluir la lógica aquí o llamarla desde el controlador/AJAX.
        // Por ahora, asumimos que existe ImagenesVehiculo_M y su método getImagenesPorVehiculo.
        $imagenes_model = new ImagenesVehiculo_M(); // Asegúrate que este modelo esté cargado o requerido.
        $imagenes = $imagenes_model->getImagenesPorVehiculo($veh_id_esc);

        if ($imagenes === false) {
            // No es un error fatal si no se pueden cargar imágenes, pero se debe loguear.
            error_log("getDetallesVehiculoParaEdicionDB: No se pudieron cargar las imágenes para veh_id $veh_id_esc, pero se devuelven los detalles del vehículo.");
            // Podríamos devolver el error de imágenes si es crítico: return ['error' => 'Error al obtener imágenes del vehículo.'];
        }
        
        // Combinar los detalles del vehículo y sus imágenes.
        // El JS esperará algo como: { vehiculo: {...}, imagenes: [...] }
        return [
            'vehiculo' => $detalles_vehiculo,
            'imagenes' => $imagenes ?: [] // Devolver array vacío si no hay imágenes o hubo error leve.
        ];
    }

    public function actualizarVehiculoDB(
    $veh_id,
    $datos_vehiculo,
    $nuevas_imagenes_subidas,
    $ids_imagenes_a_eliminar,
    $imagen_principal_actual_id,
    $nueva_imagen_principal_nombre_temporal,
    $usu_id_editor // Cambiado a un nombre más genérico
) {
    if (!$this->conn) {
        return ['resultado' => 0, 'mensaje' => 'Error de conexión a la base de datos.'];
    }

    $this->conn->begin_transaction();

    try {
        // === PASO 1: Actualizar los datos de texto del vehículo (sin cambios en esta parte) ===
        $mar_id = $this->conn->real_escape_string($datos_vehiculo['mar_id']);
        $mod_id = $this->conn->real_escape_string($datos_vehiculo['mod_id']);
        // ... (todo el resto de la sanitización de datos que ya tienes es correcta)
        $tiv_id = $this->conn->real_escape_string($datos_vehiculo['tiv_id']);
        $veh_subtipo_vehiculo = isset($datos_vehiculo['veh_subtipo_vehiculo']) && trim($datos_vehiculo['veh_subtipo_vehiculo']) !== '' ? "'" . $this->conn->real_escape_string(trim($datos_vehiculo['veh_subtipo_vehiculo'])) . "'" : "NULL";
        $veh_condicion = $this->conn->real_escape_string($datos_vehiculo['veh_condicion']);
        $veh_anio = $this->conn->real_escape_string($datos_vehiculo['veh_anio']);
        $veh_precio = $this->conn->real_escape_string($datos_vehiculo['veh_precio']);
        $veh_vin = isset($datos_vehiculo['veh_vin']) && trim($datos_vehiculo['veh_vin']) !== '' ? "'" . $this->conn->real_escape_string(trim($datos_vehiculo['veh_vin'])) . "'" : "NULL";
        $veh_placa = isset($datos_vehiculo['veh_placa']) && trim($datos_vehiculo['veh_placa']) !== '' ? "'" . $this->conn->real_escape_string(strtoupper(trim($datos_vehiculo['veh_placa']))) . "'" : "NULL";

        $p_veh_kilometraje = "NULL";
        $p_veh_placa_provincia_origen = "NULL";
        $p_veh_ultimo_digito_placa = "NULL";

        if ($veh_condicion == 'nuevo') {
            $p_veh_kilometraje = "0";
        } else {
            $p_veh_kilometraje = (isset($datos_vehiculo['veh_kilometraje']) && trim($datos_vehiculo['veh_kilometraje']) !== '') ? "'" . $this->conn->real_escape_string(trim($datos_vehiculo['veh_kilometraje'])) . "'" : "NULL";
            $p_veh_placa_provincia_origen = (isset($datos_vehiculo['veh_placa_provincia_origen']) && trim($datos_vehiculo['veh_placa_provincia_origen']) !== '') ? "'" . $this->conn->real_escape_string(trim($datos_vehiculo['veh_placa_provincia_origen'])) . "'" : "NULL";
            $p_veh_ultimo_digito_placa = (isset($datos_vehiculo['veh_ultimo_digito_placa']) && trim($datos_vehiculo['veh_ultimo_digito_placa']) !== '') ? "'" . $this->conn->real_escape_string(trim($datos_vehiculo['veh_ultimo_digito_placa'])) . "'" : "NULL";
        }
        
        $veh_ubicacion_provincia = $this->conn->real_escape_string($datos_vehiculo['veh_ubicacion_provincia']);
        $veh_ubicacion_ciudad = $this->conn->real_escape_string($datos_vehiculo['veh_ubicacion_ciudad']);
        $veh_color_exterior = $this->conn->real_escape_string(trim($datos_vehiculo['veh_color_exterior']));
        $veh_color_interior = isset($datos_vehiculo['veh_color_interior']) && trim($datos_vehiculo['veh_color_interior']) !== '' ? "'" . $this->conn->real_escape_string(trim($datos_vehiculo['veh_color_interior'])) . "'" : "NULL";
        $veh_detalles_motor = $this->conn->real_escape_string(trim($datos_vehiculo['veh_detalles_motor']));
        $veh_tipo_transmision = isset($datos_vehiculo['veh_tipo_transmision']) && trim($datos_vehiculo['veh_tipo_transmision']) !== '' ? "'" . $this->conn->real_escape_string(trim($datos_vehiculo['veh_tipo_transmision'])) . "'" : "NULL";
        $veh_traccion = isset($datos_vehiculo['veh_traccion']) && trim($datos_vehiculo['veh_traccion']) !== '' ? "'" . $this->conn->real_escape_string(trim($datos_vehiculo['veh_traccion'])) . "'" : "NULL";
        $veh_tipo_vidrios = isset($datos_vehiculo['veh_tipo_vidrios']) && trim($datos_vehiculo['veh_tipo_vidrios']) !== '' ? "'" . $this->conn->real_escape_string(trim($datos_vehiculo['veh_tipo_vidrios'])) . "'" : "NULL";
        $veh_tipo_combustible = isset($datos_vehiculo['veh_tipo_combustible']) && trim($datos_vehiculo['veh_tipo_combustible']) !== '' ? "'" . $this->conn->real_escape_string(trim($datos_vehiculo['veh_tipo_combustible'])) . "'" : "NULL";
        $veh_tipo_direccion = isset($datos_vehiculo['veh_tipo_direccion']) && trim($datos_vehiculo['veh_tipo_direccion']) !== '' ? "'" . $this->conn->real_escape_string(trim($datos_vehiculo['veh_tipo_direccion'])) . "'" : "NULL";
        $veh_sistema_climatizacion = isset($datos_vehiculo['veh_sistema_climatizacion']) && trim($datos_vehiculo['veh_sistema_climatizacion']) !== '' ? "'" . $this->conn->real_escape_string(trim($datos_vehiculo['veh_sistema_climatizacion'])) . "'" : "NULL";
        $veh_descripcion = $this->conn->real_escape_string(trim($datos_vehiculo['veh_descripcion']));
        $detalles_extra_array = isset($datos_vehiculo['veh_detalles_extra']) && is_array($datos_vehiculo['veh_detalles_extra']) ? $datos_vehiculo['veh_detalles_extra'] : [];
        $veh_detalles_extra_str = !empty($detalles_extra_array) ? "'" . $this->conn->real_escape_string(implode(', ', $detalles_extra_array)) . "'" : "NULL";
        $veh_fecha_publicacion = $this->conn->real_escape_string($datos_vehiculo['veh_fecha_publicacion']);

        $sql_update_vehiculo = "CALL sp_actualizar_vehiculo(
            {$veh_id}, {$mar_id}, {$mod_id}, {$tiv_id}, {$veh_subtipo_vehiculo}, '{$veh_condicion}', {$veh_anio}, {$p_veh_kilometraje},
            {$veh_precio}, {$veh_vin}, {$veh_placa}, '{$veh_ubicacion_provincia}', '{$veh_ubicacion_ciudad}', {$p_veh_placa_provincia_origen},
            {$p_veh_ultimo_digito_placa}, '{$veh_color_exterior}', {$veh_color_interior}, '{$veh_detalles_motor}', {$veh_tipo_transmision},
            {$veh_traccion}, {$veh_tipo_vidrios}, {$veh_tipo_combustible}, {$veh_tipo_direccion}, {$veh_sistema_climatizacion},
            '{$veh_descripcion}', {$veh_detalles_extra_str}, '{$veh_fecha_publicacion}', {$usu_id_editor},
            @p_update_resultado, @p_update_mensaje
        )";

        if (!$this->conn->query($sql_update_vehiculo)) {
            throw new Exception("Error al ejecutar SP de actualización de vehículo: " . $this->conn->error);
        }
        $res_update = $this->conn->query("SELECT @p_update_resultado AS resultado, @p_update_mensaje AS mensaje");
        if (!$res_update) throw new Exception("Error obteniendo resultado de SP actualización: " . $this->conn->error);
        $out_update_params = $res_update->fetch_assoc();
        $res_update->free();
        while($this->conn->more_results() && $this->conn->next_result()){ if($rs = $this->conn->store_result()){ $rs->free(); } }

        if (!isset($out_update_params['resultado']) || $out_update_params['resultado'] != 1) {
            throw new Exception($out_update_params['mensaje'] ?? 'Error desconocido al actualizar datos del vehículo.');
        }

        // ====== INICIO DE CAMBIOS IMPORTANTES (MANEJO DE IMÁGENES) ======
        $upload_dir_base = __DIR__ . '/../PUBLIC/uploads/vehiculos/';
        $upload_dir_vehiculo = $upload_dir_base . $veh_id . '/';

        // === PASO 2: Eliminar imágenes marcadas (usando esta misma conexión) ===
        if (!empty($ids_imagenes_a_eliminar)) {
            foreach ($ids_imagenes_a_eliminar as $ima_id_str) {
                $ima_id = filter_var(trim($ima_id_str), FILTER_VALIDATE_INT);
                if ($ima_id) {
                    $stmt_get_url = $this->conn->prepare("SELECT ima_url FROM imagenesvehiculo WHERE ima_id = ? AND veh_id = ?");
                    $stmt_get_url->bind_param("ii", $ima_id, $veh_id);
                    $stmt_get_url->execute();
                    $res_url = $stmt_get_url->get_result();
                    if ($res_url->num_rows > 0) {
                        $img_data = $res_url->fetch_assoc();
                        $url_a_borrar_fisico = __DIR__ . '/../' . $img_data['ima_url'];
                         if (file_exists($url_a_borrar_fisico)) {
                            unlink($url_a_borrar_fisico);
                        }
                    }
                    $stmt_get_url->close();

                    $stmt_delete = $this->conn->prepare("DELETE FROM imagenesvehiculo WHERE ima_id = ?");
                    $stmt_delete->bind_param("i", $ima_id);
                    $stmt_delete->execute();
                    $stmt_delete->close();
                }
            }
        }
        
        // === PASO 3: Subir y guardar nuevas imágenes (usando esta misma conexión) ===
        $nombre_archivo_nueva_principal = null;
        if (!empty($nuevas_imagenes_subidas) && !empty($nuevas_imagenes_subidas['name'][0])) {
            if (!file_exists($upload_dir_vehiculo)) {
                if (!mkdir($upload_dir_vehiculo, 0775, true)) {
                    throw new Exception("Error Crítico: No se pudo crear el directorio de imágenes: " . $upload_dir_vehiculo);
                }
            }
            foreach ($nuevas_imagenes_subidas['name'] as $key => $name) {
                if ($nuevas_imagenes_subidas['error'][$key] == UPLOAD_ERR_OK) {
                    $tmp_name = $nuevas_imagenes_subidas['tmp_name'][$key];
                    $original_name = basename(filter_var($name, FILTER_SANITIZE_STRING));
                    $extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];

                    if (!in_array($extension, $allowed_extensions)) continue;

                    $safe_filename = uniqid('vehiculo_' . $veh_id . '_', true) . '.' . $extension;
                    $destination = $upload_dir_vehiculo . $safe_filename;

                    if (move_uploaded_file($tmp_name, $destination)) {
                        $url_relativa_db = 'PUBLIC/uploads/vehiculos/' . $veh_id . '/' . $safe_filename;
                        
                        // Determinar si esta nueva imagen es la principal
                        $es_nueva_principal = ($original_name === $nueva_imagen_principal_nombre_temporal);
                        if ($es_nueva_principal) {
                            $nombre_archivo_nueva_principal = $url_relativa_db;
                        }
                        
                        // Llamar al SP de inserción directamente usando LA MISMA CONEXIÓN
                        $es_principal_esc = $es_nueva_principal ? 'TRUE' : 'FALSE';
                        $ima_url_esc = $this->conn->real_escape_string($url_relativa_db);

                        $this->conn_obj->ejecutarSP("CALL sp_insertar_imagen_vehiculo({$veh_id}, '{$ima_url_esc}', {$es_principal_esc}, @p_ima_id_insertado, @p_resultado, @p_mensaje)");

                    } else { error_log("Error moviendo nueva imagen {$original_name}."); }
                }
            }
        }
        
        // === PASO 4: Establecer la imagen principal (si no fue una nueva) ===
        if (!$nombre_archivo_nueva_principal && $imagen_principal_actual_id) {
            $this->conn->query("UPDATE imagenesvehiculo SET ima_es_principal = FALSE WHERE veh_id = {$veh_id}");
            $this->conn->query("UPDATE imagenesvehiculo SET ima_es_principal = TRUE WHERE veh_id = {$veh_id} AND ima_id = {$imagen_principal_actual_id}");
        }

        // === PASO 5: Asegurarse de que SIEMPRE haya una imagen principal si existen imágenes ===
        $res_check_main = $this->conn->query("SELECT COUNT(*) as count_main FROM imagenesvehiculo WHERE veh_id = {$veh_id} AND ima_es_principal = TRUE");
        $count_main = (int)$res_check_main->fetch_assoc()['count_main'];
        if ($count_main === 0) {
            $res_any_img = $this->conn->query("SELECT ima_id FROM imagenesvehiculo WHERE veh_id = {$veh_id} ORDER BY ima_id ASC LIMIT 1");
            if ($res_any_img && $res_any_img->num_rows > 0) {
                $first_img_id = $res_any_img->fetch_assoc()['ima_id'];
                $this->conn->query("UPDATE imagenesvehiculo SET ima_es_principal = TRUE WHERE ima_id = {$first_img_id}");
            }
        }

            $this->conn->commit();
            return ['resultado' => 1, 'mensaje' => 'Vehículo actualizado exitosamente.'];

        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Error en actualizarVehiculoDB para veh_id {$veh_id}: " . $e->getMessage());
            return ['resultado' => 0, 'mensaje' => 'Error al actualizar el vehículo: ' . $e->getMessage()];
        }
    }
}

?>