<?php

declare(strict_types=1);

namespace Sifer\Tests;

use PHPUnit\Framework\TestCase;

final class UsuarioTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
        \reiniciar_base();
    }

    public function testAutenticarConClaveCorrectaIncorrectaYUsuarioInactivo(): void
    {
        self::assertSame('admin', \usuario_autenticar('admin', 'Test1234!')['username']);
        self::assertNull(\usuario_autenticar('admin', 'incorrecta'));
        self::assertNull(\usuario_autenticar('inactivo', 'Test1234!'));
    }

    public function testUsernameExisteSinDistinguirMayusculas(): void
    {
        self::assertTrue(\usuario_username_existe('ADMIN'));
        self::assertFalse(\usuario_username_existe('ADMIN', 1));
    }

    public function testNoPermiteAutodesactivarseNiDejarElSistemaSinAdmin(): void
    {
        $_SESSION['usuario'] = ['id' => 1, 'rol' => 'admin'];
        self::assertFalse(\usuario_cambiar_activo(1, false));

        self::assertFalse(\usuario_actualizar(1, 'admin', 'Administrador de prueba', 'user', null));
        self::assertSame('admin', \usuario_obtener(1)['rol']);

        $_SESSION['usuario'] = ['id' => 2, 'rol' => 'user'];
        self::assertFalse(\usuario_actualizar(1, 'admin', 'Administrador de prueba', 'user', null));
        self::assertFalse(\usuario_cambiar_activo(1, false));
        self::assertSame(1, \usuario_contar_admins_activos());
    }

    public function testPuedeDesactivarOtroUsuario(): void
    {
        $_SESSION['usuario'] = ['id' => 1, 'rol' => 'admin'];

        self::assertTrue(\usuario_cambiar_activo(2, false));
        self::assertSame(0, (int) \usuario_obtener(2)['activo']);
    }
}
