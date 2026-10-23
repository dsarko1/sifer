<?php

declare(strict_types=1);

require __DIR__ . '/partials/header.php';

$categoria = $datos['categoria'] ?? null;
$errores = $datos['errores'] ?? [];
$accion = ($datos['accion'] ?? 'crear') === 'editar' ? 'editar' : 'crear';
$esEdicion = $accion === 'editar';
$valor = static fn (string $campo): string => (string) ($categoria[$campo] ?? '');
$errorCampo = static fn (string $campo): string => (string) ($errores[$campo] ?? '');
?>
<section class="page-heading">
    <div>
        <div class="eyebrow">Inventario</div>
        <h1><?= $esEdicion ? 'Editar categoría' : 'Nueva categoría' ?></h1>
        <p class="muted"><?= $esEdicion ? 'Actualizá los datos de la categoría.' : 'Completá los datos para crear una categoría.' ?></p>
    </div>
    <a class="button button-quiet" href="<?= e(url('categorias.php')) ?>">Volver a categorías</a>
</section>

<section class="panel form-panel">
    <?php if (!empty($errores['general'])): ?>
        <div class="form-error" role="alert"><?= e($errores['general']) ?></div>
    <?php endif; ?>
    <form action="<?= e(url($esEdicion ? 'categoria-editar.php' : 'categoria-crear.php')) ?>" method="post" class="form-grid">
        <?= csrf_field() ?>
        <?php if ($esEdicion): ?>
            <input type="hidden" name="id" value="<?= e($valor('id')) ?>">
        <?php endif; ?>
        <div class="field field-full">
            <label for="nombre">Nombre</label>
            <input id="nombre" name="nombre" type="text" required value="<?= e($valor('nombre')) ?>"
                <?= $errorCampo('nombre') !== '' ? 'aria-invalid="true"' : '' ?>>
            <?php if ($errorCampo('nombre') !== ''): ?><span class="field-error"><?= e($errorCampo('nombre')) ?></span><?php endif; ?>
        </div>
        <div class="field field-full">
            <label for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion" rows="4"
                <?= $errorCampo('descripcion') !== '' ? 'aria-invalid="true"' : '' ?>><?= e($valor('descripcion')) ?></textarea>
            <?php if ($errorCampo('descripcion') !== ''): ?><span class="field-error"><?= e($errorCampo('descripcion')) ?></span><?php endif; ?>
        </div>
        <div class="form-actions field-full">
            <a class="button button-quiet" href="<?= e(url('categorias.php')) ?>">Volver a categorías</a>
            <button class="button button-primary" type="submit">Guardar</button>
        </div>
    </form>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
