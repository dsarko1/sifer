<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/export.php';
require_once __DIR__ . '/../includes/models/Movimiento.php';

require_admin();

$tipos = movimiento_tipos();
$tipoEntrada = $_GET['tipo'] ?? '';
$tipo = is_string($tipoEntrada) && array_key_exists($tipoEntrada, $tipos) ? $tipoEntrada : '';

$productoEntrada = $_GET['producto'] ?? null;
$producto = is_string($productoEntrada) || is_int($productoEntrada)
    ? id_valido($productoEntrada)
    : null;

$desdeEntrada = $_GET['desde'] ?? '';
$desde = is_string($desdeEntrada) && movimiento_fecha_valida($desdeEntrada) ? $desdeEntrada : '';
$hastaEntrada = $_GET['hasta'] ?? '';
$hasta = is_string($hastaEntrada) && movimiento_fecha_valida($hastaEntrada) ? $hastaEntrada : '';

$movimientos = movimiento_exportar([
    'tipo' => $tipo,
    'producto' => $producto,
    'desde' => $desde,
    'hasta' => $hasta,
]);
$filas = [];

foreach ($movimientos as $movimiento) {
    $filas[] = [
        $movimiento['creado_en'],
        $movimiento['tipo_etiqueta'],
        $movimiento['producto'],
        $movimiento['producto_codigo'],
        (int) $movimiento['cantidad'],
        (int) $movimiento['stock_anterior'],
        (int) $movimiento['stock_nuevo'],
        $movimiento['usuario_nombre'],
        $movimiento['motivo'],
    ];
}

csv_enviar(
    'movimientos-' . date('Ymd') . '.csv',
    ['Fecha', 'Tipo', 'Producto', 'Codigo', 'Cantidad', 'Stock anterior', 'Stock nuevo', 'Usuario', 'Motivo'],
    $filas
);
