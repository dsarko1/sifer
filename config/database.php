<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

/**
 * Devuelve la única conexión PDO de la aplicación.
 *
 * Se crea la primera vez que se llama y se reutiliza durante el resto
 * de la petición (patrón singleton).
 */
function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        DB_HOST,
        DB_PORT,
        DB_NAME,
        DB_CHARSET
    );

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $excepcion) {
        // El detalle queda en el log; al usuario no se le muestran credenciales.
        error_log('Sifer: no se pudo conectar a la base de datos: ' . $excepcion->getMessage());
        throw new RuntimeException('No se pudo conectar con la base de datos.');
    }

    return $pdo;
}
