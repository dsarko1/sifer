<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/models/Usuario.php';

require_admin();

$busqueda = trim((string) ($_GET['buscar'] ?? ''));
$usuarios = usuario_listar($busqueda === '' ? null : $busqueda);

$titulo = 'Usuarios';
$datos = [
    'usuarios' => $usuarios,
    'buscar' => $busqueda,
];

require __DIR__ . '/../views/usuarios.php';
