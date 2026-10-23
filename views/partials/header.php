<?php

declare(strict_types=1);

$usuario = usuario_actual();
$flashes = obtener_flashes();
$nombreVisible = nombre_usuario_actual();
?>
<!doctype html>
<html lang="es" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo ?? 'Sifer') ?> · Sifer</title>
    <link rel="stylesheet" href="<?= e(asset('assets/css/style.css')) ?>">
    <script src="<?= e(asset('assets/js/app.js')) ?>" defer></script>
</head>
<body>
<header class="site-header">
    <a class="brand" href="<?= e(url($usuario === null ? 'login.php' : 'index.php')) ?>">Sifer</a>
    <div class="header-tools">
        <?php if ($usuario !== null): ?>
            <nav class="main-nav" aria-label="Navegación principal">
                <?php if (es_admin()): ?>
                    <a href="<?= e(url('dashboard.php')) ?>">Panel</a>
                    <a href="<?= e(url('productos.php')) ?>">Productos</a>
                    <a href="<?= e(url('categorias.php')) ?>">Categorías</a>
                    <a href="<?= e(url('usuarios.php')) ?>">Usuarios</a>
                    <a href="<?= e(url('movimientos.php')) ?>">Movimientos</a>
                    <a href="<?= e(url('reportes.php')) ?>">Reportes</a>
                <?php endif; ?>
                <a href="<?= e(url('catalogo.php')) ?>">Catálogo</a>
            </nav>
            <span class="user-name"><?= e($nombreVisible) ?></span>
            <form action="<?= e(url('logout.php')) ?>" method="post">
                <?= csrf_field() ?>
                <button class="button button-small button-quiet" type="submit">Salir</button>
            </form>
        <?php endif; ?>
        <button class="theme-toggle" type="button" data-theme-toggle aria-label="Cambiar tema">
            <span data-theme-label>Tema oscuro</span>
        </button>
    </div>
</header>
<main class="page-shell">
    <?php if ($flashes !== []): ?>
        <div class="flash-stack" aria-live="polite">
            <?php foreach ($flashes as $flash): ?>
                <?php $tipoFlash = (string) ($flash['tipo'] ?? 'info'); ?>
                <div class="flash flash-<?= e($tipoFlash) ?>" role="status">
                    <?= e($flash['mensaje'] ?? '') ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
