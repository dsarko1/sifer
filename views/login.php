<?php

declare(strict_types=1);

require __DIR__ . '/partials/header.php';

$error = $datos['error'] ?? null;
$username = (string) ($datos['username'] ?? '');
?>
<section class="auth-card">
    <div class="eyebrow">Gestión de stock</div>
    <h1>Ingresar</h1>
    <p class="muted">Accedé a tu espacio de trabajo.</p>

    <?php if ($error !== null && $error !== ''): ?>
        <div class="form-error" role="alert"><?= e($error) ?></div>
    <?php endif; ?>

    <form action="<?= e(url('login.php')) ?>" method="post" class="form-stack">
        <?= csrf_field() ?>
        <div class="field">
            <label for="username">Usuario</label>
            <input id="username" name="username" type="text" autocomplete="username" required
                   value="<?= e($username) ?>">
        </div>
        <div class="field">
            <label for="password">Contraseña</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required>
        </div>
        <button class="button button-primary button-block" type="submit">Ingresar</button>
    </form>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
