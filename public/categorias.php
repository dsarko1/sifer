<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/models/Categoria.php';

require_admin();

$busqueda = trim((string) ($_GET['buscar'] ?? ''));
$categorias = categoria_listar($busqueda === '' ? null : $busqueda);

$titulo = 'Categorías';
$datos = [
    'categorias' => $categorias,
    'buscar' => $busqueda,
    'es_admin' => true,
];

require __DIR__ . '/../views/categorias.php';
