<?php

declare(strict_types=1);

require __DIR__ . '/partials/header.php';

$movimientos = $datos['movimientos'] ?? [];
$productos = $datos['productos'] ?? [];
$tipos = $datos['tipos'] ?? [];
$filtros = $datos['filtros'] ?? [];
$paginacion = $datos['paginacion'] ?? [];
$tipoSeleccionado = (string) ($filtros['tipo'] ?? '');
$productoSeleccionado = (string) ($filtros['producto'] ?? '');
$desde = (string) ($filtros['desde'] ?? '');
$hasta = (string) ($filtros['hasta'] ?? '');
$pagina = max(1, (int) ($paginacion['pagina'] ?? 1));
$paginas = max(1, (int) ($paginacion['paginas'] ?? 1));
$total = max(0, (int) ($paginacion['total'] ?? 0));
$mostrados = count($movimientos);
$filtrosQuery = [
    'tipo' => $tipoSeleccionado,
    'producto' => $productoSeleccionado,
    'desde' => $desde,
    'hasta' => $hasta,
];
$urlPagina = static function (int $numero) use ($filtrosQuery): string {
    return url('movimientos.php?' . http_build_query($filtrosQuery + ['pagina' => $numero]));
};
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
    </div>
    <form method="get" action="<?= e(url('movimientos.php')) ?>" class="filter-bar">
        <div class="field">
            <label for="filtro-tipo">Tipo</label>
            <select id="filtro-tipo" name="tipo">
                <option value="">Todos</option>
                <?php foreach ($tipos as $claveTipo => $tipo): ?>
                    <?php
                    if (is_array($tipo)) {
                        $valorTipo = (string) ($tipo['tipo'] ?? $tipo['valor'] ?? $claveTipo);
                        $etiquetaTipo = (string) ($tipo['etiqueta'] ?? $tipo['nombre'] ?? $tipo['label'] ?? $valorTipo);
                    } else {
                        $valorTipo = is_string($claveTipo) ? $claveTipo : (string) $tipo;
                        $etiquetaTipo = (is_string($claveTipo) ? (string) $tipo : $valorTipo);
                    }
                    ?>
                    <option value="<?= e($valorTipo) ?>" <?= $tipoSeleccionado === $valorTipo ? 'selected' : '' ?>>
                        <?= e($etiquetaTipo) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field">
            <label for="filtro-producto">Producto</label>
            <select id="filtro-producto" name="producto">
                <option value="">Todos</option>
                <?php foreach ($productos as $producto): ?>
                    <option value="<?= e($producto['id'] ?? '') ?>"
                        <?= $productoSeleccionado === (string) ($producto['id'] ?? '') ? 'selected' : '' ?>>
                        <?= e($producto['nombre'] ?? '') ?> · <?= e($producto['codigo'] ?? '') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field">
            <label for="filtro-desde">Desde</label>
            <input id="filtro-desde" name="desde" type="date" value="<?= e($desde) ?>">
        </div>
        <div class="field">
            <label for="filtro-hasta">Hasta</label>
            <input id="filtro-hasta" name="hasta" type="date" value="<?= e($hasta) ?>">
        </div>
        <button class="button button-secondary filter-submit" type="submit">Filtrar</button>
        <a class="button button-quiet filter-reset" href="<?= e(url('movimientos.php')) ?>">Limpiar</a>
    </form>
    <?php if ($movimientos === []): ?>
        <p class="empty-state">No se encontraron movimientos con los filtros seleccionados.</p>
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
    <div class="pagination">
        <p class="muted">Mostrando <?= e($mostrados) ?> de <?= e($total) ?> movimientos</p>
        <nav class="pagination-links" aria-label="Paginación de movimientos">
            <?php if ($pagina <= 1): ?>
                <button class="button button-small button-quiet" type="button" disabled>Anterior</button>
            <?php else: ?>
                <a class="button button-small button-quiet" href="<?= e($urlPagina($pagina - 1)) ?>">Anterior</a>
            <?php endif; ?>

            <?php
            $primeraPagina = max(1, $pagina - 2);
            $ultimaPagina = min($paginas, $pagina + 2);
            ?>
            <?php if ($primeraPagina > 1): ?>
                <a class="button button-small button-quiet" href="<?= e($urlPagina(1)) ?>">1</a>
                <?php if ($primeraPagina > 2): ?><span class="pagination-ellipsis" aria-hidden="true">…</span><?php endif; ?>
            <?php endif; ?>
            <?php for ($numeroPagina = $primeraPagina; $numeroPagina <= $ultimaPagina; $numeroPagina++): ?>
                <?php if ($numeroPagina === $pagina): ?>
                    <span class="button button-small button-primary" aria-current="page"><?= e($numeroPagina) ?></span>
                <?php else: ?>
                    <a class="button button-small button-quiet" href="<?= e($urlPagina($numeroPagina)) ?>"><?= e($numeroPagina) ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            <?php if ($ultimaPagina < $paginas): ?>
                <?php if ($ultimaPagina < $paginas - 1): ?><span class="pagination-ellipsis" aria-hidden="true">…</span><?php endif; ?>
                <a class="button button-small button-quiet" href="<?= e($urlPagina($paginas)) ?>"><?= e($paginas) ?></a>
            <?php endif; ?>

            <?php if ($pagina >= $paginas): ?>
                <button class="button button-small button-quiet" type="button" disabled>Siguiente</button>
            <?php else: ?>
                <a class="button button-small button-quiet" href="<?= e($urlPagina($pagina + 1)) ?>">Siguiente</a>
            <?php endif; ?>
        </nav>
    </div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
