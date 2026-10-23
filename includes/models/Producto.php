<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/Categoria.php';
require_once __DIR__ . '/Movimiento.php';

/**
 * Cuenta caracteres sin depender de que mbstring esté instalado.
 */
function producto_longitud(string $texto): int
{
    return function_exists('mb_strlen') ? mb_strlen($texto, 'UTF-8') : strlen($texto);
}

/**
 * Agrega los datos derivados que usan las vistas.
 */
function producto_completar_estado(array $producto): array
{
    $producto['disponible'] = (int) $producto['stock_actual'] > 0;
    $producto['alerta']     = (int) $producto['stock_actual'] <= (int) $producto['stock_minimo'];

    return $producto;
}

/**
 * Lista productos activos, con búsqueda por nombre o código y filtro por categoría.
 */
function producto_listar(string $busqueda = '', ?int $categoriaId = null, bool $soloActivos = true): array
{
    $sql = 'SELECT p.id, p.codigo, p.nombre, p.descripcion, p.categoria_id, p.precio,
                   p.stock_actual, p.stock_minimo, p.activo, p.creado_en, p.actualizado_en,
                   c.nombre AS categoria
            FROM productos p
            LEFT JOIN categorias c ON c.id = p.categoria_id
            WHERE 1 = 1';

    $parametros = [];

    if ($soloActivos) {
        $sql .= ' AND p.activo = 1';
    }

    if ($busqueda !== '') {
        $sql .= ' AND (p.nombre LIKE :busqueda_nombre OR p.codigo LIKE :busqueda_codigo)';
        $parametros[':busqueda_nombre'] = '%' . $busqueda . '%';
        $parametros[':busqueda_codigo'] = '%' . $busqueda . '%';
    }

    if ($categoriaId !== null) {
        $sql .= ' AND p.categoria_id = :categoria_id';
        $parametros[':categoria_id'] = $categoriaId;
    }

    $sql .= ' ORDER BY p.nombre ASC';

    $consulta = db()->prepare($sql);
    $consulta->execute($parametros);

    return array_map('producto_completar_estado', $consulta->fetchAll());
}

/**
 * Busca un producto por id.
 */
function producto_buscar_por_id(int $id, bool $soloActivo = true): ?array
{
    $sql = 'SELECT p.id, p.codigo, p.nombre, p.descripcion, p.categoria_id, p.precio,
                   p.stock_actual, p.stock_minimo, p.activo, p.creado_en, p.actualizado_en,
                   c.nombre AS categoria
            FROM productos p
            LEFT JOIN categorias c ON c.id = p.categoria_id
            WHERE p.id = :id';

    if ($soloActivo) {
        $sql .= ' AND p.activo = 1';
    }

    $sql .= ' LIMIT 1';

    $consulta = db()->prepare($sql);
    $consulta->execute([':id' => $id]);
    $producto = $consulta->fetch();

    return $producto === false ? null : producto_completar_estado($producto);
}

/**
 * Indica si un código de producto ya está en uso.
 */
function producto_codigo_existe(string $codigo, ?int $exceptoId = null): bool
{
    $sql = 'SELECT 1 FROM productos WHERE codigo = :codigo';
    $parametros = [':codigo' => $codigo];

    if ($exceptoId !== null) {
        $sql .= ' AND id <> :id';
        $parametros[':id'] = $exceptoId;
    }

    $sql .= ' LIMIT 1';

    $consulta = db()->prepare($sql);
    $consulta->execute($parametros);

    return $consulta->fetchColumn() !== false;
}

/**
 * Valida y normaliza los datos de un producto.
 *
 * Devuelve [datos, errores]. En el alta se valida también el stock inicial;
 * en la edición el stock solo cambia mediante movimientos.
 *
 * @return array{0: array<string, string>, 1: array<string, string>}
 */
