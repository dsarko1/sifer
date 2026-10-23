<?php

declare(strict_types=1);

require __DIR__ . '/partials/header.php';

$registros = $datos['registros'] ?? [];
$filtros = $datos['filtros'] ?? [];
$paginacion = $datos['paginacion'] ?? [];
$entidades = $datos['entidades'] ?? [];
$busqueda = (string) ($filtros['busqueda'] ?? '');
$entidadSeleccionada = (string) ($filtros['entidad'] ?? '');
$pagina = max(1, (int) ($paginacion['pagina'] ?? 1));
$paginas = max(1, (int) ($paginacion['paginas'] ?? 1));
$total = max(0, (int) ($paginacion['total'] ?? 0));
$mostrados = count($registros);
$filtrosQuery = [
    'busqueda' => $busqueda,
    'entidad' => $entidadSeleccionada,
];
$urlPagina = static function (int $numero) use ($filtrosQuery): string {
    return url('auditoria.php?' . http_build_query($filtrosQuery + ['pagina' => $numero]));
};
?>
<section class="page-heading">
    <div>
        <div class="eyebrow">Administración</div>
        <h1>Auditoría</h1>
        <p class="muted">Consultá el historial de acciones registradas.</p>
    </div>
</section>

<section class="panel">
    <form method="get" action="<?= e(url('auditoria.php')) ?>" class="filter-bar">
        <div class="field filter-search">
            <label for="busqueda">Buscar</label>
            <input id="busqueda" name="busqueda" type="search" value="<?= e($busqueda) ?>"
                   placeholder="Usuario, acción o detalle">
        </div>
        <div class="field">
            <label for="entidad">Entidad</label>
            <select id="entidad" name="entidad">
                <option value="">Todas</option>
                <?php foreach ($entidades as $entidad): ?>
                    <?php
                    if (is_array($entidad)) {
                        $valorEntidad = (string) ($entidad['valor'] ?? $entidad['entidad'] ?? $entidad['nombre'] ?? '');
                        $etiquetaEntidad = (string) ($entidad['etiqueta'] ?? $entidad['nombre'] ?? $valorEntidad);
                    } else {
                        $valorEntidad = (string) $entidad;
                        $etiquetaEntidad = $valorEntidad;
                    }
                    ?>
                    <option value="<?= e($valorEntidad) ?>"
                        <?= $entidadSeleccionada === $valorEntidad ? 'selected' : '' ?>>
                        <?= e($etiquetaEntidad) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button class="button button-secondary filter-submit" type="submit">Filtrar</button>
        <a class="button button-quiet filter-reset" href="<?= e(url('auditoria.php')) ?>">Limpiar</a>
    </form>

    <?php if ($registros === []): ?>
        <p class="empty-state">No se encontraron registros de auditoría con los filtros seleccionados.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Acción</th>
                    <th>Entidad</th>
                    <th>Detalle</th>
                    <th>IP</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($registros as $registro): ?>
                    <?php
                    $accion = (string) ($registro['accion'] ?? '');
                    $accionNormalizada = strtolower($accion);
                    $claseAccion = match ($accionNormalizada) {
                        'crear', 'create' => 'badge-success',
                        'eliminar', 'delete' => '',
                        default => 'badge-neutral',
                    };
                    ?>
                    <tr>
                        <td><?= e(fecha_formateada($registro['creado_en'] ?? null)) ?></td>
                        <td><?= e(($registro['nombre'] ?? '') !== '' ? $registro['nombre'] : ($registro['username'] ?? '')) ?></td>
                        <td>
                            <span class="badge <?= e($claseAccion) ?>"
                                <?php if ($accionNormalizada === 'eliminar' || $accionNormalizada === 'delete'): ?>
                                    style="background: var(--danger-soft); color: var(--danger)"
                                <?php endif; ?>>
                                <?= e($accion) ?>
                            </span>
                        </td>
                        <td>
                            <?= e($registro['entidad'] ?? '') ?>
                            <?php if (($registro['entidad_id'] ?? '') !== ''): ?>
                                <span class="table-subtitle">#<?= e($registro['entidad_id']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?= e($registro['detalle'] ?? '') ?></td>
                        <td><?= e($registro['ip'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <div class="pagination">
        <p class="muted">Mostrando <?= e($mostrados) ?> de <?= e($total) ?> registros</p>
        <nav class="pagination-links" aria-label="Paginación de auditoría">
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
