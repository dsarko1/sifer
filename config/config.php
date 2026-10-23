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
defined('BASE_URL') || define('BASE_URL', '/sifer/public');
defined('ZONA_HORARIA') || define('ZONA_HORARIA', 'America/Argentina/Buenos_Aires');
defined('SIFER_INTENTOS_MAX') || define('SIFER_INTENTOS_MAX', 5);
defined('SIFER_BLOQUEO_MINUTOS') || define('SIFER_BLOQUEO_MINUTOS', 15);
defined('SIFER_SESION_INACTIVA') || define('SIFER_SESION_INACTIVA', 1800);
defined('SIFER_SESION_REGENERAR') || define('SIFER_SESION_REGENERAR', 900);

// Conexión a MariaDB (valores por defecto de XAMPP).
defined('DB_HOST') || define('DB_HOST', '127.0.0.1');
defined('DB_PORT') || define('DB_PORT', '3306');
defined('DB_NAME') || define('DB_NAME', 'sifer');
defined('DB_USER') || define('DB_USER', 'root');
defined('DB_PASS') || define('DB_PASS', '');
defined('DB_CHARSET') || define('DB_CHARSET', 'utf8mb4');

date_default_timezone_set(ZONA_HORARIA);
