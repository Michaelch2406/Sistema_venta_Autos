-- Script para crear la base de datos y todas las tablas para 'sistemaventaautos'
-- --------------------------------------------------------------------------------

-- Seleccionar la base de datos. Asegúrate de que exista.
-- CREATE DATABASE IF NOT EXISTS sistemaventaautos;
USE sistemaventaautos;

-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
-- TABLAS MAESTRAS (Sin dependencias externas)
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

-- Tabla: roles
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `rol_id` int NOT NULL AUTO_INCREMENT,
  `rol_nombre` varchar(50) NOT NULL,
  `rol_descripcion` text,
  `rol_activo` tinyint(1) DEFAULT '1',
  `rol_creado_en` timestamp DEFAULT CURRENT_TIMESTAMP,
  `rol_actualizado_en` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`rol_id`),
  UNIQUE KEY `uk_roles_rol_nombre` (`rol_nombre`)
);

-- Tabla: marcas
DROP TABLE IF EXISTS `marcas`;
CREATE TABLE `marcas` (
  `mar_id` int NOT NULL AUTO_INCREMENT,
  `mar_nombre` varchar(100) NOT NULL,
  `mar_logo_url` varchar(255) DEFAULT NULL,
  `mar_actualizado_en` datetime DEFAULT NULL,
  PRIMARY KEY (`mar_id`),
  UNIQUE KEY `uk_marcas_mar_nombre` (`mar_nombre`)
);

-- Tabla: tiposvehiculo
DROP TABLE IF EXISTS `tiposvehiculo`;
CREATE TABLE `tiposvehiculo` (
  `tiv_id` int NOT NULL AUTO_INCREMENT,
  `tiv_nombre` varchar(100) NOT NULL,
  `tiv_descripcion` text,
  `tiv_icono_url` varchar(255) DEFAULT NULL,
  `tiv_activo` tinyint(1) DEFAULT '1',
  `tiv_creado_en` timestamp DEFAULT CURRENT_TIMESTAMP,
  `tiv_actualizado_en` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`tiv_id`),
  UNIQUE KEY `uk_tiposvehiculo_tiv_nombre` (`tiv_nombre`)
);

-- Tabla: formaspago
DROP TABLE IF EXISTS `formaspago`;
CREATE TABLE `formaspago` (
  `fpa_id` int NOT NULL AUTO_INCREMENT,
  `fpa_nombre` varchar(50) NOT NULL,
  `fpa_activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`fpa_id`),
  UNIQUE KEY `uk_formaspago_fpa_nombre` (`fpa_nombre`)
);

-- Tabla: ofertaspromociones
DROP TABLE IF EXISTS `ofertaspromociones`;
CREATE TABLE `ofertaspromociones` (
  `ofe_id` int NOT NULL AUTO_INCREMENT,
  `ofe_nombre` varchar(150) NOT NULL,
  `ofe_descripcion` text,
  `ofe_tipo` enum('descuento_porcentaje','descuento_fijo','envio_gratis','otro') NOT NULL,
  `ofe_valor` decimal(10,2) DEFAULT NULL,
  `ofe_codigo_cupon` varchar(50) DEFAULT NULL,
  `ofe_fecha_inicio` datetime NOT NULL,
  `ofe_fecha_fin` datetime NOT NULL,
  `ofe_estado` enum('activa','inactiva','caducada') DEFAULT 'activa',
  `ofe_uso_maximo` int DEFAULT NULL,
  `ofe_uso_por_cliente` int DEFAULT '1',
  `ofe_creado_en` timestamp DEFAULT CURRENT_TIMESTAMP,
  `ofe_actualizado_en` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ofe_id`),
  UNIQUE KEY `uk_ofertaspromociones_ofe_codigo_cupon` (`ofe_codigo_cupon`),
  KEY `idx_ofertaspromociones_ofe_estado` (`ofe_estado`)
);


-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
-- TABLAS CON DEPENDENCIAS DE PRIMER NIVEL
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

