-- Script para crear la base de datos y todas las tablas para 'sistemaventaautos'
-- --------------------------------------------------------------------------------

-- Seleccionar la base de datos. Asegúrate de que exista.
-- CREATE DATABASE IF NOT EXISTS sistemaventaautos;
USE sistemaventaautos;

-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
-- TABLAS MAESTRAS (Sin dependencias externas)
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

-- MySQL dump 10.13  Distrib 8.0.41, for Win64 (x86_64)
--
-- Host: localhost    Database: sistemaventaautos


--
-- Table structure for table `citas`
--

DROP TABLE IF EXISTS `citas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `citas` (
  `cit_id` int NOT NULL AUTO_INCREMENT,
  `usu_id_solicitante` int DEFAULT NULL,
  `veh_id` int DEFAULT NULL,
  `cit_detalles_vehiculo_solicitado` text,
  `cit_mensaje` text,
  `cit_estado` enum('pendiente','aprobada','rechazado') NOT NULL DEFAULT 'pendiente',
  `cit_fecha_disponibilidad` date DEFAULT NULL,
  `cit_hora_disponibilidad` varchar(20) DEFAULT NULL,
  `cit_fecha_solicitud` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cit_notas_admin` text,
  PRIMARY KEY (`cit_id`),
  KEY `idx_cot_usuario` (`usu_id_solicitante`),
  KEY `idx_cot_vehiculo` (`veh_id`),
  KEY `idx_cot_estado` (`cit_estado`),
  CONSTRAINT `citas_ibfk_1` FOREIGN KEY (`usu_id_solicitante`) REFERENCES `usuarios` (`usu_id`) ON DELETE CASCADE,
  CONSTRAINT `citas_ibfk_2` FOREIGN KEY (`veh_id`) REFERENCES `vehiculos` (`veh_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `detallesventa`
--

DROP TABLE IF EXISTS `detallesventa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detallesventa` (
  `dve_id` int NOT NULL AUTO_INCREMENT,
  `ven_id` int NOT NULL,
  `veh_id` int NOT NULL,
  `dve_precio_unitario` decimal(12,2) NOT NULL,
  `dve_cantidad` int DEFAULT '1',
  PRIMARY KEY (`dve_id`),
  UNIQUE KEY `veh_id` (`veh_id`),
  KEY `idx_dve_venta` (`ven_id`),
  KEY `idx_dve_vehiculo` (`veh_id`),
  CONSTRAINT `detallesventa_ibfk_1` FOREIGN KEY (`ven_id`) REFERENCES `ventas` (`ven_id`) ON DELETE CASCADE,
  CONSTRAINT `detallesventa_ibfk_2` FOREIGN KEY (`veh_id`) REFERENCES `vehiculos` (`veh_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `favoritos`
--

DROP TABLE IF EXISTS `favoritos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `favoritos` (
  `fav_id` int NOT NULL AUTO_INCREMENT,
  `usu_id` int NOT NULL,
  `veh_id` int NOT NULL,
  `fav_fecha_agregado` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`fav_id`),
  UNIQUE KEY `uk_usuario_vehiculo_fav` (`usu_id`,`veh_id`),
  KEY `idx_fav_usuario` (`usu_id`),
  KEY `idx_fav_vehiculo` (`veh_id`),
  CONSTRAINT `favoritos_ibfk_1` FOREIGN KEY (`usu_id`) REFERENCES `usuarios` (`usu_id`) ON DELETE CASCADE,
  CONSTRAINT `favoritos_ibfk_2` FOREIGN KEY (`veh_id`) REFERENCES `vehiculos` (`veh_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formaspago`
--

DROP TABLE IF EXISTS `formaspago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `formaspago` (
  `fpa_id` int NOT NULL AUTO_INCREMENT,
  `fpa_nombre` varchar(50) NOT NULL,
  `fpa_activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`fpa_id`),
  UNIQUE KEY `fpa_nombre` (`fpa_nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `imagenesvehiculo`
--

DROP TABLE IF EXISTS `imagenesvehiculo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `imagenesvehiculo` (
  `ima_id` int NOT NULL AUTO_INCREMENT,
  `veh_id` int NOT NULL,
  `ima_url` varchar(255) NOT NULL,
  `ima_es_principal` tinyint(1) DEFAULT '0',
  `ima_creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ima_id`),
  KEY `idx_ima_vehiculo` (`veh_id`),
  CONSTRAINT `imagenesvehiculo_ibfk_1` FOREIGN KEY (`veh_id`) REFERENCES `vehiculos` (`veh_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `marcas`
--

DROP TABLE IF EXISTS `marcas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `marcas` (
  `mar_id` int NOT NULL AUTO_INCREMENT,
  `mar_nombre` varchar(100) NOT NULL,
  `mar_logo_url` varchar(255) DEFAULT NULL,
  `mar_actualizado_en` datetime DEFAULT NULL,
  PRIMARY KEY (`mar_id`),
  UNIQUE KEY `mar_nombre` (`mar_nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `modelos`
--

DROP TABLE IF EXISTS `modelos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modelos` (
  `mod_id` int NOT NULL AUTO_INCREMENT,
  `mar_id` int NOT NULL,
  `mod_nombre` varchar(100) NOT NULL,
  `mod_actualizado_en` datetime DEFAULT NULL,
  PRIMARY KEY (`mod_id`),
  UNIQUE KEY `uk_marca_modelo` (`mar_id`,`mod_nombre`),
  KEY `idx_mod_marca` (`mar_id`),
  CONSTRAINT `modelos_ibfk_1` FOREIGN KEY (`mar_id`) REFERENCES `marcas` (`mar_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ofertaspromociones`
--

DROP TABLE IF EXISTS `ofertaspromociones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
  `ofe_creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ofe_actualizado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ofe_id`),
  UNIQUE KEY `ofe_codigo_cupon` (`ofe_codigo_cupon`),
  KEY `idx_ofe_estado_fechas` (`ofe_estado`,`ofe_fecha_inicio`,`ofe_fecha_fin`),
  KEY `idx_ofe_codigo_cupon` (`ofe_codigo_cupon`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pagos`
--

DROP TABLE IF EXISTS `pagos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pagos` (
  `pag_id` int NOT NULL AUTO_INCREMENT,
  `ven_id` int NOT NULL,
  `fpa_id` int NOT NULL,
  `pag_monto` decimal(12,2) NOT NULL,
  `pag_fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `pag_referencia` varchar(100) DEFAULT NULL,
  `pag_estado` enum('pendiente','completado','fallido','reembolsado') DEFAULT 'pendiente',
  `pag_notas` text,
  PRIMARY KEY (`pag_id`),
  KEY `idx_pag_venta` (`ven_id`),
  KEY `idx_pag_formapago` (`fpa_id`),
  KEY `idx_pag_estado` (`pag_estado`),
  CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`ven_id`) REFERENCES `ventas` (`ven_id`) ON DELETE CASCADE,
  CONSTRAINT `pagos_ibfk_2` FOREIGN KEY (`fpa_id`) REFERENCES `formaspago` (`fpa_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `rol_id` int NOT NULL AUTO_INCREMENT,
  `rol_nombre` varchar(50) NOT NULL,
  `rol_descripcion` text,
  `rol_activo` tinyint(1) DEFAULT '1',
  `rol_creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `rol_actualizado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`rol_id`),
  UNIQUE KEY `rol_nombre` (`rol_nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tiposvehiculo`
--

DROP TABLE IF EXISTS `tiposvehiculo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tiposvehiculo` (
  `tiv_id` int NOT NULL AUTO_INCREMENT,
  `tiv_nombre` varchar(100) NOT NULL,
  `tiv_descripcion` text,
  `tiv_icono_url` varchar(255) DEFAULT NULL,
  `tiv_activo` tinyint(1) DEFAULT '1',
  `tiv_creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `tiv_actualizado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`tiv_id`),
  UNIQUE KEY `tiv_nombre` (`tiv_nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
  `usu_creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `usu_actualizado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `usu_cedula` varchar(13) NOT NULL,
  PRIMARY KEY (`usu_id`),
  UNIQUE KEY `usu_usuario` (`usu_usuario`),
  UNIQUE KEY `usu_email` (`usu_email`),
  UNIQUE KEY `UQ_usu_cedula` (`usu_cedula`),
  KEY `idx_usu_rol` (`rol_id`),
  CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`rol_id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vehiculooferta`
--

DROP TABLE IF EXISTS `vehiculooferta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehiculooferta` (
  `vof_id` int NOT NULL AUTO_INCREMENT,
  `veh_id` int NOT NULL,
  `ofe_id` int NOT NULL,
  PRIMARY KEY (`vof_id`),
  UNIQUE KEY `uk_vehiculo_oferta` (`veh_id`,`ofe_id`),
  KEY `ofe_id` (`ofe_id`),
  CONSTRAINT `vehiculooferta_ibfk_1` FOREIGN KEY (`veh_id`) REFERENCES `vehiculos` (`veh_id`) ON DELETE CASCADE,
  CONSTRAINT `vehiculooferta_ibfk_2` FOREIGN KEY (`ofe_id`) REFERENCES `ofertaspromociones` (`ofe_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vehiculos`
--

DROP TABLE IF EXISTS `vehiculos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
  `veh_creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `veh_actualizado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`veh_id`),
  UNIQUE KEY `veh_vin` (`veh_vin`),
  UNIQUE KEY `veh_placa` (`veh_placa`),
  KEY `idx_veh_marca_modelo` (`mar_id`,`mod_id`),
  KEY `idx_veh_tipo` (`tiv_id`),
  KEY `idx_veh_usuario_gestor` (`usu_id_gestor`),
  KEY `idx_veh_estado` (`veh_estado`),
  KEY `idx_veh_ubicacion` (`veh_ubicacion_provincia`,`veh_ubicacion_ciudad`),
  KEY `mod_id` (`mod_id`),
  CONSTRAINT `vehiculos_ibfk_1` FOREIGN KEY (`mar_id`) REFERENCES `marcas` (`mar_id`) ON DELETE RESTRICT,
  CONSTRAINT `vehiculos_ibfk_2` FOREIGN KEY (`mod_id`) REFERENCES `modelos` (`mod_id`) ON DELETE RESTRICT,
  CONSTRAINT `vehiculos_ibfk_3` FOREIGN KEY (`tiv_id`) REFERENCES `tiposvehiculo` (`tiv_id`) ON DELETE RESTRICT,
  CONSTRAINT `vehiculos_ibfk_4` FOREIGN KEY (`usu_id_gestor`) REFERENCES `usuarios` (`usu_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ventas`
--

DROP TABLE IF EXISTS `ventas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
  `ven_creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ven_actualizado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ven_id`),
  KEY `idx_ven_comprador` (`usu_id_comprador`),
  KEY `idx_ven_gestor` (`usu_id_gestor`),
  KEY `idx_ven_estado` (`ven_estado`),
  KEY `ofe_id` (`ofe_id`),
  CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`usu_id_comprador`) REFERENCES `usuarios` (`usu_id`) ON DELETE RESTRICT,
  CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`usu_id_gestor`) REFERENCES `usuarios` (`usu_id`) ON DELETE SET NULL,
  CONSTRAINT `ventas_ibfk_3` FOREIGN KEY (`ofe_id`) REFERENCES `ofertaspromociones` (`ofe_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `videosvehiculo`
--

DROP TABLE IF EXISTS `videosvehiculo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `videosvehiculo` (
  `viv_id` int NOT NULL AUTO_INCREMENT,
  `veh_id` int NOT NULL,
  `viv_url` varchar(255) NOT NULL,
  `viv_titulo` varchar(150) DEFAULT NULL,
  `viv_plataforma` enum('youtube','vimeo','local','otro') DEFAULT 'otro',
  `viv_id_plataforma` varchar(100) DEFAULT NULL,
  `viv_es_principal` tinyint(1) DEFAULT '0',
  `viv_creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`viv_id`),
  KEY `idx_viv_vehiculo` (`veh_id`),
  CONSTRAINT `videosvehiculo_ibfk_1` FOREIGN KEY (`veh_id`) REFERENCES `vehiculos` (`veh_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;








=======================================================
DESCRIBE MYSQL
=======================================================

mysql> show tables;
+-----------------------------+
| Tables_in_sistemaventaautos |
+-----------------------------+
| cotizaciones                |
| detallesventa               |
| favoritos                   |
| formaspago                  |
| imagenesvehiculo            |
| marcas                      |
| modelos                     |
| ofertaspromociones          |
| pagos                       |
| roles                       |
| tiposvehiculo               |
| usuarios                    |
| vehiculooferta              |
| vehiculos                   |
| ventas                      |
| videosvehiculo              |
+-----------------------------+
16 rows in set (0.00 sec)

mysql> clear
mysql> DESCRIBE cotizaciones;
+----------------------------------+-----------------------------------------------------------------------+------+-----+-------------------+-------------------+
| Field                            | Type                                                                  | Null | Key | Default           | Extra             |
+----------------------------------+-----------------------------------------------------------------------+------+-----+-------------------+-------------------+
| cot_id                           | int                                                                   | NO   | PRI | NULL              | auto_increment    |
| usu_id_solicitante               | int                                                                   | NO   | MUL | NULL              |                   |
| veh_id                           | int                                                                   | YES  | MUL | NULL              |                   |
| cot_detalles_vehiculo_solicitado | text                                                                  | YES  |     | NULL              |                   |
| cot_mensaje                      | text                                                                  | YES  |     | NULL              |                   |
| cot_estado                       | enum('pendiente','aprobada_admin','contactado','cerrado','rechazado') | YES  | MUL | pendiente         |                   |
| cot_fecha_solicitud              | timestamp                                                             | YES  |     | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
| cot_notas_admin                  | text                                                                  | YES  |     | NULL              |                   |
+----------------------------------+-----------------------------------------------------------------------+------+-----+-------------------+-------------------+
8 rows in set (0.00 sec)

mysql> DESCRIBE detallesventa;
+---------------------+---------------+------+-----+---------+----------------+
| Field               | Type          | Null | Key | Default | Extra          |
+---------------------+---------------+------+-----+---------+----------------+
| dve_id              | int           | NO   | PRI | NULL    | auto_increment |
| ven_id              | int           | NO   | MUL | NULL    |                |
| veh_id              | int           | NO   | UNI | NULL    |                |
| dve_precio_unitario | decimal(12,2) | NO   |     | NULL    |                |
| dve_cantidad        | int           | YES  |     | 1       |                |
+---------------------+---------------+------+-----+---------+----------------+
5 rows in set (0.00 sec)

mysql> DESCRIBE favoritos;
+--------------------+-----------+------+-----+-------------------+-------------------+
| Field              | Type      | Null | Key | Default           | Extra             |
+--------------------+-----------+------+-----+-------------------+-------------------+
| fav_id             | int       | NO   | PRI | NULL              | auto_increment    |
| usu_id             | int       | NO   | MUL | NULL              |                   |
| veh_id             | int       | NO   | MUL | NULL              |                   |
| fav_fecha_agregado | timestamp | YES  |     | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
+--------------------+-----------+------+-----+-------------------+-------------------+
4 rows in set (0.00 sec)

mysql> DESCRIBE formaspago;
+------------+-------------+------+-----+---------+----------------+
| Field      | Type        | Null | Key | Default | Extra          |
+------------+-------------+------+-----+---------+----------------+
| fpa_id     | int         | NO   | PRI | NULL    | auto_increment |
| fpa_nombre | varchar(50) | NO   | UNI | NULL    |                |
| fpa_activo | tinyint(1)  | YES  |     | 1       |                |
+------------+-------------+------+-----+---------+----------------+
3 rows in set (0.00 sec)

mysql> DESCRIBE imagenesvehiculo;
+------------------+--------------+------+-----+-------------------+-------------------+
| Field            | Type         | Null | Key | Default           | Extra             |
+------------------+--------------+------+-----+-------------------+-------------------+
| ima_id           | int          | NO   | PRI | NULL              | auto_increment    |
| veh_id           | int          | NO   | MUL | NULL              |                   |
| ima_url          | varchar(255) | NO   |     | NULL              |                   |
| ima_es_principal | tinyint(1)   | YES  |     | 0                 |                   |
| ima_creado_en    | timestamp    | YES  |     | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
+------------------+--------------+------+-----+-------------------+-------------------+
5 rows in set (0.00 sec)

mysql> DESCRIBE marcas;
+--------------------+--------------+------+-----+---------+----------------+
| Field              | Type         | Null | Key | Default | Extra          |
+--------------------+--------------+------+-----+---------+----------------+
| mar_id             | int          | NO   | PRI | NULL    | auto_increment |
| mar_nombre         | varchar(100) | NO   | UNI | NULL    |                |
| mar_logo_url       | varchar(255) | YES  |     | NULL    |                |
| mar_actualizado_en | datetime     | YES  |     | NULL    |                |
+--------------------+--------------+------+-----+---------+----------------+
4 rows in set (0.00 sec)

mysql> DESCRIBE modelos;
+--------------------+--------------+------+-----+---------+----------------+
| Field              | Type         | Null | Key | Default | Extra          |
+--------------------+--------------+------+-----+---------+----------------+
| mod_id             | int          | NO   | PRI | NULL    | auto_increment |
| mar_id             | int          | NO   | MUL | NULL    |                |
| mod_nombre         | varchar(100) | NO   |     | NULL    |                |
| mod_actualizado_en | datetime     | YES  |     | NULL    |                |
+--------------------+--------------+------+-----+---------+----------------+
4 rows in set (0.00 sec)

mysql> DESCRIBE ofertaspromociones;
+---------------------+---------------------------------------------------------------------+------+-----+-------------------+-----------------------------------------------+
| Field               | Type                                                                | Null | Key | Default           | Extra                                         |
+---------------------+---------------------------------------------------------------------+------+-----+-------------------+-----------------------------------------------+
| ofe_id              | int                                                                 | NO   | PRI | NULL              | auto_increment                                |
| ofe_nombre          | varchar(150)                                                        | NO   |     | NULL              |                                               |
| ofe_descripcion     | text                                                                | YES  |     | NULL              |                                               |
| ofe_tipo            | enum('descuento_porcentaje','descuento_fijo','envio_gratis','otro') | NO   |     | NULL              |                                               |
| ofe_valor           | decimal(10,2)                                                       | YES  |     | NULL              |                                               |
| ofe_codigo_cupon    | varchar(50)                                                         | YES  | UNI | NULL              |                                               |
| ofe_fecha_inicio    | datetime                                                            | NO   |     | NULL              |                                               |
| ofe_fecha_fin       | datetime                                                            | NO   |     | NULL              |                                               |
| ofe_estado          | enum('activa','inactiva','caducada')                                | YES  | MUL | activa            |                                               |
| ofe_uso_maximo      | int                                                                 | YES  |     | NULL              |                                               |
| ofe_uso_por_cliente | int                                                                 | YES  |     | 1                 |                                               |
| ofe_creado_en       | timestamp                                                           | YES  |     | CURRENT_TIMESTAMP | DEFAULT_GENERATED                             |
| ofe_actualizado_en  | timestamp                                                           | YES  |     | CURRENT_TIMESTAMP | DEFAULT_GENERATED on update CURRENT_TIMESTAMP |
+---------------------+---------------------------------------------------------------------+------+-----+-------------------+-----------------------------------------------+
13 rows in set (0.00 sec)

mysql> DESCRIBE pagos;
+----------------+--------------------------------------------------------+------+-----+-------------------+-------------------+
| Field          | Type                                                   | Null | Key | Default           | Extra             |
+----------------+--------------------------------------------------------+------+-----+-------------------+-------------------+
| pag_id         | int                                                    | NO   | PRI | NULL              | auto_increment    |
| ven_id         | int                                                    | NO   | MUL | NULL              |                   |
| fpa_id         | int                                                    | NO   | MUL | NULL              |                   |
| pag_monto      | decimal(12,2)                                          | NO   |     | NULL              |                   |
| pag_fecha      | timestamp                                              | YES  |     | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
| pag_referencia | varchar(100)                                           | YES  |     | NULL              |                   |
| pag_estado     | enum('pendiente','completado','fallido','reembolsado') | YES  | MUL | pendiente         |                   |
| pag_notas      | text                                                   | YES  |     | NULL              |                   |
+----------------+--------------------------------------------------------+------+-----+-------------------+-------------------+
8 rows in set (0.00 sec)

mysql> DESCRIBE roles;
+--------------------+-------------+------+-----+-------------------+-----------------------------------------------+
| Field              | Type        | Null | Key | Default           | Extra                                         |
+--------------------+-------------+------+-----+-------------------+-----------------------------------------------+
| rol_id             | int         | NO   | PRI | NULL              | auto_increment                                |
| rol_nombre         | varchar(50) | NO   | UNI | NULL              |                                               |
| rol_descripcion    | text        | YES  |     | NULL              |                                               |
| rol_activo         | tinyint(1)  | YES  |     | 1                 |                                               |
| rol_creado_en      | timestamp   | YES  |     | CURRENT_TIMESTAMP | DEFAULT_GENERATED                             |
| rol_actualizado_en | timestamp   | YES  |     | CURRENT_TIMESTAMP | DEFAULT_GENERATED on update CURRENT_TIMESTAMP |
+--------------------+-------------+------+-----+-------------------+-----------------------------------------------+
6 rows in set (0.00 sec)

mysql> DESCRIBE tiposvehiculo;
+--------------------+--------------+------+-----+-------------------+-----------------------------------------------+
| Field              | Type         | Null | Key | Default           | Extra                                         |
+--------------------+--------------+------+-----+-------------------+-----------------------------------------------+
| tiv_id             | int          | NO   | PRI | NULL              | auto_increment                                |
| tiv_nombre         | varchar(100) | NO   | UNI | NULL              |                                               |
| tiv_descripcion    | text         | YES  |     | NULL              |                                               |
| tiv_icono_url      | varchar(255) | YES  |     | NULL              |                                               |
| tiv_activo         | tinyint(1)   | YES  |     | 1                 |                                               |
| tiv_creado_en      | timestamp    | YES  |     | CURRENT_TIMESTAMP | DEFAULT_GENERATED                             |
| tiv_actualizado_en | timestamp    | YES  |     | CURRENT_TIMESTAMP | DEFAULT_GENERATED on update CURRENT_TIMESTAMP |
+--------------------+--------------+------+-----+-------------------+-----------------------------------------------+
7 rows in set (0.00 sec)

mysql> DESCRIBE usuarios;
+--------------------+--------------+------+-----+-------------------+-----------------------------------------------+
| Field              | Type         | Null | Key | Default           | Extra                                         |
+--------------------+--------------+------+-----+-------------------+-----------------------------------------------+
| usu_id             | int          | NO   | PRI | NULL              | auto_increment                                |
| rol_id             | int          | NO   | MUL | NULL              |                                               |
| usu_usuario        | varchar(50)  | NO   | UNI | NULL              |                                               |
| usu_nombre         | varchar(100) | NO   |     | NULL              |                                               |
| usu_apellido       | varchar(100) | NO   |     | NULL              |                                               |
| usu_email          | varchar(100) | NO   | UNI | NULL              |                                               |
| usu_password       | varchar(255) | NO   |     | NULL              |                                               |
| usu_telefono       | varchar(20)  | YES  |     | NULL              |                                               |
| usu_direccion      | varchar(255) | YES  |     | NULL              |                                               |
| usu_fnacimiento    | date         | YES  |     | NULL              |                                               |
| usu_verificado     | tinyint(1)   | YES  |     | 0                 |                                               |
| usu_creado_en      | timestamp    | YES  |     | CURRENT_TIMESTAMP | DEFAULT_GENERATED                             |
| usu_actualizado_en | timestamp    | YES  |     | CURRENT_TIMESTAMP | DEFAULT_GENERATED on update CURRENT_TIMESTAMP |
| usu_cedula         | varchar(13)  | NO   | UNI | NULL              |                                               |
+--------------------+--------------+------+-----+-------------------+-----------------------------------------------+
14 rows in set (0.00 sec)

mysql> DESCRIBE vehiculooferta;
+--------+------+------+-----+---------+----------------+
| Field  | Type | Null | Key | Default | Extra          |
+--------+------+------+-----+---------+----------------+
| vof_id | int  | NO   | PRI | NULL    | auto_increment |
| veh_id | int  | NO   | MUL | NULL    |                |
| ofe_id | int  | NO   | MUL | NULL    |                |
+--------+------+------+-----+---------+----------------+
3 rows in set (0.00 sec)

mysql> DESCRIBE vehiculos;
+----------------------------+--------------------------------------------------------------------------------------------------------------------+------+-----+-------------------+-----------------------------------------------+
| Field                      | Type                                                                                                               | Null | Key | Default           | Extra                                         |
+----------------------------+--------------------------------------------------------------------------------------------------------------------+------+-----+-------------------+-----------------------------------------------+
| veh_id                     | int                                                                                                                | NO   | PRI | NULL              | auto_increment                                |
| mar_id                     | int                                                                                                                | NO   | MUL | NULL              |                                               |
| mod_id                     | int                                                                                                                | NO   | MUL | NULL              |                                               |
| tiv_id                     | int                                                                                                                | NO   | MUL | NULL              |                                               |
| veh_subtipo_vehiculo       | varchar(100)                                                                                                       | YES  |     | NULL              |                                               |
| usu_id_gestor              | int                                                                                                                | YES  | MUL | NULL              |                                               |
| veh_condicion              | enum('nuevo','usado')                                                                                              | NO   |     | NULL              |                                               |
| veh_anio                   | int                                                                                                                | NO   |     | NULL              |                                               |
| veh_kilometraje            | int                                                                                                                | NO   |     | NULL              |                                               |
| veh_precio                 | decimal(12,2)                                                                                                      | NO   |     | NULL              |                                               |
| veh_vin                    | varchar(20)                                                                                                        | YES  | UNI | NULL              |                                               |
| veh_placa                  | varchar(10)                                                                                                        | YES  | UNI | NULL              |                                               |
| veh_ubicacion_ciudad       | varchar(100)                                                                                                       | NO   |     | NULL              |                                               |
| veh_ubicacion_provincia    | varchar(100)                                                                                                       | NO   | MUL | NULL              |                                               |
| veh_color_exterior         | varchar(50)                                                                                                        | YES  |     | NULL              |                                               |
| veh_color_interior         | varchar(50)                                                                                                        | YES  |     | NULL              |                                               |
| veh_detalles_motor         | text                                                                                                               | YES  |     | NULL              |                                               |
| veh_tipo_transmision       | varchar(50)                                                                                                        | YES  |     | NULL              |                                               |
| veh_sistema_climatizacion  | enum('Ninguno','Aire Acondicionado','Climatizador Manual','Climatizador Automatico','Climatizador Bi-Zona','Otro') | YES  |     | NULL              |                                               |
| veh_ultimo_digito_placa    | char(1)                                                                                                            | YES  |     | NULL              |                                               |
| veh_placa_provincia_origen | varchar(100)                                                                                                       | YES  |     | NULL              |                                               |
| veh_traccion               | enum('Delantera','Trasera','4x4','AWD','Otro')                                                                     | YES  |     | NULL              |                                               |
| veh_tipo_vidrios           | enum('Manuales','Electricos Delanteros','Electricos Completos','Otro')                                             | YES  |     | NULL              |                                               |
| veh_tipo_combustible       | enum('Gasolina','Diesel','Hibrido','Electrico','Flex (Gasolina/Etanol)','GLP','GNV','Otro')                        | YES  |     | NULL              |                                               |
| veh_tipo_direccion         | enum('Mecanica','Hidraulica','Electroasistida','Electrica','Otra')                                                 | YES  |     | NULL              |                                               |
| veh_descripcion            | text                                                                                                               | YES  |     | NULL              |                                               |
| veh_detalles_extra         | text                                                                                                               | YES  |     | NULL              |                                               |
| veh_estado                 | enum('disponible','reservado','vendido','desactivado')                                                             | YES  | MUL | disponible        |                                               |
| veh_fecha_publicacion      | date                                                                                                               | NO   |     | NULL              |                                               |
| veh_creado_en              | timestamp                                                                                                          | YES  |     | CURRENT_TIMESTAMP | DEFAULT_GENERATED                             |
| veh_actualizado_en         | timestamp                                                                                                          | YES  |     | CURRENT_TIMESTAMP | DEFAULT_GENERATED on update CURRENT_TIMESTAMP |
+----------------------------+--------------------------------------------------------------------------------------------------------------------+------+-----+-------------------+-----------------------------------------------+
31 rows in set (0.00 sec)

mysql> DESCRIBE ventas;
+--------------------+-------------------------------------------------------------------------------------------------+------+-----+-------------------+-----------------------------------------------+
| Field              | Type                                                                                            | Null | Key | Default           | Extra                                         |
+--------------------+-------------------------------------------------------------------------------------------------+------+-----+-------------------+-----------------------------------------------+
| ven_id             | int                                                                                             | NO   | PRI | NULL              | auto_increment                                |
| usu_id_comprador   | int                                                                                             | NO   | MUL | NULL              |                                               |
| usu_id_gestor      | int                                                                                             | YES  | MUL | NULL              |                                               |
| ofe_id             | int                                                                                             | YES  | MUL | NULL              |                                               |
| ven_fecha_venta    | date                                                                                            | NO   |     | NULL              |                                               |
| ven_subtotal       | decimal(12,2)                                                                                   | NO   |     | NULL              |                                               |
| ven_descuento      | decimal(12,2)                                                                                   | YES  |     | 0.00              |                                               |
| ven_impuestos      | decimal(12,2)                                                                                   | YES  |     | 0.00              |                                               |
| ven_precio_total   | decimal(12,2)                                                                                   | NO   |     | NULL              |                                               |
| ven_estado         | enum('pendiente_pago','pagado_parcial','pagado_completo','en_entrega','completado','cancelado') | YES  | MUL | pendiente_pago    |                                               |
| ven_notas_internas | text                                                                                            | YES  |     | NULL              |                                               |
| ven_creado_en      | timestamp                                                                                       | YES  |     | CURRENT_TIMESTAMP | DEFAULT_GENERATED                             |
| ven_actualizado_en | timestamp                                                                                       | YES  |     | CURRENT_TIMESTAMP | DEFAULT_GENERATED on update CURRENT_TIMESTAMP |
+--------------------+-------------------------------------------------------------------------------------------------+------+-----+-------------------+-----------------------------------------------+
13 rows in set (0.00 sec)

mysql> DESCRIBE videosvehiculo;
+-------------------+----------------------------------------+------+-----+-------------------+-------------------+
| Field             | Type                                   | Null | Key | Default           | Extra             |
+-------------------+----------------------------------------+------+-----+-------------------+-------------------+
| viv_id            | int                                    | NO   | PRI | NULL              | auto_increment    |
| veh_id            | int                                    | NO   | MUL | NULL              |                   |
| viv_url           | varchar(255)                           | NO   |     | NULL              |                   |
| viv_titulo        | varchar(150)                           | YES  |     | NULL              |                   |
| viv_plataforma    | enum('youtube','vimeo','local','otro') | YES  |     | otro              |                   |
| viv_id_plataforma | varchar(100)                           | YES  |     | NULL              |                   |
| viv_es_principal  | tinyint(1)                             | YES  |     | 0                 |                   |
| viv_creado_en     | timestamp                              | YES  |     | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
+-------------------+----------------------------------------+------+-----+-------------------+-------------------+