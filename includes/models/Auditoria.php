<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';

/**
 * Busca registros de auditoría con filtros y paginación.
 *
 * @return array{filas: array, total: int, pagina: int, paginas: int, por_pagina: int}
 */
function auditoria_listar(array $filtros = [], int $pagina = 1, int $porPagina = 25): array
{
    $pagina = max(1, $pagina);
    $porPagina = max(1, min(100, $porPagina));
    $condiciones = [];
    $parametros = [];

    $busqueda = $filtros['busqueda'] ?? null;
    if (is_string($busqueda) && trim($busqueda) !== '') {
        $termino = '%' . trim($busqueda) . '%';
        $condiciones[] = '(a.detalle LIKE :busqueda_detalle OR a.accion LIKE :busqueda_accion OR u.username LIKE :busqueda_username)';
        $parametros[':busqueda_detalle'] = $termino;
        $parametros[':busqueda_accion'] = $termino;
        $parametros[':busqueda_username'] = $termino;
    }

    $entidad = $filtros['entidad'] ?? null;
    if (is_string($entidad) && trim($entidad) !== '') {
        $condiciones[] = 'a.entidad = :entidad';
        $parametros[':entidad'] = trim($entidad);
    }

    $where = $condiciones === [] ? '' : ' WHERE ' . implode(' AND ', $condiciones);
    $from = ' FROM auditoria a LEFT JOIN usuarios u ON u.id = a.usuario_id';
    $consultaTotal = db()->prepare('SELECT COUNT(*)' . $from . $where);
    $consultaTotal->execute($parametros);
    $total = (int) $consultaTotal->fetchColumn();

    $consulta = db()->prepare(
        'SELECT a.id, a.creado_en, a.accion, a.entidad, a.entidad_id, a.detalle, a.ip,
                u.username, u.nombre'
        . $from . $where
        . ' ORDER BY a.creado_en DESC, a.id DESC'
        . ' LIMIT ' . $porPagina . ' OFFSET ' . (($pagina - 1) * $porPagina)
    );
    $consulta->execute($parametros);

    return [
        'filas' => $consulta->fetchAll(),
        'total' => $total,
        'pagina' => $pagina,
        'paginas' => max(1, (int) ceil($total / $porPagina)),
        'por_pagina' => $porPagina,
    ];
}
