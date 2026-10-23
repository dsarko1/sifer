<?php

declare(strict_types=1);

require __DIR__ . '/partials/header.php';

$resumen = $datos['resumen'] ?? [];
$porCategoria = $datos['por_categoria'] ?? [];
$bajoMinimo = $datos['bajo_minimo'] ?? [];
?>
<section class="page-heading">
    <div>
        <div class="eyebrow">Inventario</div>
        <h1>Reportes</h1>
        <p class="muted">Resumen del inventario y productos que requieren atención.</p>
    </div>
    <div class="table-actions">
        <a class="button button-secondary" href="<?= e(url('exportar-productos.php')) ?>">Exportar productos (CSV)</a>
        <a class="button button-quiet" href="<?= e(url('exportar-movimientos.php')) ?>">Exportar movimientos (CSV)</a>
    </div>
</section>

<section class="stat-grid" aria-label="Resumen del inventario">
    <article class="stat-card">
        <span class="stat-label">Productos activos</span>
        <strong><?= e($resumen['productos'] ?? 0) ?></strong>
    </article>
    <article class="stat-card">
        <span class="stat-label">Unidades en stock</span>
        <strong><?= e($resumen['unidades'] ?? 0) ?></strong>
    </article>
    <article class="stat-card">
        <span class="stat-label">Valor del inventario</span>
        <strong><?= e(precio_formateado($resumen['valor'] ?? 0)) ?></strong>
    </article>
    <article class="stat-card">
        <span class="stat-label">Productos bajo mínimo</span>
        <strong class="<?= (int) ($resumen['bajo_minimo'] ?? 0) > 0 ? 'text-warning' : '' ?>">
            <?= e($resumen['bajo_minimo'] ?? 0) ?>
        </strong>
    </article>
</section>

<section class="panel">
    <div class="section-heading">
        <div>
            <h2>Valor por categoría</h2>
            <p class="muted">Productos, unidades y valor del inventario agrupados por categoría.</p>
        </div>
    </div>
    <?php if ($porCategoria === []): ?>
        <p class="empty-state">Todavía no hay datos de inventario por categoría.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                <tr><th>Categoría</th><th>Productos</th><th>Unidades</th><th>Valor</th></tr>
                </thead>
                <tbody>
                <?php foreach ($porCategoria as $categoria): ?>
                    <tr>
                        <td><?= e($categoria['nombre'] ?? '') ?></td>
                        <td><?= e($categoria['productos'] ?? 0) ?></td>
                        <td><?= e($categoria['unidades'] ?? 0) ?></td>
                        <td><?= e(precio_formateado($categoria['valor'] ?? 0)) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <th>Total</th>
                    <th><?= e($resumen['productos'] ?? 0) ?></th>
                    <th><?= e($resumen['unidades'] ?? 0) ?></th>
                    <th><?= e(precio_formateado($resumen['valor'] ?? 0)) ?></th>
                </tr>
                </tfoot>
            </table>
        </div>
    <?php endif; ?>
</section>

<section class="panel">
    <div class="section-heading">
        <div>
            <h2>Productos bajo mínimo</h2>
            <p class="muted">Productos cuyo stock actual está en el mínimo o por debajo.</p>
        </div>
    </div>
    <?php if ($bajoMinimo === []): ?>
        <p class="empty-state">¡Todo en orden! No hay productos bajo el mínimo de stock.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                <tr><th>Código</th><th>Nombre</th><th>Categoría</th><th>Stock actual / mínimo</th></tr>
                </thead>
                <tbody>
                <?php foreach ($bajoMinimo as $producto): ?>
                    <tr>
                        <td><?= e($producto['codigo'] ?? '') ?></td>
                        <td><strong><?= e($producto['nombre'] ?? '') ?></strong></td>
                        <td><?= e($producto['categoria'] ?? 'Sin categoría') ?></td>
                        <td>
                            <span class="badge badge-warning">
                                <?= e($producto['stock_actual'] ?? 0) ?> / <?= e($producto['stock_minimo'] ?? 0) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
