
-- Elimina el procedimiento almacenado si ya existe para evitar errores en la creación.
DROP PROCEDURE IF EXISTS sp_obtener_datos_factura;

DELIMITER //

-- SP para obtener datos completos de una factura
CREATE PROCEDURE sp_obtener_datos_factura(IN p_vnt_id INT)
BEGIN
    SELECT
        v.vnt_id,
        v.vnt_fecha_venta,
        v.vnt_precio_final,
        v.vnt_notas,
        dv.dv_codigo_factura,
        dv.dv_fecha_creacion,

        -- Datos del comprador
        comp.usu_nombre    AS comprador_nombre,
        comp.usu_apellido  AS comprador_apellido,
        comp.usu_cedula    AS comprador_cedula,
        comp.usu_email     AS comprador_email,
        comp.usu_telefono  AS comprador_telefono,
        comp.usu_direccion AS comprador_direccion,

        -- Datos del vendedor
        vend.usu_nombre    AS vendedor_nombre,
        vend.usu_apellido  AS vendedor_apellido,
        vend.usu_cedula    AS vendedor_cedula,
        vend.usu_email     AS vendedor_email,

        -- Datos del vehículo
        veh.veh_anio,
        veh.veh_kilometraje,
        veh.veh_condicion,
        veh.veh_placa,
        veh.veh_vin,
        veh.veh_color_exterior,
        veh.veh_descripcion,

        -- Datos de marca y modelo
        m.mar_nombre,
        mo.mod_nombre,

        -- Datos del tipo de vehículo
        tv.tiv_nombre

    FROM ventas v
    INNER JOIN detalles_venta dv ON v.vnt_id = dv.vnt_id
    INNER JOIN usuarios comp ON v.comprador_id = comp.usu_id
    INNER JOIN usuarios vend ON v.vendedor_id = vend.usu_id
    INNER JOIN vehiculos veh ON v.vehiculo_id = veh.veh_id
    INNER JOIN marcas m ON veh.mar_id = m.mar_id
    INNER JOIN modelos mo ON veh.mod_id = mo.mod_id
    INNER JOIN tiposvehiculo tv ON veh.tiv_id = tv.tiv_id

    WHERE v.vnt_id = p_vnt_id;
END //

DELIMITER ;


-- Elimina el trigger si ya existe para evitar errores en la creación.
DROP TRIGGER IF EXISTS trg_generar_codigo_factura;

DELIMITER //

-- Elimina el trigger si ya existe para evitar errores en la creación.
DROP TRIGGER IF EXISTS trg_generar_codigo_factura;

-- Cambiamos el delimitador para que MySQL trate todo el bloque como una sola instrucción.
DELIMITER //

CREATE TRIGGER trg_generar_codigo_factura
AFTER INSERT ON ventas
FOR EACH ROW
BEGIN
    -- Declaración de variables. Cada una debe terminar con ';'
    DECLARE nuevo_codigo VARCHAR(20); -- <-- FALTABA EL PUNTO Y COMA AQUÍ.
    DECLARE contador INT;             -- <-- ES BUENA PRÁCTICA AÑADIRLO AQUÍ TAMBIÉN.

    -- Obtener el siguiente número secuencial. La instrucción SET también debe terminar con ';'.
    SET contador = (SELECT IFNULL(MAX(dv_id), 0) + 1 FROM detalles_venta);

    -- Generar código con formato AT-YYYYMM-000001
    SET nuevo_codigo = CONCAT('AT-', DATE_FORMAT(NOW(), '%Y%m'), '-', LPAD(contador, 6, '0'));

    -- Insertar el detalle de venta con el código generado. La instrucción INSERT también debe terminar con ';'.
    INSERT INTO detalles_venta (vnt_id, dv_codigo_factura)
    VALUES (NEW.vnt_id, nuevo_codigo);

-- El 'END' va seguido del nuevo delimitador que definimos al principio.
END //

-- Devolvemos el delimitador a su estado original.
DELIMITER ;

-- Stored procedure para registrar una nueva venta
DELIMITER $$
CREATE PROCEDURE sp_registrar_venta(
    IN p_cit_id INT,
    IN p_precio_final DECIMAL(10, 2),
    IN p_comprador_id INT,
    IN p_vendedor_id INT,
    IN p_vehiculo_id INT,
    IN p_notas TEXT
)
BEGIN
    INSERT INTO ventas (cit_id_venta, vnt_precio_final, comprador_id, vendedor_id, vehiculo_id, vnt_notas)
    VALUES (p_cit_id, p_precio_final, p_comprador_id, p_vendedor_id, p_vehiculo_id, p_notas);
END$$
DELIMITER ;

-- Stored procedure para obtener el detalle de una venta
DELIMITER $$
CREATE PROCEDURE sp_obtener_detalle_venta(
    IN p_vnt_id INT
)
BEGIN
    SELECT 
        v.vnt_id,
        v.vnt_fecha_venta,
        v.vnt_precio_final,
        CONCAT(veh.veh_marca, ' ', veh.veh_modelo) AS vehiculo_nombre,
        CONCAT(comp.usu_nombre, ' ', comp.usu_apellido) AS comprador_nombre,
        CONCAT(vend.usu_nombre, ' ', vend.usu_apellido) AS vendedor_nombre,
        dv.dv_codigo_factura
    FROM ventas v
    JOIN vehiculos veh ON v.vehiculo_id = veh.veh_id
    JOIN usuarios comp ON v.comprador_id = comp.usu_id
    JOIN usuarios vend ON v.vendedor_id = vend.usu_id
    JOIN detalles_venta dv ON v.vnt_id = dv.vnt_id
    WHERE v.vnt_id = p_vnt_id;
END$$
DELIMITER ;
