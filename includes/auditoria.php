<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

/**
 * Registra una acción del usuario actual sin interrumpir la operación principal.
 */
function auditar(
    string $accion,
    string $entidad,
    ?int $entidadId = null,
    string $detalle = ''
): void {
    $usuarioId = filter_var($_SESSION['usuario']['id'] ?? null, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1],
    ]);
    if ($usuarioId === false || $usuarioId === null) {
        return;
    }

    $ipEntrada = $_SERVER['REMOTE_ADDR'] ?? null;
    $ip = is_string($ipEntrada) && filter_var($ipEntrada, FILTER_VALIDATE_IP) !== false
        ? $ipEntrada
        : null;
    $detalle = function_exists('mb_substr')
        ? mb_substr($detalle, 0, 255, 'UTF-8')
        : substr($detalle, 0, 255);

    try {
        $consulta = db()->prepare(
            'INSERT INTO auditoria (usuario_id, accion, entidad, entidad_id, detalle, ip)
             VALUES (:usuario_id, :accion, :entidad, :entidad_id, :detalle, :ip)'
        );
        $consulta->execute([
            ':usuario_id' => $usuarioId,
            ':accion' => $accion,
            ':entidad' => $entidad,
            ':entidad_id' => $entidadId,
            ':detalle' => $detalle,
            ':ip' => $ip,
        ]);
    } catch (Throwable $excepcion) {
        error_log('Sifer: no se pudo registrar auditoría: ' . $excepcion->getMessage());
    }
}
