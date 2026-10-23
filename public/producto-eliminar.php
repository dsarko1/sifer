<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/auditoria.php';
require_once __DIR__ . '/../includes/models/Producto.php';

require_admin();

if (!es_post()) {
    redirigir('productos.php');
}

csrf_verify();

$id = id_valido($_POST['id'] ?? null);

if ($id === null) {
    flash('error', 'El producto indicado no es válido.');
    redirigir('productos.php');
}

$producto = producto_buscar_por_id($id);

if ($producto === null) {
    flash('error', 'El producto no existe o ya está dado de baja.');
    redirigir('productos.php');
}

producto_desactivar($id);
auditar('eliminar', 'producto', $id, 'Código: ' . $producto['codigo'] . '; nombre: ' . $producto['nombre']);
flash('exito', 'Producto "' . $producto['nombre'] . '" dado de baja.');
redirigir('productos.php');
