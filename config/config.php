<?php

declare(strict_types=1);

/**
 * Configuración general de Sifer.
 *
 * Los valores de conexión se pueden sobreescribir sin tocar este archivo
 * creando config/config.local.php (ver config/config.example.php).
 */

$siferConfigLocal = __DIR__ . '/config.local.php';
if (is_file($siferConfigLocal)) {
    require $siferConfigLocal;
}

// Datos del negocio.
defined('SIFER_NOMBRE') || define('SIFER_NOMBRE', 'Sifer');
// URL base pública, sin barra final (ej.: /sifer/public).
$siferBaseUrl = getenv('SIFER_BASE_URL');
defined('BASE_URL') || define('BASE_URL', $siferBaseUrl === false ? '/sifer/public' : $siferBaseUrl);
defined('ZONA_HORARIA') || define('ZONA_HORARIA', 'America/Argentina/Buenos_Aires');
defined('SIFER_INTENTOS_MAX') || define('SIFER_INTENTOS_MAX', 5);
defined('SIFER_BLOQUEO_MINUTOS') || define('SIFER_BLOQUEO_MINUTOS', 15);
defined('SIFER_SESION_INACTIVA') || define('SIFER_SESION_INACTIVA', 1800);
defined('SIFER_SESION_REGENERAR') || define('SIFER_SESION_REGENERAR', 900);

// Conexión a MariaDB (valores por defecto de XAMPP).
$siferDbHost = getenv('SIFER_DB_HOST');
defined('DB_HOST') || define('DB_HOST', $siferDbHost === false ? '127.0.0.1' : $siferDbHost);
$siferDbPort = getenv('SIFER_DB_PORT');
defined('DB_PORT') || define('DB_PORT', $siferDbPort === false ? '3306' : $siferDbPort);
$siferDbName = getenv('SIFER_DB_NAME');
defined('DB_NAME') || define('DB_NAME', $siferDbName === false ? 'sifer' : $siferDbName);
$siferDbUser = getenv('SIFER_DB_USER');
defined('DB_USER') || define('DB_USER', $siferDbUser === false ? 'root' : $siferDbUser);
$siferDbPass = getenv('SIFER_DB_PASS');
defined('DB_PASS') || define('DB_PASS', $siferDbPass === false ? '' : $siferDbPass);
defined('DB_CHARSET') || define('DB_CHARSET', 'utf8mb4');

date_default_timezone_set(ZONA_HORARIA);
