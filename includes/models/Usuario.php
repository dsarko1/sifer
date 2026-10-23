<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';

/**
 * Busca un usuario por su nombre de usuario.
 */
function usuario_buscar_por_username(string $username): ?array
{
    $consulta = db()->prepare(
        'SELECT id, username, nombre, password_hash, rol, activo
         FROM usuarios
         WHERE username = :username
         LIMIT 1'
    );
    $consulta->execute([':username' => $username]);
    $usuario = $consulta->fetch();

    return $usuario === false ? null : $usuario;
}

/**
 * Comprueba las credenciales de un usuario activo.
 *
 * Devuelve los datos públicos del usuario o null si no coincide.
 */
function usuario_autenticar(string $username, string $password): ?array
{
    $usuario = usuario_buscar_por_username($username);

    if ($usuario === null || (int) $usuario['activo'] !== 1) {
        return null;
    }

    if (!password_verify($password, (string) $usuario['password_hash'])) {
        return null;
    }

    unset($usuario['password_hash'], $usuario['activo']);

    return $usuario;
}
