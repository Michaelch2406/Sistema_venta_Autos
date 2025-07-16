<?php
// Configuración para el entorno de desarrollo local
if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
    define("DB_HOST", "localhost");
    define("DB_NAME", "SISTEMAVENTAAUTOS");
    define("DB_USERNAME", "root");
    define("DB_PASSWORD", "admin");
} else {
    // Configuración para el servidor de producción
    define("DB_HOST", "10.10.35.45");
    define("DB_NAME", "Chasiguano_4");
    define("DB_USERNAME", "chasiguano");
    define("DB_PASSWORD", "Chasiguano.2025");
}

define("DB_ENCODE", "utf8");
define("PRO_NOMBRE", "SISTEMAAUTOS");
?>