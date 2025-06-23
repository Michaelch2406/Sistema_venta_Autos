USE SistemaVentaAutos;

-- MARCAS --
DROP PROCEDURE IF EXISTS sp_admin_get_all_marcas;
DELIMITER //
CREATE PROCEDURE sp_admin_get_all_marcas()
BEGIN
    SELECT mar_id, mar_nombre, mar_logo_url 
    FROM Marcas 
    ORDER BY mar_nombre ASC;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS sp_admin_insertar_marca;
DELIMITER //
CREATE PROCEDURE sp_admin_insertar_marca(
    IN p_mar_nombre VARCHAR(100),
    IN p_mar_logo_url VARCHAR(255),
    OUT p_mar_id_insertado INT,
    OUT p_resultado INT,
    OUT p_mensaje VARCHAR(255)
)
BEGIN
    SET p_resultado = 0;
    SET p_mensaje = 'Error al insertar la marca.';
    IF EXISTS (SELECT 1 FROM Marcas WHERE mar_nombre = p_mar_nombre) THEN
        SET p_mensaje = 'El nombre de la marca ya existe.';
    ELSE
        INSERT INTO Marcas (mar_nombre, mar_logo_url) VALUES (p_mar_nombre, p_mar_logo_url);
        IF ROW_COUNT() > 0 THEN
            SET p_mar_id_insertado = LAST_INSERT_ID();
            SET p_resultado = 1;
            SET p_mensaje = 'Marca insertada exitosamente.';
        END IF;
    END IF;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS sp_admin_actualizar_marca;
DELIMITER //
CREATE PROCEDURE sp_admin_actualizar_marca(
    IN p_mar_id INT,
    IN p_mar_nombre VARCHAR(100),
    IN p_mar_logo_url VARCHAR(255),
    OUT p_resultado INT,
    OUT p_mensaje VARCHAR(255)
)
BEGIN
    SET p_resultado = 0;
    SET p_mensaje = 'Error al actualizar la marca o marca no encontrada.';
    IF EXISTS (SELECT 1 FROM Marcas WHERE mar_nombre = p_mar_nombre AND mar_id != p_mar_id) THEN
        SET p_mensaje = 'El nombre de la marca ya existe para otra marca.';
    ELSE
        UPDATE Marcas 
        SET mar_nombre = p_mar_nombre, mar_logo_url = p_mar_logo_url, mar_actualizado_en = CURRENT_TIMESTAMP
        WHERE mar_id = p_mar_id;
        IF ROW_COUNT() > 0 THEN
            SET p_resultado = 1;
            SET p_mensaje = 'Marca actualizada exitosamente.';
        ELSE
             -- Podría ser que no se encontraron filas o los datos son los mismos
            IF EXISTS (SELECT 1 FROM Marcas WHERE mar_id = p_mar_id) THEN
                SET p_resultado = 1; -- Considerar éxito si los datos son idénticos y la marca existe
                SET p_mensaje = 'No se realizaron cambios en la marca (datos idénticos o marca no encontrada).';
            END IF;
        END IF;
    END IF;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS sp_admin_eliminar_marca;
DELIMITER //
CREATE PROCEDURE sp_admin_eliminar_marca(
    IN p_mar_id INT,
    OUT p_resultado INT,
    OUT p_mensaje VARCHAR(255)
)
BEGIN
    SET p_resultado = 0;
    SET p_mensaje = 'Error al eliminar la marca.';
    -- Verificar si hay modelos asociados (la FK con ON DELETE RESTRICT/CASCADE lo maneja, pero es bueno informar)
    IF EXISTS (SELECT 1 FROM Modelos WHERE mar_id = p_mar_id) THEN
        SET p_mensaje = 'No se puede eliminar la marca porque tiene modelos asociados. Elimine los modelos primero.';
    ELSE
        DELETE FROM Marcas WHERE mar_id = p_mar_id;
        IF ROW_COUNT() > 0 THEN
            SET p_resultado = 1;
            SET p_mensaje = 'Marca eliminada exitosamente.';
        ELSE
            SET p_mensaje = 'Marca no encontrada o ya eliminada.';
        END IF;
    END IF;
END //
DELIMITER ;


