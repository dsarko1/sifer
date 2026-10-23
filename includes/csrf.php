<?php

declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

/**
 * Devuelve el token CSRF de la sesión, creándolo si todavía no existe.
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        csrf_regenerar();
    }

    return $_SESSION['csrf_token'];
}

/**
 * Genera un token CSRF nuevo. Se usa al iniciar sesión.
 */
function csrf_regenerar(): void
{
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/**
 * Devuelve el campo oculto que deben incluir todos los formularios POST.
 */
function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

/**
 * Comprueba el token CSRF de un POST.
 *
 * Si el token no coincide, corta la petición: deja un mensaje flash y
 * vuelve al inicio (que redirige según el rol o invita a iniciar sesión).
 */
function csrf_verify(): void
{
    $enviado  = $_POST['_csrf'] ?? '';
    $guardado = $_SESSION['csrf_token'] ?? '';

    if (
        !is_string($enviado)
        || !is_string($guardado)
        || $guardado === ''
        || !hash_equals($guardado, $enviado)
    ) {
        http_response_code(403);
        flash('error', 'La sesión expiró o la solicitud no es válida. Volvé a intentarlo.');
        redirigir('index.php');
    }
}
