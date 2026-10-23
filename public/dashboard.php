<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/models/Producto.php';
require_once __DIR__ . '/../includes/models/Categoria.php';
require_once __DIR__ . '/../includes/models/Movimiento.php';

require_admin();

$alertas = producto_alertas(5);

$totales = [
    'productos'  => producto_contar(),
    'categorias' => categoria_contar(),
    'bajo_stock' => producto_contar_alertas(),
];

$movimientos = movimiento_listar(8);

$titulo = 'Panel';
$datos  = [
    'alertas'     => $alertas,
    'totales'     => $totales,
    'movimientos' => $movimientos,
];

require __DIR__ . '/../views/dashboard.php';
