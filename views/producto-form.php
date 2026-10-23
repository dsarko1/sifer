<?php

declare(strict_types=1);

require __DIR__ . '/partials/header.php';

$producto = $datos['producto'] ?? null;
$categorias = $datos['categorias'] ?? [];
$errores = $datos['errores'] ?? [];
$accion = ($datos['accion'] ?? 'crear') === 'editar' ? 'editar' : 'crear';
$esEdicion = $accion === 'editar';
$valor = static fn (string $campo, mixed $predeterminado = ''): string =>
    (string) ($producto[$campo] ?? $predeterminado);
$errorCampo = static fn (string $campo): string => (string) ($errores[$campo] ?? '');
?>
<section class="page-heading">
    <div>
        <div class="eyebrow">Inventario</div>
        <h1><?= $esEdicion ? 'Editar producto' : 'Nuevo producto' ?></h1>
        <p class="muted"><?= $esEdicion ? 'Actualizá los datos del producto.' : 'Completá los datos para sumarlo al inventario.' ?></p>
    </div>
    <a class="button button-quiet" href="<?= e(url('productos.php')) ?>">Volver a productos</a>
</section>

<section class="panel form-panel">
    <?php if (!empty($errores['general'])): ?>
        <div class="form-error" role="alert"><?= e($errores['general']) ?></div>
    <?php endif; ?>
    <form action="<?= e(url($esEdicion ? 'producto-editar.php' : 'producto-crear.php')) ?>" method="post" class="form-grid">
        <?= csrf_field() ?>
        <?php if ($esEdicion): ?>
            <input type="hidden" name="id" value="<?= e($valor('id')) ?>">
        <?php endif; ?>
        <div class="field">
            <label for="codigo">Código</label>
            <input id="codigo" name="codigo" type="text" maxlength="30" required value="<?= e($valor('codigo')) ?>"
                <?= $errorCampo('codigo') !== '' ? 'aria-invalid="true"' : '' ?>>
            <?php if ($errorCampo('codigo') !== ''): ?><span class="field-error"><?= e($errorCampo('codigo')) ?></span><?php endif; ?>
        </div>
        <div class="field">
            <label for="nombre">Nombre</label>
            <input id="nombre" name="nombre" type="text" maxlength="120" required value="<?= e($valor('nombre')) ?>"
                <?= $errorCampo('nombre') !== '' ? 'aria-invalid="true"' : '' ?>>
            <?php if ($errorCampo('nombre') !== ''): ?><span class="field-error"><?= e($errorCampo('nombre')) ?></span><?php endif; ?>
        </div>
        <div class="field">
            <label for="categoria_id">Categoría</label>
            <select id="categoria_id" name="categoria_id">
                <option value="">Sin categoría</option>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= e($categoria['id'] ?? '') ?>"
                        <?= $valor('categoria_id') === (string) ($categoria['id'] ?? '') ? 'selected' : '' ?>>
                        <?= e($categoria['nombre'] ?? '') ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if ($errorCampo('categoria_id') !== ''): ?><span class="field-error"><?= e($errorCampo('categoria_id')) ?></span><?php endif; ?>
        </div>
        <div class="field">
            <label for="precio">Precio</label>
            <input id="precio" name="precio" type="number" min="0" step="0.01" required value="<?= e($valor('precio')) ?>">
            <?php if ($errorCampo('precio') !== ''): ?><span class="field-error"><?= e($errorCampo('precio')) ?></span><?php endif; ?>
        </div>
        <?php if (!$esEdicion): ?>
            <div class="field">
                <label for="stock_actual">Stock inicial</label>
                <input id="stock_actual" name="stock_actual" type="number" min="0" step="1" required value="<?= e($valor('stock_actual', '0')) ?>">
                <?php if ($errorCampo('stock_actual') !== ''): ?><span class="field-error"><?= e($errorCampo('stock_actual')) ?></span><?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="field">
            <label for="stock_minimo">Stock mínimo</label>
            <input id="stock_minimo" name="stock_minimo" type="number" min="0" step="1" required value="<?= e($valor('stock_minimo', '0')) ?>">
            <?php if ($errorCampo('stock_minimo') !== ''): ?><span class="field-error"><?= e($errorCampo('stock_minimo')) ?></span><?php endif; ?>
        </div>
        <div class="field field-full">
            <label for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion" rows="4"><?= e($valor('descripcion')) ?></textarea>
        </div>
        <div class="form-actions field-full">
            <a class="button button-quiet" href="<?= e(url('productos.php')) ?>">Cancelar</a>
            <button class="button button-primary" type="submit"><?= $esEdicion ? 'Guardar cambios' : 'Crear producto' ?></button>
        </div>
    </form>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
