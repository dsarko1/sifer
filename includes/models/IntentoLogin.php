<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';

/**
 * Normaliza el nombre usado como clave del limitador de acceso.
 */
function intento_login_normalizar_username(string $username): string
{
    $username = trim($username);

    $normalizado = function_exists('mb_strtolower')
        ? mb_strtolower($username, 'UTF-8')
        : strtolower($username);

    return function_exists('mb_substr')
        ? mb_substr($normalizado, 0, 50, 'UTF-8')
        : substr($normalizado, 0, 50);
}

/**
 * Informa el estado del límite de intentos para una IP y un usuario.
 *
 * @return array{intentos: int, bloqueado: bool, segundos_restantes: int}
 */
function intento_login_estado(string $ip, string $username, ?int $ahora = null): array
{
    $ahora ??= time();
    $consulta = db()->prepare(
        'SELECT intentos, bloqueado_hasta
         FROM intentos_login
         WHERE ip = :ip AND username = :username
         LIMIT 1'
    );
    $consulta->execute([
        ':ip' => $ip,
        ':username' => intento_login_normalizar_username($username),
    ]);
    $fila = $consulta->fetch();

    if ($fila === false || $fila['bloqueado_hasta'] === null) {
        return ['intentos' => $fila === false ? 0 : (int) $fila['intentos'], 'bloqueado' => false, 'segundos_restantes' => 0];
    }

    $hasta = strtotime((string) $fila['bloqueado_hasta']);
    $restantes = $hasta === false ? 0 : max(0, $hasta - $ahora);

    return [
        'intentos' => (int) $fila['intentos'],
        'bloqueado' => $restantes > 0,
        'segundos_restantes' => $restantes,
    ];
}

/**
 * Registra un fallo y bloquea la clave al alcanzar el máximo configurado.
 */
function intento_login_fallo(string $ip, string $username, ?int $ahora = null): void
{
    $ahora ??= time();
    $username = intento_login_normalizar_username($username);
    $marcaTiempo = date('Y-m-d H:i:s', $ahora);
    $pdo = db();
    $transaccionPropia = !$pdo->inTransaction();
    if ($transaccionPropia) {
        $pdo->beginTransaction();
    }

    try {
        $crear = $pdo->prepare(
            'INSERT INTO intentos_login (ip, username, intentos, bloqueado_hasta, ultimo_intento)
             VALUES (:ip, :username, 0, NULL, :ultimo_intento)
             ON DUPLICATE KEY UPDATE id = id'
        );
        $crear->execute([
            ':ip' => $ip,
            ':username' => $username,
            ':ultimo_intento' => $marcaTiempo,
        ]);

        $consulta = $pdo->prepare(
            'SELECT intentos, ultimo_intento
             FROM intentos_login
             WHERE ip = :ip AND username = :username
             LIMIT 1 FOR UPDATE'
        );
        $consulta->execute([':ip' => $ip, ':username' => $username]);
        $fila = $consulta->fetch();

        if ($fila === false) {
            throw new RuntimeException('No se pudo preparar el registro del intento de acceso.');
        }

        $ultimoIntento = strtotime((string) $fila['ultimo_intento']);
        $intentosPrevios = (int) $fila['intentos'];
        if ($ultimoIntento !== false && $ahora - $ultimoIntento > SIFER_BLOQUEO_MINUTOS * 60) {
            $intentosPrevios = 0;
        }

        $intentos = $intentosPrevios + 1;
        $bloqueadoHasta = $intentos >= SIFER_INTENTOS_MAX
            ? date('Y-m-d H:i:s', $ahora + SIFER_BLOQUEO_MINUTOS * 60)
            : null;
        $actualizar = db()->prepare(
            'UPDATE intentos_login
             SET intentos = :intentos, bloqueado_hasta = :bloqueado_hasta, ultimo_intento = :ultimo_intento
             WHERE ip = :ip AND username = :username'
        );
        $actualizar->execute([
            ':intentos' => $intentos,
            ':bloqueado_hasta' => $bloqueadoHasta,
            ':ultimo_intento' => $marcaTiempo,
            ':ip' => $ip,
            ':username' => $username,
        ]);
        if ($transaccionPropia) {
            $pdo->commit();
        }
    } catch (Throwable $excepcion) {
        if ($transaccionPropia && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $excepcion;
    }
}

/**
 * Limpia el registro de intentos fallidos de una IP y un usuario.
 */
function intento_login_limpiar(string $ip, string $username): void
{
    $consulta = db()->prepare(
        'DELETE FROM intentos_login WHERE ip = :ip AND username = :username'
    );
    $consulta->execute([
        ':ip' => $ip,
        ':username' => intento_login_normalizar_username($username),
    ]);
}
