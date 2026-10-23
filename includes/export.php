<?php

declare(strict_types=1);

/**
 * Envía una descarga CSV en UTF-8 con delimitador punto y coma.
 */
function csv_enviar(string $archivo, array $encabezados, array $filas): void
{
    $nombre = pathinfo(basename($archivo), PATHINFO_FILENAME);
    $nombre = preg_replace('/[^A-Za-z0-9.-]/', '', $nombre) ?? '';
    $nombre = trim($nombre, '.');
    if ($nombre === '') {
        $nombre = 'exportacion';
    }
    $nombre .= '.csv';

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $nombre . '"');

    $salida = fopen('php://output', 'wb');
    if ($salida === false) {
        exit;
    }

    fwrite($salida, "\xEF\xBB\xBF");
    fputcsv($salida, $encabezados, ';', '"', '');
    foreach ($filas as $fila) {
        fputcsv($salida, $fila, ';', '"', '');
    }

    fclose($salida);
    exit;
}
