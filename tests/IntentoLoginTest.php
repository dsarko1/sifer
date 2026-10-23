<?php

declare(strict_types=1);

namespace Sifer\Tests;

use PHPUnit\Framework\TestCase;

final class IntentoLoginTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
        \reiniciar_base();
    }

    public function testCincoFallosBloqueanLaCuenta(): void
    {
        $ahora = 1_800_000_000;
        for ($intento = 0; $intento < SIFER_INTENTOS_MAX; $intento++) {
            \intento_login_fallo('192.0.2.10', ' Vendedor ', $ahora);
        }

        self::assertSame([
            'intentos' => 5,
            'bloqueado' => true,
            'segundos_restantes' => SIFER_BLOQUEO_MINUTOS * 60,
        ], \intento_login_estado('192.0.2.10', 'vendedor', $ahora));
    }

    public function testElBloqueoVenceAlPasarElTiempoConfigurado(): void
    {
        $ahora = 1_800_000_000;
        for ($intento = 0; $intento < SIFER_INTENTOS_MAX; $intento++) {
            \intento_login_fallo('192.0.2.11', 'admin', $ahora);
        }

        $estado = \intento_login_estado(
            '192.0.2.11',
            'admin',
            $ahora + SIFER_BLOQUEO_MINUTOS * 60 + 1
        );

        self::assertFalse($estado['bloqueado']);
        self::assertSame(0, $estado['segundos_restantes']);
    }

    public function testFalloFueraDeLaVentanaReiniciaLaCuenta(): void
    {
        $ahora = 1_800_000_000;
        \intento_login_fallo('192.0.2.12', 'admin', $ahora);
        $nuevoIntento = $ahora + SIFER_BLOQUEO_MINUTOS * 60 + 1;
        \intento_login_fallo('192.0.2.12', 'admin', $nuevoIntento);

        self::assertSame(1, \intento_login_estado('192.0.2.12', 'admin', $nuevoIntento)['intentos']);
    }

    public function testLimpiarBorraElRegistro(): void
    {
        \intento_login_fallo('192.0.2.13', 'admin');
        \intento_login_limpiar('192.0.2.13', ' ADMIN ');

        self::assertSame([
            'intentos' => 0,
            'bloqueado' => false,
            'segundos_restantes' => 0,
        ], \intento_login_estado('192.0.2.13', 'admin'));
    }

    public function testNormalizaUsernameSinDistinguirMayusculas(): void
    {
        \intento_login_fallo('192.0.2.14', ' Admin ');

        self::assertSame(1, \intento_login_estado('192.0.2.14', 'admin')['intentos']);
    }
}
