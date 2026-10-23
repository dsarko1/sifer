<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/auditoria.php';
require_once __DIR__ . '/../includes/models/Usuario.php';

require_admin();

if (!es_post()) {
    redirigir('usuarios.php');
}

csrf_verify();

$id = id_valido($_POST['id'] ?? null);
if ($id === null) {
    flash('error', 'El usuario indicado no es válido.');
    redirigir('usuarios.php');
}

$usuario = usuario_obtener($id);
if ($usuario === null) {
    flash('error', 'El usuario no existe.');
    redirigir('usuarios.php');
}

$activar = (int) $usuario['activo'] !== 1;
$esUsuarioActual = (int) usuario_actual()['id'] === $id;

if (!$activar && $esUsuarioActual) {
    flash('error', 'No podés desactivar tu propio usuario.');
    redirigir('usuarios.php');
}

if (
    !$activar
    && $usuario['rol'] === 'admin'
    && usuario_contar_admins_activos() <= 1
) {
    flash('error', 'Debe quedar al menos un administrador activo.');
    redirigir('usuarios.php');
}

if (!usuario_cambiar_activo($id, $activar)) {
    flash('error', 'No se pudo cambiar el estado del usuario.');
    redirigir('usuarios.php');
}

auditar('cambiar_estado', 'usuario', $id, 'Estado: ' . ($activar ? 'activo' : 'inactivo'));
flash('exito', 'Usuario "' . $usuario['username'] . '" ' . ($activar ? 'activado' : 'desactivado') . '.');
redirigir('usuarios.php');
