<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/auditoria.php';
require_once __DIR__ . '/../includes/models/Usuario.php';

require_admin();

$errores = [];

if (es_post()) {
    csrf_verify();
    [$valores, $errores] = usuario_validar($_POST, true);

    if ($errores === []) {
        try {
            $usuarioId = usuario_crear(
                $valores['username'],
                $valores['nombre'],
                $valores['rol'],
                $valores['password']
            );
            auditar('crear', 'usuario', $usuarioId, 'Username: ' . $valores['username']);
            flash('exito', 'Usuario "' . $valores['username'] . '" creado correctamente.');
            redirigir('usuarios.php');
        } catch (PDOException $excepcion) {
            if ($excepcion->getCode() === '23000') {
                $errores['username'] = 'Ese nombre de usuario ya está en uso.';
            } else {
                error_log('Sifer: error al crear usuario: ' . $excepcion->getMessage());
                $errores['general'] = 'No se pudo guardar el usuario.';
            }
        }
    }
}

$titulo = 'Nuevo usuario';
$datos = [
    'usuario' => null,
    'roles' => ['admin', 'user'],
    'errores' => $errores,
    'accion' => 'crear',
];

require __DIR__ . '/../views/usuario-form.php';
