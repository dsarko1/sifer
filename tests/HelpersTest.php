<?php

declare(strict_types=1);

namespace Sifer\Tests;

use PHPUnit\Framework\TestCase;

final class HelpersTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    public function testIdValidoAceptaEnterosPositivos(): void
    {
        self::assertSame(1, \id_valido(1));
        self::assertSame(27, \id_valido('27'));
    }

    public function testIdValidoRechazaCeroNegativosYBasura(): void
    {
        self::assertNull(\id_valido(0));
        self::assertNull(\id_valido(-3));
        self::assertNull(\id_valido('abc'));
    }

    public function testEnteroNoNegativoAceptaCeroYRechazaNegativos(): void
    {
        self::assertSame(0, \entero_no_negativo(0));
        self::assertNull(\entero_no_negativo(-1));
    }

    public function testPrecioFormateadoUsaDosDecimales(): void
    {
        self::assertSame('$ 1.234,50', \precio_formateado(1234.5));
    }

    public function testFechaFormateadaAceptaNulosYFechasValidas(): void
    {
        self::assertSame('—', \fecha_formateada(null));
        self::assertSame('04/03/2025 16:30', \fecha_formateada('2025-03-04 16:30:00'));
    }

    public function testEscapaComillasYEtiquetas(): void
    {
        self::assertSame('&lt;tag title=&quot;x&quot;&gt;&#039;hola&#039;&lt;/tag&gt;', \e('<tag title="x">\'hola\'</tag>'));
    }
}
