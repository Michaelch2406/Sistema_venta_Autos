-- Nueva tabla para registrar las ventas
CREATE TABLE IF NOT EXISTS ventas (
    vnt_id INT AUTO_INCREMENT PRIMARY KEY,
    cit_id_venta INT NOT NULL,
    vnt_fecha_venta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    vnt_precio_final DECIMAL(10, 2) NOT NULL,
    comprador_id INT NOT NULL,
    vendedor_id INT NOT NULL,
    vehiculo_id INT NOT NULL,
    vnt_notas TEXT,
    FOREIGN KEY (cit_id_venta) REFERENCES citas(cit_id),
    FOREIGN KEY (comprador_id) REFERENCES usuarios(usu_id),
    FOREIGN KEY (vendedor_id) REFERENCES usuarios(usu_id),
    FOREIGN KEY (vehiculo_id) REFERENCES vehiculos(veh_id)
);

-- Nueva tabla para los detalles de la venta
CREATE TABLE IF NOT EXISTS detalles_venta (
    dv_id INT AUTO_INCREMENT PRIMARY KEY,
    vnt_id INT NOT NULL,
    dv_codigo_factura VARCHAR(20) NOT NULL,
    dv_fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vnt_id) REFERENCES ventas(vnt_id)
);

-- Trigger para generar el código de factura
DELIMITER $$
CREATE TRIGGER trg_generar_codigo_factura
AFTER INSERT ON ventas
FOR EACH ROW
BEGIN
    DECLARE nuevo_id INT;
    SET nuevo_id = (SELECT IFNULL(MAX(dv_id), 0) + 1 FROM detalles_venta);
    INSERT INTO detalles_venta (vnt_id, dv_codigo_factura)
    VALUES (NEW.vnt_id, CONCAT('AT-', LPAD(nuevo_id, 6, '0')));
END$$
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
