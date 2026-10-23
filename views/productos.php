<?php

declare(strict_types=1);

require __DIR__ . '/partials/header.php';

$productos = $datos['productos'] ?? [];
$categorias = $datos['categorias'] ?? [];
$categoriaSeleccionada = (string) ($_GET['categoria'] ?? '');
$busqueda = (string) ($_GET['buscar'] ?? '');
?>
<section class="page-heading">
    <div>
        <div class="eyebrow">Inventario</div>
        <h1>Productos</h1>
        <p class="muted">Administrá productos y niveles de stock.</p>
    </div>
    <a class="button button-primary" href="<?= e(url('producto-crear.php')) ?>">Nuevo producto</a>
</section>

<form class="filter-bar" method="get" action="<?= e(url('productos.php')) ?>">
    <div class="field filter-search">
        <label for="buscar">Buscar</label>
        <input id="buscar" name="buscar" type="search" value="<?= e($busqueda) ?>" placeholder="Nombre o código">
    </div>
    <div class="field">
        <label for="categoria">Categoría</label>
        <select id="categoria" name="categoria">
            <option value="">Todas</option>
            <?php foreach ($categorias as $categoria): ?>
                <option value="<?= e($categoria['id'] ?? '') ?>"
                    <?= $categoriaSeleccionada === (string) ($categoria['id'] ?? '') ? 'selected' : '' ?>>
                    <?= e($categoria['nombre'] ?? '') ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <button class="button button-secondary filter-submit" type="submit">Filtrar</button>
    <a class="button button-quiet filter-reset" href="<?= e(url('productos.php')) ?>">Limpiar</a>
</form>

<section class="panel">
    <?php if ($productos === []): ?>
        <p class="empty-state">No se encontraron productos.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                <tr><th>Producto</th><th>Categoría</th><th>Precio</th><th>Stock</th><th>Mínimo</th><th>Estado</th><th><span class="visually-hidden">Acciones</span></th></tr>
                </thead>
                <tbody>
                <?php foreach ($productos as $producto): ?>
                    <tr>
                        <td>
                            <strong><?= e($producto['nombre'] ?? '') ?></strong>
                            <span class="table-subtitle"><?= e($producto['codigo'] ?? '') ?></span>
                        </td>
                        <td><?= e($producto['categoria'] ?? 'Sin categoría') ?></td>
                        <td><?= e(precio_formateado($producto['precio'] ?? 0)) ?></td>
                        <td><?= e($producto['stock_actual'] ?? 0) ?></td>
                        <td><?= e($producto['stock_minimo'] ?? 0) ?></td>
                        <td>
                            <?php if (!empty($producto['alerta'])): ?>
                                <span class="badge badge-warning">Bajo stock</span>
                            <?php else: ?>
                                <span class="badge badge-success">Normal</span>
                            <?php endif; ?>
                        </td>
                        <td class="table-actions">
                            <a class="button button-small button-quiet"
                               href="<?= e(url('producto-editar.php?id=' . rawurlencode((string) ($producto['id'] ?? '')))) ?>">Editar</a>
                            <form action="<?= e(url('producto-eliminar.php')) ?>" method="post"
                                  data-confirm="¿Dar de baja este producto?">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= e($producto['id'] ?? '') ?>">
                                <button class="button button-small button-danger-quiet" type="submit">Dar de baja</button>
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
