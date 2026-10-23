<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/auditoria.php';
require_once __DIR__ . '/../includes/models/Categoria.php';

require_admin();

if (!es_post()) {
    redirigir('categorias.php');
}

csrf_verify();

$id = id_valido($_POST['id'] ?? null);

if ($id === null) {
    flash('error', 'La categoría indicada no es válida.');
    redirigir('categorias.php');
}

$categoria = categoria_obtener($id);

if ($categoria === null || !categoria_eliminar($id)) {
    flash('error', 'La categoría no existe o ya fue eliminada.');
    redirigir('categorias.php');
}

auditar('eliminar', 'categoria', $id, 'Nombre: ' . $categoria['nombre']);
flash('exito', 'Categoría "' . $categoria['nombre'] . '" eliminada correctamente.');
redirigir('categorias.php');
