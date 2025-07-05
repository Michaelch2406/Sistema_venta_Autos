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
USE SistemaVentaAutos;

USE SistemaVentaAutos;

DROP PROCEDURE IF EXISTS sp_insertar_vehiculo;

DELIMITER //

CREATE PROCEDURE sp_insertar_vehiculo(
    -- PARÁMETROS DE ENTRADA (IN)
    IN p_mar_id INT,
    IN p_mod_id INT,
    IN p_tiv_id INT,
    IN p_veh_subtipo_vehiculo VARCHAR(100),
    IN p_usu_id_gestor INT, -- <-- Este es el ID del vendedor que llega desde PHP
    IN p_veh_condicion ENUM('nuevo', 'usado'),
    IN p_veh_anio INT,
    IN p_veh_kilometraje INT,
    IN p_veh_precio DECIMAL(12, 2),
    IN p_veh_vin VARCHAR(20),
    IN p_veh_placa VARCHAR(10),
    IN p_veh_ubicacion_provincia VARCHAR(100),
    IN p_veh_ubicacion_ciudad VARCHAR(100),
    IN p_veh_placa_provincia_origen VARCHAR(100),
    IN p_veh_ultimo_digito_placa CHAR(1),
    IN p_veh_color_exterior VARCHAR(50),
    IN p_veh_color_interior VARCHAR(50),
    IN p_veh_detalles_motor TEXT,
    IN p_veh_tipo_transmision VARCHAR(50),
    IN p_veh_traccion ENUM('Delantera', 'Trasera', '4x4', 'AWD', 'Otro'),
    IN p_veh_tipo_vidrios ENUM('Manuales', 'Electricos Delanteros', 'Electricos Completos', 'Otro'),
    IN p_veh_tipo_combustible ENUM('Gasolina', 'Diesel', 'Hibrido', 'Electrico', 'Flex (Gasolina/Etanol)', 'GLP', 'GNV', 'Otro'),
    IN p_veh_tipo_direccion ENUM('Mecanica', 'Hidraulica', 'Electroasistida', 'Electrica', 'Otra'),
    IN p_veh_sistema_climatizacion ENUM('Ninguno', 'Aire Acondicionado', 'Climatizador Manual', 'Climatizador Automatico', 'Climatizador Bi-Zona', 'Otro'),
    IN p_veh_descripcion TEXT,
    IN p_veh_detalles_extra TEXT,
    IN p_veh_fecha_publicacion DATE,
    -- PARÁMETROS DE SALIDA (OUT)
    OUT p_veh_id_insertado INT,
    OUT p_resultado INT, 
    OUT p_mensaje VARCHAR(255)
)
BEGIN
    -- Inicializar variables de salida
    SET p_resultado = 0;
    SET p_mensaje = 'Error desconocido al insertar el vehículo.';
    SET p_veh_id_insertado = NULL;

    -- Validaciones antes de insertar
    IF p_veh_placa IS NOT NULL AND p_veh_placa != '' AND EXISTS (SELECT 1 FROM Vehiculos WHERE veh_placa = p_veh_placa) THEN
        SET p_mensaje = 'La placa ingresada ya existe para otro vehículo.';
    ELSEIF p_veh_vin IS NOT NULL AND p_veh_vin != '' AND EXISTS (SELECT 1 FROM Vehiculos WHERE veh_vin = p_veh_vin) THEN
        SET p_mensaje = 'El VIN ingresado ya existe para otro vehículo.';
    ELSE
        -- === PUNTO CLAVE DE LA CORRECCIÓN ===
        -- La sentencia INSERT utiliza el parámetro p_usu_id_gestor que viene de PHP.
        -- No hay ningún valor '1' escrito en el código.
        INSERT INTO Vehiculos (
            mar_id, mod_id, tiv_id, veh_subtipo_vehiculo, usu_id_gestor, veh_condicion, veh_anio, veh_kilometraje,
            veh_precio, veh_vin, veh_placa, veh_ubicacion_provincia, veh_ubicacion_ciudad, veh_placa_provincia_origen, veh_ultimo_digito_placa,
            veh_color_exterior, veh_color_interior, veh_detalles_motor, veh_tipo_transmision,
            veh_traccion, veh_tipo_vidrios, veh_tipo_combustible, veh_tipo_direccion, veh_sistema_climatizacion,
            veh_descripcion, veh_detalles_extra,
            veh_estado, veh_fecha_publicacion
        ) VALUES (
            p_mar_id, p_mod_id, p_tiv_id, p_veh_subtipo_vehiculo, p_usu_id_gestor, p_veh_condicion, p_veh_anio, p_veh_kilometraje,
            p_veh_precio, p_veh_vin, p_veh_placa, p_veh_ubicacion_provincia, p_veh_ubicacion_ciudad, p_veh_placa_provincia_origen, p_veh_ultimo_digito_placa,
            p_veh_color_exterior, p_veh_color_interior, p_veh_detalles_motor, p_veh_tipo_transmision,
            p_veh_traccion, p_veh_tipo_vidrios, p_veh_tipo_combustible, p_veh_tipo_direccion, p_veh_sistema_climatizacion,
            p_veh_descripcion, p_veh_detalles_extra,
            'disponible', p_veh_fecha_publicacion
        );

        -- Verificar si la inserción fue exitosa
        IF ROW_COUNT() > 0 THEN
            SET p_veh_id_insertado = LAST_INSERT_ID();
            SET p_resultado = 1;
            SET p_mensaje = 'Vehículo publicado exitosamente.';
        ELSE
            SET p_mensaje = 'No se pudo insertar el vehículo en la base de datos. Verifique los datos.';
        END IF;
    END IF;
