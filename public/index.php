<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

if (!esta_logueado()) {
    redirigir('login.php');
}

redirigir(es_admin() ? 'dashboard.php' : 'catalogo.php');
