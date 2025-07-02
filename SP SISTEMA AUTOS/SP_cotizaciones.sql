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

-- SP para OBTENER las cotizaciones que le pertenecen a un vendedor
DROP PROCEDURE IF EXISTS sp_get_cotizaciones_por_vendedor;
DELIMITER //
CREATE PROCEDURE sp_get_cotizaciones_por_vendedor(
    IN p_usu_id_vendedor INT
)
BEGIN
    SELECT 
        c.cot_id,
        c.cot_fecha_solicitud,
        c.cot_mensaje,
        c.cot_estado,
        c.cot_detalles_vehiculo_solicitado,
        u.usu_nombre,
        u.usu_apellido,
        u.usu_email,
        u.usu_telefono
    FROM cotizaciones c
    JOIN usuarios u ON c.usu_id_solicitante = u.usu_id
    JOIN vehiculos v ON c.veh_id = v.veh_id
    WHERE v.usu_id_gestor = p_usu_id_vendedor
    ORDER BY c.cot_fecha_solicitud DESC;
END //
DELIMITER ;

-- SP para ACTUALIZAR el estado de una cotización
DROP PROCEDURE IF EXISTS sp_update_cotizacion_estado;
DELIMITER //
CREATE PROCEDURE sp_update_cotizacion_estado(
    IN p_cot_id INT,
    IN p_nuevo_estado ENUM('pendiente','contactado','cerrado','rechazado'),
    IN p_usu_id_vendedor INT,
    OUT p_resultado INT,
    OUT p_mensaje VARCHAR(255)
)
BEGIN
    DECLARE v_owner_check INT DEFAULT 0;
    SET p_resultado = 0; -- Por defecto, error

    -- Medida de seguridad: Verificar que el usuario que actualiza es el dueño del vehículo de la cotización
    SELECT COUNT(*) INTO v_owner_check
    FROM cotizaciones c
    JOIN vehiculos v ON c.veh_id = v.veh_id
    WHERE c.cot_id = p_cot_id AND v.usu_id_gestor = p_usu_id_vendedor;

    IF v_owner_check > 0 THEN
        UPDATE cotizaciones
        SET cot_estado = p_nuevo_estado
        WHERE cot_id = p_cot_id;

        IF ROW_COUNT() > 0 THEN
            SET p_resultado = 1;
            SET p_mensaje = 'Estado de la cotización actualizado correctamente.';
        ELSE
            SET p_mensaje = 'No se realizaron cambios (el estado ya era el mismo).';
        END IF;
    ELSE
        SET p_mensaje = 'Error: No tienes permiso para modificar esta cotización o no existe.';
    END IF;
END //
DELIMITER ;