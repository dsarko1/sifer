<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/models/Producto.php';
require_once __DIR__ . '/../includes/models/Categoria.php';

require_login();

$esAdmin     = es_admin();
$busqueda    = trim((string) ($_GET['buscar'] ?? ''));
$categoriaId = id_valido($_GET['categoria'] ?? null);

$productos  = producto_listar($busqueda, $categoriaId, true);
$categorias = categoria_listar();

if (!$esAdmin) {
    // El usuario común solo recibe disponibilidad: nunca el stock exacto.
    foreach ($productos as &$producto) {
        $producto['disponible'] = ((int) $producto['stock_actual'] > 0);

        unset($producto['stock_actual'], $producto['stock_minimo'], $producto['alerta'], $producto['faltante']);
    }

    unset($producto);
}

$titulo = 'Catálogo';
$datos  = [
    'productos'  => $productos,
    'categorias' => $categorias,
    'es_admin'   => $esAdmin,
];

if ($esAdmin) {
    $datos['alertas'] = producto_alertas();
}

require __DIR__ . '/../views/catalogo.php';
