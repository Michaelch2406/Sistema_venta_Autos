USE SISTEMAVENTAAUTOS;

USE SistemaVentaAutos;

DROP PROCEDURE IF EXISTS sp_insertar_cotizacion;

DELIMITER //

CREATE PROCEDURE sp_insertar_cotizacion(
    IN p_usu_id_solicitante INT,
    IN p_veh_id INT,
    IN p_mensaje TEXT,
    OUT p_resultado INT,
    OUT p_mensaje_respuesta VARCHAR(255)
)
BEGIN
    DECLARE v_vehiculo_info TEXT;

    SET p_resultado = 0; -- Por defecto, asumimos un error

    -- Verificar si el usuario ya ha cotizado este vehículo
    IF EXISTS(SELECT 1 FROM cotizaciones WHERE usu_id_solicitante = p_usu_id_solicitante AND veh_id = p_veh_id) THEN
        SET p_mensaje_respuesta = 'Ya has enviado una solicitud para este vehículo. El vendedor se pondrá en contacto contigo pronto.';
    ELSE
        -- Obtener un resumen del vehículo para guardarlo en la cotización
        SELECT CONCAT(m.mar_nombre, ' ', mo.mod_nombre, ' (Año: ', v.veh_anio, ', Precio: ', v.veh_precio, ')')
        INTO v_vehiculo_info
        FROM Vehiculos v
        JOIN Marcas m ON v.mar_id = m.mar_id
        JOIN Modelos mo ON v.mod_id = mo.mod_id
        WHERE v.veh_id = p_veh_id;

        -- Insertar la nueva cotización
        INSERT INTO cotizaciones (
            usu_id_solicitante, 
            veh_id, 
            cot_detalles_vehiculo_solicitado, 
            cot_mensaje
        ) VALUES (
            p_usu_id_solicitante, 
            p_veh_id, 
            v_vehiculo_info, 
            p_mensaje
        );

        IF ROW_COUNT() > 0 THEN
            SET p_resultado = 1;
            SET p_mensaje_respuesta = '¡Solicitud enviada! El vendedor ha sido notificado y se pondrá en contacto contigo.';
        ELSE
            SET p_mensaje_respuesta = 'Error: No se pudo registrar tu solicitud. Inténtalo de nuevo más tarde.';
        END IF;
    END IF;
END //

DELIMITER ;

USE SistemaVentaAutos;


DELIMITER $$

DROP PROCEDURE IF EXISTS `sp_obtener_cotizaciones_usuario`$$
CREATE PROCEDURE `sp_obtener_cotizaciones_usuario`(IN `p_usu_id` INT)
BEGIN
    SELECT
        c.cot_id,
        c.veh_id,
        c.cot_detalles_vehiculo_solicitado,
        c.cot_mensaje,
        c.cot_estado,
        c.cot_fecha_solicitud,
        c.cot_notas_admin, 
        v.veh_precio AS cot_monto_estimado,
        -- Para mostrar un nombre de vehículo más descriptivo, podrías hacer JOIN con marcas y modelos aquí si fuera necesario
        -- Por ahora, se usa el texto almacenado:
        c.cot_detalles_vehiculo_solicitado AS vehiculo_nombre_display 
    FROM
        cotizaciones c
    LEFT JOIN
        vehiculos v ON c.veh_id = v.veh_id
    WHERE
        c.usu_id_solicitante = p_usu_id
    ORDER BY
        c.cot_fecha_solicitud DESC;
END$$

DELIMITER ;


DELIMITER $$

