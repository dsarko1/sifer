<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/models/Categoria.php';

require_admin();

$errores = [];

if (es_post()) {
    csrf_verify();
    [$categoriaValidada, $errores] = categoria_validar($_POST);

    if ($errores === []) {
        try {
            categoria_crear($categoriaValidada['nombre'], $categoriaValidada['descripcion']);
            flash('exito', 'Categoría "' . $categoriaValidada['nombre'] . '" creada correctamente.');
            redirigir('categorias.php');
        } catch (PDOException $excepcion) {
            if ($excepcion->getCode() === '23000') {
                $errores['nombre'] = 'Ya existe una categoría con ese nombre.';
            } else {
                error_log('Sifer: error al crear categoría: ' . $excepcion->getMessage());
                $errores['general'] = 'No se pudo guardar la categoría.';
            }
        }
    }
}

$titulo = 'Nueva categoría';
$datos = [
    'categoria' => null,
    'errores' => $errores,
    'accion' => 'crear',
];

require __DIR__ . '/../views/categoria-form.php';
