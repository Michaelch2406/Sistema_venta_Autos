USE SISTEMAVENTAAUTOS;

DROP PROCEDURE IF EXISTS `sp_insertar_cita`;
DELIMITER //
CREATE PROCEDURE `sp_insertar_cita`(
    IN p_usu_id_solicitante INT,
    IN p_veh_id INT,
    IN p_mensaje TEXT,
    IN p_fecha_disponibilidad DATE,
    IN p_hora_disponibilidad VARCHAR(20),
    OUT p_resultado INT,
    OUT p_mensaje_respuesta VARCHAR(255)
)
BEGIN
    DECLARE v_vehiculo_info TEXT;

    SET p_resultado = 0; 

    IF EXISTS(SELECT 1 FROM citas WHERE usu_id_solicitante = p_usu_id_solicitante AND veh_id = p_veh_id AND cit_estado IN ('pendiente', 'aprobada')) THEN
        SET p_mensaje_respuesta = 'Ya tienes una cita pendiente o aprobada para este vehículo.';
    ELSE
        SELECT CONCAT(m.mar_nombre, ' ', mo.mod_nombre, ' (Año: ', v.veh_anio, ', Precio: ', v.veh_precio, ')')
        INTO v_vehiculo_info
        FROM Vehiculos v JOIN Marcas m ON v.mar_id = m.mar_id JOIN Modelos mo ON v.mod_id = mo.mod_id
        WHERE v.veh_id = p_veh_id;

        INSERT INTO citas (
            usu_id_solicitante, 
            veh_id, 
            cit_detalles_vehiculo_solicitado, 
            cit_mensaje,
            cit_fecha_disponibilidad,
            cit_hora_disponibilidad
        ) VALUES (
            p_usu_id_solicitante, 
            p_veh_id, 
            v_vehiculo_info, 
            p_mensaje,
            p_fecha_disponibilidad,
            p_hora_disponibilidad
        );

        IF ROW_COUNT() > 0 THEN
            SET p_resultado = 1;
            SET p_mensaje_respuesta = '¡Tu cita ha sido solicitada con éxito! Un gestor la confirmará pronto.';
        ELSE
            SET p_mensaje_respuesta = 'Error: No se pudo registrar tu solicitud de cita.';
        END IF;
    END IF;
END //
DELIMITER ;

-- --- SP para obtener las citas de un usuario (ACTUALIZADO) ---
DROP PROCEDURE IF EXISTS `sp_obtener_citas_usuario`;
DELIMITER //
CREATE PROCEDURE `sp_obtener_citas_usuario`(IN `p_usu_id` INT)
BEGIN
    SELECT
        c.cit_id,
        c.veh_id,
        c.cit_detalles_vehiculo_solicitado,
        c.cit_mensaje,
        c.cit_estado,
        c.cit_fecha_disponibilidad,
        c.cit_hora_disponibilidad,
        c.cit_fecha_solicitud,
        c.cit_notas_admin, 
        v.veh_precio AS cit_monto_estimado,
        c.cit_detalles_vehiculo_solicitado AS vehiculo_nombre_display 
    FROM
        citas c
    LEFT JOIN
        vehiculos v ON c.veh_id = v.veh_id
    WHERE
        c.usu_id_solicitante = p_usu_id
    ORDER BY
        c.cit_fecha_solicitud DESC;
END //
DELIMITER ;

-- --- SP para obtener todas las citas (admin) (ACTUALIZADO) ---
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
            v.veh_precio AS cit_monto_estimado
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

-- --- SP para obtener el detalle de una cita (ACTUALIZADO) ---
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
        v.veh_precio AS cit_monto_estimado
        /* Otros campos del vehículo se pueden mantener si son necesarios para mostrar detalles adicionales */
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

-- --- SP para actualizar el estado de una cita ---
DROP PROCEDURE IF EXISTS `sp_actualizar_estado_cita`;
DELIMITER //
CREATE PROCEDURE `sp_actualizar_estado_cita`(
    IN `p_cit_id` INT,
    IN `p_nuevo_estado` ENUM('pendiente','aprobada','rechazado'),
    IN `p_actor_id` INT 
)
BEGIN
    UPDATE citas
    SET
        cit_estado = p_nuevo_estado
    WHERE
        cit_id = p_cit_id;
    
    SELECT ROW_COUNT() AS filas_afectadas;
END //
DELIMITER ;

-- --- SP para guardar notas de admin en una cita ---
DROP PROCEDURE IF EXISTS `sp_guardar_notas_admin_cita`;
DELIMITER //
CREATE PROCEDURE `sp_guardar_notas_admin_cita`(
    IN `p_cit_id` INT,
    IN `p_notas` TEXT,
    IN `p_admin_id` INT 
)
BEGIN
    UPDATE citas
    SET
        cit_notas_admin = p_notas
    WHERE
        cit_id = p_cit_id;

    SELECT ROW_COUNT() AS filas_afectadas;
END //
DELIMITER ;

-- --- NUEVO SP para obtener disponibilidad simulada para un vehículo ---
DROP PROCEDURE IF EXISTS `sp_obtener_disponibilidad_cita`;
DELIMITER //
CREATE PROCEDURE `sp_obtener_disponibilidad_cita`(
    IN `p_veh_id` INT,
    IN `p_fecha_consulta` DATE -- La fecha que el usuario selecciona para ver las horas
)
BEGIN
    -- Este SP simula horas ocupadas. No necesita una tabla real de horarios.
    -- Cada vez que se llame, generará un conjunto diferente de horas "ocupadas".
    -- Selecciona las horas en las que ya hay una cita 'aprobada' en la fecha consultada
    SELECT cit_hora_disponibilidad 
    FROM citas
    WHERE veh_id = p_veh_id 
    AND cit_fecha_disponibilidad = p_fecha_consulta 
    AND cit_estado = 'aprobada';
END //
DELIMITER ;