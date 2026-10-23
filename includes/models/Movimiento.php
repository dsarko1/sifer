<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';

/**
 * Tipos de movimiento disponibles y su etiqueta para pantalla.
 *
 * @return array<string, string>
 */
function movimiento_tipos(): array
{
    return [
        'entrada' => 'Entrada',
        'salida'  => 'Salida',
        'ajuste'  => 'Ajuste',
    ];
}

/**
 * Devuelve la etiqueta legible de un tipo de movimiento.
 */
function movimiento_tipo_etiqueta(string $tipo): string
{
    return movimiento_tipos()[$tipo] ?? $tipo;
}

/**
 * Lista movimientos, del más reciente al más antiguo.
 */
function movimiento_listar(int $limite = 100, ?int $productoId = null): array
{
    $limite = max(1, min(500, $limite));

    $sql = 'SELECT m.id, m.producto_id, m.usuario_id, m.tipo, m.cantidad,
                   m.stock_anterior, m.stock_nuevo, m.motivo, m.creado_en,
                   p.nombre AS producto, p.codigo AS producto_codigo,
                   u.username AS usuario, u.nombre AS usuario_nombre
            FROM movimientos m
            INNER JOIN productos p ON p.id = m.producto_id
            INNER JOIN usuarios u ON u.id = m.usuario_id';

    $parametros = [];

    if ($productoId !== null) {
        $sql .= ' WHERE m.producto_id = :producto_id';
        $parametros[':producto_id'] = $productoId;
    }

    $sql .= ' ORDER BY m.creado_en DESC, m.id DESC LIMIT ' . $limite;

    $consulta = db()->prepare($sql);
    $consulta->execute($parametros);
    $movimientos = $consulta->fetchAll();

    foreach ($movimientos as &$movimiento) {
        $movimiento['tipo_etiqueta'] = movimiento_tipo_etiqueta($movimiento['tipo']);
    }

    unset($movimiento);

    return $movimientos;
}

/**
 * Devuelve movimientos filtrados y paginados, junto con los metadatos de página.
 *
 * @return array{filas: array, total: int, pagina: int, paginas: int, por_pagina: int}
 */
function movimiento_buscar(array $filtros = [], int $pagina = 1, int $porPagina = 20): array
{
    $pagina = max(1, $pagina);
    $porPagina = max(1, min(100, $porPagina));
    $condiciones = [];
    $parametros = [];

    $tipo = $filtros['tipo'] ?? null;
    if (is_string($tipo) && array_key_exists($tipo, movimiento_tipos())) {
        $condiciones[] = 'm.tipo = :tipo';
        $parametros[':tipo'] = $tipo;
    }

    $producto = filter_var($filtros['producto'] ?? null, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1],
    ]);
    if ($producto !== false && $producto !== null) {
        $condiciones[] = 'm.producto_id = :producto';
        $parametros[':producto'] = $producto;
    }

    foreach (['desde', 'hasta'] as $clave) {
        $fecha = $filtros[$clave] ?? null;
        if (!is_string($fecha) || !movimiento_fecha_valida($fecha)) {
            continue;
        }

        if ($clave === 'desde') {
            $condiciones[] = 'm.creado_en >= :desde';
            $parametros[':desde'] = $fecha;
        } else {
            $fechaHasta = (new DateTimeImmutable($fecha))->modify('+1 day')->format('Y-m-d');
            $condiciones[] = 'm.creado_en < :hasta_mas_un_dia';
            $parametros[':hasta_mas_un_dia'] = $fechaHasta;
        }
    }

    $where = $condiciones === [] ? '' : ' WHERE ' . implode(' AND ', $condiciones);
    $consultaTotal = db()->prepare('SELECT COUNT(*) FROM movimientos m' . $where);
    $consultaTotal->execute($parametros);
    $total = (int) $consultaTotal->fetchColumn();

    $sql = 'SELECT m.id, m.producto_id, m.usuario_id, m.tipo, m.cantidad,
                   m.stock_anterior, m.stock_nuevo, m.motivo, m.creado_en,
                   p.nombre AS producto, p.codigo AS producto_codigo,
                   u.username AS usuario, u.nombre AS usuario_nombre
            FROM movimientos m
            INNER JOIN productos p ON p.id = m.producto_id
            INNER JOIN usuarios u ON u.id = m.usuario_id'
        . $where
        . ' ORDER BY m.creado_en DESC, m.id DESC'
        . ' LIMIT ' . $porPagina
        . ' OFFSET ' . (($pagina - 1) * $porPagina);

    $consulta = db()->prepare($sql);
    $consulta->execute($parametros);
    $filas = $consulta->fetchAll();

    foreach ($filas as &$movimiento) {
        $movimiento['tipo_etiqueta'] = movimiento_tipo_etiqueta($movimiento['tipo']);
    }
    unset($movimiento);

    return [
        'filas' => $filas,
        'total' => $total,
        'pagina' => $pagina,
        'paginas' => max(1, (int) ceil($total / $porPagina)),
        'por_pagina' => $porPagina,
    ];
}

