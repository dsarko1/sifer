<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/models/Producto.php';
require_once __DIR__ . '/../includes/models/Movimiento.php';

require_admin();

$productoId  = id_valido($_GET['producto'] ?? null);
$productos   = producto_listar('', null, true);
$movimientos = movimiento_listar(100, $productoId);

$titulo = 'Movimientos';
$datos  = [
    'movimientos' => $movimientos,
    'productos'   => $productos,
];

require __DIR__ . '/../views/movimientos.php';
