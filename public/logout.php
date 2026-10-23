<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

if (!es_post()) {
    redirigir(esta_logueado() ? 'index.php' : 'login.php');
}

csrf_verify();
logout_usuario();
flash('exito', 'Cerraste sesión correctamente.');
redirigir('login.php');
