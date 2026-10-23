<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/models/Producto.php';

require_admin();

$titulo = 'Reportes';
$datos = [
    'resumen' => producto_resumen_stock(),
    'por_categoria' => producto_valor_por_categoria(),
    'bajo_minimo' => producto_bajo_minimo(),
];

require __DIR__ . '/../views/reportes.php';
