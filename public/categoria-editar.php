<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/auditoria.php';
require_once __DIR__ . '/../includes/models/Categoria.php';

require_admin();

$id = id_valido($_POST['id'] ?? $_GET['id'] ?? null);

if ($id === null) {
    flash('error', 'La categoría indicada no es válida.');
    redirigir('categorias.php');
}

$categoria = categoria_obtener($id);

if ($categoria === null) {
    flash('error', 'La categoría no existe.');
    redirigir('categorias.php');
}

$errores = [];

if (es_post()) {
    csrf_verify();
    [$valores, $errores] = categoria_validar($_POST, $id);
    $categoria = array_merge($categoria, $valores);

    if ($errores === []) {
        try {
            if (categoria_actualizar($id, $categoria['nombre'], $categoria['descripcion'])) {
                auditar('editar', 'categoria', $id, 'Nombre: ' . $categoria['nombre']);
                flash('exito', 'Categoría "' . $categoria['nombre'] . '" actualizada correctamente.');
                redirigir('categorias.php');
            }

            $errores['general'] = 'La categoría no existe.';
        } catch (PDOException $excepcion) {
            if ($excepcion->getCode() === '23000') {
                $errores['nombre'] = 'Ya existe una categoría con ese nombre.';
            } else {
                error_log('Sifer: error al editar categoría: ' . $excepcion->getMessage());
                $errores['general'] = 'No se pudo guardar la categoría.';
            }
        }
    }
}

$titulo = 'Editar categoría';
$datos = [
    'categoria' => $categoria,
    'errores' => $errores,
    'accion' => 'editar',
];

require __DIR__ . '/../views/categoria-form.php';