DROP PROCEDURE IF EXISTS `sp_obtener_todas_las_cotizaciones`$$
CREATE PROCEDURE `sp_obtener_todas_las_cotizaciones`(
    IN `p_filtro_texto` VARCHAR(255),
    IN `p_filtro_estado` VARCHAR(50),
    IN `p_filtro_fecha_desde` DATE,
    IN `p_filtro_fecha_hasta` DATE
)
BEGIN
    SET @sql_query = CONCAT("
        SELECT
            c.cot_id,
            c.usu_id_solicitante,
            c.veh_id,
            c.cot_detalles_vehiculo_solicitado,
            c.cot_mensaje,
            c.cot_estado,
            c.cot_fecha_solicitud,
            c.cot_notas_admin, 
            CONCAT(u.usu_nombre, ' ', u.usu_apellido) AS nombre_solicitante, 
            u.usu_email AS email_solicitante,  
            v.veh_precio AS cot_monto_estimado
        FROM
            cotizaciones c
        LEFT JOIN
            usuarios u ON c.usu_id_solicitante = u.usu_id 
        LEFT JOIN
            vehiculos v ON c.veh_id = v.veh_id
        WHERE 1=1");

    IF p_filtro_texto IS NOT NULL AND p_filtro_texto != '' THEN
        SET @sanitized_filtro_texto = REPLACE(p_filtro_texto, "'", "''");
        SET @sql_query = CONCAT(@sql_query, " AND (c.cot_detalles_vehiculo_solicitado LIKE '%", @sanitized_filtro_texto, "%' OR CONCAT(u.usu_nombre, ' ', u.usu_apellido) LIKE '%", @sanitized_filtro_texto, "%' OR u.usu_email LIKE '%", @sanitized_filtro_texto, "%' OR c.cot_id LIKE '%", @sanitized_filtro_texto, "%')");
    END IF;

    IF p_filtro_estado IS NOT NULL AND p_filtro_estado != '' THEN
        SET @sql_query = CONCAT(@sql_query, " AND c.cot_estado = '", REPLACE(p_filtro_estado, "'", "''"), "'");
    END IF;

    IF p_filtro_fecha_desde IS NOT NULL THEN
        SET @sql_query = CONCAT(@sql_query, " AND DATE(c.cot_fecha_solicitud) >= '", p_filtro_fecha_desde, "'");
    END IF;

    IF p_filtro_fecha_hasta IS NOT NULL THEN
        SET @sql_query = CONCAT(@sql_query, " AND DATE(c.cot_fecha_solicitud) <= '", p_filtro_fecha_hasta, "'");
    END IF;

    SET @sql_query = CONCAT(@sql_query, " ORDER BY c.cot_fecha_solicitud DESC");

    PREPARE stmt FROM @sql_query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

DELIMITER ;

DELIMITER $$

DROP PROCEDURE IF EXISTS `sp_obtener_detalle_cotizacion`$$
CREATE PROCEDURE `sp_obtener_detalle_cotizacion`(IN `p_cot_id` INT)
BEGIN
    SELECT
        c.cot_id,
        c.usu_id_solicitante,
        c.veh_id,
        c.cot_detalles_vehiculo_solicitado,
        c.cot_mensaje, -- Mensaje del usuario solicitante
        c.cot_estado,
        c.cot_fecha_solicitud,
        c.cot_notas_admin, -- Notas del administrador
        CONCAT(u.usu_nombre, ' ', u.usu_apellido) AS nombre_solicitante, 
        u.usu_email AS email_solicitante,
        v.veh_precio AS cot_monto_estimado,
        v.veh_anio,
        v.veh_kilometraje,
        v.veh_condicion,
        v.veh_color_exterior,
        v.veh_tipo_transmision,
        v.veh_tipo_combustible,
        v.veh_descripcion AS vehiculo_descripcion_vehiculo
    FROM
        cotizaciones c
    LEFT JOIN
        usuarios u ON c.usu_id_solicitante = u.usu_id
    LEFT JOIN
        vehiculos v ON c.veh_id = v.veh_id
    WHERE
        c.cot_id = p_cot_id;
END$$

DELIMITER ;

DELIMITER $$

DROP PROCEDURE IF EXISTS `sp_actualizar_estado_cotizacion`$$
CREATE PROCEDURE `sp_actualizar_estado_cotizacion`(
    IN `p_cot_id` INT,
    IN `p_nuevo_estado` ENUM('pendiente','aprobada_admin','contactado','cerrado','rechazado'),
    IN `p_actor_id` INT 
)
BEGIN
    UPDATE cotizaciones
    SET
        cot_estado = p_nuevo_estado
        -- Si en el futuro añades campos de auditoría como cot_actualizado_por_usu_id y cot_fecha_actualizacion,
        -- los actualizarías aquí: 
        -- , cot_actualizado_por_usu_id = p_actor_id
        -- , cot_fecha_actualizacion = CURRENT_TIMESTAMP 
    WHERE
        cot_id = p_cot_id;
    
    SELECT ROW_COUNT() AS filas_afectadas;
END$$

DELIMITER ;

DELIMITER $$

DROP PROCEDURE IF EXISTS `sp_guardar_notas_admin_cotizacion`$$
CREATE PROCEDURE `sp_guardar_notas_admin_cotizacion`(
    IN `p_cot_id` INT,
    IN `p_notas` TEXT,
    IN `p_admin_id` INT 
)
BEGIN
    UPDATE cotizaciones
    SET
        cot_notas_admin = p_notas
        -- Si en el futuro añades campos de auditoría:
        -- , cot_actualizado_por_usu_id = p_admin_id
        -- , cot_fecha_actualizacion = CURRENT_TIMESTAMP
    WHERE
        cot_id = p_cot_id;

    SELECT ROW_COUNT() AS filas_afectadas;
END$$

DELIMITER ;