function producto_validar(array $entrada, bool $incluirStock = true, ?int $productoId = null): array
{
    $datos = [
        'codigo'       => trim((string) ($entrada['codigo'] ?? '')),
        'nombre'       => trim((string) ($entrada['nombre'] ?? '')),
        'descripcion'  => trim((string) ($entrada['descripcion'] ?? '')),
        'categoria_id' => trim((string) ($entrada['categoria_id'] ?? '')),
        'precio'       => trim((string) ($entrada['precio'] ?? '')),
        'stock_minimo' => trim((string) ($entrada['stock_minimo'] ?? '0')),
    ];

    $errores = [];

    if ($datos['codigo'] === '') {
        $errores['codigo'] = 'El código es obligatorio.';
    } elseif (producto_longitud($datos['codigo']) > 30) {
        $errores['codigo'] = 'El código no puede superar los 30 caracteres.';
    } elseif (producto_codigo_existe($datos['codigo'], $productoId)) {
        $errores['codigo'] = 'Ya existe un producto con ese código.';
    }

    if ($datos['nombre'] === '') {
        $errores['nombre'] = 'El nombre es obligatorio.';
    } elseif (producto_longitud($datos['nombre']) > 120) {
        $errores['nombre'] = 'El nombre no puede superar los 120 caracteres.';
    }

    if ($datos['categoria_id'] !== '') {
        $categoriaId = id_valido($datos['categoria_id']);

        if ($categoriaId === null || !categoria_existe($categoriaId)) {
            $errores['categoria_id'] = 'Elegí una categoría válida.';
        } else {
            $datos['categoria_id'] = (string) $categoriaId;
        }
    }

    $precioNormalizado = $datos['precio'];

    if (str_contains($precioNormalizado, ',')) {
        $precioNormalizado = str_replace('.', '', $precioNormalizado);
        $precioNormalizado = str_replace(',', '.', $precioNormalizado);
    }

    if ($precioNormalizado === '' || !is_numeric($precioNormalizado) || (float) $precioNormalizado < 0) {
        $errores['precio'] = 'Ingresá un precio válido, mayor o igual a 0.';
    } else {
        $datos['precio'] = number_format((float) $precioNormalizado, 2, '.', '');
    }

    if ($datos['stock_minimo'] === '') {
        $datos['stock_minimo'] = '0';
    }

    $stockMinimo = entero_no_negativo($datos['stock_minimo']);

    if ($stockMinimo === null) {
        $errores['stock_minimo'] = 'El stock mínimo debe ser un entero mayor o igual a 0.';
    } else {
        $datos['stock_minimo'] = (string) $stockMinimo;
    }

    if ($incluirStock) {
        $stockInicial = trim((string) ($entrada['stock_actual'] ?? '0'));

        if ($stockInicial === '') {
            $stockInicial = '0';
        }

        $stock = entero_no_negativo($stockInicial);

        if ($stock === null) {
            $errores['stock_actual'] = 'El stock inicial debe ser un entero mayor o igual a 0.';
        } else {
            $datos['stock_actual'] = (string) $stock;
        }
    }

    return [$datos, $errores];
}

/**
 * Inserta un producto nuevo. El stock siempre nace en 0.
 */
function producto_crear(array $producto): int
{
    $categoriaId = $producto['categoria_id'] !== '' ? (int) $producto['categoria_id'] : null;
    $descripcion = $producto['descripcion'] !== '' ? $producto['descripcion'] : null;

    $consulta = db()->prepare(
        'INSERT INTO productos
            (codigo, nombre, descripcion, categoria_id, precio, stock_actual, stock_minimo, activo)
         VALUES
            (:codigo, :nombre, :descripcion, :categoria_id, :precio, 0, :stock_minimo, 1)'
    );

    $consulta->execute([
        ':codigo'       => $producto['codigo'],
        ':nombre'       => $producto['nombre'],
        ':descripcion'  => $descripcion,
        ':categoria_id' => $categoriaId,
        ':precio'       => number_format((float) $producto['precio'], 2, '.', ''),
        ':stock_minimo' => (int) $producto['stock_minimo'],
    ]);

    return (int) db()->lastInsertId();
}

/**
 * Crea un producto y, si corresponde, carga el stock inicial como ajuste.
 *
 * Ambas operaciones van en una misma transacción: o se guardan las dos,
 * o no se guarda ninguna.
 */
function producto_crear_con_stock_inicial(array $producto, int $usuarioId): int
{
    $pdo = db();
    $pdo->beginTransaction();

    try {
        $productoId    = producto_crear($producto);
        $stockInicial  = (int) ($producto['stock_actual'] ?? 0);

        if ($stockInicial > 0) {
            movimiento_registrar($productoId, 'ajuste', $stockInicial, 'Stock inicial', $usuarioId);
        }

        $pdo->commit();

        return $productoId;
    } catch (Throwable $excepcion) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $excepcion;
    }
}

/**
 * Actualiza los datos de un producto. No modifica el stock actual.
 */
function producto_actualizar(int $id, array $producto): void
{
    $categoriaId = $producto['categoria_id'] !== '' ? (int) $producto['categoria_id'] : null;
    $descripcion = $producto['descripcion'] !== '' ? $producto['descripcion'] : null;

    $consulta = db()->prepare(
        'UPDATE productos
         SET codigo = :codigo,
             nombre = :nombre,
             descripcion = :descripcion,
             categoria_id = :categoria_id,
             precio = :precio,
             stock_minimo = :stock_minimo
         WHERE id = :id AND activo = 1'
    );

    $consulta->execute([
        ':codigo'       => $producto['codigo'],
        ':nombre'       => $producto['nombre'],
        ':descripcion'  => $descripcion,
        ':categoria_id' => $categoriaId,
        ':precio'       => number_format((float) $producto['precio'], 2, '.', ''),
        ':stock_minimo' => (int) $producto['stock_minimo'],
        ':id'           => $id,
    ]);
}

