<?php

declare(strict_types=1);

require __DIR__ . '/partials/header.php';

$totales = $datos['totales'] ?? [];
$alertas = $datos['alertas'] ?? [];
$movimientos = $datos['movimientos'] ?? [];
?>
<section class="page-heading">
    <div>
        <div class="eyebrow">Resumen</div>
        <h1>Panel de control</h1>
        <p class="muted">Estado general del inventario y actividad reciente.</p>
    </div>
    <a class="button button-primary" href="<?= e(url('producto-crear.php')) ?>">Nuevo producto</a>
</section>

<section class="stat-grid" aria-label="Totales">
    <article class="stat-card">
        <span class="stat-label">Productos</span>
        <strong><?= e($totales['productos'] ?? 0) ?></strong>
        <a href="<?= e(url('productos.php')) ?>">Ver productos</a>
    </article>
    <article class="stat-card">
        <span class="stat-label">Categorías</span>
        <strong><?= e($totales['categorias'] ?? 0) ?></strong>
    </article>
    <article class="stat-card">
        <span class="stat-label">Bajo stock</span>
        <strong class="<?= (int) ($totales['bajo_stock'] ?? 0) > 0 ? 'text-warning' : '' ?>">
            <?= e($totales['bajo_stock'] ?? 0) ?>
        </strong>
        <a href="<?= e(url('catalogo.php')) ?>">Revisar catálogo</a>
    </article>
</section>

<div class="content-grid">
    <section class="panel">
        <div class="section-heading">
            <div>
                <h2>Alertas de stock</h2>
                <p class="muted">Productos en el mínimo o por debajo.</p>
            </div>
            <a href="<?= e(url('catalogo.php')) ?>">Ver catálogo</a>
        </div>
        <?php if ($alertas === []): ?>
            <p class="empty-state">No hay productos con alerta de stock.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Producto</th><th>Disponible</th><th>Mínimo</th><th>Faltante</th></tr></thead>
                    <tbody>
                    <?php foreach ($alertas as $producto): ?>
                        <tr>
                            <td>
                                <strong><?= e($producto['nombre'] ?? '') ?></strong>
                                <span class="table-subtitle"><?= e($producto['codigo'] ?? '') ?></span>
                            </td>
                            <td><?= e($producto['stock_actual'] ?? 0) ?></td>
                            <td><?= e($producto['stock_minimo'] ?? 0) ?></td>
                            <td><span class="badge badge-warning"><?= e($producto['faltante'] ?? 0) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

    <section class="panel">
        <div class="section-heading">
            <div>
                <h2>Actividad reciente</h2>
                <p class="muted">Últimos movimientos registrados.</p>
            </div>
            <a href="<?= e(url('movimientos.php')) ?>">Ver todos</a>
        </div>
        <?php if ($movimientos === []): ?>
            <p class="empty-state">Todavía no hay movimientos.</p>
        <?php else: ?>
            <div class="activity-list">
                <?php foreach ($movimientos as $movimiento): ?>
                    <article class="activity-item">
                        <span class="activity-marker activity-<?= e($movimiento['tipo'] ?? 'info') ?>"></span>
                        <div class="activity-body">
                            <strong><?= e($movimiento['producto'] ?? '') ?></strong>
                            <span><?= e($movimiento['tipo_etiqueta'] ?? '') ?> · <?= e($movimiento['cantidad'] ?? 0) ?></span>
                            <span class="table-subtitle"><?= e($movimiento['usuario_nombre'] ?? $movimiento['usuario'] ?? '') ?></span>
                        </div>
                        <time class="muted"><?= e(fecha_formateada($movimiento['creado_en'] ?? null)) ?></time>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
