<?php
// global.php

// Detectamos el host actual
$hostName = $_SERVER['HTTP_HOST'];

// Entorno local (XAMPP, Laragon, etc.) o mapeo a localhost
if (strpos($hostName, 'localhost') !== false) {
    // Configuración para desarrollo local
    define("DB_HOST",     "localhost");
    define("DB_NAME",     "SISTEMAVENTAAUTOS");
    define("DB_USERNAME", "root");
    define("DB_PASSWORD", "admin");
    define("DB_ENCODE",   "utf8");
    define("PRO_NOMBRE",  "SISTEMAAUTOS");
}
// Entorno de servidor interno (IP 10.10.35.45)
elseif ($hostName === '10.10.35.45') {
    // Configuración para tu servidor
    define("DB_HOST",     "localhost");            // La BD sigue corriendo en el mismo servidor
    define("DB_NAME",     "Chasiguano_4");
    define("DB_USERNAME", "chasiguano");
    define("DB_PASSWORD", "Chasiguano.2025");
    define("DB_ENCODE",   "utf8");
    define("PRO_NOMBRE",  "SISTEMAAUTOS");
}
// Si necesitas más entornos (QA, producción, etc.), puedes seguir agregando más elseif()
// Por defecto, podrías lanzar un error si no coincide
else {
    die("Entorno desconocido: asegúrate de revisar \$_SERVER['HTTP_HOST'] (actualmente: $hostName)");
}