-- Tabla: usuarios
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `usu_id` int NOT NULL AUTO_INCREMENT,
  `rol_id` int NOT NULL,
  `usu_usuario` varchar(50) NOT NULL,
  `usu_nombre` varchar(100) NOT NULL,
  `usu_apellido` varchar(100) NOT NULL,
  `usu_email` varchar(100) NOT NULL,
  `usu_password` varchar(255) NOT NULL,
  `usu_telefono` varchar(20) DEFAULT NULL,
  `usu_direccion` varchar(255) DEFAULT NULL,
  `usu_fnacimiento` date DEFAULT NULL,
  `usu_verificado` tinyint(1) DEFAULT '0',
  `usu_creado_en` timestamp DEFAULT CURRENT_TIMESTAMP,
  `usu_actualizado_en` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `usu_cedula` varchar(13) NOT NULL,
  PRIMARY KEY (`usu_id`),
  UNIQUE KEY `uk_usuarios_usu_usuario` (`usu_usuario`),
  UNIQUE KEY `uk_usuarios_usu_email` (`usu_email`),
  UNIQUE KEY `uk_usuarios_usu_cedula` (`usu_cedula`),
  KEY `fk_usuarios_roles` (`rol_id`),
  CONSTRAINT `fk_usuarios_roles` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`rol_id`)
);

-- Tabla: modelos
DROP TABLE IF EXISTS `modelos`;
CREATE TABLE `modelos` (
  `mod_id` int NOT NULL AUTO_INCREMENT,
  `mar_id` int NOT NULL,
  `mod_nombre` varchar(100) NOT NULL,
  `mod_actualizado_en` datetime DEFAULT NULL,
  PRIMARY KEY (`mod_id`),
  KEY `fk_modelos_marcas` (`mar_id`),
  CONSTRAINT `fk_modelos_marcas` FOREIGN KEY (`mar_id`) REFERENCES `marcas` (`mar_id`)
);

-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
-- TABLA CENTRAL: vehiculos
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
DROP TABLE IF EXISTS `vehiculos`;
CREATE TABLE `vehiculos` (
  `veh_id` int NOT NULL AUTO_INCREMENT,
  `mar_id` int NOT NULL,
  `mod_id` int NOT NULL,
  `tiv_id` int NOT NULL,
  `veh_subtipo_vehiculo` varchar(100) DEFAULT NULL,
  `usu_id_gestor` int DEFAULT NULL,
  `veh_condicion` enum('nuevo','usado') NOT NULL,
  `veh_anio` int NOT NULL,
  `veh_kilometraje` int NOT NULL,
  `veh_precio` decimal(12,2) NOT NULL,
  `veh_vin` varchar(20) DEFAULT NULL,
  `veh_placa` varchar(10) DEFAULT NULL,
  `veh_ubicacion_ciudad` varchar(100) NOT NULL,
  `veh_ubicacion_provincia` varchar(100) NOT NULL,
  `veh_color_exterior` varchar(50) DEFAULT NULL,
  `veh_color_interior` varchar(50) DEFAULT NULL,
  `veh_detalles_motor` text,
  `veh_tipo_transmision` varchar(50) DEFAULT NULL,
  `veh_sistema_climatizacion` enum('Ninguno','Aire Acondicionado','Climatizador Manual','Climatizador Automatico','Climatizador Bi-Zona','Otro') DEFAULT NULL,
  `veh_ultimo_digito_placa` char(1) DEFAULT NULL,
  `veh_placa_provincia_origen` varchar(100) DEFAULT NULL,
  `veh_traccion` enum('Delantera','Trasera','4x4','AWD','Otro') DEFAULT NULL,
  `veh_tipo_vidrios` enum('Manuales','Electricos Delanteros','Electricos Completos','Otro') DEFAULT NULL,
  `veh_tipo_combustible` enum('Gasolina','Diesel','Hibrido','Electrico','Flex (Gasolina/Etanol)','GLP','GNV','Otro') DEFAULT NULL,
  `veh_tipo_direccion` enum('Mecanica','Hidraulica','Electroasistida','Electrica','Otra') DEFAULT NULL,
  `veh_descripcion` text,
  `veh_detalles_extra` text,
  `veh_estado` enum('disponible','reservado','vendido','desactivado') DEFAULT 'disponible',
  `veh_fecha_publicacion` date NOT NULL,
  `veh_creado_en` timestamp DEFAULT CURRENT_TIMESTAMP,
  `veh_actualizado_en` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`veh_id`),
  UNIQUE KEY `uk_vehiculos_veh_vin` (`veh_vin`),
  UNIQUE KEY `uk_vehiculos_veh_placa` (`veh_placa`),
  KEY `fk_vehiculos_marcas` (`mar_id`),
  KEY `fk_vehiculos_modelos` (`mod_id`),
  KEY `fk_vehiculos_tiposvehiculo` (`tiv_id`),
  KEY `fk_vehiculos_usuarios_gestor` (`usu_id_gestor`),
  KEY `idx_vehiculos_veh_ubicacion_provincia` (`veh_ubicacion_provincia`),
  KEY `idx_vehiculos_veh_estado` (`veh_estado`),
  CONSTRAINT `fk_vehiculos_marcas` FOREIGN KEY (`mar_id`) REFERENCES `marcas` (`mar_id`),
  CONSTRAINT `fk_vehiculos_modelos` FOREIGN KEY (`mod_id`) REFERENCES `modelos` (`mod_id`),
  CONSTRAINT `fk_vehiculos_tiposvehiculo` FOREIGN KEY (`tiv_id`) REFERENCES `tiposvehiculo` (`tiv_id`),
  CONSTRAINT `fk_vehiculos_usuarios_gestor` FOREIGN KEY (`usu_id_gestor`) REFERENCES `usuarios` (`usu_id`)
);

