<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/models/Producto.php';
require_once __DIR__ . '/../includes/models/Movimiento.php';

require_admin();

if (!es_post()) {
    redirigir('movimientos.php');
}

csrf_verify();

$errores     = [];
$productoId  = id_valido($_POST['producto_id'] ?? null);
$tipo        = strtolower(trim((string) ($_POST['tipo'] ?? '')));
$cantidadRaw = trim((string) ($_POST['cantidad'] ?? ''));
$motivo      = trim((string) ($_POST['motivo'] ?? ''));

if ($productoId === null) {
    $errores[] = 'Elegí un producto válido.';
}

if (!array_key_exists($tipo, movimiento_tipos())) {
    $errores[] = 'Elegí un tipo de movimiento válido.';
}

$cantidad = entero_no_negativo($cantidadRaw);

if ($cantidad === null) {
    $errores[] = 'La cantidad debe ser un número entero mayor o igual a 0.';
} elseif ($tipo === 'entrada' || $tipo === 'salida') {
    if ($cantidad <= 0) {
        $errores[] = 'La cantidad debe ser mayor a 0.';
    }
}

if ($motivo === '') {
    $errores[] = 'El motivo es obligatorio.';
} elseif (function_exists('mb_strlen') ? mb_strlen($motivo, 'UTF-8') > 255 : strlen($motivo) > 255) {
    $errores[] = 'El motivo no puede superar los 255 caracteres.';
}

if ($errores !== []) {
    flash('error', implode(' ', $errores));
    redirigir('movimientos.php');
}

try {
    $resultado = movimiento_registrar(
        $productoId,
        $tipo,
        $cantidad,
        $motivo,
        (int) usuario_actual()['id']
    );

    flash(
        'exito',
        'Movimiento registrado: el stock pasó de ' . $resultado['stock_anterior']
        . ' a ' . $resultado['stock_nuevo'] . '.'
    );
} catch (DomainException $excepcion) {
    flash('error', $excepcion->getMessage());
} catch (Throwable $excepcion) {
    error_log('Sifer: error al registrar movimiento: ' . $excepcion->getMessage());
    flash('error', 'No se pudo registrar el movimiento.');
}

redirigir('movimientos.php');
