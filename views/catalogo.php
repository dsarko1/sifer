<?php

declare(strict_types=1);

require __DIR__ . '/partials/header.php';

$productos = $datos['productos'] ?? [];
$categorias = $datos['categorias'] ?? [];
$esAdmin = (bool) ($datos['es_admin'] ?? false);
$alertas = $esAdmin ? ($datos['alertas'] ?? []) : [];
$categoriaSeleccionada = (string) ($_GET['categoria'] ?? '');
$busqueda = (string) ($_GET['buscar'] ?? '');
?>
<section class="page-heading">
    <div>
        <div class="eyebrow"><?= $esAdmin ? 'Administración' : 'Explorá el inventario' ?></div>
        <h1>Catálogo</h1>
        <p class="muted"><?= $esAdmin ? 'Productos y alertas de stock.' : 'Consultá los productos y su disponibilidad.' ?></p>
    </div>
    <?php if ($esAdmin): ?>
        <a class="button button-primary" href="<?= e(url('producto-crear.php')) ?>">Nuevo producto</a>
    <?php endif; ?>
</section>

<?php if ($esAdmin && $alertas !== []): ?>
    <div class="notice notice-warning">
        <strong><?= e(count($alertas)) ?> productos requieren atención.</strong>
        <span>Revisá las etiquetas de stock bajo en el catálogo.</span>
    </div>
<?php endif; ?>

<form class="filter-bar" method="get" action="<?= e(url('catalogo.php')) ?>">
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
    <a class="button button-quiet filter-reset" href="<?= e(url('catalogo.php')) ?>">Limpiar</a>
</form>

<?php if ($productos === []): ?>
    <section class="panel"><p class="empty-state">No se encontraron productos.</p></section>
<?php else: ?>
    <section class="catalog-grid" aria-label="Productos">
        <?php foreach ($productos as $producto): ?>
            <article class="product-card">
                <div class="product-card-top">
                    <span class="eyebrow"><?= e($producto['categoria'] ?? 'Sin categoría') ?></span>
                    <?php if ($esAdmin): ?>
                        <?php if (!empty($producto['alerta'])): ?>
                            <span class="badge badge-warning">Bajo stock</span>
                        <?php else: ?>
                            <span class="badge badge-success">Normal</span>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if (!empty($producto['disponible'])): ?>
                            <span class="badge badge-success">Disponible</span>
                        <?php else: ?>
                            <span class="badge badge-neutral">Sin disponibilidad</span>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <h2><?= e($producto['nombre'] ?? '') ?></h2>
                <p class="product-code">Código: <?= e($producto['codigo'] ?? '') ?></p>
                <?php if (!empty($producto['descripcion'])): ?>
                    <p class="product-description"><?= e($producto['descripcion']) ?></p>
                <?php endif; ?>
                <div class="product-card-bottom">
                    <strong class="product-price"><?= e(precio_formateado($producto['precio'] ?? 0)) ?></strong>
                    <?php if ($esAdmin): ?>
                        <span class="stock-summary">Stock <?= e($producto['stock_actual'] ?? 0) ?> · mínimo <?= e($producto['stock_minimo'] ?? 0) ?></span>
                    <?php endif; ?>
                </div>
                <?php if ($esAdmin): ?>
                    <div class="product-card-actions">
                        <a class="button button-small button-quiet"
                           href="<?= e(url('producto-editar.php?id=' . rawurlencode((string) ($producto['id'] ?? '')))) ?>">Editar</a>
                        <form action="<?= e(url('producto-eliminar.php')) ?>" method="post"
                              data-confirm="¿Dar de baja este producto?">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= e($producto['id'] ?? '') ?>">
                            <button class="button button-small button-danger-quiet" type="submit">Dar de baja</button>
                        </form>
                    </div>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </section>
<?php endif; ?>
<?php require __DIR__ . '/partials/footer.php'; ?>
