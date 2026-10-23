<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/models/Usuario.php';

if (esta_logueado()) {
    redirigir('index.php');
}

$error    = null;
$username = '';

if (es_post()) {
    csrf_verify();

    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Ingresá tu usuario y contraseña.';
    } else {
        $usuario = usuario_autenticar($username, $password);

        if ($usuario === null) {
            $error = 'Usuario o contraseña incorrectos.';
        } else {
            login_usuario($usuario);
            $nombre = $usuario['nombre'] !== '' ? $usuario['nombre'] : $usuario['username'];
            flash('exito', 'Bienvenido/a, ' . $nombre . '.');
            redirigir('index.php');
        }
    }
}

$titulo = 'Ingresar';
$datos  = [
    'error'    => $error,
    'username' => $username,
];

require __DIR__ . '/../views/login.php';
