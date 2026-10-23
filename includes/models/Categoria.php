<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';

/**
 * Devuelve todas las categorías ordenadas por nombre.
 */
function categoria_listar(): array
{
    $consulta = db()->query(
        'SELECT id, nombre, descripcion
         FROM categorias
         ORDER BY nombre ASC'
    );

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
 * Indica si existe una categoría con ese id.
 */
function categoria_existe(int $id): bool
{
    $consulta = db()->prepare('SELECT 1 FROM categorias WHERE id = :id LIMIT 1');
    $consulta->execute([':id' => $id]);

    return $consulta->fetchColumn() !== false;
}
