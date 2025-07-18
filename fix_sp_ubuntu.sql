-- Script para corregir el stored procedure en Ubuntu
-- Ejecutar este script en el servidor Ubuntu para corregir el problema

USE sistemaventaautos;

-- Eliminar el stored procedure existente
DROP PROCEDURE IF EXISTS sp_get_vehiculos_listado;

-- Crear la versión correcta del stored procedure
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
    -- Declarar variables
    DECLARE where_clause TEXT DEFAULT '';
    DECLARE params_count INT DEFAULT 0;
    
    -- Construir WHERE clause dinámicamente
    SET where_clause = 'WHERE v.veh_estado != "vendido"';
    
    -- Filtro por condición
    IF p_veh_condicion != 'todos' THEN
        SET where_clause = CONCAT(where_clause, ' AND v.veh_condicion = "', p_veh_condicion, '"');
    END IF;
    
    -- Filtro por marca
    IF p_mar_id IS NOT NULL AND p_mar_id > 0 THEN
        SET where_clause = CONCAT(where_clause, ' AND v.mar_id = ', p_mar_id);
    END IF;
    
    -- Filtro por modelo
    IF p_mod_id IS NOT NULL AND p_mod_id > 0 THEN
        SET where_clause = CONCAT(where_clause, ' AND v.mod_id = ', p_mod_id);
    END IF;
    
    -- Filtro por tipo de vehículo
    IF p_tiv_id IS NOT NULL AND p_tiv_id > 0 THEN
        SET where_clause = CONCAT(where_clause, ' AND v.tiv_id = ', p_tiv_id);
    END IF;
    
    -- Filtro por precio mínimo
    IF p_precio_min IS NOT NULL THEN
        SET where_clause = CONCAT(where_clause, ' AND v.veh_precio >= ', p_precio_min);
    END IF;
    
    -- Filtro por precio máximo
    IF p_precio_max IS NOT NULL THEN
        SET where_clause = CONCAT(where_clause, ' AND v.veh_precio <= ', p_precio_max);
    END IF;
    
    -- Filtro por año mínimo
    IF p_anio_min IS NOT NULL THEN
        SET where_clause = CONCAT(where_clause, ' AND v.veh_anio >= ', p_anio_min);
    END IF;
    
    -- Filtro por año máximo
    IF p_anio_max IS NOT NULL THEN
        SET where_clause = CONCAT(where_clause, ' AND v.veh_anio <= ', p_anio_max);
    END IF;
    
    -- Filtro por kilometraje máximo
    IF p_kilometraje_max IS NOT NULL THEN
        SET where_clause = CONCAT(where_clause, ' AND v.veh_kilometraje <= ', p_kilometraje_max);
    END IF;
    
    -- Filtro por provincia
    IF p_ubicacion_provincia IS NOT NULL AND p_ubicacion_provincia != '' THEN
        SET where_clause = CONCAT(where_clause, ' AND v.veh_ubicacion_provincia = "', p_ubicacion_provincia, '"');
    END IF;
    
    -- Construir y ejecutar la consulta principal
    SET @sql_query = CONCAT(
        'SELECT SQL_CALC_FOUND_ROWS ',
        'v.veh_id, v.veh_anio, v.veh_kilometraje, v.veh_precio, ',
        'v.veh_ubicacion_ciudad, v.veh_ubicacion_provincia, ',
        'm.mar_nombre, mo.mod_nombre, tv.tiv_nombre, ',
        '(SELECT ima_url FROM imagenesvehiculo iv WHERE iv.veh_id = v.veh_id AND iv.ima_es_principal = TRUE LIMIT 1) AS imagen_principal_url ',
        'FROM vehiculos v ',
        'JOIN marcas m ON v.mar_id = m.mar_id ',
        'JOIN modelos mo ON v.mod_id = mo.mod_id ',
        'JOIN tiposvehiculo tv ON v.tiv_id = tv.tiv_id ',
        where_clause,
        ' ORDER BY v.veh_id DESC ',
        'LIMIT ', p_items_por_pagina, ' OFFSET ', p_offset
    );
    
    -- Preparar y ejecutar la consulta
    PREPARE stmt FROM @sql_query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    
    -- Obtener el total de registros
    SELECT FOUND_ROWS() INTO p_total_vehiculos;
    
END //
DELIMITER ;

-- Verificar que el procedimiento se creó correctamente
SELECT 'Stored procedure sp_get_vehiculos_listado recreado exitosamente' AS status;