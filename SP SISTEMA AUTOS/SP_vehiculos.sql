-- SP para Obtener todas las Marcas Activas
DELIMITER //
CREATE PROCEDURE sp_get_marcas_activas()
BEGIN
    SELECT mar_id, mar_nombre FROM Marcas WHERE mar_logo_url IS NOT NULL OR TRUE ORDER BY mar_nombre ASC; -- Modificado para incluir todas si no tienen logo
END //
DELIMITER ;

-- SP para Obtener Modelos Activos por Marca
DELIMITER //
CREATE PROCEDURE sp_get_modelos_por_marca(IN p_mar_id INT)
BEGIN
    SELECT mod_id, mod_nombre FROM Modelos WHERE mar_id = p_mar_id ORDER BY mod_nombre ASC;
END //
DELIMITER ;

-- SP para Obtener todos los Tipos de Vehículo Activos
DELIMITER //
CREATE PROCEDURE sp_get_tipos_vehiculo_activos()
BEGIN
    SELECT tiv_id, tiv_nombre FROM TiposVehiculo WHERE tiv_activo = TRUE ORDER BY tiv_nombre ASC;
END //
DELIMITER ;

-- 3. MODIFICAR STORED PROCEDURE sp_insertar_vehiculo
DROP PROCEDURE IF EXISTS sp_insertar_vehiculo;
DELIMITER //
CREATE PROCEDURE sp_insertar_vehiculo(
    IN p_mar_id INT,
    IN p_mod_id INT,
    IN p_tiv_id INT,
    IN p_veh_subtipo_vehiculo VARCHAR(100),
    IN p_usu_id_gestor INT,
    -- p_veh_condicion ahora se asume 'usado'
    IN p_veh_anio INT,
    IN p_veh_kilometraje INT, -- Ahora es obligatorio
    IN p_veh_precio DECIMAL(12, 2),
    IN p_veh_vin VARCHAR(20),
    IN p_veh_ubicacion_provincia VARCHAR(100),
    IN p_veh_ubicacion_ciudad VARCHAR(100),
    IN p_veh_placa_provincia_origen VARCHAR(100),
    IN p_veh_ultimo_digito_placa CHAR(1),
    IN p_veh_color_exterior VARCHAR(50), -- Sigue siendo requerido
    IN p_veh_color_interior VARCHAR(50), -- Ahora opcional
    IN p_veh_detalles_motor TEXT,       -- Sigue siendo requerido
    IN p_veh_tipo_transmision VARCHAR(50), -- Ahora opcional
    IN p_veh_traccion ENUM('Delantera', 'Trasera', '4x4', 'AWD', 'Otro'), -- Ahora opcional
    IN p_veh_tipo_vidrios ENUM('Manuales', 'Electricos Delanteros', 'Electricos Completos', 'Otro'), -- Ahora opcional
    IN p_veh_tipo_combustible ENUM('Gasolina', 'Diesel', 'Hibrido', 'Electrico', 'Flex (Gasolina/Etanol)', 'GLP', 'GNV', 'Otro'), -- Ahora opcional
    IN p_veh_tipo_direccion ENUM('Mecanica', 'Hidraulica', 'Electroasistida', 'Electrica', 'Otra'), -- Ahora opcional
    IN p_veh_sistema_climatizacion ENUM('Ninguno', 'Aire Acondicionado', 'Climatizador Manual', 'Climatizador Automatico', 'Climatizador Bi-Zona', 'Otro'), -- Ahora opcional
    IN p_veh_caracteristicas_seguridad TEXT, -- Ahora opcional
    IN p_veh_caracteristicas_adicionales TEXT, -- Ahora opcional
    IN p_veh_descripcion TEXT,                -- Sigue siendo requerido
    IN p_veh_detalles_extra TEXT,
    IN p_veh_fecha_publicacion DATE,
    OUT p_veh_id_insertado INT,
    OUT p_resultado INT, 
    OUT p_mensaje VARCHAR(255)
)
BEGIN
    SET p_resultado = 0; 
    SET p_mensaje = 'Error al insertar el vehículo.';
    SET p_veh_id_insertado = NULL;
    SET @v_veh_condicion = 'usado'; -- Siempre 'usado'

    IF p_veh_vin IS NOT NULL AND p_veh_vin != '' AND EXISTS (SELECT 1 FROM Vehiculos WHERE veh_vin = p_veh_vin) THEN
        SET p_mensaje = 'El VIN ingresado ya existe para otro vehículo.';
    ELSE
        INSERT INTO Vehiculos (
            mar_id, mod_id, tiv_id, veh_subtipo_vehiculo, usu_id_gestor, veh_condicion, veh_anio, veh_kilometraje,
            veh_precio, veh_vin, veh_ubicacion_provincia, veh_ubicacion_ciudad, veh_placa_provincia_origen, veh_ultimo_digito_placa,
            veh_color_exterior, veh_color_interior, veh_detalles_motor, veh_tipo_transmision,
            veh_traccion, veh_tipo_vidrios, veh_tipo_combustible, veh_tipo_direccion, veh_sistema_climatizacion,
            veh_caracteristicas_seguridad, veh_caracteristicas_adicionales, veh_descripcion, veh_detalles_extra,
            veh_estado, veh_fecha_publicacion
        ) VALUES (
            p_mar_id, p_mod_id, p_tiv_id, p_veh_subtipo_vehiculo, p_usu_id_gestor, @v_veh_condicion, p_veh_anio, p_veh_kilometraje,
            p_veh_precio, p_veh_vin, p_veh_ubicacion_provincia, p_veh_ubicacion_ciudad, p_veh_placa_provincia_origen, p_veh_ultimo_digito_placa,
            p_veh_color_exterior, p_veh_color_interior, p_veh_detalles_motor, p_veh_tipo_transmision,
            p_veh_traccion, p_veh_tipo_vidrios, p_veh_tipo_combustible, p_veh_tipo_direccion, p_veh_sistema_climatizacion,
            p_veh_caracteristicas_seguridad, p_veh_caracteristicas_adicionales, p_veh_descripcion, p_veh_detalles_extra,
            'disponible', p_veh_fecha_publicacion
        );

        IF ROW_COUNT() > 0 THEN
            SET p_veh_id_insertado = LAST_INSERT_ID();
            SET p_resultado = 1;
            SET p_mensaje = 'Vehículo usado publicado exitosamente.';
        ELSE
            SET p_mensaje = 'No se pudo insertar el vehículo en la base de datos.';
        END IF;
    END IF;
