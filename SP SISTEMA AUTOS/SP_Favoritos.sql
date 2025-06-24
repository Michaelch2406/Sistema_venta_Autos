USE SistemaVentaAutos;

-- SP para Añadir un Vehículo a Favoritos
DROP PROCEDURE IF EXISTS sp_agregar_favorito;
DELIMITER //
CREATE PROCEDURE sp_agregar_favorito(
    IN p_usu_id INT,
    IN p_veh_id INT,
    OUT p_resultado INT, -- 1: Éxito, 0: Ya era favorito, -1: Error
    OUT p_mensaje VARCHAR(255)
)
BEGIN
    SET p_resultado = -1; -- Error por defecto
    SET p_mensaje = 'Error al agregar a favoritos.';

    IF NOT EXISTS (SELECT 1 FROM Usuarios WHERE usu_id = p_usu_id) THEN
        SET p_mensaje = 'Usuario no válido.';
    ELSEIF NOT EXISTS (SELECT 1 FROM Vehiculos WHERE veh_id = p_veh_id AND veh_estado = 'disponible') THEN
        SET p_mensaje = 'Vehículo no válido o no disponible.';
    ELSEIF EXISTS (SELECT 1 FROM Favoritos WHERE usu_id = p_usu_id AND veh_id = p_veh_id) THEN
        SET p_resultado = 0;
        SET p_mensaje = 'Este vehículo ya está en tus favoritos.';
    ELSE
        INSERT INTO Favoritos (usu_id, veh_id) VALUES (p_usu_id, p_veh_id);
        IF ROW_COUNT() > 0 THEN
            SET p_resultado = 1;
            SET p_mensaje = 'Vehículo añadido a favoritos exitosamente.';
        ELSE
            SET p_mensaje = 'No se pudo añadir el vehículo a favoritos.';
        END IF;
    END IF;
END //
DELIMITER ;

-- SP para Quitar un Vehículo de Favoritos
DROP PROCEDURE IF EXISTS sp_quitar_favorito;
DELIMITER //
CREATE PROCEDURE sp_quitar_favorito(
    IN p_usu_id INT,
    IN p_veh_id INT,
    OUT p_resultado INT, -- 1: Éxito, 0: No era favorito, -1: Error
    OUT p_mensaje VARCHAR(255)
)
BEGIN
    SET p_resultado = -1; -- Error por defecto
    SET p_mensaje = 'Error al quitar de favoritos.';

    IF NOT EXISTS (SELECT 1 FROM Favoritos WHERE usu_id = p_usu_id AND veh_id = p_veh_id) THEN
        SET p_resultado = 0;
        SET p_mensaje = 'Este vehículo no estaba en tus favoritos.';
    ELSE
        DELETE FROM Favoritos WHERE usu_id = p_usu_id AND veh_id = p_veh_id;
        IF ROW_COUNT() > 0 THEN
            SET p_resultado = 1;
            SET p_mensaje = 'Vehículo quitado de favoritos exitosamente.';
        ELSE
            SET p_mensaje = 'No se pudo quitar el vehículo de favoritos.';
        END IF;
    END IF;
END //
DELIMITER ;

-- SP para Verificar si un Vehículo es Favorito de un Usuario
DROP PROCEDURE IF EXISTS sp_verificar_favorito;
DELIMITER //
CREATE PROCEDURE sp_verificar_favorito(
    IN p_usu_id INT,
    IN p_veh_id INT,
    OUT p_es_favorito BOOLEAN
)
BEGIN
    SET p_es_favorito = FALSE;
    IF EXISTS (SELECT 1 FROM Favoritos WHERE usu_id = p_usu_id AND veh_id = p_veh_id) THEN
        SET p_es_favorito = TRUE;
    END IF;
END //
DELIMITER ;

-- SP para Obtener los Vehículos Favoritos de un Usuario (para "Mis Favoritos")
DROP PROCEDURE IF EXISTS sp_get_favoritos_por_usuario;
DELIMITER //
CREATE PROCEDURE sp_get_favoritos_por_usuario(
    IN p_usu_id INT
)
BEGIN
    SELECT
        v.veh_id,
        v.veh_condicion,
        v.veh_anio,
        v.veh_precio,
        v.veh_estado,
        v.veh_ubicacion_ciudad,
        v.veh_ubicacion_provincia,
        m.mar_nombre,
        mo.mod_nombre,
        tv.tiv_nombre,
        (SELECT ima_url FROM ImagenesVehiculo iv WHERE iv.veh_id = v.veh_id AND iv.ima_es_principal = TRUE LIMIT 1) AS imagen_principal_url,
        f.fav_fecha_agregado
    FROM Favoritos f
    JOIN Vehiculos v ON f.veh_id = v.veh_id
    JOIN Marcas m ON v.mar_id = m.mar_id
    JOIN Modelos mo ON v.mod_id = mo.mod_id
    JOIN TiposVehiculo tv ON v.tiv_id = tv.tiv_id
    WHERE f.usu_id = p_usu_id AND v.veh_estado = 'disponible' -- Opcional: solo mostrar favoritos si aún están disponibles
    ORDER BY f.fav_fecha_agregado DESC;
END //
DELIMITER ;