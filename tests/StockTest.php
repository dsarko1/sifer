<?php

declare(strict_types=1);

namespace Sifer\Tests;

use DomainException;
use PHPUnit\Framework\TestCase;

final class StockTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
        \reiniciar_base();
    }

    public function testEntradaSumaStockYDevuelveValoresAnteriorYNuevo(): void
    {
        $resultado = \movimiento_registrar(1, 'entrada', 3, 'Reposición', 1);

        self::assertSame(10, $resultado['stock_anterior']);
        self::assertSame(13, $resultado['stock_nuevo']);
        self::assertSame(13, (int) \producto_buscar_por_id(1)['stock_actual']);
    }

    public function testSalidaRestaStock(): void
    {
        $resultado = \movimiento_registrar(1, 'salida', 4, 'Venta', 1);

        self::assertSame(10, $resultado['stock_anterior']);
        self::assertSame(6, $resultado['stock_nuevo']);
        self::assertSame(6, (int) \producto_buscar_por_id(1)['stock_actual']);
    }

    public function testSalidaMayorAlStockFallaSinCambiarlo(): void
    {
        try {
            \movimiento_registrar(1, 'salida', 11, 'Venta excedida', 1);
            self::fail('Se esperaba una DomainException.');
        } catch (DomainException) {
            self::assertSame(10, (int) \producto_buscar_por_id(1)['stock_actual']);
        }
    }

    public function testAjusteFijaElStockExacto(): void
    {
        $resultado = \movimiento_registrar(1, 'ajuste', 7, 'Recuento', 1);

        self::assertSame(10, $resultado['stock_anterior']);
        self::assertSame(7, $resultado['stock_nuevo']);
        self::assertSame(7, (int) \producto_buscar_por_id(1)['stock_actual']);
    }

    public function testProductoInactivoNoAceptaMovimientos(): void
    {
        $this->expectException(DomainException::class);
        \movimiento_registrar(3, 'entrada', 1, 'Prueba', 1);
    }

    public function testMovimientoRegistraElUsuarioIndicado(): void
    {
        \movimiento_registrar(1, 'entrada', 2, 'Carga registrada por vendedor', 2);
        $consulta = \db()->query('SELECT usuario_id FROM movimientos ORDER BY id DESC LIMIT 1');

        self::assertSame(2, (int) $consulta->fetchColumn());
    }
}
