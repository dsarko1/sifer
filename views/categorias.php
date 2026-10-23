<?php

declare(strict_types=1);

require __DIR__ . '/partials/header.php';

$categorias = $datos['categorias'] ?? [];
$busqueda = (string) ($datos['buscar'] ?? '');
?>
<section class="page-heading">
    <div>
        <div class="eyebrow">Inventario</div>
        <h1>Categorías</h1>
        <p class="muted">Organizá los productos por categoría.</p>
    </div>
    <a class="button button-primary" href="<?= e(url('categoria-crear.php')) ?>">Nueva categoría</a>
</section>

<form class="filter-bar" method="get" action="<?= e(url('categorias.php')) ?>">
    <div class="field filter-search">
        <label for="buscar">Buscar</label>
        <input id="buscar" name="buscar" type="search" value="<?= e($busqueda) ?>" placeholder="Nombre o descripción">
    </div>
    <button class="button button-secondary filter-submit" type="submit">Buscar</button>
    <a class="button button-quiet filter-reset" href="<?= e(url('categorias.php')) ?>">Limpiar</a>
</form>

<section class="panel">
    <?php if ($categorias === []): ?>
        <p class="empty-state">No se encontraron categorías.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                <tr><th>Nombre</th><th>Descripción</th><th>Productos</th><th><span class="visually-hidden">Acciones</span></th></tr>
                </thead>
                <tbody>
                <?php foreach ($categorias as $categoria): ?>
                    <tr>
                        <td><strong><?= e($categoria['nombre'] ?? '') ?></strong></td>
                        <td><?= e($categoria['descripcion'] ?? '') ?></td>
                        <td><?= e($categoria['productos'] ?? 0) ?></td>
                        <td class="table-actions">
                            <a class="button button-small button-quiet"
                               href="<?= e(url('categoria-editar.php?id=' . rawurlencode((string) ($categoria['id'] ?? '')))) ?>">Editar</a>
                            <form action="<?= e(url('categoria-eliminar.php')) ?>" method="post"
                                  data-confirm="¿Eliminar esta categoría? Los productos quedarán sin categoría.">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= e($categoria['id'] ?? '') ?>">
                                <button class="button button-small button-danger-quiet" type="submit">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
