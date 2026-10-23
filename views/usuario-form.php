<?php

declare(strict_types=1);

require __DIR__ . '/partials/header.php';

$usuario = $datos['usuario'] ?? null;
$roles = $datos['roles'] ?? [];
$errores = $datos['errores'] ?? [];
$accion = ($datos['accion'] ?? 'crear') === 'editar' ? 'editar' : 'crear';
$esEdicion = $accion === 'editar';
$valor = static fn (string $campo): string => (string) ($usuario[$campo] ?? '');
$errorCampo = static fn (string $campo): string => (string) ($errores[$campo] ?? '');
?>
<section class="page-heading">
    <div>
        <div class="eyebrow">Administración</div>
        <h1><?= $esEdicion ? 'Editar usuario' : 'Nuevo usuario' ?></h1>
        <p class="muted"><?= $esEdicion ? 'Actualizá los datos y permisos de la cuenta.' : 'Completá los datos para crear una cuenta.' ?></p>
    </div>
    <a class="button button-quiet" href="<?= e(url('usuarios.php')) ?>">Volver a usuarios</a>
</section>

<section class="panel form-panel">
    <?php if (!empty($errores['general'])): ?>
        <div class="form-error" role="alert"><?= e($errores['general']) ?></div>
    <?php endif; ?>
    <form action="<?= e(url($esEdicion ? 'usuario-editar.php' : 'usuario-crear.php')) ?>" method="post" class="form-grid">
        <?= csrf_field() ?>
        <?php if ($esEdicion): ?>
            <input type="hidden" name="id" value="<?= e($valor('id')) ?>">
        <?php endif; ?>
        <div class="field">
            <label for="username">Usuario</label>
            <input id="username" name="username" type="text" required value="<?= e($valor('username')) ?>"
                <?= $errorCampo('username') !== '' ? 'aria-invalid="true"' : '' ?>>
            <?php if ($errorCampo('username') !== ''): ?><span class="field-error"><?= e($errorCampo('username')) ?></span><?php endif; ?>
        </div>
        <div class="field">
            <label for="nombre">Nombre</label>
            <input id="nombre" name="nombre" type="text" required value="<?= e($valor('nombre')) ?>"
                <?= $errorCampo('nombre') !== '' ? 'aria-invalid="true"' : '' ?>>
            <?php if ($errorCampo('nombre') !== ''): ?><span class="field-error"><?= e($errorCampo('nombre')) ?></span><?php endif; ?>
        </div>
        <div class="field">
            <label for="rol">Rol</label>
            <select id="rol" name="rol" required <?= $errorCampo('rol') !== '' ? 'aria-invalid="true"' : '' ?>>
                <option value="">Elegí un rol</option>
                <?php foreach ($roles as $rol): ?>
                    <option value="<?= e($rol) ?>" <?= $valor('rol') === (string) $rol ? 'selected' : '' ?>>
                        <?= e($rol) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if ($errorCampo('rol') !== ''): ?><span class="field-error"><?= e($errorCampo('rol')) ?></span><?php endif; ?>
        </div>
        <div class="field">
            <label for="password">Contraseña<?= $esEdicion ? ' (dejala vacía para no cambiarla)' : '' ?></label>
            <input id="password" name="password" type="password" autocomplete="new-password"
                <?= $esEdicion ? '' : 'required' ?>
                <?= $errorCampo('password') !== '' ? 'aria-invalid="true"' : '' ?>>
            <?php if ($errorCampo('password') !== ''): ?><span class="field-error"><?= e($errorCampo('password')) ?></span><?php endif; ?>
        </div>
        <div class="form-actions field-full">
            <a class="button button-quiet" href="<?= e(url('usuarios.php')) ?>">Volver a usuarios</a>
            <button class="button button-primary" type="submit">Guardar</button>
        </div>
    </form>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