END //

DELIMITER ;


-- Usar la base de datos
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
        'FROM Vehiculos v ',
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
    IF p_ubicacion_provincia IS NOT NULL AND p_ubicacion_provincia != '' THEN
        SET @sql_query = CONCAT(@sql_query, ' AND v.veh_ubicacion_provincia = ''', p_ubicacion_provincia, '''');
    END IF;

    -- Orden y Paginación
    SET @sql_query = CONCAT(@sql_query, ' ORDER BY v.veh_fecha_publicacion DESC, v.veh_creado_en DESC');
    IF p_items_por_pagina IS NOT NULL AND p_offset IS NOT NULL THEN
        SET @sql_query = CONCAT(@sql_query, ' LIMIT ', p_offset, ', ', p_items_por_pagina);
    END IF;

    -- Preparar y ejecutar la consulta principal
    PREPARE stmt FROM @sql_query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

    -- Obtener el total de filas que coinciden con los filtros (para paginación)
    SELECT FOUND_ROWS() INTO p_total_vehiculos;

END //
DELIMITER ;


USE SistemaVentaAutos;

-- TIPOS DE VEHICULO --
DROP PROCEDURE IF EXISTS sp_admin_get_all_tipos_vehiculo;
DELIMITER //
CREATE PROCEDURE sp_admin_get_all_tipos_vehiculo()
BEGIN
    SELECT tiv_id, tiv_nombre, tiv_descripcion, tiv_icono_url, tiv_activo 
    FROM TiposVehiculo 
    ORDER BY tiv_nombre ASC;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS sp_admin_insertar_tipo_vehiculo;
DELIMITER //
CREATE PROCEDURE sp_admin_insertar_tipo_vehiculo(
    IN p_tiv_nombre VARCHAR(100),
    IN p_tiv_descripcion TEXT,
    IN p_tiv_icono_url VARCHAR(255),
    IN p_tiv_activo BOOLEAN,
    OUT p_tiv_id_insertado INT,
    OUT p_resultado INT,
    OUT p_mensaje VARCHAR(255)
)
BEGIN
    SET p_resultado = 0;
    SET p_mensaje = 'Error al insertar el tipo de vehículo.';
    IF EXISTS (SELECT 1 FROM TiposVehiculo WHERE tiv_nombre = p_tiv_nombre) THEN
        SET p_mensaje = 'El nombre del tipo de vehículo ya existe.';
    ELSE
        INSERT INTO TiposVehiculo (tiv_nombre, tiv_descripcion, tiv_icono_url, tiv_activo) 
        VALUES (p_tiv_nombre, p_tiv_descripcion, p_tiv_icono_url, p_tiv_activo);
        IF ROW_COUNT() > 0 THEN
            SET p_tiv_id_insertado = LAST_INSERT_ID();
            SET p_resultado = 1;
            SET p_mensaje = 'Tipo de vehículo insertado exitosamente.';
        END IF;
    END IF;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS sp_admin_actualizar_tipo_vehiculo;
DELIMITER //
CREATE PROCEDURE sp_admin_actualizar_tipo_vehiculo(
    IN p_tiv_id INT,
    IN p_tiv_nombre VARCHAR(100),
    IN p_tiv_descripcion TEXT,
    IN p_tiv_icono_url VARCHAR(255),
    IN p_tiv_activo BOOLEAN,
    OUT p_resultado INT,
    OUT p_mensaje VARCHAR(255)
)
BEGIN
    DECLARE v_tipo_existe INT DEFAULT 0;
    DECLARE v_nombre_duplicado INT DEFAULT 0;
    SET p_resultado = 0;
    SET p_mensaje = 'Error desconocido al actualizar el tipo de vehículo.';

    SELECT COUNT(*) INTO v_tipo_existe FROM TiposVehiculo WHERE tiv_id = p_tiv_id;
    IF v_tipo_existe = 0 THEN
        SET p_mensaje = 'Tipo de vehículo no encontrado.';
    ELSE
        SELECT COUNT(*) INTO v_nombre_duplicado 
        FROM TiposVehiculo 
        WHERE tiv_nombre = p_tiv_nombre AND tiv_id != p_tiv_id;

        IF v_nombre_duplicado > 0 THEN
            SET p_mensaje = 'El nombre del tipo de vehículo ya está en uso por otro tipo.';
        ELSE
            UPDATE TiposVehiculo 
            SET 
                tiv_nombre = p_tiv_nombre, 
                tiv_descripcion = p_tiv_descripcion, 
                tiv_icono_url = p_tiv_icono_url,
                tiv_activo = p_tiv_activo,
                tiv_actualizado_en = CURRENT_TIMESTAMP
            WHERE tiv_id = p_tiv_id;

            IF ROW_COUNT() > 0 THEN
                SET p_resultado = 1;
                SET p_mensaje = 'Tipo de vehículo actualizado exitosamente.';
            ELSE
                IF EXISTS (SELECT 1 FROM TiposVehiculo WHERE tiv_id = p_tiv_id AND tiv_nombre = p_tiv_nombre AND 
                           ( (tiv_descripcion IS NULL AND p_tiv_descripcion IS NULL) OR tiv_descripcion = p_tiv_descripcion ) AND
                           ( (tiv_icono_url IS NULL AND p_tiv_icono_url IS NULL) OR tiv_icono_url = p_tiv_icono_url ) AND
                           tiv_activo = p_tiv_activo) THEN
                    SET p_resultado = 1;
                    SET p_mensaje = 'No se realizaron cambios (datos idénticos).';
                ELSE
                    SET p_mensaje = 'No se realizaron cambios en el tipo de vehículo.';
                END IF;
            END IF;
        END IF;
    END IF;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS sp_admin_eliminar_tipo_vehiculo;
DELIMITER //
CREATE PROCEDURE sp_admin_eliminar_tipo_vehiculo(
    IN p_tiv_id INT,
    OUT p_resultado INT,
    OUT p_mensaje VARCHAR(255)
)
BEGIN
    SET p_resultado = 0;
    SET p_mensaje = 'Error al eliminar el tipo de vehículo.';
    IF EXISTS (SELECT 1 FROM Vehiculos WHERE tiv_id = p_tiv_id) THEN
        SET p_mensaje = 'No se puede eliminar el tipo de vehículo porque tiene vehículos asociados. Reasigne los vehículos primero.';
    ELSE
        DELETE FROM TiposVehiculo WHERE tiv_id = p_tiv_id;
        IF ROW_COUNT() > 0 THEN
            SET p_resultado = 1;
            SET p_mensaje = 'Tipo de vehículo eliminado exitosamente.';
        ELSE
            SET p_mensaje = 'Tipo de vehículo no encontrado o ya eliminado.';
        END IF;
    END IF;
END //
DELIMITER ;

USE SISTEMAVENTAAUTOS;
DROP PROCEDURE IF EXISTS sp_actualizar_estado_vehiculo;
DELIMITER //
CREATE PROCEDURE sp_actualizar_estado_vehiculo(
    IN p_veh_id INT,
    IN p_nuevo_estado ENUM('disponible','reservado','vendido','desactivado'),
    IN p_usu_id_sesion INT, -- ID del usuario que realiza la acción
    OUT p_resultado INT,
    OUT p_mensaje VARCHAR(255)
)
BEGIN
    DECLARE v_usu_id_gestor_vehiculo INT;
    DECLARE v_rol_id_sesion INT;
    DECLARE v_estado_actual ENUM('disponible','reservado','vendido','desactivado');

    SET p_resultado = 0; -- Error por defecto
    SET p_mensaje = 'Error desconocido al actualizar el estado.';

    -- Obtener el usu_id_gestor del vehículo y el estado actual
    SELECT usu_id_gestor, veh_estado INTO v_usu_id_gestor_vehiculo, v_estado_actual
    FROM Vehiculos
    WHERE veh_id = p_veh_id;

    IF v_usu_id_gestor_vehiculo IS NULL THEN
        SET p_mensaje = 'Vehículo no encontrado.';
    ELSE
        -- Obtener el rol del usuario de la sesión directamente desde la tabla Usuarios
        SELECT rol_id INTO v_rol_id_sesion
        FROM Usuarios
        WHERE usu_id = p_usu_id_sesion;

        -- Verificar permisos: 
        -- El usuario de la sesión debe ser el gestor del vehículo O tener rol de Administrador (rol_id = 3)
        IF v_usu_id_gestor_vehiculo = p_usu_id_sesion OR v_rol_id_sesion = 3 THEN
            IF v_estado_actual = p_nuevo_estado THEN
                SET p_mensaje = CONCAT('El vehículo ya se encuentra en estado ', p_nuevo_estado, '.');
                -- Considerar p_resultado = 1 aquí si no se considera un error, o incluso 0 si no se hizo cambio.
            ELSE
                UPDATE Vehiculos
                SET veh_estado = p_nuevo_estado,
                    veh_actualizado_en = CURRENT_TIMESTAMP
                WHERE veh_id = p_veh_id;

                IF ROW_COUNT() > 0 THEN
                    SET p_resultado = 1;
                    SET p_mensaje = CONCAT('Estado del vehículo actualizado a ', p_nuevo_estado, ' exitosamente.');
                ELSE
                    SET p_mensaje = 'No se pudo actualizar el estado del vehículo en la base de datos.';
                END IF;
            END IF;
        ELSE
            SET p_mensaje = 'No tienes permiso para modificar el estado de este vehículo.';
        END IF;
    END IF;
END //
DELIMITER ;


USE SistemaVentaAutos;

DROP PROCEDURE IF EXISTS sp_get_vehiculo_detalle;
DELIMITER //
CREATE PROCEDURE sp_get_vehiculo_detalle(
    IN p_veh_id INT
)
BEGIN
    SELECT
        v.veh_id, 
        -- === CAMPO AÑADIDO AQUÍ ===
        v.usu_id_gestor,
        -- ========================
        v.veh_subtipo_vehiculo, v.veh_condicion, v.veh_anio, v.veh_kilometraje,
        v.veh_precio, v.veh_vin, v.veh_placa, v.veh_ubicacion_provincia, v.veh_ubicacion_ciudad,
        v.veh_placa_provincia_origen, v.veh_ultimo_digito_placa,
        v.veh_color_exterior, v.veh_color_interior, v.veh_detalles_motor,
        v.veh_tipo_transmision, v.veh_traccion, v.veh_tipo_vidrios, v.veh_tipo_combustible,
        v.veh_tipo_direccion, v.veh_sistema_climatizacion,
        v.veh_descripcion, v.veh_detalles_extra, v.veh_fecha_publicacion, v.veh_estado,
        m.mar_nombre, mo.mod_nombre, tv.tiv_nombre,
        u_gestor.usu_usuario AS gestor_usuario, 
        CONCAT(u_gestor.usu_nombre, ' ', u_gestor.usu_apellido) AS gestor_nombre_completo,
        u_gestor.usu_telefono AS gestor_telefono
    FROM Vehiculos v
    JOIN Marcas m ON v.mar_id = m.mar_id
    JOIN Modelos mo ON v.mod_id = mo.mod_id
    JOIN TiposVehiculo tv ON v.tiv_id = tv.tiv_id
    LEFT JOIN Usuarios u_gestor ON v.usu_id_gestor = u_gestor.usu_id
    WHERE v.veh_id = p_veh_id;
END //
DELIMITER ;

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
END//
DELIMITER ;


USE SistemaVentaAutos; -- Asegúrate de usar la base de datos correcta

DROP PROCEDURE IF EXISTS sp_get_vehiculos_destacados;

DELIMITER //

CREATE PROCEDURE sp_get_vehiculos_destacados(
    IN p_condicion VARCHAR(10),
    IN p_limite INT
)
BEGIN
    SELECT 
        v.veh_id, 
        v.veh_anio, 
        v.veh_kilometraje, 
        v.veh_precio, 
        v.veh_ubicacion_ciudad,
        v.veh_condicion,
        m.mar_nombre, 
        mo.mod_nombre,
        (SELECT ima_url 
         FROM ImagenesVehiculo iv 
         WHERE iv.veh_id = v.veh_id AND iv.ima_es_principal = TRUE 
         LIMIT 1) AS imagen_principal_url
    FROM Vehiculos v
    JOIN Marcas m ON v.mar_id = m.mar_id
    JOIN Modelos mo ON v.mod_id = mo.mod_id
    WHERE v.veh_condicion = p_condicion 
      AND v.veh_estado = 'disponible'
    ORDER BY v.veh_fecha_publicacion DESC, v.veh_creado_en DESC
    LIMIT p_limite;
END //

DELIMITER ;
