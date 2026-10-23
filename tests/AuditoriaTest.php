<?php

declare(strict_types=1);

namespace Sifer\Tests;

use PHPUnit\Framework\TestCase;

final class AuditoriaTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
        $_SERVER['REMOTE_ADDR'] = '192.0.2.25';
        \reiniciar_base();
    }

    public function testAuditarGuardaUsuarioEIp(): void
    {
        $_SESSION['usuario']['id'] = 1;
        \auditar('crear', 'producto', 12, 'Producto de prueba');

        $fila = \db()->query('SELECT usuario_id, accion, entidad, entidad_id, detalle, ip FROM auditoria')->fetch();
        self::assertSame(1, (int) $fila['usuario_id']);
        self::assertSame('crear', $fila['accion']);
        self::assertSame('producto', $fila['entidad']);
        self::assertSame(12, (int) $fila['entidad_id']);
        self::assertSame('Producto de prueba', $fila['detalle']);
        self::assertSame('192.0.2.25', $fila['ip']);
    }

    public function testAuditarSinSesionNoInserta(): void
    {
        \auditar('crear', 'producto', 12, 'No debe registrarse');

        self::assertSame(0, (int) \db()->query('SELECT COUNT(*) FROM auditoria')->fetchColumn());
    }

    public function testListadoFiltraPorEntidadYPagina(): void
    {
        $_SESSION['usuario']['id'] = 1;
        \auditar('crear', 'producto', 1, 'Producto uno');
        \auditar('editar', 'producto', 2, 'Producto dos');
        \auditar('crear', 'categoria', 1, 'Categoría uno');

        $paginaUno = \auditoria_listar(['entidad' => 'producto'], 1, 1);
        $paginaDos = \auditoria_listar(['entidad' => 'producto'], 2, 1);

        self::assertSame(2, $paginaUno['total']);
        self::assertSame(2, $paginaUno['paginas']);
        self::assertCount(1, $paginaUno['filas']);
        self::assertSame(1, $paginaUno['pagina']);
        self::assertSame('producto', $paginaUno['filas'][0]['entidad']);
        self::assertCount(1, $paginaDos['filas']);
        self::assertSame(2, $paginaDos['pagina']);
    }
}
