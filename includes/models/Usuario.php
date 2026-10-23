<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';

/**
 * Cuenta caracteres UTF-8 sin depender de que mbstring esté instalado.
 */
function usuario_longitud(string $texto): int
{
    if (function_exists('mb_strlen')) {
        return mb_strlen($texto, 'UTF-8');
    }

    $cantidad = preg_match_all('/./us', $texto, $coincidencias);

    return $cantidad === false ? strlen($texto) : $cantidad;
}

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

/**
 * Lista usuarios por username o nombre, en orden de username.
 */
function usuario_listar(?string $busqueda = null): array
{
    $sql = 'SELECT id, username, nombre, rol, activo, creado_en FROM usuarios';
    $parametros = [];

    if ($busqueda !== null && trim($busqueda) !== '') {
        $sql .= ' WHERE username LIKE :username OR nombre LIKE :nombre';
        $termino = '%' . trim($busqueda) . '%';
        $parametros = [':username' => $termino, ':nombre' => $termino];
    }

    $sql .= ' ORDER BY username ASC';
    $consulta = db()->prepare($sql);
    $consulta->execute($parametros);

    return $consulta->fetchAll();
}

/**
 * Busca un usuario por id.
 */
function usuario_obtener(int $id): ?array
{
    $consulta = db()->prepare(
        'SELECT id, username, nombre, rol, activo, creado_en FROM usuarios WHERE id = :id LIMIT 1'
    );
    $consulta->execute([':id' => $id]);
    $usuario = $consulta->fetch();

    return $usuario === false ? null : $usuario;
}

/**
 * Indica si un username ya está utilizado, ignorando mayúsculas y minúsculas.
 */
function usuario_username_existe(string $username, ?int $exceptoId = null): bool
{
    $sql = 'SELECT 1 FROM usuarios WHERE LOWER(username) = LOWER(:username)';
    $parametros = [':username' => $username];

    if ($exceptoId !== null) {
        $sql .= ' AND id <> :id';
        $parametros[':id'] = $exceptoId;
    }

    $consulta = db()->prepare($sql . ' LIMIT 1');
    $consulta->execute($parametros);

    return $consulta->fetchColumn() !== false;
}

/**
 * Valida y normaliza los campos del formulario de usuario.
 *
 * @return array{0: array{username: string, nombre: string, rol: string, password: string}, 1: array<string, string>}
 */
function usuario_validar(array $entrada, bool $crear, ?int $usuarioId = null): array
{
    $valores = [
        'username' => trim((string) ($entrada['username'] ?? '')),
        'nombre' => trim((string) ($entrada['nombre'] ?? '')),
        'rol' => trim((string) ($entrada['rol'] ?? '')),
        'password' => (string) ($entrada['password'] ?? ''),
    ];
    $errores = [];

    if ($valores['username'] === '') {
        $errores['username'] = 'El nombre de usuario es obligatorio.';
    } elseif (strlen($valores['username']) < 3 || strlen($valores['username']) > 50) {
        $errores['username'] = 'El nombre de usuario debe tener entre 3 y 50 caracteres.';
    } elseif (preg_match('/\A[a-zA-Z0-9._-]+\z/', $valores['username']) !== 1) {
        $errores['username'] = 'Usá solo letras, números, punto, guion y guion bajo.';
    } elseif (usuario_username_existe($valores['username'], $usuarioId)) {
        $errores['username'] = 'Ese nombre de usuario ya está en uso.';
    }

    $longitudNombre = usuario_longitud($valores['nombre']);
    if ($longitudNombre < 2 || $longitudNombre > 100) {
        $errores['nombre'] = 'El nombre debe tener entre 2 y 100 caracteres.';
    }

    if (!in_array($valores['rol'], ['admin', 'user'], true)) {
        $errores['rol'] = 'Elegí un rol válido.';
    }

    if (($crear || $valores['password'] !== '') && usuario_longitud($valores['password']) < 8) {
        $errores['password'] = 'La contraseña debe tener al menos 8 caracteres.';
    }

    return [$valores, $errores];
}

/**
 * Crea un usuario con la contraseña protegida mediante password_hash.
 */
function usuario_crear(string $username, string $nombre, string $rol, string $password): int
{
    $consulta = db()->prepare(
        'INSERT INTO usuarios (username, nombre, rol, password_hash, activo)
         VALUES (:username, :nombre, :rol, :password_hash, 1)'
    );
    $consulta->execute([
        ':username' => trim($username),
        ':nombre' => trim($nombre),
        ':rol' => $rol,
        ':password_hash' => password_hash($password, PASSWORD_DEFAULT),
    ]);

    return (int) db()->lastInsertId();
}

/**
 * Actualiza un usuario, conservando la contraseña si se recibe vacía o null.
 */
function usuario_actualizar(
    int $id,
    string $username,
    string $nombre,
    string $rol,
    ?string $password
): bool {
    $actual = db()->prepare('SELECT rol, activo FROM usuarios WHERE id = :id LIMIT 1');
    $actual->execute([':id' => $id]);
    $usuarioActual = $actual->fetch();

    if ($usuarioActual === false) {
        return false;
    }

    $esUsuarioActual = (int) ($_SESSION['usuario']['id'] ?? 0) === $id;
    if ($esUsuarioActual && $usuarioActual['rol'] === 'admin' && $rol !== 'admin') {
        return false;
    }
    if (
        $usuarioActual['rol'] === 'admin'
        && (int) $usuarioActual['activo'] === 1
        && $rol !== 'admin'
        && usuario_contar_admins_activos() <= 1
    ) {
        return false;
    }

    $campos = 'username = :username, nombre = :nombre, rol = :rol';
    $parametros = [
        ':id' => $id,
        ':username' => trim($username),
        ':nombre' => trim($nombre),
        ':rol' => $rol,
    ];

    if ($password !== null && $password !== '') {
        $campos .= ', password_hash = :password_hash';
        $parametros[':password_hash'] = password_hash($password, PASSWORD_DEFAULT);
    }

    $consulta = db()->prepare('UPDATE usuarios SET ' . $campos . ' WHERE id = :id');
    $consulta->execute($parametros);

    return $consulta->rowCount() > 0 || usuario_obtener($id) !== null;
}

/**
 * Activa o desactiva un usuario respetando las protecciones de administradores.
 */
function usuario_cambiar_activo(int $id, bool $activo): bool
{
    $consulta = db()->prepare('SELECT rol, activo FROM usuarios WHERE id = :id LIMIT 1');
    $consulta->execute([':id' => $id]);
    $usuario = $consulta->fetch();

    if ($usuario === false) {
        return false;
    }

    if (!$activo && (int) ($_SESSION['usuario']['id'] ?? 0) === $id) {
        return false;
    }
    if (
        !$activo
        && $usuario['rol'] === 'admin'
        && (int) $usuario['activo'] === 1
        && usuario_contar_admins_activos() <= 1
    ) {
        return false;
    }

    $actualizar = db()->prepare('UPDATE usuarios SET activo = :activo WHERE id = :id');
    $actualizar->execute([':activo' => $activo ? 1 : 0, ':id' => $id]);

    return $actualizar->rowCount() > 0 || usuario_obtener($id) !== null;
}

/**
 * Cuenta los administradores activos.
 */
function usuario_contar_admins_activos(): int
{
    return (int) db()->query(
        "SELECT COUNT(*) FROM usuarios WHERE rol = 'admin' AND activo = 1"
    )->fetchColumn();
}