-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
-- TABLAS TRANSACCIONALES Y DE RELACIÓN
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

-- Tabla: ventas
DROP TABLE IF EXISTS `ventas`;
CREATE TABLE `ventas` (
  `ven_id` int NOT NULL AUTO_INCREMENT,
  `usu_id_comprador` int NOT NULL,
  `usu_id_gestor` int DEFAULT NULL,
  `ofe_id` int DEFAULT NULL,
  `ven_fecha_venta` date NOT NULL,
  `ven_subtotal` decimal(12,2) NOT NULL,
  `ven_descuento` decimal(12,2) DEFAULT '0.00',
  `ven_impuestos` decimal(12,2) DEFAULT '0.00',
  `ven_precio_total` decimal(12,2) NOT NULL,
  `ven_estado` enum('pendiente_pago','pagado_parcial','pagado_completo','en_entrega','completado','cancelado') DEFAULT 'pendiente_pago',
  `ven_notas_internas` text,
  `ven_creado_en` timestamp DEFAULT CURRENT_TIMESTAMP,
  `ven_actualizado_en` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ven_id`),
  KEY `fk_ventas_usuarios_comprador` (`usu_id_comprador`),
  KEY `fk_ventas_usuarios_gestor` (`usu_id_gestor`),
  KEY `fk_ventas_ofertaspromociones` (`ofe_id`),
  KEY `idx_ventas_ven_estado` (`ven_estado`),
  CONSTRAINT `fk_ventas_ofertaspromociones` FOREIGN KEY (`ofe_id`) REFERENCES `ofertaspromociones` (`ofe_id`),
  CONSTRAINT `fk_ventas_usuarios_comprador` FOREIGN KEY (`usu_id_comprador`) REFERENCES `usuarios` (`usu_id`),
  CONSTRAINT `fk_ventas_usuarios_gestor` FOREIGN KEY (`usu_id_gestor`) REFERENCES `usuarios` (`usu_id`)
);

-- Tabla: cotizaciones
DROP TABLE IF EXISTS `cotizaciones`;
CREATE TABLE `cotizaciones` (
  `cot_id` int NOT NULL AUTO_INCREMENT,
  `usu_id_solicitante` int NOT NULL,
  `veh_id` int DEFAULT NULL,
  `cot_detalles_vehiculo_solicitado` text,
  `cot_mensaje` text,
  `cot_estado` enum('pendiente','contactado','cerrado','rechazado') DEFAULT 'pendiente',
  `cot_fecha_solicitud` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cot_id`),
  KEY `fk_cotizaciones_usuarios` (`usu_id_solicitante`),
  KEY `fk_cotizaciones_vehiculos` (`veh_id`),
  KEY `idx_cotizaciones_cot_estado` (`cot_estado`),
  CONSTRAINT `fk_cotizaciones_usuarios` FOREIGN KEY (`usu_id_solicitante`) REFERENCES `usuarios` (`usu_id`),
  CONSTRAINT `fk_cotizaciones_vehiculos` FOREIGN KEY (`veh_id`) REFERENCES `vehiculos` (`veh_id`)
);

-- Tabla: detallesventa
DROP TABLE IF EXISTS `detallesventa`;
CREATE TABLE `detallesventa` (
  `dve_id` int NOT NULL AUTO_INCREMENT,
  `ven_id` int NOT NULL,
  `veh_id` int NOT NULL,
  `dve_precio_unitario` decimal(12,2) NOT NULL,
  `dve_cantidad` int DEFAULT '1',
  PRIMARY KEY (`dve_id`),
  UNIQUE KEY `uk_detallesventa_veh_id` (`veh_id`),
  KEY `fk_detallesventa_ventas` (`ven_id`),
  CONSTRAINT `fk_detallesventa_vehiculos` FOREIGN KEY (`veh_id`) REFERENCES `vehiculos` (`veh_id`),
  CONSTRAINT `fk_detallesventa_ventas` FOREIGN KEY (`ven_id`) REFERENCES `ventas` (`ven_id`)
);

