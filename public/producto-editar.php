<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/models/Producto.php';
require_once __DIR__ . '/../includes/models/Categoria.php';

require_admin();

$id = id_valido($_POST['id'] ?? $_GET['id'] ?? null);

if ($id === null) {
    flash('error', 'El producto indicado no es válido.');
    redirigir('productos.php');
}

$producto = producto_buscar_por_id($id);

if ($producto === null) {
    flash('error', 'El producto no existe o está dado de baja.');
    redirigir('productos.php');
}

$categorias = categoria_listar();
$errores    = [];

if (es_post()) {
    csrf_verify();

    [$valores, $errores] = producto_validar($_POST, false, $id);
    $producto            = array_merge($producto, $valores);

    if ($errores === []) {
        try {
            producto_actualizar($id, $producto);
            flash('exito', 'Producto "' . $producto['nombre'] . '" actualizado correctamente.');
            redirigir('productos.php');
        } catch (PDOException $excepcion) {
            if ($excepcion->getCode() === '23000') {
                $errores['codigo'] = 'Ya existe un producto con ese código.';
            } else {
                error_log('Sifer: error al editar producto: ' . $excepcion->getMessage());
                $errores['general'] = 'No se pudo guardar el producto.';
            }
        }
    }
}

$titulo = 'Editar producto';
$datos  = [
    'producto'   => $producto,
    'categorias' => $categorias,
    'errores'    => $errores,
    'accion'     => 'editar',
];

require __DIR__ . '/../views/producto-form.php';
