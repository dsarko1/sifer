<?php

declare(strict_types=1);

/**
 * Ejemplo de configuración local.
 *
 * Copiar este archivo como config/config.local.php y ajustar los valores
 * si la instalación de MariaDB no usa los datos por defecto de XAMPP.
 * config.local.php no se versiona en el repositorio.
 */

define('SIFER_NOMBRE', 'Sifer');
define('BASE_URL', '/sifer/public');
define('ZONA_HORARIA', 'America/Argentina/Buenos_Aires');

define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'sifer');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