-- Tabla: favoritos
DROP TABLE IF EXISTS `favoritos`;
CREATE TABLE `favoritos` (
  `fav_id` int NOT NULL AUTO_INCREMENT,
  `usu_id` int NOT NULL,
  `veh_id` int NOT NULL,
  `fav_fecha_agregado` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`fav_id`),
  KEY `fk_favoritos_usuarios` (`usu_id`),
  KEY `fk_favoritos_vehiculos` (`veh_id`),
  CONSTRAINT `fk_favoritos_usuarios` FOREIGN KEY (`usu_id`) REFERENCES `usuarios` (`usu_id`),
  CONSTRAINT `fk_favoritos_vehiculos` FOREIGN KEY (`veh_id`) REFERENCES `vehiculos` (`veh_id`)
);

-- Tabla: imagenesvehiculo
DROP TABLE IF EXISTS `imagenesvehiculo`;
CREATE TABLE `imagenesvehiculo` (
  `ima_id` int NOT NULL AUTO_INCREMENT,
  `veh_id` int NOT NULL,
  `ima_url` varchar(255) NOT NULL,
  `ima_es_principal` tinyint(1) DEFAULT '0',
  `ima_creado_en` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ima_id`),
  KEY `fk_imagenesvehiculo_vehiculos` (`veh_id`),
  CONSTRAINT `fk_imagenesvehiculo_vehiculos` FOREIGN KEY (`veh_id`) REFERENCES `vehiculos` (`veh_id`)
);

-- Tabla: videosvehiculo
DROP TABLE IF EXISTS `videosvehiculo`;
CREATE TABLE `videosvehiculo` (
  `viv_id` int NOT NULL AUTO_INCREMENT,
  `veh_id` int NOT NULL,
  `viv_url` varchar(255) NOT NULL,
  `viv_titulo` varchar(150) DEFAULT NULL,
  `viv_plataforma` enum('youtube','vimeo','local','otro') DEFAULT 'otro',
  `viv_id_plataforma` varchar(100) DEFAULT NULL,
  `viv_es_principal` tinyint(1) DEFAULT '0',
  `viv_creado_en` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`viv_id`),
  KEY `fk_videosvehiculo_vehiculos` (`veh_id`),
  CONSTRAINT `fk_videosvehiculo_vehiculos` FOREIGN KEY (`veh_id`) REFERENCES `vehiculos` (`veh_id`)
);

-- Tabla: pagos
DROP TABLE IF EXISTS `pagos`;
CREATE TABLE `pagos` (
  `pag_id` int NOT NULL AUTO_INCREMENT,
  `ven_id` int NOT NULL,
  `fpa_id` int NOT NULL,
  `pag_monto` decimal(12,2) NOT NULL,
  `pag_fecha` timestamp DEFAULT CURRENT_TIMESTAMP,
  `pag_referencia` varchar(100) DEFAULT NULL,
  `pag_estado` enum('pendiente','completado','fallido','reembolsado') DEFAULT 'pendiente',
  `pag_notas` text,
  PRIMARY KEY (`pag_id`),
  KEY `fk_pagos_ventas` (`ven_id`),
  KEY `fk_pagos_formaspago` (`fpa_id`),
  KEY `idx_pagos_pag_estado` (`pag_estado`),
  CONSTRAINT `fk_pagos_formaspago` FOREIGN KEY (`fpa_id`) REFERENCES `formaspago` (`fpa_id`),
  CONSTRAINT `fk_pagos_ventas` FOREIGN KEY (`ven_id`) REFERENCES `ventas` (`ven_id`)
);

-- Tabla: vehiculooferta (Tabla de enlace)
DROP TABLE IF EXISTS `vehiculooferta`;
CREATE TABLE `vehiculooferta` (
  `vof_id` int NOT NULL AUTO_INCREMENT,
  `veh_id` int NOT NULL,
  `ofe_id` int NOT NULL,
  PRIMARY KEY (`vof_id`),
  KEY `fk_vehiculooferta_vehiculos` (`veh_id`),
  KEY `fk_vehiculooferta_ofertaspromociones` (`ofe_id`),
  CONSTRAINT `fk_vehiculooferta_ofertaspromociones` FOREIGN KEY (`ofe_id`) REFERENCES `ofertaspromociones` (`ofe_id`),
  CONSTRAINT `fk_vehiculooferta_vehiculos` FOREIGN KEY (`veh_id`) REFERENCES `vehiculos` (`veh_id`)
);