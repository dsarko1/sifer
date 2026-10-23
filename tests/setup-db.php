<?php

declare(strict_types=1);

$host = getenv('SIFER_TEST_HOST') ?: '127.0.0.1';
$port = getenv('SIFER_TEST_PORT') ?: '3306';
$usuario = getenv('SIFER_TEST_USER') ?: 'root';
$clave = getenv('SIFER_TEST_PASS') ?: '';
$base = getenv('SIFER_TEST_DB') ?: 'sifer_test';

$pdo = new PDO(
    "mysql:host={$host};port={$port};charset=utf8mb4",
    $usuario,
    $clave,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
);

$pdo->exec("CREATE DATABASE IF NOT EXISTS `{$base}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

putenv('SIFER_TEST_DB=' . $base);

require __DIR__ . '/bootstrap.php';

sifer_ejecutar_archivo(__DIR__ . '/../sql/schema.sql');
sifer_ejecutar_archivo(__DIR__ . '/fixtures.sql');

echo "Base de pruebas '{$base}' lista.\n";