END //
DELIMITER ;


-- Usar la base de datos
USE SistemaVentaAutos;

DROP PROCEDURE IF EXISTS sp_insertar_imagen_vehiculo;
DELIMITER //
CREATE PROCEDURE sp_insertar_imagen_vehiculo(
    IN p_veh_id INT,
    IN p_ima_url VARCHAR(255),
    IN p_ima_es_principal BOOLEAN,
    OUT p_ima_id_insertado INT,
    OUT p_resultado INT,
    OUT p_mensaje VARCHAR(255)
)
BEGIN
    SET p_resultado = 0; -- Error por defecto
    SET p_mensaje = 'Error al guardar la imagen del vehículo.';
    SET p_ima_id_insertado = NULL;

    -- Si se está marcando como principal, desmarcar otras para el mismo vehículo
    IF p_ima_es_principal = TRUE THEN
        UPDATE ImagenesVehiculo SET ima_es_principal = FALSE WHERE veh_id = p_veh_id;
    END IF;

    INSERT INTO ImagenesVehiculo (veh_id, ima_url, ima_es_principal)
    VALUES (p_veh_id, p_ima_url, p_ima_es_principal);

    IF ROW_COUNT() > 0 THEN
        SET p_ima_id_insertado = LAST_INSERT_ID();
        SET p_resultado = 1;
        SET p_mensaje = 'Imagen guardada exitosamente.';
    ELSE
        SET p_mensaje = 'No se pudo guardar la imagen en la base de datos.';
    END IF;
