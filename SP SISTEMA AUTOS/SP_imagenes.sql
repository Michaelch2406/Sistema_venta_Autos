Use sistemaventaautos;
DROP PROCEDURE IF EXISTS sp_actualizar_orden_imagenes;
DELIMITER //
CREATE PROCEDURE sp_actualizar_orden_imagenes(
    IN p_veh_id INT,
    IN p_orden_ids VARCHAR(1000) -- Ej: '15,14,17,16'
)
BEGIN
    -- Actualiza el campo ima_orden para cada imagen del vehículo
    -- usando la posición que tiene en la cadena de texto p_orden_ids.
    -- FIND_IN_SET devuelve la posición (1, 2, 3...) de un valor en una lista separada por comas.
    UPDATE ImagenesVehiculo
    SET ima_orden = FIND_IN_SET(ima_id, p_orden_ids) - 1 -- Restamos 1 para que el orden empiece en 0
    WHERE veh_id = p_veh_id;
END //
DELIMITER ;