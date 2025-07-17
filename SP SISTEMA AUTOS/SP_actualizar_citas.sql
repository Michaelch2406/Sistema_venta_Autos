-- Ejecutar este script para actualizar los procedimientos almacenados de citas
USE SISTEMAVENTAAUTOS;

-- SP para obtener citas por vehículos propios (NO ADMIN)
DROP PROCEDURE IF EXISTS `sp_obtener_citas_vehiculos_propios`;
DELIMITER //
CREATE PROCEDURE `sp_obtener_citas_vehiculos_propios`(
    IN `p_usu_id` INT,
    IN `p_filtro_texto` VARCHAR(255),
    IN `p_filtro_estado` VARCHAR(50),
    IN `p_filtro_fecha_desde` DATE,
    IN `p_filtro_fecha_hasta` DATE
)
BEGIN
    SET @sql_query = CONCAT("
        SELECT
            c.cit_id,
            c.usu_id_solicitante,
            c.veh_id,
            c.cit_detalles_vehiculo_solicitado,
            c.cit_mensaje,
            c.cit_estado,
            c.cit_fecha_disponibilidad,
            c.cit_hora_disponibilidad,
            c.cit_fecha_solicitud,
            c.cit_notas_admin, 
            CONCAT(u.usu_nombre, ' ', u.usu_apellido) AS nombre_solicitante, 
            u.usu_email AS email_solicitante,  
            v.veh_precio AS cit_monto_estimado,
            v.veh_estado,
            v.usu_id_gestor
        FROM
            citas c
        LEFT JOIN
            usuarios u ON c.usu_id_solicitante = u.usu_id 
        LEFT JOIN
            vehiculos v ON c.veh_id = v.veh_id
        WHERE v.usu_id_gestor = ", p_usu_id);

    IF p_filtro_texto IS NOT NULL AND p_filtro_texto != '' THEN
        SET @sanitized_filtro_texto = REPLACE(p_filtro_texto, "'", "''");
        SET @sql_query = CONCAT(@sql_query, " AND (c.cit_detalles_vehiculo_solicitado LIKE '%", @sanitized_filtro_texto, "%' OR CONCAT(u.usu_nombre, ' ', u.usu_apellido) LIKE '%", @sanitized_filtro_texto, "%' OR u.usu_email LIKE '%", @sanitized_filtro_texto, "%' OR c.cit_id LIKE '%", @sanitized_filtro_texto, "%')");
    END IF;

    IF p_filtro_estado IS NOT NULL AND p_filtro_estado != '' THEN
        SET @sql_query = CONCAT(@sql_query, " AND c.cit_estado = '", REPLACE(p_filtro_estado, "'", "''"), "'");
    END IF;

    IF p_filtro_fecha_desde IS NOT NULL THEN
        SET @sql_query = CONCAT(@sql_query, " AND DATE(c.cit_fecha_solicitud) >= '", p_filtro_fecha_desde, "'");
    END IF;

    IF p_filtro_fecha_hasta IS NOT NULL THEN
        SET @sql_query = CONCAT(@sql_query, " AND DATE(c.cit_fecha_solicitud) <= '", p_filtro_fecha_hasta, "'");
    END IF;

    SET @sql_query = CONCAT(@sql_query, " ORDER BY c.cit_fecha_solicitud DESC");

    PREPARE stmt FROM @sql_query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END //
DELIMITER ;

-- Actualizar SP para obtener detalle de cita (incluir estado del vehículo)
DROP PROCEDURE IF EXISTS `sp_obtener_detalle_cita`;
DELIMITER //
CREATE PROCEDURE `sp_obtener_detalle_cita`(IN `p_cit_id` INT)
BEGIN
    SELECT
        c.cit_id,
        c.usu_id_solicitante,
        c.veh_id,
        c.cit_detalles_vehiculo_solicitado,
        c.cit_mensaje,
        c.cit_estado,
        c.cit_fecha_disponibilidad,
        c.cit_hora_disponibilidad,
        c.cit_fecha_solicitud,
        c.cit_notas_admin,
        CONCAT(u.usu_nombre, ' ', u.usu_apellido) AS nombre_solicitante, 
        u.usu_email AS email_solicitante,
        v.veh_precio AS cit_monto_estimado,
        v.veh_estado,
        v.usu_id_gestor
    FROM
        citas c
    LEFT JOIN
        usuarios u ON c.usu_id_solicitante = u.usu_id
    LEFT JOIN
        vehiculos v ON c.veh_id = v.veh_id
    WHERE
        c.cit_id = p_cit_id;
END //
DELIMITER ;

-- Actualizar SP para obtener todas las citas (incluir estado del vehículo)
DROP PROCEDURE IF EXISTS `sp_obtener_todas_las_citas`;
DELIMITER //
CREATE PROCEDURE `sp_obtener_todas_las_citas`(
    IN `p_filtro_texto` VARCHAR(255),
    IN `p_filtro_estado` VARCHAR(50),
    IN `p_filtro_fecha_desde` DATE,
    IN `p_filtro_fecha_hasta` DATE
)
BEGIN
    SET @sql_query = CONCAT("
        SELECT
            c.cit_id,
            c.usu_id_solicitante,
            c.veh_id,
            c.cit_detalles_vehiculo_solicitado,
            c.cit_mensaje,
            c.cit_estado,
            c.cit_fecha_disponibilidad,
            c.cit_hora_disponibilidad,
            c.cit_fecha_solicitud,
            c.cit_notas_admin, 
            CONCAT(u.usu_nombre, ' ', u.usu_apellido) AS nombre_solicitante, 
            u.usu_email AS email_solicitante,  
            v.veh_precio AS cit_monto_estimado,
            v.veh_estado,
            v.usu_id_gestor
        FROM
            citas c
        LEFT JOIN
            usuarios u ON c.usu_id_solicitante = u.usu_id 
        LEFT JOIN
            vehiculos v ON c.veh_id = v.veh_id
        WHERE 1=1");

    IF p_filtro_texto IS NOT NULL AND p_filtro_texto != '' THEN
        SET @sanitized_filtro_texto = REPLACE(p_filtro_texto, "'", "''");
        SET @sql_query = CONCAT(@sql_query, " AND (c.cit_detalles_vehiculo_solicitado LIKE '%", @sanitized_filtro_texto, "%' OR CONCAT(u.usu_nombre, ' ', u.usu_apellido) LIKE '%", @sanitized_filtro_texto, "%' OR u.usu_email LIKE '%", @sanitized_filtro_texto, "%' OR c.cit_id LIKE '%", @sanitized_filtro_texto, "%')");
    END IF;

    IF p_filtro_estado IS NOT NULL AND p_filtro_estado != '' THEN
        SET @sql_query = CONCAT(@sql_query, " AND c.cit_estado = '", REPLACE(p_filtro_estado, "'", "''"), "'");
    END IF;

    IF p_filtro_fecha_desde IS NOT NULL THEN
        SET @sql_query = CONCAT(@sql_query, " AND DATE(c.cit_fecha_solicitud) >= '", p_filtro_fecha_desde, "'");
    END IF;

    IF p_filtro_fecha_hasta IS NOT NULL THEN
        SET @sql_query = CONCAT(@sql_query, " AND DATE(c.cit_fecha_solicitud) <= '", p_filtro_fecha_hasta, "'");
    END IF;

    SET @sql_query = CONCAT(@sql_query, " ORDER BY c.cit_fecha_solicitud DESC");

    PREPARE stmt FROM @sql_query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END //
DELIMITER ;