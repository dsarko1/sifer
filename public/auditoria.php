<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/models/Auditoria.php';

require_admin();

$entidades = ['producto', 'movimiento', 'categoria', 'usuario'];
$busquedaEntrada = $_GET['busqueda'] ?? '';
$busqueda = is_string($busquedaEntrada) ? trim($busquedaEntrada) : '';
$entidadEntrada = $_GET['entidad'] ?? '';
$entidad = is_string($entidadEntrada) && in_array($entidadEntrada, $entidades, true)
    ? $entidadEntrada
    : '';
$paginaEntrada = $_GET['pagina'] ?? null;
$pagina = is_string($paginaEntrada) || is_int($paginaEntrada)
    ? (id_valido($paginaEntrada) ?? 1)
    : 1;

$resultado = auditoria_listar([
    'busqueda' => $busqueda,
    'entidad' => $entidad,
], $pagina);

$titulo = 'Auditoría';
$datos = [
    'registros' => $resultado['filas'],
    'filtros' => [
        'busqueda' => $busqueda,
        'entidad' => $entidad,
    ],
    'paginacion' => [
        'pagina' => $resultado['pagina'],
        'paginas' => $resultado['paginas'],
        'total' => $resultado['total'],
        'por_pagina' => $resultado['por_pagina'],
    ],
    'entidades' => $entidades,
];

require __DIR__ . '/../views/auditoria.php';
