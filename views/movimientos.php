<?php

declare(strict_types=1);

require __DIR__ . '/partials/header.php';

$movimientos = $datos['movimientos'] ?? [];
$productos = $datos['productos'] ?? [];
$productoSeleccionado = (string) ($_GET['producto'] ?? '');
?>
<section class="page-heading">
    <div>
        <div class="eyebrow">Inventario</div>
        <h1>Movimientos</h1>
        <p class="muted">Registrá entradas, salidas y ajustes de stock.</p>
    </div>
</section>

<section class="panel form-panel movement-form-panel">
    <h2>Registrar movimiento</h2>
    <form action="<?= e(url('movimiento-crear.php')) ?>" method="post" class="form-grid">
        <?= csrf_field() ?>
        <div class="field">
            <label for="producto_id">Producto</label>
            <select id="producto_id" name="producto_id" required>
                <option value="">Elegí un producto</option>
                <?php foreach ($productos as $producto): ?>
                    <option value="<?= e($producto['id'] ?? '') ?>">
                        <?= e($producto['nombre'] ?? '') ?> · <?= e($producto['codigo'] ?? '') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field">
            <label for="tipo">Tipo</label>
            <select id="tipo" name="tipo" required>
                <option value="entrada">Entrada</option>
                <option value="salida">Salida</option>
                <option value="ajuste">Ajuste (establece el stock final)</option>
            </select>
        </div>
        <div class="field">
            <label for="cantidad">Cantidad</label>
            <input id="cantidad" name="cantidad" type="number" min="0" step="1" required>
            <span class="field-hint">Para un ajuste, indicá el stock final deseado.</span>
        </div>
        <div class="field">
            <label for="motivo">Motivo</label>
            <input id="motivo" name="motivo" type="text" maxlength="255" required>
        </div>
        <div class="form-actions field-full">
            <button class="button button-primary" type="submit">Registrar movimiento</button>
        </div>
    </form>
</section>

<section class="panel">
    <div class="section-heading">
        <div>
            <h2>Historial</h2>
            <p class="muted">Movimientos más recientes primero.</p>
        </div>
        <form method="get" action="<?= e(url('movimientos.php')) ?>" class="inline-filter">
            <label class="visually-hidden" for="filtro-producto">Filtrar por producto</label>
            <select id="filtro-producto" name="producto">
                <option value="">Todos los productos</option>
                <?php foreach ($productos as $producto): ?>
                    <option value="<?= e($producto['id'] ?? '') ?>"
                        <?= $productoSeleccionado === (string) ($producto['id'] ?? '') ? 'selected' : '' ?>>
                        <?= e($producto['nombre'] ?? '') ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button class="button button-small button-secondary" type="submit">Filtrar</button>
        </form>
    </div>
    <?php if ($movimientos === []): ?>
        <p class="empty-state">No hay movimientos para mostrar.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Fecha</th><th>Producto</th><th>Tipo</th><th>Cantidad</th><th>Stock resultante</th><th>Motivo</th><th>Usuario</th></tr></thead>
                <tbody>
                <?php foreach ($movimientos as $movimiento): ?>
                    <tr>
                        <td><?= e(fecha_formateada($movimiento['creado_en'] ?? null)) ?></td>
                        <td>
                            <strong><?= e($movimiento['producto'] ?? '') ?></strong>
                            <span class="table-subtitle"><?= e($movimiento['producto_codigo'] ?? '') ?></span>
                        </td>
                        <td><span class="badge badge-neutral"><?= e($movimiento['tipo_etiqueta'] ?? $movimiento['tipo'] ?? '') ?></span></td>
                        <td><?= e($movimiento['cantidad'] ?? 0) ?></td>
                        <td><?= e($movimiento['stock_nuevo'] ?? 0) ?></td>
                        <td><?= e($movimiento['motivo'] ?? '') ?></td>
                        <td><?= e($movimiento['usuario_nombre'] ?? $movimiento['usuario'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
