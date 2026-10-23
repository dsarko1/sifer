<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/models/Usuario.php';
require_once __DIR__ . '/../includes/models/IntentoLogin.php';

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
        $ipEntrada = $_SERVER['REMOTE_ADDR'] ?? '';
        $ip = is_string($ipEntrada) && filter_var($ipEntrada, FILTER_VALIDATE_IP) !== false
            ? $ipEntrada
            : '0.0.0.0';
        $estadoIntentos = intento_login_estado($ip, $username);

        if ($estadoIntentos['bloqueado']) {
            $minutos = (int) ceil($estadoIntentos['segundos_restantes'] / 60);
            flash('error', 'Demasiados intentos. Volvé a intentarlo en ' . $minutos . ' minuto(s).');
        } else {
            $usuario = usuario_autenticar($username, $password);

            if ($usuario === null) {
                intento_login_fallo($ip, $username);
                $error = 'Usuario o contraseña incorrectos.';
            } else {
                intento_login_limpiar($ip, $username);
                login_usuario($usuario);
                $nombre = $usuario['nombre'] !== '' ? $usuario['nombre'] : $usuario['username'];
                flash('exito', 'Bienvenido/a, ' . $nombre . '.');
                redirigir('index.php');
            }
        }
    }
}

$titulo = 'Ingresar';
$datos  = [
    'error'    => $error,
    'username' => $username,
];

require __DIR__ . '/../views/login.php';