-- MODELOS --
DROP PROCEDURE IF EXISTS sp_admin_get_modelos_por_marca;
DELIMITER //
CREATE PROCEDURE sp_admin_get_modelos_por_marca(
    IN p_mar_id INT
)
BEGIN
    SELECT mod_id, mar_id, mod_nombre 
    FROM Modelos 
    WHERE mar_id = p_mar_id 
    ORDER BY mod_nombre ASC;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS sp_admin_insertar_modelo;
DELIMITER //
CREATE PROCEDURE sp_admin_insertar_modelo(
    IN p_mar_id INT,
    IN p_mod_nombre VARCHAR(100),
    OUT p_mod_id_insertado INT,
    OUT p_resultado INT,
    OUT p_mensaje VARCHAR(255)
)
BEGIN
    SET p_resultado = 0;
    SET p_mensaje = 'Error al insertar el modelo.';
    IF NOT EXISTS (SELECT 1 FROM Marcas WHERE mar_id = p_mar_id) THEN
        SET p_mensaje = 'La marca especificada no existe.';
    ELSEIF EXISTS (SELECT 1 FROM Modelos WHERE mar_id = p_mar_id AND mod_nombre = p_mod_nombre) THEN
        SET p_mensaje = 'El nombre del modelo ya existe para esta marca.';
    ELSE
        INSERT INTO Modelos (mar_id, mod_nombre) VALUES (p_mar_id, p_mod_nombre);
        IF ROW_COUNT() > 0 THEN
            SET p_mod_id_insertado = LAST_INSERT_ID();
            SET p_resultado = 1;
            SET p_mensaje = 'Modelo insertado exitosamente.';
        END IF;
    END IF;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS sp_admin_actualizar_modelo;
DELIMITER //
CREATE PROCEDURE sp_admin_actualizar_modelo(
    IN p_mod_id INT,
    IN p_mar_id INT, -- Permitir cambiar la marca del modelo si es necesario, aunque usualmente no se hace
    IN p_mod_nombre VARCHAR(100),
    OUT p_resultado INT,
    OUT p_mensaje VARCHAR(255)
)
BEGIN
    SET p_resultado = 0;
    SET p_mensaje = 'Error al actualizar el modelo o modelo no encontrado.';
    IF NOT EXISTS (SELECT 1 FROM Marcas WHERE mar_id = p_mar_id) THEN
        SET p_mensaje = 'La nueva marca especificada no existe.';
    ELSEIF EXISTS (SELECT 1 FROM Modelos WHERE mar_id = p_mar_id AND mod_nombre = p_mod_nombre AND mod_id != p_mod_id) THEN
        SET p_mensaje = 'El nombre del modelo ya existe para la marca especificada (en otro modelo).';
    ELSE
        UPDATE Modelos 
        SET mar_id = p_mar_id, mod_nombre = p_mod_nombre, mod_actualizado_en = CURRENT_TIMESTAMP
        WHERE mod_id = p_mod_id;
        IF ROW_COUNT() > 0 THEN
            SET p_resultado = 1;
            SET p_mensaje = 'Modelo actualizado exitosamente.';
        ELSE
            IF EXISTS (SELECT 1 FROM Modelos WHERE mod_id = p_mod_id) THEN
                 SET p_resultado = 1; 
                 SET p_mensaje = 'No se realizaron cambios en el modelo (datos idénticos o modelo no encontrado).';
            END IF;
        END IF;
    END IF;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS sp_admin_eliminar_modelo;
DELIMITER //
CREATE PROCEDURE sp_admin_eliminar_modelo(
    IN p_mod_id INT,
    OUT p_resultado INT,
    OUT p_mensaje VARCHAR(255)
)
BEGIN
    SET p_resultado = 0;
    SET p_mensaje = 'Error al eliminar el modelo.';
    -- Verificar si hay vehículos asociados (la FK con ON DELETE RESTRICT lo previene)
    IF EXISTS (SELECT 1 FROM Vehiculos WHERE mod_id = p_mod_id) THEN
        SET p_mensaje = 'No se puede eliminar el modelo porque tiene vehículos asociados. Elimine o reasigne los vehículos primero.';
    ELSE
        DELETE FROM Modelos WHERE mod_id = p_mod_id;
        IF ROW_COUNT() > 0 THEN
            SET p_resultado = 1;
            SET p_mensaje = 'Modelo eliminado exitosamente.';
        ELSE
            SET p_mensaje = 'Modelo no encontrado o ya eliminado.';
        END IF;
    END IF;
END //
DELIMITER ;