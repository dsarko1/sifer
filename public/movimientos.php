<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/models/Producto.php';
require_once __DIR__ . '/../includes/models/Movimiento.php';

require_admin();

$tipos = movimiento_tipos();
$tipoEntrada = $_GET['tipo'] ?? '';
$tipo = is_string($tipoEntrada) && array_key_exists($tipoEntrada, $tipos) ? $tipoEntrada : '';

$productoEntrada = $_GET['producto'] ?? null;
$productoId = is_string($productoEntrada) || is_int($productoEntrada)
    ? id_valido($productoEntrada)
    : null;

$desdeEntrada = $_GET['desde'] ?? '';
$desde = is_string($desdeEntrada) && movimiento_fecha_valida($desdeEntrada) ? $desdeEntrada : '';
$hastaEntrada = $_GET['hasta'] ?? '';
$hasta = is_string($hastaEntrada) && movimiento_fecha_valida($hastaEntrada) ? $hastaEntrada : '';
$paginaEntrada = $_GET['pagina'] ?? null;
$pagina = is_string($paginaEntrada) || is_int($paginaEntrada)
    ? (id_valido($paginaEntrada) ?? 1)
    : 1;

$filtros = [
    'tipo' => $tipo,
    'producto' => $productoId,
    'desde' => $desde,
    'hasta' => $hasta,
];
$resultado = movimiento_buscar($filtros, $pagina);
$productos = producto_listar('', null, true);

$titulo = 'Movimientos';
$datos = [
    'movimientos' => $resultado['filas'],
    'filtros' => $filtros,
    'paginacion' => [
        'pagina' => $resultado['pagina'],
        'paginas' => $resultado['paginas'],
        'total' => $resultado['total'],
        'por_pagina' => $resultado['por_pagina'],
    ],
    'productos' => $productos,
    'tipos' => $tipos,
];

require __DIR__ . '/../views/movimientos.php';
