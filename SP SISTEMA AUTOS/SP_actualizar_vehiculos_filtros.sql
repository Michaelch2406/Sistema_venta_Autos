-- Script para actualizar el procedimiento almacenado de filtros de vehículos
-- Ejecutar este script para arreglar los filtros de búsqueda en autos_usados.php y autos_nuevos.php

USE SistemaVentaAutos;

DROP PROCEDURE IF EXISTS sp_get_vehiculos_listado;
DELIMITER //
CREATE PROCEDURE sp_get_vehiculos_listado(
    IN p_veh_condicion ENUM('nuevo', 'usado', 'todos'), -- 'todos' para no filtrar por condición
    IN p_mar_id INT,            -- 0 o NULL para no filtrar por marca
    IN p_mod_id INT,            -- 0 o NULL para no filtrar por modelo
    IN p_tiv_id INT,            -- 0 o NULL para no filtrar por tipo
    IN p_precio_min DECIMAL(12,2),
    IN p_precio_max DECIMAL(12,2),
    IN p_anio_min INT,
    IN p_anio_max INT,
    IN p_kilometraje_max INT,   -- Filtro de kilometraje máximo
    IN p_ubicacion_provincia VARCHAR(100), -- Vacío o NULL para no filtrar
    IN p_items_por_pagina INT,
    IN p_offset INT,
    OUT p_total_vehiculos INT -- Total de vehículos que coinciden con los filtros (para paginación)
)
BEGIN
    -- Construir la consulta base
    SET @sql_query = CONCAT(
        'SELECT SQL_CALC_FOUND_ROWS ',
        'v.veh_id, v.veh_anio, v.veh_kilometraje, v.veh_precio, v.veh_ubicacion_ciudad, v.veh_ubicacion_provincia, ',
        'm.mar_nombre, mo.mod_nombre, tv.tiv_nombre, ',
        '(SELECT ima_url FROM ImagenesVehiculo iv WHERE iv.veh_id = v.veh_id AND iv.ima_es_principal = TRUE LIMIT 1) AS imagen_principal_url ',
        'FROM vehiculos v ',
        'JOIN Marcas m ON v.mar_id = m.mar_id ',
        'JOIN Modelos mo ON v.mod_id = mo.mod_id ',
        'JOIN TiposVehiculo tv ON v.tiv_id = tv.tiv_id ',
        'WHERE v.veh_estado = ''disponible'' '
    );

    -- Aplicar filtros
    IF p_veh_condicion != 'todos' THEN
        SET @sql_query = CONCAT(@sql_query, ' AND v.veh_condicion = ''', p_veh_condicion, '''');
    END IF;
    IF p_mar_id IS NOT NULL AND p_mar_id > 0 THEN
        SET @sql_query = CONCAT(@sql_query, ' AND v.mar_id = ', p_mar_id);
    END IF;
    IF p_mod_id IS NOT NULL AND p_mod_id > 0 THEN
        SET @sql_query = CONCAT(@sql_query, ' AND v.mod_id = ', p_mod_id);
    END IF;
    IF p_tiv_id IS NOT NULL AND p_tiv_id > 0 THEN
        SET @sql_query = CONCAT(@sql_query, ' AND v.tiv_id = ', p_tiv_id);
    END IF;
    IF p_precio_min IS NOT NULL THEN
        SET @sql_query = CONCAT(@sql_query, ' AND v.veh_precio >= ', p_precio_min);
    END IF;
    IF p_precio_max IS NOT NULL THEN
        SET @sql_query = CONCAT(@sql_query, ' AND v.veh_precio <= ', p_precio_max);
    END IF;
    IF p_anio_min IS NOT NULL THEN
        SET @sql_query = CONCAT(@sql_query, ' AND v.veh_anio >= ', p_anio_min);
    END IF;
    IF p_anio_max IS NOT NULL THEN
        SET @sql_query = CONCAT(@sql_query, ' AND v.veh_anio <= ', p_anio_max);
    END IF;
    IF p_kilometraje_max IS NOT NULL THEN
        SET @sql_query = CONCAT(@sql_query, ' AND v.veh_kilometraje <= ', p_kilometraje_max);
    END IF;
    IF p_ubicacion_provincia IS NOT NULL AND p_ubicacion_provincia != '' THEN
        SET @sql_query = CONCAT(@sql_query, ' AND v.veh_ubicacion_provincia = ''', p_ubicacion_provincia, '''');
    END IF;

    -- Orden y Paginación
    SET @sql_query = CONCAT(@sql_query, ' ORDER BY v.veh_fecha_publicacion DESC, v.veh_creado_en DESC');
    IF p_items_por_pagina IS NOT NULL AND p_offset IS NOT NULL THEN
        SET @sql_query = CONCAT(@sql_query, ' LIMIT ', p_items_por_pagina, ' OFFSET ', p_offset);
    END IF;

    -- Ejecutar la consulta
    PREPARE stmt FROM @sql_query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

    -- Obtener el total de vehículos
    SET p_total_vehiculos = FOUND_ROWS();
END //
DELIMITER ;