END //
DELIMITER ;


-- Usar la base de datos
USE SistemaVentaAutos;

DROP PROCEDURE IF EXISTS sp_get_vehiculos_por_gestor;
DELIMITER //
CREATE PROCEDURE sp_get_vehiculos_por_gestor(
    IN p_usu_id_gestor INT
)
BEGIN
    SELECT
        v.veh_id,
        v.veh_condicion,
        v.veh_anio,
        v.veh_precio,
        v.veh_estado,
        v.veh_fecha_publicacion,
        m.mar_nombre,
        mo.mod_nombre,
        tv.tiv_nombre,
        -- Obtener la URL de la imagen principal
        (SELECT ima_url FROM ImagenesVehiculo iv WHERE iv.veh_id = v.veh_id AND iv.ima_es_principal = TRUE LIMIT 1) AS imagen_principal_url,
        -- Contar cuántas imágenes tiene (opcional, pero útil)
        (SELECT COUNT(*) FROM ImagenesVehiculo iv_count WHERE iv_count.veh_id = v.veh_id) AS total_imagenes
    FROM Vehiculos v
    JOIN Marcas m ON v.mar_id = m.mar_id
    JOIN Modelos mo ON v.mod_id = mo.mod_id
    JOIN TiposVehiculo tv ON v.tiv_id = tv.tiv_id
    WHERE v.usu_id_gestor = p_usu_id_gestor
    ORDER BY v.veh_fecha_publicacion DESC, v.veh_creado_en DESC;
END //
DELIMITER ;



-- Usar la base de datos
USE SistemaVentaAutos;

DROP PROCEDURE IF EXISTS sp_actualizar_estado_vehiculo;
DELIMITER //
CREATE PROCEDURE sp_actualizar_estado_vehiculo(
    IN p_veh_id INT,
    IN p_nuevo_estado ENUM('disponible', 'reservado', 'vendido', 'desactivado'),
    IN p_usu_id_gestor_actual INT,
    OUT p_resultado INT, -- 1: Éxito, 0: Vehículo no encontrado o no pertenece al gestor, -1: Error de actualización
    OUT p_mensaje VARCHAR(255)
)
BEGIN
    DECLARE v_current_gestor_id INT;
    SET p_resultado = -1; -- Error por defecto
    SET p_mensaje = 'Error desconocido al actualizar el estado.';

    -- Verificar si el vehículo existe y pertenece al gestor actual
    SELECT usu_id_gestor INTO v_current_gestor_id FROM Vehiculos WHERE veh_id = p_veh_id;

    IF v_current_gestor_id IS NULL THEN
        SET p_resultado = 0;
        SET p_mensaje = 'Vehículo no encontrado.';
    ELSEIF v_current_gestor_id != p_usu_id_gestor_actual THEN
        SET p_resultado = 0;
        SET p_mensaje = 'No tienes permiso para modificar este vehículo.';
    ELSE
        -- Actualizar el estado del vehículo
        UPDATE Vehiculos
        SET veh_estado = p_nuevo_estado,
            veh_actualizado_en = CURRENT_TIMESTAMP
        WHERE veh_id = p_veh_id;

        IF ROW_COUNT() > 0 THEN
            SET p_resultado = 1;
            SET p_mensaje = CONCAT('Estado del vehículo actualizado a "', p_nuevo_estado, '" exitosamente.');
        ELSE
            -- Esto podría pasar si el estado ya era el mismo, o si hubo un error inesperado.
            -- ROW_COUNT() devuelve 0 si no se modificaron filas.
            -- Para diferenciar, podríamos añadir una comprobación del estado actual antes del UPDATE.
            SET p_mensaje = 'No se realizaron cambios en el estado (podría ser el mismo estado o un error).';
            -- Si quieres ser más específico, puedes seleccionar el estado actual y compararlo.
            -- Si es diferente y ROW_COUNT() es 0, entonces es un error.
        END IF;
    END IF;
END //
DELIMITER ;