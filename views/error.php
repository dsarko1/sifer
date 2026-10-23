<?php

declare(strict_types=1);

require __DIR__ . '/partials/header.php';

$codigo = $datos['codigo'] ?? '';
$mensaje = $datos['mensaje'] ?? 'Ocurrió un error al procesar la solicitud.';
?>
<section class="error-card panel">
    <?php if ($codigo !== ''): ?><div class="error-code"><?= e($codigo) ?></div><?php endif; ?>
    <h1>No pudimos completar la solicitud</h1>
    <p class="muted"><?= e($mensaje) ?></p>
    <a class="button button-primary" href="<?= e(url('index.php')) ?>">Volver al inicio</a>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
