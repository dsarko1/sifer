<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/models/Producto.php';
require_once __DIR__ . '/../includes/models/Categoria.php';

require_admin();

$busqueda    = trim((string) ($_GET['buscar'] ?? ''));
$categoriaId = id_valido($_GET['categoria'] ?? null);

$productos  = producto_listar($busqueda, $categoriaId, true);
$categorias = categoria_listar();

$titulo = 'Productos';
$datos  = [
    'productos'  => $productos,
    'categorias' => $categorias,
    'es_admin'   => true,
];

require __DIR__ . '/../views/productos.php';
