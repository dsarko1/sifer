<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/models/Usuario.php';

require_admin();

$id = id_valido($_POST['id'] ?? $_GET['id'] ?? null);

if ($id === null) {
    flash('error', 'El usuario indicado no es válido.');
    redirigir('usuarios.php');
}

$usuario = usuario_obtener($id);
if ($usuario === null) {
    flash('error', 'El usuario no existe.');
    redirigir('usuarios.php');
}

$errores = [];

if (es_post()) {
    csrf_verify();
    $rolAnterior = $usuario['rol'];
    [$valores, $errores] = usuario_validar($_POST, false, $id);
    $usuario = array_merge($usuario, $valores);
    $usuario['password'] = '';

    $esUsuarioActual = (int) usuario_actual()['id'] === $id;
    if ($esUsuarioActual && $usuario['rol'] !== 'admin') {
        $errores['rol'] = 'No podés quitarte tu propio rol de administrador.';
    } elseif (
        $rolAnterior === 'admin'
        && $usuario['rol'] !== 'admin'
        && (int) $usuario['activo'] === 1
        && usuario_contar_admins_activos() <= 1
    ) {
        $errores['rol'] = 'Debe quedar al menos un administrador activo.';
    }

    if ($errores === []) {
        try {
            $password = $valores['password'] === '' ? null : $valores['password'];
            if (usuario_actualizar(
                $id,
                $valores['username'],
                $valores['nombre'],
                $valores['rol'],
                $password
            )) {
                flash('exito', 'Usuario "' . $valores['username'] . '" actualizado correctamente.');
                redirigir('usuarios.php');
            }

            $errores['general'] = 'No se pudo guardar el usuario: revisá las protecciones de administradores activos.';
        } catch (PDOException $excepcion) {
            if ($excepcion->getCode() === '23000') {
                $errores['username'] = 'Ese nombre de usuario ya está en uso.';
            } else {
                error_log('Sifer: error al editar usuario: ' . $excepcion->getMessage());
                $errores['general'] = 'No se pudo guardar el usuario.';
            }
        }
    }
}

$titulo = 'Editar usuario';
$datos = [
    'usuario' => $usuario,
    'roles' => ['admin', 'user'],
    'errores' => $errores,
    'accion' => 'editar',
];

require __DIR__ . '/../views/usuario-form.php';
