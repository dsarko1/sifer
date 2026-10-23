<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/auditoria.php';
require_once __DIR__ . '/../includes/models/Producto.php';
require_once __DIR__ . '/../includes/models/Categoria.php';

require_admin();

$categorias = categoria_listar();
$producto   = [
    'id'           => null,
    'codigo'       => '',
    'nombre'       => '',
    'descripcion'  => '',
    'categoria_id' => '',
    'precio'       => '',
    'stock_actual' => '0',
    'stock_minimo' => '0',
    'activo'       => 1,
];
$errores = [];

if (es_post()) {
    csrf_verify();

    [$valores, $errores] = producto_validar($_POST, true);
    $valores['id']       = null;
    $valores['activo']   = 1;
    $producto            = $valores;

    if ($errores === []) {
        try {
            $productoId = producto_crear_con_stock_inicial($producto, (int) usuario_actual()['id']);
            auditar('crear', 'producto', $productoId, 'Código: ' . $producto['codigo'] . '; nombre: ' . $producto['nombre']);
            flash('exito', 'Producto "' . $producto['nombre'] . '" creado correctamente.');
            redirigir('productos.php');
        } catch (PDOException $excepcion) {
            if ($excepcion->getCode() === '23000') {
                $errores['codigo'] = 'Ya existe un producto con ese código.';
            } else {
                error_log('Sifer: error al crear producto: ' . $excepcion->getMessage());
                $errores['general'] = 'No se pudo guardar el producto.';
            }
        } catch (Throwable $excepcion) {
            error_log('Sifer: error al crear producto: ' . $excepcion->getMessage());
            $errores['general'] = 'No se pudo guardar el producto.';
        }
    }
}

$titulo = 'Nuevo producto';
$datos  = [
    'producto'   => $producto,
    'categorias' => $categorias,
    'errores'    => $errores,
    'accion'     => 'crear',
];

require __DIR__ . '/../views/producto-form.php';
