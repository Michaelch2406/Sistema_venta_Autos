-- SP para obtener datos completos de una factura
DELIMITER //
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
        comp.usu_nombre as comprador_nombre,
        comp.usu_apellido as comprador_apellido,
        comp.usu_cedula as comprador_cedula,
        comp.usu_email as comprador_email,
        comp.usu_telefono as comprador_telefono,
        comp.usu_direccion as comprador_direccion,
        
        -- Datos del vendedor
        vend.usu_nombre as vendedor_nombre,
        vend.usu_apellido as vendedor_apellido,
        vend.usu_cedula as vendedor_cedula,
        vend.usu_email as vendedor_email,
        
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

-- Primero eliminar el trigger si existe
DROP TRIGGER IF EXISTS trg_generar_codigo_factura;

-- Trigger mejorado para generar el código de factura
DELIMITER //
CREATE TRIGGER trg_generar_codigo_factura
AFTER INSERT ON ventas
FOR EACH ROW
BEGIN
    DECLARE nuevo_codigo VARCHAR(20);
    DECLARE contador INT;
    
    -- Obtener el siguiente número secuencial
    SET contador = (SELECT IFNULL(MAX(dv_id), 0) + 1 FROM detalles_venta);
    
    -- Generar código con formato AT-YYYYMM-000001
    SET nuevo_codigo = CONCAT('AT-', DATE_FORMAT(NOW(), '%Y%m'), '-', LPAD(contador, 6, '0'));
    
    -- Insertar el detalle de venta con el código generado
    INSERT INTO detalles_venta (vnt_id, dv_codigo_factura)
    VALUES (NEW.vnt_id, nuevo_codigo);
END //
DELIMITER ;