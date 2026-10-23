<?php

declare(strict_types=1);

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/../config/database.php';

// Sesión única de la aplicación, con cookie HttpOnly y SameSite Lax.
if (session_status() === PHP_SESSION_NONE) {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? '') === '443');

    session_name('SIFERSESSID');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => $https,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

/**
 * Devuelve el usuario logueado o null si no hay sesión iniciada.
 *
 * @return array{id: int, username: string, nombre: string, rol: string}|null
 */
function usuario_actual(): ?array
{
    $usuario = $_SESSION['usuario'] ?? null;

    return is_array($usuario) ? $usuario : null;
}

/**
 * Indica si hay una sesión iniciada.
 */
function esta_logueado(): bool
{
    return isset($_SESSION['usuario']['id']);
}

/**
 * Indica si el usuario logueado tiene el rol admin.
 */
function es_admin(): bool
{
    return (usuario_actual()['rol'] ?? '') === 'admin';
}

/**
 * Devuelve el nombre visible del usuario logueado.
 */
function nombre_usuario_actual(): string
{
    $usuario = usuario_actual();

    return $usuario['nombre'] ?? $usuario['username'] ?? '';
}

/**
 * Guarda al usuario en la sesión y renueva el id de sesión.
 */
function login_usuario(array $usuario): void
{
    session_regenerate_id(true);

    $_SESSION['usuario'] = [
        'id'       => (int) $usuario['id'],
        'username' => (string) $usuario['username'],
        'nombre'   => (string) $usuario['nombre'],
        'rol'      => (string) $usuario['rol'],
    ];
    $_SESSION['ultima_actividad'] = time();
    $_SESSION['ultima_regeneracion'] = time();

    csrf_regenerar();
}

/**
 * Cierra la sesión del usuario.
 *
 * Se conserva una sesión vacía para poder mostrar el mensaje flash de
 * despedida en la pantalla de login.
 */
function logout_usuario(): void
{
    $_SESSION = [];
    session_regenerate_id(true);
    csrf_regenerar();
}

/**
 * Exige una sesión iniciada. Si no la hay, vuelve al login.
 */
function require_login(): void
{
    if (!esta_logueado()) {
        flash('error', 'Iniciá sesión para continuar.');
        redirigir('login.php');
    }

    $ahora = time();
    $ultimaActividad = (int) ($_SESSION['ultima_actividad'] ?? $ahora);
    if ($ahora - $ultimaActividad > SIFER_SESION_INACTIVA) {
        logout_usuario();
        flash('error', 'Sesión expirada por inactividad.');
        redirigir('login.php');
    }

    $_SESSION['ultima_actividad'] = $ahora;
    $ultimaRegeneracion = (int) ($_SESSION['ultima_regeneracion'] ?? 0);
    if ($ahora - $ultimaRegeneracion > SIFER_SESION_REGENERAR) {
        session_regenerate_id(true);
        $_SESSION['ultima_regeneracion'] = $ahora;
    }
}

/**
 * Exige el rol admin. Un usuario común vuelve al catálogo.
 *
 * La comprobación se hace siempre en el servidor, antes de ejecutar
 * cualquier acción o de cargar una vista.
 */
function require_admin(): void
{
    require_login();

    if (!es_admin()) {
        flash('error', 'No tenés permisos para acceder a esa sección.');
        redirigir('catalogo.php');
    }
}
