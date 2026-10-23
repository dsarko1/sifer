<?php

declare(strict_types=1);

$basePruebas = getenv('SIFER_TEST_DB');
defined('DB_NAME') || define('DB_NAME', $basePruebas === false || $basePruebas === '' ? 'sifer_test' : $basePruebas);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/models/Usuario.php';
require_once __DIR__ . '/../includes/models/Categoria.php';
require_once __DIR__ . '/../includes/models/Producto.php';
require_once __DIR__ . '/../includes/models/Movimiento.php';

/**
 * Convierte un archivo SQL simple en sentencias ejecutables.
 *
 * @return list<string>
 */
function sifer_sentencias(string $sql): array
{
    $lineas = [];

    foreach (preg_split('/\R/', $sql) ?: [] as $linea) {
        if (preg_match('/^\s*--/', $linea) === 1) {
            continue;
        }

        $lineas[] = $linea;
    }

    $sentencias = [];

    foreach (explode(';', implode("\n", $lineas)) as $sentencia) {
        $sentencia = trim($sentencia);

        if ($sentencia === '' || preg_match('/^(CREATE DATABASE|USE)\b/i', $sentencia) === 1) {
            continue;
        }

        $sentencias[] = $sentencia;
    }

    return $sentencias;
}

/**
 * Ejecuta un archivo SQL contra la base de pruebas.
 */
function sifer_ejecutar_archivo(string $ruta): void
{
    $sql = file_get_contents($ruta);

    if ($sql === false) {
        throw new RuntimeException('No se pudo leer ' . $ruta);
    }

    foreach (sifer_sentencias($sql) as $sentencia) {
        db()->exec($sentencia);
    }
}

/**
 * Deja la base en el estado de tests/fixtures.sql.
 */
function reiniciar_base(): void
{
    db()->exec('SET FOREIGN_KEY_CHECKS = 0');

    foreach (['movimientos', 'productos', 'categorias', 'usuarios'] as $tabla) {
        db()->exec('TRUNCATE TABLE ' . $tabla);
    }

    db()->exec('SET FOREIGN_KEY_CHECKS = 1');

    sifer_ejecutar_archivo(__DIR__ . '/fixtures.sql');
}