/**
 * Comprueba que una fecha tenga formato exacto Y-m-d y exista en el calendario.
 */
function movimiento_fecha_valida(string $fecha): bool
{
    $fechaParseada = DateTime::createFromFormat('!Y-m-d', $fecha);
    $errores = DateTime::getLastErrors();

    return $fechaParseada !== false
        && $fechaParseada->format('Y-m-d') === $fecha
        && ($errores === false || ($errores['warning_count'] === 0 && $errores['error_count'] === 0));
}

/**
 * Registra un movimiento de stock y actualiza el producto en una transacción.
 *
 * Entrada suma, salida resta (sin permitir stock negativo) y ajuste fija el
 * stock en la cantidad indicada. Si ya hay una transacción abierta, se une a
 * ella (así el alta de un producto y su stock inicial son atómicos).
 *
 * @return array{stock_anterior: int, stock_nuevo: int, tipo: string, cantidad: int}
 *
 * @throws DomainException cuando el producto no existe, está inactivo o el
 *                         movimiento no es válido.
 */
function movimiento_registrar(
    int $productoId,
    string $tipo,
    int $cantidad,
    string $motivo,
    int $usuarioId
): array {
    $tipo = strtolower(trim($tipo));

    if (!array_key_exists($tipo, movimiento_tipos())) {
        throw new DomainException('El tipo de movimiento no es válido.');
    }

    if ($tipo === 'ajuste') {
        if ($cantidad < 0) {
            throw new DomainException('El stock de un ajuste no puede ser negativo.');
        }
    } elseif ($cantidad <= 0) {
        throw new DomainException('La cantidad debe ser mayor a 0.');
    }

    $motivo = trim($motivo);

    if ($motivo === '') {
        throw new DomainException('El motivo es obligatorio.');
    }

    $longitud = function_exists('mb_strlen') ? mb_strlen($motivo, 'UTF-8') : strlen($motivo);

    if ($longitud > 255) {
        throw new DomainException('El motivo no puede superar los 255 caracteres.');
    }

    $pdo = db();
    $transaccionPropia = !$pdo->inTransaction();

    if ($transaccionPropia) {
        $pdo->beginTransaction();
    }

    try {
        $consulta = $pdo->prepare(
            'SELECT stock_actual
             FROM productos
             WHERE id = :id AND activo = 1
             FOR UPDATE'
        );
        $consulta->execute([':id' => $productoId]);
        $producto = $consulta->fetch();

        if ($producto === false) {
            throw new DomainException('El producto no existe o está dado de baja.');
        }

        $stockAnterior = (int) $producto['stock_actual'];

        $stockNuevo = match ($tipo) {
            'entrada' => $stockAnterior + $cantidad,
            'salida'  => $stockAnterior - $cantidad,
            'ajuste'  => $cantidad,
            default   => throw new DomainException('El tipo de movimiento no es válido.'),
        };

        if ($stockNuevo < 0) {
            throw new DomainException('No hay stock suficiente para registrar la salida.');
        }

        $pdo->prepare('UPDATE productos SET stock_actual = :stock WHERE id = :id')
            ->execute([':stock' => $stockNuevo, ':id' => $productoId]);

        $pdo->prepare(
            'INSERT INTO movimientos
                (producto_id, usuario_id, tipo, cantidad, stock_anterior, stock_nuevo, motivo)
             VALUES
                (:producto_id, :usuario_id, :tipo, :cantidad, :stock_anterior, :stock_nuevo, :motivo)'
        )->execute([
            ':producto_id'    => $productoId,
            ':usuario_id'     => $usuarioId,
            ':tipo'           => $tipo,
            ':cantidad'       => $cantidad,
            ':stock_anterior' => $stockAnterior,
            ':stock_nuevo'    => $stockNuevo,
            ':motivo'         => $motivo,
        ]);

        if ($transaccionPropia) {
            $pdo->commit();
        }

        return [
            'stock_anterior' => $stockAnterior,
            'stock_nuevo'    => $stockNuevo,
            'tipo'           => $tipo,
            'cantidad'       => $cantidad,
        ];
    } catch (Throwable $excepcion) {
        if ($transaccionPropia && $pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $excepcion;
    }
}
