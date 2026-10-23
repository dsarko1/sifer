<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';

/**
 * Cuenta caracteres sin depender de que mbstring esté instalado.
 */
function categoria_longitud(string $texto): int
{
    return function_exists('mb_strlen') ? mb_strlen($texto, 'UTF-8') : strlen($texto);
}

/**
 * Lista las categorías por nombre, con la cantidad de productos activos.
 */
function categoria_listar(?string $busqueda = null): array
{
    $sql = 'SELECT c.id, c.nombre, c.descripcion,
                   COUNT(p.id) AS productos
            FROM categorias c
            LEFT JOIN productos p ON p.categoria_id = c.id AND p.activo = 1';
    $parametros = [];

    if ($busqueda !== null && trim($busqueda) !== '') {
        $sql .= ' WHERE c.nombre LIKE :busqueda';
        $parametros[':busqueda'] = '%' . trim($busqueda) . '%';
    }

    $sql .= ' GROUP BY c.id, c.nombre, c.descripcion ORDER BY c.nombre ASC';
    $consulta = db()->prepare($sql);
    $consulta->execute($parametros);

    return $consulta->fetchAll();
}

/**
 * Cuenta las categorías cargadas.
 */
function categoria_contar(): int
{
    return (int) db()->query('SELECT COUNT(*) FROM categorias')->fetchColumn();
}

/**
 * Busca una categoría por id.
 */
function categoria_obtener(int $id): ?array
{
    $consulta = db()->prepare(
        'SELECT id, nombre, descripcion FROM categorias WHERE id = :id LIMIT 1'
    );
    $consulta->execute([':id' => $id]);
    $categoria = $consulta->fetch();

    return $categoria === false ? null : $categoria;
}

/**
 * Indica si existe una categoría con ese id.
 */
function categoria_existe(int $id): bool
{
    $consulta = db()->prepare('SELECT 1 FROM categorias WHERE id = :id LIMIT 1');
    $consulta->execute([':id' => $id]);

    return $consulta->fetchColumn() !== false;
}

/**
 * Indica si un nombre ya está utilizado, ignorando mayúsculas y minúsculas.
 */
function categoria_nombre_existe(string $nombre, ?int $exceptoId = null): bool
{
    $sql = 'SELECT 1 FROM categorias WHERE LOWER(nombre) = LOWER(:nombre)';
    $parametros = [':nombre' => $nombre];

    if ($exceptoId !== null) {
        $sql .= ' AND id <> :id';
        $parametros[':id'] = $exceptoId;
    }

    $consulta = db()->prepare($sql . ' LIMIT 1');
    $consulta->execute($parametros);

    return $consulta->fetchColumn() !== false;
}

/**
 * Valida y normaliza los datos de una categoría.
 *
 * @return array{0: array{nombre: string, descripcion: ?string}, 1: array<string, string>}
 */
function categoria_validar(array $entrada, ?int $categoriaId = null): array
{
    $nombre = trim((string) ($entrada['nombre'] ?? ''));
    $descripcionEntrada = trim((string) ($entrada['descripcion'] ?? ''));
    $descripcion = $descripcionEntrada === '' ? null : $descripcionEntrada;
    $errores = [];

    if ($nombre === '') {
        $errores['nombre'] = 'El nombre es obligatorio.';
    } elseif (categoria_longitud($nombre) < 2 || categoria_longitud($nombre) > 80) {
        $errores['nombre'] = 'El nombre debe tener entre 2 y 80 caracteres.';
    } elseif (categoria_nombre_existe($nombre, $categoriaId)) {
        $errores['nombre'] = 'Ya existe una categoría con ese nombre.';
    }

    if ($descripcion !== null && categoria_longitud($descripcion) > 255) {
        $errores['descripcion'] = 'La descripción no puede superar los 255 caracteres.';
    }

    return [[
        'nombre' => $nombre,
        'descripcion' => $descripcion,
    ], $errores];
}

/**
 * Crea una categoría y devuelve su identificador.
 */
function categoria_crear(string $nombre, ?string $descripcion): int
{
    $consulta = db()->prepare(
        'INSERT INTO categorias (nombre, descripcion) VALUES (:nombre, :descripcion)'
    );
    $consulta->execute([
        ':nombre' => trim($nombre),
        ':descripcion' => $descripcion === null || trim($descripcion) === '' ? null : trim($descripcion),
    ]);

    return (int) db()->lastInsertId();
}

/**
 * Actualiza una categoría.
 */
function categoria_actualizar(int $id, string $nombre, ?string $descripcion): bool
{
    $consulta = db()->prepare(
        'UPDATE categorias
         SET nombre = :nombre, descripcion = :descripcion
         WHERE id = :id'
    );
    $consulta->execute([
        ':id' => $id,
        ':nombre' => trim($nombre),
        ':descripcion' => $descripcion === null || trim($descripcion) === '' ? null : trim($descripcion),
    ]);

    return $consulta->rowCount() > 0 || categoria_existe($id);
}

/**
 * Elimina una categoría. La clave foránea deja sus productos sin categoría.
 */
function categoria_eliminar(int $id): bool
{
    $consulta = db()->prepare('DELETE FROM categorias WHERE id = :id');
    $consulta->execute([':id' => $id]);

    return $consulta->rowCount() > 0;
}
