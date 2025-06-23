USE SistemaVentaAutos;

-- SP para obtener TODOS los usuarios para el panel de admin
DROP PROCEDURE IF EXISTS sp_get_todos_usuarios_admin;
DELIMITER //
CREATE PROCEDURE sp_get_todos_usuarios_admin()
BEGIN
    SELECT
        u.usu_id,
        u.usu_usuario,
        u.usu_nombre,
        u.usu_apellido,
        u.usu_email,
        u.usu_telefono,
        u.usu_verificado,
        r.rol_id,
        r.rol_nombre,
        u.usu_creado_en
    FROM Usuarios u
    JOIN Roles r ON u.rol_id = r.rol_id
    ORDER BY u.usu_id ASC;
END //
DELIMITER ;

-- SP para obtener todos los roles (para un select en el formulario)
DROP PROCEDURE IF EXISTS sp_get_roles_para_select;
DELIMITER //
CREATE PROCEDURE sp_get_roles_para_select()
BEGIN
    SELECT rol_id, rol_nombre FROM Roles WHERE rol_activo = TRUE ORDER BY rol_nombre ASC;
END //
DELIMITER ;

-- SP para obtener un usuario específico por ID para edición
DROP PROCEDURE IF EXISTS sp_get_usuario_por_id_admin;
DELIMITER //
CREATE PROCEDURE sp_get_usuario_por_id_admin(
    IN p_usu_id INT
)
BEGIN
    SELECT
        usu_id,
        rol_id,
        usu_usuario,
        usu_nombre,
        usu_apellido,
        usu_email,
        usu_telefono,
        usu_direccion,
        usu_fnacimiento,
        usu_verificado
    FROM Usuarios
    WHERE usu_id = p_usu_id;
END //
DELIMITER ;

-- SP para crear un nuevo usuario por el administrador
DROP PROCEDURE IF EXISTS sp_crear_usuario_admin;
DELIMITER //
CREATE PROCEDURE sp_crear_usuario_admin(
    IN p_rol_id INT,
    IN p_usu_usuario VARCHAR(50),
    IN p_usu_nombre VARCHAR(100),
    IN p_usu_apellido VARCHAR(100),
    IN p_usu_email VARCHAR(100),
    IN p_usu_password_hash VARCHAR(255), -- Contraseña ya hasheada
    IN p_usu_telefono VARCHAR(20),
    IN p_usu_direccion VARCHAR(255),
    IN p_usu_fnacimiento DATE,
    IN p_usu_verificado BOOLEAN,
    OUT p_usu_id_creado INT,
    OUT p_resultado INT,
    OUT p_mensaje VARCHAR(255)
)
BEGIN
    DECLARE v_count_usuario INT;
    DECLARE v_count_email INT;
    SET p_resultado = -2; 
    SET p_mensaje = 'Error desconocido al crear el usuario.';
    SET p_usu_id_creado = NULL;

    SELECT COUNT(*) INTO v_count_usuario FROM Usuarios WHERE usu_usuario = p_usu_usuario;
    IF v_count_usuario > 0 THEN
        SET p_resultado = 0;
        SET p_mensaje = 'El nombre de usuario ya está en uso.';
    ELSE
        SELECT COUNT(*) INTO v_count_email FROM Usuarios WHERE usu_email = p_usu_email;
        IF v_count_email > 0 THEN
            SET p_resultado = -1;
            SET p_mensaje = 'El correo electrónico ya está registrado.';
        ELSE
            INSERT INTO Usuarios (
                rol_id, usu_usuario, usu_nombre, usu_apellido, usu_email,
                usu_password, usu_telefono, usu_direccion, usu_fnacimiento, usu_verificado
            ) VALUES (
                p_rol_id, p_usu_usuario, p_usu_nombre, p_usu_apellido, p_usu_email,
                p_usu_password_hash, p_usu_telefono, p_usu_direccion, p_usu_fnacimiento, p_usu_verificado
            );
            IF ROW_COUNT() > 0 THEN
                SET p_usu_id_creado = LAST_INSERT_ID();
                SET p_resultado = 1;
                SET p_mensaje = 'Usuario creado exitosamente.';
            ELSE
                SET p_mensaje = 'Error al insertar el usuario en la base de datos.';
            END IF;
        END IF;
    END IF;
END //
DELIMITER ;

-- SP para actualizar un usuario por el administrador
DROP PROCEDURE IF EXISTS sp_actualizar_usuario_admin;
DELIMITER //
CREATE PROCEDURE sp_actualizar_usuario_admin(
    IN p_usu_id INT,
    IN p_rol_id INT,
    IN p_usu_usuario VARCHAR(50),
    IN p_usu_nombre VARCHAR(100),
    IN p_usu_apellido VARCHAR(100),
    IN p_usu_email VARCHAR(100),
    IN p_usu_password_hash VARCHAR(255), -- Contraseña ya hasheada, o NULL si no se cambia
    IN p_usu_telefono VARCHAR(20),
    IN p_usu_direccion VARCHAR(255),
    IN p_usu_fnacimiento DATE,
    IN p_usu_verificado BOOLEAN,
    OUT p_resultado INT,
    OUT p_mensaje VARCHAR(255)
)
BEGIN
    DECLARE v_count_usuario INT;
    DECLARE v_count_email INT;
    SET p_resultado = -2;
    SET p_mensaje = 'Error desconocido al actualizar el usuario.';

    SELECT COUNT(*) INTO v_count_usuario FROM Usuarios WHERE usu_usuario = p_usu_usuario AND usu_id != p_usu_id;
    IF v_count_usuario > 0 THEN
        SET p_resultado = 0;
        SET p_mensaje = 'El nombre de usuario ya está en uso por otro usuario.';
    ELSE
        SELECT COUNT(*) INTO v_count_email FROM Usuarios WHERE usu_email = p_usu_email AND usu_id != p_usu_id;
        IF v_count_email > 0 THEN
            SET p_resultado = -1;
            SET p_mensaje = 'El correo electrónico ya está registrado por otro usuario.';
        ELSE
            UPDATE Usuarios SET
                rol_id = p_rol_id,
                usu_usuario = p_usu_usuario,
                usu_nombre = p_usu_nombre,
                usu_apellido = p_usu_apellido,
                usu_email = p_usu_email,
                usu_password = IF(p_usu_password_hash IS NOT NULL AND p_usu_password_hash != '', p_usu_password_hash, usu_password), -- Solo actualiza si se provee una nueva
                usu_telefono = p_usu_telefono,
                usu_direccion = p_usu_direccion,
                usu_fnacimiento = p_usu_fnacimiento,
                usu_verificado = p_usu_verificado,
                usu_actualizado_en = CURRENT_TIMESTAMP
            WHERE usu_id = p_usu_id;

            IF ROW_COUNT() > 0 THEN
                SET p_resultado = 1;
                SET p_mensaje = 'Usuario actualizado exitosamente.';
            ELSE
                -- Podría ser que no se modificaron filas porque los datos eran los mismos
                SET p_resultado = 1; -- Considerar éxito si no hay error, aunque no haya cambios.
                SET p_mensaje = 'No se realizaron cambios o el usuario no fue encontrado.';
            END IF;
        END IF;
    END IF;
END //
DELIMITER ;