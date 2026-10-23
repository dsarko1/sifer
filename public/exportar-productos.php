<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/export.php';
require_once __DIR__ . '/../includes/models/Producto.php';

require_admin();

$buscarEntrada = $_GET['buscar'] ?? '';
$buscar = is_string($buscarEntrada) ? trim($buscarEntrada) : '';
$categoriaEntrada = $_GET['categoria'] ?? null;
$categoriaId = is_string($categoriaEntrada) || is_int($categoriaEntrada)
    ? id_valido($categoriaEntrada)
    : null;

// Incluye productos activos e inactivos para informar su estado en el archivo.
$productos = producto_listar($buscar, $categoriaId, false);
$filas = [];

foreach ($productos as $producto) {
    $filas[] = [
        $producto['codigo'],
        $producto['nombre'],
        $producto['categoria'] ?? 'Sin categoría',
        number_format((float) $producto['precio'], 2, '.', ''),
        (int) $producto['stock_actual'],
        (int) $producto['stock_minimo'],
        (int) $producto['activo'] === 1 ? 'Activo' : 'Inactivo',
        number_format((float) $producto['precio'] * (int) $producto['stock_actual'], 2, '.', ''),
    ];
}

csv_enviar(
    'productos-' . date('Ymd') . '.csv',
    ['Codigo', 'Nombre', 'Categoria', 'Precio', 'Stock', 'Minimo', 'Estado', 'Valor'],
    $filas
);
