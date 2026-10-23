<?php

declare(strict_types=1);

/**
 * Funciones auxiliares compartidas por controladores y vistas.
 */

/**
 * Escapa un valor para mostrarlo de forma segura dentro de HTML.
 */
function e(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Arma la URL de un recurso ubicado en la carpeta assets de la raíz del proyecto.
 */
function asset(string $ruta): string
{
    return dirname(BASE_URL) . '/assets/' . ltrim($ruta, '/');
}

/**
 * Renderiza una página de error y finaliza la petición con el código indicado.
 */
function error_http(int $codigo, string $mensaje): never
{
    http_response_code($codigo);
    $titulo = 'Error ' . $codigo;
    $datos = [
        'codigo' => $codigo,
        'mensaje' => $mensaje,
    ];

    require dirname(__DIR__) . '/views/error.php';
    exit;
}

/**
 * Arma una URL interna a partir de la URL base configurada.
 */
function url(string $ruta = ''): string
{
    $base = defined('BASE_URL') ? BASE_URL : '';

    return $base . ($ruta === '' ? '' : '/' . ltrim($ruta, '/'));
}

/**
 * Redirige a una ruta interna y corta la ejecución.
 */
function redirigir(string $ruta): never
{
    header('Location: ' . url($ruta));
    exit;
}

/**
 * Indica si la petición actual es un POST.
 */
function es_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

/**
 * Guarda un mensaje para la próxima pantalla.
 *
 * Tipos previstos: exito, error e info.
 */
function flash(string $tipo, string $mensaje): void
{
    $_SESSION['flash'][] = [
        'tipo'    => $tipo,
        'mensaje' => $mensaje,
    ];
}

/**
 * Devuelve los mensajes pendientes y limpia la cola.
 *
 * @return array<int, array{tipo: string, mensaje: string}>
 */
function obtener_flashes(): array
{
    $mensajes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);

    return is_array($mensajes) ? $mensajes : [];
}

/**
 * Formatea un precio en pesos argentinos.
 */
function precio_formateado(float | int | string $precio): string
{
    return '$ ' . number_format((float) $precio, 2, ',', '.');
}

/**
 * Formatea una fecha de la base de datos para mostrarla en pantalla.
 */
function fecha_formateada(?string $fecha, string $formato = 'd/m/Y H:i'): string
{
    if ($fecha === null || $fecha === '') {
        return '—';
    }

    $marca = strtotime($fecha);

    return $marca === false ? '—' : date($formato, $marca);
}

/**
 * Valida un identificador entero positivo (por ejemplo, un id de la URL).
 */
function id_valido(mixed $valor): ?int
{
    $id = filter_var($valor, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

    return $id === false ? null : (int) $id;
}

/**
 * Valida un entero mayor o igual a cero.
 */
function entero_no_negativo(mixed $valor): ?int
{
    $numero = filter_var($valor, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);

    return $numero === false ? null : (int) $numero;
}