/**
 * Da de baja un producto de forma lógica (activo = 0).
 */
function producto_desactivar(int $id): void
{
    $consulta = db()->prepare('UPDATE productos SET activo = 0 WHERE id = :id AND activo = 1');
    $consulta->execute([':id' => $id]);
}

/**
 * Lista los productos activos con stock igual o por debajo del mínimo.
 */
function producto_alertas(int $limite = 0): array
{
    $sql = 'SELECT p.id, p.codigo, p.nombre, p.descripcion, p.categoria_id, p.precio,
                   p.stock_actual, p.stock_minimo, p.activo, p.creado_en, p.actualizado_en,
                   c.nombre AS categoria
            FROM productos p
            LEFT JOIN categorias c ON c.id = p.categoria_id
            WHERE p.activo = 1 AND p.stock_actual <= p.stock_minimo
            ORDER BY (p.stock_actual - p.stock_minimo) ASC, p.nombre ASC';

    if ($limite > 0) {
        $sql .= ' LIMIT ' . (int) $limite;
    }

    $consulta = db()->query($sql);
    $productos = array_map('producto_completar_estado', $consulta->fetchAll());

    foreach ($productos as &$producto) {
        $producto['faltante'] = max(0, (int) $producto['stock_minimo'] - (int) $producto['stock_actual']);
    }

    unset($producto);

    return $productos;
}

/**
 * Cuenta productos activos (o todos, si se pide).
 */
function producto_contar(bool $soloActivos = true): int
{
    $sql = 'SELECT COUNT(*) FROM productos' . ($soloActivos ? ' WHERE activo = 1' : '');

    return (int) db()->query($sql)->fetchColumn();
}

/**
 * Cuenta productos activos con stock bajo.
 */
function producto_contar_alertas(): int
{
    return (int) db()
        ->query('SELECT COUNT(*) FROM productos WHERE activo = 1 AND stock_actual <= stock_minimo')
        ->fetchColumn();
}

/**
 * Devuelve los indicadores generales de stock activo.
 *
 * @return array{productos: int, unidades: int, valor: float, bajo_minimo: int}
 */
function producto_resumen_stock(): array
{
    $consulta = db()->query(
        'SELECT COUNT(*) AS productos,
                COALESCE(SUM(stock_actual), 0) AS unidades,
                COALESCE(SUM(precio * stock_actual), 0) AS valor,
                COALESCE(SUM(CASE WHEN stock_actual <= stock_minimo THEN 1 ELSE 0 END), 0) AS bajo_minimo
         FROM productos
         WHERE activo = 1'
    );
    $resumen = $consulta->fetch();

    return [
        'productos' => (int) $resumen['productos'],
        'unidades' => (int) $resumen['unidades'],
        'valor' => (float) $resumen['valor'],
        'bajo_minimo' => (int) $resumen['bajo_minimo'],
    ];
}

/**
 * Resume unidades y valor de stock activo por categoría.
 */
function producto_valor_por_categoria(): array
{
    $consulta = db()->query(
        "SELECT COALESCE(c.nombre, 'Sin categoría') AS nombre,
                COUNT(p.id) AS productos,
                COALESCE(SUM(p.stock_actual), 0) AS unidades,
                COALESCE(SUM(p.precio * p.stock_actual), 0) AS valor
         FROM productos p
         LEFT JOIN categorias c ON c.id = p.categoria_id
         WHERE p.activo = 1
         GROUP BY c.id, c.nombre
         ORDER BY valor DESC, nombre ASC"
    );

    return array_map(static fn (array $fila): array => [
        'nombre' => $fila['nombre'],
        'productos' => (int) $fila['productos'],
        'unidades' => (int) $fila['unidades'],
        'valor' => (float) $fila['valor'],
    ], $consulta->fetchAll());
}

/**
 * Lista productos activos cuyo stock está igual o por debajo del mínimo.
 */
function producto_bajo_minimo(): array
{
    $consulta = db()->query(
        'SELECT p.id, p.codigo, p.nombre, p.stock_actual, p.stock_minimo,
                c.nombre AS categoria
         FROM productos p
         LEFT JOIN categorias c ON c.id = p.categoria_id
         WHERE p.activo = 1 AND p.stock_actual <= p.stock_minimo
         ORDER BY (p.stock_actual - p.stock_minimo) ASC, p.nombre ASC'
    );

    return $consulta->fetchAll();
}
