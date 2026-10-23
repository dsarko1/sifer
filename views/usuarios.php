<?php

declare(strict_types=1);

require __DIR__ . '/partials/header.php';

$usuarios = $datos['usuarios'] ?? [];
$busqueda = (string) ($datos['buscar'] ?? '');
?>
<section class="page-heading">
    <div>
        <div class="eyebrow">Administración</div>
        <h1>Usuarios</h1>
        <p class="muted">Administrá las cuentas y sus permisos de acceso.</p>
    </div>
    <a class="button button-primary" href="<?= e(url('usuario-crear.php')) ?>">Nuevo usuario</a>
</section>

<form class="filter-bar" method="get" action="<?= e(url('usuarios.php')) ?>">
    <div class="field filter-search">
        <label for="buscar">Buscar</label>
        <input id="buscar" name="buscar" type="search" value="<?= e($busqueda) ?>" placeholder="Usuario o nombre">
    </div>
    <button class="button button-secondary filter-submit" type="submit">Buscar</button>
    <a class="button button-quiet filter-reset" href="<?= e(url('usuarios.php')) ?>">Limpiar</a>
</form>

<section class="panel">
    <?php if ($usuarios === []): ?>
        <p class="empty-state">No se encontraron usuarios.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                <tr><th>Usuario</th><th>Nombre</th><th>Rol</th><th>Estado</th><th><span class="visually-hidden">Acciones</span></th></tr>
                </thead>
                <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <?php $activo = !empty($usuario['activo']); ?>
                    <tr>
                        <td><strong><?= e($usuario['username'] ?? '') ?></strong></td>
                        <td><?= e($usuario['nombre'] ?? '') ?></td>
                        <td><?= e($usuario['rol'] ?? '') ?></td>
                        <td>
                            <?php if ($activo): ?>
                                <span class="badge badge-success">Activo</span>
                            <?php else: ?>
                                <span class="badge badge-warning">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td class="table-actions">
                            <a class="button button-small button-quiet"
                               href="<?= e(url('usuario-editar.php?id=' . rawurlencode((string) ($usuario['id'] ?? '')))) ?>">Editar</a>
                            <form action="<?= e(url('usuario-estado.php')) ?>" method="post"
                                <?= $activo ? 'data-confirm="¿Desactivar este usuario?"' : '' ?>>
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= e($usuario['id'] ?? '') ?>">
                                <button class="button button-small <?= $activo ? 'button-danger-quiet' : 'button-secondary' ?>"
                                        type="submit"><?= $activo ? 'Desactivar' : 'Activar' ?></button>
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
