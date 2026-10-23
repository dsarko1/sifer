<?php

declare(strict_types=1);

namespace Sifer\Tests;

use PHPUnit\Framework\TestCase;

final class CsrfTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    public function testTokenEsEstableYHexadecimalDe64Caracteres(): void
    {
        $token = \csrf_token();

        self::assertSame($token, \csrf_token());
        self::assertSame(64, strlen($token));
        self::assertMatchesRegularExpression('/\A[a-f0-9]{64}\z/', $token);
    }

    public function testRegenerarCambiaElToken(): void
    {
        $anterior = \csrf_token();
        \csrf_regenerar();

        self::assertNotSame($anterior, \csrf_token());
    }

    public function testCampoIncluyeInputOcultoConElToken(): void
    {
        $token = \csrf_token();

        self::assertSame(
            '<input type="hidden" name="_csrf" value="' . $token . '">',
            \csrf_field()
        );
    }
}
