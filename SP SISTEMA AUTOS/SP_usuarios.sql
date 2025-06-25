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


USE SistemaVentaAutos; -- Asegúrate de que sea el nombre correcto de tu BD

-- SP para obtener los datos del usuario para la página de configuración
DROP PROCEDURE IF EXISTS sp_get_usuario_configuracion;
DELIMITER //
CREATE PROCEDURE sp_get_usuario_configuracion(
    IN p_usu_id INT
)
BEGIN
    SELECT
        usu_id,
        usu_usuario, -- Generalmente no editable
        usu_nombre,
        usu_apellido,
        usu_email,   -- Manejo especial si se permite editar
        usu_cedula,  -- Nuevo campo
        usu_telefono,
        usu_direccion,
        usu_fnacimiento
    FROM Usuarios
    WHERE usu_id = p_usu_id;
END //
DELIMITER ;

-- SP para actualizar los datos del perfil del usuario
DROP PROCEDURE IF EXISTS sp_actualizar_perfil_usuario;
DELIMITER //
CREATE PROCEDURE sp_actualizar_perfil_usuario(
    IN p_usu_id INT,
    IN p_nombre VARCHAR(100),
    IN p_apellido VARCHAR(100),
    IN p_cedula VARCHAR(13),
    IN p_telefono VARCHAR(20),
    IN p_direccion VARCHAR(255),
    IN p_fnacimiento DATE,
    OUT p_resultado INT,       -- 1: Éxito, 0: Error de validación, -1: Error general
    OUT p_mensaje VARCHAR(255)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SET p_resultado = -1;
        SET p_mensaje = 'Error interno del servidor al actualizar el perfil.';
        ROLLBACK;
    END;

    SET p_resultado = -1; -- Por defecto

    -- Validaciones básicas (puedes añadir más)
    IF p_nombre IS NULL OR LENGTH(TRIM(p_nombre)) = 0 THEN
        SET p_resultado = 0;
        SET p_mensaje = 'El nombre no puede estar vacío.';
    ELSEIF p_apellido IS NULL OR LENGTH(TRIM(p_apellido)) = 0 THEN
        SET p_resultado = 0;
        SET p_mensaje = 'El apellido no puede estar vacío.';
    ELSEIF p_cedula IS NULL OR LENGTH(TRIM(p_cedula)) = 0 THEN
        SET p_resultado = 0;
        SET p_mensaje = 'La cédula no puede estar vacía.';
    -- Aquí puedes añadir una validación de formato de cédula si lo deseas a nivel de SP,
    -- aunque la validación de la lógica ecuatoriana es mejor en PHP/JS.
    -- Por ejemplo, verificar longitud:
    ELSEIF CHAR_LENGTH(p_cedula) NOT IN (10, 13) THEN 
        SET p_resultado = 0;
        SET p_mensaje = 'La cédula debe tener 10 o 13 dígitos.';
    ELSEIF EXISTS (SELECT 1 FROM Usuarios WHERE usu_cedula = p_cedula AND usu_id != p_usu_id) THEN
        SET p_resultado = 0;
        SET p_mensaje = 'La cédula ingresada ya está registrada por otro usuario.';
    ELSE
        START TRANSACTION;
        UPDATE Usuarios
        SET
            usu_nombre = TRIM(p_nombre),
            usu_apellido = TRIM(p_apellido),
            usu_cedula = TRIM(p_cedula),
            usu_telefono = TRIM(p_telefono),
            usu_direccion = TRIM(p_direccion),
            usu_fnacimiento = p_fnacimiento,
            usu_actualizado_en = CURRENT_TIMESTAMP
        WHERE usu_id = p_usu_id;

        IF ROW_COUNT() > 0 THEN
            SET p_resultado = 1;
            SET p_mensaje = 'Perfil actualizado exitosamente.';
            COMMIT;
        ELSE
            -- Podría ser que no se actualizó nada porque los datos eran los mismos
            -- o el usu_id no existe (aunque esto último es menos probable aquí).
            -- Para dar una respuesta más precisa, podrías verificar si el usuario existe primero.
            SET p_resultado = 1; -- Considerar éxito si no hay error y los datos son iguales
            SET p_mensaje = 'No se realizaron cambios o los datos son los mismos.';
            COMMIT; 
            -- O si quieres ser más estricto:
            -- SET p_resultado = 0;
            -- SET p_mensaje = 'No se pudo actualizar el perfil o el usuario no existe.';
            -- ROLLBACK;
        END IF;
    END IF;
END //
DELIMITER ;

-- SP para cambiar la contraseña del usuario
DROP PROCEDURE IF EXISTS sp_cambiar_contrasena;
DELIMITER //
CREATE PROCEDURE sp_cambiar_contrasena(
    IN p_usu_id INT,
    IN p_pass_actual_ingresada VARCHAR(255), -- Contraseña actual tal como la ingresa el usuario
    IN p_pass_nueva VARCHAR(255),
    OUT p_resultado INT,          -- 1: Éxito, 0: Contraseña actual incorrecta, -1: Error, 2: Contraseña nueva no válida
    OUT p_mensaje VARCHAR(255)
)
BEGIN
    DECLARE v_pass_actual_guardada VARCHAR(255);

    SET p_resultado = -1; -- Por defecto

    -- Obtener la contraseña actual hasheada del usuario
    SELECT usu_password INTO v_pass_actual_guardada FROM Usuarios WHERE usu_id = p_usu_id;

    IF v_pass_actual_guardada IS NULL THEN
        SET p_resultado = -1;
        SET p_mensaje = 'Usuario no encontrado.';
    -- Aquí necesitas una función para verificar la contraseña. 
    -- MySQL no tiene password_verify() directamente en SQL estándar para SPs.
    -- La verificación de p_pass_actual_ingresada contra v_pass_actual_guardada DEBE hacerse en PHP usando password_verify().
    -- Por lo tanto, este SP asumirá que PHP ya verificó la contraseña actual.
    -- O, si p_pass_actual_ingresada es la contraseña ya hasheada por PHP (lo cual no es lo usual para este flujo),
    -- entonces se podría comparar directamente si el hash es idéntico (solo si no hay salt variable por usuario).
    -- Modificación: Este SP no verificará la contraseña actual. PHP lo hará y solo llamará a este SP si la actual es correcta.
    -- Este SP solo actualizará la contraseña con la nueva.

    /* -- COMENTADO: Lógica de verificación de contraseña actual que es mejor en PHP
    ELSEIF (SELECT PASSWORD(p_pass_actual_ingresada)) != v_pass_actual_guardada THEN -- ESTO ES INCORRECTO SI USAS password_hash() de PHP
        SET p_resultado = 0;
        SET p_mensaje = 'La contraseña actual es incorrecta.';
    */

    -- Validación de la nueva contraseña (ej. longitud mínima)
    ELSEIF CHAR_LENGTH(p_pass_nueva) < 8 THEN -- Ejemplo: mínimo 8 caracteres
        SET p_resultado = 2;
        SET p_mensaje = 'La nueva contraseña debe tener al menos 8 caracteres.';
    ELSE
        -- Aquí la nueva contraseña (p_pass_nueva) DEBE ser la contraseña YA HASHEADA por PHP con password_hash()
        UPDATE Usuarios
        SET usu_password = p_pass_nueva, -- p_pass_nueva ya debe estar hasheada
            usu_actualizado_en = CURRENT_TIMESTAMP
        WHERE usu_id = p_usu_id;

        IF ROW_COUNT() > 0 THEN
            SET p_resultado = 1;
            SET p_mensaje = 'Contraseña actualizada exitosamente.';
        ELSE
            SET p_resultado = -1;
            SET p_mensaje = 'No se pudo actualizar la contraseña.';
        END IF;
    END IF;
END //
DELIMITER ;
